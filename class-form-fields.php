<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<p>Вітаємо, <?php echo esc_html( $booking['first_name'] ); ?>!</p>
<p>Вашу заявку до Колегіуму прийнято.</p>
<p><strong>Співбесіда:</strong> <?php echo esc_html( mysql2date( 'd.m.Y', $slot['slot_date'] ) ); ?>, <?php echo esc_html( substr( $slot['start_time'], 0, 5 ) . '-' . substr( $slot['end_time'], 0, 5 ) ); ?></p>
<p><strong>Google Meet:</strong> <a href="<?php echo esc_url( $booking['meet_link'] ); ?>"><?php echo esc_html( $booking['meet_link'] ); ?></a></p>
<h3>Копія відповідей</h3>
<table cellpadding="6" cellspacing="0" border="1">
    <?php foreach ( UCU_Collegium_Form_Fields::get_fields() as $field ) : ?>
        <?php if ( 'attachment' === $field['type'] ) { continue; } ?>
        <tr><th align="left"><?php echo esc_html( $field['label'] ); ?></th><td><?php echo esc_html( UCU_Collegium_Form_Fields::format_value( $field, $booking['form_data_array'][ $field['key'] ] ?? '' ) ); ?></td></tr>
    <?php endforeach; ?>
</table>
