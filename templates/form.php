<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="ucu-booking" data-ucu-booking-form>
    <h2 class="ucu-booking__title">Анкета вступника до Колегіуму</h2>
    <div class="ucu-booking__notice ucu-booking__notice--success" data-ucu-success hidden></div>
    <div class="ucu-booking__notice ucu-booking__notice--error" data-ucu-error hidden></div>

    <form class="ucu-booking__form" enctype="multipart/form-data" novalidate>
        <input type="hidden" name="action" value="ucu_collegium_submit_booking">
        <input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'ucu_collegium_booking_nonce' ) ); ?>">
        <input type="hidden" name="session_token" value="<?php echo esc_attr( $session_token ); ?>">

        <div class="ucu-booking__grid">
            <?php foreach ( $fields as $field ) : ?>
                <?php
                $key       = $field['key'];
                $id        = 'ucu-field-' . sanitize_html_class( $key );
                $condition = ! empty( $field['condition'] ) ? wp_json_encode( $field['condition'] ) : '';
                $required  = ! empty( $field['required'] );
                ?>
                <div class="ucu-field ucu-field--<?php echo esc_attr( $field['type'] ); ?>" data-field-key="<?php echo esc_attr( $key ); ?>" <?php echo $condition ? 'data-condition="' . esc_attr( $condition ) . '"' : ''; ?> data-required="<?php echo $required ? '1' : '0'; ?>">
                    <label class="ucu-field__label" for="<?php echo esc_attr( $id ); ?>">
                        <?php echo esc_html( $field['label'] ); ?><?php if ( $required ) : ?><span aria-hidden="true"> *</span><?php endif; ?>
                    </label>
                    <?php if ( in_array( $field['type'], array( 'text', 'email', 'date' ), true ) ) : ?>
                        <input id="<?php echo esc_attr( $id ); ?>" type="<?php echo esc_attr( $field['type'] ); ?>" name="<?php echo esc_attr( $key ); ?>" <?php echo $required ? 'required' : ''; ?>>
                    <?php elseif ( 'phone' === $field['type'] ) : ?>
                        <input id="<?php echo esc_attr( $id ); ?>" type="tel" name="<?php echo esc_attr( $key ); ?>" autocomplete="tel" <?php echo $required ? 'required' : ''; ?>>
                    <?php elseif ( 'textarea' === $field['type'] ) : ?>
                        <textarea id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $key ); ?>" rows="4" <?php echo $required ? 'required' : ''; ?>></textarea>
                    <?php elseif ( 'select' === $field['type'] ) : ?>
                        <select id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $key ); ?>" <?php echo $required ? 'required' : ''; ?>>
                            <?php foreach ( $field['options'] as $value => $label ) : ?>
                                <option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php elseif ( 'radio' === $field['type'] ) : ?>
                        <div class="ucu-field__choices" role="radiogroup" aria-labelledby="<?php echo esc_attr( $id ); ?>-label">
                            <?php foreach ( $field['options'] as $value => $label ) : ?>
                                <label class="ucu-choice">
                                    <input type="radio" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php echo $required ? 'required' : ''; ?>>
                                    <span><?php echo esc_html( $label ); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <span class="ucu-field__error" data-field-error></span>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="ucu-field" data-required="1" data-field-key="photo">
            <label class="ucu-field__label" for="ucu-photo">Фото *</label>
            <input id="ucu-photo" type="file" name="photo" accept="image/jpeg,image/png,image/webp" required>
            <span class="ucu-field__hint">JPG, PNG або WebP до 5 MB.</span>
            <span class="ucu-field__error" data-field-error></span>
        </div>

        <div class="ucu-field" data-required="1" data-field-key="slot_id">
            <label class="ucu-field__label" for="ucu-slot-id">Слот співбесіди *</label>
            <select id="ucu-slot-id" name="slot_id" required data-ucu-slots>
                <option value="">Завантаження доступних слотів...</option>
            </select>
            <span class="ucu-field__hint" data-ucu-hold-message></span>
            <span class="ucu-field__error" data-field-error></span>
        </div>

        <button class="ucu-booking__submit" type="submit" data-ucu-submit>Надіслати заявку</button>
    </form>
</div>
