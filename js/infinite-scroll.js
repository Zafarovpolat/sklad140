(function () {
    'use strict';

    var container = document.querySelector('.catalog-products__items');
    var pagination = document.querySelector('.pagination');

    if (!container) {
        return;
    }

    if (pagination) pagination.style.display = 'none';

    var loading = false;
    var currentPage = 1;
    var termId = parseInt(container.dataset.termId) || 0;
    var maxPages = parseInt(container.dataset.maxPages) || 1;

    // Devin: persistent set of loaded product IDs (lives across AJAX calls).
    // Populated from initial server-render and grows with every successful load.
    var loadedIds = new Set();

    function collectOuterPid(node) {
        // An "outer card" is .product-item--catalog. Only these should be counted as products.
        if (!node || node.nodeType !== 1) return 0;
        if (!node.classList || !node.classList.contains('product-item--catalog')) return 0;
        var pid = parseInt(node.getAttribute('data-product-id'), 10);
        return pid || 0;
    }

    function ingestInitial() {
        var dups = [];
        var seen = new Set();
        container.querySelectorAll(':scope > .product-item--catalog').forEach(function (el) {
            var pid = parseInt(el.getAttribute('data-product-id'), 10);
            if (!pid) return;
            if (seen.has(pid)) {
                // Remove any pre-existing duplicate from the initial render.
                dups.push(el);
                return;
            }
            seen.add(pid);
            loadedIds.add(pid);
        });
        dups.forEach(function (el) { el.remove(); });
    }

    ingestInitial();

    if (maxPages <= 1) {
        return;
    }

    var loader = document.createElement('div');
    loader.className = 'infinite-scroll-loader';
    loader.style.display = 'none';
    loader.style.textAlign = 'center';
    loader.style.padding = '20px';

    if (container.parentNode) {
        container.parentNode.insertBefore(loader, container.nextSibling);
    }

    function reinitYITH() {
        if (typeof jQuery === 'undefined') return;

        jQuery('.yith-wcwl-add-button a.add_to_wishlist').off('click.yith');

        if (typeof yith_woocompare !== 'undefined') {
            jQuery('.compare:not(.compare-initialized)').each(function () {
                var $btn = jQuery(this);
                var productId = $btn.closest('.product-item').data('product-id');
                if (productId) $btn.attr('data-product_id', productId);
                $btn.addClass('compare-initialized');
            });
            jQuery(document).trigger('yith_woocompare_init');
        }
    }

    function showLoader() {
        loader.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;gap:10px;color:#258FFB;"><svg width="40" height="40" viewBox="0 0 50 50"><circle cx="25" cy="25" r="20" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-dasharray="31.4 31.4" transform="rotate(-90 25 25)"><animateTransform attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="1s" repeatCount="indefinite"/></circle></svg><span style="font-size:16px;font-weight:500;">Загружаем товары...</span></div>';
        loader.style.display = 'block';
    }

    function hideLoader() {
        loader.style.display = 'none';
    }

    function showEndMessage() {
        loader.innerHTML = '<p style="color:#999;font-size:16px;font-weight:500;">✓ Все товары загружены</p>';
        loader.style.display = 'block';
    }

    function loadMore() {
        if (loading || currentPage >= maxPages) {
            if (currentPage >= maxPages) {
                showEndMessage();
            }
            return;
        }

        loading = true;
        showLoader();

        var nextPage = currentPage + 1;

        var formData = new FormData();
        formData.append('action', 'load_more_products');
        formData.append('paged', nextPage);
        formData.append('term_id', termId);
        formData.append('nonce', '');

        var urlParams = new URLSearchParams(window.location.search);
        urlParams.forEach(function (value, key) {
            if (key !== 'page' && key !== 'paged') {
                formData.append(key, value);
            }
        });

        // Devin: send the full persistent set to the server so it can exclude them via post__not_in.
        loadedIds.forEach(function (pid) {
            formData.append('loaded_ids[]', pid);
        });

        fetch('/wp-admin/admin-ajax.php', {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data && data.success && data.data && data.data.html && data.data.html.trim()) {
                    // Parse returned HTML, keep only NEW outer cards whose pid is not already loaded.
                    var _tmp = document.createElement('div');
                    _tmp.innerHTML = data.data.html;

                    // Iterate over direct children. Typical server output is a flat list of .product-item--catalog divs.
                    var nodes = Array.prototype.slice.call(_tmp.children);
                    var frag = document.createDocumentFragment();

                    nodes.forEach(function (node) {
                        var pid = collectOuterPid(node);
                        if (!pid) {
                            // Non-product node: append as-is (e.g. end-of-list message).
                            frag.appendChild(node);
                            return;
                        }
                        if (loadedIds.has(pid)) {
                            // Already present — drop it.
                            return;
                        }
                        loadedIds.add(pid);
                        frag.appendChild(node);
                    });

                    if (frag.childNodes.length > 0) {
                        container.appendChild(frag);
                    }

                    setTimeout(function () {
                        reinitYITH();
                    }, 200);

                    currentPage = nextPage;
                    maxPages = data.data.max_pages || maxPages;

                    setTimeout(checkAndLoad, 300);
                }

                loading = false;

                if (currentPage >= maxPages) {
                    showEndMessage();
                } else {
                    hideLoader();
                }
            })
            .catch(function (error) {
                loading = false;
                loader.innerHTML = '<p style="color:#f53535;">Ошибка загрузки</p>';
            });
    }

    function checkAndLoad() {
        var scrollPos = window.pageYOffset + window.innerHeight;
        var docHeight = Math.max(
            document.documentElement.scrollHeight,
            document.body.scrollHeight
        );

        var scrollPercent = (scrollPos / docHeight) * 100;

        if (scrollPercent >= 75 && currentPage < maxPages && !loading) {
            loadMore();
        }
    }

    var scrollTimer;
    window.addEventListener('scroll', function () {
        clearTimeout(scrollTimer);
        scrollTimer = setTimeout(checkAndLoad, 200);
    }, { passive: true });

    var resizeTimer;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(checkAndLoad, 300);
    }, { passive: true });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(reinitYITH, 500);
        });
    } else {
        setTimeout(reinitYITH, 500);
    }

    document.addEventListener('catalogFiltersApplied', function () {
        currentPage = 1;
        loading = false;
        // Reset known IDs from DOM after filters re-populate the grid.
        loadedIds = new Set();
        ingestInitial();
        setTimeout(reinitYITH, 600);
    });

    setTimeout(checkAndLoad, 500);

})();
