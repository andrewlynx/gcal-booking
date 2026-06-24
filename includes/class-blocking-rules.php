<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class UCU_Collegium_Blocking_Rules {
    public static function get_rules(): array {
        $rules = array(
            array( 'field' => 'housing_rules_consent', 'operator' => '=', 'value' => 'no', 'message' => 'Немає згоди з правилами проживання.' ),
            array( 'field' => 'personal_data_consent', 'operator' => '=', 'value' => 'no', 'message' => 'Немає згоди на обробку персональних даних.' ),
        );

        return apply_filters( 'ucu_collegium_booking_blocking_rules', $rules );
    }

    public static function evaluate( array $data ): array {
        $messages = array();
        foreach ( self::get_rules() as $rule ) {
            $field = array( 'condition' => $rule );
            if ( UCU_Collegium_Form_Fields::is_field_active( $field, $data ) ) {
                $messages[] = $rule['message'] ?? 'Блокуюча відповідь.';
            }
        }
        return $messages;
    }
}
