/**
 * Turns a `x-front.checkout.search-select` markup block into a type-to-filter
 * combobox. Options can be provided upfront (`data-options`, a static JSON
 * list — used for cities) or supplied later via `setOptions()` (used for the
 * delivery-point select, whose list depends on the chosen city + delivery
 * type and is fetched on demand).
 */
function initSearchSelect(root) {
    const input = root.querySelector('[data-search-select-input]');
    const list = root.querySelector('[data-search-select-list]');

    if (!input || !list) {
        return null;
    }

    let options = JSON.parse(input.dataset.options || '[]');

    const render = () => {
        const query = input.value.trim().toLowerCase();
        const matches = (query === ''
            ? options
            : options.filter((option) => option.toLowerCase().includes(query))
        ).slice(0, 30);

        if (matches.length === 0) {
            list.classList.add('hidden');
            list.innerHTML = '';

            return;
        }

        list.innerHTML = matches
            .map((option) => `<li data-search-select-option class="cursor-pointer px-3 py-2 hover:bg-stone/10">${option}</li>`)
            .join('');
        list.classList.remove('hidden');
    };

    input.addEventListener('input', render);
    input.addEventListener('focus', render);

    list.addEventListener('mousedown', (event) => {
        const optionEl = event.target.closest('[data-search-select-option]');

        if (!optionEl) {
            return;
        }

        input.value = optionEl.textContent;
        list.classList.add('hidden');
        input.dispatchEvent(new Event('change'));
    });

    input.addEventListener('blur', () => {
        setTimeout(() => list.classList.add('hidden'), 150);
    });

    return {
        setOptions(newOptions) {
            options = newOptions;
        },
    };
}

export function init(root) {
    const deliverySection = root.querySelector('[data-delivery-section]');
    const cityWrapper = deliverySection?.querySelector('[data-city-select] [data-search-select]');
    const pointWrapper = root.querySelector('[data-delivery-point-wrapper]');
    const pointSelectEl = pointWrapper?.querySelector('[data-search-select]');
    const pointLabel = pointWrapper?.querySelector('label');
    const pointInput = pointSelectEl?.querySelector('[data-search-select-input]');
    const addressWrapper = root.querySelector('[data-delivery-address-wrapper]');
    const addressInput = addressWrapper?.querySelector('input[name="address"]');

    const cityInput = cityWrapper?.querySelector('[data-search-select-input]');
    initSearchSelect(cityWrapper);
    const pointSelectApi = pointSelectEl ? initSearchSelect(pointSelectEl) : null;

    const deliveryPointsUrl = deliverySection?.dataset.deliveryPointsUrl;
    let pointFetchToken = 0;

    const refreshDeliveryPoints = async () => {
        const type = root.querySelector('[data-delivery-type]:checked')?.value;
        const city = cityInput?.value.trim();

        if (!deliveryPointsUrl || !pointSelectApi || !city || (type !== 'branch' && type !== 'postomat')) {
            return;
        }

        const token = ++pointFetchToken;
        const response = await fetch(`${deliveryPointsUrl}?${new URLSearchParams({ city, type })}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });

        if (token !== pointFetchToken || !response.ok) {
            return;
        }

        pointSelectApi.setOptions(await response.json());
    };

    const updateDeliverySections = () => {
        const selected = root.querySelector('[data-delivery-type]:checked');
        const needsPoint = selected?.dataset.needsPoint === '1';
        const needsAddress = selected?.value === 'address';

        if (pointInput) {
            pointInput.value = '';
        }

        if (pointWrapper) {
            pointWrapper.classList.toggle('hidden', !needsPoint);

            if (pointInput) {
                pointInput.required = needsPoint;
            }
        }

        if (pointLabel && selected) {
            pointLabel.textContent = selected.value === 'postomat' ? 'Номер поштомату' : 'Номер відділення';
        }

        if (addressWrapper) {
            addressWrapper.classList.toggle('hidden', !needsAddress);
        }

        if (addressInput) {
            addressInput.required = needsAddress;
        }

        if (needsPoint) {
            refreshDeliveryPoints();
        }
    };

    root.querySelectorAll('[data-delivery-type]').forEach((input) => {
        input.addEventListener('change', updateDeliverySections);
    });

    cityInput?.addEventListener('change', () => {
        if (pointInput) {
            pointInput.value = '';
        }

        refreshDeliveryPoints();
    });

    updateDeliverySections();

    // Payment is rendered as checkboxes (per spec) but only one method can
    // ever apply to an order — checking one unchecks any other.
    const paymentCheckboxes = root.querySelectorAll('[data-payment-checkbox]');
    paymentCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', () => {
            if (checkbox.checked) {
                paymentCheckboxes.forEach((other) => {
                    if (other !== checkbox) {
                        other.checked = false;
                    }
                });
            }
        });
    });

    const createAccountCheckbox = root.querySelector('[data-create-account-checkbox]');
    const passwordFields = root.querySelector('[data-password-fields]');

    createAccountCheckbox?.addEventListener('change', () => {
        passwordFields?.classList.toggle('hidden', !createAccountCheckbox.checked);
    });
}
