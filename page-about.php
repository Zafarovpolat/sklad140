<?php
/**
 * Template Name: О нас
 */
get_header();
?>
<link rel="stylesheet" href="<?= get_template_directory_uri(); ?>/css/about.min.css" />
<script src="<?= get_template_directory_uri(); ?>/js/minified/about.min.js" defer></script>
<section class="hero">
    <div class="container">
        <div class="hero__inner">
            <?php
            $banner = get_field('banner');
            if ($banner):
                $title = $banner['zagolovok'];
                $text  = $banner['tekst'];
                $img   = $banner['kartinka'];
                $bg    = $banner['czvet_fona'];
            ?>
            <div class="hero-top" style="background-color: <?= esc_attr($bg); ?>;">
                <h2 class="hero-top__title"><?= esc_html($title); ?></h2>
                <p class="hero-top__text"><?= esc_html($text); ?></p>
                <button class="callme-back button button--fill">Связаться с нами</button>
                <?php if ($img): ?>
                    <img class="hero-top__img"
                         src="<?= esc_url($img['url']); ?>"
                         srcset="<?= esc_url($img['url']); ?> 1x, <?= esc_url($img['url']); ?> 2x"
                         alt="<?= esc_attr($img['alt']); ?>">
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php
            $preim = get_field('bloki_preimushhestva', get_the_ID());
            if ($preim && !empty($preim['bloki'])):
                $circles = [
                    "hero-bottom-item__circle--green",
                    "hero-bottom-item__circle--beige",
                    "hero-bottom-item__circle--rad"
                ];
            ?>
                <div class="hero-bottom">
                    <?php 
                    $i = 0;
                    foreach ($preim['bloki'] as $item):
                        $zag   = $item['zagolovok'];
                        $text  = $item['tekst'];
                        $img   = $item['kartinka'];
                        $color = $item['czvet_fona'];
                        $circle = $circles[$i % 3];
                        $i++;
                    ?>
                        <div class="hero-bottom-item">
                            <?php if ($zag): ?>
                                <h5 class="hero-bottom-item__title"><?= esc_html($zag); ?></h5>
                            <?php endif; ?>
                            <?php if ($text): ?>
                                <p class="hero-bottom-item__text"><?= wp_kses_post($text); ?></p>
                            <?php endif; ?>
                            <?php if ($img): ?>
                                <img class="hero-bottom-item__img" 
                                     src="<?= esc_url($img['url']); ?>" 
                                     alt="<?= esc_attr($img['alt']); ?>">
                            <?php endif; ?>

                            <div class="hero-bottom-item__circle <?= $circle; ?>" style="<?= $color ? 'background-color: '.$color.';' : '' ?>"></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="hero-bottom-swiper">
                    <div class="swiper-wrapper">
                        <?php 
                        $i = 0;
                        foreach ($preim['bloki'] as $item):
                            $zag   = $item['zagolovok'];
                            $text  = $item['tekst'];
                            $img   = $item['kartinka'];
                            $color = $item['czvet_fona'];

                            $circle = $circles[$i % 3];
                            $i++;
                        ?>
                            <div class="swiper-slide">
                                <div class="hero-bottom-item" style="<?= $color ? 'background-color: '.$color.';' : '' ?>">
                                    <?php if ($zag): ?>
                                        <h5 class="hero-bottom-item__title"><?= esc_html($zag); ?></h5>
                                    <?php endif; ?>

                                    <?php if ($text): ?>
                                        <p class="hero-bottom-item__text"><?= wp_kses_post($text); ?></p>
                                    <?php endif; ?>

                                    <?php if ($img): ?>
                                        <img class="hero-bottom-item__img" 
                                             src="<?= esc_url($img['url']); ?>" 
                                             alt="<?= esc_attr($img['alt']); ?>">
                                    <?php endif; ?>

                                    <div class="hero-bottom-item__circle <?= $circle; ?>"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="hero-bottom-swiper-scrollbar swiper-scrollbar"></div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
$group = get_field('predostavlyaem_vozmozhnosti');
if ($group):
    $title = $group['zagolovok_bloka'];
    $items = $group['bloki'];
?>
<section class="abilities">
    <div class="container">
        <div class="section-header">
            <div class="section-header__left flex items-center gap-4">
                <div class="section-header__icon">
                    <img src="<?= get_template_directory_uri(); ?>/images/content/others/box-emoji.png" alt="Box emoji">
                </div>
                <h2 class="section-header__title">
                    <?= esc_html($title); ?>
                </h2>
            </div>
        </div>
        <div class="abilities__inner">
            <?php
            $i = 0;
            foreach ($items as $item):
                $i++;
                $zag   = $item['zagolovok'];
                $text  = $item['tekst'];
                $img   = $item['kartinka'];
                $img_url  = $img ? $img['url'] : '';
                $img_2x   = $img ? ($img['sizes']['large'] ?? $img_url) : '';
                $img_alt  = $img ? ($img['alt'] ?: $zag) : $zag;
                $clean_text = trim($text);
                $list_html = '';
                $p_html = '';
                if (strpos($clean_text, '<ul') !== false) {
                    $list_html = preg_replace(
                        '/.*?(<ul[\s\S]*<\/ul>)/',
                        '$1',
                        $clean_text
                    );
                    $before_ul = trim(str_replace($list_html, '', $clean_text));
                    $p_html = $before_ul ? '<p class="abilities-item__text">'.$before_ul.'</p>' : '';

                } else {
                    $p_html = '<p class="abilities-item__text">'.$clean_text.'</p>';
                }
                $btn_html = '';
                if ($i === 1) {
                    $btn_html = '<button class="callme-back button button--dark">Связаться с нами</button>';
                } elseif ($i === 2) {
                    $btn_html = '<button class="callme-back button button--white">Связаться с нами</button>';
                } elseif ($i === 3) {
                    $btn_html = '<button class="callme-back button button--fill">Связаться с нами</button>';
                } elseif ($i === 4) {
                    $btn_html = '<button class="callme-back button button--dark">Связаться с нами</button>';
                }
            ?>
                <div class="abilities-item">
                    <h3 class="abilities-item__title"><?= esc_html($zag); ?></h3>
                    <?= $p_html; ?>
                    <?php if ($list_html): ?>
                        <?= str_replace(
                            '<ul',
                            '<ul class="abilities-item__list"',
                            str_replace(
                                '<li',
                                '<li class="abilities-item__list-item"',
                                $list_html
                            )
                        ); ?>
                    <?php endif; ?>
                    <?= $btn_html; ?>
                    <?php if ($img_url): ?>
                        <img class="abilities-item__img"
                             src="<?= esc_url($img_url); ?>"
                             srcset="<?= esc_url($img_url); ?> 1x, <?= esc_url($img_2x); ?> 2x"
                             alt="<?= esc_attr($img_alt); ?>">
                             <?php if ($i === 3): ?>
                                <svg class="abilities-item__circle" width="390" height="302" viewBox="0 0 390 302" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M586.451 307.981C586.451 236.323 557.985 167.599 507.315 116.929C456.645 66.2589 387.921 37.7927 316.263 37.7927C244.604 37.7927 175.881 66.2589 125.211 116.929C74.5406 167.599 46.0744 236.323 46.0744 307.981L316.263 307.981H586.451Z" fill="#E0FFD0"></path>
                                <mask id="path-2-inside-1_747_12583" fill="white">
                                <path d="M597.838 305.911C597.838 229.859 567.627 156.923 513.851 103.147C460.074 49.3703 387.138 19.1591 311.087 19.1591C235.035 19.1591 162.099 49.3703 108.323 103.147C54.5463 156.923 24.3351 229.859 24.3351 305.911L311.087 305.911H597.838Z"></path>
                                </mask>
                                <path d="M597.838 305.911C597.838 229.859 567.627 156.923 513.851 103.147C460.074 49.3703 387.138 19.1591 311.087 19.1591C235.035 19.1591 162.099 49.3703 108.323 103.147C54.5463 156.923 24.3351 229.859 24.3351 305.911L311.087 305.911H597.838Z" stroke="#E0FFD0" stroke-width="2" mask="url(#path-2-inside-1_747_12583)"></path>
                                <mask id="path-3-inside-2_747_12583" fill="white">
                                <path d="M632 316.263C632 232.524 598.735 152.215 539.523 93.0027C480.31 33.7905 400.001 0.525397 316.263 0.525391C232.524 0.525384 152.215 33.7905 93.0027 93.0027C33.7905 152.215 0.525403 232.524 0.525391 316.263L316.263 316.263H632Z"></path>
                                </mask>
                                <path d="M632 316.263C632 232.524 598.735 152.215 539.523 93.0027C480.31 33.7905 400.001 0.525397 316.263 0.525391C232.524 0.525384 152.215 33.7905 93.0027 93.0027C33.7905 152.215 0.525403 232.524 0.525391 316.263L316.263 316.263H632Z" stroke="#E0FFD0" stroke-width="2" mask="url(#path-3-inside-2_747_12583)"></path>
                            </svg>
                             <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
$obzor = get_field('obzor_sklada');
if ($obzor):
    $title = $obzor['zagolovok_bloka'];
    $b = $obzor['blok_s_video'];
?>
<section class="overview">
    <?php if ($title): ?>
        <h2 class="overview__title"><?= esc_html($title); ?></h2>
    <?php endif; ?>
    <div class="container">
        <div class="overview__holder">
            <div class="overview__inner">
                <div class="flex items-center">
                    <div class="flex flex-col gap-6">
                        <svg width="24" height="24"><use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#corner-top-left"></use></svg>
                        <svg width="24" height="24"><use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#corner-bottom-left"></use></svg>
                    </div>
                    <?php if (!empty($b['zagolovok'])): ?>
                        <h3 class="about__title overview__inner-title"><?= esc_html($b['zagolovok']); ?></h3>
                    <?php endif; ?>

                    <div class="flex flex-col gap-6">
                        <svg width="24" height="24"><use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#corner-top-right"></use></svg>
                        <svg width="24" height="24"><use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#corner-bottom-right"></use></svg>
                    </div>
                </div>
                <?php if (!empty($b['ssylka_na_video'])): ?>
                    <a href="<?= esc_url($b['ssylka_na_video']); ?>" target="_blank"
                       class="about-left__play overview__play size-15 rounded-full overflow-hidden bg-brand-blue flex items-center justify-center">
                        <svg width="20" height="20">
                            <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#play"></use>
                        </svg>
                    </a>
                <?php endif; ?>
                <?php if (!empty($b['tekst'])): ?>
                    <h3 class="overview__inner-subtitle"><?= esc_html($b['tekst']); ?></h3>
                <?php endif; ?>
                <?php if (!empty($b['tegi'])): ?>
                    <div class="overview__tubs">
                        <?php foreach ($b['tegi'] as $tg): ?>
                            <?php
                                $name = $tg['nazvanie_tega'];
                                $url  = $tg['ssylka_tega'];
                            ?>
                            <?php if ($name): ?>
                                <?php if ($url): ?>
                                    <a href="<?= esc_url($url); ?>">
                                        <div class="tub tub--stroke overview__tub"><?= esc_html($name); ?></div>
                                    </a>
                                <?php else: ?>
                                    <div class="tub tub--stroke overview__tub"><?= esc_html($name); ?></div>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (!empty($b['animaczionnaya_kartinka'])):
                $gif = $b['animaczionnaya_kartinka']['url'];
            ?>
                <div class="overview__gif">
                    <img src="<?= esc_url($gif); ?>" loading="lazy" alt="gif">
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
$p = get_field('pochemu_vybirayut_nas');
$leftClasses   = ['advantages-item--lightblue', 'advantages-item--rad'];
$centerClasses = ['advantages-item--green', 'advantages-item--beige'];
$rightClass    = 'advantages-item--violet';
?>
<section class="advantages">
    <div class="container">
        <div class="section-header">
            <div class="section-header__left flex items-center gap-4">
                <div class="section-header__icon">
                    <img src="<?= get_template_directory_uri(); ?>/images/content/others/emoji-with-glasses.png" alt="">
                </div>
                <h2 class="section-header__title"><?= esc_html($p['zagolovok_bloka']) ?></h2>
            </div>
            <img src="<?= get_template_directory_uri(); ?>/images/gif/drag-mobile.gif" loading="lazy" alt="drag" class="section-header__icon--drag-mobile">
        </div>
        <div class="advantages__inner">
            <!-- ЛЕВАЯ ГРУППА -->
            <div class="advantages-item__wrapper">
                <?php if (!empty($p['dva_levyh_bloka'])): ?>
                    <?php foreach ($p['dva_levyh_bloka'] as $i => $levyh): ?>
                        <div class="advantages-item <?= $leftClasses[$i] ?>">
                            <h5 class="advantages-item__title"><?= esc_html($levyh['zagolovok']) ?></h5>
                            <p class="advantages-item__text"><?= $levyh['tekst'] ?></p>
                            <img class="advantages-item__img" src="<?= esc_url($levyh['kartinka']['url']) ?>" alt="<?= esc_html($levyh['zagolovok']) ?>">
                            <div class="advantages-item__circle" style="background-color: <?= esc_attr($levyh['czvet_fona']) ?>;"></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <!-- ЦЕНТРАЛЬНАЯ ГРУППА -->
            <div class="advantages-item__wrapper">
                <?php if (!empty($p['dva_center_bloka'])): ?>
                    <?php foreach ($p['dva_center_bloka'] as $i => $item): ?>
                        <div class="advantages-item <?= $centerClasses[$i] ?>">
                            <h5 class="advantages-item__title"><?= esc_html($item['zagolovok']) ?></h5>
                            <p class="advantages-item__text"><?= $item['tekst'] ?></p>
                            <img class="advantages-item__img" src="<?= esc_url($item['kartinka']['url']) ?>" alt="<?= esc_html($item['zagolovok']) ?>">
                            <div class="advantages-item__circle" style="background-color: <?= esc_attr($item['czvet_fona']) ?>;"></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <!-- ПРАВЫЙ ОДИНОЧНЫЙ БЛОК -->
            <?php $right = $p['odin_pravyj_blok']; ?>
            <div class="advantages-item <?= $rightClass ?>">
                <h5 class="advantages-item__title"><?= esc_html($right['zagolovok']) ?></h5>
                <p class="advantages-item__text"><?= $right['tekst'] ?></p>
                <?php if (!empty($right['ssylka_na_katalog'])): ?>
                    <a class="button button--fill" href="<?= esc_url($right['ssylka_na_katalog']) ?>">Открыть каталог</a>
                <?php endif; ?>
                <img class="advantages-item__img" src="<?= esc_url($right['kartinka']['url']) ?>" alt="<?= esc_html($right['zagolovok']) ?>">
                <div class="advantages-item__circle" style="background-color: <?= esc_attr($right['czvet_fona']) ?>;"></div>
            </div>
        </div>
        <!-- МОБИЛЬНЫЙ SWIPER -->
        <div class="advantages-swiper swiper">
            <div class="swiper-wrapper">

                <!-- ЛЕВЫЕ ДВА БЛОКА -->
                <div class="swiper-slide">
                    <div class="advantages-item__wrapper">
                        <?php foreach ($p['dva_levyh_bloka'] as $i => $levyh): ?>
                            <div class="advantages-item <?= $leftClasses[$i] ?>">
                                <h5 class="advantages-item__title"><?= esc_html($levyh['zagolovok']) ?></h5>
                                <p class="advantages-item__text"><?= $levyh['tekst'] ?></p>
                                <img class="advantages-item__img" src="<?= esc_url($levyh['kartinka']['url']) ?>" alt="<?= esc_html($levyh['zagolovok']) ?>">
                                <div class="advantages-item__circle" style="background-color: <?= esc_attr($levyh['czvet_fona']) ?>;"></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <!-- ЦЕНТРАЛЬНЫЕ ДВА БЛОКА -->
                <div class="swiper-slide">
                    <div class="advantages-item__wrapper">
                        <?php foreach ($p['dva_center_bloka'] as $i => $item): ?>
                            <div class="advantages-item <?= $centerClasses[$i] ?>">
                                <h5 class="advantages-item__title"><?= esc_html($item['zagolovok']) ?></h5>
                                <p class="advantages-item__text"><?= $item['tekst'] ?></p>
                                <img class="advantages-item__img" src="<?= esc_url($item['kartinka']['url']) ?>" alt="<?= esc_html($item['zagolovok']) ?>">
                                <div class="advantages-item__circle" style="background-color: <?= esc_attr($item['czvet_fona']) ?>;"></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <!-- ПРАВЫЙ ОДИН БЛОК -->
                <div class="swiper-slide">
                    <div class="advantages-item <?= $rightClass ?>">
                        <h5 class="advantages-item__title"><?= esc_html($right['zagolovok']) ?></h5>
                        <p class="advantages-item__text"><?= $right['tekst'] ?></p>
                        <?php if (!empty($right['ssylka_na_katalog'])): ?>
                            <a class="button button--fill" href="<?= esc_url($right['ssylka_na_katalog']) ?>">Открыть каталог</a>
                        <?php endif; ?>
                        <img class="advantages-item__img" src="<?= esc_url($right['kartinka']['url']) ?>" alt="<?= esc_html($right['zagolovok']) ?>">
                        <div class="advantages-item__circle" style="background-color: <?= esc_attr($right['czvet_fona']) ?>;"></div>
                    </div>
                </div>
            </div>
            <div class="advantages-swiper-scrollbar swiper-scrollbar"></div>
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