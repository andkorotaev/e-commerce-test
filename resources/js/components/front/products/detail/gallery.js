const ZOOM_SCALE = 2;

export function init(root) {
    const mainImage = root.querySelector('[data-gallery-main]');
    const thumbs = root.querySelectorAll('[data-gallery-thumb]');
    const zoomContainer = root.querySelector('[data-gallery-zoom]');

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

    if (!mainImage || !zoomContainer || matchMedia('(pointer: coarse)').matches) {
        return;
    }

    zoomContainer.addEventListener('mousemove', (event) => {
        const rect = zoomContainer.getBoundingClientRect();
        const xPercent = ((event.clientX - rect.left) / rect.width) * 100;
        const yPercent = ((event.clientY - rect.top) / rect.height) * 100;

        mainImage.style.transformOrigin = `${xPercent}% ${yPercent}%`;
        mainImage.style.transform = `scale(${ZOOM_SCALE})`;
    });

    zoomContainer.addEventListener('mouseleave', () => {
        mainImage.style.transform = 'scale(1)';
    });
}
