<?php
defined('ABSPATH') || exit;
global $product;

$rating  = $product->get_average_rating();
$reviews = $product->get_review_count();
$in_stock = $product->is_in_stock();
$attributes = $product->get_attributes();

$logo = get_field('product_logo', $product->get_id());
?>

<div class="product-specifications">

    <h3 class="product-specifications__title"><?php the_title(); ?></h3>

    <div class="product-specifications__row">

        <div class="product-specifications-rating">
            <svg width="16" height="16" style="margin-top:-3px;">
                <use xlink:href="/images/sprite.svg#star"></use>
            </svg>
            <p class="product-specifications-rating__rate">
                <?= esc_html($rating ? number_format($rating, 1) : '0.0') ?>
            </p>
            <p class="product-specifications-rating__reviews">
                (<?= esc_html($reviews) ?> отзыв<?= $reviews == 1 ? '' : 'ов' ?>)
            </p>
        </div>

        <div class="product-item__availability bg-white rounded-lg w-fit flex items-center gap-1.5 py-2 px-3 mb-2">
            <?php if ($in_stock): ?>
                <svg class="product-item__availability--check" width="12" height="12">
                    <use xlink:href="/images/sprite.svg#check"></use>
                </svg>
                <p>В наличии</p>
            <?php else: ?>
                <svg class="product-item__availability--cross" width="12" height="12">
                    <use xlink:href="/images/sprite.svg#cross"></use>
                </svg>
                <p>Нет в наличии</p>
            <?php endif; ?>
        </div>

    </div>

    <ul class="product-item__specifications w-full text-sm/[120%] mb-2 space-y-1">
        <?php foreach ($attributes as $attr): 
            $label = wc_attribute_label($attr->get_name());
            $value = implode(', ', $attr->get_options());
        ?>
        <li>
            <div class="flex gap-0.5 items-end justify-between">
                <p class="product-specifications-field__name text-brand-gray"><?= esc_html($label) ?></p>
                <div class="w-full flex-1 border-b border-dashed border-brand-blue-200"></div>
                <span class="product-specifications-field__value"><?= esc_html($value) ?></span>
            </div>
        </li>
        <?php endforeach; ?>
    </ul>

    <?php if (!empty($logo['url'])): ?>
        <div class="product-specifications__logo">
            <img src="<?= esc_url($logo['url']) ?>" alt="">
        </div>
    <?php endif; ?>

    <p class="product-specifications__description">
        <?= wpautop( wp_kses_post( $product->get_short_description() ) ); ?>
    </p>

</div>
