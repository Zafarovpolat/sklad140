<?php
defined('ABSPATH') || exit;
global $product;

$price = $product->get_price();
$regular = $product->get_regular_price();
$sale = $product->get_sale_price();
$has_sale = $product->is_on_sale();
?>

<div class="product-price">
    <div class="product-price-top">
        <p class="product-price-top__subtitle">Цена, рублей</p>

        <div class="flex items-center gap-2 mb-2">
            <h5 class="product-item__price font-semibold text-brand-red text-main-22/[120%]">
                <?= wc_price($price); ?>
            </h5>

            <?php if ($has_sale && $regular): ?>
                <p class="font-medium text-sm/[120%] text-brand-blue-200 line-through decoration-brand-red">
                    <?= wc_price($regular); ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="bg-primary rounded-[30px] h-max py-1.25 px-2 w-fit">
            <p class="font-medium text-[10px] text-white">Товар БУ</p>
        </div>
    </div>

    <div class="product-price__btns">
        <button class="button button--fill" onclick="document.querySelector('.single_add_to_cart_button').click()">
            Купить сейчас
        </button>

        <?php woocommerce_template_single_add_to_cart(); ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const btn = document.querySelector('.single_add_to_cart_button');
                if (!btn) return;

                btn.classList.add('button');
                btn.innerHTML = `
                <svg class="fill-current transition-colors" width="18" height="18"><use xlink:href="/images/sprite.svg#cart--filled-black"></use></svg>
                В корзину
            `;
            });
        </script>

    </div>

    <div class="product-price-actions">
        <svg width="12" height="16">
            <use xlink:href="/images/sprite.svg#bar-chart-black"></use>
        </svg>
        <div class="product-price-actions__separator"></div>
        <svg width="19" height="18">
            <use xlink:href="/images/sprite.svg#favourites"></use>
        </svg>
    </div>
</div>

<div class="info-block">
    <div class="info-block__top"> <svg width="18" height="18">
            <use xlink:href="<?= esc_url(get_template_directory_uri()); ?>/images/sprite.svg#info"> </use>
        </svg>
        <p class="info-block__title">Уважаемые покупатели</p>
    </div>
    <div class="info-block__text">
        <p>Уважаемые покупатели, все оборудование на сайте является Б/У, в полностью рабочем состоянии и готово к
            ежедневной эксплуатации.</p> <br>
        <p>Просьба актуальную информацию о товаре уточнять у менеджера.</p>
    </div>
</div>