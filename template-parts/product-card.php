<?php
/**
 * Шаблон карточки товара для каталога
 */

$p = wc_get_product(get_the_ID());
if (!$p)
    return;

$pid = $p->get_id();
$permalink = get_permalink($pid);
$thumb = get_the_post_thumbnail_url($pid, 'woocommerce_thumbnail') ?: wc_placeholder_img_src('woocommerce_thumbnail');
$price = $p->get_price_html() ?: wc_price($p->get_price());
$in_stock = $p->is_in_stock();
$in_cart = function_exists('strike_in_cart') ? strike_in_cart($pid) : false;
$attrs = function_exists('strike_first_attributes') ? strike_first_attributes($p, 3) : array();
$cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : '/cart/';

// Проверяем находится ли товар в wishlist
$is_in_wishlist = false;
if (function_exists('yith_wcwl_wishlists')) {
    $is_in_wishlist = yith_wcwl_wishlists()->is_product_in_wishlist($pid);
}

// Генерируем URL кнопки wishlist с nonce
$wishlist_url = add_query_arg('add_to_wishlist', $pid);
if (function_exists('wp_create_nonce')) {
    $wishlist_url = add_query_arg('_wpnonce', wp_create_nonce('add_to_wishlist'), $wishlist_url);
}

// Классы контейнера wishlist
$wishlist_classes = 'yith-wcwl-add-to-wishlist add-to-wishlist-' . esc_attr($pid);
if ($is_in_wishlist) {
    $wishlist_classes .= ' exists';
}
?>
<div class="product-item product-item--catalog" data-product-id="<?= esc_attr($pid); ?>">
    <a href="<?= esc_url($permalink); ?>"
        class="product-item__img relative flex items-center justify-center bg-white text-white rounded-2xl overflow-hidden mb-2 relative">
        <img class="relative z-0 object-contain size-57 <?php if ($in_stock): ?><?php else: ?>none_instock<?php endif; ?>" src="<?= esc_url($thumb); ?>"
            alt="<?= esc_attr($p->get_name()); ?>" />
        <!-- Бейдж БУ - слева снизу -->
        <div class="absolute z-10 bottom-3 left-3 bg-primary rounded-[30px] h-max py-1.25 px-2"
            style="position: absolute; bottom: 12px; left: 12px;">
            <p class="font-medium text-[10px] text-white">Товар БУ</p>
        </div>
    </a>

    <div class="product-item__actions absolute z-10 top-0 w-fit right-0 flex justify-between gap-5">
        <div class="flex flex-col mt-1 mr-1">
            <?php // ПРЯМАЯ генерация HTML кнопки wishlist с проверкой статуса ?>
            <div class="<?= $wishlist_classes; ?>">
                <div class="yith-wcwl-add-button">
                    <a href="<?= esc_url($wishlist_url); ?>" class="add_to_wishlist single_add_to_wishlist"
                        data-product-id="<?= esc_attr($pid); ?>" data-product-type="<?= esc_attr($p->get_type()); ?>"
                        data-original-product-id="<?= esc_attr($pid); ?>" data-title="В избранное" rel="nofollow">
                        <svg id="yith-wcwl-icon-heart-outline" class="yith-wcwl-icon-svg" fill="none" stroke-width="1.5"
                            stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z">
                            </path>
                        </svg>
                        <span>В избранное</span>
                    </a>
                </div>
            </div>

            <div class="compare-observer" style="display:none;">
                <?php echo do_shortcode('[yith_compare_button product_id="' . intval($pid) . '"]'); ?>
            </div>
            <a href="#" class="product-item__sravnenie size-10 flex items-center justify-center"
                data-product_id="<?= esc_attr($pid); ?>">
                <svg width="13" height="16" viewBox="0 0 13 16">
                    <path d="M2 15V8M7 15V1M12 15V5" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
            </a>
        </div>
    </div>

    <div class="flex items-center gap-2 mb-2">
        <h5 class="product-item__price font-semibold text-brand-red text-main-22/[120%]"><?= $price; ?></h5>
    </div>

    <a href="<?= esc_url($permalink); ?>"
        class="product-item__title font-medium text-primary mb-8.5"><?= esc_html($p->get_name()); ?></a>

    <div class="product-item__availability bg-white rounded-lg w-fit flex items-center gap-1.5 py-2 px-3 mb-2">
        <?php if ($in_stock): ?>
            <svg class="product-item__availability--check" width="12" height="12">
                <use xlink:href="<?= esc_url(get_template_directory_uri()); ?>/images/sprite.svg#check"></use>
            </svg>
            <p>В наличии</p>
        <?php else: ?>
            <svg class="product-item__availability--check" width="12" height="12">
                <use xlink:href="<?= esc_url(get_template_directory_uri()); ?>/images/sprite.svg#cross"></use>
            </svg>
            <p>Нет в наличии</p>
        <?php endif; ?>
    </div>

    <ul class="product-item__specifications w-full text-sm/[120%] mb-2 space-y-1">
        <?php if (!empty($attrs)):
            foreach ($attrs as $a): ?>
                <li>
                    <div class="flex gap-0.5 items-end justify-between">
                        <p class="text-brand-gray"><?= esc_html($a['label']); ?></p>
                        <div class="w-full flex-1 border-b border-dashed border-brand-blue-200"></div>
                        <span><?= esc_html($a['value']); ?></span>
                    </div>
                </li>
            <?php endforeach; endif; ?>
    </ul>

    <?php if ($in_stock): ?>
        <div class="product-item__btns product-item__btns--availability flex items-center justify-between gap-1">
            <button class="product-item__buy-now button button--fill" href="<?= esc_url($permalink); ?>"
                style="font-size:16px;">
                <p class="button__text">Купить сейчас</p>
            </button>
            <?php if ($in_cart): ?>
                <a href="<?= esc_url($cart_url); ?>"
                    class="product-item__btn-cart button button--red flex items-center justify-center p-3.75 rounded-xl hover-greed-75 added">
                    <svg class="product-item__cart" width="18" height="18">
                        <use xlink:href="<?= esc_url(get_template_directory_uri()); ?>/images/sprite.svg#cart--filled"></use>
                    </svg>
                    <svg class="product-item__cart--added" width="18" height="18" viewBox="0 0 18 18" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M5.03211 10.8526L3.84025 5.2876H16.5671L15.3296 10.8601C15.2562 11.1935 15.0713 11.4919 14.8054 11.706C14.5395 11.9201 14.2085 12.037 13.8671 12.0376H6.53211C6.18329 12.0454 5.84267 11.9313 5.56889 11.715C5.29511 11.4987 5.10529 11.1937 5.03211 10.8526Z"
                            fill="#031343" />
                        <path
                            d="M1.53711 1.5376H3.03711L5.03211 10.8526C5.10529 11.1937 5.29511 11.4987 5.56889 11.715C5.84267 11.9313 6.18329 12.0454 6.53211 12.0376H13.8671C14.2085 12.037 14.5395 11.9201 14.8054 11.706C15.0713 11.4919 15.2562 11.1935 15.3296 10.8601L16.5671 5.2876H3.83961"
                            stroke="#031343" stroke-linecap="round" stroke-linejoin="round" />
                        <path
                            d="M6 16.5C6.41421 16.5 6.75 16.1642 6.75 15.75C6.75 15.3358 6.41421 15 6 15C5.58579 15 5.25 15.3358 5.25 15.75C5.25 16.1642 5.58579 16.5 6 16.5Z"
                            fill="#031343" />
                        <path
                            d="M14.25 16.5C14.6642 16.5 15 16.1642 15 15.75C15 15.3358 14.6642 15 14.25 15C13.8358 15 13.5 15.3358 13.5 15.75C13.5 16.1642 13.8358 16.5 14.25 16.5Z"
                            fill="#031343" />
                        <path
                            d="M6 16.5C6.41421 16.5 6.75 16.1642 6.75 15.75C6.75 15.3358 6.41421 15 6 15C5.58579 15 5.25 15.3358 5.25 15.75C5.25 16.1642 5.58579 16.5 6 16.5Z"
                            stroke="#031343" stroke-linecap="round" stroke-linejoin="round" />
                        <path
                            d="M14.25 16.5C14.6642 16.5 15 16.1642 15 15.75C15 15.3358 14.6642 15 14.25 15C13.8358 15 13.5 15.3358 13.5 15.75C13.5 16.1642 13.8358 16.5 14.25 16.5Z"
                            stroke="#031343" stroke-linecap="round" stroke-linejoin="round" />
                        <circle cx="14" cy="4" r="4" fill="#01CD3A" />
                        <path d="M15.4545 3L13.0795 5.375L12 4.29545" stroke="white" stroke-width="0.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </a>
            <?php else: ?>
                <button type="button"
                    class="product-item__btn-cart button button--red flex items-center justify-center p-3.75 rounded-xl hover-greed-75"
                    data-product_id="<?= esc_attr($pid); ?>">
                    <svg class="product-item__cart" width="18" height="18">
                        <use xlink:href="<?= esc_url(get_template_directory_uri()); ?>/images/sprite.svg#cart--filled"></use>
                    </svg>
                    <svg class="product-item__cart--added" width="18" height="18" viewBox="0 0 18 18" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M5.03211 10.8526L3.84025 5.2876H16.5671L15.3296 10.8601C15.2562 11.1935 15.0713 11.4919 14.8054 11.706C14.5395 11.9201 14.2085 12.037 13.8671 12.0376H6.53211C6.18329 12.0454 5.84267 11.9313 5.56889 11.715C5.29511 11.4987 5.10529 11.1937 5.03211 10.8526Z"
                            fill="#031343" />
                        <path
                            d="M1.53711 1.5376H3.03711L5.03211 10.8526C5.10529 11.1937 5.29511 11.4987 5.56889 11.715C5.84267 11.9313 6.18329 12.0454 6.53211 12.0376H13.8671C14.2085 12.037 14.5395 11.9201 14.8054 11.706C15.0713 11.4919 15.2562 11.1935 15.3296 10.8601L16.5671 5.2876H3.83961"
                            stroke="#031343" stroke-linecap="round" stroke-linejoin="round" />
                        <path
                            d="M6 16.5C6.41421 16.5 6.75 16.1642 6.75 15.75C6.75 15.3358 6.41421 15 6 15C5.58579 15 5.25 15.3358 5.25 15.75C5.25 16.1642 5.58579 16.5 6 16.5Z"
                            fill="#031343" />
                        <path
                            d="M14.25 16.5C14.6642 16.5 15 16.1642 15 15.75C15 15.3358 14.6642 15 14.25 15C13.8358 15 13.5 15.3358 13.5 15.75C13.5 16.1642 13.8358 16.5 14.25 16.5Z"
                            fill="#031343" />
                        <path
                            d="M6 16.5C6.41421 16.5 6.75 16.1642 6.75 15.75C6.75 15.3358 6.41421 15 6 15C5.58579 15 5.25 15.3358 5.25 15.75C5.25 16.1642 5.58579 16.5 6 16.5Z"
                            stroke="#031343" stroke-linecap="round" stroke-linejoin="round" />
                        <path
                            d="M14.25 16.5C14.6642 16.5 15 16.1642 15 15.75C15 15.3358 14.6642 15 14.25 15C13.8358 15 13.5 15.3358 13.5 15.75C13.5 16.1642 13.8358 16.5 14.25 16.5Z"
                            stroke="#031343" stroke-linecap="round" stroke-linejoin="round" />
                        <circle cx="14" cy="4" r="4" fill="#01CD3A" />
                        <path d="M15.4545 3L13.0795 5.375L12 4.29545" stroke="white" stroke-width="0.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </button>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="product-item__btns product-item__btns--out-of-stock">
            <button class="button button--dark" type="button">Подобрать аналог</button>
        </div>
    <?php endif; ?>

    <style>
        .product-item__btns--out-of-stock {
            display: block;
        }
    </style>

    <div class="product-item__added-to-cart">
        <svg class="product-item__added-to-cart__cross" width="16" height="16" viewBox="0 0 16 16" fill="none">
            <path d="M20 -4L-4 20M-4 -4L20 20" stroke="#031343" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" />
        </svg>
        <div class="product-item__added-to-cart__info">
            <p>Товар <a href="<?= esc_url($permalink); ?>"><?= esc_html($p->get_name()); ?></a> добавлен в корзину</p>
            <a href="<?= esc_url($cart_url); ?>">Перейти в корзину</a>
        </div>
    </div>
</div>

<!-- <style>
    /* Тултип для wishlist - СЛЕВА */
    .yith-wcwl-add-to-wishlist .add_to_wishlist {
        position: relative;
    }

    .yith-wcwl-add-to-wishlist .add_to_wishlist::after {
        content: "Добавить в избранное";
        position: absolute;
        top: 50%;
        right: 100%;
        transform: translateY(-50%);
        margin-right: 8px;
        background: #258ffb;
        color: #fff;
        padding: 4px 12px !important;
        border-radius: 6px;
        font-size: 10px !important;
        font-weight: 500;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.2s, visibility 0.2s;
        pointer-events: none;
        z-index: 100;
    }

    .yith-wcwl-add-to-wishlist .add_to_wishlist:hover::after {
        opacity: 1 !important;
        visibility: visible;
    }

    /* Скрываем стандартный span */
    .yith-wcwl-add-to-wishlist .add_to_wishlist span {
        display: none;
    }

    /* Тултип для сравнения - СЛЕВА */
    .product-item__sravnenie {
        position: relative;
    }

    .product-item__sravnenie::after {
        content: "Добавить в сравнение";
        position: absolute;
        top: 50%;
        right: 100%;
        transform: translateY(-50%);
        margin-right: 8px;
        background: #258ffb;
        color: #fff;
        padding: 5px 12px !important;
        border-radius: 6px;
        font-size: 10px !important;
        font-weight: 500;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.2s, visibility 0.2s;
        pointer-events: none;
        z-index: 100;
    }

    .product-item__sravnenie:hover::after {
        opacity: 1;
        visibility: visible;
    }
</style> -->