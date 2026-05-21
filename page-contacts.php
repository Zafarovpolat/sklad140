<?php
/**
 * Template Name: Контакты
 */

get_header();

?>
<link rel="stylesheet" href="<?= get_template_directory_uri(); ?>/css/contacts.min.css" />

<section class="contacts">
    <div class="container">
        <h1 class="contacts__title">Контакты</h1>
        <p class="contacts__subtitle">Мы готовы ответить на любые звонки, письма и сообщения.</p>
        <div class="contacts__inner">
            <?php
            $phone  = get_field('phone', 'option');
            $email  = get_field('pochta', 'option');
            function clean_phone_contacts($p) {
                return preg_replace('/\D+/', '', $p);
            }
            ?>
            <?php if ($phone || $email): ?>
            <div class="contacts-item contacts-item--contacts">
                <p class="contacts-item__title">Контакты</p>
                <div class="contacts-item__column">
                    <?php if ($phone): ?>
                        <a class="contacts-item__link" 
                           href="tel:<?= clean_phone_contacts($phone); ?>">
                           <?= esc_html($phone); ?>
                        </a>
                    <?php endif; ?>
                    <?php if ($email): ?>
                        <a class="contacts-item__link" 
                           href="mailto:<?= esc_attr($email); ?>">
                           <?= esc_html($email); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            <div class="contacts-item contacts-item--social">
                <p class="contacts-item__title">Мы в соцсетях</p>
                <?php
                $whatsapp = get_field('ssylka_na_whatsapp', 'option');
                $tg       = get_field('ssylka_na_tg', 'option');
                $vk       = get_field('ssylka_na_vk', 'option');
                ?>
                <div class="contacts-item__column">
                    <?php if ($whatsapp): ?>
                        <a class="contacts-item__link" href="<?= esc_url($whatsapp); ?>" target="_blank" rel="nofollow">
                            <svg width="30" height="30">
                                <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#whatsapp-green"></use>
                            </svg>
                            WhatsApp
                        </a>
                    <?php endif; ?>
                    <?php if ($tg): ?>
                        <a class="contacts-item__link" href="<?= esc_url($tg); ?>" target="_blank" rel="nofollow">
                            <svg width="30" height="30">
                                <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#social--telegram"></use>
                            </svg>
                            Telegram
                        </a>
                    <?php endif; ?>
                    <?php if ($vk): ?>
                        <a class="contacts-item__link" href="<?= esc_url($vk); ?>" target="_blank" rel="nofollow">
                            <svg width="30" height="30">
                                <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#social--vk"></use>
                            </svg>
                            ВКонтакте
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php
            $addr = get_field('address', 'option');
            $graf = get_field('graffik', 'option');
            ?>
            <?php if ($addr || $graf): ?>
            <div class="contacts-item contacts-item--address">
                <p class="contacts-item__title">Адрес склада</p>
                <?php if ($addr): ?>
                    <p class="contacts-item__text">
                        <span><?= esc_html($addr); ?></span>
                    </p>
                <?php endif; ?>
                <?php if ($graf): ?>
                    <p class="contacts-item__text">
                        <?= esc_html($graf); ?>
                    </p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <div class="contacts-item contacts-item--marketplaces">
                <p class="contacts-item__title">Мы на маркетплейсах</p>
                <?php
                $yamarket = get_field('ssylka_na_yandeksmarket', 'option');
                $ozon     = get_field('ssylka_na_ozon', 'option');
                ?>
                <div class="contacts-item__column">
                    <?php if ($yamarket): ?>
                        <a class="contacts-item__link" href="<?= esc_url($yamarket); ?>" target="_blank" rel="nofollow">
                            <img src="<?= get_template_directory_uri(); ?>/images/content/others/yandex-market.png"
                                 alt="Яндекс маркет"
                                 class="contacts-item__link-img">
                            Мы в Яндекс.Маркете
                        </a>
                    <?php endif; ?>
                    <?php if ($ozon): ?>
                        <a class="contacts-item__link" href="<?= esc_url($ozon); ?>" target="_blank" rel="nofollow">
                            <img src="<?= get_template_directory_uri(); ?>/images/content/others/ozon.png"
                                 alt="Ozon"
                                 class="contacts-item__link-img">
                            Мы в Озон
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="contacts-item contacts-item--youtube">
                <div class="contacts-item__img">
                    <svg width="94" height="21">
                        <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#social--youtube-red"></use>
                    </svg>
                </div>
                <p class="contacts-item__text">Обзор на оборудование Б/У и собственное производство</p>
                <?php
                $youtube = get_field('ssylka_na_youtube', 'option');
                ?>
                <?php if ($youtube): ?>
                    <a href="<?= esc_url($youtube); ?>" 
                       class="button button--fill contacts-item__btn" 
                       target="_blank" 
                       rel="nofollow">
                        Подписаться
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<div class="contacts-map">
    <div class="container">
        <?php
        $contmap = get_field('karta_skript_konstruktora_yandeks_contacts', 'option');
        ?>
        <?= $contmap; ?>
    </div>
</div>
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
