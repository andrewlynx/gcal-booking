<?php if ( ! defined( 'ABSPATH' ) ) { exit; } $settings = UCU_Collegium_Settings::get(); ?>
<div class="wrap">
    <h1>Колегіум Booking — Налаштування</h1>
    <form method="post" action="options.php">
        <?php settings_fields( 'ucu_collegium_booking_settings' ); ?>
        <table class="form-table ucu-form-table" role="presentation">
            <?php
            $fields = array(
                'calendar_id' => 'Google Calendar ID', 'credentials_path' => 'Шлях до credentials JSON', 'event_title_template' => 'Шаблон назви події',
                'spreadsheet_id' => 'Google Spreadsheet ID', 'sheet_name' => 'Google Sheet name', 'admin_email' => 'Admin email', 'from_email' => 'From email', 'from_name' => 'From name',
                'user_subject' => 'Subject листа вступнику', 'admin_subject' => 'Subject листа адміністратору', 'success_message' => 'Success message', 'slot_unavailable_message' => 'Slot unavailable message', 'blocked_message' => 'Blocked application message', 'generic_error_message' => 'Generic error message',
            );
            foreach ( $fields as $key => $label ) : ?>
                <tr><th><label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th><td><input class="regular-text" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( UCU_Collegium_Settings::OPTION . '[' . $key . ']' ); ?>" value="<?php echo esc_attr( $settings[ $key ] ); ?>"></td></tr>
            <?php endforeach; ?>
            <tr><th><label for="credentials_json">Google credentials JSON</label></th><td><textarea class="large-text code" id="credentials_json" name="<?php echo esc_attr( UCU_Collegium_Settings::OPTION ); ?>[credentials_json]" rows="8"><?php echo esc_textarea( $settings['credentials_json'] ); ?></textarea><p class="description">Не показуйте цей вміст публічно. Краще використовувати шлях до файла поза webroot.</p></td></tr>
            <tr><th><label for="event_description_template">Шаблон опису події</label></th><td><textarea class="large-text" id="event_description_template" name="<?php echo esc_attr( UCU_Collegium_Settings::OPTION ); ?>[event_description_template]" rows="4"><?php echo esc_textarea( $settings['event_description_template'] ); ?></textarea></td></tr>
            <tr><th>Створювати Google Meet</th><td><label><input type="checkbox" name="<?php echo esc_attr( UCU_Collegium_Settings::OPTION ); ?>[create_meet]" value="1" <?php checked( $settings['create_meet'], '1' ); ?>> Так</label></td></tr>
            <tr><th>Enable Sheets sync</th><td><label><input type="checkbox" name="<?php echo esc_attr( UCU_Collegium_Settings::OPTION ); ?>[enable_sheets]" value="1" <?php checked( $settings['enable_sheets'], '1' ); ?>> Так</label></td></tr>
        </table>
        <?php submit_button( 'Зберегти налаштування' ); ?>
    </form>
    <p>Shortcode: <code>[ucu_collegium_booking_form]</code></p>
</div>
