<section class="vacancies">
    <div class="container">
        <div class="section-header">
            <div class="section-header__left">
                <div class="section-header__icon">
                    <img src="<?= get_template_directory_uri(); ?>/images/content/others/magnifying-emoji.png" alt="Magnifying emoji" />
                </div>
                <h2 class="section-header__title">Мы ищем профи в нашу команду</h2>
            </div>
            <img src="<?= get_template_directory_uri(); ?>/images/gif/drag-mobile.gif" loading="lazy" alt="drag" class="section-header__icon--drag-mobile">
            <a href="/vacancy/" class="link">
                <div class="link__icon">
                    <svg width="8" height="12">
                        <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow-white"></use>
                    </svg>
                </div>
                <p class="link__text">Все вакансии</p>
            </a>
        </div>

        <?php
        $vacancies = new WP_Query([
            'post_type'      => 'vacancy',
            'posts_per_page' => 10,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);
        ?>

        <?php if ($vacancies->have_posts()): ?>
        <div class="vacancies-swiper swiper">
            <div class="swiper-wrapper">
                <?php while ($vacancies->have_posts()): $vacancies->the_post();
                    $exp   = get_field('trebuemyj_opyt_raboty');
                    $edu   = get_field('obrazovanie');
                    $sched = get_field('grafik_raboty');
                    $thumb_id = get_post_thumbnail_id();
                    $img1 = $thumb_id ? wp_get_attachment_image_src($thumb_id, 'large') : null;
                    $img2 = $thumb_id ? wp_get_attachment_image_src($thumb_id, 'full')  : null;
                    $ph   = get_template_directory_uri() . '/images/content/vacancies/placeholder.webp';
                    $src1 = $img1[0] ?? $ph;
                    $src2 = $img2[0] ?? $src1;
                    $alt  = $thumb_id ? ( get_post_meta($thumb_id, '_wp_attachment_image_alt', true) ?: get_the_title() ) : get_the_title();
                ?>
                <div class="swiper-slide">
                    <div class="vacancies-item">
                        <div class="vacancies-item__info">
                            <h5 class="vacancies-item__info-title"><?= esc_html(get_the_title()); ?></h5>
                            <div class="vacancies-item__info-options">
                                <div class="vacancies-item__info-option">
                                    <p class="vacancies-item__info-option__title">Опыт</p>
                                    <p class="vacancies-item__info-option__value"><?= esc_html($exp ?: '—'); ?></p>
                                </div>
                                <div class="vacancies-item__info-option">
                                    <p class="vacancies-item__info-option__title">Образование</p>
                                    <p class="vacancies-item__info-option__value"><?= esc_html($edu ?: '—'); ?></p>
                                </div>
                                <div class="vacancies-item__info-option">
                                    <p class="vacancies-item__info-option__title">График</p>
                                    <p class="vacancies-item__info-option__value"><?= esc_html($sched ?: '—'); ?></p>
                                </div>
                            </div>
                            <a href="<?= esc_url(get_permalink()); ?>" class="link link--white">
                                <div class="link__icon">
                                    <svg width="8" height="12">
                                        <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow"></use>
                                    </svg>
                                </div>
                                <p class="link__text">Подробнее</p>
                            </a>
                        </div>
                        <img loading="lazy" src="<?= esc_url($src1); ?>" srcset="<?= esc_url($src1); ?> 1x, <?= esc_url($src2); ?> 2x" alt="<?= esc_attr($alt); ?>" class="vacancies-item__img">
                        <div class="vacancies-item__circle vacancies-item__circle--top"></div>
                        <div class="vacancies-item__circle vacancies-item__circle--bottom"></div>
                    </div>
                </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
            <div class="swiper-pagination__wrapper">
                <div class="swiper-pagination__left">
                    <p class="swiper-pagination__subtitle">Зажми и потяни</p>
                    <img class="swiper-pagination__icon" src="<?= get_template_directory_uri(); ?>/images/gif/drag.gif" loading="lazy" alt="">
                </div>
                <div class="vacancies-swiper-pagination swiper-pagination swiper-scrollbar"></div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>