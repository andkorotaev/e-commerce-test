export function init(root) {
    const backdrop = root.querySelector('[data-size-guide-backdrop]');
    const panel = root.querySelector('[data-size-guide-panel]');
    const closeButton = root.querySelector('[data-size-guide-close]');

    if (!backdrop || !panel) {
        return;
    }

    const open = () => {
        backdrop.classList.remove('opacity-0', 'pointer-events-none');
        panel.classList.remove('opacity-0', 'pointer-events-none');
        document.body.classList.add('overflow-hidden');
    };

    const close = () => {
        backdrop.classList.add('opacity-0', 'pointer-events-none');
        panel.classList.add('opacity-0', 'pointer-events-none');
        document.body.classList.remove('overflow-hidden');
    };

    window.addEventListener('size-guide:open', open);
    closeButton?.addEventListener('click', close);
    backdrop.addEventListener('click', close);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            close();
        }
    });
}
