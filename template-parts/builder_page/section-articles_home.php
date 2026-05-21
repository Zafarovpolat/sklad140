<!-- Последние новости -->
<section class="news">
    <div class="container">
        <div class="section-header">
            <div class="section-header__left">
                <div class="section-header__icon">
                    <img src="<?= get_template_directory_uri(); ?>/images/content/others/bell-emoji.png" alt="Bell emoji" />
                </div>
                <h2 class="section-header__title">Последние новости</h2>
            </div>
            <img src="<?= get_template_directory_uri(); ?>/images/gif/drag-mobile.gif" loading="lazy" alt="drag" class="section-header__icon--drag-mobile">
            <a href="/category/stati/" class="link">
                <div class="link__icon">
                    <svg width="8" height="12">
                        <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow-white"></use>
                    </svg>
                </div>
                <p class="link__text">Все новости</p>
            </a>
        </div>

        <?php
        $news = new WP_Query([
            'post_type'      => 'post',
            'posts_per_page' => 10,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);
        ?>

        <?php if ($news->have_posts()): ?>
            <div class="news-swiper swiper">
                <div class="swiper-wrapper">
                    <?php while ($news->have_posts()): $news->the_post();
                        $date = get_the_date('d.m.Y');
                        $excerpt = wp_trim_words(strip_tags(get_the_content()), 30, '...');
                        $thumb_id = get_post_thumbnail_id();
                        $img1 = $thumb_id ? wp_get_attachment_image_src($thumb_id, 'large') : null;
                        $img2 = $thumb_id ? wp_get_attachment_image_src($thumb_id, 'full') : null;
                        $ph   = get_template_directory_uri() . '/images/content/news/placeholder.webp';
                        $src1 = $img1[0] ?? $ph;
                        $src2 = $img2[0] ?? $src1;
                        $alt  = $thumb_id ? ( get_post_meta($thumb_id, '_wp_attachment_image_alt', true) ?: get_the_title() ) : get_the_title();
                    ?>
                        <div class="swiper-slide">
                            <article class="news-card">
                                <div class="news-card__info">
                                    <p class="news-card__date"><?= esc_html($date); ?></p>
                                    <a href="<?= esc_url(get_permalink()); ?>" class="news-card__title"><?= esc_html(get_the_title()); ?></a>
                                    <p class="news-card__description"><?= esc_html($excerpt); ?></p>
                                </div>
                                <div class="news-card__img">
                                    <a href="<?= esc_url(get_permalink()); ?>">
                                        <img src="<?= esc_url($src1); ?>" srcset="<?= esc_url($src1); ?> 1x, <?= esc_url($src2); ?> 2x" alt="<?= esc_attr($alt); ?>">
                                    </a>
                                </div>
                            </article>
                        </div>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>

                <div class="swiper-pagination__wrapper">
                    <div class="swiper-pagination__left">
                        <p class="swiper-pagination__subtitle">Зажми и потяни</p>
                        <img class="swiper-pagination__icon" src="<?= get_template_directory_uri(); ?>/images/gif/drag.gif" loading="lazy" alt="">
                    </div>
                    <div class="news-swiper-pagination swiper-pagination swiper-scrollbar"></div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>