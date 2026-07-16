export function init(root) {
    const button = root.querySelector('[data-wishlist-button]');
    const icon = root.querySelector('[data-wishlist-icon]');
    const label = root.querySelector('[data-wishlist-label]');

    if (!button || !icon) {
        return;
    }

    const activeClasses = (button.dataset.activeClass || '').split(' ').filter(Boolean);
    const inactiveClasses = (button.dataset.inactiveClass || '').split(' ').filter(Boolean);

    const applyState = (isWishlisted) => {
        button.classList.remove(...activeClasses, ...inactiveClasses);
        button.classList.add(...(isWishlisted ? activeClasses : inactiveClasses));

        const text = isWishlisted ? root.dataset.removeLabel : root.dataset.addLabel;
        button.setAttribute('aria-label', text);
        icon.setAttribute('fill', isWishlisted ? 'currentColor' : 'none');

        if (label) {
            label.textContent = text;
        }
    };

    root.addEventListener('submit', async (event) => {
        event.preventDefault();

        const response = await window.axios.post(
            root.action,
            {},
            { headers: { 'X-Requested-With': 'XMLHttpRequest' } },
        );

        applyState(Boolean(response.data.isWishlisted));
    });
}
