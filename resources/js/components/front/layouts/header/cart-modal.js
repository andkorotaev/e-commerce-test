export function init(root) {
    const backdrop = root.querySelector('[data-cart-modal-backdrop]');
    const panel = root.querySelector('[data-cart-modal-panel]');
    const closeButton = root.querySelector('[data-cart-modal-close]');
    const triggers = document.querySelectorAll('[data-cart-trigger]');

    if (!backdrop || !panel) {
        return;
    }

    let isOpen = false;

    const open = () => {
        // Dispatched before this modal's own DOM changes, so if another
        // header modal is currently open its close() (which clears
        // overflow-hidden) runs first — otherwise the two closures could
        // race and leave body scroll unlocked while this panel is visible.
        window.dispatchEvent(new CustomEvent('front-modal:opening', { detail: { name: 'cart' } }));

        panel.classList.remove('translate-x-full');
        backdrop.classList.remove('opacity-0', 'pointer-events-none');
        document.body.classList.add('overflow-hidden');
        isOpen = true;

        window.dispatchEvent(new CustomEvent('cart-modal:open'));
    };

    const close = () => {
        panel.classList.add('translate-x-full');
        backdrop.classList.add('opacity-0', 'pointer-events-none');
        document.body.classList.remove('overflow-hidden');
        isOpen = false;
    };

    triggers.forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
            event.preventDefault();
            open();
        });
    });

    closeButton?.addEventListener('click', close);
    backdrop.addEventListener('click', close);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && isOpen) {
            close();
        }
    });

    window.addEventListener('front-modal:opening', (event) => {
        if (isOpen && event.detail?.name !== 'cart') {
            close();
        }
    });
}
