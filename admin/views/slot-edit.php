<?php if ( ! defined( 'ABSPATH' ) ) { exit; } $slot = $slot ?: array( 'id' => 0, 'slot_date' => '', 'start_time' => '', 'end_time' => '', 'status' => 'active', 'booked_count' => 0 ); $locked = ! empty( $slot['id'] ) && ( (int) $slot['booked_count'] > 0 || ( new UCU_Collegium_Slot_Service() )->has_bookings( (int) $slot['id'] ) ); ?>
<div class="wrap">
    <h1><?php echo $slot['id'] ? 'Редагувати слот' : 'Додати слот'; ?></h1>
    <?php if ( $locked ) : ?><div class="notice notice-warning"><p>Дата і час заблоковані, бо на слот вже є заявки.</p></div><?php endif; ?>
    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <input type="hidden" name="action" value="ucu_collegium_slot_save"><input type="hidden" name="slot_id" value="<?php echo (int) $slot['id']; ?>"><?php wp_nonce_field( 'ucu_collegium_slot_save' ); ?>
        <table class="form-table"><tr><th>Дата</th><td><input type="date" name="slot_date" value="<?php echo esc_attr( $slot['slot_date'] ); ?>" <?php disabled( $locked ); ?> required></td></tr><tr><th>Час початку</th><td><input type="time" name="start_time" value="<?php echo esc_attr( substr( $slot['start_time'], 0, 5 ) ); ?>" <?php disabled( $locked ); ?> required></td></tr><tr><th>Час завершення</th><td><input type="time" name="end_time" value="<?php echo esc_attr( substr( $slot['end_time'], 0, 5 ) ); ?>" <?php disabled( $locked ); ?> required></td></tr><tr><th>Status</th><td><select name="status"><?php foreach ( array( 'active', 'inactive', 'full' ) as $status ) : ?><option value="<?php echo esc_attr( $status ); ?>" <?php selected( $slot['status'], $status ); ?>><?php echo esc_html( $status ); ?></option><?php endforeach; ?></select></td></tr></table>
        <?php submit_button( 'Зберегти' ); ?>
    </form>
</div>
