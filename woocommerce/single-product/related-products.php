<?php
defined('ABSPATH') || exit;

global $product;
if (!$product instanceof WC_Product)
    return;

$related_ids = wc_get_related_products($product->get_id(), 16);
if (!$related_ids)
    return;

$cart_url = wc_get_cart_url();
$products = array_filter(array_map('wc_get_product', $related_ids));
?>

<section class="same-products">
    <div class="container">
        <div class="section-header">
            <div class="section-header__left">
                <div class="section-header__icon">
                    <img src="<?= esc_url(get_template_directory_uri()); ?>/images/content/others/sofa-emoji.png"
                        alt="диван эмодзи" />
                </div>
                <h2>Похожие товары</h2>
            </div>
            <img src="<?= esc_url(get_template_directory_uri()); ?>/images/gif/drag-mobile.gif" alt="drag"
                class="section-header__icon--drag-mobile">
        </div>

        <div class="same-products-swiper swiper">
            <div class="swiper-wrapper">
                <?php foreach ($products as $p):
                    $pid = $p->get_id();
                    $permalink = get_permalink($pid);
                    $thumb = get_the_post_thumbnail_url($pid, 'woocommerce_thumbnail') ?: wc_placeholder_img_src('woocommerce_thumbnail');
                    $price = $p->get_price_html() ? htmlspecialchars_decode($p->get_price_html()) : htmlspecialchars_decode(wc_price($p->get_price()));
                    $in_stock = $p->is_in_stock();
                    $in_cart = strike_in_cart($pid);
                    $attrs = strike_first_attributes($p, 3);
                    ?>
                    <div class="swiper-slide">
                        <div class="product-item product-item--catalog" data-product-id="<?= esc_attr($pid); ?>">
                            <!-- Обёртка для позиционирования -->
                            <div class="product-item__img-wrapper relative mb-2" style="position: relative;">
                                <!-- Ссылка с фото -->
                                <a href="<?= esc_url($permalink); ?>"
                                    class="product-item__img flex items-center justify-center bg-white rounded-2xl overflow-hidden block">
                                    <img class="relative z-0 object-contain size-57" src="<?= esc_url($thumb); ?>"
                                        alt="<?= esc_attr($p->get_name()); ?>" />
                                </a>

                                <!-- Кнопки - справа сверху -->
                                <div class="absolute z-10 top-1 right-1 flex flex-col"
                                    style="position: absolute; top: 4px; right: 4px;">
                                    <?php echo do_shortcode('[yith_wcwl_add_to_wishlist product_id="' . $p->get_id() . '"]'); ?>

                                    <div class="compare-observer" style="display:none;">
                                        <?php echo do_shortcode('[yith_compare_button product="' . $p->get_id() . '"]'); ?>
                                    </div>

                                    <a href="#" class="product-item__sravnenie size-10 flex items-center justify-center"
                                        data-product_id="<?php echo esc_attr($p->get_id()); ?>">
                                        <svg width="13" height="16" viewBox="0 0 13 16">
                                            <path d="M2 15V8M7 15V1M12 15V5" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" />
                                        </svg>
                                    </a>
                                </div>

                                <!-- Бейдж БУ - слева снизу -->
                                <div class="absolute z-10 bottom-3 left-3 bg-primary rounded-[30px] h-max py-1.25 px-2"
                                    style="position: absolute; bottom: 12px; left: 12px;">
                                    <p class="font-medium text-[10px] text-white">Товар БУ</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 mb-2">
                                <h5 class="product-item__price font-semibold text-brand-red text-main-22/[120%]">
                                    <?= $price; ?>
                                </h5>
                            </div>

                            <p class="product-item__title font-medium text-primary mb-8.5"><a
                                    href="<?= esc_url($permalink); ?>"><?= esc_html($p->get_name()); ?></a></p>

                            <div
                                class="product-item__availability bg-white rounded-lg w-fit flex items-center gap-1.5 py-2 px-3 mb-2">
                                <?php if ($in_stock): ?>
                                    <svg class="product-item__availability--check" width="12" height="12">
                                        <use xlink:href="<?= esc_url(get_template_directory_uri()); ?>/images/sprite.svg#check">
                                        </use>
                                    </svg>
                                    <p>В наличии</p>
                                <?php else: ?>
                                    <svg class="product-item__availability--check" width="12" height="12">
                                        <use xlink:href="<?= esc_url(get_template_directory_uri()); ?>/images/sprite.svg#cross">
                                        </use>
                                    </svg>
                                    <p>Нет в наличии</p>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($attrs)): ?>
                                <ul class="product-item__specifications w-full text-sm/[120%] mb-2 space-y-1">
                                    <?php foreach ($attrs as $a): ?>
                                        <li>
                                            <div class="flex gap-0.5 items-end justify-between">
                                                <p class="text-brand-gray"><?= esc_html($a['label']); ?></p>
                                                <div class="w-full flex-1 border-b border-dashed border-brand-blue-200"></div>
                                                <span><?= esc_html($a['value']); ?></span>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>

                            <?php if ($in_stock): ?>
                                <div
                                    class="product-item__btns product-item__btns--availability flex items-center justify-between gap-1">
                                    <button
                                        class="button button--fill single_add_to_cart_button clickBuyButton button21 alt ld-ext-left"
                                        data-variation_id="0" data-productid="<?= esc_attr($pid); ?>" style="font-size:16px;">
                                        <p class="button__text">Купить сейчас</p>
                                        <div style="font-size:14px" class="ld ld-ring ld-cycle"></div>
                                    </button>
                                    <?php if ($in_cart): ?>
                                        <a href="<?= esc_url($cart_url); ?>"
                                            class="product-item__btn-cart button button--red flex items-center justify-center p-3.75 rounded-xl hover-greed-75 is-in-cart">
                                            <svg class="product-item__cart--added" width="18" height="18" viewBox="0 0 18 18"
                                                fill="none" xmlns="http://www.w3.org/2000/svg">
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
                                                <path d="M15.4545 3L13.0795 5.375L12 4.29545" stroke="white" stroke-width="0.5"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </a>
                                    <?php else: ?>
                                        <button type="button"
                                            class="product-item__btn-cart button button--red flex items-center justify-center p-3.75 rounded-xl hover-greed-75"
                                            data-product_id="<?= esc_attr($pid); ?>">
                                            <svg class="product-item__cart" width="18" height="18">
                                                <use
                                                    xlink:href="<?= esc_url(get_template_directory_uri()); ?>/images/sprite.svg#cart--filled">
                                                </use>
                                            </svg>
                                            <svg class="product-item__cart--added" width="18" height="18" viewBox="0 0 18 18"
                                                fill="none" xmlns="http://www.w3.org/2000/svg">
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
                                                <path d="M15.4545 3L13.0795 5.375L12 4.29545" stroke="white" stroke-width="0.5"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="product-item__btns product-item__btns--out-of-stock">
                                    <button class="button button--dark" type="button">Подобрать аналог</button>
                                </div>
                            <?php endif; ?>

                            <div class="product-item__added-to-cart">
                                <svg class="product-item__added-to-cart__cross" width="16" height="16" viewBox="0 0 16 16"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20 -4L-4 20M-4 -4L20 20" stroke="#031343" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="product-item__added-to-cart__info">
                                    <p>Товар <span><?= esc_html($p->get_name()); ?></span> добавлен в корзину</p>
                                    <a href="<?= esc_url($cart_url); ?>">Перейти в корзину</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Scrollbar с текстом "Зажми и потяни" -->
        <div class="swiper-scrollbar__wrapper">
            <div class="swiper-scrollbar__left">
                <p class="swiper-scrollbar__subtitle">Зажми и потяни</p>
                <img class="swiper-scrollbar__icon"
                    src="<?= esc_url(get_template_directory_uri()); ?>/images/gif/drag.gif" loading="lazy" alt="">
            </div>
            <div class="same-products-swiper-scrollbar swiper-scrollbar"></div>
        </div>
    </div>
</section>

<style>
    .product-item__availability--cross {
        display: block;
    }

    .compare-observer {
        display: none;
    }

    .product-item__sravnenie {
        position: relative;
        cursor: pointer;
        background: none;
    }

    .product-item__sravnenie svg path {
        transition: stroke 0.3s;
        stroke: #031343;
    }

    .product-item__sravnenie.added svg path {
        stroke: #258ffb;
    }

    .product-item__sravnenie:hover::after {
        opacity: 1;
    }

    .product-item__sravnenie:not(.added)::after {
        content: "Добавить в сравнение";
    }

    .product-item__sravnenie.added::after {
        content: "Перейти в сравнение";
    }


    /* избранное */
    .yith-wcwl-add-to-wishlist span,
    .yith-wcwl-add-to-wishlist .feedback,
    .yith-wcwl-wishlistaddedbrowse a {
        display: none !important;
    }

    .yith-wcwl-add-to-wishlist a {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        position: relative;
        cursor: pointer;
        background: transparent;
        transition: 0.3s;
    }

    .yith-wcwl-add-to-wishlist svg {
        width: 24px !important;
        height: 21px !important;
        margin-right: 0px !important;
        stroke-width: 1.5;
        transition: fill .3s, stroke .3s;
    }

    .yith-wcwl-add-to-wishlist svg path {
        stroke: #999;
        fill: none;
    }

    /* активное состояние избранного */
    .yith-wcwl-add-to-wishlist.exists svg path,
    .yith-wcwl-add-to-wishlist.added svg path {
        stroke: #258ffb;
        fill: #258ffb;
    }

    .yith-wcwl-add-to-wishlist.exists a::after,
    .yith-wcwl-add-to-wishlist.added a::after {
        content: "В избранном";
    }

    .yith-wcwl-add-to-wishlist a:hover::after {
        opacity: 1;
    }

    .yith-wcwl-add-button .add_to_wishlist svg.yith-wcwl-icon-svg,
    .yith-wcwl-add-button .add_to_wishlist img {
        margin-right: 0px;
    }

    .add_to_wishlist.add_to_wishlist.single_add_to_wishlist {
        justify-content: center;
    }
</style>

<script>
    (function () {
        const holder = document.querySelector('.same-products');
        if (!holder) return;

        holder.addEventListener('click', async (e) => {
            const btn = e.target.closest('.product-item__btn-cart');
            if (!btn) return;

            if (btn.tagName === 'A') return;

            e.preventDefault();
            const card = btn.closest('.product-item');
            const pid = btn.getAttribute('data-product_id');
            if (!pid) return;

            btn.classList.add('is-loading');

            try {
                const form = new FormData();
                form.append('product_id', pid);
                form.append('quantity', 1);

                const res = await fetch('<?php echo esc_url(admin_url('admin-ajax.php?action=woocommerce_add_to_cart')); ?>', {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: form
                });

                if (!res.ok) throw new Error('add_to_cart failed');
                document.body.dispatchEvent(new Event('wc_fragment_refresh'));

                const cartLink = document.createElement('a');
                cartLink.href = '<?php echo esc_js($cart_url); ?>';
                cartLink.className = btn.className + ' is-in-cart';
                cartLink.innerHTML = btn.innerHTML;
                btn.replaceWith(cartLink);

                const toast = card.querySelector('.product-item__added-to-cart');
                if (toast) {
                    toast.classList.add('active');
                    const close = toast.querySelector('.product-item__added-to-cart__cross');
                    const hide = () => toast.classList.remove('active');
                    close && close.addEventListener('click', hide, { once: true });
                    setTimeout(hide, 4000);
                }
            } catch (err) {
                console.error(err);
            } finally {
                btn.classList.remove('is-loading');
            }
        });

        const viewLinks = holder.querySelectorAll('a.added_to_cart.wc-forward');
        viewLinks.forEach(a => a.remove());

        document.addEventListener('click', (e) => {
            const a = e.target.closest('a.added_to_cart.wc-forward');
            if (a) a.remove();
        });
    })();

    document.addEventListener('DOMContentLoaded', () => {
        const comparePage = '/compare/';
        function syncButtons() {
            document.querySelectorAll('.compare-observer .compare').forEach(orig => {
                const pid = orig.dataset.product_id;
                const custom = document.querySelector(`.product-item__sravnenie[data-product_id="${pid}"]`);
                if (!custom) return;
                custom.classList.toggle('added', orig.classList.contains('added'));
                custom.onclick = e => {
                    e.preventDefault();
                    if (custom.classList.contains('added')) {
                        window.location.href = comparePage;
                        return;
                    }
                    orig.click();
                };
            });
        }
        syncButtons();
        const obs = new MutationObserver(() => syncButtons());
        obs.observe(document.body, { childList: true, subtree: true });
    });

    document.addEventListener('DOMContentLoaded', () => {
        function fixWishlistButtons(context = document) {
            context.querySelectorAll('.yith-wcwl-add-to-wishlist').forEach(block => {
                const link = block.querySelector('a');
                if (!link) return;

                // если внутри пропала иконка — вставляем снова
                if (!link.querySelector('svg')) {
                    link.innerHTML = `
          <svg width="24" height="22" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="1.5"
               xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/>
          </svg>
        `;
                }
            });
        }

        // первичная отрисовка
        fixWishlistButtons();

        // следим за изменениями DOM (AJAX-обновления плагина)
        const observer = new MutationObserver(() => fixWishlistButtons(document));
        observer.observe(document.body, { childList: true, subtree: true });

        // событие от YITH (на всякий случай)
        jQuery(document).on('added_to_wishlist removed_from_wishlist', () => fixWishlistButtons(document));
    });
</script>