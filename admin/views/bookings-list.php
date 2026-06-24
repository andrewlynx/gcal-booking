<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<div class="wrap">
    <h1>Заявки</h1>
    <form method="get">
        <input type="hidden" name="page" value="ucu-collegium-booking-bookings">
        <input type="text" name="status" value="<?php echo esc_attr( $status ); ?>" placeholder="status">
        <input type="date" name="slot_date" value="<?php echo esc_attr( $slot_date ); ?>">
        <?php submit_button( 'Фільтрувати', 'secondary', '', false ); ?>
        <a class="button" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=ucu_collegium_export_csv' ), 'ucu_collegium_export_csv' ) ); ?>">Export CSV</a>
    </form>
    <table class="widefat striped">
        <thead><tr><th>ID</th><th>Дата подання</th><th>ПІБ</th><th>Email</th><th>Телефон</th><th>Слот</th><th>Status</th><th>Auto</th><th>Manual</th><th>Interview</th><th>Total</th><th>Meet</th></tr></thead>
        <tbody>
        <?php foreach ( $bookings as $booking ) : ?>
            <tr>
                <td><a href="<?php echo esc_url( admin_url( 'admin.php?page=ucu-collegium-booking-bookings&action=view&booking_id=' . (int) $booking['id'] ) ); ?>">#<?php echo (int) $booking['id']; ?></a></td>
                <td><?php echo esc_html( $booking['created_at'] ); ?></td>
                <td><?php echo esc_html( trim( $booking['last_name'] . ' ' . $booking['first_name'] . ' ' . $booking['middle_name'] ) ); ?></td>
                <td><?php echo esc_html( $booking['email'] ); ?></td><td><?php echo esc_html( $booking['phone'] ); ?></td>
                <td><?php echo $booking['slot_date'] ? esc_html( $booking['slot_date'] . ' ' . substr( $booking['start_time'], 0, 5 ) . '-' . substr( $booking['end_time'], 0, 5 ) ) : '—'; ?></td>
                <td><?php echo esc_html( $booking['status'] ); ?></td><td><?php echo esc_html( $booking['auto_score'] ); ?></td><td><?php echo esc_html( $booking['manual_score'] ); ?></td><td><?php echo esc_html( $booking['interview_score'] ); ?></td><td><?php echo esc_html( $booking['total_score'] ); ?></td>
                <td><?php echo $booking['meet_link'] ? '<a href="' . esc_url( $booking['meet_link'] ) . '" target="_blank" rel="noopener">Meet</a>' : '—'; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
