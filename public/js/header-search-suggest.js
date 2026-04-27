(function () {
    const DEBOUNCE_MS = 220;
    const MIN_CHARS = 1;

    const form = document.querySelector('form[data-header-search-form]');

    if (!form) {
        return;
    }

    const suggestionsUrl = form.dataset.suggestionsUrl;
    const input = form.querySelector('input[name="search"]');

    if (!input || !suggestionsUrl) {
        return;
    }

    // --- Construir el dropdown ---
    // Se añade al form (no al input-group) para evitar que overflow:hidden del input-group lo recorte.
    const inputGroup = form.querySelector('.input-group') || form;
    form.style.position = 'relative';

    const dropdown = document.createElement('ul');
    dropdown.className = 'header-search-dropdown';
    dropdown.setAttribute('role', 'listbox');
    dropdown.setAttribute('aria-label', 'Sugerencias de búsqueda');
    dropdown.hidden = true;
    form.appendChild(dropdown);

    // Alinear el dropdown con el input-group en vez de con el form completo
    function positionDropdown() {
        const groupRect = inputGroup.getBoundingClientRect();
        const formRect = form.getBoundingClientRect();
        dropdown.style.top = (groupRect.bottom - formRect.top) + 'px';
        dropdown.style.left = (groupRect.left - formRect.left) + 'px';
        dropdown.style.width = groupRect.width + 'px';
    }

    // --- Estado ---
    let debounceTimer = null;
    let activeController = null;
    let activeIndex = -1;
    let currentItems = [];

    // --- Helpers ---
    function closeDropdown() {
        dropdown.hidden = true;
        activeIndex = -1;
        currentItems = [];
    }

    function openDropdown() {
        positionDropdown();
        dropdown.hidden = false;
    }

    function renderItems(items) {
        dropdown.innerHTML = '';
        currentItems = items;
        activeIndex = -1;

        if (items.length === 0) {
            const li = document.createElement('li');
            li.className = 'header-search-dropdown-empty';
            li.textContent = 'Sin resultados';
            dropdown.appendChild(li);
            openDropdown();
            return;
        }

        items.forEach(function (item, index) {
            const li = document.createElement('li');
            li.className = 'header-search-dropdown-item';
            li.setAttribute('role', 'option');
            li.setAttribute('data-index', String(index));

            const img = document.createElement('span');
            img.className = 'header-search-suggest-img-wrap';

            if (item.image) {
                const imgEl = document.createElement('img');
                imgEl.src = item.image;
                imgEl.alt = '';
                imgEl.className = 'header-search-suggest-img';
                imgEl.onerror = function () { imgEl.style.display = 'none'; };
                img.appendChild(imgEl);
            }

            const text = document.createElement('span');
            text.className = 'header-search-suggest-text';

            const name = document.createElement('span');
            name.className = 'header-search-suggest-name';
            name.textContent = item.name;

            const price = document.createElement('span');
            price.className = 'header-search-suggest-price';
            price.textContent = '$' + item.price;

            text.appendChild(name);
            text.appendChild(price);
            li.appendChild(img);
            li.appendChild(text);

            li.addEventListener('mousedown', function (e) {
                // mousedown antes de blur para que el click no cierre el dropdown
                e.preventDefault();
                window.location.href = item.url;
            });

            li.addEventListener('mousemove', function () {
                setActive(index);
            });

            dropdown.appendChild(li);
        });

        openDropdown();
    }

    function setActive(index) {
        const items = dropdown.querySelectorAll('.header-search-dropdown-item');
        items.forEach(function (el) { el.classList.remove('is-active'); });

        if (index < 0 || index >= items.length) {
            activeIndex = -1;
            return;
        }

        activeIndex = index;
        items[index].classList.add('is-active');
    }

    async function fetchSuggestions(query) {
        if (activeController) {
            activeController.abort();
        }

        activeController = new AbortController();

        try {
            const url = new URL(suggestionsUrl, window.location.origin);
            url.searchParams.set('q', query);

            const response = await fetch(url.toString(), {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
                signal: activeController.signal,
            });

            if (!response.ok) {
                closeDropdown();
                return;
            }

            const data = await response.json();
            renderItems(Array.isArray(data.items) ? data.items : []);
        } catch (err) {
            if (err.name !== 'AbortError') {
                closeDropdown();
            }
        }
    }

    // --- Eventos del input ---
    input.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        const query = input.value.trim();

        if (query.length < MIN_CHARS) {
            closeDropdown();
            return;
        }

        debounceTimer = setTimeout(function () {
            fetchSuggestions(query);
        }, DEBOUNCE_MS);
    });

    input.addEventListener('blur', function () {
        // Pequeño delay para que mousedown en item no se cancele
        setTimeout(closeDropdown, 150);
    });

    input.addEventListener('focus', function () {
        if (input.value.trim().length >= MIN_CHARS && currentItems.length > 0) {
            openDropdown();
        }
    });

    input.addEventListener('keydown', function (e) {
        const items = dropdown.querySelectorAll('.header-search-dropdown-item');

        if (dropdown.hidden || items.length === 0) {
            return;
        }

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            setActive(Math.min(activeIndex + 1, items.length - 1));
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            setActive(Math.max(activeIndex - 1, 0));
        } else if (e.key === 'Enter') {
            if (activeIndex >= 0 && currentItems[activeIndex]) {
                e.preventDefault();
                window.location.href = currentItems[activeIndex].url;
            }
        } else if (e.key === 'Escape') {
            closeDropdown();
            input.blur();
        }
    });

    // Cerrar si se hace click fuera del formulario
    document.addEventListener('click', function (e) {
        if (!form.contains(e.target)) {
            closeDropdown();
        }
    });
})();
