document.addEventListener('DOMContentLoaded', function () {
    const headerInput = document.getElementById('search');
    const modal = document.querySelector('.search-modal');
    const modalInput = document.getElementById('modal-search-input');
    const cancelBtn = document.querySelector('.search-modal__cancel');

    const historyBlock = document.querySelector('.search-modal-content--history');
    const resultBlock = document.querySelector('.search-modal-content--results');
    const resultContainer = resultBlock ? resultBlock.querySelector('.search-modal-content__products') : null;

    // ✅ Мобильный контейнер
    const mobileContainer = document.querySelector('.search-modal-content-mobile');
    // Сохраняем оригинальный HTML для сброса
    const mobileOriginalHTML = mobileContainer ? mobileContainer.innerHTML : '';

    const historyWrapper = document.querySelector('.search-modal__recent-wrapper');
    const historyItemsContainer = document.querySelector('.search-modal__recent-items');
    const clearHistoryBtn = document.querySelector('.search-modal__clear');
    const historyClose = document.querySelector('.search-modal__recent-cross');

    const tabsContainer = document.querySelector('.search-modal__tubs');
    const showMoreBtn = document.querySelector('.search-modal__show');

    const HISTORY_KEY = 'siteSearchHistory';

    let isSyncing = false;
    let debounceTimer = null;

    function openModal() {
        if (!modal) return;

        modal.classList.add('search-modal--open');
        document.body.classList.add('search-modal-open');

        const value = headerInput ? headerInput.value : '';
        if (modalInput) {
            modalInput.value = value;

            setTimeout(function () {
                modalInput.focus();
                modalInput.setSelectionRange(modalInput.value.length, modalInput.value.length);
            }, 100);
        }

        toggleView();

        if (value && value.trim().length >= 2) {
            doSearch(value.trim());
        }
    }

    function closeModal() {
        if (!modal) return;

        modal.classList.remove('search-modal--open');
        document.body.classList.remove('search-modal-open');
    }

    if (headerInput) {
        headerInput.addEventListener('focus', openModal);
        headerInput.addEventListener('click', openModal);
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', function (e) {
            e.preventDefault();
            closeModal();
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });

    function syncInputs(from, to) {
        if (!from || !to) return;
        if (isSyncing) return;

        isSyncing = true;
        to.value = from.value;
        isSyncing = false;
    }

    if (headerInput && modalInput) {
        headerInput.addEventListener('input', function () {
            syncInputs(headerInput, modalInput);
        });

        modalInput.addEventListener('input', function () {
            syncInputs(modalInput, headerInput);
            onSearchInputChange();
        });
    } else if (modalInput) {
        modalInput.addEventListener('input', onSearchInputChange);
    }

    function submitModalSearch() {
        if (!modalInput) return;
        const term = modalInput.value.trim();
        if (term.length >= 2) {
            addToHistory(term);
            window.location.href = 'https://sklad140.ru/shop/?s=' + encodeURIComponent(term);
        }
    }

    if (modalInput) {
        modalInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                submitModalSearch();
            }
        });
    }

    const modalSubmitBtn = document.getElementById('modal-search-submit');
    if (modalSubmitBtn) {
        modalSubmitBtn.addEventListener('click', function (e) {
            e.preventDefault();
            submitModalSearch();
        });
    }

    if (headerInput) {
        headerInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();

                const term = headerInput.value.trim();

                if (term.length >= 2) {
                    window.location.href = 'https://sklad140.ru/shop/?s=' + encodeURIComponent(term);
                }
            }
        });
    }

    // Клик по иконке поиска внутри input в шапке
    var headerSearchSubmitBtn = document.getElementById('header-search-submit');
    if (headerSearchSubmitBtn) {
        headerSearchSubmitBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var term = (headerInput ? headerInput.value : '').trim();
            if (term.length >= 2) {
                window.location.href = 'https://sklad140.ru/shop/?s=' + encodeURIComponent(term);
            } else if (headerInput) {
                headerInput.focus();
            }
        });
    }

    function onSearchInputChange() {
        const term = modalInput ? modalInput.value.trim() : '';

        if (debounceTimer) {
            clearTimeout(debounceTimer);
        }

        debounceTimer = setTimeout(function () {
            if (!term || term.length < 2) {
                // Сброс результатов
                if (resultContainer) {
                    resultContainer.innerHTML = '';
                }
                // ✅ Восстановить рандомные товары в мобилке
                if (mobileContainer) {
                    mobileContainer.innerHTML = mobileOriginalHTML;
                }
                toggleView();
                return;
            }

            doSearch(term);
        }, 300);
    }

    function toggleView() {
        const term = modalInput ? modalInput.value.trim() : '';
        const hasResults = resultContainer && resultContainer.children.length > 0;

        if (!term || term.length < 2 || !hasResults) {
            if (historyBlock) historyBlock.classList.add('search-modal-content--visible');
            if (resultBlock) resultBlock.classList.remove('search-modal-content--visible');
            // ✅ Показать мобильный блок с рандомными товарами
            if (mobileContainer) mobileContainer.classList.remove('search-modal-content-mobile--results');
        } else {
            if (historyBlock) historyBlock.classList.remove('search-modal-content--visible');
            if (resultBlock) resultBlock.classList.add('search-modal-content--visible');
            // ✅ Пометить что в мобилке результаты поиска
            if (mobileContainer) mobileContainer.classList.add('search-modal-content-mobile--results');
        }
    }

    function doSearch(term) {
        if (typeof ThemeSearch === 'undefined' || !ThemeSearch.ajaxUrl) {
            return;
        }

        const formData = new FormData();
        formData.append('action', 'theme_live_search');
        formData.append('nonce', ThemeSearch.nonce);
        formData.append('term', term);

        // ✅ Лоадер для обоих контейнеров
        if (resultContainer) {
            resultContainer.classList.add('is-loading');
        }
        if (mobileContainer) {
            mobileContainer.classList.add('is-loading');
        }

        fetch(ThemeSearch.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData,
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                if (!data || !data.success) {
                    if (resultContainer) resultContainer.classList.remove('is-loading');
                    if (mobileContainer) mobileContainer.classList.remove('is-loading');
                    return;
                }

                // ✅ Обновляем ДЕСКТОП контейнер
                if (resultContainer) {
                    resultContainer.innerHTML = data.data.html || '';
                    resultContainer.classList.remove('is-loading');
                }

                // ✅ Обновляем МОБИЛЬНЫЙ контейнер
                if (mobileContainer) {
                    if (data.data.mobileHtml) {
                        mobileContainer.innerHTML = data.data.mobileHtml;
                    } else if (data.data.html) {
                        mobileContainer.innerHTML = data.data.html;
                    }
                    mobileContainer.classList.remove('is-loading');
                }

                if (data.data.count && data.data.count > 0) {
                    addToHistory(term);
                }

                toggleView();
            })
            .catch(function () {
                if (resultContainer) {
                    resultContainer.classList.remove('is-loading');
                }
                if (mobileContainer) {
                    mobileContainer.classList.remove('is-loading');
                }
            });
    }

    function readHistory() {
        try {
            const raw = localStorage.getItem(HISTORY_KEY);
            if (!raw) return [];
            const parsed = JSON.parse(raw);
            if (Array.isArray(parsed)) return parsed;
            return [];
        } catch (e) {
            return [];
        }
    }

    function saveHistory(list) {
        try {
            localStorage.setItem(HISTORY_KEY, JSON.stringify(list));
        } catch (e) { }
    }

    function addToHistory(term) {
        let list = readHistory();

        const existingIndex = list.findIndex(function (item) {
            return item.toLowerCase() === term.toLowerCase();
        });

        if (existingIndex !== -1) {
            list.splice(existingIndex, 1);
        }

        list.unshift(term);

        if (list.length > 10) {
            list = list.slice(0, 10);
        }

        saveHistory(list);
        renderHistory();
    }

    function escapeHtml(str) {
        return str.replace(/[&<>"']/g, function (m) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;',
            }[m];
        });
    }

    function renderHistory() {
        const list = readHistory();

        if (!historyWrapper || !historyItemsContainer) return;

        if (!list.length) {
            historyWrapper.style.display = 'none';
            historyItemsContainer.innerHTML = '';
            return;
        }

        historyWrapper.style.display = '';
        historyItemsContainer.innerHTML = '';

        list.forEach(function (term) {
            const item = document.createElement('div');
            item.className = 'search-modal__recent';
            item.innerHTML =
                '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">' +
                '<path d="M2 8C2 9.18669 2.35189 10.3467 3.01118 11.3334C3.67047 12.3201 4.60754 13.0892 5.7039 13.5433C6.80026 13.9974 8.00666 14.1162 9.17054 13.8847C10.3344 13.6532 11.4035 13.0818 12.2426 12.2426C13.0818 11.4035 13.6532 10.3344 13.8847 9.17054C14.1162 8.00666 13.9974 6.80026 13.5433 5.7039C13.0892 4.60754 12.3201 3.67047 11.3334 3.01118C10.3467 2.35189 9.18669 2 8 2C6.32263 2.00631 4.71265 2.66082 3.50667 3.82667L2 5.33333M2 5.33333V2M2 5.33333H5.33333M8 4.66667V8L10.6667 9.33333" stroke="#7C7C7C" stroke-linecap="round" stroke-linejoin="round"/>' +
                '</svg>' +
                '<span>' + escapeHtml(term) + '</span>';

            item.addEventListener('click', function () {
                if (modalInput) modalInput.value = term;
                if (headerInput) headerInput.value = term;
                doSearch(term);
            });

            historyItemsContainer.appendChild(item);
        });
    }

    if (clearHistoryBtn) {
        clearHistoryBtn.addEventListener('click', function () {
            localStorage.removeItem(HISTORY_KEY);
            renderHistory();
        });
    }

    if (historyClose) {
        historyClose.addEventListener('click', function () {
            localStorage.removeItem(HISTORY_KEY);
            renderHistory();
        });
    }

    renderHistory();

    function attachPopular(selector) {
        const elements = document.querySelectorAll(selector);
        elements.forEach(function (el) {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                const term = this.textContent.trim();
                if (!term) return;

                if (modalInput) modalInput.value = term;
                if (headerInput) headerInput.value = term;

                doSearch(term);
            });
        });
    }

    attachPopular('.search-modal-categories .button');
    attachPopular('.search-modal__tub');

    if (showMoreBtn && tabsContainer) {
        showMoreBtn.addEventListener('click', function (e) {
            e.preventDefault();

            const hidden = tabsContainer.querySelectorAll('.search-modal__tub[data-visible-by="click"]');
            const isOpen = this.classList.contains('is-open');

            if (!isOpen) {
                hidden.forEach(function (el) {
                    el.classList.add('search-modal__tub--visible');
                });
                this.classList.add('is-open');
                this.innerHTML = 'Скрыть';
            } else {
                hidden.forEach(function (el) {
                    el.classList.remove('search-modal__tub--visible');
                });
                this.classList.remove('is-open');
                const count = hidden.length;
                this.innerHTML = 'Показать ещё <span>' + count + '</span>';
            }
        });
    }
});