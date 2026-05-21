<?php
/**
 * Empty cart page
 */
defined( 'ABSPATH' ) || exit;
?>

<style>
.cart-empty-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 60vh;
    padding: 40px 20px;
}

.cart-empty-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    max-width: 480px;
}

.cart-empty-icon {
    width: 120px;
    height: 120px;
    color: #B6C1DD;
    margin-bottom: 32px;
}

.cart-empty-title {
    font-family: 'Onest', sans-serif;
    font-weight: 600;
    font-size: 28px;
    line-height: 120%;
    color: #031343;
    margin: 0 0 12px 0;
}

.cart-empty-text {
    font-family: 'Onest', sans-serif;
    font-weight: 400;
    font-size: 16px;
    line-height: 150%;
    color: #7A85A0;
    margin: 0 0 32px 0;
}

.cart-empty-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 16px 48px;
    background: #258FFB;
    color: #fff;
    border: none;
    border-radius: 12px;
    font-family: 'Onest', sans-serif;
    font-weight: 600;
    font-size: 16px;
    line-height: 120%;
    text-decoration: none;
    cursor: pointer;
    transition: background 0.3s ease;
}

.cart-empty-button:hover {
    background: #1a6dc4;
    color: #fff;
    text-decoration: none;
}

.cart-empty-button:active {
    background: #155ba3;
    transform: scale(0.98);
}

/* Мобильные */
@media (max-width: 768px) {
    .cart-empty-wrapper {
        min-height: 50vh;
        padding: 32px 16px;
    }

    .cart-empty-icon {
        width: 80px;
        height: 80px;
        margin-bottom: 24px;
    }

    .cart-empty-title {
        font-size: 22px;
        margin-bottom: 8px;
    }

    .cart-empty-text {
        font-size: 14px;
        margin-bottom: 24px;
    }

    .cart-empty-button {
        width: 100%;
        padding: 14px 32px;
        font-size: 15px;
    }
}
</style>

<div class="cart-empty-wrapper">
    <div class="cart-empty-content">
        <svg class="cart-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="9" cy="21" r="1"/>
            <circle cx="20" cy="21" r="1"/>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
        </svg>

        <h2 class="cart-empty-title">Корзина пуста</h2>
        <p class="cart-empty-text">Добавьте товары из каталога, чтобы оформить заказ</p>

        <?php if ( wc_get_page_id( 'shop' ) > 0 ) : ?>
            <a href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>" class="cart-empty-button">
                Перейти к товарам
            </a>
        <?php endif; ?>
    </div>
</div>