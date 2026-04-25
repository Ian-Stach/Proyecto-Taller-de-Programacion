(function () {
    const pageSelector = '[data-products-page]';
    const resultsSelector = '[data-products-results]';
    const asyncFormSelector = 'form[data-products-async-form]';
    const searchFormSelector = 'form[data-products-search-form]';
    const filterFormSelector = 'form[data-products-filter-form]';
    const autoSubmitFieldSelector = 'form[data-products-filter-form] input[type="checkbox"], select[name="sort"]';
    const asyncLinkSelector = 'a[data-products-async-link], .products-pagination a';
    let activeRequestController = null;
    let activeRequestId = 0;

    function getProductsPage() {
        return document.querySelector(pageSelector);
    }

    function getResultsSection() {
        return document.querySelector(resultsSelector);
    }

    function buildUrlFromForm(form) {
        const action = form.getAttribute('action') || window.location.pathname;
        const url = new URL(action, window.location.origin);
        const formData = new FormData(form);
        const params = new URLSearchParams();

        for (const [key, value] of formData.entries()) {
            if (typeof value === 'string' && value.trim() === '') {
                continue;
            }

            params.append(key, value.toString());
        }

        url.search = params.toString();

        return url.toString();
    }

    function getParamValues(searchParams, fieldName) {
        const baseName = fieldName.endsWith('[]') ? fieldName.slice(0, -2) : fieldName;
        const values = [];

        for (const [key, value] of searchParams.entries()) {
            if (key === fieldName || key === `${baseName}[]` || key.startsWith(`${baseName}[`)) {
                values.push(value);
            }
        }

        return values;
    }

    function replaceManagedHiddenInputs(form, name, values) {
        form.querySelectorAll(`input[type="hidden"][name="${name}"]`).forEach((input) => {
            input.remove();
        });

        values.forEach((value) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            form.appendChild(input);
        });
    }

    function syncStaticControls(url) {
        const currentUrl = new URL(url, window.location.origin);
        const searchValue = currentUrl.searchParams.get('search') || '';
        const currentSort = currentUrl.searchParams.get('sort') || 'latest';
        const filterForm = document.querySelector(filterFormSelector);
        const filterFieldNames = filterForm
            ? [...new Set(Array.from(filterForm.querySelectorAll('input[type="checkbox"]')).map((checkbox) => checkbox.name))]
            : [];

        const searchForm = document.querySelector(searchFormSelector);

        if (searchForm) {
            const searchInput = searchForm.querySelector('input[name="search"]');

            if (searchInput) {
                searchInput.value = searchValue;
            }

            filterFieldNames.forEach((fieldName) => {
                replaceManagedHiddenInputs(searchForm, fieldName, getParamValues(currentUrl.searchParams, fieldName));
            });

            replaceManagedHiddenInputs(searchForm, 'sort', currentSort !== 'latest' ? [currentSort] : []);
        }

        if (filterForm) {
            replaceManagedHiddenInputs(filterForm, 'search', searchValue ? [searchValue] : []);
            replaceManagedHiddenInputs(filterForm, 'sort', currentSort !== 'latest' ? [currentSort] : []);

            filterForm.querySelectorAll('input[type="checkbox"]').forEach((checkbox) => {
                checkbox.checked = getParamValues(currentUrl.searchParams, checkbox.name).includes(checkbox.value);
            });
        }

        document.querySelectorAll('[data-filter-param]').forEach((element) => {
            const fieldName = element.getAttribute('data-filter-param');
            element.textContent = String(getParamValues(currentUrl.searchParams, fieldName).length);
        });
    }

    async function loadProductsPage(url, pushState) {
        const page = getProductsPage();
        const results = getResultsSection();

        if (!page || !results) {
            return;
        }

        activeRequestId += 1;
        const requestId = activeRequestId;

        if (activeRequestController) {
            activeRequestController.abort();
        }

        activeRequestController = new AbortController();

        results.classList.add('products-results-loading');

        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                },
                credentials: 'same-origin',
                signal: activeRequestController.signal,
            });

            if (!response.ok) {
                throw new Error('No se pudo actualizar el catálogo.');
            }

            const html = await response.text();
            const currentResults = getResultsSection();

            if (!currentResults || requestId !== activeRequestId) {
                return;
            }

            const template = document.createElement('template');
            template.innerHTML = html.trim();

            const nextResults = template.content.firstElementChild;

            if (!nextResults) {
                throw new Error('Respuesta de catálogo inválida.');
            }

            nextResults.classList.add('products-results-enter');
            currentResults.replaceWith(nextResults);
            syncStaticControls(url);

            window.requestAnimationFrame(() => {
                nextResults.classList.remove('products-results-enter');
            });

            if (pushState) {
                window.history.pushState({}, '', url);
            }
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            window.location.assign(url);
        } finally {
            if (requestId !== activeRequestId) {
                return;
            }

            activeRequestController = null;
            const currentResults = getResultsSection();

            if (currentResults) {
                currentResults.classList.remove('products-results-loading');
            }
        }
    }

    document.addEventListener('submit', function (event) {
        const form = event.target.closest(asyncFormSelector);

        if (!form || !getProductsPage()) {
            return;
        }

        event.preventDefault();
        loadProductsPage(buildUrlFromForm(form), true);
    });

    document.addEventListener('change', function (event) {
        const field = event.target.closest(autoSubmitFieldSelector);

        if (!field) {
            return;
        }

        const form = field.form;

        if (!form || !form.matches(asyncFormSelector) || !getProductsPage()) {
            return;
        }

        loadProductsPage(buildUrlFromForm(form), true);
    });

    document.addEventListener('click', function (event) {
        const link = event.target.closest(asyncLinkSelector);

        if (!link || !getProductsPage()) {
            return;
        }

        if (event.defaultPrevented || event.button !== 0 || link.target === '_blank') {
            return;
        }

        if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
            return;
        }

        event.preventDefault();
        loadProductsPage(link.href, true);
    });

    window.addEventListener('popstate', function () {
        if (!getProductsPage()) {
            return;
        }

        loadProductsPage(window.location.href, false);
    });
})();