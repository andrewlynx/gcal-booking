<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class UCU_Collegium_Activator {
    public static function activate(): void {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset = $wpdb->get_charset_collate();
        $slots   = self::slots_table();
        $bookings = self::bookings_table();
        $holds   = self::holds_table();

        dbDelta( "CREATE TABLE $slots (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            slot_date DATE NOT NULL,
            start_time TIME NOT NULL,
            end_time TIME NOT NULL,
            capacity TINYINT UNSIGNED NOT NULL DEFAULT 5,
            booked_count TINYINT UNSIGNED NOT NULL DEFAULT 0,
            status VARCHAR(30) NOT NULL DEFAULT 'active',
            created_at DATETIME NOT NULL,
            updated_at DATETIME NULL,
            PRIMARY KEY (id),
            KEY slot_date (slot_date),
            KEY status (status)
        ) $charset;" );

        dbDelta( "CREATE TABLE $bookings (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            slot_id BIGINT UNSIGNED NULL,
            email VARCHAR(190) NOT NULL,
            first_name VARCHAR(190) NULL,
            last_name VARCHAR(190) NULL,
            middle_name VARCHAR(190) NULL,
            phone VARCHAR(100) NULL,
            form_data LONGTEXT NOT NULL,
            photo_attachment_id BIGINT UNSIGNED NULL,
            auto_score DECIMAL(10,2) NOT NULL DEFAULT 0,
            manual_score DECIMAL(10,2) NOT NULL DEFAULT 0,
            interview_score DECIMAL(10,2) NOT NULL DEFAULT 0,
            total_score DECIMAL(10,2) NOT NULL DEFAULT 0,
            calendar_event_id VARCHAR(255) NULL,
            meet_link TEXT NULL,
            sheet_row_id VARCHAR(255) NULL,
            status VARCHAR(50) NOT NULL DEFAULT 'new',
            error_message TEXT NULL,
            is_current TINYINT(1) NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NULL,
            PRIMARY KEY (id),
            KEY email (email),
            KEY slot_id (slot_id),
            KEY status (status),
            KEY is_current (is_current)
        ) $charset;" );

        dbDelta( "CREATE TABLE $holds (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            slot_id BIGINT UNSIGNED NOT NULL,
            session_token VARCHAR(190) NOT NULL,
            email VARCHAR(190) NULL,
            expires_at DATETIME NOT NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            KEY slot_id (slot_id),
            KEY session_token (session_token),
            KEY expires_at (expires_at)
        ) $charset;" );

        UCU_Collegium_Settings::add_defaults();

        if ( ! wp_next_scheduled( 'ucu_collegium_cleanup_holds' ) ) {
            wp_schedule_event( time() + 300, 'hourly', 'ucu_collegium_cleanup_holds' );
        }
    }

    public static function slots_table(): string {
        global $wpdb;
        return $wpdb->prefix . 'ucu_collegium_slots';
    }

    public static function bookings_table(): string {
        global $wpdb;
        return $wpdb->prefix . 'ucu_collegium_bookings';
    }

    public static function holds_table(): string {
        global $wpdb;
        return $wpdb->prefix . 'ucu_collegium_slot_holds';
    }
}
