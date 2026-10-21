document.addEventListener('DOMContentLoaded', function () {
    const baseUrl = document.querySelector('meta[name="base-url"]')?.getAttribute('content') || '';
    const btnCloseOthers = document.getElementById('btnCloseOtherSessions');
    let tablaDataTable;

    if (!baseUrl || typeof $ === 'undefined' || !$.fn.DataTable) {
        return;
    }

    tablaDataTable = $('#tableBrowserSessions').DataTable({
        "pagingType": "simple_numbers",
        "aProcessing": true,
        "ajax": {
            "url": baseUrl + "/account/list_browser_sessions",
            "type": "POST",
            "dataSrc": function (response) {
                if (!response || !response.status) {
                    showMessage((response && response.type) || 'error', (response && response.message) || 'No se pudieron cargar las sesiones.');
                    renderCurrentSession(null);
                    return [];
                }
                renderCurrentSession((response.result || []).find(function (row) {
                    return row.is_current === true;
                }) || null);
                return response.result || [];
            }
        },
        "language": languageDatatable(),
        "columns": [
            {"data":"id_user_browser_session"},
            {
                "data": "session_id",
                "render": function (data) {
                    const sessionId = normalize(data);
                    const shortSession = sessionId.substring(0, 24) + (sessionId.length > 24 ? '...' : '');
                    return '<span class="text-nowrap" title="' + escapeHtml(sessionId) + '">' + escapeHtml(shortSession || '-') + '</span>';
                }
            },
            {
                "data": "ip_address",
                "render": function (data) {
                    return escapeHtml(normalize(data) || '-');
                }
            },
            {
                "data": "user_agent",
                "render": function (data) {
                    return '<span class="d-block text-wrap" style="min-width: 260px;">' + escapeHtml(normalize(data) || '-') + '</span>';
                }
            },
            {
                "data": "date_last_activity_user_browser_session",
                "render": function (data) {
                    return '<span class="text-nowrap">' + escapeHtml(formatDate(data)) + '</span>';
                }
            },
            {
                "data": null,
                "render": function (data, type, row) {
                    const statusLabel = normalize(row.status_label) || 'Desconocido';
                    const statusClass = statusLabel.toLowerCase() === 'activa' ? 'bg-success' : 'bg-secondary';
                    return '<span class="badge ' + statusClass + '">' + escapeHtml(statusLabel) + '</span>';
                }
            },
            {
                "data": null,
                "render": function (data, type, row) {
                    return buildActionButton(row.is_current === true, normalize(row.session_id), normalize(row.status_label) || 'Desconocido');
                }
            }
        ],
        "columnDefs": [
            { "targets": [0], "responsivePriority": 1 },
            { "targets": [6], "orderable": false, "searchable": false, "responsivePriority": 1 },
            { "targets": [1], "responsivePriority": 2 },
            { "targets": [5], "responsivePriority": 3 },
            { "targets": [4], "responsivePriority": 4 },
            { "targets": [3], "responsivePriority": 6 }
        ],
        "responsive": true,
        "destroy": true,
        "idisplaylength": 10,
        "order": [[4, "desc"]]
    });

    function request(path, params) {
        return fetch(baseUrl + path, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            },
            body: new URLSearchParams(params || {}).toString(),
        })
            .then(function (response) { return response.text(); })
            .then(function (text) {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    return { status: false, type: 'error', message: 'Respuesta invalida del servidor.' };
                }
            });
    }

    function normalize(value) {
        return (value || '').toString().trim();
    }

    function formatDate(rawDate) {
        const value = normalize(rawDate);
        if (!value) {
            return '-';
        }
        const date = new Date(value.replace(' ', 'T'));
        if (Number.isNaN(date.getTime())) {
            return value;
        }
        return date.toLocaleString('es-PE');
    }

    function buildActionButton(isCurrent, sessionId, statusLabel) {
        const isOpen = statusLabel.toLowerCase() === 'activa';
        if (isCurrent) {
            return '<button type="button" class="btn btn-sm btn-success d-inline-flex align-items-center" disabled><i class="bx bx-check-circle me-1"></i>Sesion actual</button>';
        }
        if (!isOpen) {
            return '<button type="button" class="btn btn-sm btn-outline-gray-600 d-inline-flex align-items-center" disabled><i class="bx bx-lock-alt me-1"></i>Sesion cerrada</button>';
        }
        return '<button type="button" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center js-btn-close-session" data-session-id="' + escapeHtml(sessionId) + '"><i class="bx bx-log-out-circle me-1"></i>Cerrar sesion</button>';
    }

    function renderCurrentSession(row) {
        if (!row) {
            $('#current-session-id').text('No identificado');
            $('#current-session-ip').text('-');
            $('#current-session-activity').text('-');
            $('#current-session-agent').text('No se pudo identificar la sesion actual.');
            $('#current-session-status')
                .removeClass('bg-success bg-secondary')
                .addClass('bg-secondary')
                .text('No identificado');
            return;
        }

        const sessionId = normalize(row.session_id);
        const shortSession = sessionId.substring(0, 32) + (sessionId.length > 32 ? '...' : '');
        const statusLabel = normalize(row.status_label) || 'Activa';
        const isActive = statusLabel.toLowerCase() === 'activa';

        $('#current-session-id').text(shortSession || '-').attr('title', sessionId);
        $('#current-session-ip').text(normalize(row.ip_address) || '-');
        $('#current-session-activity').text(formatDate(row.date_last_activity_user_browser_session));
        $('#current-session-agent').text(normalize(row.user_agent) || '-');
        $('#current-session-status')
            .removeClass('bg-success bg-secondary')
            .addClass(isActive ? 'bg-success' : 'bg-secondary')
            .text(statusLabel);
    }

    $(document).on('click', '.js-btn-close-session', function () {
        const sessionId = normalize(this.dataset.sessionId);
        if (!sessionId) {
            return;
        }

        askPasswordConfirmation({
            title: 'Cerrar sesion remota?',
            text: 'La sesion seleccionada se cerrara inmediatamente.',
            confirmButtonText: 'Cerrar sesion',
        }).then(function (result) {
            if (result.isConfirmed) {
                closeOneSession(sessionId, result.value);
            }
        });
    });

    function askPasswordConfirmation(options) {
        return Swal.fire({
            title: options.title,
            text: options.text,
            icon: 'warning',
            input: 'password',
            inputLabel: 'Contrasena actual',
            inputPlaceholder: 'Ingrese su contrasena',
            inputAttributes: {
                autocomplete: 'current-password',
                autocapitalize: 'off',
                autocorrect: 'off',
            },
            showCancelButton: true,
            confirmButtonText: options.confirmButtonText,
            cancelButtonText: 'Cancelar',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-danger m-2',
                cancelButton: 'btn btn-gray-600 m-2',
                input: 'form-control',
            },
            preConfirm: function (password) {
                const currentPassword = normalize(password);
                if (!currentPassword) {
                    Swal.showValidationMessage('Ingrese su contrasena para confirmar.');
                    return false;
                }
                return currentPassword;
            },
        });
    }

    function closeOneSession(sessionId, currentPassword) {
        request('/account/close_browser_session', {
            session_id: sessionId,
            current_password: currentPassword,
        })
            .then(function (response) {
                if (!response.status) {
                    showMessage(response.type || 'error', response.message || 'No se pudo cerrar la sesion.');
                    return;
                }
                showMessage(response.type || 'success', response.message || 'Sesion cerrada.');
                tablaDataTable.ajax.reload(null, false);
            })
            .catch(function () {
                showMessage('error', 'Ocurrio un error al cerrar la sesion.');
            });
    }

    function closeOtherSessions(currentPassword) {
        request('/account/close_other_browser_sessions', {
            current_password: currentPassword,
        })
            .then(function (response) {
                if (!response.status) {
                    showMessage(response.type || 'error', response.message || 'No se pudieron cerrar las sesiones remotas.');
                    return;
                }
                showMessage(response.type || 'success', response.message || 'Sesiones remotas cerradas.');
                tablaDataTable.ajax.reload(null, false);
            })
            .catch(function () {
                showMessage('error', 'Ocurrio un error al cerrar las sesiones remotas.');
            });
    }

    if (btnCloseOthers) {
        btnCloseOthers.addEventListener('click', function () {
            askPasswordConfirmation({
                title: 'Cerrar todas las otras sesiones?',
                text: 'Se cerraran todas las sesiones activas en otros navegadores o dispositivos.',
                confirmButtonText: 'Cerrar todas',
            }).then(function (result) {
                if (result.isConfirmed) {
                    closeOtherSessions(result.value);
                }
            });
        });
    }
});

function escapeHtml(value) {
    return (value || '')
        .toString()
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
