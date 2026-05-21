<?php
defined('ABSPATH') || exit;

$checkout_nonce = wp_create_nonce('woocommerce-process_checkout');
$checkout_ajax_url = esc_url(home_url('/?wc-ajax=checkout'));

$cart = WC()->cart;
$checkout = WC()->checkout;
$ajax_url = WC_AJAX::get_endpoint('checkout');
$ajax_nonce = wp_create_nonce('woocommerce-process_checkout');
$cart_nonce = wp_create_nonce('custom-cart-nonce');
$admin_ajax = admin_url('admin-ajax.php');

$billing_country = WC()->customer->get_billing_country();
if (!$billing_country) {
    $loc = wc_get_base_location();
    $billing_country = isset($loc['country']) ? $loc['country'] : '';
}

$email_default = $checkout->get_value('billing_email');
if (!$email_default) {
    $email_default = get_option('admin_email');
}

$discount_total = 0;
if ($cart && $cart->get_cart()) {
    foreach ($cart->get_cart() as $ci) {
        $p = $ci['data'];
        if (!$p || !$p->exists()) {
            continue;
        }
        $q = (int) $ci['quantity'];
        $regular_unit = (float) $p->get_regular_price();
        $line_total = (float) $ci['line_total'];

        $discount_total += max(0, $regular_unit * $q - $line_total);
    }
}
?>

<link rel="stylesheet" href="<?= esc_url(get_template_directory_uri()); ?>/css/cart.min.css">
<script src="<?= esc_url(get_template_directory_uri()); ?>/js/pages/cart.js" defer></script>

<section class="cart">
    <div class="container">
        <div class="cart__inner">
            <div class="cart-left">
                <h2>Оформление заказа</h2>
                <div class="cart-left-form__wrapper">
                    <h5 class="cart-left__title">Контактные данные</h5>
                    <form class="cart-left-form" action="#" id="fake-contact-form">
                        <div class="cart-left-form__row">
                            <div class="cart-left-form__field">
                                <label class="cart-left-form__field-title" for="name">Имя</label>
                                <input class="input cart-left-form__field-input" type="text" id="name"
                                    autocomplete="tel" placeholder="Имя"
                                    value="<?php echo esc_attr($checkout->get_value('billing_first_name')); ?>">
                            </div>
                            <div class="cart-left-form__field">
                                <label class="cart-left-form__field-title" for="phone">Телефон</label>
                                <input class="input cart-left-form__field-input phone_mask" type="tel" id="phone"
                                    autocomplete="tel" placeholder="+7 (999) 999 99 99"
                                    value="<?php echo esc_attr($checkout->get_value('billing_phone')); ?>">
                            </div>
                        </div>
                        <div class="cart-left-form__field">
                            <label class="cart-left-form__field-title" for="address">Адрес</label>
                            <input class="input cart-left-form__field-input" type="text" name="address" id="address"
                                placeholder="г Москва"
                                value="<?php echo esc_attr($checkout->get_value('billing_address_1')); ?>">
                        </div>
                    </form>
                </div>
                <div class="cart-left-receipt">
                    <h5 class="cart-left__title">Способ получения</h5>
                    <div class="cart-left-receipt__tabs">
                        <button class="button cart-left-receipt__tab cart-left-receipt__tab--active"
                            data-tab="receipt-pickup" type="button">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M5.25 2.87657L14.25 7.51204M1.05 5.33382L9.75 9.83428M9.75 9.83428L18.45 5.33382M9.75 9.83428V18.8352M18.75 6.23391C18.7496 5.91823 18.6571 5.60818 18.4815 5.33487C18.306 5.06156 18.0537 4.8346 17.75 4.67675L10.75 1.07638C10.446 0.918385 10.1011 0.835205 9.75 0.835205C9.39893 0.835205 9.05404 0.918385 8.75 1.07638L1.75 4.67675C1.44626 4.8346 1.19398 5.06156 1.01846 5.33487C0.842943 5.60818 0.75036 5.91823 0.75 6.23391V13.4347C0.75036 13.7503 0.842943 14.0604 1.01846 14.3337C1.19398 14.607 1.44626 14.834 1.75 14.9918L8.75 18.5922C9.05404 18.7502 9.39893 18.8334 9.75 18.8334C10.1011 18.8334 10.446 18.7502 10.75 18.5922L17.75 14.9918C18.0537 14.834 18.306 14.607 18.4815 14.3337C18.6571 14.0604 18.7496 13.7503 18.75 13.4347V6.23391Z"
                                    stroke="white" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                            Самовывоз
                        </button>
                        <button class="button cart-left-receipt__tab" data-tab="receipt-delivery" type="button">
                            <svg width="20" height="16" viewBox="0 0 20 16" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12.0002 13.25V2.75C12.0002 2.28587 11.8106 1.84075 11.473 1.51256C11.1354 1.18437 10.6776 1 10.2002 1H3.0002C2.52281 1 2.06497 1.18437 1.7274 1.51256C1.38984 1.84075 1.2002 2.28587 1.2002 2.75V12.375C1.2002 12.6071 1.29502 12.8296 1.4638 12.9937C1.63258 13.1578 1.8615 13.25 2.1002 13.25H3.9002M3.9002 13.25C3.9002 14.2165 4.70608 15 5.7002 15C6.69431 15 7.5002 14.2165 7.5002 13.25M3.9002 13.25C3.9002 12.2835 4.70608 11.5 5.7002 11.5C6.69431 11.5 7.5002 12.2835 7.5002 13.25M12.9002 13.25H7.5002M12.9002 13.25C12.9002 14.2165 13.7061 15 14.7002 15C15.6943 15 16.5002 14.2165 16.5002 13.25M12.9002 13.25C12.9002 12.2835 13.7061 11.5 14.7002 11.5C15.6943 11.5 16.5002 12.2835 16.5002 13.25M16.5002 13.25H18.3002C18.5389 13.25 18.7678 13.1578 18.9366 12.9937C19.1054 12.8296 19.2002 12.6071 19.2002 12.375V9.18125C19.1998 8.98268 19.13 8.79013 19.0022 8.63525L15.8702 4.829C15.786 4.72652 15.6792 4.64375 15.5577 4.5868C15.4362 4.52985 15.3031 4.50019 15.1682 4.5H12.0002"
                                    stroke="#258FFB" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                            Доставка
                        </button>
                    </div>
                    <div class="cart-left-receipt__address cart-left-receipt__address--active"
                        data-content="receipt-pickup">
                        <h6 class="cart-left-receipt__title">Выдача заказов</h6>
                        <div class="cart-left-receipt__address-inner">
                            <div class="cart-left-receipt__address-img">
                                <img src="<?= esc_url(get_template_directory_uri()); ?>/images/content/others/pickup-address.png"
                                    alt="Адрес самовывоза на карте">
                            </div>
                            <div class="cart-left-receipt__address-info">
                                <p class="cart-left-receipt__address-info__subtitle">Адрес склада</p>
                                <p class="cart-left-receipt__address-info__title">Московская область Мытищенский район,
                                    д. Сухарево, д. 140Г</p>
                                <div class="cart-left-receipt__address-info__row">
                                    <a href="#">
                                        <svg width="18" height="18">
                                            <use
                                                xlink:href="<?= esc_url(get_template_directory_uri()); ?>/images/sprite.svg#phone-gray">
                                            </use>
                                        </svg>
                                        +7 (800) 201-80-04
                                    </a>
                                    <a href="#">
                                        <svg width="18" height="18">
                                            <use
                                                xlink:href="<?= esc_url(get_template_directory_uri()); ?>/images/sprite.svg#clock-gray">
                                            </use>
                                        </svg>
                                        Ежедневно 9:00-21:00
                                    </a>
                                </div>
                                <div class="cart-left-receipt__address-info__expiry-date">
                                    <svg width="20" height="20">
                                        <use
                                            xlink:href="<?= esc_url(get_template_directory_uri()); ?>/images/sprite.svg#timer">
                                        </use>
                                    </svg>
                                    <p>
                                        <span>Срок хранения: 2 дня,</span>
                                        не считая день оформления заказа
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="cart-left-receipt__address" data-content="receipt-delivery">
                        <h6 class="cart-left-receipt__title">Доставка</h6>
                        <p class="cart-left-receipt__text">Менеджер уточнит способ и стоимость доставки.</p>
                    </div>
                </div>
                <div class="cart-left-payment">
                    <h4 class="cart-left-payment__title">Способ оплаты</h4>
                    <form class="cart-left-payment__form" action="#" id="fake-payment-form">
                        <div class="cart-left-payment__row">
                            <label class="cart-left-payment__method" for="payment-cash">
                                <input id="payment-cash" class="input-radio__input" type="radio" name="payment-method"
                                    value="cash" checked>
                                <div class="input-radio__circle"></div>
                                <p class="input-radio__name">Наличными</p>
                            </label>
                            <label class="cart-left-payment__method" for="payment-non-cash">
                                <input id="payment-non-cash" class="input-radio__input" type="radio"
                                    name="payment-method" value="non-cash">
                                <div class="input-radio__circle"></div>
                                <p class="input-radio__name">Безналичная оплата</p>
                            </label>
                        </div>
                        <button class="button button--fill cart-left-payment__btn" type="button">Оформить заказ</button>
                        <label class="cart-left-payment__privacy-policy" for="privacy-policy-checkbox">
                            <input id="privacy-policy-checkbox"
                                class="input-checkbox__input cart-left-payment__checkbox" type="checkbox"
                                value="privacy-policy">
                            Я ознакомился и согласен с <a href="/privacy-policy/" target="_blank" rel="noopener">политикой конфиденциальности </a>в отношении хранения
                            и обработки персональных данных
                        </label>
                    </form>
                </div>
            </div>
            <div class="cart-right">
                <div class="cart-right-header">
                    <h5 class="cart-right__title">Корзина</h5>
                    <div class="cart-right-header__arrow">
                        <svg width="6" height="12">
                            <use xlink:href="<?= esc_url(get_template_directory_uri()); ?>/images/sprite.svg#arrow">
                            </use>
                        </svg>
                    </div>
                </div>
                <div class="cart-right__products">
                    <?php foreach ($cart->get_cart() as $cart_item_key => $cart_item):
                        $product = $cart_item['data'];
                        if (!$product || !$product->exists()) {
                            continue;
                        }
                        $quantity = (int) $cart_item['quantity'];
                        $thumbnail = $product->get_image('woocommerce_thumbnail');
                        $name = $product->get_name();
                        $regular_unit = (float) $product->get_regular_price();
                        $line_total = (float) $cart_item['line_total'];
                        $regular_total = $regular_unit * $quantity;
                        $has_discount = $regular_total > $line_total && $regular_total > 0;
                        $current_txt = wp_strip_all_tags(wc_price($line_total));
                        $regular_txt = $has_discount ? wp_strip_all_tags(wc_price($regular_total)) : '';
                        ?>
                        <div class="cart-product" data-cart-key="<?php echo esc_attr($cart_item_key); ?>">
                            <div class="cart-product__img">
                                <?php echo $thumbnail; ?>
                            </div>
                            <div class="cart-product__info">
                                <div class="cart-product__info-row">
                                    <p class="cart-product__info-title"><?php echo esc_html($name); ?></p>
                                    <div class="cart-product__info-price__wrapper">
                                        <p
                                            class="cart-product__info-price<?php echo $has_discount ? ' cart-product__info-price--discount' : ''; ?>">
                                            <?php echo $current_txt; ?>
                                        </p>
                                        <p class="cart-product__info-old-price">
                                            <?php echo $regular_txt ? $regular_txt : ''; ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="cart-product__actions">
                                    <div class="cart-product__counter">
                                        <div id="cart-product__counter--prev"
                                            class="cart-product__counter-btn<?php echo $quantity <= 1 ? ' cart-product__counter-btn--disabled' : ''; ?>">
                                            <svg width="12" height="2" viewBox="0 0 12 2" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1.33301 1H10.6663" stroke="#B6C1DD" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"></path>
                                            </svg>
                                        </div>
                                        <p class="cart-product__counter-value"><?php echo (int) $quantity; ?></p>
                                        <div id="cart-product__counter--next" class="cart-product__counter-btn">
                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1.33301 6.00001H10.6663M5.99967 1.33334V10.6667" stroke="#031343"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="cart-product__remove">
                                        <svg width="14" height="14" viewBox="0 0 12 14" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M0.856445 3.57142H11.1422M9.9993 3.57142V11.5714C9.9993 12.1428 9.42787 12.7143 8.85644 12.7143H3.14216C2.57073 12.7143 1.9993 12.1428 1.9993 11.5714V3.57142M3.71359 3.57142V2.42856C3.71359 1.85713 4.28502 1.28571 4.85645 1.28571H7.14216C7.71359 1.28571 8.28502 1.85713 8.28502 2.42856В3.57142M4.85645 6.42856В9.85713M7.14216 6.42856В9.85713"
                                                stroke="#B6C1DD" stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="product-item__added-to-cart">
                                <svg class="product-item__added-to-cart__cross" width="16" height="16" viewBox="0 0 16 16"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20 -4L-4 20M-4 -4L20 20" stroke="#031343" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                                <div class="product-item__added-to-cart__info">
                                    <p>Товар <span><?php echo esc_html($name); ?></span> добавлен в корзину</p>
                                    <a href="/cart.html">Перейти в корзину</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="cart-right-details">
                    <div class="cart-right-details__item">
                        <p class="cart-right-details__item-title">
                            <?php
                            $count = (int) $cart->get_cart_contents_count();
                            $mod10 = $count % 10;
                            $mod100 = $count % 100;
                            if ($mod10 === 1 && $mod100 !== 11) {
                                $word = 'товар';
                            } elseif ($mod10 >= 2 && $mod10 <= 4 && ($mod100 < 10 || $mod100 >= 20)) {
                                $word = 'товара';
                            } else {
                                $word = 'товаров';
                            }
                            echo $count . ' ' . $word . ' на сумму';
                            ?>
                        </p>
                        <div class="cart-right-details__item-dashed"></div>
                        <span class="cart-right-details__item-price">
                            <?php echo wp_strip_all_tags(wc_price($cart->get_subtotal())); ?>
                        </span>
                    </div>
                    <?php if ($discount_total > 0): ?>
                        <div class="cart-right-details__item cart-right-details__item--discount">
                            <p class="cart-right-details__item-title">Скидка</p>
                            <div class="cart-right-details__item-dashed"></div>
                            <span class="cart-right-details__item-price">
                                <?php echo wp_strip_all_tags(wc_price($discount_total)); ?>
                            </span>
                        </div>
                    <?php else: ?>
                        <div class="cart-right-details__item cart-right-details__item--discount" style="display:none;">
                            <p class="cart-right-details__item-title">Скидка</p>
                            <div class="cart-right-details__item-dashed"></div>
                            <span class="cart-right-details__item-price"></span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="cart-right__separator"></div>
                <div class="cart-right-total">
                    <p class="cart-right-total__title">Итого к оплате:</p>
                    <p class="cart-right-total__price">
                        <?php echo wp_strip_all_tags(wc_price($cart->get_total('edit'))); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal modal--cart-success" style="display:none;">
    <button type="button" class="modal__close js-cart-success-close" aria-label="Закрыть"
        style="position:absolute;top:12px;right:12px;width:36px;height:36px;border:0;background:transparent;color:#fff;font-size:28px;line-height:1;cursor:pointer;z-index:2;">×</button>
    <div class="modal-content modal-content--success">
        <div class="modal-content__icon mb-4">
            <img src="<?= get_template_directory_uri(); ?>/images/content/others/fire-emoji.png" alt="Fire emoji">
            <h2 class="modal__subtitle">Спасибо за заказ!</h2>
        </div>
        <h5 class="modal__title font-medium mb-4">
            Ваш заказ <span id="order-number" style="text-decoration: underline;">№—</span> успешно оформлен
        </h5>
        <p class="modal-content__text mb-4">
            В ближайшее время с вами свяжется менеджер для уточнения деталей заказа.
        </p>
        <a href="/shop/" class="button button--dark">Вернуться в каталог</a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var btn = document.querySelector('.cart-left-payment__btn');
        var successModal = document.querySelector('.modal--cart-success');
        // Переносим модалку успеха в <body>, чтобы она не
        // наследовала containing block от родительских контейнеров
        // (.container с content-visibility:auto и т.п.) и корректно
        // центрировалась через position:fixed относительно вьюпорта.
        if (successModal && successModal.parentElement !== document.body) {
            document.body.appendChild(successModal);
        }
        var checkbox = document.getElementById('privacy-policy-checkbox');
        // URL каталога, куда возвращаем пользователя после успешного оформления.
        var catalogUrl = '<?php echo esc_js( home_url( '/shop/' ) ); ?>';
        // Флаг — заказ оформлен; любые close-действия теперь перенаправляют в каталог.
        var orderPlaced = false;
        var ajaxUrl = '<?php echo esc_js($ajax_url); ?>';
        var ajaxNonce = '<?php echo esc_js($ajax_nonce); ?>';
        var cartAjaxUrl = '<?php echo esc_js($admin_ajax); ?>';
        var cartNonce = '<?php echo esc_js($cart_nonce); ?>';
        var billingCountry = '<?php echo esc_js($billing_country); ?>';
        var billingEmail = '<?php echo esc_js($email_default); ?>';

        // ---------- NEW: помощник, который превращает "&nbsp;&#8381;" в нормальный текст "  ₽"
        function decodeHtmlEntities(str) {
            if (!str) return '';
            var txt = document.createElement('textarea');
            txt.innerHTML = str;
            return txt.value;
        }

        function clearErrors() {
            document.querySelectorAll('.cart-left-form__field, .cart-left-payment__privacy-policy').forEach(function (wrap) {
                wrap.classList.remove('has-error');
                var err = wrap.querySelector('.cart-field-error');
                if (err) err.remove();
            });
        }

        function addError(wrapper, message) {
            if (!wrapper) return;
            wrapper.classList.add('has-error');
            var err = wrapper.querySelector('.cart-field-error');
            if (!err) {
                err = document.createElement('div');
                err.className = 'cart-field-error';
                err.textContent = message;
                wrapper.appendChild(err);
            } else {
                err.textContent = message;
            }
        }

        function trimVal(id) {
            var el = document.getElementById(id);
            return el ? el.value.trim() : '';
        }

        function getWordTovar(n) {
            n = Math.abs(n) || 0;
            var mod10 = n % 10;
            var mod100 = n % 100;
            if (mod10 === 1 && mod100 !== 11) return 'товар';
            if (mod10 >= 2 && mod10 <= 4 && (mod100 < 10 || mod100 >= 20)) return 'товара';
            return 'товаров';
        }

        // ---------- правка: декодируем сущности в суммах
        function updateSummary(data) {
            var titleEl = document.querySelector('.cart-right-details__item-title');
            var sumPriceEl = document.querySelector('.cart-right-details__item:first-child .cart-right-details__item-price');
            var discountBlock = document.querySelector('.cart-right-details__item--discount');
            var discountPriceEl = discountBlock ? discountBlock.querySelector('.cart-right-details__item-price') : null;
            var totalEl = document.querySelector('.cart-right-total__price');

            if (titleEl && typeof data.cart_count !== 'undefined') {
                var w = getWordTovar(data.cart_count);
                titleEl.textContent = data.cart_count + ' ' + w + ' на сумму';
            }
            if (sumPriceEl && data.subtotal) {
                sumPriceEl.textContent = decodeHtmlEntities(data.subtotal);
            }
            if (discountBlock && discountPriceEl) {
                if (data.discount_total && parseFloat(data.discount_total) > 0) {
                    discountBlock.style.display = '';
                    discountPriceEl.textContent = decodeHtmlEntities(data.discount_formatted || data.discount_total);
                } else {
                    discountBlock.style.display = 'none';
                    discountPriceEl.textContent = '';
                }
            }
            if (totalEl && data.total) {
                totalEl.textContent = decodeHtmlEntities(data.total);
            }
        }

        function updateCartItem(cartKey, qty, productEl) {
            if (!cartKey) return;
            var params = new URLSearchParams();
            params.append('action', 'update_cart_item_custom');
            params.append('nonce', cartNonce);
            params.append('cart_item_key', cartKey);
            params.append('quantity', qty);

            fetch(cartAjaxUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                body: params.toString()
            }).then(function (res) {
                return res.json();
            }).then(function (data) {
                if (!data || !data.success) return;
                var d = data.data || {};

                if (productEl) {
                    if (d.quantity === 0 || d.cart_count === 0 || qty === 0) {
                        // последний товар: убираем элемент, summary обновим ниже
                        productEl.remove();
                    } else {
                        var qtyEl = productEl.querySelector('.cart-product__counter-value');
                        var priceEl = productEl.querySelector('.cart-product__info-price');
                        var oldEl = productEl.querySelector('.cart-product__info-old-price');
                        var prevBtn = productEl.querySelector('#cart-product__counter--prev');

                        if (qtyEl && typeof d.quantity !== 'undefined') {
                            qtyEl.textContent = d.quantity;
                        }
                        if (priceEl && d.line_total) {
                            // ---------- правка: декодируем сущности
                            priceEl.textContent = decodeHtmlEntities(d.line_total);
                            if (d.has_discount) {
                                priceEl.classList.add('cart-product__info-price--discount');
                            } else {
                                priceEl.classList.remove('cart-product__info-price--discount');
                            }
                        }
                        if (oldEl) {
                            if (d.has_discount && d.line_regular) {
                                oldEl.textContent = decodeHtmlEntities(d.line_regular);
                            } else {
                                oldEl.textContent = '';
                            }
                        }
                        if (prevBtn && typeof d.quantity !== 'undefined') {
                            if (d.quantity <= 1) {
                                prevBtn.classList.add('cart-product__counter-btn--disabled');
                            } else {
                                prevBtn.classList.remove('cart-product__counter-btn--disabled');
                            }
                        }
                    }
                }

                updateSummary(d);

                // ---------- NEW: если корзина стала пустой — перезагружаем страницу
                if (typeof d.cart_count !== 'undefined' && parseInt(d.cart_count, 10) === 0) {
                    window.location.reload();
                }
            }).catch(function () { });
        }

        document.querySelectorAll('.cart-product').forEach(function (item) {
            var cartKey = item.getAttribute('data-cart-key');
            var prevBtn = item.querySelector('#cart-product__counter--prev');
            var nextBtn = item.querySelector('#cart-product__counter--next');
            var removeBtn = item.querySelector('.cart-product__remove');
            var qtyEl = item.querySelector('.cart-product__counter-value');

            if (nextBtn && qtyEl) {
                nextBtn.addEventListener('click', function () {
                    var q = parseInt(qtyEl.textContent, 10) || 1;
                    q++;
                    updateCartItem(cartKey, q, item);
                });
            }
            if (prevBtn && qtyEl) {
                prevBtn.addEventListener('click', function () {
                    if (prevBtn.classList.contains('cart-product__counter-btn--disabled')) return;
                    var q = parseInt(qtyEl.textContent, 10) || 1;
                    if (q <= 1) return;
                    q--;
                    updateCartItem(cartKey, q, item);
                });
            }
            if (removeBtn) {
                removeBtn.addEventListener('click', function () {
                    updateCartItem(cartKey, 0, item);
                });
            }
        });

        if (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                clearErrors();

                var name = trimVal('name');
                var phone = trimVal('phone');
                var address = trimVal('address');

                var hasError = false;

                var nameWrap = document.getElementById('name') ? document.getElementById('name').closest('.cart-left-form__field') : null;
                var phoneWrap = document.getElementById('phone') ? document.getElementById('phone').closest('.cart-left-form__field') : null;
                var addrWrap = document.getElementById('address') ? document.getElementById('address').closest('.cart-left-form__field') : null;
                var policyWrap = document.querySelector('.cart-left-payment__privacy-policy');

                if (!name) {
                    addError(nameWrap, 'Заполните поле «Имя»');
                    hasError = true;
                }
                if (!phone) {
                    addError(phoneWrap, 'Заполните поле «Телефон»');
                    hasError = true;
                }
                if (!address) {
                    addError(addrWrap, 'Заполните поле «Адрес»');
                    hasError = true;
                }
                if (!checkbox.checked) {
                    addError(policyWrap, 'Необходимо согласиться с политикой конфиденциальности');
                    hasError = true;
                }

                if (hasError) {
                    var firstErr = document.querySelector('.has-error');
                    if (firstErr && firstErr.scrollIntoView) {
                        firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    return;
                }

                var methodInput = document.querySelector('input[name="payment-method"]:checked');
                var method = methodInput ? methodInput.value : 'cash';
                var gw = method === 'non-cash' ? 'bacs' : 'cod';

                var params = new URLSearchParams();
                params.append('security', ajaxNonce);
                params.append('woocommerce-process-checkout-nonce', ajaxNonce);
                params.append('billing_first_name', name);
                params.append('billing_phone', phone);
                params.append('billing_address_1', address);
                params.append('billing_state', address);
                params.append('billing_city', address);
                if (billingCountry) {
                    params.append('billing_country', billingCountry);
                }
                if (billingEmail) {
                    params.append('billing_email', billingEmail);
                }
                params.append('payment_method', gw);
                params.append('ship_to_different_address', '0');
                params.append('terms', 'on');
                params.append('woocommerce_checkout_place_order', '1');

                fetch(ajaxUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                    body: params.toString()
                }).then(function (res) {
                    return res.json();
                }).then(function (data) {
                    var policyWrap = document.querySelector('.cart-left-payment__privacy-policy');
                    if (data && data.result === 'success' && data.redirect) {
                        var orderId = null;
                        var m = data.redirect.match(/order-received\/(\d+)/);
                        if (m) orderId = m[1];

                        var numEl = document.getElementById('order-number');
                        if (numEl && orderId) {
                            numEl.textContent = '№' + orderId;
                        }

                        if (successModal) {
                            successModal.style.display = 'block';
                            successModal.classList.add('modal--active');
                            var darkenEl = document.querySelector('.darken');
                            if (darkenEl) {
                                darkenEl.classList.add('darken--active');
                            }
                            orderPlaced = true;
                            // Авто-редирект в каталог: страница перезагружается
                            // и пользователь возвращается в /shop/. Корзина уже
                            // очищена WC после успешного оформления.
                            setTimeout(function () {
                                window.location.href = catalogUrl;
                            }, 5000);
                        }
                    } else {
                        var msg = 'Мы не смогли обработать ваш заказ. Пожалуйста, попробуйте ещё раз.';
                        if (data && data.messages) {
                            var d = document.createElement('div');
                            d.innerHTML = data.messages;
                            var t = d.textContent || d.innerText || '';
                            t = t.replace(/\s+/g, ' ').trim();
                            if (t) msg = t;
                        }
                        addError(policyWrap, msg);
                    }
                }).catch(function () {
                    var policyWrap = document.querySelector('.cart-left-payment__privacy-policy');
                    addError(policyWrap, 'Техническая ошибка. Обновите страницу и попробуйте ещё раз.');
                });
            });
        }

        // П-3: закрытие success-модалки.
        // Если заказ уже оформлен — переходим в каталог (полная перезагрузка),
        // иначе просто снимаем модалку и тёмную заливку.
        function closeCartSuccess() {
            if (orderPlaced) {
                window.location.href = catalogUrl;
                return;
            }
            if (successModal) {
                successModal.classList.remove('modal--active');
                successModal.style.display = 'none';
            }
            var dk = document.querySelector('.darken');
            if (dk) dk.classList.remove('darken--active');
        }
        var closeBtn = document.querySelector('.js-cart-success-close');
        if (closeBtn) closeBtn.addEventListener('click', closeCartSuccess);
        var darkenClickEl = document.querySelector('.darken');
        if (darkenClickEl) {
            darkenClickEl.addEventListener('click', function () {
                if (successModal && successModal.classList.contains('modal--active')) {
                    closeCartSuccess();
                }
            });
        }
        document.addEventListener('keydown', function (ev) {
            if (ev.key === 'Escape' && successModal && successModal.classList.contains('modal--active')) {
                closeCartSuccess();
            }
        });
    });
</script>
<style>
    .page-title {
        display: none
    }

    .cart-product {
        display: flex;
        align-items: center;
        width: 100%;
    }

    .cart-product__img {
        flex: 0 0 80px;
        margin-right: 16px;
    }

    .cart-product__info {
        flex: 1;
        min-width: 0;
    }

    .cart-product__actions {
        flex: 0 0 auto;
        margin-left: 12px;
        white-space: nowrap;
    }

    .cart-product__info-row {
        width: 100%
    }
</style>