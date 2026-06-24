<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class UCU_Collegium_Google_Calendar_Service {
    public function create_event_for_booking( int $booking_id ): array {
        $booking = ( new UCU_Collegium_Booking_Service() )->get_booking( $booking_id );
        if ( ! $booking ) {
            return array( 'error' => 'Заявку не знайдено.' );
        }
        $calendar_id = UCU_Collegium_Settings::get( 'calendar_id' );
        if ( ! $calendar_id ) {
            return array( 'error' => 'Google Calendar ID не налаштовано.' );
        }
        $token = $this->get_access_token( array( 'https://www.googleapis.com/auth/calendar' ) );
        if ( is_wp_error( $token ) ) {
            return array( 'error' => $token->get_error_message() );
        }

        $slot = ( new UCU_Collegium_Slot_Service() )->get_slot( (int) $booking['slot_id'] );
        if ( ! $slot ) {
            return array( 'error' => 'Слот заявки не знайдено.' );
        }

        $meet_request_id = wp_generate_uuid4();
        $body = array(
            'summary'     => $this->replace_template( UCU_Collegium_Settings::get( 'event_title_template' ), $booking, $slot, '' ),
            'description' => $this->replace_template( UCU_Collegium_Settings::get( 'event_description_template' ), $booking, $slot, '' ),
            'start'       => array( 'dateTime' => $slot['slot_date'] . 'T' . $slot['start_time'], 'timeZone' => wp_timezone_string() ),
            'end'         => array( 'dateTime' => $slot['slot_date'] . 'T' . $slot['end_time'], 'timeZone' => wp_timezone_string() ),
        );

        if ( UCU_Collegium_Settings::get( 'create_meet' ) ) {
            $body['conferenceData'] = array(
                'createRequest' => array(
                    'requestId' => $meet_request_id,
                    'conferenceSolutionKey' => array( 'type' => 'hangoutsMeet' ),
                ),
            );
        }

        $url = 'https://www.googleapis.com/calendar/v3/calendars/' . rawurlencode( $calendar_id ) . '/events?conferenceDataVersion=1';
        $response = wp_remote_post(
            $url,
            array(
                'headers' => array( 'Authorization' => 'Bearer ' . $token, 'Content-Type' => 'application/json' ),
                'body'    => wp_json_encode( $body, JSON_UNESCAPED_UNICODE ),
                'timeout' => 20,
            )
        );
        if ( is_wp_error( $response ) ) {
            return array( 'error' => $response->get_error_message() );
        }
        $code = wp_remote_retrieve_response_code( $response );
        $json = json_decode( wp_remote_retrieve_body( $response ), true );
        if ( $code < 200 || $code >= 300 ) {
            return array( 'error' => 'Google Calendar error ' . $code . ': ' . wp_remote_retrieve_body( $response ) );
        }

        $meet_link = $json['hangoutLink'] ?? '';
        if ( $meet_link ) {
            $this->patch_event_description( $calendar_id, $json['id'] ?? '', $token, $this->replace_template( UCU_Collegium_Settings::get( 'event_description_template' ), $booking, $slot, $meet_link ) );
        }
        return array( 'calendar_event_id' => $json['id'] ?? '', 'meet_link' => $meet_link );
    }

    private function patch_event_description( string $calendar_id, string $event_id, string $token, string $description ): void {
        if ( '' === $event_id ) {
            return;
        }
        wp_remote_request(
            'https://www.googleapis.com/calendar/v3/calendars/' . rawurlencode( $calendar_id ) . '/events/' . rawurlencode( $event_id ),
            array(
                'method'  => 'PATCH',
                'headers' => array( 'Authorization' => 'Bearer ' . $token, 'Content-Type' => 'application/json' ),
                'body'    => wp_json_encode( array( 'description' => $description ), JSON_UNESCAPED_UNICODE ),
                'timeout' => 20,
            )
        );
    }

    public function get_access_token( array $scopes ) {
        $credentials = $this->get_credentials();
        if ( is_wp_error( $credentials ) ) {
            return $credentials;
        }

        $now = time();
        $header = $this->base64url( wp_json_encode( array( 'alg' => 'RS256', 'typ' => 'JWT' ) ) );
        $claim = $this->base64url(
            wp_json_encode(
                array(
                    'iss'   => $credentials['client_email'],
                    'scope' => implode( ' ', $scopes ),
                    'aud'   => 'https://oauth2.googleapis.com/token',
                    'exp'   => $now + 3600,
                    'iat'   => $now,
                )
            )
        );
        $unsigned = $header . '.' . $claim;
        $signature = '';
        if ( ! openssl_sign( $unsigned, $signature, $credentials['private_key'], OPENSSL_ALGO_SHA256 ) ) {
            return new WP_Error( 'google_jwt_sign', 'Не вдалося підписати Google JWT.' );
        }

        $response = wp_remote_post(
            'https://oauth2.googleapis.com/token',
            array(
                'body'    => array( 'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer', 'assertion' => $unsigned . '.' . $this->base64url( $signature ) ),
                'timeout' => 20,
            )
        );
        if ( is_wp_error( $response ) ) {
            return $response;
        }
        $json = json_decode( wp_remote_retrieve_body( $response ), true );
        if ( empty( $json['access_token'] ) ) {
            return new WP_Error( 'google_token', 'Не вдалося отримати Google access token: ' . wp_remote_retrieve_body( $response ) );
        }
        return $json['access_token'];
    }

    private function get_credentials() {
        $json = UCU_Collegium_Settings::get( 'credentials_json' );
        $path = UCU_Collegium_Settings::get( 'credentials_path' );
        if ( $path && file_exists( $path ) && is_readable( $path ) ) {
            $json = file_get_contents( $path );
        }
        $credentials = json_decode( (string) $json, true );
        if ( empty( $credentials['client_email'] ) || empty( $credentials['private_key'] ) ) {
            return new WP_Error( 'google_credentials', 'Google credentials service account не налаштовано або некоректні.' );
        }
        return $credentials;
    }

    private function replace_template( string $template, array $booking, array $slot, string $meet_link ): string {
        $full_name = trim( $booking['last_name'] . ' ' . $booking['first_name'] . ' ' . $booking['middle_name'] );
        return strtr(
            $template,
            array(
                '{first_name}'  => $booking['first_name'],
                '{last_name}'   => $booking['last_name'],
                '{middle_name}' => $booking['middle_name'],
                '{full_name}'   => $full_name,
                '{date}'        => mysql2date( 'd.m.Y', $slot['slot_date'] ),
                '{time}'        => substr( $slot['start_time'], 0, 5 ) . '-' . substr( $slot['end_time'], 0, 5 ),
                '{meet_link}'   => $meet_link,
            )
        );
    }

    private function base64url( string $value ): string {
        return rtrim( strtr( base64_encode( $value ), '+/', '-_' ), '=' );
    }
}
