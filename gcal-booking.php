<?php
/**
 * Plugin Name: Google Calendar Booking Form
 * Description: Форма запису користувача з відправкою даних на API Google Calendar
 * Version: 1.0.0
 * Author: UCU
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'GCAL_BOOKING_VERSION', '1.0.0' );
define( 'GCAL_BOOKING_PATH', plugin_dir_path( __FILE__ ) );
define( 'GCAL_BOOKING_URL', plugin_dir_url( __FILE__ ) );

// ── Підключення стилів і скриптів ──────────────────────────────────────────
add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'gcal-booking-style',
        GCAL_BOOKING_URL . 'assets/style.css',
        [],
        GCAL_BOOKING_VERSION
    );

    wp_enqueue_script(
        'gcal-booking-script',
        GCAL_BOOKING_URL . 'assets/script.js',
        [ 'jquery' ],
        GCAL_BOOKING_VERSION,
        true
    );

    // Передаємо дані в JS (URL і nonce)
    wp_localize_script( 'gcal-booking-script', 'gcalBooking', [
        'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'gcal_booking_nonce' ),
        'apiUrl'   => get_option( 'gcal_booking_api_url', '' ),
    ]);
});

// ── Shortcode [gcal_booking_form] ───────────────────────────────────────────
add_shortcode( 'gcal_booking_form', function () {
    ob_start();
    include GCAL_BOOKING_PATH . 'templates/form.php';
    return ob_get_clean();
});

// ── AJAX обробник (відправка на зовнішній API) ──────────────────────────────
add_action( 'wp_ajax_gcal_submit',        'gcal_booking_handle_submit' );
add_action( 'wp_ajax_nopriv_gcal_submit', 'gcal_booking_handle_submit' );

function gcal_booking_handle_submit() {
    // Перевірка nonce
    if ( ! check_ajax_referer( 'gcal_booking_nonce', 'nonce', false ) ) {
        wp_send_json_error( [ 'message' => 'Помилка безпеки. Оновіть сторінку.' ], 403 );
    }

    // Валідація полів
    $name  = sanitize_text_field( $_POST['name'] ?? '' );
    $phone = sanitize_text_field( $_POST['phone'] ?? '' );
    $date  = sanitize_text_field( $_POST['date'] ?? '' );
    $time  = sanitize_text_field( $_POST['time'] ?? '' );

    if ( ! $name || ! $phone || ! $date || ! $time ) {
        wp_send_json_error( [ 'message' => 'Будь ласка, заповніть усі поля.' ] );
    }

    $api_url = get_option( 'gcal_booking_api_url', '' );

    if ( ! $api_url ) {
        wp_send_json_error( [ 'message' => 'API URL не налаштовано. Зверніться до адміністратора.' ] );
    }

    // Відправка на зовнішній API
    $response = wp_remote_post( $api_url, [
        'headers' => [ 'Content-Type' => 'application/json' ],
        'body'    => wp_json_encode([
            'name'  => $name,
            'phone' => $phone,
            'date'  => $date,
            'time'  => $time,
        ]),
        'timeout' => 15,
    ]);

    if ( is_wp_error( $response ) ) {
        wp_send_json_error( [ 'message' => 'Не вдалось зв\'язатися з сервером. Спробуйте пізніше.' ] );
    }

    $code = wp_remote_retrieve_response_code( $response );

    if ( $code >= 200 && $code < 300 ) {
        wp_send_json_success( [ 'message' => 'Дякуємо! Вашу заявку прийнято. Очікуйте підтвердження.' ] );
    } else {
        wp_send_json_error( [ 'message' => 'Помилка сервера (' . $code . '). Спробуйте пізніше.' ] );
    }
}

// ── Сторінка налаштувань в адмінці ─────────────────────────────────────────
add_action( 'admin_menu', function () {
    add_options_page(
        'Google Calendar Booking',
        'GCal Booking',
        'manage_options',
        'gcal-booking',
        'gcal_booking_settings_page'
    );
});

add_action( 'admin_init', function () {
    register_setting( 'gcal_booking_settings', 'gcal_booking_api_url', [
        'sanitize_callback' => 'esc_url_raw',
    ]);
});

function gcal_booking_settings_page() {
    ?>
    <div class="wrap">
        <h1>Google Calendar Booking — Налаштування</h1>
        <form method="post" action="options.php">
            <?php settings_fields( 'gcal_booking_settings' ); ?>
            <table class="form-table">
                <tr>
                    <th><label for="gcal_booking_api_url">API URL</label></th>
                    <td>
                        <input
                            type="url"
                            id="gcal_booking_api_url"
                            name="gcal_booking_api_url"
                            value="<?php echo esc_attr( get_option( 'gcal_booking_api_url' ) ); ?>"
                            class="regular-text"
                            placeholder="https://your-api.example.com/booking"
                        />
                        <p class="description">URL бекенду, який отримує дані і створює подію в Google Calendar.</p>
                    </td>
                </tr>
            </table>
            <?php submit_button( 'Зберегти' ); ?>
        </form>
        <hr>
        <h2>Як використовувати</h2>
        <p>Додайте шорткод на будь-яку сторінку або пост:</p>
        <code>[gcal_booking_form]</code>
    </div>
    <?php
}
