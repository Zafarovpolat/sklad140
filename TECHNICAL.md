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
10. [JavaScript модули](#javascript-модули)
11. [Безопасность](#безопасность)
12. [Кэширование и производительность](#кэширование-и-производительность)
13. [Хуки и фильтры](#хуки-и-фильтры)

---

## Архитектура

### Общая схема

```
Браузер → Apache → PHP/WordPress → WooCommerce → MySQL
                                  ↕
                            W3 Total Cache (файловый кэш)
```

**Для гостей (кэш включён):** Apache отдаёт закэшированную HTML. JS делает AJAX напрямую к `admin-ajax.php` (не кэшируется).

**Для залогиненных (кэш отключён):** Каждый запрос через PHP/WordPress полностью. Плагины (Yoast, W3TC admin) добавляют overhead.

---

## AJAX endpoints

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

**Проблема:** W3TC кэширует HTML с nonce-токеном. Nonce живёт 24ч, но страница в кэше дольше. `check_ajax_referer()` отклонял запрос → 403.

**Решение:** Nonce удалён. Endpoint публичный read-only, мутаций нет.

---

## Система поиска

### Три уровня

1. **Живой поиск (модалка)** — `theme-search.js` → `theme_live_search()`
2. **Dropdown (под input)** — `search-dropdown.js` → `theme_live_search()`
3. **Страница результатов** — `/shop/?s=запрос` → `pre_get_posts` hook

### Алгоритм мягкого поиска

```php
// "стол морозильный hicold 11" → слова >=2 символов:
$words = ['стол', 'морозильный', 'hicold', '11'];

// SQL:
WHERE post_title LIKE '%стол%'
  AND post_title LIKE '%морозильный%'
  AND post_title LIKE '%hicold%'
  AND post_title LIKE '%11%'
```

### Ранжирование

```sql
ORDER BY
  CASE
    WHEN LOWER(post_title) LIKE LOWER('стол%') THEN 100
    WHEN LOWER(post_title) LIKE LOWER('%стол%') THEN 50
    ELSE 0
  END DESC
```

### Иконка поиска (кликабельная)

`header.php`: `<button id="header-search-submit">` (ранее `<div pointer-events-none>`).

`theme-search.js`:
```javascript
document.getElementById('header-search-submit').addEventListener('click', function(e) {
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

| Параметр | Тип | Описание |
|----------|-----|----------|
| `term_id` | int | ID категории (0 = все) |
| `paged` | int | Номер страницы |
| `orderby` | string | date, price, price-desc, rating |
| `s` | string | Поисковый запрос |
| `color[]` | array | Slugs цветов |
| `material[]` | array | Slugs материалов |
| `attr_*[]` | array | Любой атрибут WooCommerce |
| `price_min` | float | Мин. цена |
| `price_max` | float | Макс. цена |
| `stock` | bool | Только в наличии |
| `loaded_ids[]` | array int | Уже загруженные ID (защита от дублей) |

### Динамические атрибуты

Автоматически обрабатываются ВСЕ атрибуты WooCommerce:
```php
foreach (wc_get_attribute_taxonomies() as $attr) {
    $param_name = 'attr_' . $attr->attribute_name;
    // → tax_query по pa_{attribute_name}
}
```

### Сортировка «в наличии сверху»

Хук `posts_clauses` (приоритет 999):
```sql
ORDER BY CASE WHEN stock_status='instock' THEN 0 WHEN 'outofstock' THEN 2 END ASC, ...
```

---

## Infinite Scroll

### Защита от дублей

Фронтенд передаёт `loaded_ids[]` — массив всех загруженных product ID:
```php
if (!empty($_POST['loaded_ids'])) {
    $args['post__not_in'] = array_map('intval', $_POST['loaded_ids']);
    $args['paged'] = 1;
}
```

Внешний `infinite-scroll.js` ОТКЛЮЧЁН — логика в инлайн-скриптах `archive-product.php`.

---

## Корзина

### Обновление количества (`update_cart_item_custom`)

Защищён nonce `custom-cart-nonce`. Возвращает:
- `line_total`, `line_regular`, `has_discount`
- `cart_count`, `subtotal`, `discount_formatted`, `total`

### AJAX добавление

Через стандартный WC endpoint `woocommerce_add_to_cart`. После успеха: обновление фрагментов WC, замена кнопки на «В корзине», toast.

---

## Формы

### Единый endpoint `s140_submit_form`

| `form_type` | Назначение |
|-------------|-----------|
| `callme-back` | Перезвонить мне |
| `one-click-buy` | Купить в 1 клик |
| `choose-exact` | Подобрать аналог |
| `contact-us` | Связаться с нами |
| `vacancy` | Отклик на вакансию |
| `subscribe` | Подписка на рассылку |

**Валидация:** имя (обязательно), телефон (>=10 цифр), согласие ПДн.
**Honeypot:** поля `website`/`hp_field` — если заполнены, тихо принимаем без отправки.
**Получатель:** ACF опция `pochta` → fallback `nasklad140@gmail.com`.
**Лог:** `wp_options` ключ `s140_form_submissions_log` (последние 200).

---

## Wishlist и Compare

### YITH Wishlist — кастомные обработчики

- `force_remove_from_wishlist` — прямой SQL DELETE из `wp_yith_wcwl`
- `get_wishlist_count` — 3 fallback-способа получения счётчика

### YITH Compare — счётчик в шапке

Парсинг cookie `yith_woocompare_list` + jQuery-события + `setInterval(300ms)`.

---

## Хлебные крошки

Функция `sklad140_get_breadcrumbs()`. Поддерживает:
- Главная → Каталог → Категория → Подкатегория → Товар
- Главная → Страница (с иерархией parent)
- Главная → Блог → Категория → Статья
- Главная → Поиск / 404

---

## JavaScript модули

| Файл | Назначение |
|------|-----------|
| `main.js` | SearchModal, Swiper, мобильное меню, каталог шапки |
| `theme-search.js` | Живой поиск через модалку, история, иконка лупы |
| `search-dropdown.js` | Dropdown под input (альтернатива модалке) |
| `ajax-cart.js` | Обновление фрагментов WC, счётчик корзины |
| `modal-validation.js` | Валидация + AJAX отправка форм |

Минифицированные версии в `/js/minified/`. Подключаются с `?ver={filemtime}`.

---

## Безопасность

**Защищено nonce:** `update_cart_item_custom`

**Без nonce (и почему):**
- `theme_live_search` — read-only, кэш ломает nonce
- `filter_products` / `load_more_products` — read-only
- `get_wishlist_count` — read-only
- `force_remove_from_wishlist` — проверяет user_id через сессию

**Везде:** `sanitize_text_field()`, `$wpdb->prepare()`, `esc_html()`, `esc_url()`

**Honeypot** в формах вместо nonce (из-за кэша).

---

## Кэширование и производительность

- **W3TC:** Disk Enhanced для гостей, отключён для залогиненных
- **WP-Cron:** отключён, серверный cron каждые 5 мин
- **БД:** Очищено 20K мусорных записей + 9K ревизий, добавлены индексы
- **Минификация:** CSS `.min.css`, JS `/js/minified/`, SVG спрайты
- **Lazy loading:** `<img loading="lazy">`
- **Версионирование:** `?ver={filemtime}` для cache-busting

---

## Хуки и фильтры

### Ключевые кастомные хуки

```php
// Фильтрация + infinite scroll
add_action('wp_ajax[_nopriv]_filter_products', 's140_ajax_filter_products');
add_action('wp_ajax[_nopriv]_load_more_products', 's140_ajax_load_more_products');

// Поиск
add_action('wp_ajax[_nopriv]_theme_live_search', 'theme_live_search');

// Формы
add_action('wp_ajax[_nopriv]_s140_submit_form', 's140_submit_form_handler');

// Сортировка «в наличии сверху»
add_filter('posts_clauses', 's140_stock_first_catalog_order', 999, 2);

// Мягкий поиск
add_action('pre_get_posts', function($query) { ... }, 20);

// Все атрибуты в карточке товара
add_filter('woocommerce_display_product_attributes', ..., 20, 2);

// 301 редирект /vacant/ → /vacancy/
add_action('init', function() { ... });
```

### Локализация JS

```php
wp_localize_script('theme-search', 'ThemeSearch', ['ajaxUrl' => ..., 'nonce' => ...]);
wp_localize_script('modal-validation', 's140Forms', ['ajaxUrl' => ..., 'nonce' => ...]);
wp_localize_script('ajax-cart', 'ajaxCart', ['ajaxurl' => ...]);
```

---

**Последнее обновление:** 21.05.2026
