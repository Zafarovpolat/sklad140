<!-- Hero -->
<section class="hero">
  <div class="container">
    <!-- Top -->
    <div class="hero-top flex gap-3 mb-4">
      <!-- Left -->
      <div class="hero-left relative main-hero-slider max-w-239.25 w-full">
        <!-- Swiper -->
        <div class="!relative !z-10 swiper sklad-hero__swiper">
          <?php
          $slides = get_field('home_slides', 'option');
          if ($slides):
            ?>
            <div class="swiper-wrapper">
              <?php foreach ($slides as $slide):
                $zagolovok = $slide['zagolovok'];
                $tekst = $slide['tekst'];
                $ssylkaknopki = $slide['ssylkaknopki'];
                $czvetfona = $slide['czvetfona'];
                $izobrazhenie = $slide['izobrazhenie'];
                $bg_style = $czvetfona ? 'background-color:' . esc_attr($czvetfona) . ';' : '';
                $alt = esc_attr($zagolovok);
                $title = esc_attr($zagolovok);
                ?>
                <div class="swiper-slide">
                  <div class="hero-slide relative min-h-107.5 text-white rounded-[40px] p-10"
                    style="<?php echo $bg_style; ?>">
                    <div class="hero-slide__text relative z-10 max-w-105 w-full">
                      <?php if ($zagolovok): ?>
                        <h1 class="hero-slide__title font-semibold text-main-40 leading-none mb-6">
                          <?php echo esc_html($zagolovok); ?>
                        </h1>
                      <?php endif; ?>

                      <?php if ($tekst):
                        if (strpos($tekst, '<ul') !== false) {
                          $tekst = str_replace('<ul', '<ul class="hero-slide__list list-inside list-disc flex flex-col gap-2 mb-6"', $tekst);
                          echo $tekst;
                        } else {
                          echo '<p>' . $tekst . '</p>';
                        }
                      endif; ?>

                      <?php if ($ssylkaknopki): ?>
                        <a href="<?php echo esc_url($ssylkaknopki); ?>"
                          class="hero-slide__btn font-medium flex items-center justify-center w-fit hover-primary-75 rounded-xl px-6 py-3">Перейти
                          в каталог</a>
                      <?php endif; ?>
                    </div>

                    <?php if ($ssylkaknopki): ?>
                      <a href="<?php echo esc_url($ssylkaknopki); ?>" class="link link--white">
                        <div class="link__icon">
                          <svg width="8" height="12">
                            <use xlink:href="<?php echo get_template_directory_uri(); ?>/images/sprite.svg#arrow"></use>
                          </svg>
                        </div>
                        <p class="link__text">Подробнее</p>
                      </a>
                    <?php endif; ?>

                    <?php if ($izobrazhenie): ?>
                      <div class="hero-slide__img absolute z-0 top-12 right-8">
                        <img class="h-87.5 object-contain" src="<?php echo esc_url($izobrazhenie['url']); ?>"
                          alt="<?php echo $alt; ?>" title="<?php echo $title; ?>">
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
          <div class="swiper-dots-pagination swiper-pagination"></div>
        </div>
        <div class="absolute z-20 right-10 bottom-10">
          <div class="flex items-center gap-4">
            <!-- Prev -->
            <button
              class="swiper-button-prev__main__hero flex items-center justify-center size-10 rounded-full bg-white">
              <svg width="7" height="12" style="transform: rotate(180deg)">
                <use xlink:href="<?php echo get_template_directory_uri(); ?>/images/sprite.svg#arrow"></use>
              </svg>
            </button>
            <!-- Next -->
            <button
              class="swiper-button-next__main__hero flex items-center justify-center size-10 rounded-full bg-white">
              <svg width="7" height="12">
                <use xlink:href="<?php echo get_template_directory_uri(); ?>/images/sprite.svg#arrow"></use>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <?php
      $blocks = get_field('pravyjblokotslajda', 'option');
      if ($blocks):
        ?>
        <div class="hero-right max-w-80.75 w-full">
          <?php foreach ($blocks as $i => $block):
            $zagolovok = $block['zagolovok'];
            $tekst = $block['tekst'];
            $ssylkanopki = $block['ssylkanopki'];
            $ik = $block['ikonka'];
            $alt = esc_attr($zagolovok);
            $title = esc_attr($zagolovok);

            $circle_classes = '';
            switch ($i) {
              case 0:
                $circle_classes = '<div class="hero-right__item-circle size-22.5 bg-brand-yellow rounded-full absolute z-0 top-0 right-0 translate-x-1/3 -translate-y-1/4"></div>';
                break;
              case 1:
                $circle_classes = '
                      <div class="hero-right__item-circle--green--small"></div>
                      <div class="hero-right__item-circle hero-right__item-circle--green"></div>';
                break;
              case 2:
                $circle_classes = '
                      <div class="hero-right__item-circle--rad--medium"></div>
                      <div class="hero-right__item-circle hero-right__item-circle--rad"></div>';
                break;
              case 3:
                $circle_classes = '<div class="hero-right__item-circle hero-right__item-circle--lightblue"></div>';
                break;
            }
            ?>
            <div href="<?php echo $ssylkanopki ? esc_url($ssylkanopki) : '#'; ?>"
              class="hero-right__item relative min-h-24.75 bg-white rounded-2xl overflow-hidden flex flex-col gap-2 px-3.75 py-4 mb-3">
              <?php echo $circle_classes; ?>
              <?php if ($ik): ?>
                <img class="absolute z-10 right-0 top-0" src="<?php echo esc_url($ik['url']); ?>" alt="<?php echo $alt; ?>"
                  title="<?php echo $title; ?>">
              <?php endif; ?>
              <div class="w-full max-w-53.5 relative z-10">
                <?php if ($zagolovok): ?>
                  <h5 class="font-medium text-lg mb-2"><?php echo esc_html($zagolovok); ?></h5>
                <?php endif; ?>
                <?php if ($tekst): ?>
                  <div class="text-sm/[120%]"><?php echo $tekst; ?></div>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- Hero Right Swiper -->
      <?php
      $blocks = get_field('pravyjblokotslajda', 'option');
      if ($blocks):
        ?>
        <div class="hero-right-swiper swiper max-w-77.75 w-full">
          <div class="swiper-wrapper">
            <?php foreach ($blocks as $block):
              $zagolovok = $block['zagolovok'];
              $tekst = $block['tekst'];
              $ssylkanopki = $block['ssylkaknopki'];
              $ik = $block['ikonka'];
              $alt = esc_attr($zagolovok);
              $title = esc_attr($zagolovok);
              ?>
              <div class="swiper-slide">
                <a href="<?php echo $ssylkanopki ? esc_url($ssylkanopki) : '#'; ?>"
                  class="hero-right__item relative min-h-24.75 bg-white rounded-2xl overflow-hidden flex flex-col gap-2 px-3.75 py-4 mb-3">
                  <div
                    class="size-22.5 bg-brand-yellow rounded-full absolute z-0 top-0 right-0 translate-x-1/3 -translate-y-1/4">
                  </div>
                  <?php if ($ik): ?>
                    <img class="absolute z-10 right-0 top-0" src="<?php echo esc_url($ik['url']); ?>"
                      alt="<?php echo $alt; ?>" title="<?php echo $title; ?>">
                  <?php endif; ?>
                  <div class="w-full max-w-53.5 relative z-10">
                    <?php if ($zagolovok): ?>
                      <h5 class="font-medium text-lg mb-2"><?php echo esc_html($zagolovok); ?></h5>
                    <?php endif; ?>
                    <?php if ($tekst): ?>
                      <div class="text-sm/[120%]"><?php echo $tekst; ?></div>
                    <?php endif; ?>
                  </div>
                </a>
              </div>
            <?php endforeach; ?>
          </div>
          <div class="hero-right-swiper-scrollbar swiper-scrollbar"></div>
        </div>
      <?php endif; ?>
    </div>

    <!-- Bottom -->
    <?php
    $blocks = get_field('nizhnijblokpodslajderom', 'option');
    if ($blocks):
      ?>
      <div class="hero-bottom grid grid-cols-3 gap-3 mb-10">
        <?php foreach ($blocks as $block):
          $stiker = $block['stiker_new'];
          $zagolovok = $block['zagolovok'];
          $ssylkanopki = $block['ssylkaknopki'];
          $kartinka = $block['kartinka'];
          $czvet_fona = $block['czvet_fona'];
          $bg_style = $czvet_fona ? 'background-color:' . esc_attr($czvet_fona) . ';' : '';
          $alt = esc_attr($zagolovok);
          $title = esc_attr($zagolovok);
          ?>
          <a href="<?php echo $ssylkanopki ? esc_url($ssylkanopki) : '#'; ?>"
            class="hero-bottom__item relative flex flex-col justify-between gap-5 h-68 text-white rounded-3xl p-6 overflow-hidden"
            style="<?php echo $bg_style; ?>">
            <div class="space-y-4">
              <?php if ($stiker): ?>
                <div class="hero-bottom__badge w-fit bg-brand-greed rounded-full px-2 py-1">
                  <span class="uppercase font-semibold text-sm">NEW</span>
                </div>
              <?php endif; ?>
              <?php if ($zagolovok): ?>
                <h4 class="hero-bottom__title max-w-65.5 relative z-10 font-semibold text-[32px]/[120%]">
                  <?php echo esc_html($zagolovok); ?>
                </h4>
              <?php endif; ?>
            </div>
            <div class="link link--white">
              <div class="link__icon">
                <svg width="6" height="10">
                  <use xlink:href="<?php echo get_template_directory_uri(); ?>/images/sprite.svg#arrow"></use>
                </svg>
              </div>
              <p class="link__text">Подробнее</p>
            </div>
            <?php if ($kartinka): ?>
              <img class="h-full absolute z-0 bottom-0 right-0" src="<?php echo esc_url($kartinka['url']); ?>"
                alt="<?php echo $alt; ?>" title="<?php echo $title; ?>">
            <?php endif; ?>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>


    <!-- Часто ищут: -->
    <?php
    $tiles = get_field('bw_pt-home1', 'option');
    if ($tiles):
      $tiles = array_slice($tiles, 0, 10); // максимум 10 элементов
      ?>
      <div class="often-search">
        <div class="often-search-header">
          <h6 class="font-medium">Часто ищут:</h6>
          <a class="often-search-header__show-more" href="/shop/">Посмотреть ещё</a>
        </div>
        <div class="often-search__items-wrapper">
          <div class="often-search__items flex items-center gap-3 whitespace-nowrap">
            <?php foreach ($tiles as $tile):
              $name = $tile['bw_pt-home2'];
              $link = $tile['bw_pt-home3'];
              $icon = $tile['nazvanie_ikonki_v_sprite'];
              $alt = esc_attr($name);
              $title = esc_attr($name);
              ?>
              <a href="<?php echo esc_url($link ?: '#'); ?>"
                class="shrink-0 hover-border-blue bg-white rounded-xl flex items-center gap-2 py-2 px-4">
                <svg width="24" height="24">
                  <use
                    xlink:href="<?php echo get_template_directory_uri(); ?>/images/sprite.svg#catalog--<?php echo esc_html($icon); ?>">
                  </use>
                </svg>
                <span><?php echo esc_html($name); ?></span>
              </a>
            <?php endforeach; ?>
          </div>
        </div>

      </div>
    <?php endif; ?>

  </div>
</section>