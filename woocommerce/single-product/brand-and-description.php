<?php
defined('ABSPATH') || exit;
global $product;

$brand_logo = get_field('brand_logo', $product->get_id());
$short_desc = apply_filters('woocommerce_short_description', $product->get_short_description());
?>

<?php if ($brand_logo): ?>
<div class="product-specifications__logo">
    <img src="<?= esc_url($brand_logo['url']); ?>"
         alt="<?= esc_attr($brand_logo['alt'] ?: 'Бренд'); ?>">
</div>
<?php endif; ?>

<?php if ($short_desc): ?>
<p class="product-specifications__description">
    <?= $short_desc; ?>
</p>
<?php endif; ?>
