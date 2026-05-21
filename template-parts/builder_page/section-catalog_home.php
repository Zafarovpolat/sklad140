<!-- Категория товаров -->
<?php
$catalog_block = get_field('katalogtovarov', 'option');
if ($catalog_block):
  $title = $catalog_block['zagolovok_bloka'];
  $catalog = $catalog_block['katalog'];
  if (!empty($catalog)):
    $chunks = array_chunk($catalog, 6);
?>
<section class="categories">
  <div class="container">
    <div class="section-header">
      <div class="section-header__left">
        <div class="section-header__icon">
          <img src="<?php echo get_template_directory_uri(); ?>/images/content/others/sofa-emoji.png"
               alt="<?php echo esc_attr($title); ?>"
               title="<?php echo esc_attr($title); ?>" />
        </div>
        <?php if ($title): ?>
          <h2 class="section-header__title"><?php echo esc_html($title); ?></h2>
        <?php endif; ?>
      </div>
    </div>

    <?php foreach ($chunks as $index => $row): ?>
      <div class="categories__items <?php echo $index === 0 ? 'grid grid-cols-[180px_180px_250px_180px_180px_250px] gap-3 mb-3' : 'grid grid-cols-[180px_180px_250px_250px_180px_180px] gap-3'; ?>">
        <?php foreach ($row as $item):
          $zagolovok = $item['zagolovok'];
          $ssylka = $item['ssylka'];
          $fonovaya = $item['fonovaya_kartinka'];
          $kartinka = $item['kartinka'];
          $alt = esc_attr($zagolovok);
        ?>
          <a href="<?php echo esc_url($ssylka ?: '#'); ?>"
             class="categories-item relative flex flex-col justify-between bg-white rounded-xl overflow-hidden">
            <div class="relative z-10 p-3">
              <p class="categories-item__title font-medium text-lg/[110%] text-center"><?php echo esc_html($zagolovok); ?></p>
            </div>
            <?php if (!empty($kartinka)): ?>
              <img class="categories-item__img relative z-10 object-contain h-40 mx-auto"
                   src="<?php echo esc_url($fonovaya['url']); ?>"
                   alt="<?php echo $alt; ?>"
                   title="<?php echo $alt; ?>" />
            <?php endif; ?>
            <?php if (!empty($fonovaya)): ?>
              <img class="categories-item__bg absolute z-0 bottom-0 left-0 object-contain w-full h-40"
                   src="<?php echo esc_url($kartinka['url']); ?>"
                   alt="<?php echo $alt; ?>"
                   title="<?php echo $alt; ?>"
                   style="object-fit: cover" />
            <?php endif; ?>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; endif; ?>