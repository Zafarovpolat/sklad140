// ============================================
// Нижний слайдер (миниатюры)
// ============================================
const productSwiperBottom = new Swiper('.product-swiper-bottom', {
    spaceBetween: 12,
    slidesPerView: 4,
    freeMode: true,
    watchSlidesProgress: true,
    breakpoints: {
        1320: {
            slidesPerView: 6,
        },
        1100: {
            slidesPerView: 5,
        },
        600: {
            slidesPerView: 6,
        },
        0: {
            slidesPerView: 'auto',
            spaceBetween: 8,
            freeMode: {
                enabled: true,
                momentumBounce: false,
            },
        },
    },
});

// ============================================
// Верхний слайдер (основное фото)
// ============================================
const isMobile = () => window.innerWidth <= 600;

function createTopSwiper(initialSlide = 0) {
    return new Swiper('.product-swiper-top', {
        effect: 'fade',
        spaceBetween: 10,
        allowTouchMove: true,
        loop: isMobile(),
        fadeEffect: { crossFade: true },
        pagination: {
            el: '.product-swiper-top .swiper-pagination',
            clickable: true,
        },
        navigation: isMobile() ? {
            nextEl: '.swiper-button-next__product-top',
            prevEl: '.swiper-button-prev__product-top',
        } : false,
        thumbs: {
            swiper: productSwiperBottom,
        },
        initialSlide: initialSlide,
    });
}

let productSwiperTop = createTopSwiper(0);

// ============================================
// Ручной loop ТОЛЬКО на десктопе (> 600px)
// ============================================
const nextBtn = document.querySelector('.swiper-button-next__product-top');
const prevBtn = document.querySelector('.swiper-button-prev__product-top');
let isLooping = false;

if (nextBtn) {
    nextBtn.addEventListener('click', () => {
        if (isMobile()) return; // На мобильных работает встроенный loop
        if (isLooping) return;
        const totalSlides = productSwiperTop.slides.length;
        if (productSwiperTop.activeIndex >= totalSlides - 1) {
            isLooping = true;
            productSwiperTop.slideTo(0);
            setTimeout(() => { isLooping = false; }, 500);
        } else {
            productSwiperTop.slideNext();
        }
    });
}

if (prevBtn) {
    prevBtn.addEventListener('click', () => {
        if (isMobile()) return; // На мобильных работает встроенный loop
        if (isLooping) return;
        const totalSlides = productSwiperTop.slides.length;
        if (productSwiperTop.activeIndex <= 0) {
            isLooping = true;
            productSwiperTop.slideTo(totalSlides - 1);
            setTimeout(() => { isLooping = false; }, 500);
        } else {
            productSwiperTop.slidePrev();
        }
    });
}

// ============================================
// Пересоздание слайдера при ресайзе
// ============================================
let wasDesktop = !isMobile();

window.addEventListener('resize', () => {
    const nowDesktop = !isMobile();

    if (nowDesktop !== wasDesktop) {
        wasDesktop = nowDesktop;

        const currentIndex = productSwiperTop.realIndex || 0;
        productSwiperTop.destroy(true, true);
        productSwiperTop = createTopSwiper(currentIndex);
    }
});

// ============================================
// Клик на миниатюру
// ============================================
document.querySelectorAll('.product-swiper-bottom .swiper-slide').forEach((slide) => {
    slide.addEventListener('click', function () {
        const index = parseInt(this.getAttribute('data-slide-index')) || 0;
        productSwiperTop.slideTo(index);
    });
});

// ============================================
// Fancybox
// ============================================
if (typeof Fancybox !== 'undefined') {
    Fancybox.bind('[data-fancybox="product-gallery"]', {
        Image: {
            zoom: true
        }
    });
}

// ============================================
// Фикс ширины слайдеров
// ============================================
const fixSlider = () => {
    const productSwiperTopEl = document.querySelector('.product-swiper-top.swiper');
    const productSwiperBottomEl = document.querySelector('.product-swiper-bottom.swiper');
    const productSwiperContainerRect = document.querySelector('.product-section .container').getBoundingClientRect();
    const productSpecificationsRect = document.querySelector('.product-specifications')?.getBoundingClientRect();
    const productRightRect = document.querySelector('.product__right')?.getBoundingClientRect();

    if (window.matchMedia('(min-width: 1201px)').matches) {
        productSwiperTopEl.style.width = `${productSwiperContainerRect.width - productSpecificationsRect.width - productRightRect.width - 48 - 40}px`;
        productSwiperBottomEl.style.width = `${productSwiperContainerRect.width - productSpecificationsRect.width - productRightRect.width - 48 - 40}px`;
    } else if (window.matchMedia('(max-width: 480px)').matches) {
        productSwiperTopEl.style.width = `${productSwiperContainerRect.width - 40}px`;
        productSwiperBottomEl.style.width = `${productSwiperContainerRect.width - 40}px`;
    } else if (window.matchMedia('(max-width: 900px)').matches) {
        productSwiperTopEl.style.width = `calc(${productSwiperContainerRect.width - 40}px - 20dvw)`;
        productSwiperBottomEl.style.width = `${productSwiperContainerRect.width - 40}px`;
    } else {
        productSwiperTopEl.style.width = `${productSwiperContainerRect.width - productRightRect.width - 24 - 40}px`;
        productSwiperBottomEl.style.width = `${productSwiperContainerRect.width - productRightRect.width - 24 - 40}px`;
    }
};

window.addEventListener('resize', fixSlider);
fixSlider();

// ============================================
// Описание: Раскрыть/Скрыть
// ============================================
const productInfoContentDescLink = document.querySelector(
    '.product-info-content-description .product__right-block__link'
);
const productInfoContentDescText = document.querySelector('.product-info-content-description__text');

if (productInfoContentDescLink) {
    productInfoContentDescLink.addEventListener('click', () => {
        productInfoContentDescText.classList.toggle('product-info-content-description__text--open');
    });
}

// ============================================
// Похожие товары слайдер
// ============================================
const sameProductsSwiper = new Swiper('.same-products-swiper', {
    spaceBetween: 8,
    slidesPerView: 2,
    scrollbar: {
        el: '.same-products-swiper-scrollbar',
        draggable: true,
        snapOnRelease: true,
    },
    breakpoints: {
        1320: {
            slidesPerView: 5,
        },
        1070: {
            slidesPerView: 4,
        },
        800: {
            slidesPerView: 3,
        },
        600: {
            spaceBetween: 12,
        },
    },
});

// ============================================
// Адаптивный перенос блока
// ============================================
window.addEventListener('resize', () => {
    moveBlock('.product__right', '.product--main', '.product__wrapper', 900);
});
moveBlock('.product__right', '.product--main', '.product__wrapper', 900);

// ============================================
// Табы
// ============================================
const tabs = document.querySelectorAll('.product-info__tab');
const contents = document.querySelectorAll('.product-info-content');

tabs.forEach(tab =>
    tab.addEventListener('click', () => {
        const currentDataTab = tab.getAttribute('data-tab');

        tabs.forEach(t => t.classList.remove('product-info__tab--active'));
        tab.classList.add('product-info__tab--active');

        contents.forEach(content => {
            content.classList.remove('product-info-content--active');

            if (content.getAttribute('data-content') === currentDataTab) {
                content.classList.add('product-info-content--active');
            }
        });
    })
);

// ============================================
// Отзывы слайдер
// ============================================
const productReviewsSwiper = new Swiper('.product-reviews-swiper', {
    spaceBetween: 24,
    slidesPerView: 1,
    scrollbar: {
        el: '.product-reviews-swiper .swiper-scrollbar',
        draggable: true,
    },
    navigation: {
        nextEl: '.swiper-button-next__product-reviews',
        prevEl: '.swiper-button-prev__product-reviews',
    },
    breakpoints: {
        600: {
            slidesPerView: 'auto',
        },
    },
});

const fixProductReviewsSwiper = () => {
    if (window.matchMedia('(max-width: 600px)').matches) {
        const containerRect = document.querySelector('.product-section .container')?.getBoundingClientRect();
        const swiperWrapper = document.querySelector('.product-reviews-swiper.swiper');
        if (containerRect && swiperWrapper) {
            swiperWrapper.style.width = `${containerRect.width - 80}px`;
        }
    }
};

window.addEventListener('resize', fixProductReviewsSwiper);
fixProductReviewsSwiper();


// ============================================
// Простой лайтбокс со свайпом
// ============================================
(function () {
    var lightbox = document.createElement('div');
    lightbox.id = 'product-lightbox';
    lightbox.innerHTML = '<div class="pl-overlay"></div><button class="pl-close">&times;</button><img class="pl-img" src="" alt="">';
    document.body.appendChild(lightbox);

    var style = document.createElement('style');
    style.textContent = `
        #product-lightbox{display:none;position:fixed;top:0;left:0;width:100%;height:100%;z-index:999999;align-items:center;justify-content:center}
        #product-lightbox.active{display:flex}
        .pl-overlay{position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.95)}
        .pl-close{position:absolute;top:20px;right:20px;width:50px;height:50px;background:#fff;border:none;border-radius:50%;font-size:30px;cursor:pointer;z-index:10}
        .pl-img{position:relative;z-index:5;max-width:90vw;max-height:90vh;object-fit:contain;transition:opacity .2s ease;user-select:none;-webkit-user-drag:none}
    `;
    document.head.appendChild(style);

    var img = lightbox.querySelector('.pl-img');
    var images = [];   // массив URL всех изображений галереи
    var currentIndex = 0;

    // Собрать все изображения из верхнего слайдера
    function collectImages() {
        images = [];
        document.querySelectorAll('.product-swiper-top .swiper-slide .product-swiper-top-slide__img').forEach(function (el) {
            var src = el.getAttribute('data-full-src') || el.querySelector('img')?.src;
            if (src) images.push(src);
        });
    }

    function showImage(index) {
        if (!images.length) return;
        // зациклить
        currentIndex = (index + images.length) % images.length;
        img.style.opacity = '0';
        setTimeout(function () {
            img.src = images[currentIndex];
            img.style.opacity = '1';
        }, 150);
    }

    function open(index) {
        collectImages();
        if (!images.length) return;
        currentIndex = index;
        img.src = images[currentIndex];
        img.style.opacity = '1';
        lightbox.classList.add('active');
    }

    function close() { lightbox.classList.remove('active'); }

    lightbox.querySelector('.pl-overlay').addEventListener('click', close);
    lightbox.querySelector('.pl-close').addEventListener('click', close);

    // Клавиатура
    document.addEventListener('keydown', function (e) {
        if (!lightbox.classList.contains('active')) return;
        if (e.key === 'Escape') close();
        if (e.key === 'ArrowRight') showImage(currentIndex + 1);
        if (e.key === 'ArrowLeft')  showImage(currentIndex - 1);
    });

    // Свайп пальцем
    var touchStartX = 0;
    var touchStartY = 0;
    lightbox.addEventListener('touchstart', function (e) {
        touchStartX = e.changedTouches[0].clientX;
        touchStartY = e.changedTouches[0].clientY;
    }, { passive: true });
    lightbox.addEventListener('touchend', function (e) {
        var dx = e.changedTouches[0].clientX - touchStartX;
        var dy = e.changedTouches[0].clientY - touchStartY;
        // свайп только если горизонтальный (dx > dy по модулю) и длиннее 40px
        if (Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > 40) {
            if (dx < 0) showImage(currentIndex + 1); // влево → следующий
            else        showImage(currentIndex - 1); // вправо → предыдущий
        }
    }, { passive: true });

    // Открытие по клику на слайд
    var swiperTop = document.querySelector('.product-swiper-top');
    if (swiperTop) {
        swiperTop.addEventListener('click', function (e) {
            if (!e.target.closest('.product-swiper-top-slide__img')) return;
            // Определить индекс по активному слайду Swiper
            var activeSlide = document.querySelector('.product-swiper-top .swiper-slide-active');
            var allSlides = Array.from(document.querySelectorAll('.product-swiper-top .swiper-slide'));
            var idx = activeSlide ? allSlides.indexOf(activeSlide) : 0;
            open(idx < 0 ? 0 : idx);
        });
    }

    document.querySelectorAll('.product-swiper-top-slide__img').forEach(function (el) {
        el.style.cursor = 'zoom-in';
    });
})();