<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class UCU_Collegium_Ajax {
    public static function init(): void {
        add_action( 'wp_ajax_ucu_collegium_get_slots', array( __CLASS__, 'get_slots' ) );
        add_action( 'wp_ajax_nopriv_ucu_collegium_get_slots', array( __CLASS__, 'get_slots' ) );
        add_action( 'wp_ajax_ucu_collegium_hold_slot', array( __CLASS__, 'hold_slot' ) );
        add_action( 'wp_ajax_nopriv_ucu_collegium_hold_slot', array( __CLASS__, 'hold_slot' ) );
        add_action( 'wp_ajax_ucu_collegium_submit_booking', array( __CLASS__, 'submit_booking' ) );
        add_action( 'wp_ajax_nopriv_ucu_collegium_submit_booking', array( __CLASS__, 'submit_booking' ) );
        add_action( 'ucu_collegium_cleanup_holds', array( 'UCU_Collegium_Hold_Service', 'cleanup_expired_holds_static' ) );
    }

    private static function check_nonce(): void {
        if ( ! check_ajax_referer( 'ucu_collegium_booking_nonce', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => 'Помилка безпеки. Оновіть сторінку.' ), 403 );
        }
    }

    public static function get_slots(): void {
        self::check_nonce();
        $slot_service = new UCU_Collegium_Slot_Service();
        $slots = array_map(
            static function ( $slot ) use ( $slot_service ) {
                return array(
                    'id'        => (int) $slot['id'],
                    'label'     => mysql2date( 'd.m.Y', $slot['slot_date'] ) . ' ' . substr( $slot['start_time'], 0, 5 ) . '–' . substr( $slot['end_time'], 0, 5 ),
                    'available' => $slot_service->get_available_capacity( (int) $slot['id'] ),
                );
            },
            $slot_service->get_available_slots()
        );
        wp_send_json_success( array( 'slots' => $slots ) );
    }

    public static function hold_slot(): void {
        self::check_nonce();
        $slot_id       = absint( $_POST['slot_id'] ?? 0 );
        $session_token = sanitize_text_field( wp_unslash( $_POST['session_token'] ?? '' ) );
        $email         = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );

        if ( ! $slot_id || '' === $session_token ) {
            wp_send_json_error( array( 'message' => UCU_Collegium_Settings::get( 'generic_error_message' ) ), 400 );
        }

        $ok = ( new UCU_Collegium_Hold_Service() )->create_or_refresh_hold( $slot_id, $session_token, $email ?: null );
        if ( ! $ok ) {
            wp_send_json_error( array( 'message' => UCU_Collegium_Settings::get( 'slot_unavailable_message' ) ), 409 );
        }

        wp_send_json_success( array( 'message' => 'Слот тимчасово зарезервовано на 5 хвилин.' ) );
    }

    public static function submit_booking(): void {
        self::check_nonce();
        $result = ( new UCU_Collegium_Booking_Service() )->submit( $_POST, $_FILES );
        if ( empty( $result['ok'] ) ) {
            wp_send_json_error( $result, 422 );
        }
        wp_send_json_success( $result );
    }
}
