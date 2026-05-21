<?php
/**
 * Template Name: Выкуп оборудования
 */

get_header();

?>
<link rel="stylesheet" href="<?= get_template_directory_uri(); ?>/css/equipment-purchase.min.css" />
<script src="<?= get_template_directory_uri(); ?>/js/minified/equipment-purchase.min.js" defer></script>

<?php
$banner = get_field('banner');
$zagolovok    = $banner['zagolovok'] ?? '';
$tekst        = $banner['tekst'] ?? '';
$tekst_knopki = $banner['tekst_knopki'] ?? '';
$kartinka     = $banner['kartinka'] ?? '';
$czvet_fona   = $banner['czvet_fona'] ?? '';
?>
<section class="hero">
    <div class="container">
        <div class="hero__inner" <?php if ($czvet_fona): ?>style="background-color: <?= esc_attr($czvet_fona); ?>;"<?php endif; ?>>
            <?php if ($zagolovok): ?>
                <h1 class="hero__title"><?= esc_html($zagolovok); ?></h1>
            <?php endif; ?>
            <?php if ($tekst): ?>
                <?php
                $items = wp_strip_all_tags($tekst);
                $items = explode("\n", $items);
                $items = array_filter($items);
                ?>
                <ul class="hero__list">
                    <?php foreach ($items as $item): ?>
                        <li class="hero__list-item"><?= esc_html($item); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <?php if ($tekst_knopki): ?>
                <button class="callme-back button button--fill"><?= esc_html($tekst_knopki); ?></button>
            <?php endif; ?>
            <?php if ($kartinka): ?>
                <img class="hero__img"
                     src="<?= esc_url($kartinka['url']); ?>"
                     srcset="<?= esc_url($kartinka['url']); ?> 1x, <?= esc_url($kartinka['url']); ?> 2x"
                     alt="<?= esc_attr($kartinka['alt']); ?>">
            <?php endif; ?>

        </div>
    </div>
</section>

<?php
$blok = get_field('blok_informaczii');
if ($blok) {
    $tekst = $blok['tekst'];
    $img = $blok['kartinka'];
?>
<section class="about">
    <div class="container">
        <div class="about__inner">
            <div class="about-info">
                <?php
                if ($tekst) {
                    $content = $tekst;
                    $content = preg_replace('/<h2([^>]*)>/', '<h2 class="about-info__title"$1>', $content);
                    $content = preg_replace('/<ul([^>]*)>/', '<ul class="about-info__list"$1>', $content);
                    $content = preg_replace('/<li([^>]*)>/', '<li class="about-info__list-item"$1>', $content);
                    $content = preg_replace('/<p([^>]*)>/', '<p class="about-info__text"$1>', $content);
                    echo $content;
                }
                ?>
            </div>
            <?php if ($img): ?>
            <div class="about__img">
                <img src="<?php echo esc_url($img['url']); ?>" alt="<?php echo esc_attr($img['alt']); ?>">
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php } ?>

<?php
$my = get_field('blok_my_vykupaem');
$icons = [
    'shop-white',
    'tableware',
    'croissant',
    'food-production',
    'cold-storage-rooms',
    'sandwich-panels'
];
if ($my && !empty($my['zagolovok_bloka']) && !empty($my['bloki'])):
?>
<section class="buying-out">
    <div class="container">
        <div class="section-header">
            <div class="section-header__left flex items-center gap-4">
                <div class="section-header__icon">
                    <img src="<?= get_template_directory_uri(); ?>/images/content/others/money-bag.png" alt="">
                </div>
                <h2 class="section-header__title"><?= esc_html($my['zagolovok_bloka']); ?></h2>
            </div>
            <img src="<?= get_template_directory_uri(); ?>/images/gif/drag-mobile.gif" loading="lazy" alt="" class="section-header__icon--drag-mobile">
        </div>
        <div class="buying-out__inner">
            <?php foreach ($my['bloki'] as $i => $item): ?>
                <div class="buying-out-item">
                    <div class="buying-out-item__header">
                        <div class="buying-out-item__icon">
                            <svg width="18" height="18">
                                <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#<?= $icons[$i] ?? $icons[0]; ?>"></use>
                            </svg>
                        </div>
                        <p class="buying-out-item__title"><?= esc_html($item['zagolovok']); ?></p>
                    </div>
                    <p class="buying-out-item__text"><?= esc_html($item['tekst']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="buying-out-swiper swiper">
            <div class="swiper-wrapper">
                <?php foreach ($my['bloki'] as $i => $item): ?>
                    <div class="swiper-slide">
                        <div class="buying-out-item">
                            <div class="buying-out-item__header">
                                <div class="buying-out-item__icon">
                                    <svg width="18" height="18">
                                        <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#<?= $icons[$i] ?? $icons[0]; ?>"></use>
                                    </svg>
                                </div>
                                <p class="buying-out-item__title"><?= esc_html($item['zagolovok']); ?></p>
                            </div>
                            <p class="buying-out-item__text"><?= esc_html($item['tekst']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="buying-out-swiper-scrollbar swiper-scrollbar"></div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
$why = get_field('blok_pochemu_vybirayut_nas');
if ($why) {
    $title = $why['zagolovok_bloka'] ?? '';
    $blocks = $why['bloki'] ?? [];
}
$classes = ['pluses-item--green', 'pluses-item--beige', 'pluses-item--rad'];
?>
<section class="pluses">
    <div class="container">
        <div class="section-header">
            <div class="section-header__left flex items-center gap-4">
                <div class="section-header__icon">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/content/others/emoji-with-glasses.png" alt="">
                </div>
                <h2 class="section-header__title"><?php echo esc_html($title); ?></h2>
            </div>
            <img src="<?php echo get_template_directory_uri(); ?>/images/gif/drag-mobile.gif" loading="lazy" alt="drag" class="section-header__icon--drag-mobile">
        </div>
        <?php if (!empty($blocks)) : ?>
        <div class="pluses__inner">
            <?php foreach ($blocks as $i => $item) :
                $zag = $item['zagolovok'] ?? '';
                $txt = $item['tekst'] ?? '';
                $img = $item['kartinka']['url'] ?? '';
                $bg = $item['czvet_fona'] ?? '#ffffff';
                $class = $classes[$i % 3];
            ?>
            <div class="pluses-item <?php echo $class; ?>">
                <h5 class="pluses-item__title"><?php echo esc_html($zag); ?></h5>
                <p class="pluses-item__text"><?php echo wp_kses_post($txt); ?></p>
                <?php if ($img): ?>
                    <img class="pluses-item__img" src="<?php echo esc_url($img); ?>" alt="">
                <?php endif; ?>
                <div class="pluses-item__circle" style="background-color: <?php echo esc_attr($bg); ?>;"></div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="pluses-swiper swiper">
            <div class="swiper-wrapper">
                <?php foreach ($blocks as $i => $item) :
                    $zag = $item['zagolovok'] ?? '';
                    $txt = $item['tekst'] ?? '';
                    $img = $item['kartinka']['url'] ?? '';
                    $bg = $item['czvet_fona'] ?? '#ffffff';
                    $class = $classes[$i % 3];
                ?>
                <div class="swiper-slide">
                    <div class="pluses-item <?php echo $class; ?>">
                        <h5 class="pluses-item__title"><?php echo esc_html($zag); ?></h5>
                        <p class="pluses-item__text"><?php echo wp_kses_post($txt); ?></p>
                        <?php if ($img): ?>
                            <img class="pluses-item__img" src="<?php echo esc_url($img); ?>" alt="">
                        <?php endif; ?>

                        <div class="pluses-item__circle" style="background-color: <?php echo esc_attr($bg); ?>;"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="pluses-swiper-scrollbar swiper-scrollbar"></div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php
$blok = get_field('blok_nashi_raboty');
if ($blok):
    $title = $blok['zagolovok_bloka'] ?? '';
    $items = $blok['raboty'] ?? [];
?>
<section class="projects">
    <div class="container">
        <div class="section-header">
            <div class="section-header__left flex items-center gap-4">
                <div class="section-header__icon">
                    <img src="<?= get_template_directory_uri(); ?>/images/content/others/bag-emoji.png" alt="">
                </div>
                <h2 class="section-header__title"><?= esc_html($title); ?></h2>
            </div>
            <div class="swiper-navigation flex items-center gap-5">
                <button class="swiper-button-projects--prev drop-shadow flex items-center justify-center size-10 bg-white rounded-full">
                    <svg width="7" height="12" style="transform: rotate(180deg)">
                        <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow"></use>
                    </svg>
                </button>
                <button class="swiper-button-projects--next drop-shadow flex items-center justify-center size-10 bg-white rounded-full">
                    <svg width="7" height="12">
                        <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow"></use>
                    </svg>
                </button>
            </div>
            <img src="<?= get_template_directory_uri(); ?>/images/gif/drag-mobile.gif" loading="lazy" alt="drag" class="section-header__icon--drag-mobile">
        </div>
        <div class="projects-swiper swiper">
            <div class="swiper-wrapper">
                <?php foreach ($items as $it):
                    $img = $it['kartinka'] ?? null;
                    $url = $it['ssylka_na_video'] ?? '';
                ?>
                    <div class="swiper-slide">
                        <?php if ($img): ?>
                            <img class="projects-item__img"
                                 src="<?= esc_url($img['url']); ?>"
                                 alt="<?= esc_attr($img['alt']); ?>">
                        <?php endif; ?>
                        <?php if ($url): ?>
                            <button class="about-left__play projects-item__play size-15 rounded-full overflow-hidden bg-brand-blue flex items-center justify-center"
                                    onclick="window.open('<?= esc_url($url); ?>','_blank')">
                                <svg width="20" height="20">
                                    <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#play"></use>
                                </svg>
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="projects-swiper-scrollbar swiper-scrollbar"></div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
$klients = get_field('blok_nashi_klienty');
$zag = $klients['zagolovok_bloka'] ?? '';
$logos = $klients['logotip_brendov'] ?? [];
?>
<?php if ($zag || !empty($logos)): ?>
<section class="clients">
    <div class="container">
        <div class="section-header">
            <div class="section-header__left flex items-center gap-4">
                <div class="section-header__icon">
                    <img src="<?= get_template_directory_uri(); ?>/images/content/others/woman-emoji.png" alt="">
                </div>
                <h2 class="section-header__title"><?= esc_html($zag); ?></h2>
            </div>
        </div>
        <?php if (!empty($logos)): ?>
        <div class="clients__inner">
            <?php foreach ($logos as $logo): ?>
                <?php
                if (is_array($logo)) {
                    $id = $logo['ID'] ?? null;
                } else {
                    $id = $logo;
                }
                if (!$id) continue;
                $url = wp_get_attachment_image_url($id, 'full');
                $alt = get_post_meta($id, '_wp_attachment_image_alt', true);
                $meta = get_post($id);
                $title = $meta->post_title ?? '';
                ?>
                <div class="clients-item">
                    <img src="<?= esc_url($url); ?>"
                         alt="<?= esc_attr($alt ?: $title); ?>"
                         title="<?= esc_attr($title); ?>">
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

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
