<?php
/**
 * Plugin Name: UCU Collegium Booking
 * Description: Анкета вступника Колегіуму з бронюванням співбесіди, Google Calendar/Meet/Sheets та адмінкою заявок.
 * Version: 1.0.0
 * Author: UCU
 * Text Domain: ucu-collegium-booking
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/ucu-collegium-booking.php';

register_activation_hook( __FILE__, array( 'UCU_Collegium_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'UCU_Collegium_Deactivator', 'deactivate' ) );
