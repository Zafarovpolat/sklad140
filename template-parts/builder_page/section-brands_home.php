<!-- Бренды оборудования -->
<?php
$brands = get_field('home_brands', 'option');
if ($brands):
?>
<section class="brands">
    <div class="container">
        <div class="section-header">
            <div class="section-header__left">
                <div class="section-header__icon">
                    <img src="<?= get_template_directory_uri(); ?>/images/content/others/emoji-with-glasses.png" alt="Cool emoji" />
                </div>
                <h2 class="section-header__title">Бренды оборудования</h2>
            </div>

            <img src="<?= get_template_directory_uri(); ?>/images/gif/drag-mobile.gif" loading="lazy" alt="drag"
                class="section-header__icon--drag-mobile">

            <div class="swiper-navigation flex items-center gap-5">
                <button class="swiper-button-brands--prev drop-shadow flex items-center justify-center size-10 bg-white rounded-full">
                    <svg width="7" height="12" style="transform: rotate(180deg)">
                        <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow"></use>
                    </svg>
                </button>
                <button class="swiper-button-brands--next drop-shadow flex items-center justify-center size-10 bg-white rounded-full">
                    <svg width="7" height="12">
                        <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow"></use>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Desktop swiper -->
        <div class="brands-swiper swiper">
            <div class="swiper-wrapper">
                <?php
                $slide = [];
                foreach ($brands as $brand) {
                    if (!empty($brand['logotip'])) {
                        foreach ($brand['logotip'] as $img) {
                            $slide[] = $img;
                        }
                    }
                }
                $chunks = array_chunk($slide, 2);
                foreach ($chunks as $chunk): ?>
                    <div class="swiper-slide">
                        <?php foreach ($chunk as $image): ?>
                            <div class="brands__item">
                                <img src="<?= esc_url($image['sizes']['medium']); ?>"
                                     srcset="<?= esc_url($image['sizes']['medium']); ?> 1x, <?= esc_url($image['sizes']['large']); ?> 2x"
                                     alt="<?= esc_attr($image['alt'] ?: 'Логотип'); ?>"
                                     loading="lazy">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="swiper-pagination__wrapper">
                <div class="swiper-pagination__left">
                    <p class="swiper-pagination__subtitle">Зажми и потяни</p>
                    <img class="swiper-pagination__icon"
                         src="<?= get_template_directory_uri(); ?>/images/gif/drag.gif" loading="lazy" alt="">
                </div>
                <div class="brands-swiper-pagination swiper-pagination swiper-scrollbar"></div>
            </div>
        </div>

        <!-- Mobile swiper -->
        <div class="brands-swiper brands-swiper--mobile swiper">
            <div class="swiper-wrapper">
                <?php $mobileChunks = array_chunk($slide, 4);
                foreach ($mobileChunks as $chunk): ?>
                    <div class="swiper-slide">
                        <?php foreach ($chunk as $image): ?>
                            <div class="brands__item">
                                <img src="<?= esc_url($image['sizes']['medium']); ?>"
                                     srcset="<?= esc_url($image['sizes']['medium']); ?> 1x, <?= esc_url($image['sizes']['large']); ?> 2x"
                                     alt="<?= esc_attr($image['alt'] ?: 'Логотип'); ?>"
                                     loading="lazy">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="swiper-pagination__wrapper">
                <div class="swiper-pagination__left">
                    <p class="swiper-pagination__subtitle">Зажми и потяни</p>
                    <img class="swiper-pagination__icon"
                         src="<?= get_template_directory_uri(); ?>/images/gif/drag.gif" loading="lazy" alt="">
                </div>
                <div class="brands-swiper-pagination swiper-pagination swiper-scrollbar"></div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>