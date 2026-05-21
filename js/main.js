const moveBlock = (elem, to, from, breakpoint) => {
	const block = document.querySelector(elem)
	const target = document.querySelector(to)
	const originalParent = document.querySelector(from)

	if (!block || !target || !originalParent) return;

	if (window.innerWidth <= breakpoint) {
		if (!block.parentNode.isSameNode(target)) {
			target.appendChild(block)
		}
	} else {
		if (!block.parentNode.isSameNode(originalParent)) {
			originalParent.appendChild(block)
		}
	}
}

const isClickOutside = (element, event) => element && !element.contains(event.target)

// Button
document.addEventListener('click', e => {
	const buttonSmall = e.target.closest('.button--small')
	if (buttonSmall) {
		buttonSmall.classList.toggle('button--active')
	}
})

// Header modal
const headerModal = document.querySelector('.header-modal')
const headerMenuBtn = document.querySelector('.header-mobile-links__link--menu')
const headerModalCross = document.querySelector('.header-modal__cross')
const darken = document.querySelector('.darken')

if (headerMenuBtn && headerModal && darken) {
	headerMenuBtn.addEventListener('click', event => {
		event.stopPropagation()
		headerModal.classList.add('header-modal--active')
		darken.classList.add('darken--active')
	})
}

if (headerModalCross && headerModal && darken) {
	headerModalCross.addEventListener('click', () => {
		headerModal.classList.remove('header-modal--active')
		darken.classList.remove('darken--active')
	})
}

window.addEventListener('resize', () => {
	moveBlock('.footer-newsletter', '.footer-bottom__inner', '.footer-right', 768)
	moveBlock('.footer .footer-marketplaces', '.footer-bottom__inner', '.footer-right', 768)
})

// Footer
moveBlock('.footer-newsletter', '.footer-bottom__inner', '.footer-right', 768)
moveBlock('.footer .footer-marketplaces', '.footer-bottom__inner', '.footer-right', 768)

// Product cart
document.addEventListener('click', e => {
	const productCartBtn = e.target.closest('.product-item__btn-cart')
	const productItemAddedToCart = e.target.closest('.product-item')?.querySelector('.product-item__added-to-cart')
	const productItemAddedToCartCross = e.target.closest('.product-item__added-to-cart__cross')

	document.querySelectorAll('.product-item__added-to-cart--active').forEach(element => {
		if (isClickOutside(element, e)) {
			element.classList.remove('product-item__added-to-cart--active')
		}
	})

	if (productCartBtn && productItemAddedToCart) {
		productCartBtn.classList.toggle('product-item__btn-cart--added')

		if (productCartBtn.classList.contains('product-item__btn-cart--added')) {
			productItemAddedToCart.classList.add('product-item__added-to-cart--active')
		}
		return
	}

	if (productItemAddedToCartCross && productItemAddedToCart) {
		productItemAddedToCart.classList.remove('product-item__added-to-cart--active')
	}
})

// Modal buy now
document.addEventListener('click', e => {
	const buyNowBtn = e.target.closest('.product-item__buy-now')
	const modalBuyNow = document.querySelector('.modal--one-click-buy')

	// ✅ ИСПРАВЛЕНО: триггер «Перезвонить мне» / «Связаться с нами»
	// — это и текст в шапке (.callme-back__text), и кнопки button.callme-back / a.callme-back
	// на страницах /about/, /vikup/, /clients/ и т.п.
	const callMeBackBtn = e.target.closest(
		'.callme-back__text, button.callme-back, a.callme-back, .js-callme-back'
	)
	const modalCallMeBack = document.querySelector('.modal--callme-back')

	const respondBtn = e.target.closest('.respond')
	const modalRespond = document.querySelector('.modal--respond')
	const analogBtn = e.target.closest('.product-item__btns--out-of-stock .button--dark')
	const modalChooseExact = document.querySelector('.modal--choose-exact')

	// ✅ ОБРАБОТКА "Купить сейчас"
	if (buyNowBtn && modalBuyNow && darken) {
		const productCard = buyNowBtn.closest('.product-item')
		if (productCard) {
			fillModalWithProduct(modalBuyNow, productCard)
		}
		modalBuyNow.classList.add('modal--active')
		darken.classList.add('darken--active')
		return
	} else if (modalBuyNow && modalBuyNow.classList.contains('modal--active') && isClickOutside(modalBuyNow, e)) {
		modalBuyNow.classList.remove('modal--active')
		if (darken) darken.classList.remove('darken--active')
		return
	}

	// ✅ ОБРАБОТКА "Подобрать аналог"
	if (analogBtn && modalChooseExact && darken) {
		const productCard = analogBtn.closest('.product-item')
		if (productCard) {
			fillModalWithProduct(modalChooseExact, productCard)
		}
		modalChooseExact.classList.add('modal--active')
		darken.classList.add('darken--active')
		return
	} else if (modalChooseExact && modalChooseExact.classList.contains('modal--active') && isClickOutside(modalChooseExact, e)) {
		modalChooseExact.classList.remove('modal--active')
		if (darken) darken.classList.remove('darken--active')
		return
	}

	// ✅ ОБРАБОТКА "Перезвонить мне" (теперь только при клике на текст)
	if (callMeBackBtn && modalCallMeBack && darken) {
		e.preventDefault() // ✅ Добавлено: отменяем переход по ссылке #
		modalCallMeBack.classList.add('modal--active')
		darken.classList.add('darken--active')
		return
	} else if (modalCallMeBack && modalCallMeBack.classList.contains('modal--active') && isClickOutside(modalCallMeBack, e)) {
		modalCallMeBack.classList.remove('modal--active')
		if (darken) darken.classList.remove('darken--active')
		return
	}

	if (respondBtn && modalRespond && darken) {
		modalRespond.classList.add('modal--active')
		darken.classList.add('darken--active')
		return
	} else if (modalRespond && modalRespond.classList.contains('modal--active') && isClickOutside(modalRespond, e)) {
		modalRespond.classList.remove('modal--active')
		if (darken) darken.classList.remove('darken--active')
		return
	}
})

// ✅ ФУНКЦИЯ: Заполнение модалки данными товара
function fillModalWithProduct(modal, productCard) {
	const productId = productCard.getAttribute('data-product-id')
	const productTitle = productCard.querySelector('.product-item__title')?.textContent?.trim() || 'Товар'
	const productImage = productCard.querySelector('.product-item__img img')?.src || ''
	const productLink = productCard.querySelector('.product-item__title')?.href || '#'

	// ✅ Получаем элемент цены из карточки товара
	const priceElement = productCard.querySelector('.product-item__price')

	// ✅ Проверяем есть ли скидка (наличие тега <del> или <ins>)
	const hasDiscount = priceElement?.querySelector('del') !== null || priceElement?.querySelector('ins') !== null

	// ✅ Получаем только АКТУАЛЬНУЮ цену (без старой)
	let currentPrice = ''
	if (hasDiscount) {
		// Если есть скидка - берём цену из <ins> (новая цена)
		const insElement = priceElement?.querySelector('ins')
		if (insElement) {
			currentPrice = insElement.innerHTML
		}
	} else {
		// Если скидки нет - берём всё содержимое
		currentPrice = priceElement?.innerHTML || ''
	}

	// ✅ Получаем старую цену (если есть)
	const oldPriceElement = priceElement?.querySelector('del')
	const oldPrice = oldPriceElement ? oldPriceElement.innerHTML : ''

	const modalProduct = modal.querySelector('.modal-content__product')
	if (!modalProduct) return

	// Заполняем изображение
	const modalImg = modalProduct.querySelector('.cart-product__img img')
	if (modalImg) {
		modalImg.src = productImage
		modalImg.alt = productTitle
	}

	// Заполняем название
	const modalTitle = modalProduct.querySelector('.cart-product__info-title')
	if (modalTitle) {
		modalTitle.innerHTML = `<a href="${productLink}" style="color: inherit; text-decoration: none;">${productTitle}</a>`
	}

	// ✅ Заполняем ТОЛЬКО актуальную цену
	const modalPrice = modalProduct.querySelector('.cart-product__info-price') ||
		modalProduct.querySelector('.cart-product__info-price--default')
	if (modalPrice) {
		modalPrice.innerHTML = currentPrice
	}

	// ✅ Обрабатываем старую цену (показываем/скрываем)
	const modalOldPrice = modalProduct.querySelector('.cart-product__info-old-price')
	if (modalOldPrice) {
		if (hasDiscount && oldPrice) {
			// Есть скидка - показываем старую цену
			modalOldPrice.innerHTML = oldPrice
			modalOldPrice.style.display = ''
		} else {
			// Нет скидки - скрываем
			modalOldPrice.innerHTML = ''
			modalOldPrice.style.display = 'none'
		}
	}

	// Сохраняем ID товара в форме модалки
	const form = modal.querySelector('.modal-content-form')
	if (form) {
		const oldInput = form.querySelector('input[name="product_id"]')
		if (oldInput) oldInput.remove()

		const input = document.createElement('input')
		input.type = 'hidden'
		input.name = 'product_id'
		input.value = productId
		form.appendChild(input)
	}

	console.log('✅ Модалка заполнена:', { productId, productTitle, hasDiscount, currentPrice: currentPrice.substring(0, 50) })
}

// Закрытие success модалок
document.addEventListener('click', (e) => {
	const darken = document.querySelector('.darken')
	if (!darken) return

	const successModals = [
		'.modal--one-click-buy-success',
		'.modal--choose-exact-success',
		'.modal--callme-back-success'
	]

	// Закрытие по клику вне модалки
	successModals.forEach(selector => {
		const modal = document.querySelector(selector)
		if (modal && modal.classList.contains('modal--active') && isClickOutside(modal, e)) {
			modal.classList.remove('modal--active')
			darken.classList.remove('darken--active')
		}
	})

	// Закрытие по кнопке "Вернуться в каталог"
	const closeBtn = e.target.closest('.modal-success-close')
	if (closeBtn) {
		successModals.forEach(selector => {
			const modal = document.querySelector(selector)
			if (modal && modal.classList.contains('modal--active')) {
				modal.classList.remove('modal--active')
				darken.classList.remove('darken--active')
			}
		})
	}
})

// Cart products counter
const checkCartProductCounterValue = (e, action) => {
	const cartProduct = e.target.closest('.cart-product')
	if (!cartProduct) return;

	const prev = cartProduct.querySelector('#cart-product__counter--prev')
	const counter = cartProduct.querySelector('.cart-product__counter-value')
	if (!prev || !counter) return;

	const value = +counter.innerHTML

	if (action === 'decrement') {
		if (value - 1 <= 1) {
			prev.classList.add('cart-product__counter-btn--disabled')
			counter.textContent = 1
		} else {
			prev.classList.remove('cart-product__counter-btn--disabled')
			counter.textContent = value - 1
		}
	} else {
		counter.textContent = value + 1
		prev.classList.remove('cart-product__counter-btn--disabled')
	}
}

document.addEventListener('click', e => {
	const cartProductCounterPrev = e.target.closest('#cart-product__counter--prev')
	const cartProductCounterNext = e.target.closest('#cart-product__counter--next')

	if (cartProductCounterPrev) {
		checkCartProductCounterValue(e, 'decrement')
	} else if (cartProductCounterNext) {
		checkCartProductCounterValue(e, 'increment')
	}
})

// Search modal
class SearchModal {
	constructor() {
		this.selectors = {
			searchModal: '.search-modal',
			headerInputSearch: '#search',
			modalInput: '#modal-search-input',
			showButton: '.search-modal__show',
			tubsContainer: '.search-modal__tubs',
			cancelButton: '.search-modal__cancel',
			clearHistory: '.search-modal-content--history .search-modal__clear',
			historyProducts: '.search-modal-content--history .search-modal-content__products',
			recentCross: '.search-modal__recent-cross',
			recentItems: '.search-modal__recent-items',
			recentWrapper: '.search-modal__recent-wrapper',
			clearResults: '.search-modal-content--results .search-modal__clear',
			resultsProducts: '.search-modal-content--results .search-modal-content__products',
			darken: '.darken',
		}

		this.elements = {}
		this.init()
	}

	init() {
		this.cacheElements()
		this.bindEvents()
	}

	cacheElements() {
		Object.entries(this.selectors).forEach(([key, selector]) => {
			this.elements[key] = document.querySelector(selector)
		})
		this.elements.headerInputSearch = document.getElementById('search')
		this.elements.modalInput = document.getElementById('search')
	}

	bindEvents() {
		const {
			headerInputSearch,
			searchModal,
			modalInput,
			showButton,
			cancelButton,
			clearHistory,
			recentCross,
			clearResults,
		} = this.elements

		headerInputSearch?.addEventListener('click', this.openModal.bind(this))
		document.addEventListener('click', this.handleOutsideClick.bind(this))
		document.addEventListener('click', this.handleContentClick.bind(this))
		showButton?.addEventListener('click', this.toggleTubsVisibility.bind(this))
		cancelButton?.addEventListener('click', this.clearInput.bind(this))
		clearHistory?.addEventListener('click', this.clearHistory.bind(this))
		recentCross?.addEventListener('click', this.clearRecent.bind(this))
		modalInput?.addEventListener('input', this.handleInput.bind(this))
		clearResults?.addEventListener('click', this.clearResults.bind(this))
	}

	openModal(e) {
		e.preventDefault()
		e.stopPropagation()

		this.toggleModal(true)
		// Фокусимся на инпуте ПОСЛЕ открытия модалки
		setTimeout(() => {
			this.elements.headerInputSearch?.focus()
		}, 100)
	}

	closeModal() {
		this.toggleModal(false)
	}

	// ✅ ИСПРАВЛЕНО: Добавлена блокировка скролла
	toggleModal(show) {
		const { searchModal, darken } = this.elements
		const method = show ? 'add' : 'remove'

		searchModal?.classList[method]('search-modal--visible')
		darken?.classList[method]('darken--active')

		// ✅ Блокировка/разблокировка скролла
		if (show) {
			// Запоминаем текущую позицию скролла
			this.scrollPosition = window.pageYOffset || document.documentElement.scrollTop

			// Блокируем скролл
			document.body.style.overflow = 'hidden'
			document.body.style.position = 'fixed'
			document.body.style.top = `-${this.scrollPosition}px`
			document.body.style.width = '100%'
		} else {
			// Разблокируем скролл
			document.body.style.overflow = ''
			document.body.style.position = ''
			document.body.style.top = ''
			document.body.style.width = ''

			// Возвращаемся к сохраненной позиции скролла
			window.scrollTo(0, this.scrollPosition || 0)
		}
	}

	handleOutsideClick(e) {
		const { searchModal } = this.elements
		if (!searchModal) return;

		const isVisible = searchModal.classList.contains('search-modal--visible')
		const isOutside = this.isClickOutside(searchModal, e)

		if (isOutside && isVisible) {
			this.closeModal()
		}
	}

	handleContentClick(e) {
		const element =
			e.target.closest('.tub') ||
			e.target.closest('.search-modal__recent') ||
			e.target.closest('.search-modal-categories .button')
		const content = element?.innerText || element?.textContent

		if (
			e.target.closest('.search-modal-categories .button') &&
			!e.target.closest('.search-modal-categories .button')?.classList?.contains('button--active')
		) {
			this.setSearchValue('')
			const { modalInput } = this.elements
			modalInput?.blur()
			return
		}

		if (content) {
			this.setSearchValue(content)
		}
	}

	setSearchValue(value) {
		const { modalInput } = this.elements
		if (!modalInput) return

		modalInput.value = value
		modalInput.focus()
		modalInput.dispatchEvent(new Event('input'))
	}

	toggleTubsVisibility() {
		const { showButton, tubsContainer } = this.elements
		const tubs = tubsContainer?.querySelectorAll('.search-modal__tub')
		const isVisible = showButton?.classList.contains('search-modal__show--visible')

		tubs?.forEach(tub => {
			if (isVisible && tub.dataset.visibleBy === 'click') {
				tub.classList.remove('search-modal__tub--visible')
			} else if (!isVisible) {
				tub.classList.add('search-modal__tub--visible')
			}
		})

		showButton?.classList.toggle('search-modal__show--visible', !isVisible)
	}

	clearInput() {
		const { modalInput } = this.elements
		if (!modalInput) return

		modalInput.value = ''
		this.closeModal()
		modalInput.dispatchEvent(new Event('input'))
	}

	clearHistory() {
		const { historyProducts, clearHistory } = this.elements
		if (historyProducts) historyProducts.innerHTML = ''
		clearHistory?.classList.add('d-none')
	}

	clearRecent() {
		const { recentItems, recentWrapper } = this.elements
		if (recentItems) recentItems.innerHTML = ''
		recentWrapper?.classList.add('d-none')
	}

	handleInput(e) {
		const value = e.target.value.trim()
		const contents = document.querySelectorAll('.search-modal-content')

		contents.forEach(content => {
			const isResults = content.classList.contains('search-modal-content--results')
			const isHistory = content.classList.contains('search-modal-content--history')

			content.classList.toggle('search-modal-content--visible', value ? isResults : isHistory)
		})
	}

	clearResults() {
		const { resultsProducts, clearResults, modalInput } = this.elements
		if (resultsProducts) resultsResults.innerHTML = ''
		clearResults?.classList.add('d-none')

		if (modalInput) {
			modalInput.value = ''
			modalInput.dispatchEvent(new Event('input'))
		}
	}

	isClickOutside(element, event) {
		return element && !element.contains(event.target)
	}
}

// Инициализация модального окна поиска
new SearchModal()

// Cloning search-product-template
// const searchModalContentMobile = document.querySelector('.search-modal-content-mobile')
// const searchProductTemplate = document.querySelector('#search-product-template')

// if (searchModalContentMobile && searchProductTemplate) {
// 	for (let i = 0; i < 10; i++) {
// 		const clone = searchProductTemplate.content.cloneNode(true)
// 		searchModalContentMobile.appendChild(clone)
// 	}
// }

// Плавный скролл
var anchors = document.querySelectorAll('a[href^="#"]');
for (var i = 0; i < anchors.length; i++) {
	anchors[i].addEventListener('click', function (e) {
		e.preventDefault();
		var target = document.querySelector(this.getAttribute('href'));
		if (target) {
			target.scrollIntoView({
				behavior: 'smooth',
			});
		}
	});
}

// === АВТОПРОКРУТКА "ЧАСТО ИЩУТ" ===
(function () {
	const container = document.querySelector('.often-search__items');
	if (!container) return;

	let scrollSpeed = 1;
	let isHovered = false;
	let scrollPosition = 0;

	const items = container.innerHTML;
	container.innerHTML = items + items;

	function autoScroll() {
		if (!isHovered) {
			scrollPosition += scrollSpeed;
			if (scrollPosition >= container.scrollWidth / 2) {
				scrollPosition = 0;
			}
			container.scrollLeft = scrollPosition;
		}
		requestAnimationFrame(autoScroll);
	}

	container.addEventListener('mouseenter', () => { isHovered = true; });
	container.addEventListener('mouseleave', () => { isHovered = false; });

	autoScroll();
})();

// === КНОПКА "НАВЕРХ" ===
(function () {
	var toTopBtn = document.querySelector('.to-the-top');
	if (!toTopBtn) return;

	toTopBtn.style.transition = 'opacity 0.3s ease, visibility 0.3s ease';

	function getScrollTop() {
		return document.body.scrollTop || document.documentElement.scrollTop || window.pageYOffset || 0;
	}

	function nearBottomForm() {
		// П-4: если на мобиле виден футер или чекбокс согласия — прячем «Вверх»,
		// иначе он перекрывает поле на мобильных устройствах.
		if (window.innerWidth > 991) return false;
		var nodes = document.querySelectorAll('.footer, .other-questions-form, .modal--active .modal-content-form');
		for (var i = 0; i < nodes.length; i++) {
			var rect = nodes[i].getBoundingClientRect();
			if (rect.top < (window.innerHeight - 80)) return true;
		}
		return false;
	}

	function toggleButton() {
		var scrollTop = getScrollTop();
		if (scrollTop < 300 || nearBottomForm()) {
			toTopBtn.style.opacity = '0';
			toTopBtn.style.visibility = 'hidden';
			toTopBtn.style.pointerEvents = 'none';
		} else {
			toTopBtn.style.opacity = '1';
			toTopBtn.style.visibility = 'visible';
			toTopBtn.style.pointerEvents = '';
		}
	}

	toggleButton();
	document.addEventListener('scroll', toggleButton, true);
	window.addEventListener('resize', toggleButton);
})();

// === ФИЛЬТР ЦВЕТОВ ===
(function () {
	var colorInputs = document.querySelectorAll('.filter-section-color input[type="checkbox"]');

	for (var i = 0; i < colorInputs.length; i++) {
		colorInputs[i].addEventListener('click', function (e) {
			var circle = this.nextElementSibling;
			if (circle) {
				if (this.checked) {
					circle.classList.add('input-radio__circle--active');
				} else {
					circle.classList.remove('input-radio__circle--active');
				}
			}
		});
	}
})();

// === INFINITE SCROLL ===
// (function () {
// 	var container = document.querySelector('.catalog-products__items');
// 	var pagination = document.querySelector('.pagination');
// 	if (!container) return;

// 	if (pagination) pagination.style.display = 'none';

// 	var loading = false;
// 	var currentPage = 1;
// 	var termId = container.dataset.termId || 0;
// 	var maxPages = parseInt(container.dataset.maxPages) || 1;

// 	console.log('Infinite Scroll Init:', { termId: termId, maxPages: maxPages });

// 	var loader = document.createElement('div');
// 	loader.className = 'infinite-scroll-loader';
// 	loader.innerHTML = '<div style="text-align:center;padding:20px;"><svg width="40" height="40" viewBox="0 0 50 50"><circle cx="25" cy="25" r="20" fill="none" stroke="#2563eb" stroke-width="4" stroke-linecap="round" stroke-dasharray="31.4 31.4" transform="rotate(-90 25 25)"><animateTransform attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="1s" repeatCount="indefinite"/></circle></svg></div>';
// 	loader.style.display = 'none';

// 	if (container.parentNode) {
// 		container.parentNode.insertBefore(loader, container.nextSibling);
// 	}

// 	function loadMore() {
// 		if (loading) return;
// 		if (currentPage >= maxPages) {
// 			if (loader) {
// 				loader.innerHTML = '<p style="text-align:center;padding:20px;color:#666;">Все товары загружены</p>';
// 				loader.style.display = 'block';
// 			}
// 			return;
// 		}

// 		loading = true;
// 		if (loader) {
// 			loader.style.display = 'block';
// 			loader.innerHTML = '<div style="text-align:center;padding:20px;"><svg width="40" height="40" viewBox="0 0 50 50"><circle cx="25" cy="25" r="20" fill="none" stroke="#2563eb" stroke-width="4" stroke-linecap="round" stroke-dasharray="31.4 31.4" transform="rotate(-90 25 25)"><animateTransform attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="1s" repeatCount="indefinite"/></circle></svg></div>';
// 		}
// 		currentPage++;

// 		console.log('Loading page:', currentPage);

// 		var formData = new FormData();
// 		formData.append('action', 'load_more_products');
// 		formData.append('page', currentPage);
// 		formData.append('term_id', termId);

// 		var urlParams = new URLSearchParams(window.location.search);
// 		urlParams.forEach(function (value, key) {
// 			formData.append(key, value);
// 		});

// 		fetch('/wp-admin/admin-ajax.php', {
// 			method: 'POST',
// 			credentials: 'same-origin',
// 			body: formData
// 		})
// 			.then(function (response) { return response.json(); })
// 			.then(function (data) {
// 				console.log('AJAX Response:', data);

// 				if (data.success && data.data.html && data.data.html.trim() !== '') {
// 					container.insertAdjacentHTML('beforeend', data.data.html);
// 					maxPages = data.data.max_pages || maxPages;
// 				}

// 				loading = false;

// 				if (loader) {
// 					if (!data.data.has_more || currentPage >= maxPages) {
// 						loader.innerHTML = '<p style="text-align:center;padding:20px;color:#666;">Все товары загружены</p>';
// 						loader.style.display = 'block';
// 					} else {
// 						loader.style.display = 'none';
// 					}
// 				}
// 			})
// 			.catch(function (error) {
// 				console.error('AJAX Error:', error);
// 				loading = false;
// 				if (loader) loader.style.display = 'none';
// 			});
// 	}

// 	window.addEventListener('scroll', function () {
// 		var scrollTop = window.pageYOffset || document.documentElement.scrollTop || 0;
// 		var scrollHeight = Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);
// 		var clientHeight = window.innerHeight;

// 		if (scrollTop + clientHeight >= scrollHeight - 500) {
// 			loadMore();
// 		}
// 	});
// })();