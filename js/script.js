document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.recipe-card[data-recipe-url]').forEach((card) => {
        const openRecipe = () => {
            window.location.href = card.dataset.recipeUrl;
        };

        card.addEventListener('click', (event) => {
            if (!event.target.closest('a, button, form')) openRecipe();
        });

        card.addEventListener('keydown', (event) => {
            if ((event.key === 'Enter' || event.key === ' ') && event.target === card) {
                event.preventDefault();
                openRecipe();
            }
        });
    });

    const menuToggle = document.querySelector('.menu-toggle');
    const nav = document.querySelector('.site-nav');
    if (menuToggle && nav) {
        menuToggle.addEventListener('click', () => {
            const open = nav.classList.toggle('is-open');
            menuToggle.setAttribute('aria-expanded', String(open));
        });
    }

    const ingredientList = document.querySelector('#ingredient-list');
    const addIngredient = document.querySelector('#add-ingredient');
    const categorySelect = document.querySelector('#category-select');

    const getUnitOptions = () => {
        const selectedOption = categorySelect?.selectedOptions[0];
        if (!selectedOption?.dataset.unitOptions) return [];

        try {
            const units = JSON.parse(selectedOption.dataset.unitOptions);
            return Array.isArray(units) ? units : [];
        } catch (error) {
            return [];
        }
    };

    const escapeHtml = (value) => String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

    const unitSelectMarkup = (selectedUnit = '') => {
        const options = getUnitOptions();
        const optionMarkup = options.map((unit) => {
            const selected = unit === selectedUnit ? ' selected' : '';
            const safeUnit = escapeHtml(unit);
            return `<option value="${safeUnit}"${selected}>${safeUnit}</option>`;
        }).join('');

        return `<select class="unit-select" name="unit[]" aria-label="Unit" required><option value="">Unit</option>${optionMarkup}<option value="__add_custom_unit__">+ Add custom unit</option></select>`;
    };

    const unitControlMarkup = () => unitSelectMarkup();

    const sortUnitOptions = (select) => {
        const selectedValue = select.value;
        const placeholder = select.querySelector('option[value=""]');
        const customOption = select.querySelector('option[value="__add_custom_unit__"]');
        const unitOptions = [...select.options]
            .filter((option) => option.value && option.value !== '__add_custom_unit__')
            .sort((first, second) => first.text.localeCompare(second.text, undefined, {
                numeric: true,
                sensitivity: 'base',
            }));

        select.replaceChildren(placeholder, ...unitOptions, customOption);
        select.value = selectedValue;
    };

    const refreshUnitSelects = () => {
        document.querySelectorAll('.unit-select').forEach((select) => {
            select.outerHTML = unitSelectMarkup();
        });
    };

    if (categorySelect) {
        categorySelect.addEventListener('change', refreshUnitSelects);
    }

    if (ingredientList && addIngredient) {
        addIngredient.addEventListener('click', () => {
            const row = document.createElement('div');
            row.className = 'ingredient-row';
            row.innerHTML = `<input type="text" name="quantity[]" placeholder="1/2" aria-label="Quantity" required><div class="unit-control">${unitControlMarkup()}</div><input class="ingredient-name" type="text" name="ingredient_name[]" placeholder="All-purpose flour" aria-label="Ingredient name" required><button class="icon-button remove-ingredient" type="button" aria-label="Remove ingredient">&times;</button>`;
            ingredientList.appendChild(row);
            row.querySelector('input').focus();
        });
        ingredientList.addEventListener('click', (event) => {
            if (event.target.classList.contains('remove-ingredient')) {
                const rows = ingredientList.querySelectorAll('.ingredient-row');
                if (rows.length > 1) event.target.closest('.ingredient-row').remove();
            }
        });
    }

    const unitModal = document.querySelector('#unit-modal');
    const unitForm = document.querySelector('#unit-form');
    const customUnitInput = document.querySelector('#custom-unit');
    let activeUnitSelect = null;
    let previousUnit = '';

    document.querySelectorAll('.unit-select').forEach((select) => {
        select.dataset.previousValue = select.value;
    });

    const closeUnitModal = () => {
        if (!unitModal) return;
        unitModal.hidden = true;
        activeUnitSelect = null;
    };

    if (ingredientList && unitModal && unitForm && customUnitInput) {
        ingredientList.addEventListener('change', (event) => {
            const unitSelect = event.target.closest('.unit-select');
            if (!unitSelect) return;

            if (unitSelect.value === '__add_custom_unit__') {
                activeUnitSelect = unitSelect;
                previousUnit = unitSelect.dataset.previousValue || '';
                unitModal.hidden = false;
                customUnitInput.value = '';
                customUnitInput.focus();
                unitSelect.value = previousUnit;
                return;
            }

            unitSelect.dataset.previousValue = unitSelect.value;
        });

        unitForm.addEventListener('submit', (event) => {
            event.preventDefault();

            const trimmedUnit = customUnitInput.value.trim();
            const categoryId = categorySelect?.value;
            if (!trimmedUnit || trimmedUnit.length > 50 || !activeUnitSelect || !categoryId) {
                window.alert('Choose a category and enter a unit of 50 characters or fewer.');
                return;
            }

            const baseUrl = document.querySelector('meta[name="base-url"]').content;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            fetch(`${baseUrl}/actions/unit_add.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: new URLSearchParams({ category_id: categoryId, unit: trimmedUnit }),
            })
                .then(async (response) => {
                    const result = await response.json();
                    if (!response.ok || !result.success) {
                        throw new Error(result.message || 'The unit could not be saved.');
                    }
                    return result;
                })
                .then((result) => {
                    if (![...activeUnitSelect.options].some((option) => option.value === result.unit)) {
                        activeUnitSelect.add(new Option(result.unit, result.unit));
                    }
                    sortUnitOptions(activeUnitSelect);
                    activeUnitSelect.value = result.unit;
                    activeUnitSelect.dataset.previousValue = result.unit;
                    closeUnitModal();
                })
                .catch((error) => window.alert(error.message));
        });

        unitModal.addEventListener('click', (event) => {
            if (event.target.matches('[data-close-unit-modal]')) closeUnitModal();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !unitModal.hidden) closeUnitModal();
        });
    }

    const confirmModal = document.querySelector('#confirm-modal');
    const confirmTitle = document.querySelector('#confirm-modal-title');
    const confirmMessage = document.querySelector('#confirm-modal-message');
    const confirmAction = document.querySelector('#confirm-action');
    const confirmCancel = document.querySelector('#confirm-cancel');
    let activeConfirmTarget = null;

    const closeConfirmModal = () => {
        if (!confirmModal) return;
        confirmModal.hidden = true;
        activeConfirmTarget = null;
    };

    const showConfirmModal = (target) => {
        const isLogout = target.matches('a');
        activeConfirmTarget = target;
        confirmTitle.textContent = isLogout ? 'Log out of your account?' : 'Remove this item?';
        confirmMessage.textContent = target.dataset.confirm;
        confirmAction.textContent = isLogout ? 'Yes, log out' : 'Delete';
        confirmCancel.textContent = isLogout ? 'No, stay logged in' : 'Cancel';
        confirmAction.classList.toggle('danger-button', !isLogout);
        confirmModal.hidden = false;
        confirmAction.focus();
    };

    if (confirmModal && confirmTitle && confirmMessage && confirmAction && confirmCancel) {
        document.querySelectorAll('[data-confirm]').forEach((target) => {
            if (target.matches('form')) {
                target.addEventListener('submit', (event) => {
                    event.preventDefault();
                    showConfirmModal(target);
                });
            }

            if (target.matches('a')) {
                target.addEventListener('click', (event) => {
                    event.preventDefault();
                    showConfirmModal(target);
                });
            }
        });

        confirmAction.addEventListener('click', () => {
            if (!activeConfirmTarget) return;
            if (activeConfirmTarget.matches('form')) {
                activeConfirmTarget.submit();
            } else if (activeConfirmTarget.matches('a')) {
                window.location.href = activeConfirmTarget.href;
            }
        });

        confirmModal.addEventListener('click', (event) => {
            if (event.target.matches('[data-close-confirm-modal]')) closeConfirmModal();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !confirmModal.hidden) closeConfirmModal();
        });
    }

    const favoriteButton = document.querySelector('[data-favorite-id]');
    if (favoriteButton) {
        const baseUrl = document.querySelector('meta[name="base-url"]').content;
        favoriteButton.addEventListener('click', async () => {
            const saved = favoriteButton.dataset.favoriteState === 'saved';
            favoriteButton.disabled = true;
            try {
                const response = await fetch(`${baseUrl}/actions/favorite_${saved ? 'remove' : 'add'}.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: new URLSearchParams({ recipe_id: favoriteButton.dataset.favoriteId })
                });
                const result = await response.json();
                if (!response.ok || !result.success) throw new Error(result.message || 'Unable to update favorites.');
                favoriteButton.dataset.favoriteState = result.saved ? 'saved' : 'unsaved';
                favoriteButton.classList.toggle('is-favorite', result.saved);
                favoriteButton.textContent = result.saved ? '★ Saved' : '☆ Save favorite';
                favoriteButton.setAttribute('aria-label', result.message);
            } catch (error) {
                window.alert(error.message);
            } finally {
                favoriteButton.disabled = false;
            }
        });
    }
});
