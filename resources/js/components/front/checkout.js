export function init(root) {
    const pointWrapper = root.querySelector('[data-delivery-point-wrapper]');
    const pointLabel = root.querySelector('[data-delivery-point-label]');
    const pointInput = pointWrapper?.querySelector('input[name="delivery_point"]');

    const updateDeliveryPointVisibility = () => {
        const selected = root.querySelector('[data-delivery-type]:checked');
        const needsPoint = selected?.dataset.needsPoint === '1';

        if (!pointWrapper) {
            return;
        }

        pointWrapper.classList.toggle('hidden', !needsPoint);

        if (pointInput) {
            pointInput.required = needsPoint;
        }

        if (pointLabel && selected) {
            pointLabel.textContent = selected.value === 'postomat' ? 'Номер поштомату' : 'Номер відділення';
        }
    };

    root.querySelectorAll('[data-delivery-type]').forEach((input) => {
        input.addEventListener('change', updateDeliveryPointVisibility);
    });

    updateDeliveryPointVisibility();

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
