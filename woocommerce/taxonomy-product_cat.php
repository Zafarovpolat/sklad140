<?php
/**
 * Страница категории товаров
 * AJAX-фильтр + Infinite Scroll (без пагинации)
 */
defined('ABSPATH') || exit;
get_header('shop');

$term = get_queried_object();
$cart_url = wc_get_cart_url();
$current_term = $term;
$current_cat_id = $current_term->term_id;
$orderby_keep = isset($_GET['orderby']) ? wc_clean(wp_unslash($_GET['orderby'])) : '';

$current_ancestors = get_ancestors($current_term->term_id, 'product_cat');

$top_ancestor_id = !empty($current_ancestors) ? end($current_ancestors) : $current_cat_id;

$parent_cat_id = wp_get_term_taxonomy_parent_id($current_cat_id, 'product_cat');
$parent_cat = $parent_cat_id ? get_term($parent_cat_id, 'product_cat') : null;

$selected_colors = [];
if (isset($_GET['color']) && is_array($_GET['color'])) {
    foreach ($_GET['color'] as $c) {
        $selected_colors[] = sanitize_title($c);
    }
} elseif (isset($_GET['color'])) {
    $selected_colors[] = sanitize_title($_GET['color']);
}
$selected_mats = isset($_GET['material']) ? array_map('sanitize_title', (array)$_GET['material']) : [];
$price_min = isset($_GET['price_min']) ? esc_attr($_GET['price_min']) : '';
$price_max = isset($_GET['price_max']) ? esc_attr($_GET['price_max']) : '';
$stock_only = !empty($_GET['stock']);

// Список товаров текущей ветки
$ids_q = new WP_Query([
    'post_type'      => 'product',
    'posts_per_page' => 500,
    'fields'         => 'ids',
    'no_found_rows'  => true,
    'tax_query'      => [[
        'taxonomy' => 'product_cat',
        'field'    => 'term_id',
        'terms'    => [$current_term->term_id],
        'include_children' => true,
    ]]
]);
$product_ids_for_terms = $ids_q->posts;

$color_tax = s140_attr_tax_slug_by_label('цвет');
$mat_tax   = s140_attr_tax_slug_by_label('материал');

$color_terms = [];
if ($color_tax && $product_ids_for_terms) {
    $tmp = wp_get_object_terms($product_ids_for_terms, $color_tax, ['fields' => 'all']);
    if (is_array($tmp) && !is_wp_error($tmp)) {
        $uniq = [];
        foreach ($tmp as $t) { $uniq[$t->term_id] = $t; }
        $color_terms = array_values($uniq);
    }
}

$mat_terms = [];
if ($mat_tax && $product_ids_for_terms) {
    $tmp = wp_get_object_terms($product_ids_for_terms, $mat_tax, ['fields' => 'all']);
    if (is_array($tmp) && !is_wp_error($tmp)) {
        $uniq = [];
        foreach ($tmp as $t) { $uniq[$t->term_id] = $t; }
        $mat_terms = array_values($uniq);
    }
}

$dynamic_filters = [];
$skip_taxonomies = array_filter([$color_tax, $mat_tax]);

if ($product_ids_for_terms) {
    $attribute_taxonomies = wc_get_attribute_taxonomies();
    
    foreach ($attribute_taxonomies as $attr) {
        $taxonomy = wc_attribute_taxonomy_name($attr->attribute_name);
        
        if (in_array($taxonomy, $skip_taxonomies)) continue;
        
        $terms = wp_get_object_terms($product_ids_for_terms, $taxonomy, ['fields' => 'all']);
        
        if (is_array($terms) && !is_wp_error($terms) && !empty($terms)) {
            $uniq = [];
            foreach ($terms as $t) { $uniq[$t->term_id] = $t; }
            $terms = array_values($uniq);
            
            $param_name = 'attr_' . $attr->attribute_name;
            $selected = isset($_GET[$param_name]) ? array_map('sanitize_text_field', (array) $_GET[$param_name]) : [];
            
            $dynamic_filters[] = [
                'label'    => $attr->attribute_label,
                'taxonomy' => $taxonomy,
                'param'    => $param_name,
                'terms'    => $terms,
                'selected' => $selected,
            ];
        }
    }
}

/**
 * Рекурсивная функция для вывода дерева категорий
 */
if (!function_exists('s140_render_category_tree')) {
    function s140_render_category_tree($parent_id, $current_term_id, $current_ancestors, $orderby_keep, $level = 0) {
        $children = get_terms([
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
            'parent'     => $parent_id,
        ]);
        
        if (empty($children) || is_wp_error($children)) {
            return;
        }
        
        $template_uri = get_template_directory_uri();
        
        foreach ($children as $cat):
            $is_current = ((int)$cat->term_id === (int)$current_term_id);
            $is_ancestor = in_array($cat->term_id, $current_ancestors);
            
            $link = get_term_link($cat);
            if (!is_wp_error($link) && $orderby_keep) {
                $link = add_query_arg(['orderby' => $orderby_keep], $link);
            }
            
            $sub_children = get_terms([
                'taxonomy'   => 'product_cat',
                'hide_empty' => false,
                'parent'     => $cat->term_id,
            ]);
            $has_children = !empty($sub_children) && !is_wp_error($sub_children);
            $is_expanded = $is_ancestor || $is_current;
            
            $item_class = 'filter-section-item';
            if ($has_children) $item_class .= ' has-children';
            if ($is_expanded && $has_children) $item_class .= ' is-expanded';
            
            $is_active = $is_current || $is_ancestor;
            $span_class = $is_active ? 'is-active' : '';
            
            $show_back_arrow = ($cat->term_id == wp_get_term_taxonomy_parent_id($current_term_id, 'product_cat'));
        ?>
            <li class="<?= $item_class; ?>" data-level="<?= $level; ?>">
                <div class="filter-section-item__row">
                    <a href="<?= esc_url($link); ?>" class="filter-section-item__link">
                        <span class="<?= $span_class; ?>">
                            <?php if ($show_back_arrow): ?>
                                <svg class="filter-section-item__back-arrow" width="6" height="12">
                                    <use xlink:href="<?= esc_url($template_uri); ?>/images/sprite.svg#arrow"></use>
                                </svg>
                            <?php endif; ?>
                            <?= esc_html($cat->name); ?>
                        </span>
                    </a>
                    <?php if ($has_children): ?>
                        <button type="button" class="filter-section-item__toggle" aria-expanded="<?= $is_expanded ? 'true' : 'false'; ?>">
                            <svg width="6" height="12" class="toggle-arrow <?= $is_expanded ? 'rotated' : ''; ?>">
                                <use xlink:href="<?= esc_url($template_uri); ?>/images/sprite.svg#arrow"></use>
                            </svg>
                        </button>
                    <?php endif; ?>
                </div>
                
                <?php if ($has_children): ?>
                    <ul class="filter-section__subitems" style="<?= $is_expanded ? '' : 'display:none;'; ?>">
                        <?php s140_render_category_tree($cat->term_id, $current_term_id, $current_ancestors, $orderby_keep, $level + 1); ?>
                    </ul>
                <?php endif; ?>
            </li>
        <?php 
        endforeach;
    }
}
?>

<link rel="stylesheet" href="<?= esc_url(get_template_directory_uri()); ?>/css/catalog.min.css" />
<link rel="stylesheet" href="<?= esc_url(get_template_directory_uri()); ?>/css/category.min.css" />
<script src="<?= esc_url(get_template_directory_uri()); ?>/js/pages/catalog.js" defer></script>

<style>
    .catalog-categories-swiper__wrapper {
        display: flex;
    }

    @media (min-width: 769px) {
        .catalog-categories-swiper {
            overflow-x: hidden;
        }
    }

    .catalog-categories-swiper-scrollbar {
        width: 70%;
    }

    .filter-section__items--tree {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .filter-section-item__row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .filter-section-item__link {
        display: flex;
        align-items: center;
        flex: 1;
        color: inherit;
        text-decoration: none;
        transition: color 0.2s;
    }

    .filter-section-item__link:hover {
        color: #258ffb;
    }

    .filter-section-item__link span.is-active {
        font-weight: 700;
        color: #258ffb;
    }

    .filter-section-item__toggle {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        padding: 0;
        border: none;
        background: #f5f5f5;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.2s;
        flex-shrink: 0;
    }

    .filter-section-item__toggle:hover {
        background: #e0e0e0;
    }

    .filter-section-item__toggle .toggle-arrow {
        transition: transform 0.3s ease;
    }

    .filter-section-item__toggle .toggle-arrow.rotated {
        transform: rotate(90deg);
    }

    .filter-section__subitems {
        list-style: none;
        padding-left: 16px;
        margin: 8px 0 0 0;
        border-left: 2px solid #e8e8e8;
    }

    .filter-section__subitems .filter-section-item {
        margin-bottom: 6px;
    }

    .filter-section__subitems .filter-section-item__link {
        font-size: 14px;
    }

    .filter-section__subitems .filter-section__subitems {
        padding-left: 12px;
    }

    [data-level="2"] .filter-section-item__link {
        font-size: 13px;
    }

    [data-level="3"] .filter-section-item__link {
        font-size: 12px;
    }

    /* AJAX загрузка */
    .catalog-products__items {
        position: relative;
        min-height: 200px;
    }

    .catalog-products__items.is-loading {
        opacity: 0.5;
        pointer-events: none;
    }

    .catalog-products__items.is-loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 40px;
        height: 40px;
        border: 3px solid #e8e8e8;
        border-top-color: #258ffb;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        to {
            transform: translate(-50%, -50%) rotate(360deg);
        }
    }

    /* Infinite Scroll Loader */
    .infinite-scroll-loader {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 30px;
        gap: 10px;
        grid-column: 1 / -1;
    }

    .infinite-scroll-loader .loader-spinner {
        width: 36px;
        height: 36px;
        border: 3px solid #e8e8e8;
        border-top-color: #258ffb;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    .infinite-scroll-loader p {
        color: #666;
        font-size: 14px;
    }

    .no-products-message {
        grid-column: 1 / -1;
        text-align: center;
        padding: 40px 20px;
        color: #666;
        font-size: 16px;
    }

    /* Кнопка сброса */
    .filter-button__reset-full {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 12px 20px;
        background: #f5f5f5;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        color: #333;
        transition: background 0.2s;
    }

    .filter-button__reset-full:hover {
        background: #e8e8e8;
    }

    @media (max-width: 768px) {
        .catalog-categories-swiper-scrollbar {
            width: 100%;
        }
    }
    .filters,
    .filters--mobile {
        position: relative;
    }

    .filters.is-loading,
    .filters--mobile.is-loading {
        pointer-events: none;
        opacity: 0.6;
    }

    .filters.is-loading::after,
    .filters--mobile.is-loading::after {
        content: 'Загрузка...';
        position: absolute;
        inset: 0;
        z-index: 20;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.65);
        color: #258ffb;
        font-size: 16px;
        font-weight: 600;
        border-radius: 8px;
    }
    .product-item .none_instock {
        filter: grayscale(60%);
        opacity: .40;
        transition: opacity 0.2s ease, filter 0.2s ease;
    }
</style>

<section class="catalog-section">
    <div class="container">
        <div class="catalog__wrapper">

            <!-- ФИЛЬТРЫ -->
            <aside class="filters">
                <form id="catalog-filters" class="filters" method="get" action="">
                    <?php if ($orderby_keep): ?>
                        <input type="hidden" name="orderby" value="<?= esc_attr($orderby_keep); ?>">
                    <?php endif; ?>
                    <input type="hidden" name="term_id" value="<?= esc_attr($current_term->term_id); ?>">

                    <!-- Категория товаров с аккордеоном -->
                    <div class="filter-section filter-section--categories">
                        <h6 class="filter-section__title">Категория товаров</h6>
                        
                        <?php
                        $top_categories = get_terms([
                            'taxonomy'   => 'product_cat',
                            'hide_empty' => false,
                            'parent'     => 0,
                            'exclude'    => [get_option('default_product_cat')],
                        ]);
                        
                        if (!empty($top_categories) && !is_wp_error($top_categories) && $top_ancestor_id) {
                            usort($top_categories, function($a, $b) use ($top_ancestor_id) {
                                if ($a->term_id == $top_ancestor_id) return -1;
                                if ($b->term_id == $top_ancestor_id) return 1;
                                return 0;
                            });
                        }
                        
                        $limit = 5;
                        $i = 0;
                        ?>
                        
                        <ul class="filter-section__items filter-section__items--tree">
                            <?php
                            if (!empty($top_categories) && !is_wp_error($top_categories)):
                                foreach ($top_categories as $cat):
                                    $is_current = ((int)$cat->term_id === (int)$current_term->term_id);
                                    $is_ancestor = in_array($cat->term_id, $current_ancestors);
                                    $is_active_branch = ($cat->term_id == $top_ancestor_id);
                                    
                                    if ($is_active_branch) {
                                        $hidden = '';
                                    } else {
                                        $i++;
                                        $hidden = ($i > $limit) ? ' style="display:none"' : '';
                                    }
                                    
                                    $link = get_term_link($cat);
                                    if (!is_wp_error($link) && $orderby_keep) {
                                        $link = add_query_arg(['orderby' => $orderby_keep], $link);
                                    }
                                    
                                    $sub_children = get_terms([
                                        'taxonomy'   => 'product_cat',
                                        'hide_empty' => false,
                                        'parent'     => $cat->term_id,
                                    ]);
                                    $has_children = !empty($sub_children) && !is_wp_error($sub_children);
                                    $is_expanded = $is_ancestor || $is_current;
                                    
                                    $item_class = 'filter-section-item';
                                    if ($has_children) $item_class .= ' has-children';
                                    if ($is_expanded && $has_children) $item_class .= ' is-expanded';
                                    
                                    $is_active = $is_current || $is_ancestor;
                                    $span_class = $is_active ? 'is-active' : '';
                                    
                                    $show_back_arrow = ($cat->term_id == $parent_cat_id);
                            ?>
                                <li class="<?= $item_class; ?>" data-level="0"<?= $hidden; ?>>
                                    <div class="filter-section-item__row">
                                        <a href="<?= esc_url($link); ?>" class="filter-section-item__link">
                                            <span class="<?= $span_class; ?>">
                                                <?php if ($show_back_arrow): ?>
                                                    <svg class="filter-section-item__back-arrow" width="6" height="12">
                                                        <use xlink:href="<?= esc_url(get_template_directory_uri()); ?>/images/sprite.svg#arrow"></use>
                                                    </svg>
                                                <?php endif; ?>
                                                <?= esc_html($cat->name); ?>
                                            </span>
                                        </a>
                                        <?php if ($has_children): ?>
                                            <button type="button" class="filter-section-item__toggle" aria-expanded="<?= $is_expanded ? 'true' : 'false'; ?>">
                                                <svg width="6" height="12" class="toggle-arrow <?= $is_expanded ? 'rotated' : ''; ?>">
                                                    <use xlink:href="<?= esc_url(get_template_directory_uri()); ?>/images/sprite.svg#arrow"></use>
                                                </svg>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if ($has_children): ?>
                                        <ul class="filter-section__subitems" style="<?= $is_expanded ? '' : 'display:none;'; ?>">
                                            <?php s140_render_category_tree($cat->term_id, $current_term->term_id, $current_ancestors, $orderby_keep, 1); ?>
                                        </ul>
                                    <?php endif; ?>
                                </li>
                            <?php 
                                endforeach;
                            endif;
                            ?>
                        </ul>
                        <?php if (!empty($top_categories) && count($top_categories) > $limit): ?>
                            <p class="toggle-text" data-type="categories">Показать всё</p>
                        <?php endif; ?>
                    </div>

                    <!-- Цвет -->
                    <?php if ($color_tax && !empty($color_terms)): ?>
                        <div class="filter-section">
                            <h6 class="filter-section__title">Цвет</h6>
                            <ul class="filter-section__colors">
                                <?php $limit = 5;
                                $i = 0;
                                foreach ($color_terms as $t):
                                    $i++;
                                    $hidden = ($i > $limit) ? ' style="display:none"' : '';
                                    $hex = function_exists('get_field') ? (string) get_field('czvet_palitra', 'term_' . $t->term_id) : '';
                                    $hex = $hex ?: '#ffffff';
                                    $checked = in_array($t->slug, $selected_colors);
                                    ?>
                                    <li class="filter-section-color" <?= $hidden; ?>>
                                        <label class="filter-section-color__label">
                                            <input class="input-checkbox__input ajax-filter-input" type="checkbox"
                                                name="color[]" value="<?= esc_attr($t->slug); ?>" <?= $checked ? ' checked' : ''; ?>>
                                            <div class="input-radio__circle<?= $checked ? ' input-radio__circle--active' : ''; ?>"
                                                style="background-color: <?= esc_attr($hex); ?>;"></div>
                                            <p class="input-radio__name"><?= esc_html($t->name); ?></p>
                                        </label>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php if (count($color_terms) > $limit): ?>
                                <p class="toggle-text">Показать всё</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Материал -->
                    <?php if ($mat_tax && !empty($mat_terms)): ?>
                        <div class="filter-section">
                            <h6 class="filter-section__title">Материал</h6>
                            <ul class="filter-section__materials">
                                <?php $limit = 5;
                                $i = 0;
                                foreach ($mat_terms as $t):
                                    $i++;
                                    $hidden = ($i > $limit) ? ' style="display:none"' : '';
                                    $checked = in_array($t->slug, $selected_mats, true);
                                    ?>
                                    <li class="filter-section-material" <?= $hidden; ?>>
                                        <label class="filter-section-material__label">
                                            <input class="input-checkbox__input ajax-filter-input" type="checkbox"
                                                name="material[]" value="<?= esc_attr($t->slug); ?>" <?= $checked ? ' checked' : ''; ?>>
                                            <p class="input-checkbox__name"><?= esc_html($t->name); ?></p>
                                        </label>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php if (count($mat_terms) > $limit): ?>
                                <p class="toggle-text">Показать всё</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php foreach ($dynamic_filters as $filter): ?>
                        <div class="filter-section">
                            <h6 class="filter-section__title"><?= esc_html($filter['label']); ?></h6>
                            <ul class="filter-section__materials">
                                <?php 
                                $limit = 5;
                                $i = 0;
                                foreach ($filter['terms'] as $t):
                                    $i++;
                                    $hidden = ($i > $limit) ? ' style="display:none"' : '';
                                    $checked = in_array($t->slug, $filter['selected'], true);
                                ?>
                                    <li class="filter-section-material"<?= $hidden; ?>>
                                        <label class="filter-section-material__label">
                                            <input class="input-checkbox__input ajax-filter-input" 
                                                   type="checkbox"
                                                   name="<?= esc_attr($filter['param']); ?>[]" 
                                                   value="<?= esc_attr($t->slug); ?>"
                                                   <?= $checked ? ' checked' : ''; ?>>
                                            <p class="input-checkbox__name"><?= esc_html($t->name); ?></p>
                                        </label>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php if (count($filter['terms']) > $limit): ?>
                                <p class="toggle-text">Показать всё</p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>

                    <!-- Цена -->
                    <div class="filter-section">
                        <h6 class="filter-section__title">Цена, ₽</h6>
                        <div class="filter-section-price__wrapper">
                            <input class="input filter-section-price__number ajax-filter-input" type="number"
                                name="price_min" placeholder="От" min="0" step="1" value="<?= $price_min; ?>">
                            <input class="input filter-section-price__number ajax-filter-input" type="number"
                                name="price_max" placeholder="До" min="0" step="1" value="<?= $price_max; ?>">
                        </div>
                    </div>

                    <!-- Наличие -->
                    <div class="filter-section">
                        <h6 class="filter-section__title">Наличие</h6>
                        <label class="filter-section-availability__label">
                            <input class="input-checkbox__input filter-section-availability__input ajax-filter-input"
                                type="checkbox" name="stock" value="1" <?= $stock_only ? ' checked' : ''; ?>>
                            <p class="filter-section-availability__name">Только в наличии</p>
                        </label>
                    </div>

                    <!-- Кнопка сброса -->
                    <div class="filter-bottom">
                        <button class="filter-button__reset-full" type="button" id="reset-filters">
                            <svg width="18" height="18">
                                <use xlink:href="<?= esc_url(get_template_directory_uri()); ?>/images/sprite.svg#reset">
                                </use>
                            </svg>
                            Сбросить фильтры
                        </button>
                    </div>
                </form>
            </aside>

            <!-- КАТАЛОГ -->
            <div class="catalog">
                <?php
                $verhnij_tekst = get_field('verhnij_tekst', $term);
                $inform_block = get_field('informaczionnyj_blok_sprava', $term);
                $czvetovoj_fon_bloka = get_field('czvetovoj_fon_bloka', $term);
                $czvet_tekstov_bloka = get_field('czvet_tekstov_bloka', $term);
                $prozrachnaya_kartinka = get_field('prozrachnaya_kartinka', $term);
                $prozrachnyj_fon_dlya_kartinki = get_field('prozrachnyj_fon_dlya_kartinki', $term);
                $bg_style = $czvetovoj_fon_bloka ? 'style="background-color:' . esc_attr($czvetovoj_fon_bloka) . ';"' : '';
                $text_color_style = $czvet_tekstov_bloka ? '<style>.catalog-cover-left__title, .catalog-cover-left__subtitle {color:' . esc_attr($czvet_tekstov_bloka) . ';}</style>' : '';
                ?>
                <?= $text_color_style; ?>

                <div class="catalog-cover">
                    <h2 class="catalog-cover-left__mobile-title"><?= esc_html($term->name); ?></h2>
                    <div class="catalog-cover-left" <?= $bg_style; ?>>
                        <?php if ($verhnij_tekst): ?>
                            <p class="catalog-cover-left__subtitle"><?= esc_html($verhnij_tekst); ?></p>
                        <?php endif; ?>
                        <h2 class="catalog-cover-left__title"><?= esc_html($term->name); ?></h2>
                        <?php if (!empty($prozrachnyj_fon_dlya_kartinki['url'])): ?>
                            <img class="catalog-cover-left__bg" src="<?= esc_url($prozrachnyj_fon_dlya_kartinki['url']); ?>"
                                alt="">
                        <?php endif; ?>
                        <?php if (!empty($prozrachnaya_kartinka['url'])): ?>
                            <img class="catalog-cover-left__img" src="<?= esc_url($prozrachnaya_kartinka['url']); ?>"
                                alt="">
                        <?php endif; ?>
                    </div>

                    <div class="info-block">
                        <div class="info-block__top">
                            <svg width="18" height="18">
                                <use xlink:href="<?= esc_url(get_template_directory_uri()); ?>/images/sprite.svg#info">
                                </use>
                            </svg>
                            <p class="info-block__title">
                                <?= esc_html(!empty($inform_block['zagolovok']) ? $inform_block['zagolovok'] : 'Уважаемые покупатели'); ?>
                            </p>
                        </div>
                        <div class="info-block__text">
                            <?php if (!empty($inform_block['tekst'])): ?>
                                <?= wp_kses_post($inform_block['tekst']); ?>
                            <?php else: ?>
                                <p>Уважаемые покупатели, все оборудование на сайте является Б/У, в полностью рабочем
                                    состоянии и
                                    готово к ежедневной эксплуатации.</p>
                                <br>
                                <p>Просьба актуальную информацию о товаре уточнять у менеджера.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php
                $children = get_terms([
                    'taxonomy' => 'product_cat',
                    'hide_empty' => false,
                    'parent' => $term->term_id,
                ]);
                ?>
                <?php if (!is_wp_error($children) && $children): ?>
                    <div class="catalog-categories-swiper__wrapper">
                        <div class="catalog-categories-swiper swiper">
                            <div class="swiper-wrapper">
                                <?php foreach ($children as $cat):
                                    $thumb_id = get_term_meta($cat->term_id, 'thumbnail_id', true);
                                    $img = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'medium') : wc_placeholder_img_src('woocommerce_thumbnail');
                                    ?>
                                    <div class="swiper-slide">
                                        <a class="catalog-categories-swiper-item" href="<?= esc_url(get_term_link($cat)); ?>">
                                            <p class="catalog-categories-swiper-item__title"><?= esc_html($cat->name); ?></p>
                                            <div class="catalog-categories-swiper-item__img">
                                                <img src="<?= esc_url($img); ?>" loading="lazy"
                                                    alt="<?= esc_attr($cat->name); ?>">
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                    <div class="swiper-scrollbar__wrapper catalog-categories-scrollbar__wrapper" style="margin-top: 20px;">
                        <div class="swiper-scrollbar__left">
                            <p class="swiper-scrollbar__subtitle">Зажми и потяни</p>
                            <img class="swiper-scrollbar__icon" src="<?= get_template_directory_uri(); ?>/images/gif/drag.gif" loading="lazy" alt="">
                        </div>
                        <div class="catalog-categories-swiper-scrollbar swiper-scrollbar"></div>
                    </div>

                    </div>
                <?php endif; ?>

                <div class="catalog-products__wrapper">
                    <div class="catalog-products-header">
                        <button class="button button--fill catalog-products-header__filter-btn">Фильтр
                            <svg width="16" height="18">
                                <use
                                    xlink:href="<?= esc_url(get_template_directory_uri()); ?>/images/sprite.svg#filter">
                                </use>
                            </svg>
                        </button>

                        <?php
                        $orderby = isset($_GET['orderby']) ? wc_clean(wp_unslash($_GET['orderby'])) : apply_filters('woocommerce_default_catalog_orderby', get_option('woocommerce_default_catalog_orderby'));
                        $current_sort_label = [
                            'date' => 'По новизне',
                            'price' => 'Дешевле',
                            'price-desc' => 'Дороже',
                            'rating' => 'С высоким рейтингом',
                            'menu_order' => 'Сначала популярные',
                        ][$orderby] ?? 'По новизне';
                        ?>
                        <div class="catalog-products-header__btn-wrapper">
                            <button class="button catalog-products-header__btn" type="button">
                                <p><?= esc_html($current_sort_label); ?></p>
                                <svg width="6" height="12">
                                    <use
                                        xlink:href="<?= esc_url(get_template_directory_uri()); ?>/images/sprite.svg#arrow">
                                    </use>
                                </svg>
                            </button>
                            <div class="catalog-products-header__btn-content">
                                <p data-sort="sort-new">По новизне</p>
                                <p data-sort="sort-cheaper">Дешевле</p>
                                <p data-sort="sort-expensive">Дороже</p>
                                <p data-sort="sort-good-rating">С высоким рейтингом</p>
                            </div>
                        </div>

                        <?php global $wp_query; ?>
                        <p class="catalog-products-header__count">Представлено <span
                                id="products-count"><?= (int) $wp_query->found_posts; ?></span> товаров</p>
                    </div>

                    <div class="catalog-products">
                        <?php
                        global $wp_query;
                        $max_pages = (int) $wp_query->max_num_pages;
                        ?>
                        <div class="catalog-products__items" id="products-grid"
                            data-term-id="<?= esc_attr($current_term->term_id); ?>"
                            data-max-pages="<?= esc_attr($max_pages); ?>">
                            <?php if (have_posts()):
                                while (have_posts()):
                                    the_post();
                                    get_template_part('template-parts/product', 'card');
                                endwhile;
                                wp_reset_postdata();
                            else: ?>
                                <p class="no-products-message">Товары не найдены.</p>
                            <?php endif; ?>
                        </div>
                        <!-- Пагинация убрана - используется Infinite Scroll -->
                    </div>

                    <div class="catalog-products-bottom">
                        <div class="catalog-products-bottom__text"><?= term_description($term) ?: ''; ?></div>
                        <p class="toggle-text">Раскрыть</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .product-item__cart--added {
        display: none;
    }

    .is-in-cart .product-item__cart {
        display: none;
    }

    .is-in-cart .product-item__cart--added {
        display: block;
    }

    #catalog-filters-mobile {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .filters-modal-observer {
        position: fixed;
        top: 0;
        right: 0;
        left: 0;
        bottom: 0;
        overflow-y: scroll;
        display: none;
        z-index: 100;
        padding-top: 20vh;
    }

    .filters-modal-observer::-webkit-scrollbar {
        display: none;
    }

    .filters-modal {
        position: relative;
        max-height: max-content;
    }
</style>

<!-- МОБИЛЬНАЯ МОДАЛКА ФИЛЬТРОВ -->
<div class="filters-modal-observer">
    <div class="filters-modal">
        <div class="filters-modal__curtain-wrapper">
            <div class="filters-modal__curtain"></div>
        </div>

        <a class="button filters-modal__btn" href="/shop/">
            <svg width="15" height="16">
                <use xlink:href="<?= esc_url(get_template_directory_uri()); ?>/images/sprite.svg#catalog-blue"></use>
            </svg>
            Каталог товаров
        </a>

        <aside class="filters filters--mobile">
            <form id="catalog-filters-mobile" method="get" action="<?= esc_url(get_term_link($current_term)); ?>">
                <?php if (!empty($orderby_keep)): ?>
                    <input type="hidden" name="orderby" value="<?= esc_attr($orderby_keep); ?>">
                <?php endif; ?>
                <input type="hidden" name="term_id" value="<?= esc_attr($current_term->term_id); ?>">

 <!-- Категория товаров с аккордеоном -->
<div class="filter-section filter-section--categories">
    <h6 class="filter-section__title">Категория товаров</h6>
    
    <?php
    // Получаем все топ-категории
    $top_categories = get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
        'parent'     => 0,
        'exclude'    => [get_option('default_product_cat')],
    ]);
    
    if (!empty($top_categories) && !is_wp_error($top_categories) && $top_ancestor_id) {
        usort($top_categories, function($a, $b) use ($top_ancestor_id) {
            if ($a->term_id == $top_ancestor_id) return -1;
            if ($b->term_id == $top_ancestor_id) return 1;
            return 0;
        });
    }
    
    $limit = 5;
    $i = 0;
    ?>
    
    <ul class="filter-section__items filter-section__items--tree">
        <?php
        if (!empty($top_categories) && !is_wp_error($top_categories)):
            foreach ($top_categories as $cat):
                $is_current = ((int)$cat->term_id === (int)$current_term->term_id);
                $is_ancestor = in_array($cat->term_id, $current_ancestors);
                $is_active_branch = ($cat->term_id == $top_ancestor_id);
                
                if ($is_active_branch) {
                    $hidden = '';
                } else {
                    $i++;
                    $hidden = ($i > $limit) ? ' style="display:none"' : '';
                }
                
                $link = get_term_link($cat);
                if (!is_wp_error($link) && $orderby_keep) {
                    $link = add_query_arg(['orderby' => $orderby_keep], $link);
                }
                
                $sub_children = get_terms([
                    'taxonomy'   => 'product_cat',
                    'hide_empty' => false,
                    'parent'     => $cat->term_id,
                ]);
                $has_children = !empty($sub_children) && !is_wp_error($sub_children);
                $is_expanded = $is_ancestor || $is_current;
                
                $item_class = 'filter-section-item';
                if ($has_children) $item_class .= ' has-children';
                if ($is_expanded && $has_children) $item_class .= ' is-expanded';
                
                $span_class = $is_current ? 'is-active' : '';
                
                $show_back_arrow = $is_ancestor && $parent_cat && ($cat->term_id == $parent_cat_id || in_array($cat->term_id, $current_ancestors));
        ?>
            <li class="<?= $item_class; ?>" data-level="0"<?= $hidden; ?>>
                <div class="filter-section-item__row">
                    <?php if ($is_ancestor && $cat->term_id == end($current_ancestors)): ?>
                        <a href="<?= esc_url($link); ?>" class="filter-section-item__back" title="Вернуться в <?= esc_attr($cat->name); ?>">
                            <svg width="6" height="12">
                                <use xlink:href="<?= esc_url(get_template_directory_uri()); ?>/images/sprite.svg#arrow"></use>
                            </svg>
                        </a>
                    <?php endif; ?>
                    
                    <a href="<?= esc_url($link); ?>" class="filter-section-item__link">
                        <span class="<?= $span_class; ?>"><?= esc_html($cat->name); ?></span>
                    </a>
                    <?php if ($has_children): ?>
                        <button type="button" class="filter-section-item__toggle" aria-expanded="<?= $is_expanded ? 'true' : 'false'; ?>">
                            <svg width="6" height="12" class="toggle-arrow <?= $is_expanded ? 'rotated' : ''; ?>">
                                <use xlink:href="<?= esc_url(get_template_directory_uri()); ?>/images/sprite.svg#arrow"></use>
                            </svg>
                        </button>
                    <?php endif; ?>
                </div>
                
                <?php if ($has_children): ?>
                    <ul class="filter-section__subitems" style="<?= $is_expanded ? '' : 'display:none;'; ?>">
                        <?php s140_render_category_tree($cat->term_id, $current_term->term_id, $current_ancestors, $orderby_keep, 1); ?>
                    </ul>
                <?php endif; ?>
            </li>
        <?php 
            endforeach;
        endif;
        ?>
    </ul>
    <?php if (!empty($top_categories) && count($top_categories) > $limit): ?>
        <p class="toggle-text" data-type="categories">Показать всё</p>
    <?php endif; ?>
</div>

                <!-- Цвет (мобильный) -->
                <?php if (!empty($color_tax) && !empty($color_terms)): ?>
                    <div class="filter-section">
                        <h6 class="filter-section__title">Цвет</h6>
                        <ul class="filter-section__colors">
                            <?php $limit = 5;
                            $i = 0;
                            foreach ($color_terms as $t):
                                $i++;
                                $hidden = $i > $limit ? ' style="display:none"' : '';
                                $hex = function_exists('get_field') ? (string) get_field('czvet_palitra', $color_tax . '_' . $t->term_id) : '';
                                if (!preg_match('/^#?[0-9a-f]{3,6}$/i', $hex))
                                    $hex = '#ffffff';
                                $checked = in_array($t->slug, $selected_colors) ? ' checked' : '';
                                ?>
                                <li class="filter-section-color" <?= $hidden; ?>>
                                    <label class="filter-section-color__label">
                                        <input class="input-checkbox__input ajax-filter-input-mobile" type="checkbox"
                                            name="color[]" value="<?= esc_attr($t->slug); ?>" <?= $checked; ?>>
                                        <div class="input-radio__circle" style="background-color: <?= esc_attr($hex); ?>;">
                                        </div>
                                        <p class="input-radio__name"><?= esc_html($t->name); ?></p>
                                    </label>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php if (count($color_terms) > $limit): ?>
                            <p class="toggle-text">Показать всё</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Материал (мобильный) -->
                <?php if (!empty($mat_tax) && !empty($mat_terms)): ?>
                    <div class="filter-section">
                        <h6 class="filter-section__title">Материал</h6>
                        <ul class="filter-section__materials">
                            <?php $limit = 5;
                            $i = 0;
                            foreach ($mat_terms as $t):
                                $i++;
                                $hidden = $i > $limit ? ' style="display:none"' : '';
                                $checked = in_array($t->slug, $selected_mats, true) ? ' checked' : '';
                                ?>
                                <li class="filter-section-material" <?= $hidden; ?>>
                                    <label class="filter-section-material__label">
                                        <input class="input-checkbox__input ajax-filter-input-mobile" type="checkbox"
                                            name="material[]" value="<?= esc_attr($t->slug); ?>" <?= $checked; ?>>
                                        <p class="input-checkbox__name"><?= esc_html($t->name); ?></p>
                                    </label>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php if (count($mat_terms) > $limit): ?>
                            <p class="toggle-text">Показать ещё</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Цена (мобильный) -->
                <div class="filter-section" style="margin-bottom: 24px;">
                    <h6 class="filter-section__title">Цена, ₽</h6>
                    <div class="filter-section-price__wrapper">
                        <input class="input filter-section-price__number ajax-filter-input-mobile" type="number"
                            name="price_min" placeholder="От" min="0" value="<?= esc_attr($price_min); ?>">
                        <input class="input filter-section-price__number ajax-filter-input-mobile" type="number"
                            name="price_max" placeholder="До" min="0" value="<?= esc_attr($price_max); ?>">
                    </div>
                </div>

                <!-- Наличие (мобильный) -->
                <div class="filter-section" style="margin-bottom: 24px;">
                    <h6 class="filter-section__title">Наличие</h6>
                    <label class="filter-section-availability__label">
                        <input class="input-checkbox__input filter-section-availability__input ajax-filter-input-mobile"
                            type="checkbox" name="stock" value="1" <?= $stock_only ? 'checked' : ''; ?>>
                        <p class="filter-section-availability__name">Только в наличии</p>
                    </label>
                </div>

                <div class="filter-bottom">
                    <button class="filter-button__reset-full" type="button" id="reset-filters-mobile">
                        <svg width="18" height="18">
                            <use xlink:href="<?= esc_url(get_template_directory_uri()); ?>/images/sprite.svg#reset">
                            </use>
                        </svg>
                        Сбросить фильтры
                    </button>
                </div>
            </form>
        </aside>
    </div>
</div>

<!-- СКРИПТЫ -->
<script>
(function () {
    const grid = document.getElementById('products-grid');
    const countEl = document.getElementById('products-count');
    const form = document.getElementById('catalog-filters');
    const formMobile = document.getElementById('catalog-filters-mobile');
    const ajaxUrl = '<?= admin_url("admin-ajax.php"); ?>';
    const nonce = '<?= wp_create_nonce("ajax_filter_nonce"); ?>';

    if (!grid) return;

    let filterTimeout = null;
    let currentPage = 1;
    let maxPages = parseInt(grid.dataset.maxPages || 1, 10);
    let currentOrderby = '<?= esc_js($orderby ?? "date"); ?>';

    let isFilterLoading = false;
    let isLoadMoreLoading = false;
    let observer = null;

    function setFiltersLoading(state) {
        if (form) form.classList.toggle('is-loading', state);
        if (formMobile) formMobile.classList.toggle('is-loading', state);
    }

    function getActiveForm() {
        return window.innerWidth < 768 && formMobile ? formMobile : form;
    }

    function getFilterData(formEl) {
        const data = new FormData(formEl);
        const params = {};

        params.term_id = data.get('term_id') || '0';

        const colors = data.getAll('color[]');
        if (colors.length) params.color = colors;

        const materials = data.getAll('material[]');
        if (materials.length) params.material = materials;

        const priceMin = data.get('price_min');
        const priceMax = data.get('price_max');

        if (priceMin) params.price_min = priceMin;
        if (priceMax) params.price_max = priceMax;
        if (data.get('stock')) params.stock = 1;

        params.orderby = currentOrderby || 'date';

        const seen = new Set();
        for (const [key] of data.entries()) {
            if (key.startsWith('attr_') && key.endsWith('[]') && !seen.has(key)) {
                seen.add(key);
                const values = data.getAll(key);
                if (values.length) {
                    params[key.replace('[]', '')] = values;
                }
            }
        }

        return params;
    }

    function updateURL(params) {
        const url = new URL(window.location.href);

        const keysToDelete = [];
        url.searchParams.forEach((v, k) => {
            if (
                k === 'color[]' ||
                k === 'material[]' ||
                k.startsWith('attr_') ||
                k === 'price_min' ||
                k === 'price_max' ||
                k === 'stock' ||
                k === 'paged'
            ) {
                keysToDelete.push(k);
            }
        });

        keysToDelete.forEach(k => url.searchParams.delete(k));

        if (params.color) {
            params.color.forEach(c => url.searchParams.append('color[]', c));
        }

        if (params.material) {
            params.material.forEach(m => url.searchParams.append('material[]', m));
        }

        if (params.price_min) url.searchParams.set('price_min', params.price_min);
        if (params.price_max) url.searchParams.set('price_max', params.price_max);
        if (params.stock) url.searchParams.set('stock', '1');

        if (params.orderby && params.orderby !== 'date') {
            url.searchParams.set('orderby', params.orderby);
        } else {
            url.searchParams.delete('orderby');
        }

        for (const [key, value] of Object.entries(params)) {
            if (key.startsWith('attr_') && Array.isArray(value)) {
                value.forEach(v => url.searchParams.append(key + '[]', v));
            }
        }

        history.replaceState(null, '', url.toString());
    }

    function appendDynamicAttrs(formData, params) {
        for (const [key, value] of Object.entries(params)) {
            if (key.startsWith('attr_') && Array.isArray(value)) {
                value.forEach(v => formData.append(key + '[]', v));
            }
        }
    }

    function buildFilterFormData(params, page = 1) {
        const formData = new FormData();

        formData.append('action', 'filter_products');
        formData.append('nonce', nonce);
        formData.append('term_id', params.term_id || '0');
        formData.append('paged', String(page));
        formData.append('orderby', params.orderby || 'date');

        if (params.color) params.color.forEach(c => formData.append('color[]', c));
        if (params.material) params.material.forEach(m => formData.append('material[]', m));
        if (params.price_min) formData.append('price_min', params.price_min);
        if (params.price_max) formData.append('price_max', params.price_max);
        if (params.stock) formData.append('stock', '1');

        appendDynamicAttrs(formData, params);

        return formData;
    }

    function buildLoadMoreFormData(params, page) {
        const formData = new FormData();

        formData.append('action', 'load_more_products');
        formData.append('nonce', nonce);
        formData.append('term_id', params.term_id || '0');
        formData.append('paged', String(page));
        formData.append('orderby', params.orderby || 'date');

        if (params.color) params.color.forEach(c => formData.append('color[]', c));
        if (params.material) params.material.forEach(m => formData.append('material[]', m));
        if (params.price_min) formData.append('price_min', params.price_min);
        if (params.price_max) formData.append('price_max', params.price_max);
        if (params.stock) formData.append('stock', '1');

        appendDynamicAttrs(formData, params);

        // Отправляем ID уже загруженных товаров для исключения дублей на сервере
        grid.querySelectorAll('.product-item--catalog').forEach(function(el) {
            var pid = el.getAttribute('data-product-id');
            if (pid) formData.append('loaded_ids[]', pid);
        });

        return formData;
    }

    async function fetchProducts(params) {
        if (isFilterLoading) return;

        isFilterLoading = true;
        grid.classList.add('is-loading');
        setFiltersLoading(true);

        if (observer) observer.disconnect();

        try {
            const response = await fetch(ajaxUrl, {
                method: 'POST',
                credentials: 'same-origin',
                body: buildFilterFormData(params, 1)
            });

            const data = await response.json();

            if (data.success) {
                grid.innerHTML = data.data.html;
                if (countEl) countEl.textContent = data.data.found;

                maxPages = parseInt(data.data.max_pages || 1, 10);
                currentPage = 1;
                grid.dataset.maxPages = String(maxPages);

                updateURL(params);
                observeLastProduct();
            }
        } catch (error) {
            console.error('Filter error:', error);
        } finally {
            isFilterLoading = false;
            grid.classList.remove('is-loading');
            setFiltersLoading(false);
        }
    }

    async function loadMoreProducts() {
        if (isFilterLoading || isLoadMoreLoading || currentPage >= maxPages) return;

        isLoadMoreLoading = true;
        const nextPage = currentPage + 1;

        const loader = document.createElement('div');
        loader.className = 'infinite-scroll-loader';
        loader.innerHTML = '<div class="loader-spinner"></div><p>Загрузка...</p>';
        grid.appendChild(loader);

        try {
            const params = getFilterData(getActiveForm());

            const response = await fetch(ajaxUrl, {
                method: 'POST',
                credentials: 'same-origin',
                body: buildLoadMoreFormData(params, nextPage)
            });

            const data = await response.json();

            loader.remove();

            if (data.success && data.data.html) {
                // Клиентская дедупликация: собираем ID уже отображённых товаров
                const existingIds = new Set();
                grid.querySelectorAll('.product-item--catalog').forEach(el => {
                    const pid = el.getAttribute('data-product-id');
                    if (pid) existingIds.add(pid);
                });

                const tmp = document.createElement('div');
                tmp.innerHTML = data.data.html;
                Array.from(tmp.children).forEach(node => {
                    if (node.nodeType !== 1) return;
                    const pid = node.getAttribute && node.getAttribute('data-product-id');
                    if (pid && existingIds.has(pid)) return;
                    if (pid) existingIds.add(pid);
                    grid.appendChild(node);
                });

                currentPage = nextPage;
                maxPages = parseInt(data.data.max_pages || maxPages, 10);
                grid.dataset.maxPages = String(maxPages);
                observeLastProduct();
            }
        } catch (error) {
            loader.remove();
            console.error('Load more error:', error);
        } finally {
            isLoadMoreLoading = false;
        }
    }

    function observeLastProduct() {
        if (observer) observer.disconnect();
        if (currentPage >= maxPages) return;

        const products = grid.querySelectorAll('.product-item');
        if (!products.length) return;

        const lastProduct = products[products.length - 1];

        observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    loadMoreProducts();
                }
            });
        }, {
            root: null,
            rootMargin: '200px',
            threshold: 0
        });

        observer.observe(lastProduct);
    }

    function applyFilterDebounced(formEl, delay = 500) {
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(() => {
            fetchProducts(getFilterData(formEl));
        }, delay);
    }

    function applyFilterInstant(formEl) {
        clearTimeout(filterTimeout);
        fetchProducts(getFilterData(formEl));
    }

    if (form) {
        form.querySelectorAll('input[type="checkbox"].ajax-filter-input').forEach(input => {
            input.addEventListener('change', () => applyFilterInstant(form));
        });

        form.querySelectorAll('input[type="number"].ajax-filter-input').forEach(input => {
            input.addEventListener('input', () => applyFilterDebounced(form, 500));
            input.addEventListener('change', () => applyFilterInstant(form));
        });
    }

    if (formMobile) {
        formMobile.querySelectorAll('input[type="checkbox"].ajax-filter-input-mobile').forEach(input => {
            input.addEventListener('change', () => applyFilterInstant(formMobile));
        });

        formMobile.querySelectorAll('input[type="number"].ajax-filter-input-mobile').forEach(input => {
            input.addEventListener('input', () => applyFilterDebounced(formMobile, 500));
            input.addEventListener('change', () => applyFilterInstant(formMobile));
        });
    }

    document.getElementById('reset-filters')?.addEventListener('click', function () {
        if (!form) return;
        form.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
        form.querySelectorAll('input[type="number"]').forEach(input => input.value = '');
        applyFilterInstant(form);
    });

    document.getElementById('reset-filters-mobile')?.addEventListener('click', function () {
        if (!formMobile) return;
        formMobile.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
        formMobile.querySelectorAll('input[type="number"]').forEach(input => input.value = '');
        applyFilterInstant(formMobile);
    });

    const sortMap = {
        'sort-new': 'date',
        'sort-cheaper': 'price',
        'sort-expensive': 'price-desc',
        'sort-good-rating': 'rating'
    };

    document.addEventListener('click', function (e) {
        const el = e.target.closest('p[data-sort]');
        if (!el) return;

        currentOrderby = sortMap[el.dataset.sort] || 'date';

        const btnText = el.closest('.catalog-products-header__btn-wrapper')?.querySelector('.catalog-products-header__btn p');
        if (btnText) {
            btnText.textContent = el.textContent;
        }

        applyFilterInstant(getActiveForm());
    });

    document.addEventListener('click', function (e) {
        const toggleBtn = e.target.closest('.filter-section-item__toggle');
        if (!toggleBtn) return;

        e.preventDefault();
        e.stopPropagation();

        const listItem = toggleBtn.closest('.filter-section-item');
        const subItems = listItem.querySelector('.filter-section__subitems');
        const arrow = toggleBtn.querySelector('.toggle-arrow');

        if (!subItems) return;

        const isExpanded = toggleBtn.getAttribute('aria-expanded') === 'true';

        if (isExpanded) {
            subItems.style.display = 'none';
            toggleBtn.setAttribute('aria-expanded', 'false');
            arrow.classList.remove('rotated');
            listItem.classList.remove('is-expanded');
        } else {
            subItems.style.display = '';
            toggleBtn.setAttribute('aria-expanded', 'true');
            arrow.classList.add('rotated');
            listItem.classList.add('is-expanded');
        }
    });

    document.addEventListener('click', function (e) {
        const toggle = e.target.closest('.toggle-text[data-type="categories"]');
        if (!toggle) return;

        const section = toggle.closest('.filter-section');
        if (!section) return;

        const items = section.querySelectorAll('.filter-section__items--tree > .filter-section-item');
        const limit = 5;
        let opened = false;

        items.forEach((item, idx) => {
            if (idx >= limit) {
                if (item.style.display === 'none') {
                    item.style.display = '';
                    opened = true;
                } else {
                    item.style.display = 'none';
                }
            }
        });

        toggle.textContent = opened ? 'Скрыть' : 'Показать всё';
    });

    document.addEventListener('click', function (e) {
        const t = e.target.closest('.toggle-text:not([data-type="categories"])');
        if (!t) return;

        const section = t.closest('.filter-section');
        if (!section) return;

        const list = section.querySelector('ul');
        if (!list) return;

        const items = Array.from(list.querySelectorAll('li'));
        const limit = 5;
        const isOpen = items.slice(limit).every(li => li.style.display !== 'none');

        items.forEach((li, idx) => {
            if (idx >= limit) {
                li.style.display = isOpen ? 'none' : '';
            }
        });

        t.textContent = isOpen ? 'Показать всё' : 'Скрыть';
    });

    observeLastProduct();
})();
</script>

<script>
    (function () {
        const grid = document.getElementById('products-grid');
        const cartUrl = '<?= esc_js($cart_url); ?>';
        if (!grid) return;

        grid.addEventListener('click', async (e) => {
            const btn = e.target.closest('.product-item__btn-cart');
            if (!btn || btn.tagName === 'A') return;
            e.preventDefault();
            const card = btn.closest('.product-item');
            const pid = btn.getAttribute('data-product_id');
            if (!pid) return;
            btn.classList.add('is-loading');
            try {
                const form = new FormData();
                form.append('product_id', pid);
                form.append('quantity', 1);
                const res = await fetch('<?= esc_url(admin_url('admin-ajax.php?action=woocommerce_add_to_cart')); ?>', { method: 'POST', credentials: 'same-origin', body: form });
                if (!res.ok) throw new Error('add_to_cart failed');
                document.body.dispatchEvent(new Event('wc_fragment_refresh'));
                const a = document.createElement('a');
                a.href = cartUrl;
                a.className = btn.className + ' is-in-cart';
                a.innerHTML = btn.innerHTML;
                btn.replaceWith(a);
                const toast = card?.querySelector('.product-item__added-to-cart');
                if (toast) {
                    toast.classList.add('active');
                    const close = toast.querySelector('.product-item__added-to-cart__cross');
                    const hide = () => toast.classList.remove('active');
                    close && close.addEventListener('click', hide, { once: true });
                    setTimeout(hide, 4000);
                }
            } catch (err) { console.error(err); }
            finally { btn.classList.remove('is-loading'); }
        });

        document.addEventListener('click', (e) => {
            const a = e.target.closest('a.added_to_cart.wc-forward');
            if (a) { e.preventDefault(); a.remove(); }
        });
    })();
</script>

<style>
    .compare-observer {
        display: none;
    }

    .product-item__sravnenie {
        position: relative;
        cursor: pointer;
    }

    .product-item__sravnenie svg path {
        transition: stroke 0.3s;
    }

    .product-item__sravnenie.added svg path {
        stroke: #258ffb;
    }

    .product-item__sravnenie::after {
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: #258ffb;
        color: #fff;
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 4px;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity .25s;
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
        width: 24px;
        height: 22px;
        stroke-width: 1.5;
        transition: fill .3s, stroke .3s;
    }

    .yith-wcwl-add-to-wishlist svg path {
        stroke: #999;
        fill: none;
    }

    .yith-wcwl-add-to-wishlist.exists svg path,
    .yith-wcwl-add-to-wishlist.added svg path {
        stroke: #258ffb;
        fill: #258ffb;
    }

    .yith-wcwl-add-to-wishlist a::after {
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: #258ffb;
        color: #fff;
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 4px;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity .25s ease;
        content: "Добавить в избранное";
    }

    .yith-wcwl-add-to-wishlist.exists a::after,
    .yith-wcwl-add-to-wishlist.added a::after {
        content: "В избранном";
    }

    .yith-wcwl-add-to-wishlist a:hover::after {
        opacity: 1;
    }

   /* Стрелка "назад" внутри span */
.filter-section-item__back-arrow {
    display: inline-block;
    width: 6px;
    height: 12px;
    margin-right: 8px;
    transform: rotate(180deg);
    vertical-align: middle;
}

/* Цвет стрелки такой же как активный текст */
.filter-section-item__link span.is-active .filter-section-item__back-arrow use {
    stroke: #258FFB;
}

/* Hover эффект */
.filter-section-item__link:hover .filter-section-item__back-arrow {
    transform: rotate(180deg) translateX(2px);
    transition: transform 0.2s;
}

.product-item__btn-cart.added .product-item__cart {
    display: none;
}

.product-item__btn-cart.added .product-item__cart--added {
    display: block;
}
</style>

<script>
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
                    if (custom.classList.contains('added')) { window.location.href = comparePage; return; }
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
                if (!link.querySelector('svg')) {
                    link.innerHTML = `<svg width="24" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/></svg>`;
                }
            });
        }
        fixWishlistButtons();
        const observer = new MutationObserver(() => fixWishlistButtons(document));
        observer.observe(document.body, { childList: true, subtree: true });
        if (typeof jQuery !== 'undefined') {
            jQuery(document).on('added_to_wishlist removed_from_wishlist', () => fixWishlistButtons(document));
        }
    });
</script>

<?php get_footer('shop'); ?>