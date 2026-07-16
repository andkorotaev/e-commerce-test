export function init(root) {
    const slides = [...root.querySelectorAll('[data-hero-slide]')];
    const dots = [...root.querySelectorAll('[data-hero-dot]')];

    if (slides.length < 2) {
        return;
    }

    let current = 0;
    let timer;

    const show = (index) => {
        slides.forEach((slide, i) => {
            const active = i === index;
            slide.classList.toggle('opacity-100', active);
            slide.classList.toggle('opacity-0', !active);
            slide.classList.toggle('pointer-events-none', !active);
        });

        dots.forEach((dot, i) => {
            dot.dataset.active = i === index ? 'true' : 'false';
        });

        current = index;
    };

    const next = () => show((current + 1) % slides.length);

    const stopAutoplay = () => clearInterval(timer);

    const startAutoplay = () => {
        stopAutoplay();

        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        timer = setInterval(next, 6000);
    };

    dots.forEach((dot) => {
        dot.addEventListener('click', () => {
            show(Number(dot.dataset.index));
            startAutoplay();
        });
    });

    startAutoplay();
}
