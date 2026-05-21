// Hero slider
let swiper = new Swiper('.sklad-hero__swiper', {
	spaceBetween: 15,
	loop: true,
	autoplay: {
		delay: 5000,
		disableOnInteraction: false,
	},
	pagination: {
		el: '.sklad-hero__swiper .swiper-pagination',
	},
	breakpoints: {
		700: {
			navigation: {
				nextEl: '.swiper-button-next__main__hero',
				prevEl: '.swiper-button-prev__main__hero',
			},
			pagination: false,
		},
	},
})

let swiperSales = null;
let swiperHits = null;

function initProductSwipers() {
  if (swiperSales) {
    swiperSales.destroy(true, true);
    swiperSales = null;
  }
  if (swiperHits) {
    swiperHits.destroy(true, true);
    swiperHits = null;
  }

  const activeWrapper = document.querySelector('.product-swiper__wrapper--active');
  if (!activeWrapper) return;

  const swiperEl = activeWrapper.querySelector('.swiper');
  if (!swiperEl) return;

  const isHits = activeWrapper.classList.contains('product-swiper__wrapper--hit-of-sales');

  const scrollbarEl = activeWrapper.querySelector('.product-swiper-scrollbar');
  const paginationEl = activeWrapper.querySelector('.product-swiper-pagination');

  // Очищаем от предыдущей инициализации
  if (scrollbarEl) {
    scrollbarEl.innerHTML = '';
    scrollbarEl.removeAttribute('style');
  }
  if (paginationEl) {
    paginationEl.innerHTML = '';
    paginationEl.removeAttribute('style');
  }

  const swiperOptions = {
    slidesPerView: 2,
    spaceBetween: 8,
    loop: false,
    autoplay: {
      delay: 5000,
      disableOnInteraction: false,
      pauseOnMouseEnter: true,
    },
    speed: 400,
    grabCursor: true,
    touchRatio: 1,
    touchAngle: 45,
    resistance: true,
    resistanceRatio: 0.85,
    navigation: {
      nextEl: '.swiper-button-next__main__product',
      prevEl: '.swiper-button-prev__main__product',
    },
    breakpoints: {
      1320: { slidesPerView: 4, spaceBetween: 12 },
      1050: { slidesPerView: 3, spaceBetween: 10 },
      700: { slidesPerView: 2, spaceBetween: 8 },
    },
  };

  // ✅ Всегда подключаем scrollbar (draggable для десктопа)
  if (scrollbarEl) {
    swiperOptions.scrollbar = {
      el: scrollbarEl,
      draggable: true,
      hide: false,
      snapOnRelease: false,
    };
  }

  // ✅ Всегда подключаем pagination (progressbar для мобилки)
  if (paginationEl) {
    swiperOptions.pagination = {
      el: paginationEl,
      type: 'progressbar',
    };
  }

  const instance = new Swiper(swiperEl, swiperOptions);

  if (isHits) {
    swiperHits = instance;
  } else {
    swiperSales = instance;
  }

  requestAnimationFrame(() => {
    instance.update();
    if (instance.scrollbar && instance.scrollbar.updateSize) {
      instance.scrollbar.updateSize();
    }
    if (instance.pagination && instance.pagination.render) {
      instance.pagination.render();
      instance.pagination.update();
    }
    console.log('✅ Swiper:', isHits ? 'Хиты' : 'Распродажа');
    console.log('   scrollbar drag:', scrollbarEl ? !!scrollbarEl.querySelector('.swiper-scrollbar-drag') : false);
    console.log('   pagination fill:', paginationEl ? !!paginationEl.querySelector('.swiper-pagination-progressbar-fill') : false);
  });
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initProductSwipers);
} else {
  initProductSwipers();
}

// Переинициализация при смене десктоп/мобилка
let wasMobile = window.matchMedia('(max-width: 800px)').matches;

window.addEventListener('resize', () => {
  const nowMobile = window.matchMedia('(max-width: 800px)').matches;
  if (nowMobile !== wasMobile) {
    wasMobile = nowMobile;
    fixSaleProductsSwiper();
    setTimeout(initProductSwipers, 150);
  }
});


/**
 * Draggable pagination progressbar
 */
function initDraggablePagination() {
  // Убираем старые обработчики через клонирование
  document.querySelectorAll('.product-swiper-pagination').forEach(bar => {
    const clone = bar.cloneNode(true);
    bar.parentNode.replaceChild(clone, bar);
  });

  document.querySelectorAll('.product-swiper-pagination').forEach(bar => {
    let isDragging = false;

    function getSwiper() {
      // Находим swiper-инстанс через наши переменные
      const wrapper = bar.closest('.product-swiper__wrapper');
      if (!wrapper) return null;

      if (wrapper.classList.contains('product-swiper__wrapper--hit-of-sales')) {
        return swiperHits;
      }
      return swiperSales;
    }

    function handleDrag(clientX) {
      const swiper = getSwiper();
      if (!swiper) {
        console.log('❌ Swiper не найден');
        return;
      }

      const rect = bar.getBoundingClientRect();
      const progress = Math.max(0, Math.min(1, (clientX - rect.left) / rect.width));

      // Останавливаем autoplay пока тянем
      if (swiper.autoplay && swiper.autoplay.running) {
        swiper.autoplay.stop();
      }

      // Считаем к какому слайду перейти
      const totalSlides = swiper.slides.length;
      const slidesPerView = Math.round(swiper.params.slidesPerView) || 2;
      const maxIndex = Math.max(0, totalSlides - slidesPerView);
      const targetIndex = Math.round(progress * maxIndex);

      // Переходим к слайду без анимации
      swiper.slideTo(targetIndex, 0);

      console.log('🎯 drag progress:', progress.toFixed(2), 'slide:', targetIndex, '/', maxIndex);
    }

    function onStart(e) {
      isDragging = true;
      document.body.style.userSelect = 'none';

      const clientX = e.touches ? e.touches[0].clientX : e.clientX;
      handleDrag(clientX);

      e.preventDefault();
      e.stopPropagation();
    }

    function onMove(e) {
      if (!isDragging) return;

      const clientX = e.touches ? e.touches[0].clientX : e.clientX;
      handleDrag(clientX);

      e.preventDefault();
    }

    function onEnd() {
      if (!isDragging) return;
      isDragging = false;
      document.body.style.userSelect = '';

      // Возобновляем autoplay
      const swiper = getSwiper();
      if (swiper && swiper.autoplay) {
        swiper.autoplay.start();
      }
    }

    // Mouse
    bar.addEventListener('mousedown', onStart);
    document.addEventListener('mousemove', onMove);
    document.addEventListener('mouseup', onEnd);

    // Touch
    bar.addEventListener('touchstart', onStart, { passive: false });
    document.addEventListener('touchmove', onMove, { passive: false });
    document.addEventListener('touchend', onEnd);

    bar.style.cursor = 'grab';
    console.log('✅ Draggable pagination привязан к:', bar.className);
  });
}

// Вызываем после инициализации Swiper
const originalInit = initProductSwipers;
initProductSwipers = function() {
  originalInit();
  setTimeout(initDraggablePagination, 400);
};

let vacanciesSwiper = new Swiper('.vacancies-swiper', {
	slidesPerView: '1',
	spaceBetween: 12,
	autoplay: {
		delay: 5000,
		disableOnInteraction: false,
	},
	scrollbar: {
		el: '.vacancies-swiper .swiper-scrollbar',
		draggable: true,
		snapOnRelease: true,
	},
	breakpoints: {
		1050: {
			slidesPerView: '3',
		},
		700: {
			slidesPerView: '2',
		},
	},
})

let newsSwiper = new Swiper('.news-swiper', {
	slidesPerView: '1',
	spaceBetween: 17,
	autoplay: {
		delay: 5000,
		disableOnInteraction: false,
	},
	navigation: {
		nextEl: '.news-swiper .swiper-pagination__wrapper',
	},
	scrollbar: {
		el: '.news-swiper .swiper-scrollbar',
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

let brandsSwiper = new Swiper('.brands-swiper', {
	slidesPerView: 2,
	spaceBetween: 12,
	loop: true,
	autoplay: {
		delay: 3000,
		disableOnInteraction: false,
	},
	navigation: {
		prevEl: '.swiper-button-brands--prev',
		nextEl: '.swiper-button-brands--next',
	},
	scrollbar: {
		el: '.brands-swiper .swiper-scrollbar',
		draggable: true,
		snapOnRelease: true,
	},
	breakpoints: {
		1050: {
			slidesPerView: '6',
		},
		600: {
			slidesPerView: '4',
		},
	},
})

let brandsMobileSwiper = new Swiper('.brands-swiper--mobile', {
	slidesPerView: 2,
	spaceBetween: 12,
	scrollbar: {
		el: '.brands-swiper .swiper-scrollbar',
		draggable: true,
		snapOnRelease: true,
	},
})

let heroRight = new Swiper('.hero-right-swiper', {
	slidesPerView: '1',
	spaceBetween: 8,
	loop: true,
	autoplay: {
		delay: 3000,
		disableOnInteraction: false,
	},
	scrollbar: {
		el: '.hero-right-swiper-scrollbar',
		draggable: true,
		snapOnRelease: true,
	},
})

const fixSaleProductsSwiper = () => {
  const productSwiperStock = document.querySelector(
    '.product-swiper__wrapper--active .product-swiper__stock'
  );
  const productSwiperContainer = document.querySelector('.sale-products .container');

  // ✅ Ищем swiper ТОЛЬКО в активном wrapper
  const activeSwiper = document.querySelector('.product-swiper__wrapper--active .swiper');

  if (!productSwiperContainer || !activeSwiper) return;

  const containerWidth = productSwiperContainer.getBoundingClientRect().width;

  let stockWidth = 0;
  if (productSwiperStock && !window.matchMedia('(max-width: 800px)').matches) {
    stockWidth = productSwiperStock.getBoundingClientRect().width;
  }

  const value = `${containerWidth - stockWidth - 12 - 30}px`;
  activeSwiper.style.width = value;
};

window.addEventListener('resize', fixSaleProductsSwiper);
fixSaleProductsSwiper();

window.addEventListener('resize', fixSaleProductsSwiper);
fixSaleProductsSwiper();

// About more btn open / close
const aboutTextBtn = document.querySelector('.about-text__btn');
const aboutTextHolder = document.querySelector('.about-text__holder');

if (aboutTextBtn && aboutTextHolder) {
	aboutTextBtn.addEventListener('click', () => {
		aboutTextHolder.classList.toggle('about-text__holder--open');
	});
}

// About more accordion open / close
document.addEventListener('click', e => {
	if (e.target.closest('.about-text-accordion__arrow')) {
		e.target.closest('.about-text-accordion').classList.toggle('about-text-accordion--open');
	}
});

// ✅ Sale products tabs с переинициализацией Swiper
const saleProductsTabs = document.querySelectorAll('.sale-products__tab');
const saleProductsContents = [
  document.querySelector('.product-swiper__wrapper--sales'),
  document.querySelector('.product-swiper__wrapper--hit-of-sales'),
];

saleProductsTabs.forEach(tab =>
  tab.addEventListener('click', () => {
    const currentDataTab = tab.getAttribute('data-tab');

    // Переключаем активные классы на кнопках
    saleProductsTabs.forEach(t => t.classList.remove('sale-products__tab--active'));
    tab.classList.add('sale-products__tab--active');

    // Переключаем контент
    saleProductsContents.forEach(content => {
      if (!content) return;
      content.classList.remove('product-swiper__wrapper--active');

      if (content.getAttribute('data-content') === currentDataTab) {
        content.classList.add('product-swiper__wrapper--active');
      }
    });

    // ✅ Пересчитываем ширину ПЕРЕД инициализацией Swiper
    fixSaleProductsSwiper();

    // ✅ Переинициализируем Swiper после смены вкладки
    setTimeout(() => {
      initProductSwipers();
    }, 150);
  })
);

// Popular categories tabs
const popularCategoriesTabs = document.querySelectorAll('.popular-categories__tab');
const popularCategoriesContents = document.querySelectorAll('.popular-categories__content');

popularCategoriesTabs.forEach(tab =>
	tab.addEventListener('click', () => {
		const currentDataTab = tab.getAttribute('data-tab');

		popularCategoriesTabs.forEach(tab => tab.classList.remove('popular-categories__tab--active'));
		tab.classList.add('popular-categories__tab--active');

		popularCategoriesContents.forEach(content => {
			content.classList.remove('popular-categories__content--active');

			if (content.getAttribute('data-content') === currentDataTab) {
				content.classList.add('popular-categories__content--active');
			}
		});
	})
);

// ============================================
// DRAGGABLE PAGINATION — вставить в самый конец файла
// ============================================
document.addEventListener('DOMContentLoaded', function() {
  
  setTimeout(function() {
    
    var bars = document.querySelectorAll('.product-swiper-pagination');
    console.log('🔍 Найдено pagination баров:', bars.length);
    
    bars.forEach(function(bar, index) {
      var isDragging = false;
      
      console.log('✅ Привязываю drag к бару #' + index, bar);
      
      bar.style.cursor = 'grab';
      bar.style.touchAction = 'pan-x';
      bar.style.userSelect = 'none';
      bar.style.webkitUserSelect = 'none';
      
      function getSwiper() {
        if (swiperSales) return swiperSales;
        if (swiperHits) return swiperHits;
        return null;
      }
      
      function onDown(e) {
        isDragging = true;
        bar.style.cursor = 'grabbing';
        
        var swiper = getSwiper();
        if (swiper) {
          // Останавливаем autoplay и transitions
          if (swiper.autoplay && swiper.autoplay.running) {
            swiper.autoplay.stop();
          }
          // Убираем transition на время drag
          swiper.setTransition(0);
        }
        
        var clientX = e.touches ? e.touches[0].clientX : e.clientX;
        doMove(clientX);
        
        e.preventDefault();
        e.stopPropagation();
      }
      
      function doMove(clientX) {
        var swiper = getSwiper();
        if (!swiper) return;
        
        var rect = bar.getBoundingClientRect();
        var progress = (clientX - rect.left) / rect.width;
        progress = Math.max(0, Math.min(1, progress));
        
        // Плавное перемещение через translate
        var minTranslate = swiper.minTranslate();
        var maxTranslate = swiper.maxTranslate();
        var translate = minTranslate + progress * (maxTranslate - minTranslate);
        
        swiper.setTranslate(translate);
        swiper.setTransition(0);
        
        // Обновляем прогресс и активный слайд визуально
        swiper.updateProgress(translate);
        swiper.updateActiveIndex();
        swiper.updateSlidesClasses();
        
        // Обновляем pagination fill
        if (swiper.pagination && swiper.pagination.render) {
          swiper.pagination.update();
        }
      }
      
      function onMouseMove(e) {
        if (!isDragging) return;
        doMove(e.clientX);
        e.preventDefault();
      }
      
      function onTouchMove(e) {
        if (!isDragging) return;
        doMove(e.touches[0].clientX);
        e.preventDefault();
      }
      
      function onUp() {
        if (!isDragging) return;
        isDragging = false;
        bar.style.cursor = 'grab';
        
        var swiper = getSwiper();
        if (swiper) {
          // Snap к ближайшему слайду с анимацией
          swiper.setTransition(300);
          swiper.slideToClosest(300);
          
          // Возобновляем autoplay
          if (swiper.autoplay) {
            swiper.autoplay.start();
          }
        }
      }
      
      bar.addEventListener('mousedown', onDown, false);
      document.addEventListener('mousemove', onMouseMove, false);
      document.addEventListener('mouseup', onUp, false);
      
      bar.addEventListener('touchstart', onDown, { passive: false });
      document.addEventListener('touchmove', onTouchMove, { passive: false });
      document.addEventListener('touchend', onUp, false);
    });
    
  }, 1000);
  
});