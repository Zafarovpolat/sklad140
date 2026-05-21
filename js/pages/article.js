let newsSwiper = new Swiper('.article-recommended-swiper', {
	slidesPerView: 1,
	spaceBetween: 12,
	navigation: {
		nextEl: '.article-recommended-swiper .swiper-pagination__wrapper',
	},
	scrollbar: {
		el: '.article-recommended-swiper .swiper-scrollbar',
		draggable: true,
		snapOnRelease: true,
	},
	breakpoints: {
		1050: {
			slidesPerView: '4',
		},
		800: {
			slidesPerView: '3',
		},
		600: {
			slidesPerView: '2',
		},
	},
})

const fixArticleRecommendedSwiper = () => {
	const containerRect = document.querySelector('.article-section .container').getBoundingClientRect()
	const swiper = document.querySelector('.article-recommended-swiper.swiper')

	const value = `${containerRect.width - 40}px`

	swiper.style.width = value
}

window.addEventListener('resize', fixArticleRecommendedSwiper)
fixArticleRecommendedSwiper()
