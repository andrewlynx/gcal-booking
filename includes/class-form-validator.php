<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class UCU_Collegium_Form_Validator {
    public static function sanitize_form_data( array $raw ): array {
        $data = array();
        foreach ( UCU_Collegium_Form_Fields::get_fields() as $field ) {
            if ( 'attachment' === $field['type'] ) {
                continue;
            }

            $key   = $field['key'];
            $value = $raw[ $key ] ?? '';

            if ( is_array( $value ) ) {
                $value = array_map( 'sanitize_text_field', wp_unslash( $value ) );
            } else {
                $value = wp_unslash( $value );
                if ( 'email' === $field['type'] ) {
                    $value = sanitize_email( $value );
                } elseif ( 'textarea' === $field['type'] ) {
                    $value = sanitize_textarea_field( $value );
                } else {
                    $value = sanitize_text_field( $value );
                }
            }
            $data[ $key ] = $value;
        }
        return $data;
    }

    public static function validate( array $data ): array {
        $errors = array();

        foreach ( UCU_Collegium_Form_Fields::active_fields( $data ) as $field ) {
            if ( 'attachment' === $field['type'] ) {
                continue;
            }

            $key   = $field['key'];
            $value = $data[ $key ] ?? '';

            if ( ! empty( $field['required'] ) && self::is_empty( $value ) ) {
                $errors[ $key ] = 'Поле обов’язкове.';
                continue;
            }

            if ( ! self::is_empty( $value ) && 'email' === $field['type'] && ! is_email( $value ) ) {
                $errors[ $key ] = 'Введіть коректний email.';
            }

            if ( ! empty( $field['options'] ) && ! self::is_empty( $value ) ) {
                $allowed = array_keys( $field['options'] );
                $submitted = is_array( $value ) ? $value : array( $value );
                $invalid   = array_diff( array_map( 'strval', $submitted ), array_map( 'strval', $allowed ) );
                if ( ! empty( $invalid ) ) {
                    $errors[ $key ] = 'Некоректне значення.';
                }
            }
        }

        return $errors;
    }

    private static function is_empty( $value ): bool {
        return is_array( $value ) ? empty( $value ) : '' === trim( (string) $value );
    }
}
