// Tabs
const tabs = document.querySelectorAll('.catalog-mobile__category')
const contents = document.querySelectorAll('.catalog-mobile__subcategories')

tabs.forEach(tab =>
	tab.addEventListener('click', () => {
		const currentDataTab = tab.getAttribute('data-tab')

		tabs.forEach(tab => tab.classList.remove('catalog-mobile__category--active'))
		tab.classList.add('catalog-mobile__category--active')

		contents.forEach(content => {
			content.classList.remove('catalog-mobile__subcategories--active')

			if (content.getAttribute('data-content') === currentDataTab) {
				content.classList.add('catalog-mobile__subcategories--active')
			}
		})
	})
)

// Max height for categories
const catalogMobileCategories = document.querySelector('.catalog-mobile__categories')

window.addEventListener('resize', () => {
	catalogMobileCategories.style.maxHeight = `${
		window.screen.height -
		document.getElementById('header').getBoundingClientRect().height -
		document.querySelector('.bottom-bar').getBoundingClientRect().height -
		45 -
		20 -
		20
	}px`
})

catalogMobileCategories.style.maxHeight = `${
	window.screen.height -
	document.getElementById('header').getBoundingClientRect().height -
	document.querySelector('.bottom-bar').getBoundingClientRect().height -
	45 -
	20 -
	20
}px`
