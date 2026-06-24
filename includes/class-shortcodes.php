<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class UCU_Collegium_Shortcodes {
    public static function init(): void {
        add_shortcode( 'ucu_collegium_booking_form', array( __CLASS__, 'render_form' ) );
    }

    public static function render_form(): string {
        UCU_Collegium_Assets::enqueue_frontend();
        $blocks        = UCU_Collegium_Form_Fields::get_blocks();
        $session_token = wp_generate_uuid4();
        ob_start();
        include UCU_COLLEGIUM_BOOKING_PATH . 'templates/form.php';
        return ob_get_clean();
    }
}
