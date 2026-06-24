<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$render_field = static function ( array $field ): void {
    $key       = $field['key'];
    $id        = 'ucu-field-' . sanitize_html_class( $key );
    $type      = $field['type'];
    $condition = ! empty( $field['condition'] ) ? wp_json_encode( $field['condition'] ) : '';
    $required  = ! empty( $field['required'] );
    $class     = 'ucu-field ucu-field--' . sanitize_html_class( $type );
    ?>
    <div class="<?php echo esc_attr( $class ); ?>" data-field-key="<?php echo esc_attr( $key ); ?>" <?php echo $condition ? 'data-condition="' . esc_attr( $condition ) . '"' : ''; ?> data-required="<?php echo $required ? '1' : '0'; ?>">
        <label class="ucu-field__label" for="<?php echo esc_attr( $id ); ?>">
            <?php echo esc_html( $field['label'] ); ?><?php if ( $required ) : ?><span aria-hidden="true"> *</span><?php endif; ?>
        </label>

        <?php if ( in_array( $type, array( 'text', 'email', 'date' ), true ) ) : ?>
            <input id="<?php echo esc_attr( $id ); ?>" type="<?php echo esc_attr( $type ); ?>" name="<?php echo esc_attr( $key ); ?>" <?php echo $required ? 'required' : ''; ?>>
        <?php elseif ( 'phone' === $type ) : ?>
            <input id="<?php echo esc_attr( $id ); ?>" type="tel" name="<?php echo esc_attr( $key ); ?>" autocomplete="tel" <?php echo $required ? 'required' : ''; ?>>
        <?php elseif ( 'textarea' === $type ) : ?>
            <textarea id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $key ); ?>" rows="4" <?php echo $required ? 'required' : ''; ?>></textarea>
        <?php elseif ( 'attachment' === $type ) : ?>
            <input id="<?php echo esc_attr( $id ); ?>" type="file" name="<?php echo esc_attr( $key ); ?>" accept="image/jpeg,image/png,image/webp" <?php echo $required ? 'required' : ''; ?>>
            <span class="ucu-field__hint">JPG, PNG або WebP до <?php echo esc_html( (string) ( $field['max_size_mb'] ?? 5 ) ); ?> MB.</span>
        <?php elseif ( 'select' === $type ) : ?>
            <select id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $key ); ?>" <?php echo $required ? 'required' : ''; ?>>
                <option value="">Оберіть варіант</option>
                <?php foreach ( $field['options'] as $value => $label ) : ?>
                    <option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
                <?php endforeach; ?>
            </select>
        <?php elseif ( in_array( $type, array( 'radio', 'checkbox' ), true ) ) : ?>
            <div class="ucu-field__choices" role="<?php echo 'radio' === $type ? 'radiogroup' : 'group'; ?>">
                <?php foreach ( $field['options'] as $value => $label ) : ?>
                    <label class="ucu-choice">
                        <input type="<?php echo esc_attr( $type ); ?>" name="<?php echo esc_attr( $key . ( 'checkbox' === $type ? '[]' : '' ) ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php echo ( $required && 'radio' === $type ) ? 'required' : ''; ?>>
                        <span><?php echo esc_html( $label ); ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <span class="ucu-field__error" data-field-error></span>
    </div>
    <?php
};
?>
<div class="ucu-booking" data-ucu-booking-form>
    <h2 class="ucu-booking__title">Анкета вступника до Колегіуму</h2>
    <div class="ucu-booking__notice ucu-booking__notice--success" data-ucu-success hidden></div>
    <div class="ucu-booking__notice ucu-booking__notice--error" data-ucu-error hidden></div>

    <form class="ucu-booking__form" enctype="multipart/form-data" novalidate>
        <input type="hidden" name="action" value="ucu_collegium_submit_booking">
        <input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'ucu_collegium_booking_nonce' ) ); ?>">
        <input type="hidden" name="session_token" value="<?php echo esc_attr( $session_token ); ?>">

        <?php foreach ( $blocks as $block ) : ?>
            <section class="ucu-booking__block" data-block-key="<?php echo esc_attr( $block['key'] ?? '' ); ?>">
                <h3 class="ucu-booking__block-title"><?php echo esc_html( $block['title'] ?? '' ); ?></h3>
                <?php if ( ! empty( $block['description'] ) ) : ?><p class="ucu-booking__block-description"><?php echo esc_html( $block['description'] ); ?></p><?php endif; ?>
                <div class="ucu-booking__grid">
                    <?php foreach ( $block['fields'] ?? array() as $field ) : ?>
                        <?php $render_field( $field ); ?>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>

        <section class="ucu-booking__block">
            <h3 class="ucu-booking__block-title">Співбесіда</h3>
            <div class="ucu-field" data-required="1" data-field-key="slot_id">
                <label class="ucu-field__label" for="ucu-slot-id">Слот співбесіди *</label>
                <select id="ucu-slot-id" name="slot_id" required data-ucu-slots>
                    <option value="">Завантаження доступних слотів...</option>
                </select>
                <span class="ucu-field__hint" data-ucu-hold-message></span>
                <span class="ucu-field__error" data-field-error></span>
            </div>
        </section>

        <button class="ucu-booking__submit" type="submit" data-ucu-submit>Надіслати заявку</button>
    </form>
</div>
