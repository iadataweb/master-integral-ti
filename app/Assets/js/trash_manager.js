document.addEventListener('DOMContentLoaded', function () {
    const baseUrlMeta = document.querySelector('meta[name="base-url"]');
    const baseUrl = baseUrlMeta ? baseUrlMeta.getAttribute('content') : '';
    if (!baseUrl) {
        return;
    }

    ensureTrashModal();
    transformToolbarButtons();
    bindTrashActions();

    function transformToolbarButtons() {
        const candidateButtons = Array.from(document.querySelectorAll('.btn.btn-sm.btn-outline-gray-600'));
        candidateButtons.forEach((button) => {
            const text = (button.textContent || '').trim().toLowerCase();
            if (text === 'exportar') {
                button.textContent = 'Papelera';
                button.classList.add('js-btn-trash-global');
            }
            if (text === 'papelera') {
                button.classList.add('js-btn-trash-global');
            }
        });

        const groups = document.querySelectorAll('.btn-toolbar .btn-group');
        groups.forEach((group) => {
            const trashButtons = group.querySelectorAll('.js-btn-trash-global');
            if (trashButtons.length <= 1) {
                return;
            }
            trashButtons.forEach((button, index) => {
                if (index > 0) {
                    button.remove();
                }
            });
        });
    }

    function resolveTrashKey() {
        const segments = window.location.pathname.split('/').filter(Boolean);
        if (segments.length < 2) {
            return '';
        }
        return segments[0] + '/' + segments[1];
    }

    function ensureTrashModal() {
        if (document.getElementById('ModalTrashGlobal')) {
            return;
        }
        const html = `
            <div class="modal fade" id="ModalTrashGlobal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="trash-modal-title">Papelera</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 90px;">#</th>
                                            <th>Registro</th>
                                            <th style="width: 170px;">Fecha</th>
                                            <th style="width: 210px;">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-trash-records">
                                        <tr><td colspan="4" class="text-center text-muted">Sin registros.</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', html);
    }

    function bindTrashActions() {
        $(document).on('click', '.js-btn-trash-global', function () {
            const trashKey = resolveTrashKey();
            if (!trashKey) {
                showMessage('error', 'No se pudo identificar la papelera de esta interfaz.');
                return;
            }
            loadTrashRecords(trashKey);
        });

        $(document).on('click', '.js-btn-trash-restore', function () {
            const trashKey = $(this).data('trash-key');
            const idRecord = Number($(this).data('id-record'));
            if (!trashKey || !idRecord) {
                return;
            }

            $.post(baseUrl + '/account/trash_restore', { trash_key: trashKey, id_record: idRecord }, function (response) {
                const parsed = parseResponse(response);
                if (!parsed.status) {
                    showMessage(parsed.type || 'error', parsed.message || 'No se pudo restaurar.');
                    return;
                }
                showMessage(parsed.type || 'success', parsed.message || 'Registro restaurado.');
                loadTrashRecords(trashKey, true);
                reloadDataTables();
            });
        });

        $(document).on('click', '.js-btn-trash-delete-hard', function () {
            const trashKey = $(this).data('trash-key');
            const idRecord = Number($(this).data('id-record'));
            if (!trashKey || !idRecord) {
                return;
            }

            Swal.fire({
                title: 'Eliminar definitivamente?',
                text: 'Esta accion no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-danger m-2',
                    cancelButton: 'btn btn-gray-600 m-2'
                }
            }).then((result) => {
                if (!result.isConfirmed) {
                    return;
                }
                $.post(baseUrl + '/account/trash_delete_permanent', { trash_key: trashKey, id_record: idRecord }, function (response) {
                    const parsed = parseResponse(response);
                    if (!parsed.status) {
                        showMessage(parsed.type || 'error', parsed.message || 'No se pudo eliminar definitivamente.');
                        return;
                    }
                    showMessage(parsed.type || 'success', parsed.message || 'Registro eliminado definitivamente.');
                    loadTrashRecords(trashKey, true);
                    reloadDataTables();
                });
            });
        });
    }

    function loadTrashRecords(trashKey, keepOpen) {
        $.post(baseUrl + '/account/trash_list', { trash_key: trashKey }, function (response) {
            const parsed = parseResponse(response);
            if (!parsed.status) {
                showMessage(parsed.type || 'error', parsed.message || 'No se pudo cargar la papelera.');
                return;
            }
            renderTrashTable(parsed.result || [], parsed.trash_key, parsed.allow_hard_delete === true);
            $('#trash-modal-title').text(parsed.title || 'Papelera');
            if (!keepOpen) {
                $('#ModalTrashGlobal').modal('show');
            }
        });
    }

    function renderTrashTable(rows, trashKey, allowHardDelete) {
        const tbody = $('#tbody-trash-records');
        tbody.empty();
        if (!Array.isArray(rows) || rows.length === 0) {
            tbody.append('<tr><td colspan="4" class="text-center text-muted">Sin registros eliminados.</td></tr>');
            return;
        }

        rows.forEach((row) => {
            const id = Number(row.id_record);
            const label = escapeHtml((row.label_record || '').toString());
            const created = escapeHtml((row.created_record || '').toString());
            const hardDeleteButton = allowHardDelete
                ? `<button type="button" class="btn btn-sm btn-outline-danger js-btn-trash-delete-hard" data-trash-key="${trashKey}" data-id-record="${id}">Eliminar</button>`
                : `<button type="button" class="btn btn-sm btn-outline-danger" disabled title="No habilitado para este modulo">Eliminar</button>`;

            tbody.append(`
                <tr>
                    <td>${id}</td>
                    <td>${label}</td>
                    <td>${created}</td>
                    <td class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-success js-btn-trash-restore" data-trash-key="${trashKey}" data-id-record="${id}">Restaurar</button>
                        ${hardDeleteButton}
                    </td>
                </tr>
            `);
        });
    }

    function parseResponse(response) {
        if (typeof response === 'string') {
            try {
                return JSON.parse(response);
            } catch (e) {
                return { status: false, type: 'error', message: 'Respuesta invalida del servidor.' };
            }
        }
        return response || { status: false, type: 'error', message: 'Respuesta vacia del servidor.' };
    }

    function escapeHtml(value) {
        return value
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function reloadDataTables() {
        if (typeof $ === 'undefined' || !$.fn || !$.fn.dataTable) {
            return;
        }
        const apiTables = $.fn.dataTable.tables({ visible: true, api: true });
        apiTables.every(function () {
            const settings = this.settings()[0];
            if (settings && settings.ajax) {
                this.ajax.reload(null, false);
            }
        });
    }
});
