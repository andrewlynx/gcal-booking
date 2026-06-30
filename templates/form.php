<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
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

        <!-- ══ КРОК 1: Загальні відомості ════════════════════════════════ -->
        <section class="ucu-step-panel active" data-panel="1">
            <div class="ucu-booking__grid">

                <div class="ucu-field" data-field-key="email">
                    <label class="ucu-field__label">E-mail <span class="ucu-req">*</span></label>
                    <input type="email" name="email" placeholder="E-mail" required>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field ucu-field--radio" data-field-key="bac" data-required="1">
                    <label class="ucu-field__label">Оберіть ступінь станом на вересень 26/27 н.р. <span class="ucu-req">*</span></label>
                    <div class="ucu-field__choices" style="flex-direction:row;gap:24px;">
                        <label class="ucu-choice"><input type="radio" name="degree" value="bachelor"> <span>Бакалавр</span></label>
                        <label class="ucu-choice"><input type="radio" name="degree" value="master"> <span>Магістр</span></label>
                    </div>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field" data-field-key="bachelor_program" data-condition='{"field":"degree","value":"bachelor"}' style="display:none;">
                    <label class="ucu-field__label">Спеціальність бакалаврату станом на вересень 26/27 <span class="ucu-req">*</span></label>
                    <select name="bachelor_program" disabled>
                        <option value="">— оберіть —</option>
                        <option value="theology">Богослов'я</option>
                        <option value="history">Історія</option>
                        <option value="philology">Філологія</option>
                        <option value="cultural_studies">Культурологія</option>
                        <option value="political_science_epe">Політичні науки «Етика-Політика-Економіка»</option>
                        <option value="sociology">Соціологія</option>
                        <option value="social_work">Соціальна робота</option>
                        <option value="physical_therapy">Фізична терапія</option>
                        <option value="psychology">Психологія</option>
                        <option value="law">Право</option>
                        <option value="it_analytics">ІТ та аналітика рішень</option>
                        <option value="computer_science">Комп'ютерні науки</option>
                        <option value="robotics">Робототехніка</option>
                    </select>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field" data-field-key="bachelor_year" data-condition='{"field":"degree","value":"bachelor"}' style="display:none;">
                    <label class="ucu-field__label">Курс станом на вересень 26/27 <span class="ucu-req">*</span></label>
                    <select name="bachelor_year" disabled>
                        <option value="">— оберіть —</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                    </select>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field" data-field-key="master_program" data-condition='{"field":"degree","value":"master"}' style="display:none;">
                    <label class="ucu-field__label">Спеціальність магістратури станом на вересень 26/27 <span class="ucu-req">*</span></label>
                    <select name="master_program" disabled>
                        <option value="">— оберіть —</option>
                        <option value="theology">Богослов'я</option>
                        <option value="history">Історія</option>
                        <option value="cultural_heritage">Культурна спадщина</option>
                        <option value="ergotherapy_physiotherapy">Ерготерапія та фізіотерапія</option>
                        <option value="clinical_psychology_cbt">Клінічна психологія (когнітивно-поведінкова терапія)</option>
                        <option value="clinical_psychology_psychodynamic">Клінічна психологія з основами психодинамічної терапії</option>
                        <option value="journalism">Журналістика</option>
                        <option value="media_communications">Медіакомунікації</option>
                        <option value="human_rights">Права людини</option>
                        <option value="data_science">Науки про дані</option>
                    </select>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field" data-field-key="master_year" data-condition='{"field":"degree","value":"master"}' style="display:none;">
                    <label class="ucu-field__label">Курс станом на вересень 26/27 <span class="ucu-req">*</span></label>
                    <select name="master_year" disabled>
                        <option value="">— оберіть —</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                    </select>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

            </div>
            <div class="ucu-step-nav">
                <div></div>
                <button type="button" class="ucu-btn ucu-btn-next">Далі</button>
            </div>
        </section>

        <!-- ══ КРОК 2: Персональні дані ══════════════════════════════════ -->
        <section class="ucu-step-panel" data-panel="2" style="display:none;">
            <div class="ucu-booking__grid">

                <div class="ucu-field" data-field-key="last_name">
                    <label class="ucu-field__label">Прізвище <span class="ucu-req">*</span></label>
                    <input type="text" name="last_name" placeholder="...." required>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field" data-field-key="first_name">
                    <label class="ucu-field__label">Ім'я <span class="ucu-req">*</span></label>
                    <input type="text" name="first_name" placeholder="..." required>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field" data-field-key="middle_name">
                    <label class="ucu-field__label">Побатькові <span class="ucu-req">*</span></label>
                    <input type="text" name="middle_name" placeholder="..." required>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field ucu-field--radio" data-field-key="gender" data-required="1">
                    <label class="ucu-field__label">Стать <span class="ucu-req">*</span></label>
                    <div class="ucu-field__choices" style="flex-direction:row;gap:24px;">
                        <label class="ucu-choice"><input type="radio" name="gender" value="male"> <span>Чоловік</span></label>
                        <label class="ucu-choice"><input type="radio" name="gender" value="female"> <span>Жінка</span></label>
                    </div>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field ucu-field--date" data-field-key="birth_date">
                    <label class="ucu-field__label">Дата народження <span class="ucu-req">*</span></label>
                    <div class="ucu-date-row">
                        <select class="ucu-date-part" data-date-part="day" data-date-target="birth_date">
                            <option value="">День</option>
                            <?php for ($d=1;$d<=31;$d++) echo '<option value="'.str_pad($d,2,'0',STR_PAD_LEFT).'">'.str_pad($d,2,'0',STR_PAD_LEFT).'</option>'; ?>
                        </select>
                        <select class="ucu-date-part" data-date-part="month" data-date-target="birth_date">
                            <option value="">Місяць</option>
                            <?php $months=['Січень','Лютий','Березень','Квітень','Травень','Червень','Липень','Серпень','Вересень','Жовтень','Листопад','Грудень']; foreach($months as $i=>$m) echo '<option value="'.str_pad($i+1,2,'0',STR_PAD_LEFT).'">'.$m.'</option>'; ?>
                        </select>
                        <select class="ucu-date-part" data-date-part="year" data-date-target="birth_date">
                            <option value="">Рік</option>
                            <?php for ($y=date('Y')-16;$y>=1970;$y--) echo '<option value="'.$y.'">'.$y.'</option>'; ?>
                        </select>
                    </div>
                    <input type="hidden" name="birth_date" data-date-hidden required>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field" data-field-key="phone">
                    <label class="ucu-field__label">Ваш контактний мобільний номер <span class="ucu-req">*</span></label>
                    <input type="tel" name="phone" placeholder="+380..." required>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field ucu-field--attachment" data-field-key="photo">
                    <label class="ucu-field__label">Додайте Ваше фото, зроблене не раніше, як за 6 місяців до заповнення анкети. (розмір файла до 5 МB) <span class="ucu-req">*</span></label>
                    <div class="ucu-file-wrap">
                        <label class="ucu-file-btn" for="ucu-field-photo">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M16 10l-4-4-4 4M12 6v10"/></svg>
                            Обрати файл
                        </label>
                        <span class="ucu-file-name" id="ucu-field-photo-name">Файл не обрано</span>
                        <input type="file" id="ucu-field-photo" name="photo" accept="image/jpeg,image/png,image/webp" style="display:none;" data-file-input>
                    </div>
                    <span class="ucu-field__hint">JPG, PNG або WebP до 5 MB.</span>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <!-- Соцмережі: чекбокс + поле посилання -->
                <input type="hidden" name="social_profile_url" id="social_profile_url_hidden">
                <div class="ucu-field ucu-field--checkbox" data-field-key="social_media">
                    <label class="ucu-field__label">Чи є у Вас акаунт у нижчезазначених соцмережах? Якщо так, вкажіть лінк на акаунт.</label>
                    <div class="ucu-field__choices">
                        <label class="ucu-choice"><input type="checkbox" name="social_media[]" value="Instagram" data-social-toggle="instagram"> <span>Instagram</span></label>
                        <div class="ucu-social-input" id="ucu-social-instagram" style="display:none;">
                            <input type="text" name="instagram_url" placeholder="Посилання на Ваш Instagram">
                        </div>
                        <label class="ucu-choice"><input type="checkbox" name="social_media[]" value="WhatsApp" data-social-toggle="whatsapp"> <span>WhatsApp</span></label>
                        <div class="ucu-social-input" id="ucu-social-whatsapp" style="display:none;">
                            <input type="text" name="whatsapp" placeholder="Номер на який зареєстрований Ваш WhatsApp">
                        </div>
                        <label class="ucu-choice"><input type="checkbox" name="social_media[]" value="Telegram" data-social-toggle="telegram"> <span>Telegram</span></label>
                        <div class="ucu-social-input" id="ucu-social-telegram" style="display:none;">
                            <input type="text" name="telegram" placeholder="Посилання на Ваш Telegram">
                        </div>
                        <label class="ucu-choice"><input type="checkbox" name="social_media[]" value="TikTok" data-social-toggle="tiktok"> <span>TikTok</span></label>
                        <div class="ucu-social-input" id="ucu-social-tiktok" style="display:none;">
                            <input type="text" name="tiktok_url" placeholder="Посилання на Ваш TikTok">
                        </div>
                        <label class="ucu-choice"><input type="checkbox" name="social_media[]" value="Немає в жодному із зазначених"> <span>Немає в жодному із зазначених</span></label>
                    </div>
                </div>

            </div>
            <div class="ucu-step-nav">
                <button type="button" class="ucu-btn ucu-btn-prev">Попередній</button>
                <button type="button" class="ucu-btn ucu-btn-next">Далі</button>
            </div>
        </section>

        <!-- ══ КРОК 3: Соціальний статус ═════════════════════════════════ -->
        <section class="ucu-step-panel" data-panel="3" style="display:none;">
            <div class="ucu-booking__grid">

                <div class="ucu-field ucu-field--checkbox" data-field-key="special_category">
                    <label class="ucu-field__label">Чи Ви належите до однієї з цих категорій:</label>
                    <div class="ucu-field__choices">
                        <label class="ucu-choice"><input type="checkbox" name="special_category[]" value="orphan"> <span>Сирота</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="special_category[]" value="half_orphan"> <span>Напівсирота</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="special_category[]" value="child_of_deceased_defender"> <span>Дитина учасника чи ветерана бойових дій</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="special_category[]" value="combatant_or_veteran"> <span>Учасник чи ветеран бойових дій</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="special_category[]" value="idp"> <span>Внутрішньо переміщена особа</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="special_category[]" value="large_family_child"> <span>Дитина з багатодітної сім'ї</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="special_category[]" value="none"> <span>Не належу до жодної</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="special_category[]" value="Інше" data-social-toggle="category-other"> <span>Інше</span></label>
                        <div class="ucu-social-input" id="ucu-social-category-other" style="display:none;">
                            <input type="text" name="special_category_other" placeholder="Вкажіть категорію">
                        </div>
                    </div>
                </div>

            </div>
            <div class="ucu-step-nav">
                <button type="button" class="ucu-btn ucu-btn-prev">Попередній</button>
                <button type="button" class="ucu-btn ucu-btn-next">Далі</button>
            </div>
        </section>

        <!-- ══ КРОК 4: Стан здоров'я ══════════════════════════════════════ -->
        <section class="ucu-step-panel" data-panel="4" style="display:none;">
            <div class="ucu-booking__grid">

                <div class="ucu-field ucu-field--radio" data-field-key="health_status" data-required="1">
                    <label class="ucu-field__label">Ви є особою, яка: <span class="ucu-req">*</span></label>
                    <div class="ucu-field__choices">
                        <label class="ucu-choice"><input type="radio" name="health_status" value="disability"> <span>має інвалідність</span></label>
                        <label class="ucu-choice"><input type="radio" name="health_status" value="physical_health_difficulties"> <span>має труднощі з фізичним здоров'ям</span></label>
                        <label class="ucu-choice"><input type="radio" name="health_status" value="mental_health_difficulties"> <span>має труднощі з психічним здоров'ям</span></label>
                        <label class="ucu-choice"><input type="radio" name="health_status" value="none"> <span>жодне з переліченого</span></label>
                    </div>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field" data-field-key="disability_group" data-condition='{"field":"health_status","not":"none"}' style="display:none;">
                    <label class="ucu-field__label">Вкажіть групу інвалідності</label>
                    <select name="disability_group" disabled>
                        <option value="">— оберіть —</option>
                        <option value="first">Перша</option>
                        <option value="second">Друга</option>
                        <option value="third">Третя</option>
                        <option value="childhood_disability">Інвалідність з дитинства</option>
                        <option value="no_group">Немає інвалідності</option>
                    </select>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field ucu-field--checkbox" data-field-key="disability_reason" data-condition='{"field":"health_status","not":"none"}' style="display:none;">
                    <label class="ucu-field__label">Яка причина інвалідності чи реєстрації Вас, як особи з особливими освітніми потребами пов'язаними з фізичним чи психічним здоров'ям?</label>
                    <div class="ucu-field__choices">
                        <label class="ucu-choice"><input type="checkbox" name="disability_reason[]" value="vision_impairment"> <span>Порушення зору</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="disability_reason[]" value="hearing_impairment"> <span>Порушення слуху</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="disability_reason[]" value="musculoskeletal_impairment"> <span>Порушення опорно-рухового апарата</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="disability_reason[]" value="physical_development_impairment"> <span>Порушення фізичного розвитку</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="disability_reason[]" value="serious_medical_condition"> <span>Важке медичне захворювання</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="disability_reason[]" value="mental_health_impairment"> <span>Порушення у сфері психічного здоров'я</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="disability_reason[]" value="Інше" data-social-toggle="disability-other"> <span>Інше</span></label>
                        <div class="ucu-social-input" id="ucu-social-disability-other" style="display:none;">
                            <input type="text" name="disability_reason_other" placeholder="Інша причина...">
                        </div>
                    </div>
                </div>

                <div class="ucu-field ucu-field--checkbox" data-field-key="health_condition_details" data-condition='{"field":"health_status","not":"none"}' style="display:none;">
                    <label class="ucu-field__label">Уточніть щодо наявного у Вас порушення стану здоров'я</label>
                    <div class="ucu-field__choices">
                        <label class="ucu-choice"><input type="checkbox" name="health_condition_details[]" value="blindness" disabled> <span>тотальна сліпота</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="health_condition_details[]" value="partial_vision_loss" disabled> <span>часткова втрата зору</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="health_condition_details[]" value="deafness" disabled> <span>глухота</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="health_condition_details[]" value="hard_of_hearing" disabled> <span>туговухість</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="health_condition_details[]" value="uses_crutches" disabled> <span>пересуваюся на милицях</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="health_condition_details[]" value="uses_wheelchair" disabled> <span>пересуваюся на візку</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="health_condition_details[]" value="depressive_or_anxiety_states" disabled> <span>депресивні та/або тривожні стани</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="health_condition_details[]" value="personality_disorders" disabled> <span>особистісні розлади</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="health_condition_details[]" value="eating_disorders" disabled> <span>харчові розлади</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="health_condition_details[]" value="other" disabled> <span>інше</span></label>
                    </div>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field" data-field-key="needs_special_living_conditions" data-condition='{"field":"health_status","not":"none"}' style="display:none;">
                    <label class="ucu-field__label">Чи потрібні Вам особливі умови для проживання в Колегіумі та/чи допомога у пересуванні по будинку та по кампусі?</label>
                    <select name="needs_special_living_conditions" disabled>
                        <option value="">— оберіть —</option>
                        <option value="yes">Так (уточніть в "інше", яку саме потребуєте допомогу та умови для комфортного проживання)</option>
                        <option value="no">Ні</option>
                    </select>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field" data-field-key="special_living_conditions_details" data-condition='{"field":"health_status","not":"none"}' style="display:none;">
                    <label class="ucu-field__label">Уточнення щодо умов проживання</label>
                    <input type="text" name="special_living_conditions_details" disabled>
                </div>

            </div>
            <div class="ucu-step-nav">
                <button type="button" class="ucu-btn ucu-btn-prev">Попередній</button>
                <button type="button" class="ucu-btn ucu-btn-next">Далі</button>
            </div>
        </section>

        <!-- ══ КРОК 5: Місце проживання ══════════════════════════════════ -->
        <section class="ucu-step-panel" data-panel="5" style="display:none;">
            <div class="ucu-booking__grid">

                <div class="ucu-field" data-field-key="region">
                    <label class="ucu-field__label">Область <span class="ucu-req">*</span></label>
                    <input type="text" name="region" required>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field" data-field-key="city">
                    <label class="ucu-field__label">Населений пункт <span class="ucu-req">*</span></label>
                    <input type="text" name="city" placeholder="місто, село, смт" required>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field" data-field-key="address">
                    <label class="ucu-field__label">Вулиця, будинок, квартира <span class="ucu-req">*</span></label>
                    <input type="text" name="actual_address" placeholder="домашня адреса" required>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

            </div>
            <div class="ucu-step-nav">
                <button type="button" class="ucu-btn ucu-btn-prev">Попередній</button>
                <button type="button" class="ucu-btn ucu-btn-next">Далі</button>
            </div>
        </section>

        <!-- ══ КРОК 6: Інформація про сім'ю ══════════════════════════════ -->
        <section class="ucu-step-panel" data-panel="6" style="display:none;">
            <div class="ucu-booking__grid">

                <div class="ucu-field" data-field-key="father_name">
                    <label class="ucu-field__label">Батько <span class="ucu-req">*</span></label>
                    <input type="text" name="father_name" placeholder="прізвище, ім'я, побатькові" required>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field" data-field-key="father_phone">
                    <label class="ucu-field__label">Контактний телефон батька <span class="ucu-req">*</span></label>
                    <input type="tel" name="father_phone" placeholder="мобільний номер" required>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field" data-field-key="father_workplace">
                    <label class="ucu-field__label">Місце праці батька <span class="ucu-req">*</span></label>
                    <input type="text" name="father_workplace" required>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field" data-field-key="mother_name">
                    <label class="ucu-field__label">Мати <span class="ucu-req">*</span></label>
                    <input type="text" name="mother_name" placeholder="прізвище, ім'я, побатькові" required>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field" data-field-key="mother_phone">
                    <label class="ucu-field__label">Контактний телефон матері <span class="ucu-req">*</span></label>
                    <input type="tel" name="mother_phone" placeholder="мобільний номер" required>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field" data-field-key="mother_workplace">
                    <label class="ucu-field__label">Місце праці матері <span class="ucu-req">*</span></label>
                    <input type="text" name="mother_workplace" required>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

            </div>
            <div class="ucu-step-nav">
                <button type="button" class="ucu-btn ucu-btn-prev">Попередній</button>
                <button type="button" class="ucu-btn ucu-btn-next">Далі</button>
            </div>
        </section>

        <!-- ══ КРОК 7: Участь у формаційній програмі ═════════════════════ -->
        <section class="ucu-step-panel" data-panel="7" style="display:none;">
            <div class="ucu-booking__grid">

                <div class="ucu-field ucu-field--radio" data-field-key="previous_collegium_participant" data-required="1">
                    <label class="ucu-field__label">Чи були Ви учасником формаційної програми з проживанням в Колегіумі раніше? <span class="ucu-req">*</span></label>
                    <div class="ucu-field__choices" style="flex-direction:row;gap:24px;">
                        <label class="ucu-choice"><input type="radio" name="previous_collegium_participant" value="yes"> <span>так</span></label>
                        <label class="ucu-choice"><input type="radio" name="previous_collegium_participant" value="no"> <span>ні</span></label>
                    </div>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <!-- Роки участі — показується завжди (як в оригіналі) -->
                <div class="ucu-field ucu-field--checkbox" data-field-key="previous_program_years" data-condition='{"field":"previous_collegium_participant","value":"yes"}' style="display:none;">
                    <label class="ucu-field__label">Вкажіть навчальній рік (роки) проходження формаційної програми та проживання в Колегіумі</label>
                    <div class="ucu-field__choices" style="flex-direction:row;flex-wrap:wrap;gap:8px 24px;">
                        <label class="ucu-choice"><input type="checkbox" name="previous_program_years[]" value="2021-2022"> <span>2021-2022</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="previous_program_years[]" value="2022-2023"> <span>2022-2023</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="previous_program_years[]" value="2023-2024"> <span>2023-2024</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="previous_program_years[]" value="2024-2025"> <span>2024-2025</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="previous_program_years[]" value="2025-2026"> <span>2025-2026</span></label>
                        <label class="ucu-choice"><input type="checkbox" name="previous_program_years[]" value="Інше" data-social-toggle="prev-year-other"> <span>Інше</span></label>
                        <div class="ucu-social-input" id="ucu-social-prev-year-other" style="display:none;">
                            <input type="text" name="previous_program_years_other" placeholder="Вкажіть рік...">
                        </div>
                    </div>
                </div>

                <div class="ucu-field" data-field-key="previous_curator_name" data-condition='{"field":"previous_collegium_participant","value":"yes"}' style="display:none;">
                    <label class="ucu-field__label">Прізвище та ім'я Вашого куратора (за останній рік формаційної програми)</label>
                    <input type="text" name="previous_curator_name">
                </div>

                <div class="ucu-field" data-field-key="previous_listener_events" data-condition='{"field":"previous_collegium_participant","value":"yes"}' style="display:none;">
                    <label class="ucu-field__label">У яких заходах ФП Ви брали участь в ролі слухача? (Понеділкові зустрічі, Колегівки, молитовні заходи та ін.)</label>
                    <input type="text" name="previous_listener_events">
                </div>

                <div class="ucu-field" data-field-key="previous_volunteer_events" data-condition='{"field":"previous_collegium_participant","value":"yes"}' style="display:none;">
                    <label class="ucu-field__label">В яких заходах ФП Ви були волонтером чи співорганізатором? (Понеділкові зустрічі, Колегівки, молитовні заходи та ін.)</label>
                    <input type="text" name="previous_volunteer_events">
                </div>

                <div class="ucu-field ucu-field--textarea" data-field-key="previous_program_experience" data-condition='{"field":"previous_collegium_participant","value":"yes"}' style="display:none;">
                    <label class="ucu-field__label">Опишіть Ваш позитивний та негативний досвід участі у формаційній програмі</label>
                    <textarea name="previous_program_experience" rows="4"></textarea>
                </div>

                <div class="ucu-field ucu-field--radio" data-field-key="previous_cleaning_responsibility" data-condition='{"field":"previous_collegium_participant","value":"yes"}' style="display:none;">
                    <label class="ucu-field__label">Оцініть, наскільки сумлінно Ви виконували обов'язок прибирання у кімнаті/у світлиці</label>
                    <div class="ucu-field__choices">
                        <label class="ucu-choice"><input type="radio" name="previous_cleaning_responsibility" value="1"> <span>1 (Не виконував/ла)</span></label>
                        <label class="ucu-choice"><input type="radio" name="previous_cleaning_responsibility" value="2"> <span>2 (Дуже рідко)</span></label>
                        <label class="ucu-choice"><input type="radio" name="previous_cleaning_responsibility" value="3"> <span>3 (Задовільно)</span></label>
                        <label class="ucu-choice"><input type="radio" name="previous_cleaning_responsibility" value="4"> <span>4 (Добре)</span></label>
                        <label class="ucu-choice"><input type="radio" name="previous_cleaning_responsibility" value="5"> <span>5 (Відмінно)</span></label>
                    </div>
                </div>

                <div class="ucu-field ucu-field--radio" data-field-key="previous_relationship_experience" data-condition='{"field":"previous_collegium_participant","value":"yes"}' style="display:none;">
                    <label class="ucu-field__label">Оберіть варіант, який найкраще відображає Ваш досвід побудови стосунків за час проходження Формаційної програми у Колегіумі (вміння знаходити спільну мову із сусідами, конструктивно вирішувати конфлікти та творити добру атмосферу навколо себе).</label>
                    <div class="ucu-field__choices">
                        <label class="ucu-choice"><input type="radio" name="previous_relationship_experience" value="good_conflict_resolution"> <span>Маю добрий досвід спілкування та конструктивного вирішення конфліктів.</span></label>
                        <label class="ucu-choice"><input type="radio" name="previous_relationship_experience" value="asks_curator_for_help"> <span>У вирішенні конфліктів завжди звертаюся по допомогу куратора.</span></label>
                        <label class="ucu-choice"><input type="radio" name="previous_relationship_experience" value="avoids_conflict_talks"> <span>Уникаю розмов стосовно конфліктних ситуацій та приймаю інших такими, якими вони є.</span></label>
                        <label class="ucu-choice"><input type="radio" name="previous_relationship_experience" value="defends_position"> <span>Відстоюю свою позицію. Не є прихильником миру "про людське око".</span></label>
                        <label class="ucu-choice"><input type="radio" name="previous_relationship_experience" value="comfort_people_only"> <span>Будую стосунки з людьми, з якими мені комфортно. Люблю, коли все по-моєму.</span></label>
                        <label class="ucu-choice"><input type="radio" name="previous_relationship_experience" value="Інше" data-radio-shows="prev-experience-other"> <span>Інше</span></label>
                    </div>
                    <div class="ucu-social-input" id="ucu-radio-prev-experience-other" style="display:none;">
                        <input type="text" name="previous_relationship_experience_other" placeholder="Інший досвід...">
                    </div>
                </div>

                <div class="ucu-field ucu-field--textarea" data-field-key="previous_community_contribution" data-condition='{"field":"previous_collegium_participant","value":"yes"}' style="display:none;">
                    <label class="ucu-field__label">Яким був Ваш особистий вклад у розбудову спільнотного життя Колегіуму?</label>
                    <textarea name="previous_community_contribution" rows="4"></textarea>
                </div>

                <div class="ucu-field ucu-field--textarea" data-field-key="student_organizations" data-condition='{"field":"previous_collegium_participant","value":"yes"}' style="display:none;">
                    <label class="ucu-field__label">До яких студентських організацій в УКУ Ви належите?</label>
                    <textarea name="student_organizations" rows="4"></textarea>
                </div>

                <!-- Питання для всіх -->
                <div class="ucu-field ucu-field--radio" data-field-key="curators_attitude" data-required="1">
                    <label class="ucu-field__label">Команда формаційної програма включає кураторів (наставників), які проживають разом зі студентами на поверхах і дбають про формацію, дотримання правил та дружню атмосферу. Як Ви до цього ставитесь? <span class="ucu-req">*</span></label>
                    <div class="ucu-field__choices">
                        <label class="ucu-choice"><input type="radio" name="curators_attitude" value="value_care"> <span>Ціную, що поруч будуть люди, які дбають про спільний добробут та мою формацію готовий(-а) створювати добру атмосферу та дотримуватися правил;</span></label>
                        <label class="ucu-choice"><input type="radio" name="curators_attitude" value="ok_unsure"> <span>Готовий(-а) до проживання на поверсі з куратором (наставником), проте не розумію, як я до цього ставлюсь, готовий(-а) жити згідно правил;</span></label>
                        <label class="ucu-choice"><input type="radio" name="curators_attitude" value="ok_less_control"> <span>Добре, але сподіваюся, що контролю не буде надто багато, готовий(-а) жити згідно правил;</span></label>
                        <label class="ucu-choice"><input type="radio" name="curators_attitude" value="negative"> <span>Негативно ставлюсь до проживання з куратором (наставником) та дотримання правил</span></label>
                    </div>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field ucu-field--radio" data-field-key="religious_staff_interaction" data-required="1">
                    <label class="ucu-field__label">Формаційна програма реалізується працівниками, серед яких є духовні особи - священники та сестри-монахині, які проживають у Колегіумі. Якою буде Ваша взаємодія з ними? <span class="ucu-req">*</span></label>
                    <div class="ucu-field__choices">
                        <label class="ucu-choice"><input type="radio" name="religious_staff_interaction" value="familiar_environment"> <span>Готовий(а) до взаємодії, оскільки маю досвід спілкування й співпраці;</span></label>
                        <label class="ucu-choice"><input type="radio" name="religious_staff_interaction" value="interested_to_meet"> <span>Готовий(а) до взаємодії, але ніколи не спілкував(-ла)ся;</span></label>
                        <label class="ucu-choice"><input type="radio" name="religious_staff_interaction" value="no_experience_unsure"> <span>Не маю досвіду спілкування, тому не знаю, як усе складеться;</span></label>
                        <label class="ucu-choice"><input type="radio" name="religious_staff_interaction" value="bad_experience_distance"> <span>Маю поганий досвід. У спілкуванні буду тримати помірну дистанцію;</span></label>
                        <label class="ucu-choice"><input type="radio" name="religious_staff_interaction" value="dont_want_imposed"> <span>Не хочу, аби мені щось нав'язували.</span></label>
                    </div>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field ucu-field--radio" data-field-key="disability_attitude" data-required="1">
                    <label class="ucu-field__label">Серед учасників Формаційної програми є особи з інвалідністю (потреби, яких впливають на спільний побут). Як Ви до цього ставитесь? <span class="ucu-req">*</span></label>
                    <div class="ucu-field__choices">
                        <label class="ucu-choice"><input type="radio" name="attitude_to_people_with_disabilities" value="same_wing_help"> <span>Повністю відкритий(-а) до спільного проживання, побуту та взаємної підтримки;</span></label>
                        <label class="ucu-choice"><input type="radio" name="attitude_to_people_with_disabilities" value="same_room_positive"> <span>Готовий(-а) проживати поруч і допомагати за потреби;</span></label>
                        <label class="ucu-choice"><input type="radio" name="attitude_to_people_with_disabilities" value="positive_unsure_relationships"> <span>Розумію важливість інклюзивного середовища та готовий(-а) вчитись будувати добрі стосунки;</span></label>
                        <label class="ucu-choice"><input type="radio" name="attitude_to_people_with_disabilities" value="no_communication"> <span>Не хочу спілкуватися з ними</span></label>
                    </div>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field ucu-field--radio" data-field-key="emmaus_attitude" data-required="1">
                    <label class="ucu-field__label">Формаційна програма передбачає спільні заходи з мешканцями дому "Емаус" (спільнота, в якій проживають особи з ментальною та/або фізичною інвалідністю). Якою буде Ваша взаємодія? <span class="ucu-req">*</span></label>
                    <div class="ucu-field__choices">
                        <label class="ucu-choice"><input type="radio" name="emmaus_attitude" value="want_to_meet_volunteer"> <span>Хочу познайомитись з ними та спробувати бути волонтером;</span></label>
                        <label class="ucu-choice"><input type="radio" name="emmaus_attitude" value="volunteering_experience"> <span>Позитивно, буду відвідувати їхні заходи;</span></label>
                        <label class="ucu-choice"><input type="radio" name="emmaus_attitude" value="positive_no_participation"> <span>Добре, але долучатись до спільних акцій не буду;</span></label>
                        <label class="ucu-choice"><input type="radio" name="emmaus_attitude" value="not_interested"> <span>Мене не цікавлять такі спільноти.</span></label>
                    </div>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field ucu-field--radio" data-field-key="rules_attitude" data-required="1">
                    <label class="ucu-field__label">Вкажіть ваше ставлення до правил та обмежень? <span class="ucu-req">*</span></label>
                    <div class="ucu-field__choices">
                        <label class="ucu-choice"><input type="radio" name="rules_attitude" value="strict_rules_needed"> <span>Це дуже потрібно. Без чіткого виконання правил ніяк;</span></label>
                        <label class="ucu-choice"><input type="radio" name="rules_attitude" value="rules_with_exceptions"> <span>Правила потрібні, але завжди мають бути винятки;</span></label>
                        <label class="ucu-choice"><input type="radio" name="rules_attitude" value="cant_live_in_frames"> <span>Я не планую постійно жити в рамках.</span></label>
                    </div>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field ucu-field--radio" data-field-key="cleanliness_attitude" data-required="1">
                    <label class="ucu-field__label">Ваше ставлення до порядку та чистоти? <span class="ucu-req">*</span></label>
                    <div class="ucu-field__choices">
                        <label class="ucu-choice"><input type="radio" name="cleanliness_attitude" value="always_keep_clean"> <span>Завжди дбаю про чистоту своєї кімнати самостійно та готовий допомагати іншим;</span></label>
                        <label class="ucu-choice"><input type="radio" name="cleanliness_attitude" value="clean_after_self_only"> <span>За собою приберу, за іншими – не буду;</span></label>
                        <label class="ucu-choice"><input type="radio" name="cleanliness_attitude" value="order_is_pressure"> <span>Негативно, оскільки порядок тисне на мене і я не можу нормально функціонувати.</span></label>
                    </div>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field ucu-field--radio" data-field-key="new_relationship_readiness" data-required="1">
                    <label class="ucu-field__label">Життя в Колегіумі передбачає спілкування та проживання з різними людьми. Оберіть варіант, який найкраще відображає Вашу готовність до побудови нових стосунків, вирішення конфліктів та вміння творити добру атмосферу навколо себе. <span class="ucu-req">*</span></label>
                    <div class="ucu-field__choices">
                        <label class="ucu-choice"><input type="radio" name="new_relationship_readiness" value="easy_conflict_resolution"> <span>Легко знаходжу спільну мову з іншими та вмію конструктивно вирішувати конфліктні ситуації</span></label>
                        <label class="ucu-choice"><input type="radio" name="new_relationship_readiness" value="friendly_avoid_conflicts"> <span>Я не люблю конфліктних ситуацій, стараюся не провокувати на конфлікт та не зважаю на провокації</span></label>
                        <label class="ucu-choice"><input type="radio" name="new_relationship_readiness" value="avoid_conflict_talks"> <span>Уникаю розмов стосовно конфліктних ситуацій та приймаю інших такими, якими вони є</span></label>
                        <label class="ucu-choice"><input type="radio" name="new_relationship_readiness" value="defend_position"> <span>Буду відстоювати свою позицію. Не прихильник показового примирення.</span></label>
                    </div>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

            </div>
            <div class="ucu-step-nav">
                <button type="button" class="ucu-btn ucu-btn-prev">Попередній</button>
                <button type="button" class="ucu-btn ucu-btn-next">Далі</button>
            </div>
        </section>

        <!-- ══ КРОК 8: Мотиваційна частина ═══════════════════════════════ -->
        <section class="ucu-step-panel" data-panel="8" style="display:none;">
            <div class="ucu-booking__grid">

                <div class="ucu-field ucu-field--textarea" data-field-key="values_events">
                    <label class="ucu-field__label">Опишіть не менше 2 подій, які сформували Ваші цінності і світогляд <span class="ucu-req">*</span></label>
                    <textarea name="values_events" rows="8" required></textarea>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field ucu-field--textarea" data-field-key="religious_experience">
                    <div class="ucu-field__hint" style="font-size:.875rem;color:#374151;margin-bottom:8px;line-height:1.7;">
                        <p><strong>Як Ваша поведінка та рішення під час участі у Формаційній програмі будуть узгоджуватися з її правилами та формаційними засадами? Наведіть приклади. У довільній формі опишіть:</strong></p>
                        <p><strong>Здатність до діалогу та відкритості</strong><br>— Як Ви поводитеся, коли у вашому оточенні виникають різні думки?<br>— Наведіть приклад ситуації, коли Вам довелося шукати порозуміння.</p>
                        <p><strong>Повага до людської гідності та правил спільноти</strong><br>— Як Ви виявляєте повагу до людей, з якими живете або навчаєтесь?<br>— Наведіть приклад відповідальної поведінки в спільному середовищі.</p>
                        <p><strong>Усвідомлення ціннісних засад програми</strong><br>— Як християнське бачення людини співпадає з Вашими поглядами?<br>— Чому важливо, щоб вчинки учасника не суперечили місії УКУ?</p>
                    </div>
                    <label class="ucu-field__label">(Максимальна відповідь 400 слів) <span class="ucu-req">*</span></label>
                    <textarea name="religious_experience" rows="8" required></textarea>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field ucu-field--textarea" data-field-key="motivation_letter">
                    <div class="ucu-field__hint" style="font-size:.875rem;color:#374151;margin-bottom:8px;">
                        <strong>Чому Ви хочете брати участь у формаційній програмі "Християнська духовність у постмодерній добі" з проживанням у Колегіумі у 2026–2027 н. р.?</strong>
                    </div>
                    <label class="ucu-field__label">Напишіть мотиваційний лист довільної форми (рекомендований обсяг — 300–500 слів), розкривши кожен із даних пунктів: Ваше розуміння формату програми та її формаційних засад; Вашу особисту мотивацію та цілі участі; Вашу готовність жити в спільноті з визначеними правилами. Лист має бути особистим і рефлексивним. <span class="ucu-req">*</span></label>
                    <textarea name="motivation_letter" rows="9" required></textarea>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field ucu-field--textarea" data-field-key="talents_hobbies">
                    <label class="ucu-field__label">Які Ваші таланти, захоплення, хобі? <span class="ucu-req">*</span></label>
                    <textarea name="talents_hobbies" rows="4" required></textarea>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

            </div>
            <div class="ucu-step-nav">
                <button type="button" class="ucu-btn ucu-btn-prev">Попередній</button>
                <button type="button" class="ucu-btn ucu-btn-next">Далі</button>
            </div>
        </section>

        <!-- ══ КРОК 9: Згода на обробку даних ════════════════════════════ -->
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

                <div class="ucu-field ucu-field--radio" data-field-key="mandatory_events_acceptance" data-required="1">
                    <label class="ucu-field__label">Навчально-формаційна програма передбачає участь в обов'язкових заходах, у тому числі й релігійного характеру: <span class="ucu-req">*</span></label>
                    <div class="ucu-field__choices" style="flex-direction:row;gap:24px;">
                        <label class="ucu-choice"><input type="radio" name="mandatory_events_acceptance" value="yes"> <span>так, я це розумію і погоджуюсь</span></label>
                        <label class="ucu-choice"><input type="radio" name="mandatory_events_acceptance" value="no"> <span>для мене це неприйнятно</span></label>
                    </div>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

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

                <div class="ucu-field ucu-field--radio" data-field-key="housing_rules_consent" data-required="1">
                    <label class="ucu-field__label">Засвідчую, що поінформований(-а) про Формаційну програму «Християнська духовність в постмодерній добі», хочу взяти в ній участь, розумію та погоджуюся з правилами проживання в Колегіумі <span class="ucu-req">*</span></label>
                    <div class="ucu-field__choices" style="flex-direction:row;gap:24px;">
                        <label class="ucu-choice"><input type="radio" name="housing_rules_consent" value="yes"> <span>так</span></label>
                        <label class="ucu-choice"><input type="radio" name="housing_rules_consent" value="no"> <span>ні</span></label>
                    </div>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

                <div class="ucu-field ucu-field--radio" data-field-key="personal_data_consent" data-required="1">
                    <label class="ucu-field__label">Гарантуємо, що зібрані персональні дані не будуть розголошені. Чи даєте дозвіл на обробку Ваших персональних даних? <span class="ucu-req">*</span></label>
                    <div class="ucu-field__choices" style="flex-direction:row;gap:24px;">
                        <label class="ucu-choice"><input type="radio" name="personal_data_consent" value="yes"> <span>так</span></label>
                        <label class="ucu-choice"><input type="radio" name="personal_data_consent" value="no"> <span>ні (заява не буде прийнята)</span></label>
                    </div>
                    <span class="ucu-field__error" data-field-error></span>
                </div>

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
