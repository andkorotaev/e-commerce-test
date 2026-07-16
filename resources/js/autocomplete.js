/**
 * Shared live-suggestions dropdown, attached to any search input carrying
 * a `data-suggest-url` attribute — used by both the header search modal and
 * the catalog toolbar's search field, so the debounce/fetch/render/dismiss
 * behavior only lives in one place.
 */
export function attachAutocomplete(input) {
    if (!input) {
        return;
    }

    const suggestUrl = input.dataset.suggestUrl;

    if (!suggestUrl) {
        return;
    }

    const wrapper = input.parentElement;
    wrapper.classList.add('relative');

    const dropdown = document.createElement('div');
    dropdown.className =
        'absolute inset-x-0 top-full z-30 mt-1 hidden max-h-80 overflow-y-auto border border-stone bg-bone shadow-lg';
    wrapper.appendChild(dropdown);

    let debounceTimer;
    let requestToken = 0;

    const hide = () => {
        dropdown.classList.add('hidden');
        dropdown.innerHTML = '';
    };

    const render = (results) => {
        if (results.length === 0) {
            hide();
            return;
        }

        dropdown.innerHTML = results
            .map(
                (item) => `
                    <a href="${item.url}" class="flex items-center gap-3 px-3 py-2 text-sm text-ink transition-colors hover:bg-stone/10">
                        ${
                            item.image
                                ? `<img src="${item.image}" alt="" class="h-10 w-10 shrink-0 object-cover">`
                                : '<div class="h-10 w-10 shrink-0 bg-stone/10"></div>'
                        }
                        <span class="flex-1 truncate">${item.name}</span>
                        <span class="shrink-0 font-mono text-xs text-ink/50">${item.price}</span>
                    </a>
                `,
            )
            .join('');

        dropdown.classList.remove('hidden');
    };

    const search = async (query) => {
        const token = ++requestToken;

        const response = await window.axios.get(suggestUrl, {
            params: { q: query },
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });

        if (token !== requestToken) {
            return;
        }

        render(response.data.results ?? []);
    };

    input.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        const query = input.value.trim();

        if (query.length < 2) {
            hide();
            return;
        }

        debounceTimer = setTimeout(() => search(query), 250);
    });

    input.addEventListener('focus', () => {
        if (input.value.trim().length >= 2 && dropdown.innerHTML !== '') {
            dropdown.classList.remove('hidden');
        }
    });

    document.addEventListener('click', (event) => {
        if (!wrapper.contains(event.target)) {
            hide();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            hide();
        }
    });
}
