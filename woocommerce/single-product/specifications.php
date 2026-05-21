<?php
defined('ABSPATH') || exit;
global $product;

if (!$product instanceof WC_Product) return;

$attributes = $product->get_attributes();
if (!$attributes) return;

/**
 * Готовим пары [label, value] напрямую из атрибутов товара,
 * без $product->get_attribute(name) — иначе при наличии плагина cyr2lat
 * sanitize_title для кастомных кириллических атрибутов даёт ключ,
 * не совпадающий с тем как они хранятся в _product_attributes (urlencoded),
 * и значение возвращается пустым.
 */
$rows = [];
foreach ($attributes as $attribute) {
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

if (!$rows) return;
?>

<ul class="product-item__specifications product-item__specifications-list w-full text-sm/[120%] mb-2 space-y-1">

<?php foreach ($rows as $row): ?>
    <li>
        <div class="flex gap-0.5 items-end justify-between specs-row">
            <p class="product-specifications-field__name text-brand-gray">
                <?= esc_html($row['label']); ?>
            </p>

            <div class="specs-dots w-full flex-1 border-b border-dashed border-brand-blue-200"></div>

            <span class="product-specifications-field__value">
                <?= esc_html($row['value']); ?>
            </span>
        </div>
    </li>
<?php endforeach; ?>

</ul>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.specs-row').forEach(row => {
        const dots = row.querySelector('.specs-dots');
        if (dots && dots.offsetWidth < 15) { // если линия меньше 15px
            dots.style.display = 'none';
        }
    });
});
</script>
