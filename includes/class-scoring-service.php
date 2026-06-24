<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class UCU_Collegium_Scoring_Service {
    public function calculate_auto_score( array $form_data ): array {
        $score_details = array();
        $default_rules = array();
        foreach ( UCU_Collegium_Form_Fields::active_fields( $form_data ) as $field ) {
            if ( ! empty( $field['score_enabled'] ) ) {
                $default_rules[ $field['key'] ] = (float) ( $field['default_score'] ?? 1 );
            }
        }

        $rules = apply_filters( 'ucu_collegium_booking_score_rules', $default_rules, $form_data );
        $total = 0.0;
        foreach ( $rules as $key => $points ) {
            $value = $form_data[ $key ] ?? '';
            $filled = is_array( $value ) ? ! empty( $value ) : '' !== trim( (string) $value );
            if ( $filled ) {
                $score_details[ $key ] = (float) $points;
                $total += (float) $points;
            }
        }

        $total = (float) apply_filters( 'ucu_collegium_booking_calculated_score', $total, $form_data, $score_details );
        return array( 'score' => $total, 'details' => $score_details );
    }
}
