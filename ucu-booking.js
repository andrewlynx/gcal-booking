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

define( 'UCU_COLLEGIUM_BOOKING_VERSION', '1.0.0' );
define( 'UCU_COLLEGIUM_BOOKING_PATH', plugin_dir_path( __FILE__ ) );
define( 'UCU_COLLEGIUM_BOOKING_URL', plugin_dir_url( __FILE__ ) );
define( 'UCU_COLLEGIUM_BOOKING_PLUGIN_FILE', __FILE__ );

spl_autoload_register(
    static function ( $class ) {
        if ( 0 !== strpos( $class, 'UCU_Collegium_' ) ) {
            return;
        }

        $relative = strtolower( str_replace( '_', '-', substr( $class, strlen( 'UCU_Collegium_' ) ) ) );
        $paths    = array(
            UCU_COLLEGIUM_BOOKING_PATH . 'includes/class-' . $relative . '.php',
            UCU_COLLEGIUM_BOOKING_PATH . 'admin/class-admin-' . str_replace( 'admin-', '', $relative ) . '.php',
        );

        foreach ( $paths as $path ) {
            if ( file_exists( $path ) ) {
                require_once $path;
                return;
            }
        }
    }
);

register_activation_hook( __FILE__, array( 'UCU_Collegium_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'UCU_Collegium_Deactivator', 'deactivate' ) );

add_action(
    'plugins_loaded',
    static function () {
        UCU_Collegium_Plugin::instance()->run();
    }
);
