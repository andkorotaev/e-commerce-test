export function init(root) {
    const backdrop = root.querySelector('[data-search-modal-backdrop]');
    const panel = root.querySelector('[data-search-modal-panel]');
    const closeButton = root.querySelector('[data-search-modal-close]');
    const input = root.querySelector('[data-search-modal-input]');
    const triggers = document.querySelectorAll('[data-search-trigger]');

    if (!backdrop || !panel) {
        return;
    }

    let isOpen = false;

    const open = () => {
        window.dispatchEvent(new CustomEvent('front-modal:opening', { detail: { name: 'search' } }));

        panel.classList.remove('-translate-y-full');
        backdrop.classList.remove('opacity-0', 'pointer-events-none');
        document.body.classList.add('overflow-hidden');
        isOpen = true;

        input?.focus();
    };

    const close = () => {
        panel.classList.add('-translate-y-full');
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
        if (isOpen && event.detail?.name !== 'search') {
            close();
        }
    });
}
