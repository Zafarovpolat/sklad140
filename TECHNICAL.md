# Техническая документация Склад140

## Содержание

1. [Архитектура](#архитектура)
2. [AJAX endpoints](#ajax-endpoints)
3. [Система поиска](#система-поиска)
4. [Фильтрация и каталог](#фильтрация-и-каталог)
5. [Infinite Scroll](#infinite-scroll)
6. [Корзина](#корзина)
7. [Формы](#формы)
8. [Wishlist и Compare](#wishlist-и-compare)
9. [Хлебные крошки](#хлебные-крошки)
10. [Сортировка товаров](#сортировка-товаров)
11. [JavaScript модули](#javascript-модули)
12. [Безопасность](#безопасность)
13. [Кэширование и производительность](#кэширование-и-производительность)
14. [Хуки и фильтры](#хуки-и-фильтры)

---

## Архитектура

### Общая схема запроса

```
Браузер → Apache → PHP/WordPress → WooCommerce → MySQL
                                  ↕
                            W3 Total Cache (файловый кэш)
```

### Для гостей (кэш включён)
1. Apache отдаёт закэшированную HTML-страницу из файла
2. JS на странице делает AJAX-запросы напрямую к `admin-ajax.php`
3. AJAX-ответы НЕ кэшируются

### Для залогиненных (кэш отключён)
1. Каждый запрос проходит через PHP/WordPress полностью
2. Плагины (Yoast, W3TC admin) добавляют overhead → медленнее

---

## AJAX endpoints

Все endpoints зарегистрированы в `functions.php` через `wp_ajax_` и `wp_ajax_nopriv_` хуки.


### Таблица endpoints

| Action | Функция | Nonce | Описание |
|--------|---------|-------|----------|
| `theme_live_search` | `theme_live_search()` | **Нет** (убран 21.05.2026) | Живой поиск товаров |
| `filter_products` | `s140_ajax_filter_products()` | Нет | Фильтрация каталога |
| `load_more_products` | `s140_ajax_load_more_products()` | Нет | Infinite scroll |
| `update_cart_item_custom` | `theme_update_cart_item_custom()` | `custom-cart-nonce` | Обновление кол-ва в корзине |
| `s140_submit_form` | `s140_submit_form_handler()` | Нет (honeypot) | Единая отправка форм |
| `force_remove_from_wishlist` | `custom_force_remove_from_wishlist()` | Нет | Удаление из избранного |
| `get_wishlist_count` | `custom_get_wishlist_count()` | Нет | Счётчик избранного |
| `update_cart_count` | `theme_update_cart_count()` | Нет | Счётчик корзины |
| `submit_review` | `submit_review_callback()` | Нет | Отправка отзыва |

### Почему убран nonce у `theme_live_search`

**Проблема:** W3 Total Cache кэширует HTML страницы вместе с nonce-токеном. Nonce действителен 24 часа, но страница в кэше может лежать дольше. Для гостей (и иногда для админов при стухшем кэше) `check_ajax_referer()` отклонял запрос → ответ `-1` с кодом 403.

**Решение:** Nonce удалён. Endpoint `theme_live_search` — публичный read-only поиск, не выполняет никаких мутаций. Защита не требуется.

**Альтернатива (если понадобится вернуть):** Загружать nonce через отдельный некэшируемый AJAX-запрос при инициализации поиска.

---

## Система поиска

### Три уровня поиска

1. **Живой поиск (модалка)** — `theme-search.js` → `theme_live_search()`
2. **Dropdown поиск (под input)** — `search-dropdown.js` → `theme_live_search()`
3. **Страница результатов** — `/shop/?s=запрос` → `pre_get_posts` hook

### Алгоритм мягкого поиска

```php
// Запрос "стол морозильный hicold 11" разбивается на слова ≥2 символов:
$words = ['стол', 'морозильный', 'hicold', '11'];

// Каждое слово ищется через LIKE в post_title (AND):
WHERE post_title LIKE '%стол%'
  AND post_title LIKE '%морозильный%'
  AND post_title LIKE '%hicold%'
  AND post_title LIKE '%11%'
```

### Ранжирование результатов

```sql
ORDER BY
  CASE
    WHEN LOWER(post_title) LIKE LOWER('стол%') THEN 100    -- начинается с первого слова
    WHEN LOWER(post_title) LIKE LOWER('%стол%') THEN 50     -- содержит первое слово
    ELSE 0
  END DESC,
  -- далее стандартная сортировка (дата/цена)
```

### Поиск в шапке — как работает

1. Пользователь кликает в input `#search`
2. Открывается модалка `.search-modal` (или dropdown `.search-dropdown`)
3. При вводе ≥2 символов → debounce 300ms → AJAX к `theme_live_search`
4. Результат: десктопный HTML + мобильный HTML (карточки товаров)
5. При Enter или клике по иконке лупы → навигация на `/shop/?s=term`


### Иконка поиска (кликабельная)

В header.php иконка лупы — это `<button id="header-search-submit">` (ранее был `<div>` с `pointer-events-none`).

JS обработчик в `theme-search.js`:
```javascript
var headerSearchSubmitBtn = document.getElementById('header-search-submit');
headerSearchSubmitBtn.addEventListener('click', function (e) {
    e.preventDefault();
    e.stopPropagation();
    var term = headerInput.value.trim();
    if (term.length >= 2) {
        window.location.href = 'https://sklad140.ru/shop/?s=' + encodeURIComponent(term);
    }
});
```

---

## Фильтрация и каталог

### POST-параметры `filter_products` / `load_more_products`

```
term_id      — int, ID категории (0 = все)
paged        — int, номер страницы
orderby      — string: date|price|price-desc|rating
s            — string, поисковый запрос
color[]      — array, slugs цветов
material[]   — array, slugs материалов
attr_brend[] — array, slugs бренда (динамический)
attr_*[]     — array, любой атрибут WooCommerce
price_min    — float
price_max    — float
stock        — bool, только в наличии
loaded_ids[] — array int, уже загруженные ID (защита от дублей)
```

### Ответ

```json
{
  "success": true,
  "data": {
    "html": "<div class='product-card'>...</div>...",
    "found": 42,
    "max_pages": 4,
    "current_page": 1
  }
}
```

### Динамические атрибуты

Система автоматически обрабатывает ВСЕ зарегистрированные атрибуты WooCommerce:

```php
$attribute_taxonomies = wc_get_attribute_taxonomies();
foreach ($attribute_taxonomies as $attr) {
    $param_name = 'attr_' . $attr->attribute_name;
    if (!empty($_POST[$param_name])) {
        // → tax_query по pa_{attribute_name}
    }
}
```

### Сортировка «в наличии сверху»

Хук `posts_clauses` (приоритет 999) добавляет сортировку по `stock_status`:

```sql
ORDER BY
  CASE
    WHEN stock_status = 'instock' THEN 0
    WHEN stock_status = 'onbackorder' THEN 1
    WHEN stock_status = 'outofstock' THEN 2
  END ASC,
  -- далее основная сортировка
```

Работает на: `/shop/`, категориях, AJAX-фильтрации (`s140_stock_first` query var).

---

## Infinite Scroll

### Защита от дублей

**Проблема:** При скролле могли загружаться те же товары повторно (разница page size, изменение данных между запросами).

**Решение:** Фронтенд передаёт `loaded_ids[]` — массив всех уже загруженных product ID:

```php
if (!empty($_POST['loaded_ids'])) {
    $args['post__not_in'] = array_map('intval', $_POST['loaded_ids']);
    $args['paged'] = 1;  // игнорируем пагинацию, опираемся только на исключение
}
```

### Логика на фронте

Инлайн-скрипт в `archive-product.php` / `taxonomy-product_cat.php`:
1. Наблюдает IntersectionObserver или scroll position
2. При приближении к низу → POST к `load_more_products`
3. Вставляет HTML в grid, сохраняет ID в массив
4. Останавливается когда `current_page >= max_pages`

**Примечание:** Внешний `infinite-scroll.js` ОТКЛЮЧЁН (дублировал инлайн-логику).

---

## Корзина

### Обновление количества (`update_cart_item_custom`)

**Защищён nonce** (`custom-cart-nonce`).

Ответ включает:
```json
{
  "quantity": 2,
  "line_total": "2 000 ₽",
  "line_regular": "2 500 ₽",
  "has_discount": true,
  "cart_count": 5,
  "subtotal": "10 000 ₽",
  "discount_total": 1500,
  "discount_formatted": "1 500 ₽",
  "total": "8 500 ₽"
}
```

### AJAX добавление в корзину

В `archive-product.php` — через стандартный WooCommerce endpoint:
```
POST /wp-admin/admin-ajax.php?action=woocommerce_add_to_cart
```
После успеха: обновление фрагментов WC, замена кнопки на «В корзине», toast-уведомление.

---

## Формы

### Единый endpoint `s140_submit_form`

Обрабатывает ВСЕ формы на сайте. Типы:

| `form_type` | Назначение |
|-------------|-----------|
| `callme-back` | Перезвонить мне |
| `one-click-buy` | Купить в 1 клик |
| `choose-exact` | Подобрать аналог |
| `other-questions` | Остались вопросы |
| `contact-us` | Связаться с нами |
| `vacancy` | Отклик на вакансию |
| `subscribe` | Подписка на рассылку |
| `generic` | Заявка с сайта |

### Валидация

**Бэкенд:**
- Имя — обязательно
- Телефон — ≥10 цифр
- Согласие на обработку ПДн — обязательно
- Honeypot: если заполнены `website` или `hp_field` → тихо принимаем, не шлём

**Фронтенд** (`modal-validation.js`):
- Подсветка невалидных полей (`.border-red-500`)
- Маска телефона через InputMask (`+7 (999) 999-99-99`)

### Получатель

ACF опция `pochta` → fallback `nasklad140@gmail.com`.

### Лог заявок

Все заявки сохраняются в `wp_options` (ключ `s140_form_submissions_log`, последние 200 записей) — на случай если wp_mail не сработает.


---

## Wishlist и Compare

### YITH Wishlist — кастомные обработчики

Стандартный YITH плагин дополнен прямыми SQL-обработчиками для надёжности:

**Удаление** (`force_remove_from_wishlist`):
```php
$wpdb->delete($table, ['prod_id' => $product_id, 'user_id' => $user_id]);
```

**Счётчик** (`get_wishlist_count`):
- Способ 1: `yith_wcwl_count_products()`
- Способ 2: `YITH_WCWL()->count_products()`
- Способ 3: Прямой SQL `SELECT COUNT(*) FROM wp_yith_wcwl WHERE user_id = %d`

Всё обёрнуто в try/catch с подавлением ошибок для чистого JSON-ответа.

### YITH Compare — счётчик в шапке

Парсинг cookie `yith_woocompare_list` (JSON массив или строка с разделителями).
Обновление через jQuery-события `yith_woocompare_added`/`yith_woocompare_removed` + `setInterval(300ms)`.

---

## Хлебные крошки

Функция `sklad140_get_breadcrumbs()` в `functions.php`. Поддерживает:

- Главная → Каталог → Категория → Подкатегория → Товар
- Главная → Страница (с иерархией parent)
- Главная → Блог → Категория → Статья
- Главная → Поиск
- Главная → 404

Отображение в `header.php` после `</header>` (кроме главной).

---

## Сортировка товаров

### Приоритеты (через `posts_clauses` hook, приоритет 999)

1. **Наличие** (instock → onbackorder → outofstock)
2. **Основная сортировка** (выбранная пользователем или дефолтная)

### Варианты основной сортировки

| Значение | ORDER BY |
|----------|----------|
| `date` | `post_date DESC` |
| `price` | `_price ASC` |
| `price-desc` | `_price DESC` |
| `rating` | `_wc_average_rating DESC` |

---

## JavaScript модули

### main.js (~24KB)
- Класс `SearchModal` — открытие/закрытие модалки поиска, блокировка скролла
- Инициализация Swiper слайдеров
- Мобильное меню (`.header-modal`)
- Каталог в шапке (двойной клик на мобиле)

### theme-search.js (~11KB)
- Живой поиск через модалку
- Синхронизация header input ↔ modal input
- Debounce 300ms → AJAX `theme_live_search`
- История поиска (localStorage `siteSearchHistory`)
- «Часто ищут» — клик по табам
- **Клик по иконке лупы** → навигация на `/shop/?s=`

### search-dropdown.js (~4.4KB)
- Альтернативный режим: dropdown прямо под input (без модалки)
- Скрывает `.search-modal` при использовании
- AJAX к тому же `theme_live_search`

### ajax-cart.js (~1.4KB)
- Обновление фрагментов WC после добавления в корзину
- Синхронизация счётчика корзины в шапке

### modal-validation.js (~4.5KB)
- Перехват submit всех форм (`.modal-content-form`)
- Валидация (имя, телефон, consent)
- AJAX отправка на `s140_submit_form`
- Показ success/error состояний

### minified/main.min.js
- Продакшен-версия `main.js`
- Подключается в `header.php` с `?ver={filemtime}`

---

## Безопасность

### Что защищено nonce
- `update_cart_item_custom` — `custom-cart-nonce`
- `s140_submit_form` — через `s140Forms.nonce` (на фронте), но проверка в handler отсутствует (защита через honeypot)

### Что НЕ защищено nonce (и почему)
- `theme_live_search` — публичный read-only, кэш ломает nonce
- `filter_products` / `load_more_products` — публичный read-only
- `get_wishlist_count` — read-only
- `force_remove_from_wishlist` — проверяет `user_id` через сессию

### Санитизация (везде)
```php
sanitize_text_field(), sanitize_textarea_field(), sanitize_email()
wp_unslash(), $wpdb->esc_like(), $wpdb->prepare()
```

### Экранирование (везде)
```php
esc_html(), esc_url(), esc_attr(), wp_kses_post()
```

### Honeypot (формы)
Скрытые поля `website` / `hp_field` — если заполнены, запрос тихо принимается без отправки письма.

---

## Кэширование и производительность

### W3 Total Cache

- **Page Cache:** Disk Enhanced (файловый кэш для гостей)
- **Залогиненные:** кэш отключён (нет кэширования по ролям)
- **admin-ajax.php:** не кэшируется

### WP-Cron

Отключён (`DISABLE_WP_CRON = true`). Заменён серверным cron:
```
*/5 * * * * wget -q -O /dev/null https://sklad140.ru/wp-cron.php
```

### Оптимизации БД (выполнены 21.04.2026)

- Удалено 20 000 мусорных записей (transients, orphaned meta)
- Удалено 9 000 ревизий постов
- Добавлены индексы на часто используемые meta_key

### Минификация

- CSS: все файлы `.min.css`
- JS: `/js/minified/main.min.js` (+ `index.min.js` для главной)
- Версионирование: `?ver={filemtime}` для cache-busting

### Lazy Loading

```html
<img loading="lazy" ...>
```

Все изображения в карточках товаров, баннерах, футере.

---

## Хуки и фильтры

### Кастомные actions

```php
// Фильтрация
add_action('wp_ajax[_nopriv]_filter_products', 's140_ajax_filter_products');
add_action('wp_ajax[_nopriv]_load_more_products', 's140_ajax_load_more_products');

// Поиск
add_action('wp_ajax[_nopriv]_theme_live_search', 'theme_live_search');

// Корзина
add_action('wp_ajax[_nopriv]_update_cart_item_custom', 'theme_update_cart_item_custom');
add_action('wp_ajax[_nopriv]_update_cart_count', 'theme_update_cart_count');

// Формы
add_action('wp_ajax[_nopriv]_s140_submit_form', 's140_submit_form_handler');

// Wishlist
add_action('wp_ajax[_nopriv]_force_remove_from_wishlist', ...);
add_action('wp_ajax[_nopriv]_get_wishlist_count', ...);

// Отзывы
add_action('wp_ajax[_nopriv]_submit_review', 'submit_review_callback');
```

### Кастомные filters

```php
// Сортировка «в наличии сверху»
add_filter('posts_clauses', 's140_stock_first_catalog_order', 999, 2);

// Мягкий поиск
add_action('pre_get_posts', function($query) { ... }, 20);
// → add_filter('posts_where', ...) + add_filter('posts_orderby', ...)

// Все атрибуты в карточке товара
add_filter('woocommerce_display_product_attributes', ..., 20, 2);

// Перевод "Product Info" → "Товар"
add_filter('gettext', ..., 10, 3);

// Необязательные поля checkout
add_filter('woocommerce_checkout_fields', ...);

// SVG/ICO upload
add_filter('upload_mimes', ...);
add_filter('wp_check_filetype_and_ext', ...);
```

### Редиректы

```php
// /vacant/ → /vacancy/ (301)
add_action('init', function() { ... });
```

---

## Локализация JS

```php
// Поиск
wp_localize_script('theme-search', 'ThemeSearch', [
    'ajaxUrl' => admin_url('admin-ajax.php'),
    'nonce'   => wp_create_nonce('theme_search_nonce'), // legacy, не проверяется
]);

// Формы
wp_localize_script('modal-validation', 's140Forms', [
    'ajaxUrl' => admin_url('admin-ajax.php'),
    'nonce'   => wp_create_nonce('s140_submit_form'),
]);

// Корзина
wp_localize_script('ajax-cart', 'ajaxCart', [
    'ajaxurl' => admin_url('admin-ajax.php'),
    'wc_ajax' => site_url(),
]);
```

---

## Карточка товара (`template-parts/product-card.php`)

Используется везде: каталог, поиск, infinite scroll, похожие товары, мобильный поиск.

Показывает:
- Изображение (с fallback на placeholder)
- Название
- Цена (текущая + зачёркнутая если скидка)
- Первые 3 атрибута (`strike_first_attributes()`)
- Статус наличия
- Кнопка «В корзину» (или «Подобрать аналог» если не в наличии)
- Кнопки Wishlist / Compare

---

## Атрибуты товаров

### Проблема с `is_visible`

Bulk-editor WooCommerce создаёт атрибуты с `is_visible=0`. Они появляются в листинге, но пропадают в карточке.

### Решение

Хук `woocommerce_display_product_attributes` (приоритет 20) принудительно добавляет ВСЕ атрибуты товара, включая `is_visible=0`:

```php
foreach ($product->get_attributes() as $attribute) {
    $key = 'attribute_' . sanitize_title_with_dashes($name);
    if (isset($product_attributes[$key])) continue; // уже есть
    // ... добавляем в массив
}
```

---

**Последнее обновление:** 21.05.2026
