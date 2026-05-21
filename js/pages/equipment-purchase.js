let buyingOutSwiper = new Swiper(".buying-out-swiper", {
	slidesPerView: 1,
	spaceBetween: 12,
	autoplay: {
		delay: 3000,
		disableOnInteraction: false,
		pauseOnMouseEnter: true
	},
	speed: 800,
	scrollbar: {
		el: ".buying-out-swiper-scrollbar",
		draggable: !0,
		snapOnRelease: !0
	}
}),
	plusesSwiper = new Swiper(".pluses-swiper", {
		slidesPerView: 1,
		spaceBetween: 12,
		autoplay: {
			delay: 3000,
			disableOnInteraction: false,
			pauseOnMouseEnter: true
		},
		speed: 800,
		scrollbar: {
			el: ".pluses-swiper-scrollbar",
			draggable: !0,
			snapOnRelease: !0
		}
	}),
	projectsSwiper = new Swiper(".projects-swiper", {
		slidesPerView: 1,
		spaceBetween: 12,
		autoplay: {
			delay: 3000,
			disableOnInteraction: false,
			pauseOnMouseEnter: true
		},
		speed: 800,
		navigation: {
			prevEl: ".swiper-button-projects--prev",
			nextEl: ".swiper-button-projects--next"
		},
		scrollbar: {
			el: ".projects-swiper-scrollbar",
			draggable: !0,
			snapOnRelease: !0
		},
		breakpoints: {
			900: {
				slidesPerView: 2
			}
		}
	});