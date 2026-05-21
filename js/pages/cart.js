const products = document.querySelector('.cart-right__products')
const productTemplate = document.querySelector('#product-template')

/*for (let i = 0; i < 3; i++) {
	const clone = productTemplate.content.cloneNode(true)
	if (i === 0) {
		clone.querySelector('.cart-product__info-price').classList.add('cart-product__info-price--discount')
		clone.querySelector('.cart-product__remove').classList.add('cart-product__remove--disabled')
	}

	products.appendChild(clone)
}*/

window.addEventListener('resize', () => {
	moveBlock('.cart-left > h2', '.cart__inner', '.cart-left', 1024)
})

moveBlock('.cart-left > h2', '.cart__inner', '.cart-left', 1024)

const cartRight = document.querySelector('.cart-right')
const cartRightHeaderArrow = document.querySelector('.cart-right-header__arrow')

cartRightHeaderArrow.addEventListener('click', () => {
	cartRight.classList.toggle('cart-right--open')
})

// Tabs
const tabs = document.querySelectorAll('.cart-left-receipt__tab')
const contents = document.querySelectorAll('.cart-left-receipt__address')

tabs.forEach(tab =>
	tab.addEventListener('click', () => {
		const currentDataTab = tab.getAttribute('data-tab')

		tabs.forEach(tab => tab.classList.remove('cart-left-receipt__tab--active'))
		tab.classList.add('cart-left-receipt__tab--active')

		contents.forEach(content => {
			content.classList.remove('cart-left-receipt__address--active')

			if (content.getAttribute('data-content') === currentDataTab) {
				content.classList.add('cart-left-receipt__address--active')
			}
		})
	})
)
