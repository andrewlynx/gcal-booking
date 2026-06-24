<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class UCU_Collegium_Assets {
    public static function init(): void {
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'maybe_enqueue_frontend' ) );
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin' ) );
    }

    public static function maybe_enqueue_frontend(): void {
        wp_register_style( 'ucu-collegium-booking-frontend', UCU_COLLEGIUM_BOOKING_URL . 'assets/frontend.css', array(), UCU_COLLEGIUM_BOOKING_VERSION );
        wp_register_script( 'ucu-collegium-booking-frontend', UCU_COLLEGIUM_BOOKING_URL . 'assets/frontend.js', array( 'jquery' ), UCU_COLLEGIUM_BOOKING_VERSION, true );

        if ( is_singular() ) {
            $post = get_post();
            if ( $post && has_shortcode( $post->post_content, 'ucu_collegium_booking_form' ) ) {
                self::enqueue_frontend();
            }
        }
    }

    public static function enqueue_frontend(): void {
        wp_enqueue_style( 'ucu-collegium-booking-frontend' );
        wp_enqueue_script( 'ucu-collegium-booking-frontend' );
        wp_localize_script(
            'ucu-collegium-booking-frontend',
            'ucuCollegiumBooking',
            array(
                'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'ucu_collegium_booking_nonce' ),
                'messages' => UCU_Collegium_Settings::messages(),
            )
        );
    }

    public static function enqueue_admin( string $hook ): void {
        if ( false !== strpos( $hook, 'ucu-collegium-booking' ) ) {
            wp_enqueue_style( 'ucu-collegium-booking-admin', UCU_COLLEGIUM_BOOKING_URL . 'assets/admin.css', array(), UCU_COLLEGIUM_BOOKING_VERSION );
        }
    }
}
