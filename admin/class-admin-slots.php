<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class UCU_Collegium_Admin_Slots {
    public static function render(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Insufficient permissions.', 'ucu-collegium-booking' ) );
        }
        $action = sanitize_key( $_GET['action'] ?? '' );
        if ( 'edit' === $action ) {
            $slot = ( new UCU_Collegium_Slot_Service() )->get_slot( absint( $_GET['slot_id'] ?? 0 ) );
            include UCU_COLLEGIUM_BOOKING_PATH . 'admin/views/slot-edit.php';
            return;
        }
        global $wpdb;
        $slots = $wpdb->get_results( 'SELECT * FROM ' . UCU_Collegium_Activator::slots_table() . " WHERE status != 'deleted' ORDER BY slot_date DESC, start_time DESC", ARRAY_A );
        include UCU_COLLEGIUM_BOOKING_PATH . 'admin/views/slots-list.php';
    }

    public static function save(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Insufficient permissions.', 'ucu-collegium-booking' ) );
        }
        check_admin_referer( 'ucu_collegium_slot_save' );
        $slot_id = absint( $_POST['slot_id'] ?? 0 );
        $data = array(
            'slot_date'  => sanitize_text_field( wp_unslash( $_POST['slot_date'] ?? '' ) ),
            'start_time' => sanitize_text_field( wp_unslash( $_POST['start_time'] ?? '' ) ),
            'end_time'   => sanitize_text_field( wp_unslash( $_POST['end_time'] ?? '' ) ),
            'status'     => sanitize_key( $_POST['status'] ?? 'active' ),
        );
        $service = new UCU_Collegium_Slot_Service();
        $slot_id ? $service->update_slot( $slot_id, $data ) : $service->create_slot( $data );
        wp_safe_redirect( admin_url( 'admin.php?page=ucu-collegium-booking-slots&updated=1' ) );
        exit;
    }

    public static function delete(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Insufficient permissions.', 'ucu-collegium-booking' ) );
        }
        check_admin_referer( 'ucu_collegium_slot_delete' );
        ( new UCU_Collegium_Slot_Service() )->delete_slot( absint( $_GET['slot_id'] ?? 0 ) );
        wp_safe_redirect( admin_url( 'admin.php?page=ucu-collegium-booking-slots&deleted=1' ) );
        exit;
    }
}
