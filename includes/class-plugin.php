<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class UCU_Collegium_Plugin {
    private static $instance = null;

    public static function instance(): self {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function run(): void {
        UCU_Collegium_Settings::init();
        UCU_Collegium_Assets::init();
        UCU_Collegium_Shortcodes::init();
        UCU_Collegium_Ajax::init();

        if ( is_admin() ) {
            UCU_Collegium_Admin_Menu::init();
        }
    }
}
