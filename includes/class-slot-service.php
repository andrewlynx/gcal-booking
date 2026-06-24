<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class UCU_Collegium_Slot_Service {
    public function create_slot( array $data ): int {
        global $wpdb;
        $wpdb->insert(
            UCU_Collegium_Activator::slots_table(),
            array(
                'slot_date'    => sanitize_text_field( $data['slot_date'] ?? '' ),
                'start_time'   => sanitize_text_field( $data['start_time'] ?? '' ),
                'end_time'     => sanitize_text_field( $data['end_time'] ?? '' ),
                'capacity'     => 5,
                'booked_count' => 0,
                'status'       => in_array( $data['status'] ?? 'active', array( 'active', 'inactive', 'full' ), true ) ? $data['status'] : 'active',
                'created_at'   => current_time( 'mysql' ),
            ),
            array( '%s', '%s', '%s', '%d', '%d', '%s', '%s' )
        );
        return (int) $wpdb->insert_id;
    }

    public function update_slot( int $slot_id, array $data ): bool {
        global $wpdb;
        $current = $this->get_slot( $slot_id );
        if ( ! $current ) {
            return false;
        }

        $update = array( 'updated_at' => current_time( 'mysql' ) );
        if ( isset( $data['status'] ) && in_array( $data['status'], array( 'active', 'inactive', 'full' ), true ) ) {
            $update['status'] = $data['status'];
        }

        if ( ! $this->has_bookings( $slot_id ) && 0 === (int) $current['booked_count'] ) {
            foreach ( array( 'slot_date', 'start_time', 'end_time' ) as $key ) {
                if ( isset( $data[ $key ] ) ) {
                    $update[ $key ] = sanitize_text_field( $data[ $key ] );
                }
            }
        }

        return false !== $wpdb->update( UCU_Collegium_Activator::slots_table(), $update, array( 'id' => $slot_id ) );
    }

    public function delete_slot( int $slot_id ): bool {
        global $wpdb;
        return false !== $wpdb->update( UCU_Collegium_Activator::slots_table(), array( 'status' => 'deleted', 'updated_at' => current_time( 'mysql' ) ), array( 'id' => $slot_id ) );
    }

    public function get_slot( int $slot_id ): ?array {
        global $wpdb;
        $slot = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . UCU_Collegium_Activator::slots_table() . ' WHERE id = %d', $slot_id ), ARRAY_A );
        return $slot ?: null;
    }

    public function get_available_slots(): array {
        global $wpdb;
        UCU_Collegium_Hold_Service::cleanup_expired_holds_static();
        $rows = $wpdb->get_results(
            "SELECT * FROM " . UCU_Collegium_Activator::slots_table() . " WHERE status = 'active' AND booked_count < capacity AND CONCAT(slot_date, ' ', end_time) >= '" . esc_sql( current_time( 'mysql' ) ) . "' ORDER BY slot_date ASC, start_time ASC",
            ARRAY_A
        );

        return array_values(
            array_filter(
                $rows,
                function ( $slot ) {
                    return $this->get_available_capacity( (int) $slot['id'] ) > 0;
                }
            )
        );
    }

    public function has_bookings( int $slot_id ): bool {
        global $wpdb;
        return (int) $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . UCU_Collegium_Activator::bookings_table() . " WHERE slot_id = %d AND status NOT IN ('cancelled','replaced','blocked_by_answer')", $slot_id ) ) > 0;
    }

    public function get_active_holds_count( int $slot_id ): int {
        global $wpdb;
        return (int) $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . UCU_Collegium_Activator::holds_table() . ' WHERE slot_id = %d AND expires_at > %s', $slot_id, current_time( 'mysql' ) ) );
    }

    public function get_available_capacity( int $slot_id ): int {
        $slot = $this->get_slot( $slot_id );
        if ( ! $slot || 'active' !== $slot['status'] ) {
            return 0;
        }
        return max( 0, (int) $slot['capacity'] - (int) $slot['booked_count'] - $this->get_active_holds_count( $slot_id ) );
    }

    public function atomic_book( int $slot_id ): bool {
        global $wpdb;
        $updated = $wpdb->query( $wpdb->prepare( 'UPDATE ' . UCU_Collegium_Activator::slots_table() . " SET booked_count = booked_count + 1, status = IF(booked_count + 1 >= capacity, 'full', status), updated_at = %s WHERE id = %d AND status = 'active' AND booked_count < capacity", current_time( 'mysql' ), $slot_id ) );
        return 1 === (int) $updated;
    }

    public function decrement_booked_count( int $slot_id ): void {
        global $wpdb;
        $wpdb->query( $wpdb->prepare( 'UPDATE ' . UCU_Collegium_Activator::slots_table() . " SET booked_count = IF(booked_count > 0, booked_count - 1, 0), status = IF(status = 'full', 'active', status), updated_at = %s WHERE id = %d", current_time( 'mysql' ), $slot_id ) );
    }
}
