document.addEventListener('DOMContentLoaded', function () {
    applyEnhancements(document);
    observeDynamicNodes();
});

function applyEnhancements(root) {
    const scope = root || document;

    scope.querySelectorAll('.modal').forEach(function (modal) {
        if (!modal.dataset.uiEnhanced) {
            modal.classList.add('modal-modern');
            modal.dataset.uiEnhanced = '1';
        }
    });

    normalizeSelectorActionButtons(scope);
    normalizeTableActionButtons(scope);
    upgradeSeeModalStructure(scope);

    scope.querySelectorAll('.card').forEach(function (card) {
        if (!card.dataset.uiEnhancedCard) {
            card.classList.add('card-modern');
            card.dataset.uiEnhancedCard = '1';
        }
    });

    scope.querySelectorAll('table.table').forEach(function (table) {
        if (!table.dataset.uiEnhancedTable) {
            table.classList.add('table-modern');
            table.dataset.uiEnhancedTable = '1';
        }
    });

    scope.querySelectorAll('form').forEach(function (form) {
        if (!form.dataset.uiEnhancedForm) {
            form.classList.add('form-modern');
            form.dataset.uiEnhancedForm = '1';
        }
    });
}

// Convención global para acciones de filas, incluidas tablas AJAX y filas responsivas.
function normalizeTableActionButtons(root) {
    const candidates = [];
    if (root.matches && root.matches('button, a.btn')) candidates.push(root);
    root.querySelectorAll('button, a.btn').forEach(button => candidates.push(button));
    const actions = [
        {key:'view', selector:'.js-btn-view, .js-btn-view-history, .js-location-detail', icon:'bx-eye', label:'Ver'},
        {key:'edit', selector:'.js-btn-edit', icon:'bx-pencil', label:'Editar'},
        {key:'delete', selector:'.js-btn-delete, .js-btn-trash-delete-hard', icon:'bx-trash', label:'Eliminar'},
        {key:'more', selector:'.js-btn-menu, .js-movement-actions', icon:'bx-dots-horizontal-rounded', label:'Más opciones'},
        {key:'move', selector:'.js-btn-move', icon:'bx-transfer', label:'Mover'}
    ];
    candidates.forEach(button => {
        if (!button.closest('table') || button.dataset.uiRowAction) return;
        const label = button.textContent.trim();
        const action = actions.find(item => button.matches(item.selector) || button.querySelector('.' + item.icon) || label === item.label);
        if (!action) return;
        const title = button.getAttribute('title') || action.label;
        button.dataset.uiRowAction = action.key;
        button.classList.add('ui-row-action', 'icon');
        button.setAttribute('title', title);
        button.setAttribute('aria-label', title);
        if (button.tagName === 'BUTTON' && !button.hasAttribute('type')) button.type = 'button';
        const icon = document.createElement('i');
        icon.className = 'bx ' + action.icon;
        icon.setAttribute('aria-hidden','true');
        if (action.key === 'move') {
            const svg = document.createElementNS('http://www.w3.org/2000/svg','svg');
            svg.setAttribute('viewBox','0 0 24 24');svg.setAttribute('width','16');svg.setAttribute('height','16');svg.setAttribute('fill','none');svg.setAttribute('stroke','currentColor');svg.setAttribute('stroke-width','2');svg.setAttribute('aria-hidden','true');
            const path = document.createElementNS('http://www.w3.org/2000/svg','path');path.setAttribute('d','M4 7h15m-4-4 4 4-4 4M20 17H5m4-4-4 4 4 4');svg.append(path);button.replaceChildren(svg);
        } else button.replaceChildren(icon);
    });
}

function normalizeSelectorActionButtons(root) {
    const scope = root || document;
    const selector = 'button span[id^="btnSubmitAdd"], button span[id^="btnSubmitAdditional"]';
    const candidates = [];

    if (scope.matches && scope.matches(selector)) {
        candidates.push(scope);
    }

    scope.querySelectorAll(selector).forEach(function (span) {
        candidates.push(span);
    });

    candidates.forEach(function (span) {
        const button = span.closest('button');
        if (!button || button.dataset.selectorButtonEnhanced) {
            return;
        }

        if (!button.id && span.id) {
            button.id = span.id;
            span.removeAttribute('id');
        }

        if (!button.getAttribute('type') || button.getAttribute('type').toLowerCase() === 'submit') {
            button.setAttribute('type', 'button');
        }

        span.classList.add('btn-label');
        button.dataset.selectorButtonEnhanced = '1';
    });
}

function upgradeSeeModalStructure(root) {
    const scope = root || document;
    const modalSelector = '#ModalSee, #ModalSeeHistory';
    const modals = [];

    if (scope.matches && scope.matches(modalSelector)) {
        modals.push(scope);
    }

    scope.querySelectorAll(modalSelector).forEach(function (modal) {
        modals.push(modal);
    });

    modals.forEach(function (modal) {
        if (modal.dataset.uiSeeStructureEnhanced) {
            return;
        }

        if (modal.querySelector('.asset-layout, .history-layout, .modal-detail-layout')) {
            modal.dataset.uiSeeStructureEnhanced = '1';
            return;
        }

        const table = modal.querySelector('.modal-body > .table-responsive > table.table-bordered:not(.asset-table):not(.history-table):not(.modal-attachments-table)');
        if (!table || !table.tBodies || table.tBodies.length === 0) {
            return;
        }

        const rows = Array.from(table.tBodies[0].rows || []);
        if (rows.length === 0) {
            return;
        }

        const layout = document.createElement('div');
        layout.className = 'modal-detail-layout';

        const primarySection = createModalDetailSection('Datos principales');
        const additionalSection = createModalDetailSection('Detalles adicionales');

        rows.forEach(function (row, index) {
            const cells = row.querySelectorAll('td, th');
            if (cells.length < 2) {
                return;
            }

            const label = cells[0].textContent.trim();
            const valueCell = cells[1];
            const card = document.createElement('article');
            card.className = 'modal-detail-card';

            if (shouldUseWideDetailCard(label, valueCell)) {
                card.classList.add('modal-detail-card-wide');
            }

            const labelElement = document.createElement('span');
            labelElement.className = 'modal-detail-label';
            labelElement.textContent = label;

            const valueElement = document.createElement('div');
            valueElement.className = 'modal-detail-value';
            while (valueCell.firstChild) {
                valueElement.appendChild(valueCell.firstChild);
            }

            card.appendChild(labelElement);
            card.appendChild(valueElement);

            const targetGrid = index < 6 ? primarySection.grid : additionalSection.grid;
            targetGrid.appendChild(card);
        });

        if (primarySection.grid.children.length > 0) {
            layout.appendChild(primarySection.section);
        }

        if (additionalSection.grid.children.length > 0) {
            layout.appendChild(additionalSection.section);
        }

        const wrapper = table.closest('.table-responsive');
        wrapper.replaceWith(layout);
        modal.dataset.uiSeeStructureEnhanced = '1';
    });
}

function shouldUseWideDetailCard(label, valueCell) {
    const normalizedLabel = (label || '').toString().toLowerCase();
    const wideLabelTerms = [
        'descripcion',
        'descripción',
        'observacion',
        'observación',
        'detalle',
        'comentario',
        'mensaje',
        'solucion',
        'solución',
        'diagnostico',
        'diagnóstico',
    ];

    return (
        valueCell.querySelector('.text-wrap') ||
        valueCell.textContent.length > 90 ||
        wideLabelTerms.some(function (term) {
            return normalizedLabel.indexOf(term) !== -1;
        })
    );
}

function createModalDetailSection(title) {
    const section = document.createElement('section');
    section.className = 'modal-detail-section';

    const heading = document.createElement('h6');
    heading.className = 'modal-detail-section-title';
    heading.textContent = title;

    const grid = document.createElement('div');
    grid.className = 'modal-detail-grid';

    section.appendChild(heading);
    section.appendChild(grid);

    return { section: section, grid: grid };
}

function observeDynamicNodes() {
    if (!window.MutationObserver) {
        return;
    }

    const observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            mutation.addedNodes.forEach(function (node) {
                if (node.nodeType === 1) {
                    applyEnhancements(node);
                }
            });
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true,
    });
}

// Los registros no persistidos aún no tienen ID; no fabricar un número consecutivo.
function formatRecordId(row, field) {
    const value = row && row[field];
    return value !== null && value !== undefined && /^\d+$/.test(String(value)) ? String(value) : '—';
}
