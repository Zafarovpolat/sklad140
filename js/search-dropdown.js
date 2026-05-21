document.addEventListener('DOMContentLoaded', function () {
    var searchInput = document.getElementById('search');
    var dropdown = document.getElementById('search-dropdown');
    var modal = document.querySelector('.search-modal');
    
    if (!searchInput || !dropdown) return;
    
    var resultsContainer = dropdown.querySelector('.search-dropdown__results');
    var loadingEl = dropdown.querySelector('.search-dropdown__loading');
    var emptyEl = dropdown.querySelector('.search-dropdown__empty');

    var debounceTimer = null;
    var isOpen = false;

    // Скрываем модалку если она есть
    if (modal) {
        modal.style.display = 'none';
    }

    function showDropdown() {
        dropdown.style.display = 'block';
        isOpen = true;
    }

    function hideDropdown() {
        dropdown.style.display = 'none';
        isOpen = false;
    }

    function showLoading() {
        if (loadingEl) loadingEl.style.display = 'block';
        if (resultsContainer) resultsContainer.style.display = 'none';
        if (emptyEl) emptyEl.style.display = 'none';
    }

    function showResults(html) {
        if (loadingEl) loadingEl.style.display = 'none';
        if (emptyEl) emptyEl.style.display = 'none';
        if (resultsContainer) {
            resultsContainer.innerHTML = html;
            resultsContainer.style.display = 'block';
        }
    }

    function showEmpty() {
        if (loadingEl) loadingEl.style.display = 'none';
        if (resultsContainer) resultsContainer.style.display = 'none';
        if (emptyEl) emptyEl.style.display = 'block';
    }

    function doSearch(term) {
        if (typeof ThemeSearch === 'undefined' || !ThemeSearch.ajaxUrl) {
            console.error('ThemeSearch not defined');
            return;
        }

        showLoading();
        showDropdown();

        var formData = new FormData();
        formData.append('action', 'theme_live_search');
        formData.append('nonce', ThemeSearch.nonce);
        formData.append('term', term);

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
                showEmpty();
                return;
            }

            if (data.data.html && data.data.count > 0) {
                showResults(data.data.html);
            } else {
                showEmpty();
            }
        })
        .catch(function (error) {
            console.error('Search error:', error);
            showEmpty();
        });
    }

    // При вводе текста
    searchInput.addEventListener('input', function () {
        var term = this.value.trim();

        if (debounceTimer) {
            clearTimeout(debounceTimer);
        }

        if (term.length < 2) {
            hideDropdown();
            return;
        }

        debounceTimer = setTimeout(function () {
            doSearch(term);
        }, 300);
    });

    // При фокусе — показать dropdown если есть результаты
    searchInput.addEventListener('focus', function (e) {
        e.stopPropagation();
        // Убираем darken если вдруг появился
        var darken = document.querySelector('.darken');
        if (darken) darken.classList.remove('darken--active');
        
        var term = this.value.trim();
        if (term.length >= 2 && resultsContainer && resultsContainer.innerHTML) {
            showDropdown();
        }
    });

    // Предотвращаем всплытие
    searchInput.addEventListener('click', function(e) {
        e.stopPropagation();
    });

    // При клике вне — скрыть dropdown
    document.addEventListener('click', function (e) {
        var wrapper = searchInput.closest('.header-search__input');
        var isClickInside = wrapper && wrapper.contains(e.target);
        if (!isClickInside && isOpen) {
            hideDropdown();
        }
    });

    // При нажатии Escape — скрыть dropdown
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && isOpen) {
            hideDropdown();
            searchInput.blur();
        }
    });
});
