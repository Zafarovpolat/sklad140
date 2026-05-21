document.addEventListener('DOMContentLoaded', () => {

  const cartCountEl = document.querySelector('.js-cart-count');
  const cartCountWrap = document.querySelector('.header-cart__count');
  if (!cartCountEl) return;

  const refreshCartCount = () => {
    fetch(ajaxCart.ajaxurl + '?action=update_cart_count', {
      credentials: 'same-origin'
    })
      .then(res => res.json())
      .then(data => {
        if (data?.count !== undefined) {
          cartCountEl.textContent = data.count;
          cartCountWrap.style.display = data.count > 0 ? 'flex' : 'flex';
        }
      });
  };

  document.body.addEventListener('click', (e) => {
    const addBtn = e.target.closest('.add_to_cart_button');
    if (addBtn) {
      setTimeout(refreshCartCount, 500);
      setTimeout(refreshCartCount, 1500);
    }
  });

  document.body.addEventListener('click', (e) => {
    if (e.target.closest('.cart-product__remove')) {
      setTimeout(refreshCartCount, 400);
      setTimeout(refreshCartCount, 1200);
    }
  });

  document.body.addEventListener('click', (e) => {
    if (e.target.closest('#cart-product__counter--prev')) {
      setTimeout(refreshCartCount, 400);
      setTimeout(refreshCartCount, 1200);
    }
  });

  document.body.addEventListener('click', (e) => {
    if (e.target.closest('#cart-product__counter--next')) {
      setTimeout(refreshCartCount, 400);
      setTimeout(refreshCartCount, 1200);
    }
  });

  refreshCartCount();
});
