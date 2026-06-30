(function ($) {
    'use strict';

    function conditionMatches(condition, value) {
        // Support shorthand {"not":"value"} syntax used in step 4 health_status conditions
        if (condition.not !== undefined) {
            if (Array.isArray(value)) return value.indexOf(String(condition.not)) === -1;
            return String(value) !== String(condition.not);
        }
        // Support shorthand {"values":[...]} syntax
        if (condition.values !== undefined) {
            if (Array.isArray(value)) return condition.values.some(function(v) { return value.indexOf(String(v)) !== -1; });
            return condition.values.indexOf(value) !== -1;
        }
        var expected = condition.value;
        if (Array.isArray(value)) {
            if (condition.operator === '!=') return value.indexOf(String(expected)) === -1;
            if (condition.operator === 'in') return Array.isArray(expected) && expected.some(function (item) { return value.indexOf(String(item)) !== -1; });
            if (condition.operator === 'not_in') return Array.isArray(expected) && expected.every(function (item) { return value.indexOf(String(item)) === -1; });
            return value.indexOf(String(expected)) !== -1;
        }
        if (condition.operator === '!=') return String(value) !== String(expected);
        if (condition.operator === 'in') return Array.isArray(expected) && expected.indexOf(value) !== -1;
        if (condition.operator === 'not_in') return Array.isArray(expected) && expected.indexOf(value) === -1;
        return String(value) === String(expected);
    }

    function fieldValue($form, key) {
        var $checkboxes = $form.find('[name="' + key + '[]"]:checked');
        if ($checkboxes.length) {
            return $checkboxes.map(function () { return String($(this).val()); }).get();
        }
        var $checked = $form.find('[name="' + key + '"]:checked');
        if ($checked.length) return $checked.val();
        return $form.find('[name="' + key + '"]').val() || '';
    }

    function updateConditional($form) {
        $form.find('[data-condition]').each(function () {
            var $field = $(this);
            var condition = $field.data('condition');
            var active = conditionMatches(condition, fieldValue($form, condition.field));
            $field.toggle(active);
            $field.find('input, select, textarea').prop('disabled', !active).prop('required', false);
            if (active && $field.data('required') === 1) {
                $field.find('input:not([type="checkbox"]), select, textarea').prop('required', true);
            }
            if (!active) {
                $field.find('[data-field-error]').text('');
            }
        });
    }

    function showMessage($wrap, type, message) {
        $wrap.find('[data-ucu-success],[data-ucu-error]').prop('hidden', true).text('');
        var selector = type === 'success' ? '[data-ucu-success]' : '[data-ucu-error]';
        $wrap.find(selector).text(message).prop('hidden', false);
    }

    function loadSlots($wrap) {
        var $select = $wrap.find('[data-ucu-slots]');
        $.post(ucuCollegiumBooking.ajaxUrl, { action: 'ucu_collegium_get_slots', nonce: ucuCollegiumBooking.nonce })
            .done(function (res) {
                $select.empty().append($('<option>', { value: '', text: 'Оберіть час співбесіди' }));
                if (!res.success || !res.data.slots.length) {
                    $select.append($('<option>', { value: '', text: 'Немає доступних слотів' }));
                    return;
                }
                res.data.slots.forEach(function (slot) {
                    $select.append($('<option>', { value: slot.id, text: slot.label + ' (' + slot.available + ' місць)' }));
                });
            })
            .fail(function () {
                $select.empty().append($('<option>', { value: '', text: 'Не вдалося завантажити слоти' }));
            });
    }

    $('[data-ucu-booking-form]').each(function () {
        var $wrap = $(this);
        var $form = $wrap.find('form');
        var $submit = $wrap.find('[data-ucu-submit]');
        updateConditional($form);
        loadSlots($wrap);

        $form.on('change input', 'input, select, textarea', function () {
            updateConditional($form);
        });

        $wrap.find('[data-ucu-slots]').on('change', function () {
            var slotId = $(this).val();
            var message = $wrap.find('[data-ucu-hold-message]');
            message.text('');
            if (!slotId) return;

            $.post(ucuCollegiumBooking.ajaxUrl, {
                action: 'ucu_collegium_hold_slot',
                nonce: ucuCollegiumBooking.nonce,
                slot_id: slotId,
                session_token: $form.find('[name="session_token"]').val(),
                email: fieldValue($form, 'email')
            }).done(function (res) {
                if (res.success) {
                    message.text(res.data.message);
                } else {
                    showMessage($wrap, 'error', res.data.message || ucuCollegiumBooking.messages.slotUnavailable);
                    loadSlots($wrap);
                }
            }).fail(function (xhr) {
                var res = xhr.responseJSON || {};
                showMessage($wrap, 'error', res.data && res.data.message ? res.data.message : ucuCollegiumBooking.messages.slotUnavailable);
                loadSlots($wrap);
            });
        });

        $form.on('submit', function (event) {
            event.preventDefault();
            updateConditional($form);
            $wrap.find('[data-field-error]').text('');
            $submit.prop('disabled', true).text('Надсилання...');

            var formData = new FormData($form[0]);
            formData.set('nonce', ucuCollegiumBooking.nonce);
            $form.find('input[type="file"]:not(:disabled)').each(function () {
                if (this.name && this.files && this.files.length) {
                    formData.set(this.name, this.files[0]);
                }
            });

            $.ajax({
                url: ucuCollegiumBooking.ajaxUrl,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false
            }).done(function (res) {
                if (res.success) {
                    showMessage($wrap, 'success', res.data.message || ucuCollegiumBooking.messages.success);
                    $form[0].reset();
                    updateConditional($form);
                    loadSlots($wrap);
                } else {
                    showMessage($wrap, 'error', res.data.message || ucuCollegiumBooking.messages.genericError);
                }
            }).fail(function (xhr) {
                var res = xhr.responseJSON || {};
                var data = res.data || {};
                if (data.errors) {
                    Object.keys(data.errors).forEach(function (key) {
                        $wrap.find('[data-field-key="' + key + '"] [data-field-error]').text(data.errors[key]);
                    });
                }
                showMessage($wrap, 'error', data.message || ucuCollegiumBooking.messages.genericError);
            }).always(function () {
                $submit.prop('disabled', false).text('Надіслати заявку');
            });
        });
    });



    // ── Radio "Інше" → показати поле ─────────────────────────────────────
    $(document).on('change', '[data-radio-shows]', function () {
        var target = $(this).data('radio-shows');
        $('#ucu-radio-' + target).toggle($(this).is(':checked'));
    });
    // При зміні radio групи — ховаємо всі radio-shows в цій групі
    $(document).on('change', 'input[type="radio"]', function () {
        var name = $(this).attr('name');
        $('[name="' + name + '"][data-radio-shows]').each(function () {
            var target = $(this).data('radio-shows');
            if (!$(this).is(':checked')) {
                $('#ucu-radio-' + target).hide();
            }
        });
    });

    // ── Соціальні мережі: чекбокс → показати поле ────────────────────────
    $(document).on('change', '[data-social-toggle]', function () {
        var target = $(this).data('social-toggle');
        $('#ucu-social-' + target).toggle($(this).is(':checked'));
        if (!$(this).is(':checked')) {
            $('#ucu-social-' + target).find('input').val('');
        }
    });


    // ── social_profile_url — збираємо всі соцмережі в одне поле ─────────
    function updateSocialProfileUrl() {
        var links = [];
        var instagram = jQuery('input[name="instagram_url"]').val();
        var whatsapp  = jQuery('input[name="whatsapp"]').val();
        var telegram  = jQuery('input[name="telegram"]').val();
        var tiktok    = jQuery('input[name="tiktok_url"]').val();
        if (instagram) links.push('Instagram: ' + instagram);
        if (whatsapp)  links.push('WhatsApp: ' + whatsapp);
        if (telegram)  links.push('Telegram: ' + telegram);
        if (tiktok)    links.push('TikTok: ' + tiktok);
        jQuery('#social_profile_url_hidden').val(links.join(', ') || 'Немає');
    }
    jQuery(document).on('input', 'input[name="instagram_url"], input[name="whatsapp"], input[name="telegram"], input[name="tiktok_url"]', updateSocialProfileUrl);
    jQuery(document).on('change', 'input[name="social_media[]"]', function() {
        setTimeout(updateSocialProfileUrl, 100);
        if (jQuery('input[name="social_media[]"][value="Немає в жодному із зазначених"]').is(':checked')) {
            jQuery('#social_profile_url_hidden').val('Немає');
        }
    });

    // ── Date picker: три селекти → hidden input ──────────────────────────
    $(document).on('change', '[data-date-part]', function () {
        var target = $(this).data('date-target');
        var $wrap  = $(this).closest('[data-field-key]');
        var day    = $wrap.find('[data-date-part="day"]').val();
        var month  = $wrap.find('[data-date-part="month"]').val();
        var year   = $wrap.find('[data-date-part="year"]').val();
        if (day && month && year) {
            $wrap.find('[data-date-hidden]').val(year + '-' + month + '-' + day);
            $wrap.find('.ucu-date-part').removeClass('ucu-invalid');
            $wrap.find('[data-field-error]').text('');
        } else {
            $wrap.find('[data-date-hidden]').val('');
        }
    });

    // ── Файл: показуємо назву ────────────────────────────────────────────
    $(document).on('change', '[data-file-input]', function () {
        var name   = this.files && this.files[0] ? this.files[0].name : 'Файл не обрано';
        var hasFile = !!(this.files && this.files[0]);
        $('#' + this.id + '-name').text(name).toggleClass('ucu-has-file', hasFile);
    });


    // ══ СТЕПЕР ════════════════════════════════════════════════════════════
    var currentStep = 1;
    var totalSteps  = 10;

    function updateStepper(step) {
        $('[data-ucu-booking-form] .ucu-step').each(function () {
            var s = parseInt($(this).data('step'));
            $(this).removeClass('active done');
            if (s === step) $(this).addClass('active');
            if (s < step)  $(this).addClass('done');
        });
        $('[data-ucu-booking-form] .ucu-step-line').each(function (i) {
            $(this).toggleClass('done', i + 1 < step);
        });
    }

    function goToStep(step) {
        $('[data-ucu-booking-form] .ucu-step-panel').hide().removeClass('active');
        $('[data-ucu-booking-form] [data-panel="' + step + '"]').show().addClass('active');
        currentStep = step;
        updateStepper(step);
        var $wrap = $('[data-ucu-booking-form]');
        if ($wrap.length) {
            $('html,body').animate({ scrollTop: $wrap.offset().top - 30 }, 250);
        }
        // Перерахувати умовні поля після переходу
        setTimeout(applyConditions, 50);
    }

    function validateCurrentStep() {
        var $panel = $('[data-ucu-booking-form] [data-panel="' + currentStep + '"]');
        var isValid = true;

        $panel.find('[data-field-error]').text('');

        // Text/email/tel/textarea inputs
        $panel.find('input[required]:not([type="radio"]):not([type="checkbox"]):not([data-date-hidden]):not([style*="display:none"]):not(:disabled), select[required]:not(:disabled), textarea[required]:not(:disabled)').each(function () {
            var $el  = $(this);
            if ($el.closest('[style*="display:none"]').length) return;
            var val  = $el.val() ? $el.val().trim() : '';
            var type = $el.attr('type');
            var $err = $el.closest('[data-field-key]').find('[data-field-error]');
            if (!val) {
                $el.addClass('ucu-invalid');
                $err.text("Це поле обов'язкове.");
                isValid = false;
            } else if (type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
                $el.addClass('ucu-invalid');
                $err.text('Введіть коректний e-mail.');
                isValid = false;
            } else {
                $el.removeClass('ucu-invalid');
            }
        });

        // Date hidden fields
        $panel.find('[data-date-hidden][required]').each(function () {
            if (!$(this).val()) {
                $(this).closest('[data-field-key]').find('.ucu-date-part').addClass('ucu-invalid');
                $(this).closest('[data-field-key]').find('[data-field-error]').text('Оберіть дату народження.');
                isValid = false;
            }
        });

        // Radio groups
        $panel.find('[data-required="1"]').each(function () {
            var $field = $(this);
            if ($field.closest('[style*="display:none"]').length) return;
            var $radios = $field.find('input[type="radio"]');
            if (!$radios.length) return;
            if ($field.find('input[type="radio"]:checked').length === 0) {
                $field.find('[data-field-error]').text('Оберіть один з варіантів.');
                isValid = false;
            }
        });

        return isValid;
    }

    // ── Кнопка "Далі"

    $(document).on('click', '[data-ucu-booking-form] .ucu-btn-next:not([data-ucu-submit])', function () {
        if (validateCurrentStep()) goToStep(currentStep + 1);
    });

    // Кнопка "Попередній"
    $(document).on('click', '[data-ucu-booking-form] .ucu-btn-prev', function () {
        goToStep(currentStep - 1);
    });

    // Знімаємо помилки при зміні
    $(document).on('input change', '[data-ucu-booking-form] input, [data-ucu-booking-form] select, [data-ucu-booking-form] textarea', function () {
        $(this).removeClass('ucu-invalid');
        $(this).closest('[data-field-key]').find('[data-field-error]').text('');
    });

    // ── Умовна логіка: єдина функція, делегує до updateConditional ───────
    // FIX: стара applyConditions дублювала логіку і не підтримувала оператор
    // "not", що використовується на кроці 4 (health_status). Тепер вся умовна
    // логіка проходить через updateConditional де conditionMatches вже
    // підтримує "not", "values" та всі інші оператори.
    function applyConditions() {
        $('[data-ucu-booking-form] form').each(function () {
            updateConditional($(this));
        });
    }

    // Тригер на radio і select (native, обходить jQuery конфлікти)
    document.addEventListener('change', function(e) {
        var t = e.target;
        if (!t) return;
        if ((t.type === 'radio' || t.tagName === 'SELECT') && t.closest('[data-ucu-booking-form]')) {
            applyConditions();
        }
    });

    // Date picker
    $(document).on('change', '[data-ucu-booking-form] [data-date-part]', function () {
        var $wrap  = $(this).closest('[data-field-key]');
        var day    = $wrap.find('[data-date-part="day"]').val();
        var month  = $wrap.find('[data-date-part="month"]').val();
        var year   = $wrap.find('[data-date-part="year"]').val();
        if (day && month && year) {
            $wrap.find('[data-date-hidden]').val(year + '-' + month + '-' + day);
            $wrap.find('.ucu-date-part').removeClass('ucu-invalid');
            $wrap.find('[data-field-error]').text('');
        } else {
            $wrap.find('[data-date-hidden]').val('');
        }
    });

    // File input
    $(document).on('change', '[data-ucu-booking-form] [data-file-input]', function () {
        var name    = this.files && this.files[0] ? this.files[0].name : 'Файл не обрано';
        var hasFile = !!(this.files && this.files[0]);
        $('#' + this.id + '-name').text(name).toggleClass('ucu-has-file', hasFile);
    });

    // Ініціалізація
    goToStep(1);

    // ── Native JS fallback для умовних полів (обходить jQuery конфлікти) ──
    // Ініціалізація умовних полів
    applyConditions();

})(jQuery);