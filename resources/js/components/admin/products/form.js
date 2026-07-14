export function init(root) {
    const container = root.querySelector('[data-variants-container]');
    const template = root.querySelector('[data-variant-row-template]');
    const addButton = root.querySelector('[data-add-variant]');

    if (!container || !template || !addButton) {
        return;
    }

    let counter = 0;

    addButton.addEventListener('click', () => {
        const html = template.innerHTML.replaceAll('__KEY__', `new-${counter++}`);
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html.trim();
        container.appendChild(wrapper.firstElementChild);
    });

    container.addEventListener('click', (event) => {
        const removeButton = event.target.closest('[data-remove-variant]');

        if (removeButton) {
            removeButton.closest('[data-variant-row]').remove();
        }
    });
}
