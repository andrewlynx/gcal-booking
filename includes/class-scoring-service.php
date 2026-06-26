<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class UCU_Collegium_Scoring_Service {
    public function calculate_auto_score( array $form_data ): array {
        $score_details = array();

        foreach ( UCU_Collegium_Form_Fields::active_fields( $form_data ) as $field ) {
            if ( empty( $field['score_enabled'] ) ) {
                continue;
            }

            $key   = $field['key'];
            $value = $form_data[ $key ] ?? '';

            if ( is_array( $value ) ) {
                $points = 0.0;
                foreach ( $value as $item ) {
                    $points += (float) ( $field['score_map'][ $item ] ?? 0 );
                }
            } elseif ( ! empty( $field['score_map'] ) ) {
                $points = (float) ( $field['score_map'][ $value ] ?? 0 );
            } else {
                $filled = '' !== trim( (string) $value );
                $points = $filled ? (float) ( $field['default_score'] ?? 0 ) : 0.0;
            }

            if ( 0.0 !== $points ) {
                $score_details[ $key ] = $points;
            }
        }

        $total = array_sum( $score_details );
        $score_details = apply_filters( 'ucu_collegium_booking_score_rules', $score_details, $form_data );
        $total         = array_sum( $score_details );
        $total = (float) apply_filters( 'ucu_collegium_booking_calculated_score', $total, $form_data, $score_details );
        return array( 'score' => $total, 'details' => $score_details );
    }
}
