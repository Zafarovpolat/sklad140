<!-- Распродажа и топовые товары -->
<?php
if (!function_exists('wc'))
  return;

/** Соберём ID товаров, уже лежащих в корзине (для стартового состояния кнопок) */
$in_cart_ids = [];
if (WC()->cart) {
  foreach (WC()->cart->get_cart() as $item) {
    if (!empty($item['product_id']))
      $in_cart_ids[] = (int) $item['product_id'];
    if (!empty($item['variation_id']))
      $in_cart_ids[] = (int) $item['variation_id'];
  }
  $in_cart_ids = array_unique($in_cart_ids);
}

/** ---------- сбор кандидатов, без дублей между блоками ---------- */
$used_ids = [];

/** 1) ПРОМО: один случайный товар со скидкой и в наличии (фиксированный для обеих вкладок) */
$promo = null;
$promo_candidates = wc_get_products([
  'status' => 'publish',
  'stock_status' => 'instock',
  'on_sale' => true,
  'orderby' => 'rand',
  'limit' => 12,
  'return' => 'objects',
]);
if ($promo_candidates) {
  $promo_candidates = array_values(array_filter($promo_candidates, fn($p) => $p->is_on_sale()));
  if (!empty($promo_candidates))
    $promo = $promo_candidates[array_rand($promo_candidates)];
}
if ($promo)
  $used_ids[] = $promo->get_id();

/** 2) Распродажа: только со скидкой + в наличии (16 шт), без дублей с промо */
$discount_ids = array_map('intval', wc_get_product_ids_on_sale());
$discount_ids = array_diff($discount_ids, $used_ids);
shuffle($discount_ids);
$discount_raw = wc_get_products([
  'include' => $discount_ids,
  'status' => 'publish',
  'stock_status' => 'instock',
  'limit' => 64,
  'return' => 'objects',
]);
$discount_products = [];
foreach ($discount_raw as $p) {
  if ($p->is_on_sale() && $p->is_in_stock() && !in_array($p->get_id(), $used_ids, true)) {
    $discount_products[] = $p;
    $used_ids[] = $p->get_id();
    if (count($discount_products) >= 16)
      break;
  }
}

/** 3) Хиты продаж: только в наличии и БЕЗ скидки (16 шт), без дублей с промо/распродажей */
$best_raw = wc_get_products([
  'status' => 'publish',
  'stock_status' => 'instock',
  'limit' => 120,
  'orderby' => 'meta_value_num',
  'meta_key' => 'total_sales',
  'order' => 'DESC',
  'return' => 'objects',
]);
$best_sellers = [];
foreach ($best_raw as $p) {
  if (!$p->is_on_sale() && $p->is_in_stock() && !in_array($p->get_id(), $used_ids, true)) {
    $best_sellers[] = $p;
    $used_ids[] = $p->get_id();
    if (count($best_sellers) >= 16)
      break;
  }
}

/** ---------- helpers ---------- */
function strike_img(WC_Product $p, $size = 'woocommerce_single')
{
  $id = $p->get_image_id();
  if (!$id) {
    $src = wc_placeholder_img_src($size);
    return ['src' => $src, 'src2x' => $src, 'alt' => esc_attr($p->get_name())];
  }
  return [
    'src' => wp_get_attachment_image_url($id, $size),
    'src2x' => wp_get_attachment_image_url($id, $size),
    'alt' => get_post_meta($id, '_wp_attachment_image_alt', true) ?: esc_attr($p->get_name()),
  ];
}
function strike_discount(WC_Product $p)
{
  if (!$p->is_on_sale())
    return 0;
  if ($p->is_type('variable')) {
    $reg = (float) $p->get_variation_regular_price('max');
    $sale = (float) $p->get_variation_sale_price('min');
  } else {
    $reg = (float) $p->get_regular_price();
    $sale = (float) $p->get_sale_price();
  }
  if ($reg <= 0 || $sale <= 0)
    return 0;
  return max(0, round((1 - ($sale / $reg)) * 100));
}
function strike_btn_classes(WC_Product $p)
{
  $classes = [
    'product-item__btn-cart',
    'button',
    'button--red',
    'flex',
    'items-center',
    'justify-center',
    'p-3.75',
    'rounded-xl',
    'hover-greed-75',
    'product_type_' . $p->get_type(),
  ];
  if ($p->is_purchasable() && $p->is_in_stock()) {
    $classes[] = 'add_to_cart_button';
  }
  if ($p->supports('ajax_add_to_cart')) {
    $classes[] = 'ajax_add_to_cart';
  }
  return implode(' ', array_unique($classes));
}
function strike_btn_html(WC_Product $p, array $in_cart_ids)
{
  $id = $p->get_id();
  $sku = $p->get_sku();
  $cart_url = wc_get_cart_url();

  // Уже в корзине: сразу делаем ссылку на корзину и состояние "added"
  if (in_array($id, $in_cart_ids, true)) {
    ?>
    <a href="<?= esc_url($cart_url); ?>"
      class="product-item__btn-cart button button--red flex items-center justify-center p-3.75 rounded-xl hover-greed-75 added"
      data-in-cart="1" data-product_id="<?= esc_attr($id); ?>">
      <svg class="product-item__cart" width="18" height="18">
        <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#cart--filled"></use>
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
    <?php
    return;
  }

  // Ещё не в корзине: обычная AJAX-кнопка Woo
  $url = $p->add_to_cart_url();
  $attrs = [
    'data-quantity' => 1,
    'data-product_id' => $id,
    'data-product_sku' => $sku,
    'aria-label' => esc_attr($p->add_to_cart_description()),
    'rel' => 'nofollow',
  ];
  $attr_html = wc_implode_html_attributes($attrs);
  ?>
  <a href="<?= esc_url($url); ?>" class="<?= esc_attr(strike_btn_classes($p)); ?>" <?= $attr_html; ?>>
    <svg class="product-item__cart" width="18" height="18">
      <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#cart--filled"></use>
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
  <?php
}

/** Карточки */
function strike_card_sale(WC_Product $p, array $in_cart_ids)
{
  $img = strike_img($p);
  $disc = strike_discount($p);
  $price = $p->get_price();
  $reg = $p->get_regular_price();
  $link = get_permalink($p->get_id());
  ?>
  <div class="swiper-slide">
    <div class="product-item">
      <!-- Обёртка для позиционирования -->
      <div class="product-item__img-wrapper relative mb-2" style="position: relative;">
        <!-- Ссылка с фото -->
        <a href="<?= esc_url($link); ?>"
          class="product-item__img flex items-center justify-center bg-white rounded-2xl overflow-hidden block">
          <img class="relative z-0 object-contain size-57" src="<?= esc_url($img['src']); ?>"
            srcset="<?= esc_url($img['src']); ?> 1x, <?= esc_url($img['src2x']); ?> 2x"
            alt="<?= esc_attr($img['alt']); ?>" />
        </a>

        <!-- Скидка - слева сверху -->
        <?php if ($disc > 0): ?>
          <div class="absolute z-10 top-3 left-3 bg-brand-greed rounded-[30px] h-max py-1 px-2"
            style="position: absolute; top: 12px; left: 12px;">
            <p class="product-item__discount font-semibold text-xs text-white">-<?= esc_html($disc); ?>%</p>
          </div>
        <?php endif; ?>

        <!-- Кнопки - справа сверху -->
        <div class="absolute z-10 top-1 right-1 flex flex-col" style="position: absolute; top: 4px; right: 4px;">
          <?php echo do_shortcode('[yith_wcwl_add_to_wishlist product_id="' . $p->get_id() . '"]'); ?>

          <div class="compare-observer" style="display:none;">
            <?php echo do_shortcode('[yith_compare_button product="' . $p->get_id() . '"]'); ?>
          </div>

          <a href="#" class="product-item__sravnenie size-10 flex items-center justify-center"
            data-product_id="<?php echo esc_attr($p->get_id()); ?>">
            <svg width="13" height="16" viewBox="0 0 13 16">
              <path d="M2 15V8M7 15V1M12 15V5" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
          </a>
        </div>

        <!-- Бейдж БУ - слева снизу -->
        <?php if (function_exists('get_field') && get_field('bu_tovar', $p->get_id())): ?>
          <div class="absolute z-10 bottom-3 left-3 bg-primary rounded-[30px] h-max py-1.25 px-2"
            style="position: absolute; bottom: 12px; left: 12px;">
            <p class="font-medium text-[10px] text-white">Товар БУ</p>
          </div>
        <?php endif; ?>
      </div>

      <div class="flex items-center gap-2 mb-2">
        <h5 class="product-item__price font-semibold text-brand-red text-main-22/[120%]">
          <?= htmlspecialchars_decode(wc_price($price)); ?>
        </h5>
        <?php if ($reg && $reg > $price): ?>
          <p class="font-medium text-sm/[120%] text-brand-blue-200 line-through decoration-brand-red">
            <?= htmlspecialchars_decode(wc_price($reg)); ?>
          </p>
        <?php endif; ?>
      </div>

      <p class="product-item__title font-medium text-primary mb-8.5"><a
          href="<?= esc_url($link); ?>"><?= esc_html($p->get_name()); ?></a></p>

      <?php $in_stock = $p->is_in_stock(); ?>
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

      <?php
      $attrs = strike_first_attributes($p);
      ?>
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

      <div class="product-item__btns product-item__btns--availability flex items-center justify-between gap-1">
        <a class="product-item__buy-now button button--fill" href="#" style="font-size: 16px;">
          <p class="button__text">Купить сейчас</p>
        </a>
        <?php strike_btn_html($p, $in_cart_ids); ?>
      </div>

      <div class="product-item__btns product-item__btns--out-of-stock"><button class="button button--dark">Подобрать
          аналог</button></div>
      <div class="product-item__added-to-cart">
        <svg class="product-item__added-to-cart__cross" width="16" height="16" viewBox="0 0 16 16" fill="none"
          xmlns="http://www.w3.org/2000/svg">
          <path d="M20 -4L-4 20M-4 -4L20 20" stroke="#031343" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round" />
        </svg>
        <div class="product-item__added-to-cart__info">
          <p>Товар <a href="<?= esc_url($link); ?>"><?= esc_html($p->get_name()); ?></a> добавлен в корзину</p>
          <a href="<?= esc_url(wc_get_cart_url()); ?>">Перейти в корзину</a>
        </div>
      </div>
    </div>
  </div>
  <?php
}

function strike_card_hit(WC_Product $p, array $in_cart_ids)
{
  $img = strike_img($p);
  $price = $p->get_price();
  $link = get_permalink($p->get_id());
  ?>
  <div class="swiper-slide">
    <div class="product-item">
      <!-- ✅ ИСПРАВЛЕНО: убрана лишняя обёртка, структура как в strike_card_sale -->
      <div class="product-item__img-wrapper relative mb-2" style="position: relative;">
        <!-- Ссылка с фото -->
        <a href="<?= esc_url($link); ?>"
          class="product-item__img flex items-center justify-center bg-white rounded-2xl overflow-hidden block">
          <img class="relative z-0 object-contain size-57" src="<?= esc_url($img['src']); ?>"
            srcset="<?= esc_url($img['src']); ?> 1x, <?= esc_url($img['src2x']); ?> 2x"
            alt="<?= esc_attr($img['alt']); ?>" />
        </a>

        <!-- Кнопки - справа сверху -->
        <div class="absolute z-10 top-1 right-1 flex flex-col" style="position: absolute; top: 4px; right: 4px;">
          <?php echo do_shortcode('[yith_wcwl_add_to_wishlist product_id="' . $p->get_id() . '"]'); ?>

          <div class="compare-observer" style="display:none;">
            <?php echo do_shortcode('[yith_compare_button product="' . $p->get_id() . '"]'); ?>
          </div>

          <a href="#" class="product-item__sravnenie size-10 flex items-center justify-center"
            data-product_id="<?php echo esc_attr($p->get_id()); ?>">
            <svg width="13" height="16" viewBox="0 0 13 16">
              <path d="M2 15V8M7 15V1M12 15V5" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
          </a>
        </div>

        <!-- Бейдж БУ - слева снизу -->
        <?php if (function_exists('get_field') && get_field('bu_tovar', $p->get_id())): ?>
          <div class="absolute z-10 bottom-3 left-3 bg-primary rounded-[30px] h-max py-1.25 px-2"
            style="position: absolute; bottom: 12px; left: 12px;">
            <p class="font-medium text-[10px] text-white">Товар БУ</p>
          </div>
        <?php endif; ?>
      </div>

      <div class="flex items-center gap-2 mb-2">
        <h5 class="product-item__price font-semibold text-brand-red text-main-22/[120%]">
          <?= htmlspecialchars_decode(wc_price($price)); ?>
        </h5>
      </div>

      <p class="product-item__title font-medium text-primary mb-8.5"><a
          href="<?= esc_url($link); ?>"><?= esc_html($p->get_name()); ?></a></p>


      <?php $in_stock = $p->is_in_stock(); ?>
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

      <?php
      $attrs = strike_first_attributes($p);
      ?>
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

      <div class="product-item__btns product-item__btns--availability flex items-center justify-between gap-1">
        <a class="product-item__buy-now button button--fill" href="#" style="font-size: 16px;">
          <p class="button__text">Купить сейчас</p>
        </a>
        <?php strike_btn_html($p, $in_cart_ids); ?>
      </div>

      <div class="product-item__btns product-item__btns--out-of-stock"><button class="button button--dark">Подобрать
          аналог</button></div>
      <div class="product-item__added-to-cart">
        <svg class="product-item__added-to-cart__cross" width="16" height="16" viewBox="0 0 16 16" fill="none"
          xmlns="http://www.w3.org/2000/svg">
          <path d="M20 -4L-4 20M-4 -4L20 20" stroke="#031343" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round" />
        </svg>
        <div class="product-item__added-to-cart__info">
          <p>Товар <a href="<?= esc_url($link); ?>"><?= esc_html($p->get_name()); ?></a> добавлен в корзину</p>
          <a href="<?= esc_url(wc_get_cart_url()); ?>">Перейти в корзину</a>
        </div>
      </div>
    </div>
  </div>
  <?php
}

/** ---------- промо HTML (фикс для обеих вкладок) ---------- */
ob_start(); ?>
<div
  class="product-swiper__stock bg-primary text-white rounded-2xl w-full flex flex-col justify-between gap-5 py-3 px-2">
  <div class="text-center">
    <div class="w-fit mx-auto bg-brand-greed rounded-xl px-4 py-2 mb-1">
      <p class="font-semibold uppercase text-lg/[120%]">Акция</p>
    </div>
    <p class="font-semibold text-lg/[120%] uppercase">на Складе140!</p>
  </div>
  <?php if ($promo instanceof WC_Product):
    $pimg = strike_img($promo);
    $pdisc = strike_discount($promo);
    $plink = get_permalink($promo->get_id());
    $pp = $promo->get_price();
    $pr = $promo->get_regular_price(); ?>
    <a class="product-swiper__stock-img relative flex items-center justify-center size-60 bg-white rounded-2xl overflow-hidden"
      href="<?= esc_url($plink); ?>">
      <img class="relative z-0 object-contain size-full" src="<?= esc_url($pimg['src']); ?>"
        alt="<?= esc_attr($pimg['alt']); ?>" />
      <?php if ($pdisc > 0): ?>
        <div class="absolute z-10 top-3 left-3 bg-brand-greed rounded-[30px] py-1 px-2">
          <p class="font-semibold text-lg/[120%]">-<?= esc_html($pdisc); ?>%</p>
        </div>
      <?php endif; ?>
    </a>
    <div class="py-3 px-1">
      <div class="flex items-center gap-2 mb-3">
        <h5 class="font-semibold text-main-22/[120%]"><?= htmlspecialchars_decode(wc_price($pp)); ?></h5>
        <?php if ($pr && $pr > $pp): ?>
          <p class="font-medium text-sm/[120%] text-brand-blue-200 line-through decoration-brand-greed">
            <?= htmlspecialchars_decode(wc_price($pr)); ?>
          </p>
        <?php endif; ?>
      </div>
      <h5 class="font-semibold text-lg/[120%] mb-3"><a
          href="<?= esc_url($plink); ?>"><?= esc_html($promo->get_name()); ?></a></h5>
      <a href="<?= esc_url($plink); ?>" class="link">
        <div class="link__icon"><svg width="7" height="12">
            <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow"></use>
          </svg></div>
        <p class="link__text">Подробнее</p>
      </a>
    </div>
  <?php else: ?>
    <div class="py-3 px-1">
      <p>Нет товаров со скидкой.</p>
    </div>
  <?php endif; ?>
</div>
<?php $PROMO_BLOCK_HTML = ob_get_clean(); ?>

<section class="sale-products">
  <div class="container">
    <div class="section-header">
      <div class="section-header__left">
        <div class="section-header__icon">
          <img src="<?= get_template_directory_uri(); ?>/images/content/others/fire-emoji.png" alt="Fire emoji" />
        </div>
        <h2 class="section-header__title">Распродажа и топовые товары</h2>
        <h2 class="section-header__title section-header__title--mobile">Скидки недели</h2>
      </div>
      <img src="<?= get_template_directory_uri(); ?>/images/gif/drag-mobile.gif" loading="lazy" alt="drag"
        class="section-header__icon--drag-mobile">
    </div>

    <div class="relative product-swiper">
      <div class="flex items-center justify-between gap-5 mb-6">
        <div class="flex items-center gap-3">
          <button class="button sale-products__tab sale-products__tab--active" data-tab="sales">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M18 5L5 19" stroke="#FCFCFC" stroke-width="2" stroke-linecap="round" />
              <circle cx="7.5" cy="7.5" r="2.5" stroke="#FCFCFC" stroke-width="2" />
              <circle cx="16.5" cy="16.5" r="2.5" stroke="#FCFCFC" stroke-width="2" />
            </svg>
            <span class="button__text">Распродажа недели </span>
          </button>
          <button class="button sale-products__tab" data-tab="hit-of-sales">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                d="M9 9.35669C9 8.42062 7.73667 8.1725 7.4015 9.04836C6.59074 11.167 6 13.0623 6 14.087C6 17.3527 8.68629 20 12 20C15.3137 20 18 17.3527 18 14.087C18 12.9861 17.3181 10.8803 16.4141 8.57226C15.2431 5.58242 14.6576 4.0875 13.9348 4.00698C13.7035 3.98121 13.4512 4.02754 13.2449 4.13365C12.6 4.46526 12.6 6.09574 12.6 9.35669C12.6 10.3364 11.7941 11.1306 10.8 11.1306C9.80589 11.1306 9 10.3364 9 9.35669Z"
                fill="#F53535" />
            </svg>
            <span class="button__text">Хит продаж</span>
          </button>
        </div>
        <div class="swiper-navigation flex items-center gap-5">
          <button
            class="swiper-button-prev__main__product drop-shadow flex items-center justify-center size-10 bg-white rounded-full">
            <svg width="7" height="12" style="transform: rotate(180deg)">
              <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow"></use>
            </svg>
          </button>
          <button
            class="swiper-button-next__main__product drop-shadow flex items-center justify-center size-10 bg-white rounded-full">
            <svg width="7" height="12">
              <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow"></use>
            </svg>
          </button>
        </div>
      </div>

      <div class="product-swiper__holder">

        <!-- РАСПРОДАЖА -->
        <div class="product-swiper__wrapper product-swiper__wrapper--active product-swiper__wrapper--sales"
          data-content="sales">
          <?= $PROMO_BLOCK_HTML; ?>
          <div class="swiper" style="width: 100%;">
            <div class="swiper-wrapper">
              <?php foreach ($discount_products as $p)
                strike_card_sale($p, $in_cart_ids); ?>
            </div>
            <div class="swiper-scrollbar__wrapper">
              <div class="swiper-scrollbar__left">
                <p class="swiper-scrollbar__subtitle">Зажми и потяни</p>
                <img class="swiper-scrollbar__icon" src="<?= get_template_directory_uri(); ?>/images/gif/drag.gif"
                  loading="lazy" alt="">
              </div>
              <!-- ✅ Уникальный класс для scrollbar -->
              <div class="product-swiper-scrollbar product-swiper-scrollbar--sales swiper-scrollbar"></div>
            </div>
          </div>

          <div class="swiper-pagination__wrapper swiper-pagination__wrapper--sales" style="margin-top: 20px;">
            <div class="swiper-pagination__left">
              <p class="swiper-pagination__subtitle">Зажми и потяни</p>
              <img class="swiper-pagination__icon" src="<?= get_template_directory_uri(); ?>/images/gif/drag.gif"
                loading="lazy" alt="">
            </div>
            <!-- ✅ Уникальный класс для pagination -->
            <div class="product-swiper-pagination product-swiper-pagination--sales swiper-pagination"></div>
          </div>
        </div>

        <!-- ХИТЫ ПРОДАЖ -->
        <div class="product-swiper__wrapper product-swiper__wrapper--hit-of-sales" data-content="hit-of-sales">
          <?= $PROMO_BLOCK_HTML; ?>
          <div class="swiper" style="width: 100%;">
            <div class="swiper-wrapper">
              <?php foreach ($best_sellers as $p)
                strike_card_hit($p, $in_cart_ids); ?>
            </div>
            <div class="swiper-scrollbar__wrapper">
              <div class="swiper-scrollbar__left">
                <p class="swiper-scrollbar__subtitle">Зажми и потяни</p>
                <img class="swiper-scrollbar__icon" src="<?= get_template_directory_uri(); ?>/images/gif/drag.gif"
                  loading="lazy" alt="">
              </div>
              <!-- ✅ Уникальный класс для scrollbar -->
              <div class="product-swiper-scrollbar product-swiper-scrollbar--hits swiper-scrollbar"></div>
            </div>
          </div>

          <div class="swiper-pagination__wrapper swiper-pagination__wrapper--hits" style="margin-top: 20px;">
            <div class="swiper-pagination__left">
              <p class="swiper-pagination__subtitle">Зажми и потяни</p>
              <img class="swiper-pagination__icon" src="<?= get_template_directory_uri(); ?>/images/gif/drag.gif"
                loading="lazy" alt="">
            </div>
            <!-- ✅ Уникальный класс для pagination -->
            <div class="product-swiper-pagination product-swiper-pagination--hits swiper-pagination"></div>
          </div>
        </div>
      </div>
    </div>
</section>

<!-- === Шаблоны — оставляю пока что === -->
<template id="product-template--hit-of-sales">
  <div class="product-item">
    <div
      class="product-item__img relative flex items-center justify-center bg-white text-white rounded-2xl overflow-hidden mb-2">
      <img class="relative z-0 object-contain size-57"
        src="<?= get_template_directory_uri(); ?>/images/content/products/showcase-stand.webp"
        srcset="<?= get_template_directory_uri(); ?>/images/content/products/showcase-stand.webp 1x, <?= get_template_directory_uri(); ?>/images/content/products/showcase-stand@2x.webp 2x"
        alt="img" />
      <div class="product-item__actions absolute z-10 top-0 left-0 w-full flex justify-between gap-5">
        <div class="flex flex-col mt-1 mr-1">
          <button class="product-item__like size-10 flex items-center justify-center">
            <svg width="20" height="18" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                d="M16.3 10.778C17.641 9.48 19 7.924 19 5.888c0-1.296-.521-2.54-1.45-3.456A4.982 4.982 0 0014.05 1c-1.584 0-2.7.444-4.05 1.778C8.65 1.444 7.534 1 5.95 1a4.982 4.982 0 00-3.5 1.432A4.859 4.859 0 001 5.889c0 2.044 1.35 3.6 2.7 4.889L10 17l6.3-6.222z"
                stroke="#7C7C7C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </button>
          <button class="product-item__sravnenie size-10 flex items-center justify-center">
            <svg width="13" height="16" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 15V6.25M6.5 15V1M1 15В9.75" stroke="#7C7C7C" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" />
            </svg>
          </button>
        </div>
      </div>
      <div class="absolute z-10 bottom-3 left-3 bg-primary rounded-[30px] h-max py-1.25 px-2">
        <p class="font-medium text-[10px]">Товар БУ</p>
      </div>
    </div>
    <!-- Cost -->
    <div class="flex items-center gap-2 mb-2">
      <h5 class="product-item__price font-semibold text-brand-red text-main-22/[120%]">
        2 500 ₽
      </h5>
    </div>
    <p class="product-item__title font-medium text-primary mb-8.5">Стойка витрина 120/80/121</p>
    <div class="product-item__availability bg-white rounded-lg w-fit flex items-center gap-1.5 py-2 px-3 mb-2">
      <svg class="product-item__availability--check" width="12" height="12">
        <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#check"></use>
      </svg>
      <svg class="product-item__availability--cross" width="12" height="12">
        <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#cross"></use>
      </svg>
      <p>В наличии</p>
    </div>
    <ul class="product-item__specifications w-full text-sm/[120%] mb-2 space-y-1">
      <li>
        <div class="flex gap-0.5 items-end justify-between">
          <p class="text-brand-gray">Длина</p>
          <div class="w-full flex-1 border-b border-dashed border-brand-blue-200">
          </div>
          <span>120 см </span>
        </div>
      </li>
      <li>
        <div class="flex gap-0.5 items-end justify-between">
          <p class="text-brand-gray">Ширина</p>
          <div class="w-full flex-1 border-b border-dashed border-brand-blue-200">
          </div>
          <span>80 см </span>
        </div>
      </li>
      <li>
        <div class="flex gap-0.5 items-end justify-between">
          <p class="text-brand-gray">Высота</p>
          <div class="w-full flex-1 border-b border-dashed border-brand-blue-200">
          </div>
          <span>121 см </span>
        </div>
      </li>
      <li>
        <div class="flex gap-0.5 items-end justify-between">
          <p class="text-brand-gray">Материал</p>
          <div class="w-full flex-1 border-b border-dashed border-brand-blue-200">
          </div>
          <span>Дерево, массив, сталь </span>
        </div>
      </li>
    </ul>
    <div class="product-item__btns product-item__btns--availability flex items-center justify-between gap-1">
      <a class="product-item__buy-now button button--fill" href="#" style="font-size: 16px;">
        <p class="button__text">
          Купить
          сейчас
        </p>
      </a>
      <button
        class="product-item__btn-cart button button--red flex items-center justify-center p-3.75 rounded-xl hover-greed-75">
        <svg class="product-item__cart" width="18" height="18">
          <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#cart--filled"></use>
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
          <path d="M15.4545 3Л13.0795 5.375L12 4.29545" stroke="white" stroke-width="0.5" stroke-linecap="round"
            stroke-linejoin="round" />
        </svg>
      </button>
    </div>
    <div class="product-item__btns product-item__btns--out-of-stock">
      <button class="button button--dark">Подобрать аналог</button>
    </div>
    <div class="product-item__added-to-cart">
      <svg class="product-item__added-to-cart__cross" width="16" height="16" viewBox="0 0 16 16" fill="none"
        xmlns="http://www.w3.org/2000/svg">
        <path d="M20 -4L-4 20M-4 -4L20 20" stroke="#031343" stroke-width="2" stroke-linecap="round"
          stroke-linejoin="round" />
      </svg>
      <div class="product-item__added-to-cart__info">
        <p>
          Товар <span>Подстолье Астро 26/26/д38/в72 белый сталь</span> добавлен в корзину
        </p>
        <a href="/cart.html">Перейти в корзину</a>
      </div>
    </div>
  </div>
</template>

<template id="product-template--sales">
  <div class="product-item">
    <div
      class="product-item__img relative flex items-center justify-center bg-white text-white rounded-2xl overflow-hidden mb-2">
      <img class="relative z-0 object-contain size-57"
        src="<?= get_template_directory_uri(); ?>/images/content/products/loft-basement.webp"
        srcset="<?= get_template_directory_uri(); ?>/images/content/products/loft-basement.webp 1x, <?= get_template_directory_uri(); ?>/images/content/products/loft-basement@2x.webp 2x"
        alt="img" />
      <div class="absolute z-10 top-0 left-0 w-full flex justify-between gap-5">
        <div class="bg-brand-greed rounded-[30px] h-max py-1 px-2 mt-3 ml-3">
          <p class="product-item__discount font-semibold text-xs">-30%</p>
        </div>
        <div class="flex flex-col mt-1 mr-1">
          <button class="product-item__like size-10 flex items-center justify-center">
            <svg width="20" height="18">
              <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#favourites"></use>
            </svg>
          </button>
          <button class="size-10 flex items-center justify-center">
            <svg width="13" height="16">
              <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#bar-chart"></use>
            </svg>
          </button>
        </div>
      </div>
      <div class="absolute z-10 bottom-3 left-3 bg-primary rounded-[30px] h-max py-1.25 px-2">
        <p class="font-medium text-[10px]">Товар БУ</p>
      </div>
    </div>
    <!-- Cost -->
    <div class="flex items-center gap-2 mb-2">
      <h5 class="product-item__price font-semibold text-brand-red text-main-22/[120%]">2 500 ₽.</h5>
      <p class="font-medium text-sm/[120%] text-brand-blue-200 line-through decoration-brand-red">4 500 ₽.</p>
    </div>
    <p class="product-item__title font-medium text-primary mb-8.5">Стойка витрина 120/80/121</p>
    <div class="product-item__availability bg-white rounded-lg w-fit flex items-center gap-1.5 py-2 px-3 mb-2">
      <svg width="12" height="12">
        <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#check"></use>
      </svg>
      <p>В наличии</p>
    </div>
    <ul class="product-item__specifications w-full text-sm/[120%] mb-2 space-y-1">
      <li>
        <div class="flex gap-0.5 items-end justify-between">
          <p class="text-brand-gray">Длина</p>
          <div class="w-full flex-1 border-b border-dashed border-brand-blue-200"></div>
          <span>120 см </span>
        </div>
      </li>
      <li>
        <div class="flex gap-0.5 items-end justify-between">
          <p class="text-brand-gray">Ширина</p>
          <div class="w-full flex-1 border-b border-dashed border-brand-blue-200"></div>
          <span>80 см </span>
        </div>
      </li>
      <li>
        <div class="flex gap-0.5 items-end justify-between">
          <p class="text-brand-gray">Высота</p>
          <div class="w-full flex-1 border-b border-dashed border-brand-blue-200"></div>
          <span>121 см </span>
        </div>
      </li>
      <li>
        <div class="flex gap-0.5 items-end justify-between">
          <p class="text-brand-gray">Материал</p>
          <div class="w-full flex-1 border-b border-dashed border-brand-blue-200"></div>
          <span>Дерево, массив, сталь </span>
        </div>
      </li>
    </ul>
    <div class="product-item__btns product-item__btns--availability flex items-center justify-between gap-1">
      <a class="product-item__buy-now button button--fill" href="#" style="font-size: 16px;">
        <p class="button__text">
          Купить
          сейчас
        </p>
      </a>
      <button
        class="product-item__btn-cart button button--red flex items-center justify-center p-3.75 rounded-xl hover-greed-75">
        <svg class="product-item__cart" width="18" height="18">
          <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#cart--filled"></use>
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
    </div>
    <div class="product-item__btns product-item__btns--out-of-stock">
      <button class="button button--dark">Подобрать аналог</button>
    </div>
    <div class="product-item__added-to-cart">
      <svg class="product-item__added-to-cart__cross" width="16" height="16" viewBox="0 0 16 16" fill="none"
        xmlns="http://www.w3.org/2000/svg">
        <path d="M20 -4L-4 20M-4 -4L20 20" stroke="#031343" stroke-width="2" stroke-linecap="round"
          stroke-linejoin="round" />
      </svg>
      <div class="product-item__added-to-cart__info">
        <p>
          Товар <span>Подстолье Астро 26/26/д38/в72 белый сталь</span> добавлен в корзину
        </p>
        <a href="/cart.html">Перейти в корзину</a>
      </div>
    </div>
  </div>
</template>

<style>
  /* два состояния SVG */
  .product-item__btn-cart .product-item__cart--added {
    display: none;
  }

  .product-item__btn-cart.added .product-item__cart {
    display: none;
  }

  .product-item__btn-cart.added .product-item__cart--added {
    display: inline;
  }

  .product-item__btn-cart:hover {
    background-color: #031343
  }

  /* сносим автоссылку Woo "Просмотр корзины" */
  a.added_to_cart.wc-forward {
    display: none !important;
  }
</style>

<script>
  jQuery(function ($) {
    // адрес корзины для перевода кнопки в ссылку
    window.SKLAD_CART_URL = "<?= esc_js(wc_get_cart_url()); ?>";

    // После успешного добавления: фиксируем кнопку как ссылку на корзину
    $(document.body).on('added_to_cart', function (event, fragments, cart_hash, $button) {
      if (!$button || !$button.length) return;

      // убрать автоссылку Woo рядом с кнопкой
      var $next = $button.next('.added_to_cart.wc-forward');
      if ($next.length) $next.remove();

      // зафиксировать состояние
      $button
        .removeClass('add_to_cart_button ajax_add_to_cart')
        .addClass('added')
        .attr('href', window.SKLAD_CART_URL)
        .attr('data-in-cart', '1');
    });
  });
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
    stroke: #031343;
  }

  .product-item__sravnenie.added svg path {
    stroke: #258ffb;
  }

  /* тултип */
  .product-item__sravnenie::after {
    position: absolute;
    bottom: 23%;
    margin-right: 40px;
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


  /* избрн  */
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

  /* активное состояние */
  .yith-wcwl-add-to-wishlist.exists svg path,
  .yith-wcwl-add-to-wishlist.added svg path {
    stroke: #258ffb;
    fill: #258ffb;
  }

  /* тултип */
  .yith-wcwl-add-to-wishlist a::after {
    position: absolute;
    bottom: 23%;
    margin-right: 40px;
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

  .yith-wcwl-add-button .add_to_wishlist svg.yith-wcwl-icon-svg,
  .yith-wcwl-add-button .add_to_wishlist img {
    margin-right: 0px;
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