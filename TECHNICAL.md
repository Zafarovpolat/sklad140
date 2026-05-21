# Техническая документация Склад140

## 📚 Содержание

1. [JavaScript модули](#javascript-модули)
2. [AJAX обработчики](#ajax-обработчики)
3. [Система фильтрации](#система-фильтрации)
4. [Infinite Scroll](#infinite-scroll)
5. [Поиск товаров](#поиск-товаров)
6. [Корзина](#корзина)
7. [Wishlist и Compare](#wishlist-и-compare)
8. [Модальные окна](#модальные-окна)
9. [База данных](#база-данных)
10. [Хуки и фильтры](#хуки-и-фильтры)

---

## 🎯 JavaScript модули

### 1. main.js (24KB)

**Назначение**: Основной скрипт темы, управляет общей функциональностью.

**Основные функции**:

- Инициализация слайдеров Swiper
- Управление мобильным меню
- Обработка модальных окон
- Управление каталогом в шапке
- Анимации и переходы

**Ключевые компоненты**:

```javascript
// Инициализация Swiper слайдеров
const swiper = new Swiper(".swiper", {
  // конфигурация
});

// Управление каталогом
const catalogBtn = document.querySelector(".header-search__catalog-btn");
const catalogMenu = document.querySelector(".header-search__catalog-links");

// Мобильное меню
const menuToggle = document.querySelector(".header-mobile-links__link--menu");
```

### 2. infinite-scroll.js (12KB)

**Назначение**: Реализация бесконечной прокрутки для каталога товаров.

**Принцип работы**:

1. Отслеживает позицию скролла
2. При достижении конца страницы отправляет AJAX запрос
3. Загружает следующую страницу товаров
4. Добавляет товары в DOM без перезагрузки

**Ключевые параметры**:

```javascript
const grid = document.getElementById("products-grid");
const termId = grid.dataset.termId;
const maxPages = grid.dataset.maxPages;
let currentPage = 1;
let isLoading = false;
```

**AJAX запрос**:

```javascript
fetch(ajaxUrl + "?action=load_more_products", {
  method: "POST",
  body: formData,
});
```

### 3. theme-search.js (11KB)

**Назначение**: Живой поиск товаров с автодополнением.

**Функционал**:

- Поиск по мере ввода (debounce 300ms)
- Отображение результатов в выпадающем списке
- Подсветка найденных товаров
- История поиска

**Структура**:

```javascript
const searchInput = document.getElementById("search");
let searchTimeout = null;

searchInput.addEventListener("input", (e) => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    performSearch(e.target.value);
  }, 300);
});
```

### 4. ajax-cart.js (1.4KB)

**Назначение**: AJAX обновление корзины.

**Функции**:

- Добавление товара в корзину
- Обновление счетчика корзины
- Обновление фрагментов WooCommerce

**Интеграция с WooCommerce**:

```javascript
jQuery(document.body).on("wc_fragments_refreshed", function () {
  updateCartCount();
});
```

### 5. modal-validation.js (4.5KB)

**Назначение**: Валидация форм в модальных окнах.

**Проверки**:

- Обязательные поля
- Формат телефона
- Формат email
- Длина текста

**Пример валидации**:

```javascript
function validatePhone(phone) {
  const pattern = /^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/;
  return pattern.test(phone);
}
```

### 6. search-dropdown.js (4.4KB)

**Назначение**: Выпадающий список результатов поиска.

**Особенности**:

- Позиционирование относительно input
- Клик вне области закрывает dropdown
- Навигация клавиатурой (стрелки, Enter)

---

## 🔌 AJAX обработчики

### 1. filter_products

**Файл**: `functions.php:439`  
**Action**: `wp_ajax_filter_products`, `wp_ajax_nopriv_filter_products`

**Параметры POST**:

```php
[
    'term_id' => int,           // ID категории
    'paged' => int,             // Номер страницы
    'orderby' => string,        // Сортировка
    'color' => array,           // Фильтр по цвету
    'material' => array,        // Фильтр по материалу
    'price_min' => float,       // Минимальная цена
    'price_max' => float,       // Максимальная цена
    'stock' => bool,            // Только в наличии
    's' => string,              // Поисковый запрос
    'attr_*' => array           // Динамические атрибуты
]
```

**Ответ**:

```json
{
  "success": true,
  "data": {
    "html": "...",
    "found": 42,
    "max_pages": 4,
    "current_page": 1
  }
}
```

**Логика фильтрации**:

1. Построение WP_Query с tax_query и meta_query
2. Фильтрация по категории (с дочерними)
3. Фильтрация по атрибутам (цвет, материал, кастомные)
4. Фильтрация по цене (BETWEEN, >=, <=)
5. Фильтрация по наличию (\_stock_status)
6. Поиск по названию товара (LIKE)

### 2. load_more_products

**Файл**: `functions.php:642`  
**Action**: `wp_ajax_load_more_products`, `wp_ajax_nopriv_load_more_products`

**Параметры**: Аналогичны `filter_products`

**Отличия**:

- Используется для infinite scroll
- Возвращает только HTML товаров
- Поддерживает рандомную сортировку для главной страницы

### 3. theme_live_search

**Файл**: `functions.php:877`  
**Action**: `wp_ajax_theme_live_search`, `wp_ajax_nopriv_theme_live_search`

**Параметры**:

```php
[
    'term' => string,  // Поисковый запрос (мин. 2 символа)
    'nonce' => string  // Nonce для безопасности
]
```

**Ответ**:

```json
{
  "success": true,
  "data": {
    "html": "...",
    "count": 15
  }
}
```

**Особенности**:

- Минимум 2 символа для поиска
- Лимит 30 результатов
- Сортировка по релевантности
- Возвращает готовый HTML

### 4. update_cart_item_custom

**Файл**: `functions.php:200`  
**Action**: `wp_ajax_update_cart_item_custom`, `wp_ajax_nopriv_update_cart_item_custom`

**Параметры**:

```php
[
    'cart_item_key' => string,  // Ключ товара в корзине
    'quantity' => int,          // Новое количество
    'nonce' => string           // Nonce
]
```

**Ответ**:

```json
{
  "success": true,
  "data": {
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
}
```

**Логика**:

1. Проверка nonce
2. Обновление количества в корзине WooCommerce
3. Пересчет итогов
4. Расчет скидок (regular_price vs sale_price)
5. Возврат обновленных данных

### 5. force_remove_from_wishlist

**Файл**: `functions.php:1072`  
**Action**: `wp_ajax_force_remove_from_wishlist`, `wp_ajax_nopriv_force_remove_from_wishlist`

**Параметры**:

```php
[
    'product_id' => int  // ID товара
]
```

**Ответ**:

```json
{
    "success": true,
    "data": {
        "removed": true,
        "product_id": 123,
        "count": 5,
        "debug": {...}
    }
}
```

**Особенности**:

- Прямая работа с таблицей `wp_yith_wcwl`
- Отключение вывода ошибок для чистого JSON
- Подробная отладочная информация

### 6. get_wishlist_count

**Файл**: `functions.php:1149`  
**Action**: `wp_ajax_get_wishlist_count`, `wp_ajax_nopriv_get_wishlist_count`

**Ответ**:

```json
{
  "success": true,
  "data": {
    "count": 7
  }
}
```

**Методы получения счетчика**:

1. `yith_wcwl_count_products()` - стандартная функция
2. `YITH_WCWL()->count_products()` - через объект
3. Прямой запрос к БД - резервный вариант

---

## 🔍 Система фильтрации

### Архитектура

```
┌─────────────────┐
│  Форма фильтров │
│  (sidebar)      │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  JavaScript     │
│  обработчик     │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  AJAX запрос    │
│  filter_products│
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  PHP обработчик │
│  WP_Query       │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  HTML товаров   │
│  (product-card) │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Обновление DOM │
│  + URL          │
└─────────────────┘
```

### Типы фильтров

#### 1. Категории (Taxonomy)

```php
$tax_query[] = [
    'taxonomy' => 'product_cat',
    'field' => 'term_id',
    'terms' => $term_id,
    'include_children' => true
];
```

#### 2. Атрибуты (Color, Material)

```php
$color_tax = s140_attr_tax_slug_by_label('цвет');
$tax_query[] = [
    'taxonomy' => $color_tax,  // pa_cvet
    'field' => 'slug',
    'terms' => ['krasnyj', 'sinij'],
    'operator' => 'IN'
];
```

#### 3. Цена (Meta Query)

```php
$meta_query[] = [
    'key' => '_price',
    'value' => [$min, $max],
    'compare' => 'BETWEEN',
    'type' => 'DECIMAL(20,4)'
];
```

#### 4. Наличие (Stock Status)

```php
$meta_query[] = [
    'key' => '_stock_status',
    'value' => 'instock'
];
```

### Динамические атрибуты

Система автоматически обрабатывает любые атрибуты WooCommerce:

```php
$attribute_taxonomies = wc_get_attribute_taxonomies();
foreach ($attribute_taxonomies as $attr) {
    $param_name = 'attr_' . $attr->attribute_name;

    if (!empty($_POST[$param_name])) {
        $taxonomy = wc_attribute_taxonomy_name($attr->attribute_name);
        // Добавление в tax_query
    }
}
```

**Примеры атрибутов**:

- `attr_brend` → `pa_brend` (Бренд)
- `attr_strana` → `pa_strana` (Страна)
- `attr_material` → `pa_material` (Материал)

### Сортировка

```php
switch ($orderby) {
    case 'price':
        $args['orderby'] = 'meta_value_num';
        $args['meta_key'] = '_price';
        $args['order'] = 'ASC';
        break;
    case 'price-desc':
        $args['orderby'] = 'meta_value_num';
        $args['meta_key'] = '_price';
        $args['order'] = 'DESC';
        break;
    case 'rating':
        $args['orderby'] = 'meta_value_num';
        $args['meta_key'] = '_wc_average_rating';
        $args['order'] = 'DESC';
        break;
    default:
        $args['orderby'] = 'date';
        $args['order'] = 'DESC';
}
```

---

## ♾️ Infinite Scroll

### Принцип работы

1. **Инициализация**:

```javascript
const grid = document.getElementById("products-grid");
const termId = parseInt(grid.dataset.termId) || 0;
let maxPages = parseInt(grid.dataset.maxPages) || 1;
let currentPage = 1;
let isLoading = false;
```

2. **Отслеживание скролла**:

```javascript
window.addEventListener("scroll", () => {
  if (isLoading) return;

  const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
  const scrollHeight = document.documentElement.scrollHeight;
  const clientHeight = document.documentElement.clientHeight;

  if (scrollTop + clientHeight >= scrollHeight - 500) {
    loadMoreProducts();
  }
});
```

3. **Загрузка товаров**:

```javascript
async function loadMoreProducts() {
  if (currentPage >= maxPages) return;

  isLoading = true;
  currentPage++;

  const formData = new FormData();
  formData.append("action", "load_more_products");
  formData.append("term_id", termId);
  formData.append("paged", currentPage);
  // ... другие параметры фильтров

  const response = await fetch(ajaxUrl, {
    method: "POST",
    body: formData,
  });

  const data = await response.json();

  if (data.success) {
    grid.insertAdjacentHTML("beforeend", data.data.html);
  }

  isLoading = false;
}
```

### Синхронизация с фильтрами

При изменении фильтров:

1. Сбрасывается `currentPage = 1`
2. Обновляется `maxPages` из ответа сервера
3. Очищается grid и загружается первая страница
4. Infinite scroll продолжает работать с новыми параметрами

---

## 🔎 Поиск товаров

### Типы поиска

#### 1. Живой поиск (Live Search)

**Файл**: `theme-search.js`

**Особенности**:

- Debounce 300ms
- Минимум 2 символа
- Поиск только по названию товара
- Лимит 30 результатов

**Реализация**:

```javascript
let searchTimeout;
searchInput.addEventListener("input", (e) => {
  const term = e.target.value.trim();

  clearTimeout(searchTimeout);

  if (term.length < 2) {
    hideResults();
    return;
  }

  searchTimeout = setTimeout(() => {
    performSearch(term);
  }, 300);
});
```

#### 2. Поиск в каталоге

**Файл**: `functions.php:466-491`

**SQL запрос**:

```php
add_filter('posts_where', function ($where) use ($search_term) {
    global $wpdb;
    $like_term = $wpdb->esc_like($search_term);

    $where .= $wpdb->prepare(
        " AND (
            {$wpdb->posts}.post_title LIKE %s OR
            {$wpdb->posts}.post_title LIKE %s OR
            {$wpdb->posts}.post_title LIKE %s OR
            {$wpdb->posts}.post_title = %s
        ) ",
        $like_term . ' %',        // "стол что-то"
        '% ' . $like_term . ' %', // "что-то стол что-то"
        '% ' . $like_term,        // "что-то стол"
        $like_term                // точное "стол"
    );

    return $where;
}, 10);
```

**Варианты поиска**:

- Начинается с: `"стол%"`
- Содержит: `"%стол%"`
- Заканчивается на: `"%стол"`
- Точное совпадение: `"стол"`

### Отображение результатов

**HTML структура**:

```html
<a class="search-modal-content-product" href="...">
  <div class="search-modal-content-product__img">
    <img src="..." alt="..." />
  </div>
  <div class="search-modal-content-product__info">
    <div class="cart-product__info-price__wrapper">
      <p class="cart-product__info-price">2 000 ₽</p>
      <p class="cart-product__info-old-price">2 500 ₽</p>
    </div>
    <p class="search-modal-content-product__title">Название товара</p>
  </div>
</a>
```

---

## 🛒 Корзина

### AJAX добавление в корзину

**Файл**: `archive-product.php:680-711`

```javascript
grid.addEventListener('click', async (e) => {
    const btn = e.target.closest('.product-item__btn-cart');
    if (!btn || btn.tagName === 'A') return;

    e.preventDefault();
    const pid = btn.getAttribute('data-product_id');

    btn.classList.add('is-loading');

    const form = new FormData();
    form.append('product_id', pid);
    form.append('quantity', 1);

    const res = await fetch(
        '<?= admin_url('admin-ajax.php?action=woocommerce_add_to_cart'); ?>',
        { method: 'POST', credentials: 'same-origin', body: form }
    );

    if (res.ok) {
        // Обновление фрагментов WooCommerce
        document.body.dispatchEvent(new Event('wc_fragment_refresh'));

        // Замена кнопки на ссылку "В корзине"
        const a = document.createElement('a');
        a.href = cartUrl;
        a.className = btn.className + ' is-in-cart';
        a.innerHTML = btn.innerHTML;
        btn.replaceWith(a);

        // Показ уведомления
        showToast(card);
    }

    btn.classList.remove('is-loading');
});
```

### Обновление количества

**Файл**: `functions.php:200-271`

**Процесс**:

1. Проверка nonce
2. Получение ключа товара и нового количества
3. Обновление через `WC()->cart->set_quantity()`
4. Пересчет итогов `WC()->cart->calculate_totals()`
5. Расчет скидок
6. Возврат обновленных данных

**Расчет скидки**:

```php
$regular_unit = (float) $product->get_regular_price();
$line_total = (float) $item['line_total'];
$regular_total = $regular_unit * $quantity;

$has_discount = $regular_total > $line_total && $regular_total > 0;
```

### Счетчик корзины

**Обновление в шапке**:

```javascript
jQuery(document.body).on("wc_fragments_refreshed", function () {
  const count = WC.cart.get_cart_contents_count();
  document.querySelector(".js-cart-count").textContent = count;
});
```

---

## ❤️ Wishlist и Compare

### YITH Wishlist

#### Добавление в избранное

**Стандартный механизм YITH**:

```html
<a href="?add_to_wishlist=123" class="add_to_wishlist" data-product-id="123">
  Добавить в избранное
</a>
```

#### Удаление из избранного

**Кастомный AJAX**:

```javascript
async function removeFromWishlist(productId) {
  const formData = new FormData();
  formData.append("action", "force_remove_from_wishlist");
  formData.append("product_id", productId);

  const response = await fetch(ajaxUrl, {
    method: "POST",
    body: formData,
  });

  const data = await response.json();

  if (data.success && data.data.removed) {
    updateWishlistCount(data.data.count);
    removeProductCard(productId);
  }
}
```

#### Получение счетчика

```javascript
async function getWishlistCount() {
  const response = await fetch(ajaxUrl + "?action=get_wishlist_count", {
    credentials: "same-origin",
  });

  const data = await response.json();

  if (data.success) {
    updateBadge(data.data.count);
  }
}
```

### YITH Compare

#### Структура cookie

```javascript
// Формат: JSON массив или строка с разделителями
yith_woocompare_list = [123, 456, 789]
// или
yith_woocompare_list = 123%2C456%2C789
```

#### Парсинг cookie

```javascript
function getCompareIds() {
  const cookieStr = document.cookie
    .split("; ")
    .find((r) => r.startsWith("yith_woocompare_list="));

  if (!cookieStr) return [];

  let value = decodeURIComponent(cookieStr.split("=")[1] || "");

  try {
    if (value.startsWith("[") && value.endsWith("]")) {
      const parsed = JSON.parse(value);
      return Array.isArray(parsed) ? parsed.map(String) : [];
    }
  } catch (e) {
    return value.split(/[%2C,|]/).filter((v) => v && /^\d+$/.test(v));
  }

  return [];
}
```

#### Обновление счетчика

```javascript
function updateCompareCount() {
  const ids = getCompareIds();
  const count = ids.length;

  countBox.textContent = count;
  badge.classList.toggle("hidden", count === 0);
}

// Обновление при изменениях
jQuery(document).on(
  "yith_woocompare_added yith_woocompare_removed",
  updateCompareCount,
);

// Периодическая проверка
setInterval(updateCompareCount, 300);
```

---

## 🪟 Модальные окна

### Типы модальных окон

1. **Поиск** (`.search-modal`)
2. **Мобильное меню** (`.header-modal`)
3. **Фильтры** (`.filters-modal`)
4. **Формы обратной связи** (кастомные)

### Управление модальными окнами

```javascript
// Открытие
function openModal(modalSelector) {
  const modal = document.querySelector(modalSelector);
  modal.classList.add("active");
  document.body.classList.add("modal-open");
}

// Закрытие
function closeModal(modalSelector) {
  const modal = document.querySelector(modalSelector);
  modal.classList.remove("active");
  document.body.classList.remove("modal-open");
}

// Закрытие по клику вне области
modal.addEventListener("click", (e) => {
  if (e.target === modal) {
    closeModal(modalSelector);
  }
});

// Закрытие по Escape
document.addEventListener("keydown", (e) => {
  if (e.key === "Escape") {
    closeAllModals();
  }
});
```

### Валидация форм

```javascript
function validateForm(form) {
  const errors = [];

  // Проверка обязательных полей
  form.querySelectorAll("[required]").forEach((field) => {
    if (!field.value.trim()) {
      errors.push(field);
      field.classList.add("border-red-500");
    } else {
      field.classList.remove("border-red-500");
    }
  });

  // Проверка телефона
  const phone = form.querySelector('input[type="tel"]');
  if (phone && !validatePhone(phone.value)) {
    errors.push(phone);
    phone.classList.add("border-red-500");
  }

  // Проверка email
  const email = form.querySelector('input[type="email"]');
  if (email && !validateEmail(email.value)) {
    errors.push(email);
    email.classList.add("border-red-500");
  }

  return errors.length === 0;
}
```

---

## 💾 База данных

### Таблицы WooCommerce

#### wp_posts

```sql
-- Товары (post_type = 'product')
ID, post_title, post_content, post_status, post_type
```

#### wp_postmeta

```sql
-- Метаданные товаров
meta_key:
  _price              -- Цена
  _regular_price      -- Обычная цена
  _sale_price         -- Цена со скидкой
  _stock_status       -- Статус наличия (instock/outofstock)
  _wc_average_rating  -- Средний рейтинг
```

#### wp_term_taxonomy

```sql
-- Категории и атрибуты
taxonomy:
  product_cat         -- Категории товаров
  pa_cvet            -- Атрибут: Цвет
  pa_material        -- Атрибут: Материал
  pa_brend           -- Атрибут: Бренд
  pa_strana          -- Атрибут: Страна
```

#### wp_yith_wcwl

```sql
-- Избранное YITH
CREATE TABLE wp_yith_wcwl (
    ID int(11) NOT NULL AUTO_INCREMENT,
    prod_id int(11) NOT NULL,
    user_id int(11) NOT NULL,
    wishlist_id int(11) DEFAULT NULL,
    dateadded timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (ID),
    KEY prod_id (prod_id),
    KEY user_id (user_id)
);
```

### Запросы

#### Получение товаров с фильтрацией

```php
$args = [
    'post_type' => 'product',
    'post_status' => 'publish',
    'posts_per_page' => 12,
    'paged' => 1,
    'tax_query' => [
        'relation' => 'AND',
        [
            'taxonomy' => 'product_cat',
            'field' => 'term_id',
            'terms' => 123
        ],
        [
            'taxonomy' => 'pa_cvet',
            'field' => 'slug',
            'terms' => ['krasnyj', 'sinij']
        ]
    ],
    'meta_query' => [
        'relation' => 'AND',
        [
            'key' => '_price',
            'value' => [1000, 5000],
            'compare' => 'BETWEEN',
            'type' => 'DECIMAL'
        ],
        [
            'key' => '_stock_status',
            'value' => 'instock'
        ]
    ]
];

$query = new WP_Query($args);
```

#### Получение счетчика wishlist

```sql
SELECT COUNT(*)
FROM wp_yith_wcwl
WHERE user_id = %d
```

#### Удаление из wishlist

```sql
DELETE FROM wp_yith_wcwl
WHERE prod_id = %d AND user_id = %d
```

---

## 🎣 Хуки и фильтры

### Actions

#### wp_enqueue_scripts

```php
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script('ajax-cart', ...);
    wp_enqueue_script('theme-search', ...);
    wp_enqueue_script('modal-validation', ...);
});
```

#### after_setup_theme

```php
add_action('after_setup_theme', function() {
    register_nav_menus([
        'header' => 'Меню в шапке',
        'footer-1' => 'Меню в футере-1',
        // ...
    ]);
});
```

#### pre_get_posts

```php
add_action('pre_get_posts', function($query) {
    // Модификация запросов товаров
    // Фильтрация, поиск
}, 11);
```

#### wp_footer

```php
add_action('wp_footer', function() {
    // Подключение infinite-scroll.js
    // Отладочные скрипты
}, 999);
```

### Filters

#### woocommerce_checkout_fields

```php
add_filter('woocommerce_checkout_fields', function($fields) {
    // Делаем поля необязательными
    $fields['billing']['billing_last_name']['required'] = false;
    $fields['billing']['billing_email']['required'] = false;
    return $fields;
});
```

#### upload_mimes

```php
add_filter('upload_mimes', function($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    $mimes['ico'] = 'image/vnd.microsoft.icon';
    return $mimes;
});
```

#### yith_wcwl_localize_script

```php
add_filter('yith_wcwl_localize_script', function($localize) {
    $localize['is_wishlist_responsive'] = false;
    return $localize;
});
```

#### posts_where

```php
add_filter('posts_where', function($where) use ($search_term) {
    global $wpdb;
    // Кастомный поиск по post_title
    return $where;
}, 10);
```

### Custom AJAX Actions

```php
// Фильтрация товаров
add_action('wp_ajax_filter_products', 's140_ajax_filter_products');
add_action('wp_ajax_nopriv_filter_products', 's140_ajax_filter_products');

// Infinite scroll
add_action('wp_ajax_load_more_products', 's140_ajax_load_more_products');
add_action('wp_ajax_nopriv_load_more_products', 's140_ajax_load_more_products');

// Живой поиск
add_action('wp_ajax_theme_live_search', 'theme_live_search');
add_action('wp_ajax_nopriv_theme_live_search', 'theme_live_search');

// Обновление корзины
add_action('wp_ajax_update_cart_item_custom', 'theme_update_cart_item_custom');
add_action('wp_ajax_nopriv_update_cart_item_custom', 'theme_update_cart_item_custom');

// Wishlist
add_action('wp_ajax_force_remove_from_wishlist', 'custom_force_remove_from_wishlist');
add_action('wp_ajax_nopriv_force_remove_from_wishlist', 'custom_force_remove_from_wishlist');
add_action('wp_ajax_get_wishlist_count', 'custom_get_wishlist_count');
add_action('wp_ajax_nopriv_get_wishlist_count', 'custom_get_wishlist_count');

// Отзывы
add_action('wp_ajax_submit_review', 'submit_review_callback');
add_action('wp_ajax_nopriv_submit_review', 'submit_review_callback');
```

---

## 🔧 Вспомогательные функции

### s140_attr_tax_slug_by_label()

```php
/**
 * Получить slug таксономии атрибута по label
 *
 * @param string $label Название атрибута (например, "цвет")
 * @return string Slug таксономии (например, "pa_cvet")
 */
function s140_attr_tax_slug_by_label($label) {
    $label = mb_strtolower(trim($label), 'UTF-8');

    foreach (wc_get_attribute_taxonomies() as $a) {
        $attr_label = mb_strtolower($a->attribute_label, 'UTF-8');
        if ($attr_label === $label) {
            return 'pa_' . $a->attribute_name;
        }
    }

    return '';
}
```

### strike_in_cart()

```php
/**
 * Проверка наличия товара в корзине
 *
 * @param int $pid ID товара
 * @return bool
 */
function strike_in_cart($pid) {
    if (!function_exists('WC')) return false;

    $cart = WC()->cart->get_cart();
    if (empty($cart)) return false;

    foreach ($cart as $item) {
        if ((int)($item['product_id'] ?? 0) === (int)$pid) {
            return true;
        }
    }

    return false;
}
```

### strike_first_attributes()

```php
/**
 * Получение первых N атрибутов товара
 *
 * @param WC_Product $prod Объект товара
 * @param int $limit Количество атрибутов
 * @return array [['label' => '...', 'value' => '...'], ...]
 */
function strike_first_attributes($prod, $limit = 3) {
    $out = [];

    foreach ($prod->get_attributes() as $attr) {
        if (count($out) >= $limit) break;

        if ($attr->is_taxonomy()) {
            $name = wc_attribute_label($attr->get_name());
            $terms = wc_get_product_terms(
                $prod->get_id(),
                $attr->get_name(),
                ['fields' => 'names']
            );
            if ($terms) {
                $out[] = [
                    'label' => $name,
                    'value' => implode(', ', array_slice($terms, 0, 3))
                ];
            }
        }
    }

    return array_slice($out, 0, $limit);
}
```

---

## 📊 Производительность

### Оптимизации запросов

1. **Использование prepared statements**:

```php
$wpdb->prepare("SELECT * FROM table WHERE id = %d", $id);
```

2. **Кэширование результатов**:

```php
$cached = wp_cache_get('key', 'group');
if ($cached === false) {
    $result = expensive_query();
    wp_cache_set('key', $result, 'group', 3600);
}
```

3. **Лимиты на количество результатов**:

```php
'posts_per_page' => 12,  // Не загружаем все товары сразу
```

4. **Индексы в БД**:

```sql
KEY prod_id (prod_id)
KEY user_id (user_id)
```

### Минификация

- CSS файлы минифицированы (`.min.css`)
- JS файлы минифицированы (`.min.js`)
- SVG спрайты вместо отдельных иконок

### Lazy Loading

```html
<img src="..." loading="lazy" alt="..." />
```

---

## 🔒 Безопасность

### Nonce проверки

```php
check_ajax_referer('theme_search_nonce', 'nonce');
```

### Санитизация

```php
$term = sanitize_text_field(wp_unslash($_POST['term']));
$email = sanitize_email($_POST['email']);
$comment = sanitize_textarea_field($_POST['comment']);
```

### Экранирование

```php
echo esc_html($title);
echo esc_url($link);
echo esc_attr($attribute);
echo wp_kses_post($content);
```

### Prepared Statements

```php
$wpdb->prepare("SELECT * FROM table WHERE id = %d", $id);
```

### Проверка прав

```php
if (!current_user_can('edit_posts')) {
    wp_die('Access denied');
}
```

---

**Дата создания**: 2026-02-13  
**Версия документации**: 1.0
