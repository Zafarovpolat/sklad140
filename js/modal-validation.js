// === ВАЛИДАЦИЯ И AJAX-ОТПРАВКА ВСЕХ ФОРМ ТЕМЫ ===
(function () {
    'use strict';

    var FORMS = window.s140Forms || {};
    var AJAX_URL = FORMS.ajaxUrl || (window.ajaxurl || '/wp-admin/admin-ajax.php');
    var NONCE = FORMS.nonce || '';

    /**
     * По классу модалки определяем form_type и success-модалку
     */
    function detectModalContext(form) {
        var modal = form.closest ? form.closest('.modal') : null;
        var ctx = { type: 'generic', successSelector: '.modal--callme-back-success' };
        if (!modal) return ctx;
        if (modal.classList.contains('modal--callme-back')) {
            ctx.type = 'callme-back';
            ctx.successSelector = '.modal--callme-back-success';
        } else if (modal.classList.contains('modal--one-click-buy')) {
            ctx.type = 'one-click-buy';
            ctx.successSelector = '.modal--one-click-buy-success';
        } else if (modal.classList.contains('modal--choose-exact')) {
            ctx.type = 'choose-exact';
            ctx.successSelector = '.modal--choose-exact-success';
        } else if (modal.classList.contains('modal--respond')) {
            ctx.type = 'vacancy';
            ctx.successSelector = '.modal--callme-back-success';
        }
        return ctx;
    }

    /**
     * Достаёт название и цену из верхней части модалки (если есть товар)
     */
    function readProductFromModal(modal) {
        if (!modal) return '';
        var t = modal.querySelector('.cart-product__info-title');
        if (!t) return '';
        var name = (t.textContent || '').trim();
        var price = '';
        var p = modal.querySelector('.cart-product__info-price, .cart-product__info-price--default');
        if (p) price = (p.textContent || '').replace(/\s+/g, ' ').trim();
        return price ? (name + ' (' + price + ')') : name;
    }

    function clearErrors(form) {
        var inputs = form.querySelectorAll('input.input, input[type="text"], input[type="tel"], input[type="email"]');
        inputs.forEach(function (i) {
            i.style.borderColor = '';
            i.classList.remove('border-red-500');
        });
        var checkboxes = form.querySelectorAll('.input-checkbox__input');
        checkboxes.forEach(function (cb) {
            if (cb.parentElement) cb.parentElement.style.outline = '';
        });
    }

    function markInvalid(input) {
        if (!input) return;
        input.style.borderColor = '#f53535';
        input.classList.add('border-red-500');
    }

    function markCheckboxInvalid(cb) {
        if (cb && cb.parentElement) {
            cb.parentElement.style.outline = '2px solid #f53535';
            cb.parentElement.style.outlineOffset = '2px';
            cb.parentElement.style.borderRadius = '4px';
        }
    }

    /**
     * Универсальная валидация: имя + телефон + согласие
     */
    function validateForm(fields) {
        var ok = true;
        if (fields.nameInput) {
            if (!fields.nameInput.value.trim()) {
                markInvalid(fields.nameInput);
                ok = false;
            }
        }
        if (fields.telInput) {
            var digits = fields.telInput.value.replace(/\D/g, '');
            if (digits.length < 10) {
                markInvalid(fields.telInput);
                ok = false;
            }
        }
        if (fields.checkbox && !fields.checkbox.checked) {
            markCheckboxInvalid(fields.checkbox);
            ok = false;
        }
        return ok;
    }

    function showSuccessModal(currentModal, successSelector) {
        var success = document.querySelector(successSelector);
        var darken = document.querySelector('.darken');
        if (currentModal) currentModal.classList.remove('modal--active');
        if (!success) {
            // Нет кастомной success-модалки — показываем дефолтную
            success = document.querySelector('.modal--callme-back-success');
        }
        setTimeout(function () {
            if (success) success.classList.add('modal--active');
            if (darken && (!currentModal || !success)) {
                darken.classList.add('darken--active');
            }
        }, 300);
    }

    function setSubmitting(btn, isSubmitting) {
        if (!btn) return;
        if (isSubmitting) {
            btn.dataset.s140Original = btn.dataset.s140Original || btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = 'Отправка…';
        } else {
            btn.disabled = false;
            if (btn.dataset.s140Original) btn.innerHTML = btn.dataset.s140Original;
        }
    }

    function postForm(payload) {
        var body = new URLSearchParams();
        body.append('action', 's140_submit_form');
        if (NONCE) body.append('_ajax_nonce', NONCE);
        Object.keys(payload).forEach(function (k) {
            body.append(k, payload[k] == null ? '' : String(payload[k]));
        });
        return fetch(AJAX_URL, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: body
        }).then(function (r) { return r.json().catch(function () { return { success: false }; }); });
    }

    // ===== Обработчик модальных форм (.modal-content-form) =====
    document.addEventListener('submit', function (e) {
        var form = e.target.closest && e.target.closest('.modal-content-form');
        if (!form) return;
        e.preventDefault();

        var nameInput = form.querySelector('input[name="modal-content-form-name"]');
        var telInput  = form.querySelector('input[name="modal-content-form-tel"]');
        var checkbox  = form.querySelector('.input-checkbox__input');
        var btn       = form.querySelector('button[type="submit"], .modal-content-form__btn');

        clearErrors(form);
        if (!validateForm({ nameInput: nameInput, telInput: telInput, checkbox: checkbox })) {
            var firstInvalid = form.querySelector('.border-red-500');
            if (firstInvalid) firstInvalid.focus();
            return;
        }

        var modal = form.closest('.modal');
        var ctx = detectModalContext(form);
        var product = readProductFromModal(modal);

        setSubmitting(btn, true);

        postForm({
            form_type: ctx.type,
            name: nameInput ? nameInput.value : '',
            phone: telInput ? telInput.value : '',
            consent: checkbox && checkbox.checked ? 1 : 0,
            page_url: window.location.href,
            product: product
        }).then(function (resp) {
            setSubmitting(btn, false);
            if (resp && resp.success) {
                showSuccessModal(modal, ctx.successSelector);
                form.reset();
            } else {
                // Серверная ошибка — подсвечиваем поля, если пришли
                if (resp && resp.data && resp.data.fields) {
                    if (resp.data.fields.name)    markInvalid(nameInput);
                    if (resp.data.fields.phone)   markInvalid(telInput);
                    if (resp.data.fields.consent) markCheckboxInvalid(checkbox);
                } else {
                    alert('Не удалось отправить заявку. Попробуйте позвонить нам по номеру 8 800 201-80-04.');
                }
            }
        }).catch(function () {
            setSubmitting(btn, false);
            alert('Ошибка соединения. Попробуйте ещё раз или позвоните по номеру 8 800 201-80-04.');
        });
    });

    // ===== Обработчик «нижних» форм на статических страницах (.other-questions-form) =====
    document.addEventListener('submit', function (e) {
        var form = e.target.closest && e.target.closest('.other-questions-form');
        if (!form) return;
        e.preventDefault();

        var inputs    = form.querySelectorAll('.other-questions-form__input');
        var nameInput = inputs[0] || null;
        var telInput  = form.querySelector('.other-questions-form__input.phone_mask') || inputs[1] || null;
        var checkbox  = form.querySelector('.other-questions-form__checkbox, .input-checkbox__input');
        var btn       = form.querySelector('.other-questions-form__btn, button[type="submit"]');

        clearErrors(form);
        if (!validateForm({ nameInput: nameInput, telInput: telInput, checkbox: checkbox })) {
            var firstInvalid = form.querySelector('.border-red-500');
            if (firstInvalid) firstInvalid.focus();
            return;
        }

        setSubmitting(btn, true);

        postForm({
            form_type: 'other-questions',
            name: nameInput ? nameInput.value : '',
            phone: telInput ? telInput.value : '',
            consent: checkbox && checkbox.checked ? 1 : 0,
            page_url: window.location.href
        }).then(function (resp) {
            setSubmitting(btn, false);
            if (resp && resp.success) {
                form.reset();
                // Показываем универсальный success-попап
                var darken = document.querySelector('.darken');
                var success = document.querySelector('.modal--callme-back-success');
                if (success) success.classList.add('modal--active');
                if (darken) darken.classList.add('darken--active');
                // Иначе — простое сообщение
                if (!success) {
                    alert('Спасибо! Ваша заявка принята, менеджер свяжется с вами.');
                }
            } else {
                if (resp && resp.data && resp.data.fields) {
                    if (resp.data.fields.name)    markInvalid(nameInput);
                    if (resp.data.fields.phone)   markInvalid(telInput);
                    if (resp.data.fields.consent) markCheckboxInvalid(checkbox);
                } else {
                    alert('Не удалось отправить заявку. Попробуйте позвонить нам по номеру 8 800 201-80-04.');
                }
            }
        }).catch(function () {
            setSubmitting(btn, false);
            alert('Ошибка соединения. Попробуйте ещё раз или позвоните по номеру 8 800 201-80-04.');
        });
    });

    // ===== Подписка в футере (s140-subscribe-form) =====
    document.addEventListener('submit', function (e) {
        var form = e.target.closest && e.target.closest('.s140-subscribe-form');
        if (!form) return;
        e.preventDefault();
        var input = form.querySelector('input[type="email"], .s140-subscribe-input');
        var btn   = form.querySelector('button[type="submit"]');
        var msg   = form.querySelector('.s140-subscribe-msg');
        var email = input ? (input.value || '').trim() : '';
        if (msg) msg.textContent = '';
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email)) {
            if (msg) { msg.textContent = 'Укажите корректный e-mail'; msg.style.color = '#ffd0d0'; }
            if (input) markInvalid(input);
            return;
        }
        setSubmitting(btn, true);
        postForm({
            form_type: 'subscribe',
            name: 'Подписка из футера',
            phone: '0000000000',
            email: email,
            consent: 1,
            page_url: window.location.href
        }).then(function (resp) {
            setSubmitting(btn, false);
            if (resp && resp.success) {
                if (msg) { msg.textContent = 'Спасибо! Вы подписаны на рассылку.'; msg.style.color = '#fff'; }
                form.reset();
            } else {
                if (msg) { msg.textContent = 'Не удалось подписаться. Попробуйте позже.'; msg.style.color = '#ffd0d0'; }
            }
        }).catch(function () {
            setSubmitting(btn, false);
            if (msg) { msg.textContent = 'Ошибка соединения. Попробуйте позже.'; msg.style.color = '#ffd0d0'; }
        });
    });

    // ===== Снятие подсветки при вводе =====
    document.addEventListener('input', function (e) {
        var input = e.target;
        if (input && input.classList && input.classList.contains('border-red-500')) {
            input.style.borderColor = '';
            input.classList.remove('border-red-500');
        }
    });

    document.addEventListener('change', function (e) {
        var cb = e.target;
        if (cb && cb.classList && cb.classList.contains('input-checkbox__input') && cb.checked) {
            if (cb.parentElement) cb.parentElement.style.outline = '';
        }
        if (cb && cb.classList && cb.classList.contains('other-questions-form__checkbox') && cb.checked) {
            if (cb.parentElement) cb.parentElement.style.outline = '';
        }
    });
})();
