export function init(root) {
    const track = root.querySelector('[data-scroll-track]');
    const prevButton = root.querySelector('[data-scroll-prev]');
    const nextButton = root.querySelector('[data-scroll-next]');

    if (!track) {
        return;
    }

    const scrollByCard = (direction) => {
        const card = track.querySelector(':scope > div');
        const amount = (card?.getBoundingClientRect().width ?? 260) + 24;

        track.scrollBy({ left: amount * direction, behavior: 'smooth' });
    };

    prevButton?.addEventListener('click', () => scrollByCard(-1));
    nextButton?.addEventListener('click', () => scrollByCard(1));
}
