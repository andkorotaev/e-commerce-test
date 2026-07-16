export function init(root) {
    const backdrop = root.querySelector('[data-wishlist-modal-backdrop]');
    const panel = root.querySelector('[data-wishlist-modal-panel]');
    const closeButton = root.querySelector('[data-wishlist-modal-close]');
    const contents = root.querySelector('[data-wishlist-modal-contents]');
    const triggers = document.querySelectorAll('[data-wishlist-trigger]');

    if (!backdrop || !panel) {
        return;
    }

    let isOpen = false;

    const loadContents = async () => {
        const fetchUrl = contents?.dataset.wishlistFetchUrl;

        if (!fetchUrl) {
            return;
        }

        const response = await window.axios.get(fetchUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });
        contents.innerHTML = response.data;
    };

    const open = () => {
        window.dispatchEvent(new CustomEvent('front-modal:opening', { detail: { name: 'wishlist' } }));

        panel.classList.remove('translate-x-full');
        backdrop.classList.remove('opacity-0', 'pointer-events-none');
        document.body.classList.add('overflow-hidden');
        isOpen = true;

        loadContents();
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
        if (isOpen && event.detail?.name !== 'wishlist') {
            close();
        }
    });
}
