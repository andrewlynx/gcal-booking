(function ($) {
    'use strict';

    // Мінімальна дата — сьогодні
    var today = new Date().toISOString().split('T')[0];
    $('#gcal-date').attr('min', today);

    // ── Валідація одного поля ─────────────────────────────────────────────
    function validateField($input) {
        var val   = $input.val().trim();
        var name  = $input.attr('name');
        var $err  = $input.siblings('.gcal-field-error');
        var error = '';

        if (!val) {
            error = 'Це поле обов\'язкове.';
        } else if (name === 'phone') {
            // Перевірка формату телефону (допускає +380, 0, пробіли, дефіси)
            if (!/^[\+]?[\d\s\-\(\)]{7,15}$/.test(val)) {
                error = 'Введіть коректний номер телефону.';
            }
        } else if (name === 'date') {
            if (val < today) {
                error = 'Дата не може бути в минулому.';
            }
        }

        if (error) {
            $input.addClass('gcal-invalid');
            $err.text(error);
            return false;
        } else {
            $input.removeClass('gcal-invalid');
            $err.text('');
            return true;
        }
    }

    // Валідація при виході з поля
    $('#gcal-booking-form input').on('blur', function () {
        validateField($(this));
    });

    // Знімаємо помилку при введенні
    $('#gcal-booking-form input').on('input', function () {
        if ($(this).hasClass('gcal-invalid')) {
            validateField($(this));
        }
    });

    // ── Показ алертів ─────────────────────────────────────────────────────
    function showAlert(type, message) {
        $('.gcal-booking-alert').hide();
        var $alert = type === 'success'
            ? $('.gcal-booking-success')
            : $('.gcal-booking-error');
        $alert.text(message).slideDown(200);

        // Скрол до алерту
        $('html, body').animate({
            scrollTop: $alert.offset().top - 80
        }, 300);
    }

    function hideAlerts() {
        $('.gcal-booking-alert').hide();
    }

    // ── Стан кнопки ───────────────────────────────────────────────────────
    function setLoading(loading) {
        var $btn = $('#gcal-submit');
        if (loading) {
            $btn.prop('disabled', true);
            $btn.find('.gcal-btn-text').hide();
            $btn.find('.gcal-btn-loader').show();
        } else {
            $btn.prop('disabled', false);
            $btn.find('.gcal-btn-text').show();
            $btn.find('.gcal-btn-loader').hide();
        }
    }

    // ── Відправка форми ───────────────────────────────────────────────────
    $('#gcal-booking-form').on('submit', function (e) {
        e.preventDefault();
        hideAlerts();

        // Валідація всіх полів
        var isValid = true;
        $(this).find('input').each(function () {
            if (!validateField($(this))) {
                isValid = false;
            }
        });

        if (!isValid) return;

        setLoading(true);

        $.ajax({
            url:    gcalBooking.ajaxUrl,
            method: 'POST',
            data: {
                action: 'gcal_submit',
                nonce:  gcalBooking.nonce,
                name:   $('#gcal-name').val().trim(),
                phone:  $('#gcal-phone').val().trim(),
                date:   $('#gcal-date').val(),
                time:   $('#gcal-time').val(),
            },
            success: function (res) {
                setLoading(false);
                if (res.success) {
                    showAlert('success', res.data.message);
                    $('#gcal-booking-form')[0].reset();
                    $('#gcal-booking-form input').removeClass('gcal-invalid');
                } else {
                    showAlert('error', res.data.message);
                }
            },
            error: function () {
                setLoading(false);
                showAlert('error', 'Сталась помилка з\'єднання. Спробуйте пізніше.');
            }
        });
    });

})(jQuery);
