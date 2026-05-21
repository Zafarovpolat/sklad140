<?php
defined('ABSPATH') || exit;
global $product;

$title = get_the_title();
$rating = $product->get_average_rating();
$reviews = $product->get_review_count();
$stock_status = $product->is_in_stock();
?>

<h3 class="product-specifications__title"><?= esc_html($title) ?></h3>

<div class="product-specifications__row">

    <div class="product-specifications-rating">
        <svg width="16" height="16" style="margin-top: -3px;">
            <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#star"></use>
        </svg>

        <p class="product-specifications-rating__rate">
            <?= $rating ? number_format($rating, 1) : '0' ?>
        </p>

<?php if ($reviews > 0): ?>
    <p class="product-specifications-rating__reviews">
        (<?= $reviews ?> <?= _n('отзыв', 'отзыва', $reviews, 'woocommerce') ?>)
    </p>
<?php else: ?>
    <p class="product-specifications-rating__reviews">
        (Нет отзывов)
    </p>
<?php endif; ?>
    </div>

    <div class="product-item__availability bg-white rounded-lg w-fit flex items-center gap-1.5 py-2 px-3 mb-2">

        <svg class="product-item__availability--check" width="12" height="12" style="<?= $stock_status ? '' : 'display:none' ?>">
            <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#check"></use>
        </svg>

        <svg class="product-item__availability--cross" width="12" height="12" style="<?= $stock_status ? 'display:none' : '' ?>">
            <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#cross"></use>
        </svg>

        <p><?= $stock_status ? 'В наличии' : 'Нет в наличии' ?></p>
    </div>

</div>
