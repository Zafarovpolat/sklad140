<?php
defined('ABSPATH') || exit;
global $product;

$regular_raw = $product->get_regular_price();
$sale_raw = $product->get_sale_price();
$price_raw = $product->get_price();
// Devin: cast to float — number_format() throws TypeError on PHP 8 when price meta is empty string
$regular = is_numeric($regular_raw) ? (float) $regular_raw : 0.0;
$sale    = is_numeric($sale_raw)    ? (float) $sale_raw    : 0.0;
$price   = is_numeric($price_raw)   ? (float) $price_raw   : 0.0;
$has_price = ($regular > 0) || ($sale > 0);
$cart_url = wc_get_cart_url();
$product_name = $product->get_name();

// Товар уже в корзине?
$in_cart = false;
foreach (WC()->cart->get_cart() as $cart_item) {
    if ($cart_item['product_id'] == $product->get_id()) {
        $in_cart = true;
        break;
    }
}
?>
<div class="product-price">

    <div class="product-price-top">
        <p class="product-price-top__subtitle">Цена, рублей</p>

        <div class="flex items-center gap-2 mb-2">

            <?php if ($has_price && $sale > 0 && $sale < $regular): ?>
                <h5 class="product-item__price font-semibold text-main-22/[120%] text-brand-red">
                    <?= number_format($sale, 0, '', ' ') ?> ₽
                </h5>

                <p class="font-medium text-sm/[120%] text-brand-blue-200 line-through decoration-brand-red">
                    <?= number_format($regular, 0, '', ' ') ?> ₽
                </p>

            <?php elseif ($has_price): ?>
                <h5 class="product-item__price font-semibold text-main-22/[120%]">
                    <?= number_format($regular > 0 ? $regular : $sale, 0, '', ' ') ?> ₽
                </h5>
            <?php else: ?>
                <h5 class="product-item__price font-semibold text-main-22/[120%] text-brand-blue-200">
                    Цена по запросу
                </h5>
            <?php endif; ?>

        </div>


    </div>

    <div class="product-price__btns">

        <?php if ( $product->is_in_stock() ) : ?>

        <?php echo do_shortcode('[viewBuyButton]'); ?>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const button = document.querySelector('.single_add_to_cart_button');
                if (button) {
                    button.classList.add('button--fill');
                }
            });
        </script>

        <!-- AJAX КНОПКА В КОРЗИНУ -->
        <button class="button add-to-cart-ajax <?= $in_cart ? 'in-cart' : '' ?>"
            data-product_id="<?= $product->get_id(); ?>">

            <svg width="18" height="18">
                <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#cart--filled-black"></use>
            </svg>

            <span class="btn-text">
                <?= $in_cart ? 'Перейти в корзину' : 'В корзину'; ?>
            </span>

        </button>

        <script>
            jQuery(function ($) {

                $(document).on("click", ".add-to-cart-ajax", function (e) {
                    e.preventDefault();

                    const btn = $(this);
                    const product_id = btn.data("product_id");

                    // Если уже в корзине – ведём на страницу корзины
                    if (btn.hasClass("in-cart")) {
                        window.location = "<?= esc_js($cart_url); ?>";
                        return;
                    }

                    btn.addClass("loading").css("opacity", ".6");

                    $.ajax({
                        url: wc_add_to_cart_params.wc_ajax_url.replace('%%endpoint%%', 'add_to_cart'),
                        type: "POST",
                        data: {
                            product_id: product_id,
                            quantity: 1
                        },
                        success: function (response) {

                            // Проверяем на ошибки
                            if (response.error && response.product_url) {
                                console.error('❌ Ошибка добавления, редирект на:', response.product_url);
                                window.location = response.product_url;
                                return;
                            }

                            // обновляем мини-корзину + хедер
                            if (response && response.fragments) {
                                $.each(response.fragments, function (key, value) {
                                    $(key).replaceWith(value);
                                });
                            }

                            // Обновляем кнопку
                            btn.removeClass("loading")
                                .addClass("in-cart")
                                .css("opacity", "1");

                            btn.find(".btn-text").text("Перейти в корзину");

                            // *** ПОКАЗЫВАЕМ УВЕДОМЛЕНИЕ - только в блоке product-price ***
                            const toastNow = $('.product-price .product-toast-notification');

                            if (toastNow.length) {
                                toastNow.addClass('active');

                                // Закрытие по крестику
                                toastNow.find('.toast-close').off('click').on('click', function () {
                                    toastNow.removeClass('active');
                                });

                                // Автоскрытие через 4 секунды
                                setTimeout(function () {
                                    toastNow.removeClass('active');
                                }, 4000);
                            } else {
                                console.error('❌ Уведомление не найдено!');
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('❌ Ошибка AJAX:', error);
                            alert('Произошла ошибка при добавлении товара');
                            btn.removeClass("loading").css("opacity", "1");
                        }
                    });

                });

            });

        </script>

        <?php else : ?>

        <p class="product-price__out-of-stock" style="color:#d32f2f;font-weight:600;margin:8px 0;">
            Нет в наличии
        </p>

        <?php endif; ?>

    </div>

    <div class="product-price-actions">
        <div class="compare-observer" style="display:none;">
            <?php echo do_shortcode('[yith_compare_button product="' . $product->get_id() . '"]'); ?>
        </div>
        <a href="#" class="product-item__sravnenie size-10 flex items-center justify-center"
            data-product_id="<?php echo esc_attr($product->get_id()); ?>">
            <svg width="13" height="16" viewBox="0 0 13 16">
                <path d="M2 15V8M7 15V1M12 15V5" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
        </a>

        <div class="product-price-actions__separator"></div>
        <?php echo do_shortcode('[yith_wcwl_add_to_wishlist product_id="' . $product->get_id() . '"]'); ?>
    </div>

    <!-- УВЕДОМЛЕНИЕ О ДОБАВЛЕНИИ В КОРЗИНУ - уникальный класс -->
    <div class="product-toast-notification">
        <svg class="toast-close" width="16" height="16" viewBox="0 0 16 16" fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <path d="M12 4L4 12M4 4L12 12" stroke="#031343" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" />
        </svg>
        <div class="toast-info">
            <p>Товар <span><?= esc_html($product_name); ?></span> добавлен в корзину</p>
            <a href="<?= esc_url($cart_url); ?>">Перейти в корзину</a>
        </div>
    </div>

    <style>
        .yith-wcwl-add-to-wishlist {
            margin-top: 0px;
        }

        .add_to_wishlist {
            width: calc(var(--spacing) * 10) !important;
            height: calc(var(--spacing) * 10) !important;
            background-color: #fff !important;
        }

        .product-item__sravnenie {
            background-color: #fff;
            border-radius: 30px;
        }

        /* Стили для уведомления - уникальный класс */
        .product-toast-notification {
            position: fixed !important;
            bottom: 20px !important;
            right: 20px !important;
            background: #fff !important;
            border: 2px solid #01CD3A !important;
            border-radius: 12px !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15) !important;
            padding: 16px 20px !important;
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            z-index: 99999 !important;
            transform: translateX(500px) !important;
            opacity: 0 !important;
            transition: all 0.4s ease !important;
            max-width: 400px !important;
            pointer-events: none !important;
        }

        .product-toast-notification.active {
            transform: translateX(0) !important;
            opacity: 1 !important;
            pointer-events: auto !important;
        }

        .toast-close {
            cursor: pointer !important;
            flex-shrink: 0 !important;
            width: 20px !important;
            height: 20px !important;
            transition: opacity 0.2s !important;
        }

        .toast-close:hover {
            opacity: 0.6 !important;
        }

        .toast-info {
            flex: 1 !important;
        }

        .toast-info p {
            margin: 0 0 8px 0 !important;
            font-size: 14px !important;
            color: #031343 !important;
            line-height: 1.4 !important;
        }

        .toast-info p span {
            font-weight: 600 !important;
        }

        .toast-info a {
            color: #258ffb !important;
            font-size: 13px !important;
            text-decoration: none !important;
            font-weight: 500 !important;
            transition: color 0.2s !important;
        }

        .toast-info a:hover {
            color: #1a6dc4 !important;
            text-decoration: underline !important;
        }

        @media (max-width: 768px) {
            .product-toast-notification {
                right: 10px !important;
                left: 10px !important;
                max-width: none !important;
                bottom: 10px !important;
            }
        }
    </style>

</div>