<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class UCU_Collegium_CSV_Export {
    public static function output(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Insufficient permissions.', 'ucu-collegium-booking' ) );
        }
        check_admin_referer( 'ucu_collegium_export_csv' );

        global $wpdb;
        $rows = $wpdb->get_results( 'SELECT b.*, s.slot_date, s.start_time, s.end_time FROM ' . UCU_Collegium_Activator::bookings_table() . ' b LEFT JOIN ' . UCU_Collegium_Activator::slots_table() . ' s ON b.slot_id = s.id ORDER BY b.created_at DESC', ARRAY_A );
        $fields = UCU_Collegium_Form_Fields::get_fields();

        header( 'Content-Type: text/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename=ucu-collegium-bookings-' . gmdate( 'Y-m-d' ) . '.csv' );
        $out = fopen( 'php://output', 'w' );
        fprintf( $out, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) );
        $header = array( 'booking_id', 'status', 'created_at', 'email', 'phone', 'last_name', 'first_name', 'middle_name', 'slot_date', 'slot_start_time', 'slot_end_time', 'meet_link', 'auto_score', 'manual_score', 'interview_score', 'total_score' );
        foreach ( $fields as $field ) {
            $header[] = $field['key'];
        }
        $header[] = 'photo_url';
        fputcsv( $out, $header );

        foreach ( $rows as $row ) {
            $form = json_decode( $row['form_data'], true ) ?: array();
            $line = array( $row['id'], $row['status'], $row['created_at'], $row['email'], $row['phone'], $row['last_name'], $row['first_name'], $row['middle_name'], $row['slot_date'], $row['start_time'], $row['end_time'], $row['meet_link'], $row['auto_score'], $row['manual_score'], $row['interview_score'], $row['total_score'] );
            foreach ( $fields as $field ) {
                $line[] = $form[ $field['key'] ] ?? '';
            }
            $line[] = $row['photo_attachment_id'] ? wp_get_attachment_url( (int) $row['photo_attachment_id'] ) : '';
            fputcsv( $out, $line );
        }
        fclose( $out );
        exit;
    }
}
