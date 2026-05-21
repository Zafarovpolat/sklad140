<?php
/**
 * Template Name: Вакансии
 */

get_header();

?>
<link rel="stylesheet" href="<?= get_template_directory_uri(); ?>/css/vacancies.min.css" />
<section class="vacancies">
    <div class="container">
        <h1 class="vacancies__title"><?php echo esc_html( get_the_title() ); ?></h1>

        <?php
        $vacancies = new WP_Query([
            'post_type'      => 'vacancy',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);
        ?>

        <div class="vacancies__inner">
            <?php while ( $vacancies->have_posts() ) : $vacancies->the_post();
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
            <div class="vacancies-item">
                <div class="vacancies-item__info">
                    <h5 class="vacancies-item__info-title"><?php echo esc_html( get_the_title() ); ?></h5>
                    <div class="vacancies-item__info-options">
                        <div class="vacancies-item__info-option">
                            <p class="vacancies-item__info-option__title">Опыт</p>
                            <p class="vacancies-item__info-option__value"><?php echo esc_html( $exp ?: '—' ); ?></p>
                        </div>
                        <div class="vacancies-item__info-option">
                            <p class="vacancies-item__info-option__title">Образование</p>
                            <p class="vacancies-item__info-option__value"><?php echo esc_html( $edu ?: '—' ); ?></p>
                        </div>
                        <div class="vacancies-item__info-option">
                            <p class="vacancies-item__info-option__title">График</p>
                            <p class="vacancies-item__info-option__value"><?php echo esc_html( $sched ?: '—' ); ?></p>
                        </div>
                    </div>
                    <a href="<?php echo esc_url( get_permalink() ); ?>" class="link link--white">
                        <div class="link__icon">
                            <svg width="8" height="12"><use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow"></use></svg>
                        </div>
                        <p class="link__text">Подробнее</p>
                    </a>
                </div>

                <img
                    src="<?php echo esc_url($src1); ?>"
                    srcset="<?php echo esc_url($src1); ?> 1x, <?php echo esc_url($src2); ?> 2x"
                    alt="<?php echo esc_attr($alt); ?>"
                    class="vacancies-item__img">

                <div class="vacancies-item__circle vacancies-item__circle--top"></div>
                <div class="vacancies-item__circle vacancies-item__circle--bottom"></div>
            </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
</section>

<section class="other-questions">
    <div class="container">
        <div class="other-questions__holder">
            <div class="other-questions__inner">
                <h2 class="other-questions__title">Остались вопросы?</h2>
                <p class="other-questions__subtitle">Оставьте свои контактные данные и наш менеджер свяжется с вами.</p>
                <form class="other-questions-form" action="#">
                    <div class="other-questions-form__row">
                        <input class="input other-questions-form__input" type="text" placeholder="Имя">
                        <input class="input other-questions-form__input phone_mask" type="tel" placeholder="+7 (999) 999 99 99">
                    </div>
                    <button class="button button--dark other-questions-form__btn">Отправить</button>
                    <div class="other-questions-form__privacy-policy">
                        <input class="input-checkbox__input other-questions-form__checkbox" type="checkbox">
                        <p class="other-questions-form__text">Я ознакомился и согласен с <a href="/privacy-policy/" target="_blank" rel="noopener">политикой конфиденциальности</a> в отношении хранения и обработки персональных данных</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
