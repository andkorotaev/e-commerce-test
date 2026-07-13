import './bootstrap';

const modules = import.meta.glob('./components/**/*.js');

document.querySelectorAll('[data-component]').forEach((el) => {
    const name = el.dataset.component;
    const path = `./components/${name}.js`;
    const load = modules[path];

    if (!load) {
        console.warn(`No JS module found for component "${name}"`);
        return;
    }

    load().then((mod) => mod.init?.(el));
});
