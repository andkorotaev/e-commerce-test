export function init(root) {
    const mainImage = root.querySelector('[data-gallery-main]');
    const thumbs = root.querySelectorAll('[data-gallery-thumb]');

    thumbs.forEach((thumb) => {
        thumb.addEventListener('click', () => {
            if (!mainImage) {
                return;
            }

            mainImage.src = thumb.dataset.full;

            thumbs.forEach((el) => delete el.dataset.active);
            thumb.dataset.active = 'true';
        });
    });
}
