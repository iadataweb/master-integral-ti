document.addEventListener('DOMContentLoaded', function () {
    const baseUrlMeta = document.querySelector('meta[name="base-url"]');
    const baseUrl = baseUrlMeta ? baseUrlMeta.getAttribute('content') : '';
    const form = document.getElementById('formRoleAccess');

    const getReadCheckbox = (card) => card ? card.querySelector('input[data-permission="read"]') : null;
    const getDependentCheckboxes = (card) => {
        if (!card) return [];
        const read = getReadCheckbox(card);
        return Array.from(card.querySelectorAll('input[type="checkbox"]')).filter((input) => input !== read);
    };
    const updateModuleState = (card) => {
        const read = getReadCheckbox(card);
        if (!card || !read) return;
        card.classList.toggle('is-disabled', !read.checked);
        const state = card.querySelector('[data-module-state]');
        if (state) {
            state.textContent = read.checked ? 'Visible' : 'Oculto';
            state.classList.toggle('bg-success', read.checked);
            state.classList.toggle('bg-secondary', !read.checked);
        }
    };
    const ensureModuleVisible = (input) => {
        const card = input.closest('[data-module-card]');
        const read = getReadCheckbox(card);
        if (read && input.checked) {
            read.checked = true;
            updateModuleState(card);
        }
    };

    document.querySelectorAll('[data-module-card]').forEach((card) => updateModuleState(card));

    document.querySelectorAll('.js-module-check-all').forEach((button) => {
        button.addEventListener('click', function () {
            const card = this.closest('[data-module-card]');
            if (!card) return;
            card.querySelectorAll('input[type="checkbox"]').forEach((input) => input.checked = true);
            updateModuleState(card);
        });
    });

    document.querySelectorAll('.js-module-uncheck-all').forEach((button) => {
        button.addEventListener('click', function () {
            const card = this.closest('[data-module-card]');
            if (!card) return;
            card.querySelectorAll('input[type="checkbox"]').forEach((input) => input.checked = false);
            updateModuleState(card);
        });
    });

    document.querySelectorAll('.js-control-group-check-all').forEach((button) => {
        button.addEventListener('click', function () {
            const group = this.closest('[data-control-group]');
            if (!group) return;
            group.querySelectorAll('input[type="checkbox"]').forEach((input) => {
                input.checked = true;
                ensureModuleVisible(input);
            });
        });
    });

    document.querySelectorAll('.js-control-group-uncheck-all').forEach((button) => {
        button.addEventListener('click', function () {
            const group = this.closest('[data-control-group]');
            if (!group) return;
            group.querySelectorAll('input[type="checkbox"]').forEach((input) => input.checked = false);
        });
    });

    document.querySelectorAll('[data-module-card] input[type="checkbox"]').forEach((input) => {
        input.addEventListener('change', function () {
            const card = this.closest('[data-module-card]');
            const read = getReadCheckbox(card);
            if (this === read && !this.checked) {
                getDependentCheckboxes(card).forEach((dependent) => dependent.checked = false);
            } else if (this !== read && this.checked) {
                ensureModuleVisible(this);
            }
            updateModuleState(card);
        });
    });

    if (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            const formData = new FormData(form);
            fetch(`${baseUrl}/administration/roles/save_access`, {
                method: 'POST',
                body: formData
            })
                .then((response) => response.json())
                .then((response) => {
                    showMessage(response.type, response.message);
                })
                .catch((error) => {
                    console.error('Error:', error);
                    showMessage('error', 'Ocurrio un problema al guardar los permisos.');
                });
        });
    }
});