<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class UCU_Collegium_Mailer_Service {
    public function send_user_confirmation( int $booking_id ): bool {
        $booking = ( new UCU_Collegium_Booking_Service() )->get_booking( $booking_id );
        if ( ! $booking || empty( $booking['meet_link'] ) ) {
            return false;
        }
        $slot = ( new UCU_Collegium_Slot_Service() )->get_slot( (int) $booking['slot_id'] );
        $body = $this->render_template( 'email-user-confirmation.php', array( 'booking' => $booking, 'slot' => $slot ) );
        return $this->send( $booking['email'], UCU_Collegium_Settings::get( 'user_subject' ), $body );
    }

    public function send_admin_notification( int $booking_id ): bool {
        $booking = ( new UCU_Collegium_Booking_Service() )->get_booking( $booking_id );
        if ( ! $booking ) {
            return false;
        }
        $slot = $booking['slot_id'] ? ( new UCU_Collegium_Slot_Service() )->get_slot( (int) $booking['slot_id'] ) : null;
        $body = $this->render_template( 'email-admin-notification.php', array( 'booking' => $booking, 'slot' => $slot ) );
        return $this->send( UCU_Collegium_Settings::get( 'admin_email' ), UCU_Collegium_Settings::get( 'admin_subject' ), $body );
    }

    private function send( string $to, string $subject, string $body ): bool {
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . UCU_Collegium_Settings::get( 'from_name' ) . ' <' . UCU_Collegium_Settings::get( 'from_email' ) . '>',
        );
        return wp_mail( $to, $subject, $body, $headers );
    }

    private function render_template( string $template, array $vars ): string {
        extract( $vars, EXTR_SKIP );
        ob_start();
        include UCU_COLLEGIUM_BOOKING_PATH . 'templates/' . $template;
        return ob_get_clean();
    }
}
