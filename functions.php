<?php

add_action('wp_footer', function () {
    if (!is_tax('product_cat'))
        return;

    $mat_tax = s140_attr_tax_slug_by_label('материал');
    $color_tax = s140_attr_tax_slug_by_label('цвет');

    // Получаем термины материала
    $mat_terms = get_terms(['taxonomy' => $mat_tax, 'hide_empty' => false, 'number' => 5]);

    echo '<!-- DEBUG: ';
    echo 'mat_tax=' . ($mat_tax ?: 'EMPTY') . ' | ';
    echo 'color_tax=' . ($color_tax ?: 'EMPTY') . ' | ';
    echo 'mat_terms=';
    if (!is_wp_error($mat_terms) && $mat_terms) {
        foreach ($mat_terms as $t) {
            echo $t->slug . ',';
        }
    } else {
        echo 'ERROR';
    }
    echo ' -->';
});

/**
 * Проверка товара в корзине
 */
if (!function_exists('strike_in_cart')) {
    function strike_in_cart($pid)
    {
        if (!function_exists('WC'))
            return false;
        $wc = WC();
        if (!$wc || !isset($wc->cart) || !$wc->cart)
            return false;
        $cart = $wc->cart->get_cart();
        if (empty($cart))
            return false;
        foreach ($cart as $item) {
            if ((int) ($item['product_id'] ?? 0) === (int) $pid)
                return true;
        }
        return false;
    }
}

/**
 * Получение первых N атрибутов товара
 */
if (!function_exists('strike_first_attributes')) {
    function strike_first_attributes($prod, $limit = 3)
    {
        $out = [];
        foreach ($prod->get_attributes() as $attr) {
            if (count($out) >= $limit)
                break;
            if ($attr->is_taxonomy()) {
                $name = wc_attribute_label($attr->get_name());
                $terms = wc_get_product_terms($prod->get_id(), $attr->get_name(), ['fields' => 'names']);
                if ($terms) {
                    $out[] = ['label' => $name, 'value' => implode(', ', array_slice($terms, 0, 3))];
                }
            } else {
                $name = $attr->get_name();
                $vals = array_map('wc_clean', $attr->get_options() ?: []);
                if ($vals) {
                    $out[] = ['label' => $name, 'value' => implode(', ', array_slice($vals, 0, 3))];
                }
            }
        }
        return array_slice($out, 0, $limit);
    }
}

add_theme_support('title-tag');

add_action('after_setup_theme', function () {
    register_nav_menus(array(
        'header' => 'Меню в шапке',
        'footer-1' => 'Меню в футере-1',
        'footer_categories' => 'Футер: категории товаров',
        'footer_navigation' => 'Футер: навигация',
        'footer_for_client' => 'Футер: для клиента',
        'footer_for_client_mobile' => 'Мобильное: для клиента',
    ));
});

if (function_exists('acf_add_options_page')) {
    acf_add_options_page(array(
        'page_title' => 'Настройки темы',
        'menu_title' => 'Настройки темы',
        'menu_slug' => 'theme-general-settings',
        'capability' => 'edit_posts',
        'redirect' => false
    ));
}

function add_phone_mask_script()
{
    wp_enqueue_script('inputmask-bundle', '/wp-content/themes/sklad140/js/jquery.inputmask.min.js', array('jquery'), null, true);
    wp_add_inline_script('inputmask-bundle', 'jQuery(function($){ function s140ApplyPhoneMask(){ $(".phone_mask").not("[data-s140-masked]").inputmask("+7 (999) 999-99-99").attr("data-s140-masked","1"); } s140ApplyPhoneMask(); $(document.body).on("updated_checkout updated_cart_totals updated_wc_div", s140ApplyPhoneMask); var mo=new MutationObserver(function(){s140ApplyPhoneMask();}); mo.observe(document.body,{childList:true,subtree:true}); });');
}
add_action('wp_enqueue_scripts', 'add_phone_mask_script');

add_filter('upload_mimes', 'upload_allow_types');
function upload_allow_types($mimes)
{
    $mimes['ico'] = 'image/vnd.microsoft.icon';
    return $mimes;
}

// --- Полное снятие ограничений на загрузку SVG ---
add_filter('upload_mimes', function ($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
    return $mimes;
});

add_filter('wp_check_filetype_and_ext', function ($data, $file, $filename, $mimes, $real_mime = '') {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    if (in_array($ext, ['svg', 'svgz'])) {
        $data['ext'] = $ext;
        $data['type'] = 'image/svg+xml';
        $data['proper_filename'] = $filename;
    }
    return $data;
}, 10, 5);

add_filter('user_has_cap', function ($allcaps) {
    $allcaps['unfiltered_upload'] = true;
    return $allcaps;
});

add_action('admin_head', function () {
    echo '<style>
    .attachment-266x266, .thumbnail img[src$=".svg"] {
        width: 100% !important;
        height: auto !important;
    }
    </style>';
});

add_action('wp_ajax_update_cart_count', 'theme_update_cart_count');
add_action('wp_ajax_nopriv_update_cart_count', 'theme_update_cart_count');
function theme_update_cart_count()
{
    wp_send_json(['count' => WC()->cart->get_cart_contents_count()]);
}

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('ajax-cart', get_template_directory_uri() . '/js/ajax-cart.js', ['wc-cart-fragments'], '2.0', true);
    wp_localize_script('ajax-cart', 'ajaxCart', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'wc_ajax' => function_exists('wc_get_cart_url') ? str_replace('cart', '', wc_get_cart_url()) : site_url(),
    ]);
});

add_action("wp_ajax_submit_review", "submit_review_callback");
add_action("wp_ajax_nopriv_submit_review", "submit_review_callback");

function submit_review_callback()
{
    $rating = intval($_POST["rating"]);
    $comment = sanitize_textarea_field($_POST["comment"]);
    $author = sanitize_text_field($_POST["author"] ?? "Гость");
    $email = sanitize_email($_POST["email"]);
    $product_id = intval($_POST["product_id"]);

    if (!$rating || !$comment || !$email) {
        wp_send_json(["success" => false, "message" => "Заполните все поля."]);
    }

    $comment_id = wp_insert_comment([
        "comment_post_ID" => $product_id,
        "comment_author" => $author,
        "comment_author_email" => $email,
        "comment_content" => $comment,
        "comment_approved" => 0
    ]);

    if ($comment_id) {
        update_comment_meta($comment_id, "rating", $rating);
        wp_send_json(["success" => true]);
    }

    wp_send_json(["success" => false, "message" => "Ошибка сервера"]);
}

add_action('after_setup_theme', 'woocommerce_support');
function woocommerce_support()
{
    add_theme_support('woocommerce');
}

add_action('wp_ajax_update_cart_item_custom', 'theme_update_cart_item_custom');
add_action('wp_ajax_nopriv_update_cart_item_custom', 'theme_update_cart_item_custom');

function theme_update_cart_item_custom()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'custom-cart-nonce')) {
        wp_send_json_error(['message' => 'nonce']);
    }

    if (!WC()->cart) {
        wp_send_json_error(['message' => 'no_cart']);
    }

    $cart_key = isset($_POST['cart_item_key']) ? sanitize_text_field(wp_unslash($_POST['cart_item_key'])) : '';
    $qty = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 0;
    $qty = max(0, $qty);

    if ($cart_key === '') {
        wp_send_json_error(['message' => 'no_key']);
    }

    if ($qty <= 0) {
        WC()->cart->remove_cart_item($cart_key);
    } else {
        WC()->cart->set_quantity($cart_key, $qty, true);
    }

    WC()->cart->calculate_totals();

    $item = WC()->cart->get_cart()[$cart_key] ?? null;
    $line_total_fmt = '';
    $line_regular_fmt = '';
    $has_discount = false;
    $current_qty = $qty;

    if ($item) {
        $product = $item['data'];
        $current_qty = (int) $item['quantity'];
        $line_total = (float) $item['line_total'];
        $regular_unit = $product ? (float) $product->get_regular_price() : 0;
        $regular_total = $regular_unit * $current_qty;

        $has_discount = $regular_total > $line_total && $regular_total > 0;
        $line_total_fmt = wp_strip_all_tags(wc_price($line_total));
        $line_regular_fmt = $has_discount ? wp_strip_all_tags(wc_price($regular_total)) : '';
    }

    $total_regular = 0;
    $total_current = 0;

    foreach (WC()->cart->get_cart() as $ci) {
        $p = $ci['data'];
        if (!$p || !$p->exists())
            continue;
        $q = (int) $ci['quantity'];
        $regular_unit = (float) $p->get_regular_price();
        $line_total = (float) $ci['line_total'];
        $total_regular += $regular_unit * $q;
        $total_current += $line_total;
    }

    $discount_total = max(0, $total_regular - $total_current);

    wp_send_json_success([
        'quantity' => $current_qty,
        'line_total' => $line_total_fmt,
        'line_regular' => $line_regular_fmt,
        'has_discount' => $has_discount,
        'cart_count' => (int) WC()->cart->get_cart_contents_count(),
        'subtotal' => wp_strip_all_tags(wc_price(WC()->cart->get_subtotal())),
        'discount_total' => $discount_total,
        'discount_formatted' => $discount_total > 0 ? wp_strip_all_tags(wc_price($discount_total)) : '',
        'total' => wp_strip_all_tags(wc_price(WC()->cart->get_total('edit'))),
    ]);
}

add_filter('woocommerce_checkout_fields', function ($fields) {
    if (isset($fields['billing']['billing_last_name'])) {
        $fields['billing']['billing_last_name']['required'] = false;
    }
    if (isset($fields['billing']['billing_email'])) {
        $fields['billing']['billing_email']['required'] = false;
    }
    if (isset($fields['billing']['billing_city'])) {
        $fields['billing']['billing_city']['required'] = false;
    }
    if (isset($fields['billing']['billing_postcode'])) {
        $fields['billing']['billing_postcode']['required'] = false;
    }
    if (isset($fields['billing']['billing_country'])) {
        $fields['billing']['billing_country']['required'] = false;
    }
    return $fields;
});

add_action('wp_enqueue_scripts', function () {
    if (is_checkout()) {
        wp_dequeue_style('woocommerce-general');
        wp_dequeue_style('woocommerce-layout');
        wp_dequeue_style('woocommerce-smallscreen');
        wp_dequeue_style('select2');
        wp_dequeue_style('woocommerce_frontend_styles');
        wp_dequeue_style('woocommerce-inline');
        wp_dequeue_style('wc-blocks-style');
        wp_dequeue_style('wc-blocks-vendors-style');
        wp_dequeue_style('wc-blocks-style-layout');
        wp_dequeue_style('wc-blocks-style-cart');
        wp_dequeue_style('wc-blocks-checkout-style');
    }
}, 9999);

add_action('wp_enqueue_scripts', function () {
    if (is_checkout()) {
        wp_dequeue_script('wc-add-to-cart');
        wp_dequeue_script('wc-cart-fragments');
        wp_dequeue_script('woocommerce');
        wp_dequeue_script('wc-checkout');
        wp_dequeue_script('wc-country-select');
        wp_dequeue_script('wc-address-i18n');
        wp_dequeue_script('select2');
        wp_dequeue_script('wc-enhanced-select');
        wp_dequeue_script('wc-password-strength-meter');
        wp_dequeue_script('wc-credit-card-form');
    }
}, 9999);


/* =====================================================
 * ФИЛЬТР АТРИБУТОВ
 * ===================================================== */

/**
 * Получить slug таксономии атрибута по label (без учёта регистра)
 */
if (!function_exists('s140_attr_tax_slug_by_label')) {
    function s140_attr_tax_slug_by_label($label)
    {
        $label = mb_strtolower(trim($label), 'UTF-8');

        foreach (wc_get_attribute_taxonomies() as $a) {
            $attr_label = mb_strtolower($a->attribute_label, 'UTF-8');
            if ($attr_label === $label) {
                return 'pa_' . $a->attribute_name;
            }
        }

        return '';
    }
}

/**
 * Фильтрация на странице категории (pre_get_posts)
 */
add_action('pre_get_posts', function ($q) {
    if (is_admin() || !$q->is_main_query())
        return;
    if (!is_tax('product_cat'))
        return;

    $tax_query = (array) $q->get('tax_query');
    $meta_query = (array) $q->get('meta_query');

    $subcat = isset($_GET['subcat']) ? (int) $_GET['subcat'] : 0;
    if ($subcat > 0) {
        $tax_query[] = [
            'taxonomy' => 'product_cat',
            'field' => 'term_id',
            'terms' => [$subcat],
            'include_children' => true,
        ];
    }

    $color_tax = s140_attr_tax_slug_by_label('цвет');
    $mat_tax = s140_attr_tax_slug_by_label('материал');

    if ($color_tax && !empty($_GET['color'])) {
        $colors = [];
        if (is_array($_GET['color'])) {
            foreach ($_GET['color'] as $c) {
                $colors[] = sanitize_title(wp_unslash($c));
            }
        } else {
            $colors[] = sanitize_title(wp_unslash($_GET['color']));
        }
        if (!empty($colors)) {
            $tax_query[] = [
                'taxonomy' => $color_tax,
                'field' => 'slug',
                'terms' => $colors,
                'operator' => 'IN',
            ];
        }
    }

    if ($mat_tax && !empty($_GET['material'])) {
        $mats = [];
        if (is_array($_GET['material'])) {
            foreach ($_GET['material'] as $m) {
                $mats[] = sanitize_title(wp_unslash($m));
            }
        } else {
            $mats[] = sanitize_title(wp_unslash($_GET['material']));
        }
        if (!empty($mats)) {
            $tax_query[] = [
                'taxonomy' => $mat_tax,
                'field' => 'slug',
                'terms' => $mats,
                'operator' => 'IN',
            ];
        }
    }

    $min = isset($_GET['price_min']) ? (float) $_GET['price_min'] : 0;
    $max = isset($_GET['price_max']) ? (float) $_GET['price_max'] : 0;
    if ($min > 0 || $max > 0) {
        if ($min > 0 && $max > 0 && $max >= $min) {
            $meta_query[] = ['key' => '_price', 'value' => [$min, $max], 'compare' => 'BETWEEN', 'type' => 'DECIMAL(20,4)'];
        } elseif ($min > 0) {
            $meta_query[] = ['key' => '_price', 'value' => $min, 'compare' => '>=', 'type' => 'DECIMAL(20,4)'];
        } elseif ($max > 0) {
            $meta_query[] = ['key' => '_price', 'value' => $max, 'compare' => '<=', 'type' => 'DECIMAL(20,4)'];
        }
    }

    if (!empty($_GET['stock'])) {
        $meta_query[] = ['key' => '_stock_status', 'value' => 'instock'];
    }

    if ($tax_query)
        $q->set('tax_query', array_merge(['relation' => 'AND'], $tax_query));
    if ($meta_query)
        $q->set('meta_query', array_merge(['relation' => 'AND'], $meta_query));
}, 11);


/**
 * AJAX обработчик для фильтрации товаров
 */
add_action('wp_ajax_filter_products', 's140_ajax_filter_products');
add_action('wp_ajax_nopriv_filter_products', 's140_ajax_filter_products');

function s140_ajax_filter_products()
{
    $term_id = isset($_POST['term_id']) ? (int) $_POST['term_id'] : 0;
    $paged = isset($_POST['paged']) ? (int) $_POST['paged'] : 1;
    if (!$paged && isset($_POST['page'])) {
        $paged = (int) $_POST['page'];
    }
    $orderby = isset($_POST['orderby']) ? sanitize_text_field($_POST['orderby']) : 'date';

    $args = [
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => get_option('posts_per_page', 12),
        'paged' => $paged,
        'tax_query' => ['relation' => 'AND'],
        'meta_query' => ['relation' => 'AND'],
        's140_stock_first' => 1,
    ];

    // Devin: exclude already-loaded products to prevent duplicates on infinite scroll
    if ( ! empty( $_POST['loaded_ids'] ) && is_array( $_POST['loaded_ids'] ) ) {
        $exclude = array_values( array_unique( array_filter( array_map( 'intval', $_POST['loaded_ids'] ) ) ) );
        if ( ! empty( $exclude ) ) {
            $args['post__not_in'] = $exclude;
        }
    }


    // Добавляем tax_query по категории ТОЛЬКО если term_id > 0
    if ($term_id > 0) {
        $args['tax_query'][] = [
            'taxonomy' => 'product_cat',
            'field' => 'term_id',
            'terms' => $term_id,
            'include_children' => true,
        ];
    }

    // ПОДДЕРЖКА ПОИСКА — мягкий поиск по словам + ранжирование по позиции
    // первого слова в названии (та же логика что на странице /shop/?s=...)
    $search_words = [];
    if (!empty($_POST['s'])) {
        $search_term = sanitize_text_field($_POST['s']);
        $search_words = preg_split('/\s+/u', trim($search_term));
        $search_words = array_values(array_filter($search_words, function ($w) {
            return mb_strlen($w) >= 2;
        }));
        if (!empty($search_words)) {
            add_filter('posts_where', function ($where) use ($search_words) {
                global $wpdb;
                $clauses = [];
                $params  = [];
                foreach ($search_words as $w) {
                    $like = '%' . $wpdb->esc_like($w) . '%';
                    // Только название товара — иначе в описании несвязанных товаров
                    // встречаются упоминания ("подходит для льдогенератора") и они
                    // попадают в выдачу.
                    $clauses[] = "({$wpdb->posts}.post_title LIKE %s)";
                    $params[] = $like;
                }
                $where .= ' AND ' . implode(' AND ', $clauses);
                return $wpdb->prepare($where, $params);
            }, 10);
        }
    }

    // Сортировка
    switch ($orderby) {
        case 'price':
            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = '_price';
            $args['order'] = 'ASC';
            break;
        case 'price-desc':
            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = '_price';
            $args['order'] = 'DESC';
            break;
        case 'rating':
            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = '_wc_average_rating';
            $args['order'] = 'DESC';
            break;
        default:
            $args['orderby'] = 'date';
            $args['order'] = 'DESC';
    }

    // Фильтр по цвету
    $color_tax = s140_attr_tax_slug_by_label('цвет');
    if ($color_tax && !empty($_POST['color'])) {
        $colors = [];
        foreach ((array) $_POST['color'] as $c) {
            $colors[] = urldecode(wp_unslash($c));
        }
        if (!empty($colors)) {
            $args['tax_query'][] = [
                'taxonomy' => $color_tax,
                'field' => 'slug',
                'terms' => $colors,
                'operator' => 'IN',
            ];
        }
    }

    // Фильтр по материалу
    $mat_tax = s140_attr_tax_slug_by_label('материал');
    if ($mat_tax && !empty($_POST['material'])) {
        $mats = [];
        foreach ((array) $_POST['material'] as $m) {
            $mats[] = urldecode(wp_unslash($m));
        }
        if (!empty($mats)) {
            $args['tax_query'][] = [
                'taxonomy' => $mat_tax,
                'field' => 'slug',
                'terms' => $mats,
                'operator' => 'IN',
            ];
        }
    }

    // Динамические атрибуты (attr_brend, attr_strana и т.д.)
    $attribute_taxonomies = wc_get_attribute_taxonomies();
    foreach ($attribute_taxonomies as $attr) {
        $param_name = 'attr_' . $attr->attribute_name;

        if (!empty($_POST[$param_name])) {
            $taxonomy = wc_attribute_taxonomy_name($attr->attribute_name);
            $values = [];
            foreach ((array) $_POST[$param_name] as $v) {
                $values[] = urldecode(wp_unslash($v));
            }
            if (!empty($values)) {
                $args['tax_query'][] = [
                    'taxonomy' => $taxonomy,
                    'field' => 'slug',
                    'terms' => $values,
                    'operator' => 'IN',
                ];
            }
        }
    }

    // Фильтр по цене
    $price_min = isset($_POST['price_min']) && $_POST['price_min'] !== '' ? (float) $_POST['price_min'] : 0;
    $price_max = isset($_POST['price_max']) && $_POST['price_max'] !== '' ? (float) $_POST['price_max'] : 0;

    if ($price_min > 0 && $price_max > 0 && $price_max >= $price_min) {
        $args['meta_query'][] = [
            'key' => '_price',
            'value' => [$price_min, $price_max],
            'compare' => 'BETWEEN',
            'type' => 'DECIMAL(20,4)',
        ];
    } elseif ($price_min > 0) {
        $args['meta_query'][] = [
            'key' => '_price',
            'value' => $price_min,
            'compare' => '>=',
            'type' => 'DECIMAL(20,4)',
        ];
    } elseif ($price_max > 0) {
        $args['meta_query'][] = [
            'key' => '_price',
            'value' => $price_max,
            'compare' => '<=',
            'type' => 'DECIMAL(20,4)',
        ];
    }

    // Фильтр по наличию
    if (!empty($_POST['stock'])) {
        $args['meta_query'][] = [
            'key' => '_stock_status',
            'value' => 'instock',
        ];
    }

    // Очищаем пустые tax_query и meta_query
    if (count($args['tax_query']) === 1) {
        unset($args['tax_query']);
    }
    if (count($args['meta_query']) === 1) {
        unset($args['meta_query']);
    }

    // Ранжирование результатов поиска: товар где первое слово запроса стоит
    // в начале названия — выше всех. Применяем только при дефолтной сортировке,
    // чтобы не переопределять явный выбор пользователя "по цене/рейтингу".
    if (!empty($search_words) && $orderby === 'date') {
        add_filter('posts_orderby', function ($orderby_sql) use ($search_words) {
            global $wpdb;
            $first = $search_words[0];
            $like_full = $wpdb->esc_like($first);
            $score_sql = $wpdb->prepare(
                "(CASE
                    WHEN LOWER({$wpdb->posts}.post_title) LIKE LOWER(%s) THEN 100
                    WHEN LOWER({$wpdb->posts}.post_title) LIKE LOWER(%s) THEN 50
                    ELSE 0
                 END) DESC",
                $like_full . '%',
                '%' . $like_full . '%'
            );
            return $score_sql . ($orderby_sql ? ', ' . $orderby_sql : '');
        }, 10);
    }

    $query = new WP_Query($args);

    ob_start();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            get_template_part('template-parts/product', 'card');
        }
    } else {
        echo '<p class="no-products-message">Товары не найдены. Попробуйте изменить параметры фильтра.</p>';
    }
    $html = ob_get_clean();

    wp_reset_postdata();

    wp_send_json_success([
        'html' => $html,
        'found' => (int) $query->found_posts,
        'max_pages' => (int) $query->max_num_pages,
        'current_page' => $paged,
    ]);
}


/**
 * AJAX обработчик для Infinite Scroll
 */
add_action('wp_ajax_load_more_products', 's140_ajax_load_more_products');
add_action('wp_ajax_nopriv_load_more_products', 's140_ajax_load_more_products');

function s140_ajax_load_more_products()
{
    error_log('AJAX load_more: s=' . ($_POST['s'] ?? 'НЕТ'));
    error_log('AJAX load_more: $_POST=' . print_r($_POST, true));

    $term_id = isset($_POST['term_id']) ? (int) $_POST['term_id'] : 0;
    $paged = isset($_POST['paged']) ? (int) $_POST['paged'] : 1;
    $orderby = isset($_POST['orderby']) ? sanitize_text_field($_POST['orderby']) : 'date';

    $args = [
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => get_option('posts_per_page', 12),
        'paged' => $paged,
        'meta_query' => ['relation' => 'AND'],
        's140_stock_first' => 1,
    ];

    // Devin: exclude already-loaded product IDs to prevent infinite-scroll duplicates.
    // When loaded_ids is provided, ignore paged/offset entirely and rely only on post__not_in
    // (initial page size may differ from AJAX page size, so paged-based offset would cause dupes).
    if ( ! empty( $_POST['loaded_ids'] ) && is_array( $_POST['loaded_ids'] ) ) {
        $exclude = array_values( array_unique( array_filter( array_map( 'intval', $_POST['loaded_ids'] ) ) ) );
        if ( ! empty( $exclude ) ) {
            $args['post__not_in'] = $exclude;
            $args['paged']        = 1;
            $args['offset']       = 0;
        }
    }

    if ($term_id > 0) {
        $args['tax_query'] = [
            'relation' => 'AND',
            [
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $term_id,
                'include_children' => true,
            ]
        ];
    } else {
        $args['tax_query'] = [];
    }

    switch ($orderby) {
        case 'price':
            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = '_price';
            $args['order'] = 'ASC';
            break;

        case 'price-desc':
            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = '_price';
            $args['order'] = 'DESC';
            break;

        case 'rating':
            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = '_wc_average_rating';
            $args['order'] = 'DESC';
            break;

        default:
            $args['orderby'] = 'date';
            $args['order'] = 'DESC';
            break;
    }

    $color_tax = s140_attr_tax_slug_by_label('цвет');
    if ($color_tax && !empty($_POST['color'])) {
        $colors = [];
        foreach ((array) $_POST['color'] as $c) {
            $colors[] = urldecode(wp_unslash($c));
        }
        if (!empty($colors)) {
            if (!isset($args['tax_query'])) {
                $args['tax_query'] = ['relation' => 'AND'];
            }
            $args['tax_query'][] = [
                'taxonomy' => $color_tax,
                'field' => 'slug',
                'terms' => $colors,
                'operator' => 'IN',
            ];
        }
    }

    $mat_tax = s140_attr_tax_slug_by_label('материал');
    if ($mat_tax && !empty($_POST['material'])) {
        $mats = [];
        foreach ((array) $_POST['material'] as $m) {
            $mats[] = urldecode(wp_unslash($m));
        }
        if (!empty($mats)) {
            if (!isset($args['tax_query'])) {
                $args['tax_query'] = ['relation' => 'AND'];
            }
            $args['tax_query'][] = [
                'taxonomy' => $mat_tax,
                'field' => 'slug',
                'terms' => $mats,
                'operator' => 'IN',
            ];
        }
    }

    // Динамические атрибуты
    $attribute_taxonomies = wc_get_attribute_taxonomies();
    foreach ($attribute_taxonomies as $attr) {
        $param_name = 'attr_' . $attr->attribute_name;

        if (!empty($_POST[$param_name])) {
            $taxonomy = wc_attribute_taxonomy_name($attr->attribute_name);
            $values = [];
            foreach ((array) $_POST[$param_name] as $v) {
                $values[] = urldecode(wp_unslash($v));
            }
            if (!empty($values)) {
                if (!isset($args['tax_query'])) {
                    $args['tax_query'] = ['relation' => 'AND'];
                }
                $args['tax_query'][] = [
                    'taxonomy' => $taxonomy,
                    'field' => 'slug',
                    'terms' => $values,
                    'operator' => 'IN',
                ];
            }
        }
    }

    $price_min = isset($_POST['price_min']) && $_POST['price_min'] !== '' ? (float) $_POST['price_min'] : 0;
    $price_max = isset($_POST['price_max']) && $_POST['price_max'] !== '' ? (float) $_POST['price_max'] : 0;

    if ($price_min > 0 && $price_max > 0 && $price_max >= $price_min) {
        $args['meta_query'][] = [
            'key' => '_price',
            'value' => [$price_min, $price_max],
            'compare' => 'BETWEEN',
            'type' => 'DECIMAL(20,4)',
        ];
    } elseif ($price_min > 0) {
        $args['meta_query'][] = [
            'key' => '_price',
            'value' => $price_min,
            'compare' => '>=',
            'type' => 'DECIMAL(20,4)',
        ];
    } elseif ($price_max > 0) {
        $args['meta_query'][] = [
            'key' => '_price',
            'value' => $price_max,
            'compare' => '<=',
            'type' => 'DECIMAL(20,4)',
        ];
    }

    if (!empty($_POST['stock'])) {
        $args['meta_query'][] = [
            'key' => '_stock_status',
            'value' => 'instock',
        ];
    }

    if (count($args['meta_query']) === 1) {
        unset($args['meta_query']);
    }

    // ПОДДЕРЖКА ПОИСКА — мягкий поиск по словам + ранжирование по позиции
    // первого слова в названии (та же логика что на странице /shop/?s=...)
    $search_words = [];
    if (!empty($_POST['s'])) {
        $search_term = sanitize_text_field($_POST['s']);
        $search_words = preg_split('/\s+/u', trim($search_term));
        $search_words = array_values(array_filter($search_words, function ($w) {
            return mb_strlen($w) >= 2;
        }));
        if (!empty($search_words)) {
            add_filter('posts_where', function ($where) use ($search_words) {
                global $wpdb;
                $clauses = [];
                $params  = [];
                foreach ($search_words as $w) {
                    $like = '%' . $wpdb->esc_like($w) . '%';
                    $clauses[] = "({$wpdb->posts}.post_title LIKE %s)";
                    $params[] = $like;
                }
                $where .= ' AND ' . implode(' AND ', $clauses);
                return $wpdb->prepare($where, $params);
            }, 10);
        }
    }

    // Ранжирование: первое слово в начале названия — выше всех. Только при
    // дефолтной сортировке, чтобы не переопределять явный выбор пользователя.
    if (!empty($search_words) && $orderby === 'date') {
        add_filter('posts_orderby', function ($orderby_sql) use ($search_words) {
            global $wpdb;
            $first = $search_words[0];
            $like_full = $wpdb->esc_like($first);
            $score_sql = $wpdb->prepare(
                "(CASE
                    WHEN LOWER({$wpdb->posts}.post_title) LIKE LOWER(%s) THEN 100
                    WHEN LOWER({$wpdb->posts}.post_title) LIKE LOWER(%s) THEN 50
                    ELSE 0
                 END) DESC",
                $like_full . '%',
                '%' . $like_full . '%'
            );
            return $score_sql . ($orderby_sql ? ', ' . $orderby_sql : '');
        }, 10);
    }

    $query = new WP_Query($args);

    ob_start();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            get_template_part('template-parts/product', 'card');
        }
    }
    $html = ob_get_clean();

    wp_reset_postdata();

    wp_send_json_success([
        'html' => $html,
        'max_pages' => $query->max_num_pages,
        'current_page' => $paged,
        'found_posts' => $query->found_posts,
        'debug' => [
            's_param' => $_POST['s'] ?? 'НЕТ',
            'search_used' => !empty($_POST['s']) ? 'ДА' : 'НЕТ',
            'all_post' => array_keys($_POST)
        ]
    ]);
}

/* =====================================================
 * LIVE SEARCH
 * ===================================================== */

add_action('wp_enqueue_scripts', 'theme_search_scripts');
function theme_search_scripts()
{
    wp_enqueue_script(
        'theme-search',
        get_template_directory_uri() . '/js/theme-search.js',
        [],
        '1.0',
        true
    );

    wp_localize_script(
        'theme-search',
        'ThemeSearch',
        [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('theme_search_nonce'),
        ]
    );
}

add_action('wp_ajax_theme_live_search', 'theme_live_search');
add_action('wp_ajax_nopriv_theme_live_search', 'theme_live_search');

function theme_live_search()
{
    check_ajax_referer('theme_search_nonce', 'nonce');

    $term = isset($_POST['term']) ? sanitize_text_field(wp_unslash($_POST['term'])) : '';

    if (mb_strlen($term) < 2) {
        wp_send_json_success(['html' => '', 'mobileHtml' => '', 'count' => 0]);
    }

    if (!class_exists('WooCommerce')) {
        wp_send_json_success(['html' => '', 'mobileHtml' => '', 'count' => 0]);
    }

    // Разбиваем на слова (>=2 символа), ищем по каждому через AND (мягкий поиск)
    $words = preg_split('/\s+/u', trim($term));
    $words = array_values(array_filter($words, function ($w) {
        return mb_strlen($w) >= 2;
    }));

    $args = [
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => 30,
        'no_found_rows' => true,
    ];

    if (empty($words)) {
        // короткий запрос — оставляем штатный поиск
        $args['s'] = $term;
        $args['orderby'] = 'relevance';
        $query = new WP_Query($args);
    } else {
        // Ранжирование: товар где первое слово стоит в начале названия — выше всех
        $where_cb = function ($where) use ($words) {
            global $wpdb;
            $clauses = [];
            $params = [];
            foreach ($words as $w) {
                $like = '%' . $wpdb->esc_like($w) . '%';
                $clauses[] = "({$wpdb->posts}.post_title LIKE %s)";
                $params[] = $like;
            }
            $where .= ' AND ' . implode(' AND ', $clauses);
            return $wpdb->prepare($where, $params);
        };
        $orderby_cb = function ($orderby) use ($words) {
            global $wpdb;
            $first = $words[0];
            $like_full = $wpdb->esc_like($first);
            $score_sql = $wpdb->prepare(
                "(CASE
                    WHEN LOWER({$wpdb->posts}.post_title) LIKE LOWER(%s) THEN 100
                    WHEN LOWER({$wpdb->posts}.post_title) LIKE LOWER(%s) THEN 50
                    ELSE 0
                  END) DESC",
                $like_full . '%',
                '%' . $like_full . '%'
            );
            return $score_sql . ($orderby ? ', ' . $orderby : '');
        };
        add_filter('posts_where', $where_cb, 10);
        add_filter('posts_orderby', $orderby_cb, 10);
        $query = new WP_Query($args);
        remove_filter('posts_where', $where_cb, 10);
        remove_filter('posts_orderby', $orderby_cb, 10);
    }

    // ========== ДЕСКТОПНЫЙ HTML ==========
    ob_start();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            $product = wc_get_product(get_the_ID());
            if (!$product)
                continue;

            $permalink = get_the_permalink();
            $title = get_the_title();
            $img_url = get_the_post_thumbnail_url(get_the_ID(), 'woocommerce_thumbnail');
            $price = $product->get_price();
            $regular = $product->get_regular_price();
            ?>
            <a class="search-modal-content-product" href="<?php echo esc_url($permalink); ?>">
                <div class="search-modal-content-product__img">
                    <?php if ($img_url): ?>
                        <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($title); ?>">
                    <?php endif; ?>
                </div>
                <div class="search-modal-content-product__info">
                    <div class="cart-product__info-price__wrapper">
                        <?php if ($price !== ''): ?>
                            <p class="cart-product__info-price"><?php echo wp_kses_post(wc_price($price)); ?></p>
                        <?php endif; ?>
                        <?php if ($regular && $regular > $price): ?>
                            <p class="cart-product__info-old-price"><?php echo wp_kses_post(wc_price($regular)); ?></p>
                        <?php endif; ?>
                    </div>
                    <p class="cart-product__info-title search-modal-content-product__title"><?php echo esc_html($title); ?></p>
                </div>
            </a>
            <?php
        }
    } else {
        ?>
        <p class="search-modal__empty">По вашему запросу ничего не найдено.</p>
        <?php
    }
    $html = ob_get_clean();

    // ========== МОБИЛЬНЫЙ HTML (карточки товаров) ==========
    ob_start();
    if ($query->have_posts()) {
        $query->rewind_posts();
        while ($query->have_posts()) {
            $query->the_post();

            // Устанавливаем глобальные переменные для product-card.php
            global $product;
            $product = wc_get_product(get_the_ID());

            if ($product) {
                get_template_part('template-parts/product', 'card');
            }
        }
    } else {
        ?>
        <p class="search-modal__empty">По вашему запросу ничего не найдено.</p>
        <?php
    }
    $mobileHtml = ob_get_clean();

    wp_reset_postdata();

    wp_send_json_success([
        'html' => $html,
        'mobileHtml' => $mobileHtml,
        'count' => (int) $query->found_posts,
    ]);
}


/* =====================================================
 * YITH WISHLIST FIXES
 * ===================================================== */

add_filter('yith_wcwl_localize_script', function ($localize) {
    $localize['is_wishlist_responsive'] = false;
    return $localize;
});


/* =====================================================
 * MODAL VALIDATION SCRIPT
 * ===================================================== */

add_action('wp_enqueue_scripts', function () {
    $rel = '/js/modal-validation.js';
    $abs = get_template_directory() . $rel;
    wp_enqueue_script(
        'modal-validation',
        get_template_directory_uri() . $rel,
        array(),
        file_exists($abs) ? filemtime($abs) : '1.0.0',
        true
    );
    wp_localize_script('modal-validation', 's140Forms', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('s140_submit_form'),
    ]);
});

add_filter('yith_wcwl_show_popup_after_add_to_wishlist', '__return_false');

add_action('wp_head', function () {
    ?>
    <style>
        #yith-wcwl-popup-message,
        .yith-wcwl-popup-message {
            display: none !important;
        }
    </style>
    <?php
}, 999);

/**
 * Подключение Infinite Scroll (принудительно с отладкой)
 */
// ОТКЛЮЧЕНО: external infinite-scroll.js дублировал инлайн-скрипт бесконечного скролла
// в archive-product.php и taxonomy-product_cat.php, что вызывало дубли товаров.
// Теперь вся логика бесконечного скролла только в инлайн-скриптах шаблонов.
/*
add_action('wp_footer', function () {
    if (is_shop() || is_product_category() || is_product_tag()) {
        $script_path = get_template_directory() . '/js/infinite-scroll.js';
        $script_url = get_template_directory_uri() . '/js/infinite-scroll.js';

        if (file_exists($script_path)) {
            $version = filemtime($script_path);
            ?>
            <script src="<?php echo esc_url($script_url); ?>?v=<?php echo $version; ?>"></script>
            <?php
        }
    }
}, 999);
*/

add_action('wp_enqueue_scripts', function () {
    if (is_product()) {
        wp_enqueue_style('fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css');
        wp_enqueue_script('fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js', [], '5.0', true);
    }
});

// Подключаем кастомные стили для Избранного и Сравнения
function sklad140_wishlist_compare_styles()
{
    // Подключаем на страницах избранного
    if (function_exists('yith_wcwl_is_wishlist_page') && yith_wcwl_is_wishlist_page()) {
        wp_enqueue_style(
            'sklad140-wishlist-compare',
            get_template_directory_uri() . '/css/wishlist-compare.css',
            array(),
            '1.0.0'
        );
    }

    // Подключаем на страницах сравнения
    if (isset($_GET['action']) && $_GET['action'] === 'yith-woocompare-view-table') {
        wp_enqueue_style(
            'sklad140-wishlist-compare',
            get_template_directory_uri() . '/css/wishlist-compare.css',
            array(),
            '1.0.0'
        );
    }

    // Также подключаем на странице сравнения по URL
    global $wp;
    $current_url = home_url($wp->request);
    if (strpos($current_url, '/compare') !== false || strpos($current_url, 'action=yith') !== false) {
        wp_enqueue_style(
            'sklad140-wishlist-compare',
            get_template_directory_uri() . '/css/wishlist-compare.css',
            array(),
            '1.0.0'
        );
    }
}
add_action('wp_enqueue_scripts', 'sklad140_wishlist_compare_styles');

/**
 * ========================================
 * YITH Wishlist - Кастомные AJAX handlers v2
 * БЕЗОПАСНАЯ ВЕРСИЯ с проверками
 * ========================================
 */

add_action('wp_ajax_force_remove_from_wishlist', 'custom_force_remove_from_wishlist');
add_action('wp_ajax_nopriv_force_remove_from_wishlist', 'custom_force_remove_from_wishlist');

function custom_force_remove_from_wishlist()
{
    @ini_set('display_errors', 0);
    error_reporting(0);

    if (ob_get_level()) {
        ob_end_clean();
    }
    ob_start();

    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $user_id = get_current_user_id();

    // Debug info
    $debug = array(
        'product_id' => $product_id,
        'user_id' => $user_id,
        'steps' => array()
    );

    if (!$product_id) {
        ob_end_clean();
        wp_send_json_error(['message' => 'No product ID', 'debug' => $debug]);
        return;
    }

    $removed = false;
    $count = 0;

    global $wpdb;
    $table = $wpdb->prefix . 'yith_wcwl';

    // Проверяем есть ли товар в wishlist
    $exists = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table WHERE prod_id = %d AND user_id = %d",
        $product_id,
        $user_id
    ));

    $debug['steps'][] = 'Item exists in DB: ' . ($exists ? 'YES (ID: ' . $exists->ID . ')' : 'NO');

    if ($exists) {
        // Удаляем
        $deleted = $wpdb->delete($table, array(
            'prod_id' => $product_id,
            'user_id' => $user_id
        ));

        $debug['steps'][] = 'Delete result: ' . ($deleted !== false ? $deleted . ' rows' : 'FAILED - ' . $wpdb->last_error);
        $removed = ($deleted !== false && $deleted > 0);
    }

    // Получаем счетчик
    if (function_exists('yith_wcwl_count_products')) {
        $count = (int) yith_wcwl_count_products();
    } else {
        $count = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE user_id = %d",
            $user_id
        ));
    }

    $debug['steps'][] = 'Final count: ' . $count;

    ob_end_clean();
    wp_send_json_success([
        'removed' => (bool) $removed,
        'product_id' => $product_id,
        'count' => (int) $count,
        'debug' => $debug
    ]);
}

// Получение счетчика wishlist (ИСПРАВЛЕННАЯ версия)
add_action('wp_ajax_get_wishlist_count', 'custom_get_wishlist_count');
add_action('wp_ajax_nopriv_get_wishlist_count', 'custom_get_wishlist_count');

function custom_get_wishlist_count()
{
    // Отключаем вывод ошибок в JSON ответ
    @ini_set('display_errors', 0);
    error_reporting(0);

    // Очищаем любой предыдущий вывод
    if (ob_get_level()) {
        ob_end_clean();
    }
    ob_start();

    $count = 0;

    try {
        // Проверяем что WooCommerce активен
        if (!class_exists('WooCommerce')) {
            ob_end_clean();
            wp_send_json_success(['count' => 0]);
            return;
        }

        // Способ 1: Стандартная функция YITH
        if (function_exists('yith_wcwl_count_products')) {
            $count = (int) yith_wcwl_count_products();
        }
        // Способ 2: Через объект YITH
        elseif (function_exists('YITH_WCWL') && YITH_WCWL()) {
            $wcwl = YITH_WCWL();
            if (method_exists($wcwl, 'count_products')) {
                $count = (int) $wcwl->count_products();
            }
        }
        // Способ 3: Напрямую из БД
        else {
            global $wpdb;
            $table = $wpdb->prefix . 'yith_wcwl';

            // Проверяем существование таблицы
            $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table'");

            if ($table_exists) {
                $user_id = get_current_user_id();

                if ($user_id) {
                    $count = (int) $wpdb->get_var($wpdb->prepare(
                        "SELECT COUNT(*) FROM $table WHERE user_id = %d",
                        $user_id
                    ));
                }
            }
        }

    } catch (Exception $e) {
        $count = 0;
    }

    // Очищаем буфер и отправляем чистый JSON
    ob_end_clean();
    wp_send_json_success(['count' => (int) $count]);
}

/**
 * МЯГКИЙ ПОИСК ПО НАЗВАНИЮ ТОВАРА — каждое слово через LIKE (AND).
 * Работает как live-search в шапке: "стол морозильный hicold 11" найдёт
 * "Стол морозильный Hicold 111 BT" независимо от порядка слов и лишних пробелов.
 */
add_action('pre_get_posts', function ($query) {
    if (is_admin() || !$query->is_main_query()) {
        return;
    }
    $search_term = $query->get('s');
    if (empty($search_term)) {
        return;
    }
    $post_type = $query->get('post_type');
    if ($post_type !== 'product') {
        return;
    }

    // Разбиваем запрос на слова (>= 2 символа), без стандартного WP-поиска
    $words = preg_split('/\s+/u', trim($search_term));
    $words = array_values(array_filter($words, function ($w) {
        return mb_strlen($w) >= 2;
    }));
    if (empty($words)) {
        return; // оставляем штатный WP search (он отработает на коротком запросе)
    }

    $query->set('s', '');

    add_filter('posts_where', function ($where) use ($words) {
        global $wpdb;
        $clauses = [];
        $params  = [];
        foreach ($words as $w) {
            $like = '%' . $wpdb->esc_like($w) . '%';
            // Ищем только в названии — иначе несвязанные товары попадают через
            // упоминание в описании ("подходит для льдогенератора" и т.п.).
            $clauses[] = "({$wpdb->posts}.post_title LIKE %s)";
            $params[] = $like;
        }
        $where .= ' AND ' . implode(' AND ', $clauses);
        return $wpdb->prepare($where, $params);
    }, 10);

    // Релевантность: товары где первое слово запроса стоит в начале названия — выше.
    // Стоковость и дата — вторичные критерии. Только когда пользователь не выбрал
    // явную сортировку (orderby пустой, 'date', 'menu_order' или 'relevance').
    $orderby_param = $query->get('orderby');
    $allowed_overrides = ['', 'date', 'menu_order', 'relevance', 'menu_order title'];
    if (!in_array($orderby_param, $allowed_overrides, true)) {
        return;
    }
    add_filter('posts_orderby', function ($orderby) use ($words) {
        global $wpdb;
        $first = $words[0];
        $like_full = $wpdb->esc_like($first);
        $score_sql = $wpdb->prepare(
            "(CASE
                WHEN LOWER({$wpdb->posts}.post_title) LIKE LOWER(%s) THEN 100
                WHEN LOWER({$wpdb->posts}.post_title) LIKE LOWER(%s) THEN 50
                ELSE 0
              END) DESC",
            $like_full . '%',
            '%' . $like_full . '%'
        );
        return $score_sql . ($orderby ? ', ' . $orderby : '');
    }, 10);
}, 20);

/**
 * Карточка товара: показываем ВСЕ атрибуты (включая is_visible=0).
 * Bulk-editor создаёт атрибуты с is_visible=0 по умолчанию, из-за чего они
 * появляются в листинге каталога, но пропадают в карточке. Чиним именно
 * отображение, не трогая is_visible в БД.
 */
add_filter('woocommerce_display_product_attributes', function ($product_attributes, $product) {
    if (!is_a($product, 'WC_Product')) {
        return $product_attributes;
    }
    foreach ($product->get_attributes() as $attribute) {
        $name = $attribute->get_name();
        $key  = 'attribute_' . sanitize_title_with_dashes($name);
        if (isset($product_attributes[$key])) {
            continue; // уже добавлен (был visible=1)
        }
        if ($attribute->is_taxonomy()) {
            $tax_obj = $attribute->get_taxonomy_object();
            $terms   = wc_get_product_terms($product->get_id(), $name, ['fields' => 'all']);
            if (empty($terms)) {
                continue;
            }
            $values = [];
            foreach ($terms as $t) {
                $val = esc_html($t->name);
                if (!empty($tax_obj) && !empty($tax_obj->attribute_public)) {
                    $val = '<a href="' . esc_url(get_term_link($t->term_id, $name)) . '" rel="tag">' . $val . '</a>';
                }
                $values[] = $val;
            }
        } else {
            $values = $attribute->get_options();
            if (empty($values)) {
                continue;
            }
            foreach ($values as &$v) {
                $v = make_clickable(esc_html($v));
            }
        }
        $product_attributes[$key] = [
            'label' => wc_attribute_label($name),
            'value' => apply_filters('woocommerce_attribute', wpautop(wptexturize(implode(', ', $values))), $attribute, $values),
        ];
    }
    return $product_attributes;
}, 20, 2);

// Замена "Product Info" на "Товар" в таблице сравнения
add_filter('gettext', function ($translated, $text, $domain) {
    if ($domain === 'yith-woocommerce-compare' && $text === 'Product Info') {
        return 'Товар';
    }
    return $translated;
}, 10, 3);



// Хлеб.крош.

function sklad140_get_breadcrumbs() {
    if (is_front_page()) {
        return [];
    }

    $breadcrumbs = [];
    $breadcrumbs[] = [
        'title' => 'Главная',
        'url'   => home_url('/'),
    ];

    if (function_exists('is_shop') && is_shop()) {
        $breadcrumbs[] = [
            'title' => get_the_title(wc_get_page_id('shop')),
            'url'   => '',
        ];
        return $breadcrumbs;
    }

    if (is_home()) {
        $breadcrumbs[] = [
            'title' => get_the_title(get_option('page_for_posts')),
            'url'   => '',
        ];
        return $breadcrumbs;
    }

    if (is_page()) {
        global $post;

        if ($post->post_parent) {
            $parents = array_reverse(get_post_ancestors($post->ID));
            foreach ($parents as $parent_id) {
                $breadcrumbs[] = [
                    'title' => get_the_title($parent_id),
                    'url'   => get_permalink($parent_id),
                ];
            }
        }

        $breadcrumbs[] = [
            'title' => get_the_title($post->ID),
            'url'   => '',
        ];

        return $breadcrumbs;
    }

    if (is_singular('product')) {
        global $post;

        $shop_page_id = function_exists('wc_get_page_id') ? wc_get_page_id('shop') : 0;
        if ($shop_page_id && $shop_page_id > 0) {
            $breadcrumbs[] = [
                'title' => get_the_title($shop_page_id),
                'url'   => get_permalink($shop_page_id),
            ];
        }

        $terms = get_the_terms($post->ID, 'product_cat');
        if ($terms && !is_wp_error($terms)) {
            $term = null;

            foreach ($terms as $t) {
                if ($t->parent != 0) {
                    $term = $t;
                    break;
                }
            }

            if (!$term) {
                $term = reset($terms);
            }

            if ($term) {
                $ancestors = array_reverse(get_ancestors($term->term_id, 'product_cat'));
                foreach ($ancestors as $ancestor_id) {
                    $ancestor = get_term($ancestor_id, 'product_cat');
                    if ($ancestor && !is_wp_error($ancestor)) {
                        $breadcrumbs[] = [
                            'title' => $ancestor->name,
                            'url'   => get_term_link($ancestor),
                        ];
                    }
                }

                $breadcrumbs[] = [
                    'title' => $term->name,
                    'url'   => get_term_link($term),
                ];
            }
        }

        $breadcrumbs[] = [
            'title' => get_the_title($post->ID),
            'url'   => '',
        ];

        return $breadcrumbs;
    }

    if (is_singular()) {
        global $post;
        $post_type = get_post_type($post);

        if ($post_type === 'post') {
            $page_for_posts = get_option('page_for_posts');
            if ($page_for_posts) {
                $breadcrumbs[] = [
                    'title' => get_the_title($page_for_posts),
                    'url'   => get_permalink($page_for_posts),
                ];
            }

            $cats = get_the_category($post->ID);
            if ($cats) {
                $cat = $cats[0];
                if ($cat->parent) {
                    $ancestors = array_reverse(get_ancestors($cat->term_id, 'category'));
                    foreach ($ancestors as $ancestor_id) {
                        $ancestor = get_category($ancestor_id);
                        if ($ancestor) {
                            $breadcrumbs[] = [
                                'title' => $ancestor->name,
                                'url'   => get_category_link($ancestor->term_id),
                            ];
                        }
                    }
                }

                $breadcrumbs[] = [
                    'title' => $cat->name,
                    'url'   => get_category_link($cat->term_id),
                ];
            }
        } else {
            $post_type_obj = get_post_type_object($post_type);
            if ($post_type_obj && !empty($post_type_obj->has_archive)) {
                $breadcrumbs[] = [
                    'title' => $post_type_obj->labels->name,
                    'url'   => get_post_type_archive_link($post_type),
                ];
            }
        }

        $breadcrumbs[] = [
            'title' => get_the_title($post->ID),
            'url'   => '',
        ];

        return $breadcrumbs;
    }

    if (is_category()) {
        $cat = get_queried_object();
        if ($cat && !is_wp_error($cat)) {
            $page_for_posts = get_option('page_for_posts');
            if ($page_for_posts) {
                $breadcrumbs[] = [
                    'title' => get_the_title($page_for_posts),
                    'url'   => get_permalink($page_for_posts),
                ];
            }

            if ($cat->parent) {
                $ancestors = array_reverse(get_ancestors($cat->term_id, 'category'));
                foreach ($ancestors as $ancestor_id) {
                    $ancestor = get_category($ancestor_id);
                    if ($ancestor) {
                        $breadcrumbs[] = [
                            'title' => $ancestor->name,
                            'url'   => get_category_link($ancestor->term_id),
                        ];
                    }
                }
            }

            $breadcrumbs[] = [
                'title' => single_cat_title('', false),
                'url'   => '',
            ];
        }

        return $breadcrumbs;
    }

    if (is_tax('product_cat')) {
        $term = get_queried_object();

        $shop_page_id = function_exists('wc_get_page_id') ? wc_get_page_id('shop') : 0;
        if ($shop_page_id && $shop_page_id > 0) {
            $breadcrumbs[] = [
                'title' => get_the_title($shop_page_id),
                'url'   => get_permalink($shop_page_id),
            ];
        }

        if ($term && !is_wp_error($term)) {
            if ($term->parent) {
                $ancestors = array_reverse(get_ancestors($term->term_id, 'product_cat'));
                foreach ($ancestors as $ancestor_id) {
                    $ancestor = get_term($ancestor_id, 'product_cat');
                    if ($ancestor && !is_wp_error($ancestor)) {
                        $breadcrumbs[] = [
                            'title' => $ancestor->name,
                            'url'   => get_term_link($ancestor),
                        ];
                    }
                }
            }

            $breadcrumbs[] = [
                'title' => $term->name,
                'url'   => '',
            ];
        }

        return $breadcrumbs;
    }

    if (is_tax() || is_tag()) {
        $term = get_queried_object();
        if ($term && !is_wp_error($term)) {
            $taxonomy = get_taxonomy($term->taxonomy);

            if ($taxonomy && !empty($taxonomy->object_type[0])) {
                $post_type = $taxonomy->object_type[0];
                $post_type_obj = get_post_type_object($post_type);

                if ($post_type_obj && !empty($post_type_obj->has_archive)) {
                    $breadcrumbs[] = [
                        'title' => $post_type_obj->labels->name,
                        'url'   => get_post_type_archive_link($post_type),
                    ];
                }
            }

            if ($term->parent) {
                $ancestors = array_reverse(get_ancestors($term->term_id, $term->taxonomy));
                foreach ($ancestors as $ancestor_id) {
                    $ancestor = get_term($ancestor_id, $term->taxonomy);
                    if ($ancestor && !is_wp_error($ancestor)) {
                        $breadcrumbs[] = [
                            'title' => $ancestor->name,
                            'url'   => get_term_link($ancestor),
                        ];
                    }
                }
            }

            $breadcrumbs[] = [
                'title' => $term->name,
                'url'   => '',
            ];
        }

        return $breadcrumbs;
    }

    if (is_post_type_archive()) {
        $post_type = get_query_var('post_type');
        if (is_array($post_type)) {
            $post_type = reset($post_type);
        }

        $post_type_obj = get_post_type_object($post_type);
        if ($post_type_obj) {
            $breadcrumbs[] = [
                'title' => $post_type_obj->labels->name,
                'url'   => '',
            ];
        }

        return $breadcrumbs;
    }

    if (is_search()) {
        $breadcrumbs[] = [
            'title' => 'Поиск',
            'url'   => '',
        ];
        return $breadcrumbs;
    }

    if (is_404()) {
        $breadcrumbs[] = [
            'title' => '404',
            'url'   => '',
        ];
        return $breadcrumbs;
    }

    return $breadcrumbs;
}


add_filter('posts_clauses', 's140_stock_first_catalog_order', 999, 2);
function s140_stock_first_catalog_order($clauses, $query) {
    global $wpdb;

    if (is_admin() && !wp_doing_ajax()) {
        return $clauses;
    }

    $is_catalog_main =
        $query->is_main_query() &&
        (
            is_shop() ||
            is_product_taxonomy() ||
            is_product_category() ||
            is_tax('product_cat')
        );

    $is_ajax_catalog = !empty($query->get('s140_stock_first'));

    if (!$is_catalog_main && !$is_ajax_catalog) {
        return $clauses;
    }

    if (($query->get('post_type') !== 'product') && !$is_catalog_main) {
        return $clauses;
    }

    $lookup_table = "{$wpdb->prefix}wc_product_meta_lookup";
    if (strpos($clauses['join'], 's140_stock_lookup') === false) {
        $clauses['join'] .= "
            LEFT JOIN {$lookup_table} AS s140_stock_lookup
                ON ({$wpdb->posts}.ID = s140_stock_lookup.product_id)
        ";
    }

    $stock_first_order = "
        CASE
            WHEN s140_stock_lookup.stock_status = 'instock' THEN 0
            WHEN s140_stock_lookup.stock_status = 'onbackorder' THEN 1
            WHEN s140_stock_lookup.stock_status = 'outofstock' THEN 2
            ELSE 3
        END ASC
    ";

    if (!empty($clauses['orderby'])) {
        $clauses['orderby'] = $stock_first_order . ', ' . $clauses['orderby'];
    } else {
        $clauses['orderby'] = $stock_first_order . ", {$wpdb->posts}.menu_order ASC, {$wpdb->posts}.post_date DESC";
    }

    return $clauses;
}
// Devin: enqueue UI override stylesheet last so it wins specificity
add_action("wp_enqueue_scripts", function () {
    wp_enqueue_style(
        "devin-overrides",
        get_template_directory_uri() . "/css/devin-overrides.css",
        [],
        filemtime(get_template_directory() . "/css/devin-overrides.css")
    );
}, 9999);


/* ============================================================
 * Devin V2 (09.05.2026): доработки по списку
 * ============================================================ */

/**
 * 301 редирект /vacant/ → /vacancy/  (ВК-1)
 */
add_action('init', function () {
    if (!empty($_SERVER['REQUEST_URI'])) {
        $path = strtok($_SERVER['REQUEST_URI'], '?');
        if (preg_match('#^/vacant/?$#', $path)) {
            $qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
            wp_safe_redirect('/vacancy/' . $qs, 301);
            exit;
        }
    }
});

/**
 * AJAX endpoint: единая отправка форм («Перезвонить», «Связаться с нами»,
 * «Остались вопросы», «Купить в 1 клик», «Подобрать аналог» и пр.).
 * Письмо уходит на ACF опцию pochta (по умолчанию nasklad140@gmail.com).
 *
 * Закрывает Г-1, В-1, В-2, ОН-1, ОН-4, Д-5, КН-2 и связанные.
 */
add_action('wp_ajax_s140_submit_form',        's140_submit_form_handler');
add_action('wp_ajax_nopriv_s140_submit_form', 's140_submit_form_handler');

function s140_submit_form_handler() {
    // Базовые поля
    $name      = isset($_POST['name'])      ? sanitize_text_field(wp_unslash($_POST['name']))      : '';
    $phone_raw = isset($_POST['phone'])     ? sanitize_text_field(wp_unslash($_POST['phone']))     : '';
    $message   = isset($_POST['message'])   ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';
    $email_in  = isset($_POST['email'])     ? sanitize_email(wp_unslash($_POST['email']))          : '';
    $form_type = isset($_POST['form_type']) ? sanitize_text_field(wp_unslash($_POST['form_type'])) : 'generic';
    $page_url  = isset($_POST['page_url'])  ? esc_url_raw(wp_unslash($_POST['page_url']))          : '';
    $product   = isset($_POST['product'])   ? sanitize_text_field(wp_unslash($_POST['product']))   : '';
    $consent   = !empty($_POST['consent']);

    // Honeypot против ботов
    if (!empty($_POST['website']) || !empty($_POST['hp_field'])) {
        wp_send_json_success(['queued' => true]); // тихо принимаем, но не шлём
    }

    // Валидация
    $errors = [];
    if ($name === '') {
        $errors['name'] = 'Укажите имя';
    }
    $phone_digits = preg_replace('/\D+/', '', $phone_raw);
    if (strlen($phone_digits) < 10) {
        $errors['phone'] = 'Укажите корректный телефон';
    }
    if (!$consent) {
        $errors['consent'] = 'Требуется согласие на обработку персональных данных';
    }
    if ($errors) {
        wp_send_json_error(['fields' => $errors], 422);
    }

    // Адрес получателя — из ACF опции «pochta», иначе fallback
    $to = function_exists('get_field') ? get_field('pochta', 'option') : '';
    if (!is_email($to)) {
        $to = 'nasklad140@gmail.com';
    }

    // Заголовок и тело письма
    $form_titles = [
        'callme-back'    => 'Перезвонить мне',
        'one-click-buy'  => 'Купить в 1 клик',
        'choose-exact'   => 'Подобрать аналог',
        'other-questions'=> 'Остались вопросы',
        'contact-us'     => 'Связаться с нами',
        'vacancy'        => 'Отклик на вакансию',
        'subscribe'      => 'Подписка на рассылку',
        'generic'        => 'Заявка с сайта',
    ];
    $title  = isset($form_titles[$form_type]) ? $form_titles[$form_type] : 'Заявка с сайта';
    $site   = wp_parse_url(home_url(), PHP_URL_HOST);
    $subject = sprintf('[%s] %s — %s', $site, $title, $name);

    $lines = [];
    $lines[] = 'Тип формы: ' . $title;
    $lines[] = 'Имя:       ' . $name;
    $lines[] = 'Телефон:   ' . $phone_raw . ' (' . $phone_digits . ')';
    if ($email_in)  { $lines[] = 'Email:     ' . $email_in; }
    if ($product)   { $lines[] = 'Товар:     ' . $product; }
    if ($message)   { $lines[] = "Сообщение:\n" . $message; }
    if ($page_url)  { $lines[] = 'Страница:  ' . $page_url; }
    $lines[] = 'IP:        ' . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
    $lines[] = 'Время:     ' . current_time('mysql');

    $body = implode("\n", $lines);

    // Заголовки с поддержкой UTF-8
    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        'From: ' . sprintf('%s <%s>', $site, 'wordpress@' . $site),
    ];
    if ($email_in) {
        $headers[] = 'Reply-To: ' . $email_in;
    }

    $sent = wp_mail($to, $subject, $body, $headers);

    // Сохраняем заявку в опцию-лог (на случай, если письмо не дошло)
    $log_key = 's140_form_submissions_log';
    $log = get_option($log_key, []);
    if (!is_array($log)) { $log = []; }
    $log[] = [
        'time'     => current_time('mysql'),
        'type'     => $form_type,
        'name'     => $name,
        'phone'    => $phone_raw,
        'email'    => $email_in,
        'product'  => $product,
        'message'  => $message,
        'page'     => $page_url,
        'mail_ok'  => $sent ? 1 : 0,
    ];
    // оставляем последние 200 заявок
    if (count($log) > 200) {
        $log = array_slice($log, -200);
    }
    update_option($log_key, $log, false);

    wp_send_json_success([
        'mailed'  => (bool) $sent,
        'message' => 'Заявка принята. Мы свяжемся с вами в ближайшее время.',
    ]);
}

/* s140Forms localize прикреплён к modal-validation handle (см. выше). */

