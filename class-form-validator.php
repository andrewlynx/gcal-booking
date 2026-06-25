<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class UCU_Collegium_Settings {
    public const OPTION = 'ucu_collegium_booking_settings';

    public static function init(): void {
        add_action( 'admin_init', array( __CLASS__, 'register' ) );
    }

    public static function register(): void {
        register_setting(
            'ucu_collegium_booking_settings',
            self::OPTION,
            array(
                'sanitize_callback' => array( __CLASS__, 'sanitize' ),
                'default'           => self::defaults(),
            )
        );
    }

    public static function defaults(): array {
        return array(
            'calendar_id'           => '',
            'credentials_json'      => '',
            'credentials_path'      => '',
            'create_meet'           => '1',
            'event_title_template'  => 'Співбесіда Колегіуму — {full_name}',
            'event_description_template' => "Дата: {date}\nЧас: {time}\nMeet: {meet_link}",
            'meet_link'             => '',
            'spreadsheet_id'        => '',
            'sheet_name'            => 'Applications',
            'enable_sheets'         => '0',
            'admin_email'           => get_option( 'admin_email' ),
            'from_email'            => get_option( 'admin_email' ),
            'from_name'             => get_bloginfo( 'name' ),
            'user_subject'          => 'Підтвердження заявки до Колегіуму',
            'admin_subject'         => 'Нова заявка до Колегіуму',
            'success_message'       => 'Дякуємо! Вашу заявку прийнято. Перевірте пошту для деталей співбесіди.',
            'slot_unavailable_message' => 'Вибачте, на жаль цей час вже недоступний, спробуйте вибрати інший час.',
            'blocked_message'       => 'Дякуємо. Вашу анкету збережено, але вона не може бути прийнята через відповіді в обов’язкових згодах.',
            'generic_error_message' => 'Сталася помилка. Спробуйте ще раз або зверніться до адміністратора.',
        );
    }

    public static function add_defaults(): void {
        if ( false === get_option( self::OPTION, false ) ) {
            add_option( self::OPTION, self::defaults() );
        }
    }

    public static function get( ?string $key = null, $default = null ) {
        $settings = wp_parse_args( (array) get_option( self::OPTION, array() ), self::defaults() );
        if ( null === $key ) {
            return $settings;
        }
        return array_key_exists( $key, $settings ) ? $settings[ $key ] : $default;
    }

    public static function sanitize( $input ): array {
        $input = (array) $input;
        $clean = self::defaults();

        foreach ( $clean as $key => $value ) {
            if ( ! array_key_exists( $key, $input ) ) {
                continue;
            }
            if ( 'credentials_json' === $key ) {
                // Зберігаємо JSON як є — без wp_unslash щоб не пошкодити \n в private_key
                $raw = $input[ $key ];
                // WordPress автоматично додає слеші (magic quotes) — прибираємо їх правильно
                // але НЕ чіпаємо \n які є частиною JSON
                $decoded = json_decode( $raw, true );
                if ( ! $decoded ) {
                    // Спроба з wp_unslash якщо прямий decode не вдався
                    $decoded = json_decode( wp_unslash( $raw ), true );
                    if ( $decoded ) {
                        $raw = wp_unslash( $raw );
                    }
                }
                // Нормалізуємо private_key прямо тут при збереженні
                if ( $decoded && isset( $decoded['private_key'] ) ) {
                    $key_val = $decoded['private_key'];
                    // Відновлюємо реальні переноси рядків
                    $key_val = str_replace( '\n', "
", $key_val );
                    if ( substr_count( $key_val, "
" ) < 3 ) {
                        if ( preg_match( '/-----BEGIN ([A-Z ]+)-----(.*?)-----END ([A-Z ]+)-----/s', $key_val, $m ) ) {
                            $body    = preg_replace( '/[^A-Za-z0-9+\/=]/', '', $m[2] );
                            $body    = chunk_split( $body, 64, "
" );
                            $key_val = "-----BEGIN {$m[1]}-----
" . $body . "-----END {$m[3]}-----
";
                        }
                    }
                    $decoded['private_key'] = $key_val;
                    $clean[ $key ] = wp_json_encode( $decoded );
                } else {
                    $clean[ $key ] = trim( $raw );
                }
            } elseif ( 'event_description_template' === $key ) {
                $clean[ $key ] = sanitize_textarea_field( wp_unslash( $input[ $key ] ) );
            } elseif ( in_array( $key, array( 'create_meet', 'enable_sheets' ), true ) ) {
                $clean[ $key ] = ! empty( $input[ $key ] ) ? '1' : '0';
            } elseif ( false !== strpos( $key, 'email' ) ) {
                $clean[ $key ] = sanitize_email( $input[ $key ] );
            } else {
                $clean[ $key ] = sanitize_text_field( wp_unslash( $input[ $key ] ) );
            }
        }

        return $clean;
    }

    public static function messages(): array {
        return array(
            'success'         => self::get( 'success_message' ),
            'slotUnavailable' => self::get( 'slot_unavailable_message' ),
            'blocked'         => self::get( 'blocked_message' ),
            'genericError'    => self::get( 'generic_error_message' ),
        );
    }
}
