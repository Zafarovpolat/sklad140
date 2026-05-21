<?php
$content = get_field('content');

if ($content && is_array($content)) {
  // Страница использует ACF-билдер
  foreach ($content as $index => $row) {
    get_template_part(
      'template-parts/builder_' . get_post_type() . '/section',
      $row['acf_fc_layout'],
      [
        'row' => $row,
        'content' => $content
      ]
    );
  }

} else {
  // Проверка: если это страница корзины и корзина пуста
  $is_cart_page = function_exists('wc_get_page_id') && is_page(wc_get_page_id('cart'));
  $cart_is_empty = $is_cart_page && WC()->cart && WC()->cart->is_empty();

  if ($cart_is_empty) {
    ?>
    <main class="page-content">
      <div class="container">
        <link rel="stylesheet" href="<?= get_template_directory_uri(); ?>/css/cart.min.css">
        <?php wc_get_template('checkout/cart-empty.php'); ?>
      </div>
    </main>
    <?php
  } else {
    // Стандартная страница WordPress (без ACF-билдера)
    ?>
    <main class="page-content">
      <div class="container">
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
          <?php the_title('<h1 class="page-title">', '</h1>'); ?>
          <div class="page-body">
            <?php the_content(); ?>
          </div>
        </article>
      </div>
    </main>
    <?php
  }
}
?>