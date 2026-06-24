<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class UCU_Collegium_Deactivator {
    public static function deactivate(): void {
        wp_clear_scheduled_hook( 'ucu_collegium_cleanup_holds' );
    }
}
