<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class UCU_Collegium_Form_Fields {
    public static function get_fields(): array {
        $fields = [
            [ 'key' => 'last_name', 'label' => 'Прізвище', 'type' => 'text', 'required' => true, 'score_enabled' => true, 'default_score' => 1, 'options' => [], 'condition' => null ],
            [ 'key' => 'first_name', 'label' => 'Ім’я', 'type' => 'text', 'required' => true, 'score_enabled' => true, 'default_score' => 1, 'options' => [], 'condition' => null ],
            [ 'key' => 'middle_name', 'label' => 'По батькові', 'type' => 'text', 'required' => false, 'score_enabled' => true, 'default_score' => 1, 'options' => [], 'condition' => null ],
            [ 'key' => 'email', 'label' => 'E-mail', 'type' => 'email', 'required' => true, 'score_enabled' => true, 'default_score' => 1, 'options' => [], 'condition' => null ],
            [ 'key' => 'phone', 'label' => 'Номер телефону', 'type' => 'phone', 'required' => true, 'score_enabled' => true, 'default_score' => 1, 'options' => [], 'condition' => null ],
            [ 'key' => 'degree', 'label' => 'Ступінь навчання', 'type' => 'radio', 'required' => true, 'score_enabled' => true, 'default_score' => 1, 'options' => [ 'bachelor' => 'Бакалавр', 'master' => 'Магістр' ], 'condition' => null ],
            [ 'key' => 'bachelor_program', 'label' => 'Бакалаврська програма', 'type' => 'text', 'required' => true, 'score_enabled' => true, 'default_score' => 1, 'options' => [], 'condition' => [ 'field' => 'degree', 'operator' => '=', 'value' => 'bachelor' ] ],
            [ 'key' => 'bachelor_year', 'label' => 'Курс бакалаврату', 'type' => 'select', 'required' => true, 'score_enabled' => true, 'default_score' => 1, 'options' => [ '1' => '1 курс', '2' => '2 курс', '3' => '3 курс', '4' => '4 курс' ], 'condition' => [ 'field' => 'degree', 'operator' => '=', 'value' => 'bachelor' ], ],
            [ 'key' => 'master_program', 'label' => 'Магістерська програма', 'type' => 'text', 'required' => true, 'score_enabled' => true, 'default_score' => 1, 'options' => [], 'condition' => [ 'field' => 'degree', 'operator' => '=', 'value' => 'master' ], ],
            [ 'key' => 'master_year', 'label' => 'Курс магістратури', 'type' => 'select', 'required' => true, 'score_enabled' => true, 'default_score' => 1, 'options' => [ '1' => '1 курс', '2' => '2 курс' ], 'condition' => [ 'field' => 'degree', 'operator' => '=', 'value' => 'master' ], ],
            [ 'key' => 'previous_collegium_participant', 'label' => 'Чи були ви учасником формаційної програми Колегіуму?', 'type' => 'radio', 'required' => true, 'score_enabled' => true, 'default_score' => 1, 'options' => [ 'yes' => 'Так', 'no' => 'Ні' ], 'condition' => null ],
            [ 'key' => 'previous_program_details', 'label' => 'Коли і в якій програмі ви брали участь?', 'type' => 'textarea', 'required' => true, 'score_enabled' => true, 'default_score' => 1, 'options' => [], 'condition' => [ 'field' => 'previous_collegium_participant', 'operator' => '=', 'value' => 'yes' ], ],
            [ 'key' => 'motivation', 'label' => 'Чому ви хочете долучитися до Колегіуму?', 'type' => 'textarea', 'required' => true, 'score_enabled' => true, 'default_score' => 1, 'options' => [], 'condition' => [ 'field' => 'previous_collegium_participant', 'operator' => '=', 'value' => 'no' ], ],
            [ 'key' => 'special_category', 'label' => 'Особлива категорія', 'type' => 'select', 'required' => false, 'score_enabled' => true, 'default_score' => 1, 'options' => [ '' => 'Не обрано', 'none' => 'Немає', 'veteran_family' => 'Родина військовослужбовця/ветерана', 'idp' => 'ВПО', 'other' => 'Інше' ], 'condition' => null ],
            [ 'key' => 'special_category_other', 'label' => 'Опишіть іншу категорію', 'type' => 'text', 'required' => true, 'score_enabled' => true, 'default_score' => 1, 'options' => [], 'condition' => [ 'field' => 'special_category', 'operator' => '=', 'value' => 'other' ], ],
            [ 'key' => 'housing_rules_consent', 'label' => 'Погоджуюся з правилами проживання', 'type' => 'radio', 'required' => true, 'score_enabled' => false, 'default_score' => 0, 'options' => [ 'yes' => 'Так', 'no' => 'Ні' ], 'condition' => null ],
            [ 'key' => 'personal_data_consent', 'label' => 'Даю згоду на обробку персональних даних', 'type' => 'radio', 'required' => true, 'score_enabled' => false, 'default_score' => 0, 'options' => [ 'yes' => 'Так', 'no' => 'Ні' ], 'condition' => null ],
        ];

        return apply_filters( 'ucu_collegium_booking_form_fields', $fields );
    }

    public static function fields_by_key(): array {
        $map = [];
        foreach ( self::get_fields() as $field ) {
            $map[ $field['key'] ] = $field;
        }
        return $map;
    }

    public static function is_field_active( array $field, array $data ): bool {
        if ( empty( $field['condition'] ) || ! is_array( $field['condition'] ) ) {
            return true;
        }

        $condition = $field['condition'];
        $actual    = $data[ $condition['field'] ] ?? null;
        $expected  = $condition['value'] ?? null;
        $operator  = $condition['operator'] ?? '=';

        switch ( $operator ) {
            case '!=':
                return (string) $actual !== (string) $expected;
            case 'in':
                return in_array( $actual, (array) $expected, true );
            case 'not_in':
                return ! in_array( $actual, (array) $expected, true );
            case '=':
            default:
                return (string) $actual === (string) $expected;
        }
    }

    public static function active_fields( array $data ): array {
        return array_values(
            array_filter(
                self::get_fields(),
                static function ( $field ) use ( $data ) {
                    return self::is_field_active( $field, $data );
                }
            )
        );
    }
}
