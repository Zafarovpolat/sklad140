<?php
get_header();
while (have_posts()) : the_post();
$uroven_dohoda = get_field('uroven_dohoda');
$trebuemyj_opyt_raboty = get_field('trebuemyj_opyt_raboty');
$grafik_raboty = get_field('grafik_raboty');
$obrazovanie = get_field('obrazovanie');
$tekst_posle_opisaniya = get_field('tekst_posle_opisaniya_vakansij_kontakt');
$uroven_dohoda = $uroven_dohoda ? number_format((int)$uroven_dohoda, 0, '', ' ') . ' руб' : false;
$content = get_the_content();
$content = strip_tags($content, '<h6><ul><li>');
$content = preg_replace('/\s+/', ' ', $content);
$blocks = preg_split('/(<h6[^>]*>.*?<\/h6>)/is', $content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
$current_id = get_the_ID();
$vacancies = new WP_Query([
    'post_type'      => 'vacancy',
    'posts_per_page' => 10,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
]);
?>
<link rel="stylesheet" href="<?= get_template_directory_uri(); ?>/css/about-vacancy.min.css" />
<section class="vacancy-section">
    <div class="container">
        <div class="vacancy__inner">
            <div class="vacancy">
                <div class="vacancy-top">
                    <h1 class="vacancy-top__title"><?php the_title(); ?></h1>
                    <?php if ($uroven_dohoda): ?><p class="vacancy-top__earning">Уровень дохода: <?= esc_html($uroven_dohoda); ?></p><?php endif; ?>
                    <div class="vacancy-top__features">
                        <?php if ($trebuemyj_opyt_raboty): ?><p class="vacancy-top__features-text">Требуемый опыт работы: <span><?= esc_html($trebuemyj_opyt_raboty); ?></span></p><?php endif; ?>
                        <?php if ($grafik_raboty): ?><p class="vacancy-top__features-text">График работы: <span><?= esc_html($grafik_raboty); ?></span></p><?php endif; ?>
                        <?php if ($obrazovanie): ?><p class="vacancy-top__features-text">Образование: <span><?= esc_html($obrazovanie); ?></span></p><?php endif; ?>
                    </div>
                    <button class="button button--fill vacancy-top__btn respond">Откликнуться</button>
                </div>

                <div class="vacancy-about">
                    <?php
                    for ($i = 0; $i < count($blocks); $i++) {
                        if (preg_match('/<h6/i', $blocks[$i])) {
                            $title = $blocks[$i];
                            $text  = isset($blocks[$i + 1]) ? $blocks[$i + 1] : '';
                            echo '<div class="vacancy-about__paragraph">';
                            echo '<h6 class="vacancy-about__title">' . strip_tags($title) . '</h6>';
                            if (preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $text, $matches)) {
                                echo '<ul class="vacancy-about__list">';
                                foreach ($matches[1] as $li) echo '<li class="vacancy-about__list-item">' . trim($li) . '</li>';
                                echo '</ul>';
                            }
                            echo '</div>';
                        }
                    }
                    ?>
                    <?php if ($tekst_posle_opisaniya): ?>
					    <?php
					    $text = trim($tekst_posle_opisaniya);
					    $text = preg_replace('/^<p>(.*?)<\/p>$/is', '$1', $text);
					    $text = preg_replace('/<p>\s*<\/p>/i', '', $text);
					    $text = str_replace(["\r\n", "\r", "\n"], '<br>', $text);
					    ?>
					    <div class="vacancy-about__block"><?= wp_kses_post($text); ?></div>
					<?php endif; ?>
                </div>
            </div>

            <div class="available-vacancies">
                <p class="available-vacancies__title">Доступные вакансии</p>
                <div class="available-vacancies__items">
                    <a href="<?= esc_url(get_permalink($current_id)); ?>" class="available-vacancies__link available-vacancies__link--active"><?php the_title(); ?></a>
                    <?php if ($vacancies->have_posts()): while ($vacancies->have_posts()): $vacancies->the_post(); if (get_the_ID() === $current_id) continue; ?>
                        <a href="<?php the_permalink(); ?>" class="available-vacancies__link"><?php the_title(); ?></a>
                    <?php endwhile; wp_reset_postdata(); endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php endwhile; get_footer(); ?>