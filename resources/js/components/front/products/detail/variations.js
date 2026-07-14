export function init(root) {
    const payloadEl = root.querySelector('[data-variants-payload]');
    const variants = payloadEl ? JSON.parse(payloadEl.textContent) : [];

    const basePrice = parseFloat(root.dataset.productPrice);
    const baseOldPrice = root.dataset.productOldPrice ? parseFloat(root.dataset.productOldPrice) : null;
    const baseStock = parseInt(root.dataset.productStock, 10) || 0;

    const state = {};

    root.querySelectorAll('[data-variation-group]').forEach((group) => {
        const type = group.dataset.variationGroup;
        const selected = group.querySelector('[data-selected="true"]') ?? group.querySelector('[data-variation-option]');

        if (selected) {
            state[type] = Number(selected.dataset.valueId);
        }
    });

    const qtyInput = root.querySelector('[data-quantity-input]');
    const stockHint = root.querySelector('[data-stock-hint]');
    const addToCartBtn = root.querySelector('[data-add-to-cart]');
    const cartLabel = root.querySelector('[data-add-to-cart-label]');
    const wishlistBtn = root.querySelector('[data-add-to-wishlist]');
    const wishlistLabel = root.querySelector('[data-add-to-wishlist-label]');
    const priceDisplay = document.querySelector('[data-product-price-display]');
    const stockStatus = document.querySelector('[data-stock-status]');
    const mainImage = document.querySelector('[data-gallery-main]');

    const formatPrice = (value) => `${Math.round(value).toLocaleString('uk-UA')} ₴`;

    const resolveVariant = () => {
        if (variants.length === 0) {
            return null;
        }

        return (
            variants.find(
                (variant) =>
                    (variant.colorId === null || variant.colorId === state.color) &&
                    (variant.sizeId === null || variant.sizeId === state.size),
            ) ?? null
        );
    };

    const update = () => {
        const variant = resolveVariant();
        const stock = variant ? variant.stock : baseStock;
        const price = variant?.price ?? basePrice;

        if (qtyInput) {
            qtyInput.max = stock;
            if (Number(qtyInput.value) > stock) {
                qtyInput.value = stock > 0 ? stock : 1;
            }
        }

        if (stockHint) {
            stockHint.textContent = stock > 0 ? `Доступно: ${stock}` : 'Немає в наявності';
        }

        if (stockStatus) {
            stockStatus.textContent = stock > 0 ? 'В наявності' : 'Немає в наявності';
            stockStatus.classList.toggle('text-madder', stock <= 0);
            stockStatus.classList.toggle('text-ink/50', stock > 0);
        }

        if (priceDisplay) {
            const oldPriceHtml =
                baseOldPrice && !variant
                    ? `<span class="mr-2 text-base text-ink/30 line-through">${formatPrice(baseOldPrice)}</span>`
                    : '';
            priceDisplay.innerHTML = `${oldPriceHtml}${formatPrice(price)}`;
        }

        if (addToCartBtn) {
            addToCartBtn.disabled = stock <= 0;
            addToCartBtn.classList.toggle('opacity-50', stock <= 0);
        }

        if (variant?.image && mainImage) {
            mainImage.src = variant.image;
        }
    };

    root.querySelectorAll('[data-variation-option]').forEach((button) => {
        button.addEventListener('click', () => {
            const group = button.closest('[data-variation-group]');
            const type = group.dataset.variationGroup;

            group.querySelectorAll('[data-variation-option]').forEach((el) => delete el.dataset.selected);
            button.dataset.selected = 'true';
            state[type] = Number(button.dataset.valueId);

            update();
        });
    });

    root.querySelector('[data-quantity-decrease]')?.addEventListener('click', () => {
        if (qtyInput) {
            qtyInput.value = Math.max(1, Number(qtyInput.value) - 1);
        }
    });

    root.querySelector('[data-quantity-increase]')?.addEventListener('click', () => {
        if (qtyInput) {
            const max = Number(qtyInput.max) || 99;
            qtyInput.value = Math.min(max, Number(qtyInput.value) + 1);
        }
    });

    root.querySelector('[data-size-guide-open]')?.addEventListener('click', () => {
        window.dispatchEvent(new CustomEvent('size-guide:open'));
    });

    addToCartBtn?.addEventListener('click', () => {
        if (addToCartBtn.disabled || !cartLabel) {
            return;
        }

        const original = cartLabel.textContent;
        cartLabel.textContent = 'Додано!';
        addToCartBtn.classList.add('bg-madder');

        setTimeout(() => {
            cartLabel.textContent = original;
            addToCartBtn.classList.remove('bg-madder');
        }, 1500);
    });

    wishlistBtn?.addEventListener('click', () => {
        const active = wishlistBtn.dataset.active === 'true';
        wishlistBtn.dataset.active = active ? 'false' : 'true';
        if (wishlistLabel) {
            wishlistLabel.textContent = active ? 'В обране' : 'В обраному';
        }
    });

    update();
}
