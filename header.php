<!DOCTYPE html>
<html <?php language_attributes() ?>>

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="apple-touch-icon" sizes="180x180"
        href="<?= get_template_directory_uri(); ?>/assets/apple-touch-icon.png" />
    <link rel="icon" type="image/png" sizes="32x32"
        href="<?= get_template_directory_uri(); ?>/assets/favicon-32x32.png" />
    <link rel="icon" type="image/png" sizes="16x16"
        href="<?= get_template_directory_uri(); ?>/assets/favicon-16x16.png" />
    <link rel="manifest" href="<?= get_template_directory_uri(); ?>/assets/site.webmanifest" />
    <? // Styles ?>
    <link rel="stylesheet" href="<?= get_template_directory_uri(); ?>/css/index.min.css" />
    <? // Styles ?>


    <? // Scripts ?>
    <script src="/wp-content/plugins/elementor/assets/lib/swiper/v8/swiper.min.js" defer></script>
    <?php
    $main_js_rel = '/js/minified/main.min.js';
    $main_js_abs = get_template_directory() . $main_js_rel;
    $main_js_ver = file_exists($main_js_abs) ? filemtime($main_js_abs) : '1';
    ?>
    <script src="<?= get_template_directory_uri() . $main_js_rel . '?ver=' . $main_js_ver; ?>" defer></script>
    <?php if (is_front_page()):
        $idx_rel = '/js/minified/index.min.js';
        $idx_abs = get_template_directory() . $idx_rel;
        $idx_ver = file_exists($idx_abs) ? filemtime($idx_abs) : '1';
        echo '<script src="' . get_template_directory_uri() . $idx_rel . '?ver=' . $idx_ver . '" defer></script>';
    endif; ?>
    <? // Scripts ?>

    <?php wp_head(); ?>
    <style>
        /* Search Dropdown */
        .header-search__input {
            position: relative;
        }

        .search-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            margin-top: 8px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            z-index: 9999;
            max-height: 400px;
            overflow-y: auto;
        }

        .search-dropdown__loading,
        .search-dropdown__empty {
            display: none;
            padding: 20px;
            text-align: center;
            color: #7C7C7C;
            font-size: 14px;
        }

        .search-dropdown__results {
            display: none;
        }

        .search-dropdown .search-modal-content-product {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            text-decoration: none;
            color: inherit;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.2s;
        }

        .search-dropdown .search-modal-content-product:hover {
            background: #f8f9fb;
        }

        .search-dropdown .search-modal-content-product:last-child {
            border-bottom: none;
        }

        .search-dropdown .search-modal-content-product__img {
            width: 50px;
            height: 50px;
            flex-shrink: 0;
            border-radius: 8px;
            overflow: hidden;
            background: #f5f5f5;
        }

        .search-dropdown .search-modal-content-product__img img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .search-dropdown .search-modal-content-product__info {
            flex: 1;
            min-width: 0;
        }

        .search-dropdown .cart-product__info-price__wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
        }

        .search-dropdown .cart-product__info-price {
            font-weight: 600;
            font-size: 16px;
            color: #031343;
        }

        .search-dropdown .cart-product__info-old-price {
            font-size: 12px;
            color: #999;
            text-decoration: line-through;
        }

        .search-dropdown .search-modal-content-product__title {
            line-height: 1.3;
            font-size: 15px;
            color: #031343 overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        /* Ховер для иконок каталога - цвет #258ffb */
        .header-categories a img {
            transition: filter 0.2s ease;
        }

        .header-categories a:hover img {
            filter: invert(42%) sepia(86%) saturate(1935%) hue-rotate(196deg) brightness(101%) contrast(97%);
        }

        /* Скрываем модалку при использовании dropdown */
        /* body.use-dropdown .search-modal {
/*     display: none !important;
/* } */
    </style>
</head>
<style>
    .border-red-500 {
        border-color: #f53535 !important;
    }

    .modal-content-form input.border-red-500:focus {
        border-color: #f53535 !important;
        outline: none;
    }
</style>

<body>

    <header id="header" class="pt-3">
        <div class="container">
            <!-- Top -->
            <div class="header-top flex items-center justify-between gap-5 border-b border-brand-blue-200 pb-3">

                <?php
                $menu_items = wp_get_nav_menu_items(10880); // ID меню выкупа и аренд.
                ?>
                <div class="w-full max-w-96.5 min-h-10.5 flex items-center gap-3 bg-white rounded-lg p-3">
                    <?php if (!empty($menu_items)): ?>
                        <?php
                        $icons = ['wallet', 'clock'];
                        $text_colors = ['text-brand-blue', 'text-brand-red'];
                        ?>
                        <?php foreach ($menu_items as $i => $item): ?>
                            <?php
                            $icon = $icons[$i] ?? $icons[0];
                            $text_color = $text_colors[$i] ?? $text_colors[0];
                            ?>
                            <a href="<?= esc_url($item->url) ?>"
                                class="hover-opacity-75 min-h-4.5 flex items-center gap-1.5 <?= $i === 1 ? 'border-l border-brand-blue-200 pl-3' : '' ?>">
                                <svg width="16" height="16">
                                    <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#<?= $icon ?>"></use>
                                </svg>
                                <span class="font-medium text-sm <?= $text_color ?> whitespace-nowrap">
                                    <?= esc_html($item->title) ?>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <?php
                $menu_items = wp_get_nav_menu_items(36);
                ?>
                <nav class="header-nav text-main-15 flex items-center gap-7.5">
                    <?php if (!empty($menu_items)): ?>
                        <?php foreach ($menu_items as $index => $item): ?>

                            <a href="<?= esc_url($item->url); ?>" class="hover-opacity-75">
                                <?= esc_html($item->title); ?>
                            </a>

                            <?php if ($index !== count($menu_items) - 1): ?>
                                <span class="bg-brand-blue-200 inline-block w-[1px] h-4.5"></span>
                            <?php endif; ?>

                        <?php endforeach; ?>
                    <?php endif; ?>
                </nav>
            </div>

            <!-- Center -->
            <div class="header-center flex items-center justify-between gap-3 my-3">
                <!-- Logo -->
                <a href="/" class="header-logo inline-block shrink-0">
                    <svg width="195" height="42">
                        <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#logo"></use>
                    </svg>
                </a>
                <div class="header-mobile-links">
                    <a href="#" class="header-mobile-links__link header-mobile-links__link--whatsapp">
                        <svg width="16" height="16">
                            <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#whats-app--white">
                            </use>
                        </svg>
                    </a>
                    <a href="#" class="header-mobile-links__link header-mobile-links__link--telegram">
                        <svg width="16" height="14">
                            <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#telegram--white">
                            </use>
                        </svg>
                    </a>
                    <div class="header-mobile-links__link header-mobile-links__link--menu">
                        <svg width="14" height="10">
                            <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#menu"></use>
                        </svg>
                    </div>
                </div>


                <!-- Каталог -->
                <div class="header-search">
                    <a href="/shop/"
                        class="header-search__catalog-btn bg-brand-blue text-white flex items-center gap-2.5 rounded-xl h-12 px-6 py-3.75 hover-opacity-75">
                        <svg width="18" height="18">
                            <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#catalog"></use>
                        </svg>
                        <span class="font-medium">Каталог</span>
                    </a>

                    <?php
                    $menu_id = 10306;
                    $menu_items = wp_get_nav_menu_items($menu_id);

                    $items_by_parent = [];

                    // группируем пункты по parent_id
                    foreach ($menu_items as $item) {
                        $items_by_parent[$item->menu_item_parent][] = $item;
                    }
                    ?>

                    <ul class="header-search__catalog-links">
                        <?php foreach ($items_by_parent[0] ?? [] as $parent): ?>
                            <li class="header-search__catalog-link">

                                <a href="<?= esc_url($parent->url); ?>" class="catalog-parent-link">
                                    <span class="catalog-parent-text"><?= esc_html($parent->title); ?></span>

                                    <?php if (!empty($items_by_parent[$parent->ID])): ?>
                                        <svg class="catalog-arrow" width="10" height="10">
                                            <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#arrow">
                                            </use>
                                        </svg>
                                    <?php endif; ?>
                                </a>

                                <?php if (!empty($items_by_parent[$parent->ID])): ?>
                                    <ul class="header-search__catalog-sublist">
                                        <?php foreach ($items_by_parent[$parent->ID] as $child): ?>
                                            <li class="header-search__catalog-subitem">
                                                <a href="<?= esc_url($child->url); ?>">
                                                    <?= esc_html($child->title); ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>

                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <!-- Что будем искать? -->
                    <div class="header-search__input relative w-full max-w-131">
                        <input type="text" name="search" id="search" placeholder="Что будем искать?" autocomplete="off"
                            class="bg-white placeholder:text-brand-gray border border-transparent rounded-xl text-sm min-h-12 w-full focus:border-brand-blue outline-none px-4 py-3" />
                        <div class="absolute top-1/2 -translate-y-1/2 right-4 pointer-events-none">
                            <svg width="20" height="20">
                                <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#search"></use>
                            </svg>
                        </div>

                    </div>


                </div>
                <!-- Перезвонить мне -->
                <div class="callme-back">
                    <a href="tel:+78002018004" class="font-medium hover-opacity-75 flex items-center gap-2 mb-0.25">
                        <img class="shrink-0 size-5.5" src="<?= get_template_directory_uri(); ?>/images/gif/phone.gif"
                            alt="phone gif" loading="lazy" />
                        <p class="callme-back__number text-lg whitespace-nowrap">
                            8 <span class="text-brand-blue">(</span>800<span class="text-brand-blue">)</span>
                            201-80-04
                        </p>
                    </a>
                    <div class="flex items-center gap-4">
                        <a href="#"
                            class="callme-back__text underline uppercase hover-opacity-75 text-xs text-brand-blue-300 block min-h-3 pr-4 border-r border-brand-blue-200">Перезвонить
                            мне
                        </a>
                        <div class="callme-back__social flex items-center gap-1">
                            <a href="https://api.whatsapp.com/send/?phone=79857524764&text=%D0%AF+%D0%B8%D0%B7+sklad140.ru+%D0%9F%D0%B8%D1%88%D1%83+%D0%BF%D0%BE+%D0%BF%D0%BE%D0%B2%D0%BE%D0%B4%D1%83&type=phone_number&app_absent=0"
                                class="shrink-0 hover-opacity-75">
                                <svg width="18" height="18">
                                    <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#whats-app">
                                    </use>
                                </svg>
                            </a>
                            <a href="https://t.me/Sklad140" class="shrink-0 hover-opacity-75">
                                <svg width="18" height="18">
                                    <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#telegram">
                                    </use>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                <!-- Links -->
                <div class="header-links text-xs flex items-center gap-2">
                    <!-- 1 -->
                    <a href="/compare/" id="headerCompareLink"
                        class="flex flex-col items-center gap-2 relative hover-opacity-75">
                        <div
                            class="absolute top-0 right-2 z-10 size-4.5 flex items-center justify-center bg-brand-red text-white rounded-full compare-count hidden">
                            <span class="text-xs font-medium leading-0">0</span>
                        </div>
                        <svg width="24" height="24">
                            <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#compare"></use>
                        </svg>
                        <span class="font-inter text-brand-gray text-xs">Сравнить</span>
                    </a>
                    <!-- 2 -->
                    <?php
                    $wishlist_count = 0;
                    if (function_exists('YITH_WCWL')) {
                        $wishlist_count = (int) YITH_WCWL()->count_products();
                    }
                    ?>

                    <a href="/wishlist/" id="headerWishlistLink"
                        class="flex flex-col items-center gap-2 relative hover-opacity-75">
                        <div
                            class="absolute top-0 right-2 z-10 size-4.5 flex items-center justify-center bg-brand-red text-white rounded-full wishlist-count">
                            <span class="text-xs font-medium leading-0">
                                <?= esc_html($wishlist_count); ?>
                            </span>
                        </div>
                        <svg width="24" height="24">
                            <use
                                xlink:href="<?= esc_url(get_template_directory_uri()); ?>/images/sprite.svg#favourites">
                            </use>
                        </svg>
                        <span class="font-inter text-brand-gray text-xs">Избранное</span>
                    </a>
                    <!-- 3 - Корзина -->
                    <a href="<?php echo wc_get_cart_url(); ?>"
                        class="header-cart flex flex-col items-center gap-2 relative hover-opacity-75">
                        <div
                            class="header-cart__count absolute top-0 right-0 z-10 size-4.5 flex items-center justify-center bg-brand-red text-white rounded-full">
                            <span class="text-xs font-medium leading-0 js-cart-count">
                                <?php echo WC()->cart->get_cart_contents_count(); ?>
                            </span>
                        </div>
                        <svg width="24" height="24">
                            <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#cart"></use>
                        </svg>
                        <span class="font-inter text-brand-gray text-xs">Корзина</span>
                    </a>
                </div>
            </div>


            <script>
                // функция для шапки (СРАВНЕНИЕ)
                document.addEventListener('DOMContentLoaded', () => {
                    const compareLink = document.getElementById('headerCompareLink');
                    const countBox = compareLink?.querySelector('.compare-count span');
                    const badge = compareLink?.querySelector('.compare-count');
                    const comparePage = '/compare/';
                    function getCompareIds() {
                        const cookieStr = document.cookie.split('; ').find(r => r.startsWith('yith_woocompare_list='));
                        if (!cookieStr) return [];
                        let value = decodeURIComponent(cookieStr.split('=')[1] || '');
                        try {
                            if (value.startsWith('[') && value.endsWith(']')) {
                                const parsed = JSON.parse(value);
                                return Array.isArray(parsed) ? parsed.map(String) : [];
                            }
                        } catch (e) {
                            return value.split(/[%2C,|]/).filter(v => v && /^\d+$/.test(v));
                        }
                        return [];
                    }
                    function updateCompareCount() {
                        const ids = getCompareIds();
                        const count = ids.length;
                        if (countBox) countBox.textContent = count;
                        if (badge) badge.classList.remove('hidden');
                    }
                    updateCompareCount();
                    jQuery(document).on('yith_woocompare_added yith_woocompare_removed', updateCompareCount);
                    setInterval(updateCompareCount, 300);
                    compareLink?.addEventListener('click', e => {
                        e.preventDefault();
                        window.location.href = comparePage;
                    });
                });
            </script>

            <script>
                // функция для шапки (ИЗБРАННОЕ)
                document.addEventListener('DOMContentLoaded', function () {
                    const headerLink = document.getElementById('headerWishlistLink');
                    if (!headerLink) return;
                    const badgeSpan = headerLink.querySelector('.wishlist-count span');
                    const wishlistUrl = '/wishlist/';
                    const ajaxUrl = '<?= esc_url(admin_url('admin-ajax.php')); ?>';
                    function setCount(n) {
                        n = parseInt(n, 10);
                        if (isNaN(n) || n < 0) n = 0;
                        badgeSpan.textContent = n;
                    }
                    function reloadCount() {
                        fetch(ajaxUrl + '?action=get_wishlist_count', {
                            credentials: 'same-origin'
                        })
                            .then(function (res) { return res.json(); })
                            .then(function (data) {
                                if (data && data.success && data.data && typeof data.data.count !== 'undefined') {
                                    setCount(data.data.count);
                                }
                            })
                            .catch(function (err) {
                                console && console.warn && console.warn('Wishlist count ajax error', err);
                            });
                    }
                    reloadCount();
                    document.body.addEventListener('click', function (e) {
                        const target = e.target.closest('a');
                        if (!target) return;
                        const href = target.getAttribute('href') || '';
                        const cls = target.className || '';
                        const isWishlistAction =
                            cls.includes('add_to_wishlist') ||
                            cls.includes('yith-wcwl') ||
                            href.indexOf('add_to_wishlist=') !== -1 ||
                            href.indexOf('remove_from_wishlist=') !== -1;
                        if (!isWishlistAction) return;
                        setTimeout(reloadCount, 300);
                    });
                    headerLink.addEventListener('click', function (e) {
                        e.preventDefault();
                        window.location.href = wishlistUrl;
                    });
                });
            </script>

            <!-- Bottom -->
            <?php
            $categories = get_field('blok_kategorij_na_glavnoj', 'option');
            if (!empty($categories['katalog_bloki'])):
                $blocks = array_slice($categories['katalog_bloki'], 0, 12);
                ?>
                <div class="header-categories grid grid-cols-12 gap-1 mb-4">
                    <?php foreach ($blocks as $item):
                        $icon = $item['ikonka'];
                        $title = $item['zagolovok'];
                        $link = $item['ssylka'];
                        $alt = esc_attr($title);
                        $title_attr = esc_attr($title);
                        ?>
                        <a href="<?php echo esc_url($link ?: '#'); ?>"
                            class="bg-white rounded-xl hover-border-blue text-center flex flex-col items-center gap-1 p-2">
                            <?php if (!empty($icon)): ?>
                                <img src="<?php echo esc_url($icon['url']); ?>" alt="<?php echo $alt; ?>"
                                    title="<?php echo $title_attr; ?>" width="33" height="32" class="object-contain" />
                            <?php endif; ?>
                            <?php if ($title): ?>
                                <span class="font-medium text-xs"><?php echo esc_html($title); ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {

                const isMobile = window.innerWidth <= 768;

                if (!isMobile) {
                    return;
                }

                // ======== КНОПКА "КАТАЛОГ" с двойным кликом ========
                const catalogBtn = document.querySelector('.header-search__catalog-btn');
                const catalogMenu = document.querySelector('.header-search__catalog-links');

                if (catalogBtn && catalogMenu) {

                    // Скрываем меню по умолчанию
                    catalogMenu.style.display = 'none';

                    let catalogClickTimer = null;
                    const catalogClickTimeout = 400; // 400ms между кликами

                    catalogBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();


                        // Если уже был клик недавно - это ДВОЙНОЙ клик
                        if (catalogClickTimer !== null) {
                            clearTimeout(catalogClickTimer);
                            catalogClickTimer = null;
                            window.location.href = '/shop/';
                            return;
                        }

                        // ПЕРВЫЙ клик - открываем/закрываем меню

                        if (catalogMenu.style.display === 'none') {
                            catalogMenu.style.display = 'block';
                            catalogBtn.classList.add('catalog-btn--active');
                        } else {
                            catalogMenu.style.display = 'none';
                            catalogBtn.classList.remove('catalog-btn--active');
                        }

                        // Устанавливаем таймер
                        catalogClickTimer = setTimeout(function () {
                            catalogClickTimer = null;
                        }, catalogClickTimeout);
                    });
                }

                // ======== КАТЕГОРИИ (обычный клик) ========
                const catalogLinks = document.querySelectorAll('.header-search__catalog-link');

                catalogLinks.forEach(function (link, index) {
                    const anchor = link.querySelector('a.catalog-parent-link');
                    const sublist = link.querySelector('.header-search__catalog-sublist');

                    if (!sublist) {
                        return; // Обычная ссылка без подкатегорий - работает как есть
                    }


                    // Блокируем переход по ссылке для категорий с подменю
                    anchor.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();


                        // Закрываем все другие подменю
                        document.querySelectorAll('.header-search__catalog-link').forEach(function (otherLink) {
                            if (otherLink !== link) {
                                otherLink.classList.remove('catalog-link--open');
                            }
                        });

                        // Переключаем текущее подменю
                        const isOpen = link.classList.toggle('catalog-link--open');
                    });
                });

            });
        </script>

    </header>


    <div class="search-modal">
        <div class="container">
            <div class="search-modal-header">
                <div class="search-modal-header__input">
                    <input class="input" type="text" name="search" id="modal-search-input"
                        placeholder="Что будем искать?" />
                    <button type="button" id="modal-search-submit" class="search-modal-header__submit" aria-label="Искать">
                        <svg width="19" height="18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M17.143 17l-3.823-3.822m2.045-5.067a7.111 7.111 0 11-14.222 0 7.111 7.111 0 0114.222 0z"
                                stroke="#258FFB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
                <button class="search-modal__cancel">Отмена</button>
            </div>


            <!--<div class="search-modal-categories__wrapper">
                <div class="search-modal-categories">
                    <button class="button button--small">
                        <svg width="6" height="6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 6L6 18M6 6L18 18" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Пароконвектомат
                    </button>
                    <button class="button button--small">
                        <svg width="6" height="6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 6L6 18M6 6L18 18" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Пароконвектомат
                    </button>
                    <button class="button button--small">
                        <svg width="6" height="6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 6L6 18M6 6L18 18" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Пароконвектомат
                    </button>
                    <button class="button button--small">
                        <svg width="6" height="6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 6L6 18M6 6L18 18" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Пароконвектомат
                    </button>
                    <button class="button button--small">
                        <svg width="6" height="6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 6L6 18M6 6L18 18" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Пароконвектомат
                    </button>
                    <button class="button button--small">
                        <svg width="6" height="6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 6L6 18M6 6L18 18" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Пароконвектомат
                    </button>
                </div>
                <div class="swiper-scrollbar"></div>
            </div>-->


            <div class="search-modal__inner">
                <div class="search-modal__often-search">
                    <h5 class="search-modal__title">Часто ищут:</h5>
                    <div class="search-modal__tubs-wrapper">
                        <div class="search-modal__tubs">
                            <div class="tub search-modal__tub search-modal__tub--visible" data-visible-by="default">
                                Морозильная камера</div>
                            <div class="tub search-modal__tub search-modal__tub--visible" data-visible-by="default">
                                Торговое оборудование</div>
                            <div class="tub search-modal__tub search-modal__tub--visible" data-visible-by="default">
                                Мебель для бизнеса</div>
                            <div class="tub search-modal__tub search-modal__tub--visible" data-visible-by="default">
                                Холодильный шкаф Б/У</div>
                            <div class="tub search-modal__tub" data-visible-by="click">Торговое оборудование</div>
                            <div class="tub search-modal__tub" data-visible-by="click">Морозильная камера</div>
                            <div class="tub search-modal__tub" data-visible-by="click">Мебель для бизнеса</div>
                            <div class="tub search-modal__tub" data-visible-by="click">Морозильная камера</div>
                            <div class="tub search-modal__tub" data-visible-by="click">Холодильный шкаф Б/У</div>
                            <div class="tub search-modal__tub" data-visible-by="click">Мебель для бизнеса</div>
                            <div class="tub search-modal__tub" data-visible-by="click">Торговое оборудование</div>
                            <div class="tub search-modal__tub" data-visible-by="click">Холодильный шкаф Б/У</div>
                        </div>
                        <p class="search-modal__show">Показать ещё <span>8</span></p>
                    </div>
                </div>
                <div class="search-modal-content search-modal-content--history search-modal-content--visible">
                    <div class="search-modal-content__header">
                        <h5 class="search-modal__title">История запросов</h5>
                        <div class="search-modal__clear">Очистить историю</div>
                    </div>
                    <div class="search-modal__recent-wrapper">
                        <div class="search-modal__recent-items">
                            <div class="search-modal__recent">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M2 8C2 9.18669 2.35189 10.3467 3.01118 11.3334C3.67047 12.3201 4.60754 13.0892 5.7039 13.5433C6.80026 13.9974 8.00666 14.1162 9.17054 13.8847C10.3344 13.6532 11.4035 13.0818 12.2426 12.2426C13.0818 11.4035 13.6532 10.3344 13.8847 9.17054C14.1162 8.00666 13.9974 6.80026 13.5433 5.7039C13.0892 4.60754 12.3201 3.67047 11.3334 3.01118C10.3467 2.35189 9.18669 2 8 2C6.32263 2.00631 4.71265 2.66082 3.50667 3.82667L2 5.33333M2 5.33333V2M2 5.33333H5.33333M8 4.66667V8L10.6667 9.33333"
                                        stroke="#7C7C7C" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Пароконвектомат
                            </div>
                            <div class="search-modal__recent">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M2 8C2 9.18669 2.35189 10.3467 3.01118 11.3334C3.67047 12.3201 4.60754 13.0892 5.7039 13.5433C6.80026 13.9974 8.00666 14.1162 9.17054 13.8847C10.3344 13.6532 11.4035 13.0818 12.2426 12.2426C13.0818 11.4035 13.6532 10.3344 13.8847 9.17054C14.1162 8.00666 13.9974 6.80026 13.5433 5.7039C13.0892 4.60754 12.3201 3.67047 11.3334 3.01118C10.3467 2.35189 9.18669 2 8 2C6.32263 2.00631 4.71265 2.66082 3.50667 3.82667L2 5.33333M2 5.33333V2M2 5.33333H5.33333M8 4.66667V8L10.6667 9.33333"
                                        stroke="#7C7C7C" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Кондиционер
                            </div>
                        </div>
                        <svg class="search-modal__recent-cross" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 6L6 18M6 6L18 18" stroke="#B6C1DD" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="search-modal-content__products">
                        <?php
                        $random_products = wc_get_products(array(
                            'status' => 'publish',
                            'limit' => 8,
                            'orderby' => 'rand',
                        ));

                        if ($random_products):
                            foreach ($random_products as $rp):
                                $rp_image = wp_get_attachment_image_url($rp->get_image_id(), 'thumbnail');
                                if (!$rp_image) {
                                    $rp_image = wc_placeholder_img_src('thumbnail');
                                }
                                $rp_price = $rp->get_price();
                                $rp_reg_price = $rp->get_regular_price();
                                $rp_sale = $rp->get_sale_price();
                                $rp_title = $rp->get_name();
                                $rp_link = get_permalink($rp->get_id());
                                ?>
                                <a class="search-modal-content-product" href="<?= esc_url($rp_link); ?>">
                                    <div class="search-modal-content-product__img">
                                        <img src="<?= esc_url($rp_image); ?>" alt="<?= esc_attr($rp_title); ?>">
                                    </div>
                                    <div class="search-modal-content-product__info">
                                        <div class="cart-product__info-price__wrapper">
                                            <p class="cart-product__info-price">
                                                <?= number_format((float) $rp_price, 0, '', ' '); ?> ₽
                                            </p>
                                            <?php if ($rp_sale && $rp_reg_price && $rp_reg_price > $rp_sale): ?>
                                                <p class="cart-product__info-old-price">
                                                    <?= number_format((float) $rp_reg_price, 0, '', ' '); ?> ₽
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                        <p class="cart-product__info-title search-modal-content-product__title">
                                            <?= esc_html($rp_title); ?>
                                        </p>
                                    </div>
                                </a>
                                <?php
                            endforeach;
                        endif;
                        ?>
                    </div>
                </div>
                <div class="search-modal-content search-modal-content--results">
                    <div class="search-modal-content__header">
                        <h5 class="search-modal__title">Результаты поиска</h5>
                        <div class="search-modal__clear">Сбросить</div>
                    </div>
                    <div class="search-modal-content__products">

                    </div>
                </div>
                <div class="search-modal-content-mobile">
                    <?php
                    // Рандомные товары для мобильного поиска (используем уже загруженные, если есть)
                    if (!isset($random_products)) {
                        $random_products = wc_get_products(array(
                            'status' => 'publish',
                            'limit' => 6,
                            'orderby' => 'rand',
                        ));
                    }

                    if ($random_products):
                        foreach ($random_products as $rp):
                            global $post, $product;
                            $post = get_post($rp->get_id());
                            $product = $rp;
                            setup_postdata($post);
                            get_template_part('template-parts/product-card');
                        endforeach;
                        wp_reset_postdata();
                    endif;
                    ?>
                </div>
            </div>
        </div>
    </div>




    <div class="darken"></div>
    <div class="header-modal">
        <div class="container">
            <div class="header-modal__header">
                <svg width="164" height="34">
                    <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#logo-full-white"></use>
                </svg>
                <div class="header-modal__cross">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_697_14958)">
                            <path d="M21 -3L-3 21M-3 -3L21 21" stroke="white" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </g>
                        <defs>
                            <clipPath id="clip0_697_14958">
                                <rect width="18" height="18" fill="white" />
                            </clipPath>
                        </defs>
                    </svg>
                </div>
            </div>
            <div class="header-modal-content">
                <div class="header-modal-content__row">
                    <a class="button button--fill" href="/shop/">
                        <svg width="18" height="18">
                            <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#catalog"></use>
                        </svg>
                        Каталог
                    </a>
                    <a class="button button--white callme-back" href="/contacti/">
                        <svg width="18" height="18">
                            <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#phone-blue"></use>
                        </svg>
                        Связаться
                    </a>
                </div>

                <?php
                $phone = get_field('phone', 'option');
                $address = get_field('address', 'option');
                function normalize_phone2($phone)
                {
                    $num = preg_replace('/\D+/', '', $phone);
                    if (strpos($num, '8') === 0)
                        $num = '7' . substr($num, 1);
                    elseif (strpos($num, '7') !== 0)
                        $num = '7' . $num;
                    return '+' . $num;
                }
                function pretty_phone_html($phone)
                {
                    if (preg_match('/(8.*?)\(?(\d{3})\)?(.*)/u', $phone, $m)) {
                        $prefix = $m[1];
                        $code = $m[2];
                        $rest = $m[3];
                        return $prefix .
                            '<span>(</span>' . $code . '<span>)</span>' .
                            $rest;
                    }
                    return esc_html($phone);
                }
                $clean_phone = $phone ? normalize_phone2($phone) : '';
                $pretty_phone = $phone ? pretty_phone_html($phone) : '';
                ?>
                <div class="header-modal-content__info">
                    <p class="header-modal-content__phone">
                        <?php if ($phone): ?>
                            <a href="tel:<?= $clean_phone ?>">
                                <?= $pretty_phone ?>
                            </a>
                        <?php endif; ?>
                    </p>
                    <p class="header-modal-content__recall">Обратный звонок</p>
                    <p class="header-modal-content__address">
                        <?= esc_html($address) ?>
                    </p>
                    <p class="header-modal-content__address-title">Адрес склада</p>
                </div>

                <?php
                $menu_items = wp_get_nav_menu_items(10880);
                ?>
                <div class="header-modal-content__btns">
                    <?php if (!empty($menu_items) && count($menu_items) >= 2): ?>
                        <button class="button button--fill" onclick="location.href='<?= esc_url($menu_items[0]->url) ?>'">
                            <svg width="16" height="16">
                                <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#wallet-white"></use>
                            </svg>
                            <?= esc_html($menu_items[0]->title) ?>
                        </button>
                        <button class="button button--red" onclick="location.href='<?= esc_url($menu_items[1]->url) ?>'">
                            <svg width="16" height="16">
                                <use xlink:href="<?= get_template_directory_uri(); ?>/images/sprite.svg#clock-white"></use>
                            </svg>
                            <?= esc_html($menu_items[1]->title) ?>
                        </button>
                    <?php endif; ?>
                </div>

            </div>
            <div class="header-modal-footer">
                <div class="header-modal-footer__columns">
                    <div class="footer-column footer-catalog">
                        <h5 class="footer-column__title">Каталог</h5>
                        <?php
                        $menu_id = 10306;
                        $menu_items = wp_get_nav_menu_items($menu_id);
                        $items_by_parent = [];
                        foreach ($menu_items as $item) {
                            $items_by_parent[$item->menu_item_parent][] = $item;
                        }
                        $parent_items = $items_by_parent[0] ?? [];
                        ?>
                        <?php if (!empty($parent_items)): ?>
                            <ul class="footer-column__links">
                                <?php foreach ($parent_items as $parent): ?>
                                    <li class="footer-column__link">
                                        <a href="<?= esc_url($parent->url); ?>">
                                            <?= esc_html($parent->title); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>

                    <?php
                    $locations = get_nav_menu_locations();
                    $menu_items = [];
                    if (!empty($locations['footer_for_client_mobile'])) {
                        $menu_id = $locations['footer_for_client_mobile'];
                        $menu_items = wp_get_nav_menu_items($menu_id);
                    }
                    ?>
                    <div class="footer-column">
                        <h5 class="footer-column__title">Для клиента</h5>
                        <?php if (!empty($menu_items)): ?>
                            <ul class="footer-column__links">
                                <?php foreach ($menu_items as $item): ?>
                                    <li class="footer-column__link">
                                        <a href="<?= esc_url($item->url); ?>">
                                            <?= esc_html($item->title); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="header-modal-footer__bottom">
                    <div class="footer-marketplaces">
                        <div class="footer-marketplaces-item">
                            <img src="<?= get_template_directory_uri(); ?>/images/content/others/yandex-market.png"
                                alt="Яндекс маркет" class="footer-marketplaces-item__img">
                            <p class="footer-marketplaces-item__text">Мы в Яндекс.Маркет</p>
                        </div>
                        <div class="footer-marketplaces-item">
                            <img src="<?= get_template_directory_uri(); ?>/images/content/others/ozon.png" alt="Ozon"
                                class="footer-marketplaces-item__img">
                            <p class="footer-marketplaces-item__text">Мы в Ozon</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <main class="main">
        <?php
        if (!is_front_page()):
            global $post;
            ?>
            <div class="breadcrumbs__wrapper">
                <div class="container">
                    <div class="breadcrumbs">
                        <?php
                        $back = wp_get_referer() ? wp_get_referer() : home_url('/');
                        $breadcrumbs = sklad140_get_breadcrumbs();
                        ?>

                        <a class="breadcrumbs__back" href="<?= esc_url($back) ?>">
                            <svg width="8" height="8">
                                <use xlink:href="<?= esc_url(get_template_directory_uri()); ?>/images/sprite.svg#arrow-blue"></use>
                            </svg>
                            Вернуться
                        </a>

                        <?php foreach ($breadcrumbs as $index => $crumb): ?>
                            <p class="breadcrumbs__slash">/</p>

                            <?php if (!empty($crumb['url']) && $index !== array_key_last($breadcrumbs)): ?>
                                <a class="breadcrumbs__link" href="<?= esc_url($crumb['url']) ?>">
                                    <?= esc_html($crumb['title']) ?>
                                </a>
                            <?php else: ?>
                                <span class="breadcrumbs__link">
                                    <?= esc_html($crumb['title']) ?>
                                </span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>


        <?php
        /*
        wp_nav_menu( array(
            'theme_location' => 'header',
            'container'      => '',
            'items_wrap'     => '<ul class="menu-list">%3$s</ul>',
            'walker'         => new Franch_Walker_Nav_Menu(),
        ) );
        */
        ?>