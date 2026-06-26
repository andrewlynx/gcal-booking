(function ($) {
    'use strict';

    function conditionMatches(condition, value) {
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

    function syncSocialAccountChoices($form, changedInput) {
        var key = 'social_accounts';
        var $changed = $(changedInput);
        var $choices = $form.find('[name="' + key + '[]"]');

        if (!$choices.length || $changed.attr('name') !== key + '[]') {
            return;
        }

        if ($changed.val() === 'none' && $changed.prop('checked')) {
            $choices.not($changed).prop('checked', false);
            return;
        }

        if ($changed.val() !== 'none' && $changed.prop('checked')) {
            $choices.filter('[value="none"]').prop('checked', false);
        }
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
            syncSocialAccountChoices($form, this);
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

})(jQuery);
