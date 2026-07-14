export function init(root) {
    const form = root.querySelector('[data-products-form]');
    const countEl = root.querySelector('[data-products-count]');

    if (!form) {
        return;
    }

    const productsUrl = form.dataset.productsUrl;
    let gridEl = root.querySelector('[data-products-grid]');
    let debounceTimer;
    let requestToken = 0;

    const buildParams = () => {
        const params = new URLSearchParams(new FormData(form));
        params.delete('page');
        return params;
    };

    const updateCount = () => {
        const count = gridEl?.dataset.count;
        if (countEl && count !== undefined) {
            countEl.textContent = count;
        }
    };

    const render = async (params, { pushState = true } = {}) => {
        const token = ++requestToken;

        const response = await fetch(`${productsUrl}?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });

        if (!response.ok || token !== requestToken) {
            return;
        }

        const html = await response.text();

        if (gridEl) {
            gridEl.outerHTML = html;
        }
        gridEl = root.querySelector('[data-products-grid]');
        updateCount();

        if (pushState) {
            const url = new URL(window.location.pathname, window.location.origin);
            url.search = params.toString();
            window.history.pushState({}, '', url);
        }

        root.scrollIntoView({ behavior: 'smooth', block: 'start' });
    };

    const refresh = () => render(buildParams());

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        refresh();
    });

    form.addEventListener('change', (event) => {
        if (event.target.matches('input, select')) {
            refresh();
        }
    });

    form.addEventListener('input', (event) => {
        if (event.target.matches('[data-debounce]')) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(refresh, 400);
        }
    });

    root.addEventListener('click', (event) => {
        const resetButton = event.target.closest('[data-products-reset]');
        if (resetButton) {
            event.preventDefault();
            form.reset();
            refresh();
            return;
        }

        const link = event.target.closest('[data-products-pagination] a');
        if (link) {
            event.preventDefault();
            const params = new URL(link.href).searchParams;
            render(params);
        }
    });

    window.addEventListener('popstate', () => {
        const params = new URLSearchParams(window.location.search);
        restoreFormFromParams(form, params);
        render(params, { pushState: false });
    });

    function restoreFormFromParams(form, params) {
        [...form.elements].forEach((el) => {
            if (!el.name) {
                return;
            }

            const key = el.name.replace('[]', '');

            if (el.type === 'checkbox') {
                el.checked = params.getAll(el.name).includes(el.value);
            } else {
                el.value = params.get(key) ?? '';
            }
        });
    }
}
