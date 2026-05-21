<?php
defined('ABSPATH') || exit;
get_header('shop');
?>
<link rel="stylesheet" href="<?= get_template_directory_uri(); ?>/css/product.min.css" />
<script src="<?= get_template_directory_uri(); ?>/js/pages/product.js" defer></script>

<section class="product-section">
    <div class="container">
        <div class="product__wrapper">
            <div class="product__left">

                <div class="product product--main">

                    <?php wc_get_template('single-product/gallery.php'); ?>

                    <div class="product-specifications">

                        <?php wc_get_template('single-product/title-rating-stock.php'); ?>

                        <?php wc_get_template('single-product/specifications.php'); ?>

                        <?php wc_get_template('single-product/brand-and-description.php'); ?>

                    </div>
                </div>

                <div class="product-info">
                    <div class="product-info__tabs">
                        <h5 class="product-info__tab product-info__tab--active" data-tab="specifications">Характеристики
                        </h5>
                        <h5 class="product-info__tab" data-tab="reviews">Отзывы</h5>
                        <h5 class="product-info__tab" data-tab="delivery">Доставка и оплата</h5>
                    </div>


                    <?php wc_get_template('single-product/tabs/tab-specifications.php'); ?>


                    <?php wc_get_template('single-product/tabs/reviews-tab.php'); ?>


                    <?php wc_get_template('single-product/tabs/delivery-tab.php'); ?>


                </div>
            </div>


            <div class="product__right">


                <?php wc_get_template('single-product/price-block_card.php'); ?>

                <div class="info-block">
                    <div class="info-block__top"> <svg width="18" height="18">
                            <use xlink:href="<?= esc_url(get_template_directory_uri()); ?>/images/sprite.svg#info">
                            </use>
                        </svg>
                        <p class="info-block__title">Уважаемые покупатели</p>
                    </div>
                    <div class="info-block__text">
                        <p>Уважаемые покупатели, данный товар является бывшим в употреблении (Б/У).
                        </p> <br>
                        <p>Товар прошел предпродажную подготовку, в полностью рабочем состоянии и готов к ежедневной
                            эксплуатации. Страница с товаром не является публичной офертой.
                        </p>
                    </div>
                </div>

                <?php if (get_field('bu_tovar', $product->get_id())): ?>
                    <div class="info-block">
                        <div class="info-block__top">
                            <svg width="18" height="18">
                                <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#info"></use>
                            </svg>
                            <p class="info-block__title">Уважаемые покупатели</p>
                        </div>

                        <div class="info-block__text">
                            <p>
                                Данный товар является <span>бывшим в употреблении (Б/У).</span>
                                <br><br>
                                Товар прошел предпродажную подготовку, в полностью
                                <span>рабочем состоянии и готов к ежедневной эксплуатации.</span>
                                <br><br>
                                Страница с товаром не является публичной офертой.
                            </p>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="product__right-block">
                    <div class="product__right-block__top">
                        <svg width="18" height="20">
                            <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#box"></use>
                        </svg>
                        <p class="product__right-block__title">Доставка</p>
                    </div>
                    <ul class="product__right-block__items">
                        <li class="product__right-block__item">Самовывоз - БЕСПЛАТНО</li>
                        <li class="product__right-block__item">Служба доставки</li>
                        <li class="product__right-block__item">Безналичный расчёт</li>
                    </ul>
                    <a href="/clients/" class="product__right-block__link">
                        Подробнее
                        <svg width="10" height="6">
                            <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow-blue"></use>
                        </svg>
                    </a>
                </div>
                <div class="product__right-block">
                    <div class="product__right-block__top">
                        <svg width="20" height="14">
                            <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#payment"></use>
                        </svg>
                        <p class="product__right-block__title">Оплата</p>
                    </div>
                    <ul class="product__right-block__items">
                        <li class="product__right-block__item">Наличными</li>
                        <li class="product__right-block__item">Безналичный расчёт</li>
                    </ul>
                    <a href="/clients/" class="product__right-block__link">
                        Подробнее
                        <svg width="10" height="6">
                            <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow-blue"></use>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Похожие товары -->
<?php wc_get_template('single-product/related-products.php'); ?>

<style>
    .product-item__btns--out-of-stock {
        display: block
    }


</style>


<?php
$wa_nadz = get_field('wa_nadzgolovok', 'option');
$wa_title = get_field('wa_title', 'option');
$wa_pic = get_field('wa_pic', 'option');
$wa_pic_mob = get_field('wa_pic_mob', 'option');
$wa_text = get_field('wa_tekstknopki', 'option');
$wa_link = get_field('wa_link', 'option');
?>

<!-- Проконсультируем -->
<section class="product-consultation consultation">
    <div class="container">
        <div class="consultation__holder bg-brand-blue text-white rounded-[40px] relative p-8 overflow-hidden">
            <div class="consultation__text relative z-10 w-full max-w-112.5">
                <div class="consultation-header flex items-center gap-4">
                    <div class="consultation-header__icon bg-[#4D96FF] rounded-xl px-4 py-2">
                        <img src="<?= get_template_directory_uri(); ?>/images/content/others/chat-emoji.png"
                            alt="Chat emoji" />
                    </div>
                    <?php if ($wa_nadz): ?>
                        <p class="consultation-header__title"><?= esc_html($wa_nadz); ?></p>
                    <?php endif; ?>
                </div>
                <?php if ($wa_title): ?>
                    <p class="consultation__title font-medium text-[32px]/[110%]">
                        <?= esc_html($wa_title); ?>
                    </p>
                <?php endif; ?>
                <?php if ($wa_link && $wa_text): ?>
                    <a href="<?= esc_url($wa_link); ?>" target="_blank"
                        class="button button--dark"><?= esc_html($wa_text); ?></a>
                <?php endif; ?>
            </div>
            <div
                class="consultation__circle size-68 bg-[#3881FF] rounded-full absolute top-0 left-0 -translate-x-1/4 -translate-y-1/2 z-0">
            </div>
            <div
                class="consultation__circle consultation__circle--big size-144 border border-[#4D96FF] rounded-full absolute bottom-0 right-10 translate-y-1/2 z-0 p-8">
                <div class="size-full border border-[#4D96FF] rounded-full p-8">
                    <div class="size-full rounded-full bg-[#3881FF]"></div>
                </div>
            </div>
            <?php if (!empty($wa_pic)): ?>
                <img loading="lazy" class="consultation__img absolute z-10 bottom-0 right-15 h-90"
                    src="<?= esc_url($wa_pic['url']); ?>"
                    srcset="<?= esc_url($wa_pic['url']); ?> 1x, <?= esc_url($wa_pic['url']); ?> 2x"
                    alt="<?= esc_attr($wa_pic['alt']); ?>" />
            <?php endif; ?>
            <?php if (!empty($wa_pic_mob)): ?>
                <img loading="lazy" class="consultation__img--mobile absolute bottom-0 right-0"
                    src="<?= esc_url($wa_pic_mob['url']); ?>"
                    srcset="<?= esc_url($wa_pic_mob['url']); ?> 1x, <?= esc_url($wa_pic_mob['url']); ?> 2x"
                    alt="<?= esc_attr($wa_pic_mob['alt']); ?>" />
            <?php endif; ?>
        </div>
    </div>
</section>

<div class="modal modal--callme-back">
    <div class="modal__title">Перезвонить</div>
    <div class="modal-content">
        <p class="modal-content__text">Оставьте свои контактные данные, мы позвоним для уточнения деталей. Или напишите
            нам в Whats App.</p>
        <form class="modal-content-form" action="#">
            <div class="modal-content-form__row">
                <label class="modal-content-form__label">
                    <p>Имя</p>
                    <input class="input" name="modal-content-form-name" type="text" placeholder="Имя">
                </label>
                <label class="modal-content-form__label">
                    <p>Телефон</p>
                    <input class="input" name="modal-content-form-tel" type="tel" placeholder="+7 (999) 999 99 99">
                </label>
            </div>
            <button class="button button--dark modal-content-form__btn" type="submit">Отправить</button>
            <div class="modal-content-form__btns">
                <a class="button modal-content-form__whatsapp" href="#">
                    Написать
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="32" height="32" rx="8" fill="#01CD3A" />
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M21.6662 10.3238C20.1626 8.82667 18.1616 8 16.0306 8C11.6384 8 8.06887 11.5581 8.06504 15.9276C8.06504 17.3257 8.43233 18.6895 9.12865 19.8895L8 24L12.2238 22.899C13.3869 23.5314 14.6954 23.8629 16.0306 23.8629H16.0344C20.4228 23.8629 23.9962 20.3048 24 15.9352C24 13.8133 23.1698 11.821 21.6662 10.3238ZM16.0344 22.5219C14.8446 22.5219 13.6777 22.2019 12.6638 21.6038L12.4228 21.4629L9.91679 22.1181L10.5863 19.6876L10.4295 19.44C9.76758 18.3924 9.41559 17.1771 9.41559 15.9314C9.41559 12.2971 12.3883 9.34095 16.0383 9.34095C17.8058 9.34095 19.4701 10.0267 20.7174 11.2724C21.9684 12.5181 22.6571 14.1752 22.6533 15.9352C22.6494 19.5657 19.6805 22.5219 16.0344 22.5219ZM19.6652 17.5886C19.4663 17.4895 18.4868 17.0095 18.307 16.9448C18.1234 16.88 17.9933 16.8457 17.8594 17.0438C17.7255 17.2419 17.3467 17.6876 17.2281 17.821C17.1133 17.9543 16.9947 17.9695 16.7958 17.8705C16.5968 17.7714 15.9541 17.5619 15.1966 16.8876C14.6035 16.3619 14.2056 15.7143 14.0909 15.5162C13.9761 15.3181 14.0794 15.2114 14.1789 15.1124C14.2669 15.0248 14.3778 14.88 14.4773 14.7657C14.5768 14.6514 14.6112 14.5676 14.6762 14.4343C14.7413 14.301 14.7107 14.1867 14.6609 14.0876C14.6112 13.9886 14.2133 13.0133 14.0488 12.6171C13.8881 12.2324 13.7236 12.2819 13.6011 12.2781C13.4864 12.2705 13.3525 12.2705 13.2186 12.2705C13.0846 12.2705 12.8704 12.32 12.6868 12.5181C12.5031 12.7162 11.9904 13.1962 11.9904 14.1714C11.9904 15.1467 12.7021 16.0876 12.8015 16.221C12.901 16.3543 14.2056 18.3543 16.2028 19.2114C16.6772 19.4171 17.0483 19.539 17.3391 19.6305C17.8173 19.7829 18.2496 19.76 18.594 19.7105C18.9766 19.6533 19.7724 19.2305 19.9369 18.7695C20.1014 18.3086 20.1014 17.9086 20.0516 17.8286C19.9943 17.7371 19.8642 17.6876 19.6652 17.5886Z"
                            fill="white" />
                    </svg>
                </a>
                <a class="button modal-content-form__telegram" href="#">
                    Написать
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="32" height="32" rx="8" fill="#258FFB" />
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M9.10538 15.0498C13.3943 13.0575 16.2612 11.7698 17.7062 11.1381C21.7887 9.34015 22.6373 9.0243 23.1878 9C23.3024 9 23.5777 9.0243 23.7611 9.17007C23.8988 9.29155 23.9446 9.46163 23.9676 9.58311C23.9905 9.70459 24.0134 9.97185 23.9905 10.1905C23.7611 12.6444 22.8208 18.6456 22.3162 21.3911C22.1098 22.5573 21.697 22.946 21.307 22.9946C20.4584 23.0675 19.7933 22.3872 18.9676 21.8284C17.6832 20.9294 16.9493 20.3706 15.6879 19.496C14.2429 18.4755 15.1833 17.9167 16.009 17.0177C16.2154 16.7748 19.9997 13.1546 20.0685 12.8145C20.0685 12.7659 20.0915 12.6201 19.9997 12.5472C19.908 12.4744 19.7933 12.4987 19.7016 12.5229C19.564 12.5472 17.4998 14.005 13.4861 16.872C12.8897 17.3093 12.3622 17.5037 11.8806 17.5037C11.3531 17.5037 10.3439 17.1878 9.58702 16.9206C8.6696 16.6047 7.93567 16.4346 8.00447 15.9001C8.05034 15.6329 8.41731 15.3413 9.10538 15.0498Z"
                            fill="white" />
                    </svg>
                </a>
            </div>
            <div class="modal-content-form__privacy-policy">
                <input class="input input-checkbox__input" type="checkbox">
                <p>
                    Я ознакомился и согласен с <a href="/privacy-policy/" target="_blank" rel="noopener">политикой конфиденциальности</a> в отношении хранения и
                    обработки персональных данных
                </p>
            </div>
        </form>
    </div>
</div>

<?php
get_footer('shop');
?>