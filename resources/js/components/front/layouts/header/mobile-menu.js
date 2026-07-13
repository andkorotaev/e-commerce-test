export function init(root) {
    const trigger = root.querySelector('[data-mobile-menu-trigger]');
    const panel = root.querySelector('[data-mobile-menu-panel]');
    const backdrop = root.querySelector('[data-mobile-menu-backdrop]');
    const closeButton = root.querySelector('[data-mobile-menu-close]');

    if (!trigger || !panel || !backdrop) {
        return;
    }

    const open = () => {
        panel.classList.remove('translate-x-full');
        backdrop.classList.remove('opacity-0', 'pointer-events-none');
        document.body.classList.add('overflow-hidden');
        trigger.setAttribute('aria-expanded', 'true');
    };

    const close = () => {
        panel.classList.add('translate-x-full');
        backdrop.classList.add('opacity-0', 'pointer-events-none');
        document.body.classList.remove('overflow-hidden');
        trigger.setAttribute('aria-expanded', 'false');
    };

    trigger.addEventListener('click', open);
    closeButton?.addEventListener('click', close);
    backdrop.addEventListener('click', close);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && trigger.getAttribute('aria-expanded') === 'true') {
            close();
        }
    });
}
