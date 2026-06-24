<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class UCU_Collegium_Admin_Menu {
    public static function init(): void {
        add_action( 'admin_menu', array( __CLASS__, 'register' ) );
        add_action( 'admin_post_ucu_collegium_slot_save', array( 'UCU_Collegium_Admin_Slots', 'save' ) );
        add_action( 'admin_post_ucu_collegium_slot_delete', array( 'UCU_Collegium_Admin_Slots', 'delete' ) );
        add_action( 'admin_post_ucu_collegium_booking_action', array( 'UCU_Collegium_Admin_Bookings', 'handle_action' ) );
        add_action( 'admin_post_ucu_collegium_export_csv', array( 'UCU_Collegium_CSV_Export', 'output' ) );
    }

    public static function register(): void {
        add_menu_page( 'Колегіум Booking', 'Колегіум Booking', 'manage_options', 'ucu-collegium-booking-bookings', array( 'UCU_Collegium_Admin_Bookings', 'render' ), 'dashicons-calendar-alt', 26 );
        add_submenu_page( 'ucu-collegium-booking-bookings', 'Заявки', 'Заявки', 'manage_options', 'ucu-collegium-booking-bookings', array( 'UCU_Collegium_Admin_Bookings', 'render' ) );
        add_submenu_page( 'ucu-collegium-booking-bookings', 'Слоти', 'Слоти', 'manage_options', 'ucu-collegium-booking-slots', array( 'UCU_Collegium_Admin_Slots', 'render' ) );
        add_submenu_page( 'ucu-collegium-booking-bookings', 'Налаштування', 'Налаштування', 'manage_options', 'ucu-collegium-booking-settings', array( 'UCU_Collegium_Admin_Settings', 'render' ) );
    }
}
