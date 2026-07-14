export function init(root) {
    const contents = root.querySelector('[data-cart-contents]');
    const updateUrl = root.dataset.cartUpdateUrl;
    const removeUrl = root.dataset.cartRemoveUrl;

    if (!contents) {
        return;
    }

    let debounceTimer;

    const itemPayload = (itemEl, extra = {}) => ({
        product_id: itemEl.dataset.productId,
        variant_id: itemEl.dataset.variantId || null,
        ...extra,
    });

    const refresh = async (url, payload) => {
        const response = await window.axios.post(url, payload);
        contents.innerHTML = response.data;
    };

    contents.addEventListener('click', (event) => {
        const removeButton = event.target.closest('[data-cart-remove]');

        if (removeButton) {
            const item = removeButton.closest('[data-cart-item]');
            refresh(removeUrl, itemPayload(item));

            return;
        }

        const decreaseButton = event.target.closest('[data-cart-qty-decrease]');
        const increaseButton = event.target.closest('[data-cart-qty-increase]');

        if (!decreaseButton && !increaseButton) {
            return;
        }

        const item = (decreaseButton ?? increaseButton).closest('[data-cart-item]');
        const input = item.querySelector('[data-cart-qty-input]');
        const max = Number(input.max) || 99;
        const next = Number(input.value) + (increaseButton ? 1 : -1);

        input.value = Math.max(1, Math.min(max, next));
        refresh(updateUrl, itemPayload(item, { quantity: input.value }));
    });

    contents.addEventListener('input', (event) => {
        if (!event.target.matches('[data-cart-qty-input]')) {
            return;
        }

        const item = event.target.closest('[data-cart-item]');

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            refresh(updateUrl, itemPayload(item, { quantity: event.target.value }));
        }, 400);
    });
}
