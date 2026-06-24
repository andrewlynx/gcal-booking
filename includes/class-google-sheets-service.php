<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class UCU_Collegium_Google_Sheets_Service {
    public function sync_booking( int $booking_id ): ?string {
        if ( '1' !== UCU_Collegium_Settings::get( 'enable_sheets' ) ) {
            return null;
        }
        $spreadsheet_id = UCU_Collegium_Settings::get( 'spreadsheet_id' );
        $sheet_name     = UCU_Collegium_Settings::get( 'sheet_name' );
        if ( ! $spreadsheet_id || ! $sheet_name ) {
            $this->record_error( $booking_id, 'Google Sheets налаштування неповні.' );
            return null;
        }

        $token = ( new UCU_Collegium_Google_Calendar_Service() )->get_access_token( array( 'https://www.googleapis.com/auth/spreadsheets' ) );
        if ( is_wp_error( $token ) ) {
            $this->record_error( $booking_id, $token->get_error_message() );
            return null;
        }

        $booking = ( new UCU_Collegium_Booking_Service() )->get_booking( $booking_id );
        $slot    = $booking && $booking['slot_id'] ? ( new UCU_Collegium_Slot_Service() )->get_slot( (int) $booking['slot_id'] ) : null;
        if ( ! $booking ) {
            return null;
        }

        $row = $this->build_row( $booking, $slot );
        $url = 'https://sheets.googleapis.com/v4/spreadsheets/' . rawurlencode( $spreadsheet_id ) . '/values/' . rawurlencode( $sheet_name ) . '!A1:append?valueInputOption=USER_ENTERED&insertDataOption=INSERT_ROWS';
        $response = wp_remote_post(
            $url,
            array(
                'headers' => array( 'Authorization' => 'Bearer ' . $token, 'Content-Type' => 'application/json' ),
                'body'    => wp_json_encode( array( 'values' => array( $row ) ), JSON_UNESCAPED_UNICODE ),
                'timeout' => 20,
            )
        );
        if ( is_wp_error( $response ) ) {
            $this->record_error( $booking_id, $response->get_error_message() );
            return null;
        }
        $code = wp_remote_retrieve_response_code( $response );
        $json = json_decode( wp_remote_retrieve_body( $response ), true );
        if ( $code < 200 || $code >= 300 ) {
            $this->record_error( $booking_id, 'Google Sheets error ' . $code . ': ' . wp_remote_retrieve_body( $response ) );
            return null;
        }
        return $json['updates']['updatedRange'] ?? null;
    }

    private function record_error( int $booking_id, string $message ): void {
        global $wpdb;
        $booking = ( new UCU_Collegium_Booking_Service() )->get_booking( $booking_id );
        $prefix  = $booking && ! empty( $booking['error_message'] ) ? $booking['error_message'] . "\n" : '';
        $wpdb->update(
            UCU_Collegium_Activator::bookings_table(),
            array( 'error_message' => $prefix . 'Sheets sync: ' . $message, 'updated_at' => current_time( 'mysql' ) ),
            array( 'id' => $booking_id )
        );
    }

    private function build_row( array $booking, ?array $slot ): array {
        $form = $booking['form_data_array'];
        $full_name = trim( $booking['last_name'] . ' ' . $booking['first_name'] . ' ' . $booking['middle_name'] );
        $row = array(
            $booking['id'], $booking['slot_id'], $booking['calendar_event_id'], $booking['meet_link'], $booking['status'],
            $booking['email'], $booking['phone'], $booking['last_name'], $booking['first_name'], $booking['middle_name'], $full_name,
            $slot['slot_date'] ?? '', $slot['start_time'] ?? '', $slot['end_time'] ?? '',
            $booking['photo_attachment_id'] ? wp_get_attachment_url( (int) $booking['photo_attachment_id'] ) : '',
        );
        foreach ( UCU_Collegium_Form_Fields::get_fields() as $field ) {
            $row[] = $form[ $field['key'] ] ?? '';
        }
        array_push( $row, $booking['auto_score'], $booking['manual_score'], $booking['interview_score'], $booking['total_score'], $booking['created_at'], $booking['updated_at'] );
        return $row;
    }
}
