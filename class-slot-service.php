<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class UCU_Collegium_Google_Calendar_Service {
    public function create_event_for_booking( int $booking_id ): array {
        $booking = ( new UCU_Collegium_Booking_Service() )->get_booking( $booking_id );
        if ( ! $booking ) {
            return array( 'error' => '[1] Заявку не знайдено.' );
        }
        $calendar_id = UCU_Collegium_Settings::get( 'calendar_id' );
        if ( ! $calendar_id ) {
            return array( 'error' => '[2] Google Calendar ID не налаштовано.' );
        }

        $token = $this->get_access_token( array( 'https://www.googleapis.com/auth/calendar' ) );
        if ( is_wp_error( $token ) ) {
            return array( 'error' => '[3] Токен: ' . $token->get_error_message() );
        }

        $slot = ( new UCU_Collegium_Slot_Service() )->get_slot( (int) $booking['slot_id'] );
        if ( ! $slot ) {
            return array( 'error' => '[4] Слот не знайдено: slot_id=' . $booking['slot_id'] );
        }

        $event_body = array(
            'summary'     => $this->replace_template( UCU_Collegium_Settings::get( 'event_title_template' ), $booking, $slot, '' ),
            'description' => $this->replace_template( UCU_Collegium_Settings::get( 'event_description_template' ), $booking, $slot, '' ),
            'start'       => array( 'dateTime' => $slot['slot_date'] . 'T' . $slot['start_time'], 'timeZone' => wp_timezone_string() ),
            'end'         => array( 'dateTime' => $slot['slot_date'] . 'T' . $slot['end_time'], 'timeZone' => wp_timezone_string() ),
        );

        // Google Meet через service account не підтримується API
        // Додаємо посилання на Meet як location якщо є в налаштуваннях
        $meet_link_setting = UCU_Collegium_Settings::get( 'meet_link' );
        if ( $meet_link_setting ) {
            $event_body['location'] = $meet_link_setting;
        }

        $url = 'https://www.googleapis.com/calendar/v3/calendars/' . rawurlencode( $calendar_id ) . '/events';
        $response = wp_remote_post(
            $url,
            array(
                'headers' => array( 'Authorization' => 'Bearer ' . $token, 'Content-Type' => 'application/json' ),
                'body'    => wp_json_encode( $event_body, JSON_UNESCAPED_UNICODE ),
                'timeout' => 20,
            )
        );
        if ( is_wp_error( $response ) ) {
            return array( 'error' => '[5] HTTP: ' . $response->get_error_message() );
        }
        $code     = wp_remote_retrieve_response_code( $response );
        $body_raw = wp_remote_retrieve_body( $response );
        $json     = json_decode( $body_raw, true );
        if ( $code < 200 || $code >= 300 ) {
            $msg = isset( $json['error']['message'] ) ? $json['error']['message'] : $body_raw;
            return array( 'error' => '[6] Google API ' . $code . ': ' . $msg );
        }

        $event_id  = $json['id'] ?? '';
        $meet_link = $json['hangoutLink'] ?? UCU_Collegium_Settings::get( 'meet_link', '' );
        return array( 'calendar_event_id' => $event_id, 'meet_link' => $meet_link );
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
        $pkey_raw = $credentials['private_key'];
        $pkey = openssl_pkey_get_private( $pkey_raw );
        if ( false === $pkey ) {
            $pkey = openssl_pkey_get_private( stripslashes( $pkey_raw ) );
        }
        if ( false === $pkey ) {
            $openssl_error = openssl_error_string() ?: 'невідома помилка OpenSSL';
            // Debug: показуємо перші 200 символів ключа та кількість переносів
            $debug_key = $pkey_raw;
            $newlines   = substr_count( $debug_key, "\n" );
            $literal_n  = substr_count( $debug_key, '\\n' );
            return new WP_Error( 'google_jwt_key', '[3a] Не вдалося завантажити private_key. OpenSSL: ' . $openssl_error . ' | Довжина: ' . strlen( $pkey_raw ) . ' | Реальних \n: ' . $newlines . ' | Literal \\n: ' . $literal_n . ' | Перші 100: ' . substr( str_replace( "\n", '[NL]', $pkey_raw ), 0, 120 ) );
        }
        if ( ! openssl_sign( $unsigned, $signature, $pkey, OPENSSL_ALGO_SHA256 ) ) {
            return new WP_Error( 'google_jwt_sign', '[3b] openssl_sign failed: ' . ( openssl_error_string() ?: 'no error' ) );
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
        // WordPress може зберігати \n як літеральні два символи замість реального переносу рядка.
        // openssl_sign() потребує коректний PEM з реальними \n, інакше повертає false.
        // private_key нормалізовано при збереженні в sanitize()
        // Додаткова страховка на випадок якщо ключ збережений старою версією
        $key = $credentials['private_key'];
        $key = str_replace( '\\n', "\n", $key );
        if ( substr_count( $key, "\n" ) < 3 ) {
            if ( preg_match( '/-----BEGIN ([A-Z ]+)-----(.*?)-----END ([A-Z ]+)-----/s', $key, $m ) ) {
                $body = preg_replace( '/[^A-Za-z0-9+\/=]/', '', $m[2] );
                $body = chunk_split( $body, 64, "\n" );
                $key  = "-----BEGIN {$m[1]}-----\n" . $body . "-----END {$m[3]}-----\n";
            }
        }
        $credentials['private_key'] = $key;
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
