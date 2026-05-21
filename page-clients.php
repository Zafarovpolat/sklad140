<?php
/**
 * Template Name: Информация для покупателей
 */
get_header();
?>
<link rel="stylesheet" href="<?= get_template_directory_uri(); ?>/css/clients.min.css" />
<script src="<?= get_template_directory_uri(); ?>/js/minified/clients.min.js" defer></script>

<section class="clients">
    <div class="container">
        <h1 class="clients__title">Информация для покупателей</h1>
        <p class="clients__subtitle">Подробная информация об условиях доставки и оплаты, а также, ответы на часто задаваемые вопросы</p>
        <div class="clients-tabs">
            <div class="clients-tab clients-tab--active" data-tab="delivery">Доставка и оплата</div>
            <div class="clients-tab" data-tab="guarantee">Гарантия</div>
            <div class="clients-tab" data-tab="faq">Вопросы и ответы</div>
        </div>
        <div class="clients-content clients-content--delivery clients-content--active" data-content="delivery">
            <div class="clients-block clients-block--delivery-method">
                <div class="clients-block__header">
                    <div class="clients-block__icon">
                        <svg width="16" height="16">
                            <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#delivery"></use>
                        </svg>
                    </div>
                    <h5 class="clients-block__title">Способы доставки</h5>
                </div>
                <p class="clients-block__text">По Москве и области мы доставляем с помощью наших партнеров по логистике:</p>
                <ul class="clients-block__list">
                    <li class="clients-block__list-item">Самовывоз — БЕСПЛАТНО
                        <a href="<?php echo esc_url( home_url( "/contacti/" ) ); ?>" target="_blank" rel="noopener">
                            Рассчитать стоимость
                            <svg style="transform: rotate(-90deg);" width="7" height="12">
                                <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow-blue"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="clients-block__list-item">Собственная служба доставки по Москве
                        <a href="<?php echo esc_url( home_url( "/contacti/" ) ); ?>" target="_blank" rel="noopener">
                            Рассчитать стоимость
                            <svg style="transform: rotate(-90deg);" width="7" height="12">
                                <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow-blue"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="clients-block__list-item">ТК «ПЭК»
                        <a href="https://pecom.ru/" target="_blank" rel="noopener">
                            Рассчитать стоимость
                            <svg style="transform: rotate(-90deg);" width="7" height="12">
                                <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow-blue"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="clients-block__list-item">ТК «Деловые линии»
                        <a href="https://www.dellin.ru/services/calculator/" target="_blank" rel="noopener">
                            Рассчитать стоимость
                            <svg style="transform: rotate(-90deg);" width="7" height="12">
                                <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow-blue"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="clients-block__list-item">ТК «ЖелДорЭкспедиция»
                        <a href="https://www.jde.ru/online/calculator.html" target="_blank" rel="noopener">
                            Рассчитать стоимость
                            <svg style="transform: rotate(-90deg);" width="7" height="12">
                                <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow-blue"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="clients-block__list-item">Сервисы грузоперевозок — <br> Грузовичкофф, Газелькин
                        <a href="https://gruzovichkof.ru/" target="_blank" rel="noopener">
                            Рассчитать стоимость
                            <svg style="transform: rotate(-90deg);" width="7" height="12">
                                <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow-blue"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="clients-block__list-item">Яндекс.Доставка
                        <a href="https://dostavka.yandex.ru/order/" target="_blank" rel="noopener">
                            Рассчитать стоимость
                            <svg style="transform: rotate(-90deg);" width="7" height="12">
                                <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow-blue"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="clients-block__list-item">СДЕК
                        <a href="https://www.cdek.ru/calculate/" target="_blank" rel="noopener">
                            Рассчитать стоимость
                            <svg style="transform: rotate(-90deg);" width="7" height="12">
                                <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow-blue"></use>
                            </svg>
                        </a>
                    </li>
                </ul>
                <p class="clients-block__text">По России и СНГ отправляем ваше оборудование через транспортные компании: ПЭК, Деловые Линии и другие.</p>
                <p class="clients-block__text"><span>Сроки доставки:</span> от 2 часов до 7 дней (в зависимости от региона).</p>
                <p class="clients-block__text"><span>Стоимость доставки</span> уточнять у нашего менеджера.</p>
            </div>
            <div class="clients-block__wrapper">
                <div class="clients-block clients-block--pickup">
                    <div class="clients-block__header">
                        <div class="clients-block__icon">
                            <svg width="16" height="16">
                                <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#pickup"></use>
                            </svg>
                        </div>
                        <h5 class="clients-block__title">Самовывоз*</h5>
                    </div>
                    <p class="clients-block__text"><span>Адрес склада:</span> Московская обл. д.Сухарево, 140Г</p>
                    <p class="clients-block__text"><span>Время работы склада:</span> ежедневно, с 09:00 – 21:00</p>
                    <p class="clients-block__hint">* Сроки хранения товара при самовывозе — 2 дня (не считая день заказа).</p>
                </div>
                <div class="clients-block__map">
                    <img src="<?= get_template_directory_uri(); ?>/images/content/map-pickup.png" alt="карта">
                </div>
            </div>
            <div class="clients-block clients-block--payment">
                <div class="clients-block__header">
                    <div class="clients-block__icon">
                        <svg width="16" height="16">
                            <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#payment-white"></use>
                        </svg>
                    </div>
                    <h5 class="clients-block__title">Оплата</h5>
                </div>
                <p class="clients-block__text">Информация о способах оплаты товаров:</p>
                <ul class="clients-block__list">
                    <li class="clients-block__list-item">Наличный расчет</li>
                    <li class="clients-block__list-item">Безналичный расчет</li>
                </ul>
                <p class="clients-block__hint">* Оплата производится по факту отгрузки, если нужна доставка по Москве и МО. Отгрузка ТК для отправки по России И СНГ производится только после 100% предоплаты.</p>
                <div class="clients-block__payment-methods">
                    <img src="<?= get_template_directory_uri(); ?>/images/content/payment/visa.png" alt="visa">
                    <img src="<?= get_template_directory_uri(); ?>/images/content/payment/mc.png" alt="mc">
                    <img src="<?= get_template_directory_uri(); ?>/images/content/payment/mir.png" alt="mir">
                </div>
                <img class="clients-block__bg" src="<?= get_template_directory_uri(); ?>/images/content/others/clients-block-bg.png" alt="">
            </div>
        </div>
        <div class="clients-content clients-content--guarantee" data-content="guarantee">
            <div class="clients-block__header">
                <div class="clients-block__icon">
                    <svg width="16" height="16">
                        <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#payment-white"></use>
                    </svg>
                </div>
                <h5 class="clients-block__title">Гарантия 7 дней</h5>
            </div>
            <p class="clients-block__text">
                Мы занимаемся реализацией Б/У торгового оборудования уже многие годы. Все позиции проходят предпродажную подготовку. Мы даем гарантию 7 дней на все техническое оборудование.
                Интересующую вас позицию вы можете добавить в корзину и далее оформить заказ. Кроме того, вы можете совершить быструю покупку — нажмите кнопку «купить в 1 клик», оставьте свой номер телефона и наш менеджер свяжется с вами.
            </p>
            <a href="/shop/" class="button button--dark clients-block__btn">Открыть каталог</a>
            <img class="clients-content--guarantee__img" src="<?= get_template_directory_uri(); ?>/images/content/others/macbook-mockup-2.png" alt="">
        </div>
        <div class="clients-content clients-content--faq" data-content="faq">
            <div class="clients-block__header">
                <div class="clients-block__icon">
                    <svg width="16" height="16">
                        <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#question"></use>
                    </svg>
                </div>
                <h5 class="clients-block__title">FAQ</h5>
            </div>
            <div class="clients-questions">
                <div class="clients-questions-item">
                    <div class="clients-questions-item__top">
                        <p class="clients-questions-item__title">Мне нужна доставка. Как это происходит?</p>
                        <div class="clients-questions-item__icon">
                            <svg class="clients-questions-item__icon--plus" width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 8.68623V31.3136M8.68634 19.9999H31.3138" stroke="#031343" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <svg class="clients-questions-item__icon--cross" width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M28 11.9999L12 27.9999M12 11.9999L28 27.9999" stroke="#258FFB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                    <div class="clients-questions-item-content">
                        <p class="clients-questions-item-content__text">
                            Мы доставляем оборудование по Москве и Московской области, России и СНГ.
                            <br>
                            <br>
                            По Москве и области мы доставляем с помощью наших партнеров по логистике, а так же через сервисы грузоперевозок – Грузовичкофф, Газелькин.
                            <br>
                            <br>
                            По России и СНГ отправляем ваше оборудование через транспортные компании: ПЭК, Деловые Линии и другие.
                        </p>
                    </div>
                </div>
                <div class="clients-questions-item">
                    <div class="clients-questions-item__top">
                        <p class="clients-questions-item__title">Какие способы оплаты у вас есть?</p>
                        <div class="clients-questions-item__icon">
                            <svg class="clients-questions-item__icon--plus" width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 8.68623V31.3136M8.68634 19.9999H31.3138" stroke="#031343" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <svg class="clients-questions-item__icon--cross" width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M28 11.9999L12 27.9999M12 11.9999L28 27.9999" stroke="#258FFB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                    <div class="clients-questions-item-content">
                        <p class="clients-questions-item-content__text">
                            Мы предлагаем оплату как за наличный, так и за безналичный расчет по реквизитам.
                            <br>
                            <br>
                            Оплата производится по факту отгрузки, если нужна доставка по Москве и МО. Отгрузка ТК для отправки по России И СНГ производится только после 100% предоплаты.
                        </p>
                    </div>
                </div>
                <div class="clients-questions-item">
                    <div class="clients-questions-item__top">
                        <p class="clients-questions-item__title">Какие гарантии?</p>
                        <div class="clients-questions-item__icon">
                            <svg class="clients-questions-item__icon--plus" width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 8.68623V31.3136M8.68634 19.9999H31.3138" stroke="#031343" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <svg class="clients-questions-item__icon--cross" width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M28 11.9999L12 27.9999M12 11.9999L28 27.9999" stroke="#258FFB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                    <div class="clients-questions-item-content">
                        <p class="clients-questions-item-content__text">
                            Мы занимаемся реализацией Б/У торгового оборудования уже многие годы. Все позиции проходят предпродажную подготовку. 
                            <br>
                            <br>
                            Мы даем гарантию 7 дней на все техническое оборудование.
                        </p>
                    </div>
                </div>
                <div class="clients-questions-item">
                    <div class="clients-questions-item__top">
                        <p class="clients-questions-item__title">Как совершить заказ?</p>
                        <div class="clients-questions-item__icon">
                            <svg class="clients-questions-item__icon--plus" width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 8.68623V31.3136M8.68634 19.9999H31.3138" stroke="#031343" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <svg class="clients-questions-item__icon--cross" width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M28 11.9999L12 27.9999M12 11.9999L28 27.9999" stroke="#258FFB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                    <div class="clients-questions-item-content">
                        <p class="clients-questions-item-content__text">
                            Интересующую вас позицию вы можете добавить в корзину и далее оформить заказ. Кроме того, вы можете совершить быструю покупку — нажмите кнопку «купить в 1 клик», оставьте свой номер телефона и наш менеджер свяжется с вами.
                            <br>
                            <br>
                            По любым вопросам звоните: <a href="tel:+79857524764">8 985 752-47-64</a>
                        </p>
                    </div>
                </div>
            </div>
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


<script>
document.addEventListener('DOMContentLoaded', function () {
    function activateTab(tab) {
        const tabs = document.querySelectorAll('.clients-tab');
        const contents = document.querySelectorAll('.clients-content');

        tabs.forEach(el => {
            el.classList.toggle('clients-tab--active', el.dataset.tab === tab);
        });

        contents.forEach(el => {
            el.classList.toggle('clients-content--active', el.dataset.content === tab);
        });
    }
    if (location.hash) {
        activateTab(location.hash.replace('#', ''));
    }
    document.querySelectorAll('.clients-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            location.hash = tab.dataset.tab;
            activateTab(tab.dataset.tab);
        });
    });
    window.addEventListener('hashchange', function () {
        activateTab(location.hash.replace('#', ''));
    });
});
</script>

<?php get_footer(); ?>