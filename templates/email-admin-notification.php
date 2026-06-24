<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<p>Нова заявка до Колегіуму.</p>
<ul>
    <li><strong>ПІБ:</strong> <?php echo esc_html( trim( $booking['last_name'] . ' ' . $booking['first_name'] . ' ' . $booking['middle_name'] ) ); ?></li>
    <li><strong>Email:</strong> <?php echo esc_html( $booking['email'] ); ?></li>
    <li><strong>Телефон:</strong> <?php echo esc_html( $booking['phone'] ); ?></li>
    <li><strong>Слот:</strong> <?php echo $slot ? esc_html( mysql2date( 'd.m.Y', $slot['slot_date'] ) . ' ' . substr( $slot['start_time'], 0, 5 ) . '-' . substr( $slot['end_time'], 0, 5 ) ) : '—'; ?></li>
    <li><strong>Status:</strong> <?php echo esc_html( $booking['status'] ); ?></li>
    <li><strong>Total score:</strong> <?php echo esc_html( $booking['total_score'] ); ?></li>
</ul>
<p><a href="<?php echo esc_url( admin_url( 'admin.php?page=ucu-collegium-booking-bookings&action=view&booking_id=' . (int) $booking['id'] ) ); ?>">Переглянути заявку</a></p>
