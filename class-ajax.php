<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class UCU_Collegium_Hold_Service {
    public function create_or_refresh_hold( int $slot_id, string $session_token, ?string $email = null ): bool {
        global $wpdb;
        self::cleanup_expired_holds_static();
        $slot_service = new UCU_Collegium_Slot_Service();

        if ( $this->has_valid_hold( $slot_id, $session_token ) || $slot_service->get_available_capacity( $slot_id ) > 0 ) {
            $existing = (int) $wpdb->get_var( $wpdb->prepare( 'SELECT id FROM ' . UCU_Collegium_Activator::holds_table() . ' WHERE slot_id = %d AND session_token = %s', $slot_id, $session_token ) );
            $data = array(
                'email'      => $email ? sanitize_email( $email ) : null,
                'expires_at' => gmdate( 'Y-m-d H:i:s', current_time( 'timestamp', true ) + 5 * MINUTE_IN_SECONDS ),
            );
            if ( $existing ) {
                return false !== $wpdb->update( UCU_Collegium_Activator::holds_table(), $data, array( 'id' => $existing ) );
            }
            $data['slot_id']       = $slot_id;
            $data['session_token'] = sanitize_text_field( $session_token );
            $data['created_at']    = current_time( 'mysql' );
            return false !== $wpdb->insert( UCU_Collegium_Activator::holds_table(), $data );
        }

        return false;
    }

    public function release_hold( int $slot_id, string $session_token ): bool {
        global $wpdb;
        return false !== $wpdb->delete( UCU_Collegium_Activator::holds_table(), array( 'slot_id' => $slot_id, 'session_token' => $session_token ) );
    }

    public function cleanup_expired_holds(): void {
        self::cleanup_expired_holds_static();
    }

    public static function cleanup_expired_holds_static(): void {
        global $wpdb;
        $wpdb->query( $wpdb->prepare( 'DELETE FROM ' . UCU_Collegium_Activator::holds_table() . ' WHERE expires_at <= %s', current_time( 'mysql' ) ) );
    }

    public function has_valid_hold( int $slot_id, string $session_token ): bool {
        global $wpdb;
        return (int) $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . UCU_Collegium_Activator::holds_table() . ' WHERE slot_id = %d AND session_token = %s AND expires_at > %s', $slot_id, $session_token, current_time( 'mysql' ) ) ) > 0;
    }
}
