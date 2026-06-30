<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Динамічний рендер поля на основі опису з UCU_Collegium_Form_Fields.
 * Підтримує типи: text, email, phone, textarea, select, radio, checkbox, date, attachment.
 *
 * $field        — масив опису поля (key, label, type, required, options, condition, ...)
 * $wrapper_class — додатковий CSS-клас для обгортки (необов'язково)
 * $hint         — додатковий HTML-підказка перед полем (необов'язково)
 */
$render_field = static function ( array $field, string $wrapper_class = '', string $hint = '' ) {
    $key       = $field['key'];
    $label     = $field['label'];
    $type      = $field['type'];
    $required  = ! empty( $field['required'] );
    $options   = $field['options'] ?? array();
    $condition = $field['condition'] ?? null;

    $type_class = in_array( $type, array( 'radio', 'checkbox', 'select', 'textarea', 'date', 'attachment' ), true ) ? ' ucu-field--' . $type : '';
    if ( 'select' === $type && ! empty( $options ) ) {
        // select-поля з варіантами рендеримо як radio-вибір для кращого UX степера (зберігаємо узгодженість зі стилем кроків)
        $type_class = ' ucu-field--radio';
    }
    $classes = trim( 'ucu-field' . $type_class . ( $wrapper_class ? ' ' . $wrapper_class : '' ) );

    $attrs = 'data-field-key="' . esc_attr( $key ) . '"';
    if ( $condition ) {
        $attrs .= ' data-condition=\'' . wp_json_encode( $condition ) . '\' style="display:none;"';
    }
    if ( $required && in_array( $type, array( 'radio' ), true ) ) {
        $attrs .= ' data-required="1"';
    }
    if ( 'select' === $type && ! empty( $options ) && $required ) {
        $attrs .= ' data-required="1"';
    }

    echo '<div class="' . esc_attr( $classes ) . '" ' . $attrs . '>';

    if ( $hint ) {
        echo $hint; // phpcs:ignore -- trusted static markup
    }

    echo '<label class="ucu-field__label">' . esc_html( $label ) . ( $required ? ' <span class="ucu-req">*</span>' : '' ) . '</label>';

    $disabled_attr = $condition ? ' disabled' : '';

    switch ( $type ) {
        case 'email':
            echo '<input type="email" name="' . esc_attr( $key ) . '" placeholder="' . esc_attr( $label ) . '"' . $disabled_attr . ( $required ? ' required' : '' ) . '>';
            break;

        case 'phone':
            echo '<input type="tel" name="' . esc_attr( $key ) . '" placeholder="+380..." autocomplete="tel"' . $disabled_attr . ( $required ? ' required' : '' ) . '>';
            break;

        case 'textarea':
            echo '<textarea name="' . esc_attr( $key ) . '" rows="4"' . $disabled_attr . ( $required ? ' required' : '' ) . '></textarea>';
            break;

        case 'date':
            $id = 'ucu-date-' . sanitize_html_class( $key );
            echo '<div class="ucu-date-row">';
            echo '<select class="ucu-date-part" data-date-part="day" data-date-target="' . esc_attr( $key ) . '"' . $disabled_attr . '><option value="">День</option>';
            for ( $d = 1; $d <= 31; $d++ ) {
                $v = str_pad( (string) $d, 2, '0', STR_PAD_LEFT );
                echo '<option value="' . esc_attr( $v ) . '">' . esc_html( $v ) . '</option>';
            }
            echo '</select>';
            echo '<select class="ucu-date-part" data-date-part="month" data-date-target="' . esc_attr( $key ) . '"' . $disabled_attr . '><option value="">Місяць</option>';
            $months = array( 'Січень', 'Лютий', 'Березень', 'Квітень', 'Травень', 'Червень', 'Липень', 'Серпень', 'Вересень', 'Жовтень', 'Листопад', 'Грудень' );
            foreach ( $months as $i => $m ) {
                $v = str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT );
                echo '<option value="' . esc_attr( $v ) . '">' . esc_html( $m ) . '</option>';
            }
            echo '</select>';
            echo '<select class="ucu-date-part" data-date-part="year" data-date-target="' . esc_attr( $key ) . '"' . $disabled_attr . '><option value="">Рік</option>';
            for ( $y = (int) date( 'Y' ) - 16; $y >= 1970; $y-- ) {
                echo '<option value="' . esc_attr( (string) $y ) . '">' . esc_html( (string) $y ) . '</option>';
            }
            echo '</select>';
            echo '</div>';
            echo '<input type="hidden" name="' . esc_attr( $key ) . '" data-date-hidden' . ( $required ? ' required' : '' ) . '>';
            break;

        case 'attachment':
            $id          = 'ucu-field-' . sanitize_html_class( $key );
            $max_size_mb = (int) ( $field['max_size_mb'] ?? 5 );
            echo '<div class="ucu-file-wrap">';
            echo '<label class="ucu-file-btn" for="' . esc_attr( $id ) . '">';
            echo '<svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M16 10l-4-4-4 4M12 6v10"/></svg>';
            echo 'Обрати файл</label>';
            echo '<span class="ucu-file-name" id="' . esc_attr( $id ) . '-name">Файл не обрано</span>';
            echo '<input type="file" id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '" accept="image/jpeg,image/png,image/webp" style="display:none;" data-file-input' . $disabled_attr . ( $required ? ' required' : '' ) . '>';
            echo '</div>';
            echo '<span class="ucu-field__hint">JPG, PNG або WebP до ' . esc_html( (string) $max_size_mb ) . ' MB.</span>';
            break;

        case 'select':
            if ( empty( $options ) ) {
                echo '<input type="text" name="' . esc_attr( $key ) . '"' . $disabled_attr . ( $required ? ' required' : '' ) . '>';
                break;
            }
            // Рендеримо select-поля з варіантами як radio-список для UX, узгодженого з рештою степера.
            echo '<div class="ucu-field__choices">';
            foreach ( $options as $value => $opt_label ) {
                echo '<label class="ucu-choice"><input type="radio" name="' . esc_attr( $key ) . '" value="' . esc_attr( (string) $value ) . '"' . $disabled_attr . '> <span>' . esc_html( $opt_label ) . '</span></label>';
            }
            echo '</div>';
            break;

        case 'radio':
            echo '<div class="ucu-field__choices">';
            foreach ( $options as $value => $opt_label ) {
                echo '<label class="ucu-choice"><input type="radio" name="' . esc_attr( $key ) . '" value="' . esc_attr( (string) $value ) . '"' . $disabled_attr . '> <span>' . esc_html( $opt_label ) . '</span></label>';
            }
            echo '</div>';
            break;

        case 'checkbox':
            echo '<div class="ucu-field__choices">';
            foreach ( $options as $value => $opt_label ) {
                echo '<label class="ucu-choice"><input type="checkbox" name="' . esc_attr( $key ) . '[]" value="' . esc_attr( (string) $value ) . '"' . $disabled_attr . '> <span>' . esc_html( $opt_label ) . '</span></label>';
            }
            echo '</div>';
            break;

        case 'text':
        default:
            echo '<input type="text" name="' . esc_attr( $key ) . '"' . $disabled_attr . ( $required ? ' required' : '' ) . '>';
            break;
    }

    echo '<span class="ucu-field__error" data-field-error></span>';
    echo '</div>';
};

/**
 * Повертає масив полів конкретного блоку (за ключем блоку) з class-form-fields.php.
 */
$get_block_fields = static function ( string $block_key ) {
    foreach ( UCU_Collegium_Form_Fields::get_blocks() as $block ) {
        if ( ( $block['key'] ?? '' ) === $block_key ) {
            return $block['fields'] ?? array();
        }
    }
    return array();
};

/**
 * Повертає одне поле за ключем з усіх блоків (для вибіркового рендеру в межах кроку).
 */
$get_field_by_key = static function ( string $field_key ) use ( $get_block_fields ) {
    foreach ( UCU_Collegium_Form_Fields::get_blocks() as $block ) {
        foreach ( $block['fields'] ?? array() as $f ) {
            if ( $f['key'] === $field_key ) {
                return $f;
            }
        }
    }
    return null;
};
?>
<div class="ucu-booking" data-ucu-booking-form>

    <h2 class="ucu-booking__title">Анкета вступника до Колегіуму</h2>

    <!-- Степер -->
    <div class="ucu-stepper">
        <div class="ucu-step active" data-step="1"><div class="ucu-step-circle">1</div><div class="ucu-step-label">Загальні<br>відомості</div></div>
        <div class="ucu-step-line"></div>
        <div class="ucu-step" data-step="2"><div class="ucu-step-circle">2</div><div class="ucu-step-label">Персональні<br>дані</div></div>
        <div class="ucu-step-line"></div>
        <div class="ucu-step" data-step="3"><div class="ucu-step-circle">3</div><div class="ucu-step-label">Соціальний<br>статус</div></div>
        <div class="ucu-step-line"></div>
        <div class="ucu-step" data-step="4"><div class="ucu-step-circle">4</div><div class="ucu-step-label">Стан<br>здоров'я</div></div>
        <div class="ucu-step-line"></div>
        <div class="ucu-step" data-step="5"><div class="ucu-step-circle">5</div><div class="ucu-step-label">Місце<br>проживання</div></div>
        <div class="ucu-step-line"></div>
        <div class="ucu-step" data-step="6"><div class="ucu-step-circle">6</div><div class="ucu-step-label">Інформація<br>про сім'ю</div></div>
        <div class="ucu-step-line"></div>
        <div class="ucu-step" data-step="7"><div class="ucu-step-circle">7</div><div class="ucu-step-label">Участь у формаційній програмі Колегіуму</div></div>
        <div class="ucu-step-line"></div>
        <div class="ucu-step" data-step="8"><div class="ucu-step-circle">8</div><div class="ucu-step-label">Мотиваційна<br>частина</div></div>
        <div class="ucu-step-line"></div>
        <div class="ucu-step" data-step="9"><div class="ucu-step-circle">9</div><div class="ucu-step-label">Згода на обробку даних</div></div>
        <div class="ucu-step-line"></div>
        <div class="ucu-step" data-step="10"><div class="ucu-step-circle">10</div><div class="ucu-step-label">Співбесіда</div></div>
    </div>

    <div class="ucu-booking__notice ucu-booking__notice--success" data-ucu-success hidden></div>
    <div class="ucu-booking__notice ucu-booking__notice--error"   data-ucu-error   hidden></div>

    <form class="ucu-booking__form" enctype="multipart/form-data" novalidate>
        <input type="hidden" name="action"        value="ucu_collegium_submit_booking">
        <input type="hidden" name="nonce"         value="<?php echo esc_attr( wp_create_nonce( 'ucu_collegium_booking_nonce' ) ); ?>">
        <input type="hidden" name="session_token" value="<?php echo esc_attr( $session_token ); ?>">

        <!-- ══ КРОК 1: Загальні відомості (блок: general_info) ════════════ -->
        <section class="ucu-step-panel active" data-panel="1">
            <div class="ucu-booking__grid">
                <?php foreach ( $get_block_fields( 'general_info' ) as $field ) : $render_field( $field ); endforeach; ?>
            </div>
            <div class="ucu-step-nav">
                <div></div>
                <button type="button" class="ucu-btn ucu-btn-next">Далі</button>
            </div>
        </section>

        <!-- ══ КРОК 2: Персональні дані (блок: personal_data) ═════════════ -->
        <section class="ucu-step-panel" data-panel="2" style="display:none;">
            <div class="ucu-booking__grid">

                <input type="hidden" name="social_profile_url" id="social_profile_url_hidden">

                <?php
                foreach ( $get_block_fields( 'personal_data' ) as $field ) :
                    if ( 'social_accounts' === $field['key'] ) :
                        // Соціальні мережі: чекбокс-опції з полями посилань, що з'являються по toggle.
                        $social_field_map = array(
                            'instagram' => 'instagram_url',
                            'whatsapp'  => 'whatsapp_url',
                            'telegram'  => 'telegram_url',
                            'tiktok'    => 'tiktok_url',
                        );
                        $social_placeholders = array(
                            'instagram_url' => 'Посилання на Ваш Instagram',
                            'whatsapp_url'  => 'Номер на який зареєстрований Ваш WhatsApp',
                            'telegram_url'  => 'Посилання на Ваш Telegram',
                            'tiktok_url'    => 'Посилання на Ваш TikTok',
                        );
                        ?>
                        <div class="ucu-field ucu-field--checkbox" data-field-key="social_accounts">
                            <label class="ucu-field__label"><?php echo esc_html( $field['label'] ); ?></label>
                            <div class="ucu-field__choices">
                                <?php foreach ( $field['options'] as $value => $opt_label ) : ?>
                                    <?php if ( isset( $social_field_map[ $value ] ) ) : ?>
                                        <label class="ucu-choice"><input type="checkbox" name="social_accounts[]" value="<?php echo esc_attr( $value ); ?>" data-social-toggle="<?php echo esc_attr( $value ); ?>"> <span><?php echo esc_html( $opt_label ); ?></span></label>
                                        <div class="ucu-social-input" id="ucu-social-<?php echo esc_attr( $value ); ?>" style="display:none;">
                                            <input type="text" name="<?php echo esc_attr( $social_field_map[ $value ] ); ?>" placeholder="<?php echo esc_attr( $social_placeholders[ $social_field_map[ $value ] ] ); ?>">
                                        </div>
                                    <?php else : ?>
                                        <label class="ucu-choice"><input type="checkbox" name="social_accounts[]" value="<?php echo esc_attr( $value ); ?>"> <span><?php echo esc_html( $opt_label ); ?></span></label>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php
                    elseif ( in_array( $field['key'], array( 'instagram_url', 'whatsapp_url', 'telegram_url', 'tiktok_url' ), true ) ) :
                        // Поля-посилання вже відрендерені інлайн вище разом з social_accounts — пропускаємо.
                        continue;
                    else :
                        $render_field( $field );
                    endif;
                endforeach;
                ?>

            </div>
            <div class="ucu-step-nav">
                <button type="button" class="ucu-btn ucu-btn-prev">Попередній</button>
                <button type="button" class="ucu-btn ucu-btn-next">Далі</button>
            </div>
        </section>

        <!-- ══ КРОК 3: Соціальний статус (блок: social_status) ════════════ -->
        <section class="ucu-step-panel" data-panel="3" style="display:none;">
            <div class="ucu-booking__grid">
                <?php
                $special_category_field = $get_field_by_key( 'special_category' );
                if ( $special_category_field ) :
                    ?>
                    <div class="ucu-field ucu-field--checkbox" data-field-key="special_category">
                        <label class="ucu-field__label"><?php echo esc_html( $special_category_field['label'] ); ?></label>
                        <div class="ucu-field__choices">
                            <?php foreach ( $special_category_field['options'] as $value => $opt_label ) : ?>
                                <?php if ( 'other' === $value ) : ?>
                                    <label class="ucu-choice"><input type="checkbox" name="special_category[]" value="other" data-social-toggle="category-other"> <span><?php echo esc_html( $opt_label ); ?></span></label>
                                    <div class="ucu-social-input" id="ucu-social-category-other" style="display:none;">
                                        <input type="text" name="special_category_other" placeholder="Вкажіть категорію">
                                    </div>
                                <?php else : ?>
                                    <label class="ucu-choice"><input type="checkbox" name="special_category[]" value="<?php echo esc_attr( $value ); ?>"> <span><?php echo esc_html( $opt_label ); ?></span></label>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php
                endif;
                ?>
            </div>
            <div class="ucu-step-nav">
                <button type="button" class="ucu-btn ucu-btn-prev">Попередній</button>
                <button type="button" class="ucu-btn ucu-btn-next">Далі</button>
            </div>
        </section>

        <!-- ══ КРОК 4: Стан здоров'я (блок: health) ════════════════════════ -->
        <section class="ucu-step-panel" data-panel="4" style="display:none;">
            <div class="ucu-booking__grid">
                <?php
                foreach ( $get_block_fields( 'health' ) as $field ) :
                    if ( 'disability_reason' === $field['key'] ) :
                        ?>
                        <div class="ucu-field ucu-field--checkbox" data-field-key="disability_reason" data-condition='<?php echo wp_json_encode( $field['condition'] ); ?>' style="display:none;">
                            <label class="ucu-field__label"><?php echo esc_html( $field['label'] ); ?></label>
                            <div class="ucu-field__choices">
                                <?php foreach ( $field['options'] as $value => $opt_label ) : ?>
                                    <label class="ucu-choice"><input type="checkbox" name="disability_reason[]" value="<?php echo esc_attr( $value ); ?>"> <span><?php echo esc_html( $opt_label ); ?></span></label>
                                <?php endforeach; ?>
                                <label class="ucu-choice"><input type="checkbox" name="disability_reason[]" value="Інше" data-social-toggle="disability-other"> <span>Інше</span></label>
                                <div class="ucu-social-input" id="ucu-social-disability-other" style="display:none;">
                                    <input type="text" name="disability_reason_other" placeholder="Інша причина...">
                                </div>
                            </div>
                        </div>
                        <?php
                    else :
                        $render_field( $field );
                    endif;
                endforeach;
                ?>
            </div>
            <div class="ucu-step-nav">
                <button type="button" class="ucu-btn ucu-btn-prev">Попередній</button>
                <button type="button" class="ucu-btn ucu-btn-next">Далі</button>
            </div>
        </section>

        <!-- ══ КРОК 5: Місце проживання (блок: residence) ═════════════════ -->
        <section class="ucu-step-panel" data-panel="5" style="display:none;">
            <div class="ucu-booking__grid">
                <?php foreach ( $get_block_fields( 'residence' ) as $field ) : $render_field( $field ); endforeach; ?>
            </div>
            <div class="ucu-step-nav">
                <button type="button" class="ucu-btn ucu-btn-prev">Попередній</button>
                <button type="button" class="ucu-btn ucu-btn-next">Далі</button>
            </div>
        </section>

        <!-- ══ КРОК 6: Інформація про сім'ю (блок: family_info) ═══════════ -->
        <section class="ucu-step-panel" data-panel="6" style="display:none;">
            <div class="ucu-booking__grid">
                <?php foreach ( $get_block_fields( 'family_info' ) as $field ) : $render_field( $field ); endforeach; ?>
            </div>
            <div class="ucu-step-nav">
                <button type="button" class="ucu-btn ucu-btn-prev">Попередній</button>
                <button type="button" class="ucu-btn ucu-btn-next">Далі</button>
            </div>
        </section>

        <!-- ══ КРОК 7: Участь у формаційній програмі (блок: previous_program_participation) ═ -->
        <section class="ucu-step-panel" data-panel="7" style="display:none;">
            <div class="ucu-booking__grid">
                <?php
                foreach ( $get_block_fields( 'previous_program_participation' ) as $field ) :
                    if ( 'previous_program_years' === $field['key'] ) :
                        // Спеціальний UI: чекбокси-роки + поле "Інше" (бекенд очікує текстове поле, форма надсилає масив — це штатно обробляється sanitize_form_data()).
                        ?>
                        <div class="ucu-field ucu-field--checkbox" data-field-key="previous_program_years" data-condition='<?php echo wp_json_encode( $field['condition'] ); ?>' style="display:none;">
                            <label class="ucu-field__label"><?php echo esc_html( $field['label'] ); ?></label>
                            <div class="ucu-field__choices" style="flex-direction:row;flex-wrap:wrap;gap:8px 24px;">
                                <?php foreach ( array( '2021-2022', '2022-2023', '2023-2024', '2024-2025', '2025-2026' ) as $year_opt ) : ?>
                                    <label class="ucu-choice"><input type="checkbox" name="previous_program_years[]" value="<?php echo esc_attr( $year_opt ); ?>"> <span><?php echo esc_html( $year_opt ); ?></span></label>
                                <?php endforeach; ?>
                                <label class="ucu-choice"><input type="checkbox" name="previous_program_years[]" value="Інше" data-social-toggle="prev-year-other"> <span>Інше</span></label>
                                <div class="ucu-social-input" id="ucu-social-prev-year-other" style="display:none;">
                                    <input type="text" name="previous_program_years_other" placeholder="Вкажіть рік...">
                                </div>
                            </div>
                        </div>
                        <?php
                    elseif ( 'previous_relationship_experience' === $field['key'] ) :
                        ?>
                        <div class="ucu-field ucu-field--radio" data-field-key="previous_relationship_experience" data-condition='<?php echo wp_json_encode( $field['condition'] ); ?>' style="display:none;">
                            <label class="ucu-field__label"><?php echo esc_html( $field['label'] ); ?></label>
                            <div class="ucu-field__choices">
                                <?php foreach ( $field['options'] as $value => $opt_label ) : ?>
                                    <label class="ucu-choice"><input type="radio" name="previous_relationship_experience" value="<?php echo esc_attr( $value ); ?>"> <span><?php echo esc_html( $opt_label ); ?></span></label>
                                <?php endforeach; ?>
                                <label class="ucu-choice"><input type="radio" name="previous_relationship_experience" value="Інше" data-radio-shows="prev-experience-other"> <span>Інше</span></label>
                            </div>
                            <div class="ucu-social-input" id="ucu-radio-prev-experience-other" style="display:none;">
                                <input type="text" name="previous_relationship_experience_other" placeholder="Інший досвід...">
                            </div>
                        </div>
                        <?php
                    else :
                        $render_field( $field );
                    endif;
                endforeach;
                ?>
            </div>
            <div class="ucu-step-nav">
                <button type="button" class="ucu-btn ucu-btn-prev">Попередній</button>
                <button type="button" class="ucu-btn ucu-btn-next">Далі</button>
            </div>
        </section>

        <!-- ══ КРОК 8: Мотиваційна частина (частина блоку: motivation) ════ -->
        <section class="ucu-step-panel" data-panel="8" style="display:none;">
            <div class="ucu-booking__grid">

                <div class="ucu-field ucu-field--textarea" data-field-key="values_events">
                    <label class="ucu-field__label">Опишіть не менше 2 подій, які сформували Ваші цінності і світогляд <span class="ucu-req">*</span></label>
                    <textarea name="values_events" rows="8" required></textarea>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <?php
                $religious_experience_field = $get_field_by_key( 'religious_experience' );
                if ( $religious_experience_field ) :
                    $hint  = '<div class="ucu-field__hint" style="font-size:.875rem;color:#374151;margin-bottom:8px;line-height:1.7;">';
                    $hint .= '<p><strong>Як Ваша поведінка та рішення під час участі у Формаційній програмі будуть узгоджуватися з її правилами та формаційними засадами? Наведіть приклади. У довільній формі опишіть:</strong></p>';
                    $hint .= '<p><strong>Здатність до діалогу та відкритості</strong><br>— Як Ви поводитеся, коли у вашому оточенні виникають різні думки?<br>— Наведіть приклад ситуації, коли Вам довелося шукати порозуміння.</p>';
                    $hint .= '<p><strong>Повага до людської гідності та правил спільноти</strong><br>— Як Ви виявляєте повагу до людей, з якими живете або навчаєтесь?<br>— Наведіть приклад відповідальної поведінки в спільному середовищі.</p>';
                    $hint .= '<p><strong>Усвідомлення ціннісних засад програми</strong><br>— Як християнське бачення людини співпадає з Вашими поглядами?<br>— Чому важливо, щоб вчинки учасника не суперечили місії УКУ?</p>';
                    $hint .= '</div>';

                    $field_for_render          = $religious_experience_field;
                    $field_for_render['label'] = '(Максимальна відповідь 400 слів)';
                    $field_for_render['required'] = true;
                    $render_field( $field_for_render, '', $hint );
                endif;

                $motivation_letter_field = $get_field_by_key( 'motivation_letter' );
                if ( $motivation_letter_field ) :
                    $hint = '<div class="ucu-field__hint" style="font-size:.875rem;color:#374151;margin-bottom:8px;"><strong>Чому Ви хочете брати участь у формаційній програмі "Християнська духовність у постмодерній добі" з проживанням у Колегіумі у 2026–2027 н. р.?</strong></div>';

                    $field_for_render              = $motivation_letter_field;
                    $field_for_render['label']     = 'Напишіть мотиваційний лист довільної форми (рекомендований обсяг — 300–500 слів), розкривши кожен із даних пунктів: Ваше розуміння формату програми та її формаційних засад; Вашу особисту мотивацію та цілі участі; Вашу готовність жити в спільноті з визначеними правилами. Лист має бути особистим і рефлексивним.';
                    $field_for_render['required']  = true;
                    $render_field( $field_for_render, '', $hint );
                endif;

                $talents_field = $get_field_by_key( 'talents_hobbies' );
                if ( $talents_field ) :
                    $field_for_render             = $talents_field;
                    $field_for_render['required'] = true;
                    $render_field( $field_for_render );
                endif;
                ?>

            </div>
            <div class="ucu-step-nav">
                <button type="button" class="ucu-btn ucu-btn-prev">Попередній</button>
                <button type="button" class="ucu-btn ucu-btn-next">Далі</button>
            </div>
        </section>

        <!-- ══ КРОК 9: Згода на обробку даних (частина блоку: motivation + хардкод-поля) ═ -->
        <section class="ucu-step-panel" data-panel="9" style="display:none;">
            <div class="ucu-booking__grid">

                <div class="ucu-field ucu-field--checkbox" data-field-key="how_did_you_know">
                    <label class="ucu-field__label">Звідки Ви вперше дізнався про Колегіум?</label>
                    <div class="ucu-field__choices">
                        <label class="ucu-choice"><input type="checkbox" name="how_did_you_know[]" value="Від знайомих / друзів / родичів"> <span>Від знайомих / друзів / родичів</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="how_did_you_know[]" value="На екскурсії кампусом УКУ"> <span>На екскурсії кампусом УКУ</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="how_did_you_know[]" value="На подіях в УКУ (День відкритих дверей, Відчуй себе студентом)"> <span>На подіях в УКУ (День відкритих дверей, Відчуй себе студентом)</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="how_did_you_know[]" value="З сайту УКУ"> <span>З сайту УКУ</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="how_did_you_know[]" value="З сайту Колегіуму"> <span>З сайту Колегіуму</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="how_did_you_know[]" value="З соцмереж УКУ / Центру абітурієнта"> <span>З соцмереж УКУ / Центру абітурієнта</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="how_did_you_know[]" value="З соцмереж Колегіуму"> <span>З соцмереж Колегіуму</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="how_did_you_know[]" value="Інше" data-social-toggle="how-know-other"> <span>Інше</span></label>
                        <div class="ucu-social-input" id="ucu-social-how-know-other" style="display:none;">
                            <input type="text" name="how_did_you_know_other" placeholder="Дізнався(-лася) ...">
                        </div>
                    </div>
                </div>

                <div class="ucu-field ucu-field--radio" data-field-key="waiting_list" data-required="1">
                    <label class="ucu-field__label">Якщо ви не пройдете відбір до формаційної програми Колегіуму УКУ, чи бажаєте долучитися до списку очікування? <span class="ucu-req">*</span></label>
                    <div class="ucu-field__choices">
                        <label class="ucu-choice"><input type="radio" name="waiting_list" value="yes"> <span>Так, хочу бути в списку очікування — повідомте мене про наступні можливості</span></label>
                        <label class="ucu-choice"><input type="radio" name="waiting_list" value="no"> <span>Ні, дякую</span></label>
                    </div>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <?php
                $mandatory_events_field = $get_field_by_key( 'mandatory_events_acceptance' );
                if ( $mandatory_events_field ) :
                    $render_field( $mandatory_events_field );
                endif;
                ?>

                <div class="ucu-field ucu-field--radio" data-field-key="public_behavior_awareness" data-required="1">
                    <label class="ucu-field__label">Чи Ви розумієте, що публічна поведінка учасника Формаційної програми може впливати на спільнотний характер і репутацію формаційного середовища? <span class="ucu-req">*</span></label>
                    <div class="ucu-field__choices" style="flex-direction:row;gap:24px;">
                        <label class="ucu-choice"><input type="radio" name="public_behavior_awareness" value="yes"> <span>так</span></label>
                        <label class="ucu-choice"><input type="radio" name="public_behavior_awareness" value="no"> <span>ні (заява не буде прийнята)</span></label>
                    </div>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field ucu-field--radio" data-field-key="activism_restraint" data-required="1">
                    <label class="ucu-field__label">Чи готові Ви утримуватися, на час участі у Формаційній програмі, від публічної діяльності або активізму, що прямо суперечить формаційним засадам програми? <span class="ucu-req">*</span></label>
                    <div class="ucu-field__choices" style="flex-direction:row;gap:24px;">
                        <label class="ucu-choice"><input type="radio" name="activism_restraint" value="yes"> <span>так</span></label>
                        <label class="ucu-choice"><input type="radio" name="activism_restraint" value="no"> <span>ні (заява не буде прийнята)</span></label>
                    </div>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <?php
                $housing_rules_field = $get_field_by_key( 'housing_rules_consent' );
                if ( $housing_rules_field ) :
                    $render_field( $housing_rules_field );
                endif;

                $personal_data_field = $get_field_by_key( 'personal_data_consent' );
                if ( $personal_data_field ) :
                    $render_field( $personal_data_field );
                endif;
                ?>

            </div>
            <div class="ucu-step-nav">
                <button type="button" class="ucu-btn ucu-btn-prev">Попередній</button>
                <button type="button" class="ucu-btn ucu-btn-next">Далі</button>
            </div>
        </section>

        <!-- ══ КРОК 10: Співбесіда ═════════════════════════════════════════ -->
        <section class="ucu-step-panel" data-panel="10" style="display:none;">
            <div class="ucu-booking__grid">
                <div class="ucu-field" data-field-key="slot_id" style="grid-column:1/-1;">
                    <label class="ucu-field__label">Слот співбесіди <span class="ucu-req">*</span></label>
                    <select name="slot_id" required data-ucu-slots>
                        <option value="">Завантаження доступних слотів...</option>
                    </select>
                    <span class="ucu-field__hint" data-ucu-hold-message></span>
                    <span class="ucu-field__error" data-field-error></span>
                </div>
            </div>
            <div class="ucu-step-nav">
                <button type="button" class="ucu-btn ucu-btn-prev">Попередній</button>
                <button type="submit" class="ucu-btn ucu-btn-next" data-ucu-submit>
                    <span class="ucu-btn-text">Надіслати заявку</span>
                    <span class="ucu-btn-loader" style="display:none;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="31.4 31.4"><animateTransform attributeName="transform" type="rotate" from="0 12 12" to="360 12 12" dur="0.8s" repeatCount="indefinite"/></circle></svg>
                        Надсилання...
                    </span>
                </button>
            </div>
        </section>

    </form>
</div>
