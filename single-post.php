<?php
get_header();
?>
<link rel="stylesheet" href="<?= get_template_directory_uri(); ?>/css/article.min.css" />
<script src="<?= get_template_directory_uri(); ?>/js/minified/article.min.js" defer></script>

<section class="article-section">
    <div class="container">
        <div class="article__inner">
            <?php if (have_posts()): while (have_posts()): the_post(); ?>
                <?php
                $thumb_id = get_post_thumbnail_id();
                $img1 = $thumb_id ? wp_get_attachment_image_src($thumb_id, 'large')[0] : get_template_directory_uri().'/images/content/news/placeholder.webp';
                $img2 = $thumb_id ? wp_get_attachment_image_src($thumb_id, 'full')[0]  : $img1;
                $alt  = $thumb_id ? ( get_post_meta($thumb_id, '_wp_attachment_image_alt', true) ?: get_the_title() ) : get_the_title();
                ?>
                <div class="article">
                    <div class="article__img">
                        <img src="<?= esc_url($img1); ?>"
                             srcset="<?= esc_url($img1); ?> 1x, <?= esc_url($img2); ?> 2x"
                             alt="<?= esc_attr($alt); ?>">
                    </div>

                    <div class="article__meta">
                        <h1 class="article__title"><?= esc_html(get_the_title()); ?></h1>
                        <div class="article__date"><?= get_the_date('d.m.Y'); ?></div>
                    </div>

                    <div class="article-paragraph">
                        <?php
$content = apply_filters('the_content', get_the_content());

// Убираем <figure> обёртки, но оставляем <img>
$content = preg_replace('/<figure[^>]*>(.*?)<\/figure>/is', '$1', $content);

// Очищаем от лишних пустых тегов и балансируем HTML
$content = force_balance_tags(trim($content));

// Безопасный вывод с поддержкой HTML (p, img, h2–h6, ul/li, br и т.п.)
?>
<div class="article__text">
    <?= wp_kses_post($content); ?>
</div>

                    </div>
                </div>

                <?php
                // последние 5 записей, исключая текущую
                $recent = new WP_Query([
                    'post_type'      => 'post',
                    'posts_per_page' => 5,
                    'post__not_in'   => [get_the_ID()],
                    'orderby'        => 'date',
                    'order'          => 'DESC'
                ]);
                if ($recent->have_posts()):
                ?>
                    <div class="article-recommended">
                        <div class="article-recommended__header">
                            <h2 class="article-recommended__title">Недавние</h2>
                            <img src="<?= get_template_directory_uri(); ?>/images/gif/drag-mobile.gif" loading="lazy" alt="drag" class="section-header__icon--drag-mobile">
                        </div>

                        <div class="article-recommended__items">
                            <?php while ($recent->have_posts()): $recent->the_post();
                                $thumb_id = get_post_thumbnail_id();
                                $img1 = $thumb_id ? wp_get_attachment_image_src($thumb_id, 'large')[0] : get_template_directory_uri().'/images/content/news/placeholder.webp';
                                $img2 = $thumb_id ? wp_get_attachment_image_src($thumb_id, 'full')[0]  : $img1;
                            ?>
                                <article class="article-recommended-item">
                                    <a href="<?= esc_url(get_permalink()); ?>" class="article-recommended-item__img">
                                        <img src="<?= esc_url($img1); ?>"
                                             srcset="<?= esc_url($img1); ?> 1x, <?= esc_url($img2); ?> 2x"
                                             alt="<?= esc_attr(get_the_title()); ?>">
                                    </a>
                                    <div class="article-recommended-item__info">
                                        <a href="<?= esc_url(get_permalink()); ?>" class="article-recommended-item__title"><?= esc_html(get_the_title()); ?></a>
                                        <p class="article-recommended-item__date"><?= get_the_date('d.m.Y'); ?></p>
                                    </div>
                                </article>
                            <?php endwhile; wp_reset_postdata(); ?>
                        </div>

                        <div class="article-recommended-swiper swiper">
                            <div class="swiper-wrapper">
                                <?php
                                $recent->rewind_posts();
                                while ($recent->have_posts()): $recent->the_post();
                                    $thumb_id = get_post_thumbnail_id();
                                    $img1 = $thumb_id ? wp_get_attachment_image_src($thumb_id, 'large')[0] : get_template_directory_uri().'/images/content/news/placeholder.webp';
                                    $img2 = $thumb_id ? wp_get_attachment_image_src($thumb_id, 'full')[0]  : $img1;
                                ?>
                                    <div class="swiper-slide">
                                        <article class="news-card">
                                            <div class="news-card__info">
                                                <p class="news-card__date"><?= get_the_date('d.m.Y'); ?></p>
                                                <a href="<?= esc_url(get_permalink()); ?>" class="news-card__title"><?= esc_html(get_the_title()); ?></a>
                                                <p class="news-card__description"><?= esc_html(wp_trim_words(strip_tags(get_the_content()), 25, '...')); ?></p>
                                            </div>
                                            <div class="news-card__img">
                                                <a href="<?= esc_url(get_permalink()); ?>">
                                                    <img src="<?= esc_url($img1); ?>"
                                                         srcset="<?= esc_url($img1); ?> 1x, <?= esc_url($img2); ?> 2x"
                                                         alt="<?= esc_attr(get_the_title()); ?>">
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
                                <div class="articles-swiper-pagination swiper-pagination swiper-scrollbar"></div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endwhile; endif; ?>
        </div>
    </div>
</section>


<style>
.article__text {
  font-size: 18px;
  line-height: 1.75;
  color: #031343;
}

.article__text p {
  margin-bottom: 20px;
}

.article__text h2,
.article__text h3,
.article__text h4,
.article__text h5,
.article__text h6 {
  font-weight: 600;
  color: #031343;
  margin: 35px 0 15px;
  line-height: 1.3;
}

.article__text h2 { font-size: 28px; }
.article__text h3 { font-size: 24px; }
.article__text h4,
.article__text h5,
.article__text h6 { font-size: 20px; }

.article__text ul,
.article__text ol {
  margin: 20px 0 25px 25px;
  padding: 0;
}

.article__text li {
  position: relative;
  margin-bottom: 10px;
  line-height: 1.7;
}

.article__text ul li::before {
  content: "•";
  color: #007aff;
  font-weight: bold;
  position: absolute;
  left: -18px;
}

.article__text img {
  display: block;
  max-width: 100%;
  height: auto;
  border-radius: 12px;
  margin: 30px auto;
}

.article__text blockquote {
  background: #f6f9ff;
  border-left: 4px solid #007aff;
  padding: 20px 25px;
  margin: 30px 0;
  font-style: italic;
  color: #222;
  border-radius: 8px;
}

.article__text a {
  color: #007aff;
  text-decoration: underline;
  transition: 0.2s;
}

.article__text a:hover {
  color: #0056b3;
  text-decoration: none;
}

.article__text strong {
  font-weight: 700;
}

.article__text table {
  width: 100%;
  border-collapse: collapse;
  margin: 25px 0;
}

.article__text th,
.article__text td {
  border: 1px solid #dcdcdc;
  padding: 10px 15px;
  text-align: left;
}

.article__text th {
  background: #f2f4f7;
  font-weight: 600;
}

</style>

<?php
get_footer();
