<?php
defined('ABSPATH') || exit;
global $product;

$full_desc = apply_filters('the_content', $product->get_description());

/**
 * Готовим пары [label, value] напрямую из объектов атрибутов товара.
 * Не используем $product->get_attribute(name): cyr2lat ломает sanitize_title
 * для кастомных кириллических атрибутов, и значение возвращается пустым.
 */
$rows = [];
if ($product instanceof WC_Product) {
    foreach ($product->get_attributes() as $attribute) {
        if (!is_a($attribute, 'WC_Product_Attribute')) continue;
        $label = wc_attribute_label($attribute->get_name());
        if ($attribute->is_taxonomy()) {
            $terms = wc_get_product_terms($product->get_id(), $attribute->get_name(), ['fields' => 'names']);
            if (empty($terms)) continue;
            $value = implode(', ', array_map('wp_strip_all_tags', $terms));
        } else {
            $opts = $attribute->get_options();
            if (empty($opts)) continue;
            $value = implode(', ', array_map('wp_strip_all_tags', $opts));
        }
        if ($value === '' || $value === null) continue;
        $rows[] = ['label' => $label, 'value' => $value];
    }
}
?>
<div class="product-info-content product-info-content--active" data-content="specifications">
    <h4 class="product-info-content__title">Технические характеристики</h4>
    <div class="product-info-content-description">
        <h5 class="product-info-content-description__title">Описание</h5>
        <?php if ($full_desc): ?>
            <div class="product-info-content-description__text"><?= $full_desc ?></div>
        <?php endif; ?>

        <p class="product__right-block__link">
            Показать ещё
            <svg width="10" height="6">
                <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow-blue"></use>
            </svg>
        </p>
    </div>
    <?php if (!empty($rows)): ?>
    <div class="product-info-content-specifications">
        <h5 class="product-info-content-specifications__title">Характеристики</h5>
        <div class="product-info-content-specifications__inner">
            <ul class="w-full text-sm/[120%] mb-2 space-y-1">
                <?php foreach ($rows as $row): ?>
                <li>
                    <div class="flex gap-0.5 items-end justify-between">
                        <p class="product-specifications-field__name text-brand-gray"><?= esc_html($row['label']); ?></p>
                        <div class="w-full flex-1 border-b border-dashed border-brand-blue-200"></div>
                        <span class="product-specifications-field__value"><?= esc_html($row['value']); ?></span>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>
</div>
