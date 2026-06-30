<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class UCU_Collegium_Booking_Service {
    public function submit( array $post, array $files ): array {
        $session_token = sanitize_text_field( wp_unslash( $post['session_token'] ?? '' ) );
        $slot_id       = absint( $post['slot_id'] ?? 0 );
        $form_data     = UCU_Collegium_Form_Validator::sanitize_form_data( $post );
        $errors        = UCU_Collegium_Form_Validator::validate( $form_data );

        if ( '' === $session_token ) {
            $errors['session_token'] = 'Сесія форми недійсна. Оновіть сторінку.';
        }

        $blocking_messages = UCU_Collegium_Blocking_Rules::evaluate( $form_data );
        if ( empty( $blocking_messages ) && ! $slot_id ) {
            $errors['slot_id'] = 'Оберіть слот співбесіди.';
        }

        if ( ! empty( $errors ) ) {
            return array( 'ok' => false, 'message' => 'Перевірте заповнення форми.', 'errors' => $errors );
        }

        $photo_id = $this->handle_photo_upload( $files );
        if ( is_wp_error( $photo_id ) ) {
            return array( 'ok' => false, 'message' => $photo_id->get_error_message(), 'errors' => array( 'photo' => $photo_id->get_error_message() ) );
        }

        $scoring    = new UCU_Collegium_Scoring_Service();
        $score_data = $scoring->calculate_auto_score( $form_data );

        if ( ! empty( $blocking_messages ) ) {
            $booking_id = $this->create_booking(
                array(
                    'slot_id'             => null,
                    'form_data'           => $form_data,
                    'photo_attachment_id' => $photo_id,
                    'auto_score'          => $score_data['score'],
                    'manual_score'        => 0,
                    'interview_score'     => 0,
                    'total_score'         => $score_data['score'],
                    'status'              => 'blocked_by_answer',
                    'error_message'       => implode( ' ', $blocking_messages ),
                    'is_current'          => 0,
                )
            );
            if ( $slot_id ) {
                ( new UCU_Collegium_Hold_Service() )->release_hold( $slot_id, $session_token );
            }
            return array( 'ok' => true, 'blocked' => true, 'booking_id' => $booking_id, 'message' => UCU_Collegium_Settings::get( 'blocked_message' ) );
        }

        $slot_service = new UCU_Collegium_Slot_Service();
        $hold_service = new UCU_Collegium_Hold_Service();

        if ( ! $hold_service->has_valid_hold( $slot_id, $session_token ) && $slot_service->get_available_capacity( $slot_id ) <= 0 ) {
            return array( 'ok' => false, 'message' => UCU_Collegium_Settings::get( 'slot_unavailable_message' ), 'errors' => array( 'slot_id' => UCU_Collegium_Settings::get( 'slot_unavailable_message' ) ) );
        }

        if ( ! $slot_service->atomic_book( $slot_id ) ) {
            return array( 'ok' => false, 'message' => UCU_Collegium_Settings::get( 'slot_unavailable_message' ), 'errors' => array( 'slot_id' => UCU_Collegium_Settings::get( 'slot_unavailable_message' ) ) );
        }

        $this->replace_previous_current( $form_data['email'] );
        $booking_id = $this->create_booking(
            array(
                'slot_id'             => $slot_id,
                'form_data'           => $form_data,
                'photo_attachment_id' => $photo_id,
                'auto_score'          => $score_data['score'],
                'manual_score'        => 0,
                'interview_score'     => 0,
                'total_score'         => $score_data['score'],
                'status'              => 'pending_calendar',
                'error_message'       => '',
                'is_current'          => 1,
            )
        );
        $hold_service->release_hold( $slot_id, $session_token );

        $this->run_integrations( $booking_id );

        return array( 'ok' => true, 'booking_id' => $booking_id, 'message' => UCU_Collegium_Settings::get( 'success_message' ) );
    }

    public function create_booking( array $data ): int {
        global $wpdb;
        $form = $data['form_data'];
        $wpdb->insert(
            UCU_Collegium_Activator::bookings_table(),
            array(
                'slot_id'             => $data['slot_id'],
                'email'               => sanitize_email( $form['email'] ?? '' ),
                'first_name'          => sanitize_text_field( $form['first_name'] ?? '' ),
                'last_name'           => sanitize_text_field( $form['last_name'] ?? '' ),
                'middle_name'         => sanitize_text_field( $form['middle_name'] ?? '' ),
                'phone'               => sanitize_text_field( $form['phone'] ?? '' ),
                'form_data'           => wp_json_encode( $form, JSON_UNESCAPED_UNICODE ),
                'photo_attachment_id' => $data['photo_attachment_id'] ?: null,
                'auto_score'          => (float) $data['auto_score'],
                'manual_score'        => (float) $data['manual_score'],
                'interview_score'     => (float) $data['interview_score'],
                'total_score'         => (float) $data['total_score'],
                'status'              => sanitize_key( $data['status'] ),
                'error_message'       => sanitize_textarea_field( $data['error_message'] ?? '' ),
                'is_current'          => (int) $data['is_current'],
                'created_at'          => current_time( 'mysql' ),
            )
        );
        return (int) $wpdb->insert_id;
    }

    public function get_booking( int $booking_id ): ?array {
        global $wpdb;
        $booking = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . UCU_Collegium_Activator::bookings_table() . ' WHERE id = %d', $booking_id ), ARRAY_A );
        if ( ! $booking ) {
            return null;
        }
        $booking['form_data_array'] = json_decode( $booking['form_data'], true ) ?: array();
        return $booking;
    }

    public function update_status( int $booking_id, string $status, string $error_message = '' ): void {
        global $wpdb;
        $wpdb->update(
            UCU_Collegium_Activator::bookings_table(),
            array( 'status' => sanitize_key( $status ), 'error_message' => $error_message, 'updated_at' => current_time( 'mysql' ) ),
            array( 'id' => $booking_id )
        );
    }

    public function update_scores( int $booking_id, float $manual_score, float $interview_score ): bool {
        global $wpdb;
        $booking = $this->get_booking( $booking_id );
        if ( ! $booking ) {
            return false;
        }
        $total = (float) $booking['auto_score'] + $manual_score + $interview_score;
        return false !== $wpdb->update(
            UCU_Collegium_Activator::bookings_table(),
            array( 'manual_score' => $manual_score, 'interview_score' => $interview_score, 'total_score' => $total, 'updated_at' => current_time( 'mysql' ) ),
            array( 'id' => $booking_id )
        );
    }

    public function move_to_slot( int $booking_id, int $new_slot_id ): bool {
        global $wpdb;
        $booking = $this->get_booking( $booking_id );
        if ( ! $booking ) {
            return false;
        }
        $slot_service = new UCU_Collegium_Slot_Service();
        if ( ! $slot_service->atomic_book( $new_slot_id ) ) {
            return false;
        }
        if ( ! empty( $booking['slot_id'] ) ) {
            $slot_service->decrement_booked_count( (int) $booking['slot_id'] );
        }
        $updated = false !== $wpdb->update(
            UCU_Collegium_Activator::bookings_table(),
            array( 'slot_id' => $new_slot_id, 'status' => 'pending_calendar_update', 'error_message' => 'Заявку перенесено. Оновіть Google Calendar/Meet вручну або через Retry Calendar.', 'updated_at' => current_time( 'mysql' ) ),
            array( 'id' => $booking_id )
        );
        if ( ! $updated ) {
            $slot_service->decrement_booked_count( $new_slot_id );
        }
        return $updated;
    }

    public function cancel( int $booking_id ): bool {
        global $wpdb;
        $booking = $this->get_booking( $booking_id );
        if ( ! $booking ) {
            return false;
        }
        if ( ! empty( $booking['slot_id'] ) && ! in_array( $booking['status'], array( 'cancelled', 'replaced', 'blocked_by_answer' ), true ) ) {
            ( new UCU_Collegium_Slot_Service() )->decrement_booked_count( (int) $booking['slot_id'] );
        }
        return false !== $wpdb->update( UCU_Collegium_Activator::bookings_table(), array( 'status' => 'cancelled', 'is_current' => 0, 'updated_at' => current_time( 'mysql' ) ), array( 'id' => $booking_id ) );
    }

    public function run_integrations( int $booking_id ): void {
        $calendar = new UCU_Collegium_Google_Calendar_Service();
        $result   = $calendar->create_event_for_booking( $booking_id );
        if ( empty( $result['calendar_event_id'] ) ) {
            $this->update_status( $booking_id, 'failed_calendar', $result['error'] ?? 'Google Calendar не створено.' );
            return;
        }

        global $wpdb;
        $wpdb->update(
            UCU_Collegium_Activator::bookings_table(),
            array( 'calendar_event_id' => $result['calendar_event_id'], 'meet_link' => $result['meet_link'], 'status' => 'calendar_created', 'error_message' => '', 'updated_at' => current_time( 'mysql' ) ),
            array( 'id' => $booking_id )
        );

        $sheet_row_id = ( new UCU_Collegium_Google_Sheets_Service() )->sync_booking( $booking_id );
        if ( null !== $sheet_row_id ) {
            $wpdb->update( UCU_Collegium_Activator::bookings_table(), array( 'sheet_row_id' => $sheet_row_id, 'updated_at' => current_time( 'mysql' ) ), array( 'id' => $booking_id ) );
        }

        $mailer = new UCU_Collegium_Mailer_Service();
        if ( ! $mailer->send_user_confirmation( $booking_id ) ) {
            $this->update_status( $booking_id, 'failed_email', 'Не вдалося надіслати лист вступнику.' );
            return;
        }
        $mailer->send_admin_notification( $booking_id );
        $booking_after_sync = $this->get_booking( $booking_id );
        $this->update_status( $booking_id, 'email_sent', $booking_after_sync['error_message'] ?? '' );
    }

    private function replace_previous_current( string $email ): void {
        global $wpdb;
        $previous = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT id, slot_id, status FROM ' . UCU_Collegium_Activator::bookings_table() . ' WHERE email = %s AND is_current = 1',
                sanitize_email( $email )
            ),
            ARRAY_A
        );

        $slot_service = new UCU_Collegium_Slot_Service();
        foreach ( $previous as $booking ) {
            if ( ! empty( $booking['slot_id'] ) && ! in_array( $booking['status'], array( 'cancelled', 'replaced', 'blocked_by_answer' ), true ) ) {
                $slot_service->decrement_booked_count( (int) $booking['slot_id'] );
            }
        }

        $wpdb->update(
            UCU_Collegium_Activator::bookings_table(),
            array( 'status' => 'replaced', 'is_current' => 0, 'updated_at' => current_time( 'mysql' ) ),
            array( 'email' => sanitize_email( $email ), 'is_current' => 1 )
        );
    }

    private function handle_photo_upload( array $files ) {
        $attachment_fields = array_filter(
            UCU_Collegium_Form_Fields::get_fields(),
            static function ( $field ) {
                return 'attachment' === $field['type'];
            }
        );

        $field = reset( $attachment_fields );
        $key   = $field['key'] ?? 'photo';
        $upload_key = $key;
        $file       = $this->get_uploaded_file_from_request( $files, $key, $upload_key );

        if ( empty( $file ) || empty( $file['name'] ) ) {
            return new WP_Error( 'photo_required', 'Фото є обов’язковим.' );
        }
        if ( isset( $file['error'] ) && UPLOAD_ERR_OK !== (int) $file['error'] ) {
            return new WP_Error( 'photo_upload_error', $this->upload_error_message( (int) $file['error'] ) );
        }
        if ( (int) $file['size'] > 5 * MB_IN_BYTES ) {
            return new WP_Error( 'photo_size', 'Максимальний розмір фото — 5 MB.' );
        }
        $allowed = array( 'jpg|jpeg|jpe' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp' );
        $check   = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], $allowed );
        if ( empty( $check['type'] ) ) {
            return new WP_Error( 'photo_type', 'Дозволені формати фото: jpg, jpeg, png, webp.' );
        }
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $attachment_id = media_handle_upload( $upload_key, 0 );
        return is_wp_error( $attachment_id ) ? $attachment_id : (int) $attachment_id;
    }

    private function get_uploaded_file_from_request( array $files, string $key, string &$upload_key ): ?array {
        if ( isset( $files[ $key ] ) && is_array( $files[ $key ] ) ) {
            $upload_key = $key;
            return $files[ $key ];
        }

        if ( 'photo' !== $key && isset( $files['photo'] ) && is_array( $files['photo'] ) ) {
            $upload_key = 'photo';
            return $files['photo'];
        }

        foreach ( $files as $candidate_key => $file ) {
            if ( is_array( $file ) && ! empty( $file['name'] ) && isset( $file['tmp_name'] ) ) {
                $upload_key = (string) $candidate_key;
                return $file;
            }
        }

        return null;
    }

    private function upload_error_message( int $error_code ): string {
        switch ( $error_code ) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'Файл фото завеликий для налаштувань сервера.';
            case UPLOAD_ERR_PARTIAL:
                return 'Фото було завантажено лише частково. Спробуйте ще раз.';
            case UPLOAD_ERR_NO_FILE:
                return 'Фото є обов’язковим.';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'На сервері не налаштована тимчасова папка для upload.';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Сервер не зміг записати файл фото.';
            case UPLOAD_ERR_EXTENSION:
                return 'Завантаження фото зупинено PHP-розширенням.';
            default:
                return 'Не вдалося завантажити фото. Код помилки: ' . $error_code;
        }
    }
}
