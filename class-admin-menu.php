<?php if ( ! defined( 'ABSPATH' ) ) { exit; } $slot_service = new UCU_Collegium_Slot_Service(); ?>
<div class="wrap">
    <h1>Слоти співбесід</h1>
    <p><a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=ucu-collegium-booking-slots&action=edit' ) ); ?>">Додати слот</a></p>
    <table class="widefat striped">
        <thead><tr><th>ID</th><th>Дата</th><th>Початок</th><th>Завершення</th><th>Ліміт</th><th>Booked</th><th>Active holds</th><th>Available</th><th>Status</th><th></th></tr></thead>
        <tbody>
        <?php foreach ( $slots as $slot ) : ?>
            <tr>
                <td><?php echo (int) $slot['id']; ?></td><td><?php echo esc_html( $slot['slot_date'] ); ?></td><td><?php echo esc_html( substr( $slot['start_time'], 0, 5 ) ); ?></td><td><?php echo esc_html( substr( $slot['end_time'], 0, 5 ) ); ?></td><td><?php echo (int) $slot['capacity']; ?></td><td><?php echo (int) $slot['booked_count']; ?></td><td><?php echo (int) $slot_service->get_active_holds_count( (int) $slot['id'] ); ?></td><td><?php echo (int) $slot_service->get_available_capacity( (int) $slot['id'] ); ?></td><td><?php echo esc_html( $slot['status'] ); ?></td>
                <td><a href="<?php echo esc_url( admin_url( 'admin.php?page=ucu-collegium-booking-slots&action=edit&slot_id=' . (int) $slot['id'] ) ); ?>">Редагувати</a> | <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=ucu_collegium_slot_delete&slot_id=' . (int) $slot['id'] ), 'ucu_collegium_slot_delete' ) ); ?>">Видалити</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
