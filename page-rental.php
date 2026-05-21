<?php
/**
 * Template Name: Аренда оборудования
 */

get_header();

?>
<link rel="stylesheet" href="<?= get_template_directory_uri(); ?>/css/equipment-rental.min.css" />

<?php
$banner = get_field('banner');
$zag = $banner['zagolovok'] ?? '';
$text_raw = $banner['tekst'] ?? '';
$img = $banner['kartinka'] ?? null;
$bg = $banner['czvet_fona'] ?? '';
$link = $banner['ssylka_na_katalog'] ?? '';
?>
<?php if ($banner): ?>
<section class="hero">
    <div class="container">
        <div class="hero__inner" style="background-color: <?= esc_attr($bg); ?>;">
            <?php if ($zag): ?>
                <h1 class="hero__title"><?= esc_html($zag); ?></h1>
            <?php endif; ?>
            <?php if ($text_raw): ?>
                <?php
                $text = $text_raw;
                $text = str_replace('<p>', '<p class="hero__text">', $text);
                $text = str_replace('<ul>', '<ul class="hero__list">', $text);
                $text = str_replace('<li>', '<li class="hero__list-item">', $text);
                ?>
                <?= $text; ?>
            <?php endif; ?>
            <?php if ($link): ?>
                <a href="<?= esc_url($link); ?>" class="button button--fill">Открыть каталог</a>
            <?php else: ?>
                <button class="button button--fill">Открыть каталог</button>
            <?php endif; ?>
            <?php
            if (is_array($img)) {
                $img_url = wp_get_attachment_image_url($img['ID'], 'full');
                $img_alt = get_post_meta($img['ID'], '_wp_attachment_image_alt', true);
            } else {
                $img_url = wp_get_attachment_image_url($img, 'full');
                $img_alt = get_post_meta($img, '_wp_attachment_image_alt', true);
            }
            ?>
            <?php if ($img_url): ?>
                <img class="hero__img"
                     src="<?= esc_url($img_url); ?>"
                     alt="<?= esc_attr($img_alt); ?>">
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="guide">
    <div class="container">
        <div class="guide__inner">
            <?php
            $blocks = get_field('informacziya')['bloki'] ?? [];
            $title_cont = get_field('zagolovok_cont', 'option');
            $phone      = get_field('phone', 'option');
            $email      = get_field('pochta', 'option');
            $whatsapp   = get_field('ssylka_na_whatsapp', 'option');
            function norm_phone($phone) {
                $n = preg_replace('/\D+/', '', $phone);
                if (strpos($n, '8') === 0) $n = '7' . substr($n,1);
                elseif (strpos($n,'7') !== 0) $n = '7'.$n;
                return '+'.$n;
            }
            if ($blocks):
                foreach ($blocks as $i => $b):
                    $title = $b['zagolovok'] ?? '';
                    $content = $b['soderzhanie'] ?? '';
                    $content = str_replace('<ul>', '<ul class="guide-block__list">', $content);
                    $content = str_replace('<li>', '<li class="guide-block__list-item">', $content);
                    $content = str_replace('<p>', '<p class="guide-block__text">', $content);
                    $content = str_replace('<a ', '<a class="guide-block__link" ', $content);
                    $classes = [
                        0 => 'guide-block guide-conditions',
                        1 => 'guide-block guide-how-to-rent',
                        2 => 'guide-block guide-how-it-works'
                    ];
                    $cls = $classes[$i] ?? 'guide-block';
            ?>
                <div class="<?php echo $cls; ?>">
                    <div class="guide-block__header">
                        <div class="guide-block__icon">
                            <svg width="16" height="16">
                                <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#<?php echo $i === 0 ? 'check-file' : ($i === 1 ? 'calendar-time' : 'diagram'); ?>"></use>
                            </svg>
                        </div>
                        <h5 class="guide-block__title"><?php echo esc_html($title); ?></h5>
                    </div>
                    <?php if ($i === 1): ?>
                        <p class="guide-block__text">Чтобы получить оборудование во временное пользование, свяжитесь с нами:</p>
                        <div class="guide-block__row">
                            <a class="guide-block__link" href="tel:<?php echo norm_phone($phone); ?>"><?php echo esc_html($phone); ?></a>
                            <?php if ($whatsapp): ?>
                                <a class="guide-block__whatsapp" href="<?php echo esc_url($whatsapp); ?>" target="_blank" rel="noopener">
                                    <svg width="30" height="30">
                                        <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#whatsapp-green"></use>
                                    </svg>
                                </a>
                            <?php endif; ?>
                            <p class="guide-block__text"><span>или</span></p>
                            <a class="guide-block__link" href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
                        </div>
                    <?php else: ?>
                        <?php echo $content; ?>
                    <?php endif; ?>
                </div>
            <?php
                endforeach;
            endif;
            ?>
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
                        <input class="input other-questions-form__input" type="tel" placeholder="+7 (999) 999 99 99">
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

<?php
$title     = get_field('zagolovok_cont', 'option');
$phone     = get_field('phone', 'option');
$email     = get_field('pochta', 'option');
$address   = get_field('address', 'option');
$graffik    = get_field('graffik', 'option');
$whatsapp  = get_field('ssylka_na_whatsapp', 'option');
$telegram  = get_field('ssylka_na_tg', 'option');
$map_script = get_field('karta_skript_konstruktora_yandeks', 'option');
function normalize_phone($phone) {
    $num = preg_replace('/\D+/', '', $phone);
    if (strpos($num, '8') === 0) {
        $num = '7' . substr($num, 1);
    }
    elseif (strpos($num, '7') !== 0) {
        $num = '7' . $num;
    }
    return '+' . $num;
}
?>
<section class="map">
    <div class="map__img">
        <?= $map_script ?>
    </div>
    <div class="container">
        <div class="map-contacts">
            <div class="map-contacts-item">
                <p class="map-contacts-item__title">
                    <?= esc_html($title) ?>
                </p>
                <?php if ($phone): ?>
                    <a class="map-contacts-item__text" href="tel:<?= normalize_phone($phone) ?>">
                        <?= esc_html($phone) ?>
                    </a>
                <?php endif; ?>
                <?php if ($email): ?>
                    <a class="map-contacts-item__text" href="mailto:<?= esc_attr($email) ?>">
                        <?= esc_html($email) ?>
                    </a>
                <?php endif; ?>
            </div>
            <div class="map-contacts-item map-contacts-item--social">
                <p class="map-contacts-item__title">Мы в соцсетях</p>
                <?php if ($whatsapp): ?>
                <a class="map-contacts-item__text" href="<?= esc_url($whatsapp) ?>" target="_blank">
                    <svg width="30" height="30">
                        <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#whatsapp-green"></use>
                    </svg>
                    WhatsApp
                </a>
                <?php endif; ?>
                <?php if ($telegram): ?>
                <a class="map-contacts-item__text" href="<?= esc_url($telegram) ?>" target="_blank">
                    <svg width="30" height="30">
                        <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#telegram-dark"></use>
                    </svg>
                    Telegram
                </a>
                <?php endif; ?>
            </div>
            <div class="map-contacts-item">
                <p class="map-contacts-item__title">Адрес склада</p>
                <?php if ($address): ?>
                    <span class="map-contacts-item__text"><?= esc_html($address) ?></span>
                <?php endif; ?>
                <?php if ($graffik): ?>
                    <span class="map-contacts-item__text"><?= esc_html($graffik) ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
