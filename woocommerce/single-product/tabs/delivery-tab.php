<?php
defined('ABSPATH') || exit;
?>

<div class="product-info-content product-delivery" data-content="delivery">

    <h4 class="product-info-content__title">Доставка и оплата</h4>

    <div class="product-delivery__inner">

        <!-- ЛЕВАЯ КОЛОНКА -->
        <div class="product-delivery__column">

            <!-- Самовывоз -->
            <div class="product-delivery-item">
                <div class="product-delivery-item__header">
                    <div class="product-delivery-item__header-icon">
                        <svg width="24" height="24">
                            <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#truck"></use>
                        </svg>
                    </div>
                    <h5 class="product-delivery-item__header-title">Самовывоз – БЕСПЛАТНО</h5>
                </div>

                <ul class="product-delivery-item__list">
                    <li class="product__right-block__item product-delivery-item__list-item">
                        МО, Мытищенский район, д. Сухарево, д.140Г
                    </li>
                </ul>
            </div>

            <!-- Оплата -->
            <div class="product-delivery-item">
                <div class="product-delivery-item__header">
                    <div class="product-delivery-item__header-icon">
                        <svg width="24" height="24">
                            <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#card"></use>
                        </svg>
                    </div>
                    <h5 class="product-delivery-item__header-title">Оплата*</h5>
                </div>

                <ul class="product-delivery-item__list">
                    <li class="product__right-block__item product-delivery-item__list-item">Наличный расчет</li>
                    <li class="product__right-block__item product-delivery-item__list-item">Безналичный расчет</li>
                </ul>

                <div class="product-delivery-item__payment">
                    <img class="product-delivery-item__payment-img"
                        src="<?= get_template_directory_uri(); ?>/images/content/payment/visa.png" alt="visa">
                    <img class="product-delivery-item__payment-img"
                        src="<?= get_template_directory_uri(); ?>/images/content/payment/mc.png" alt="mc">
                    <img class="product-delivery-item__payment-img"
                        src="<?= get_template_directory_uri(); ?>/images/content/payment/mir.png" alt="mir">
                </div>
            </div>

        </div>

        <!-- ПРАВАЯ КОЛОНКА -->
        <div class="product-delivery__column">

            <div class="product-delivery-item product-delivery-item--delivery">
                <div class="product-delivery-item__header">
                    <div class="product-delivery-item__header-icon">
                        <svg width="24" height="24">
                            <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#box"></use>
                        </svg>
                    </div>
                    <h5 class="product-delivery-item__header-title">Доставка</h5>
                </div>

                <ul class="product-delivery-item__list">

                    <li class="product__right-block__item product-delivery-item__list-item">
                        <p>Собственная служба доставки</p>
                        <a href="<?php echo esc_url( home_url( "/contacti/" ) ); ?>" target="_blank" rel="noopener" class="product__right-block__link">
                            Рассчитать стоимость
                            <svg width="10" height="6">
                                <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow-blue">
                                </use>
                            </svg>
                        </a>
                    </li>

                    <li class="product__right-block__item product-delivery-item__list-item">
                        <p>ТК «ПЭК»</p>
                        <a href="https://pecom.ru/" target="_blank" rel="noopener" class="product__right-block__link">
                            Рассчитать стоимость
                            <svg width="10" height="6">
                                <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow-blue">
                                </use>
                            </svg>
                        </a>
                    </li>

                    <li class="product__right-block__item product-delivery-item__list-item">
                        <p>Яндекс.Доставка</p>
                        <a href="https://dostavka.yandex.ru/order/" target="_blank" rel="noopener" class="product__right-block__link">
                            Рассчитать стоимость
                            <svg width="10" height="6">
                                <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow-blue">
                                </use>
                            </svg>
                        </a>
                    </li>

                    <li class="product__right-block__item product-delivery-item__list-item">
                        <p>ТК «Деловые линии»</p>
                        <a href="https://www.dellin.ru/services/calculator/" target="_blank" rel="noopener" class="product__right-block__link">
                            Рассчитать стоимость
                            <svg width="10" height="6">
                                <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow-blue">
                                </use>
                            </svg>
                        </a>
                    </li>

                    <li class="product__right-block__item product-delivery-item__list-item">
                        <p>СДЕК</p>
                        <a href="https://www.cdek.ru/calculate/" target="_blank" rel="noopener" class="product__right-block__link">
                            Рассчитать стоимость
                            <svg width="10" height="6">
                                <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow-blue">
                                </use>
                            </svg>
                        </a>
                    </li>

                </ul>
            </div>

            <p class="product-delivery-item__text">
                *Оплата производится по факту отгрузки, если нужна доставка по Москве и МО.
                Отгрузка ТК для отправки по России и СНГ производится только после 100% предоплаты.
            </p>

        </div>

    </div>

</div>