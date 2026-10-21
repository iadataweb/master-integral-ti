document.addEventListener('DOMContentLoaded', function () {
    const base_url = document.querySelector('meta[name="base-url"]').getAttribute('content');
    const form = $('#formAssetWindowsCredentials');
    let current_asset_id = null;

    $(document).on('click', '.js-btn-asset-windows-credentials', function () {
        current_asset_id = $(this).attr('data-id-asset');
        form.trigger('reset');
        $('#input-id-asset-windows-credential-assignment').val(current_asset_id);
        loadAssetWindowsCredentials(current_asset_id);
        $('#ModalAssetWindowsCredentials').modal('show');
    });

    form.on('submit', function (event) {
        event.preventDefault();
        $.ajax({
            url: base_url + '/administration/windows_credentials/assign_asset_windows_credentials',
            type: 'POST',
            dataType: 'json',
            data: form.serialize(),
            success: function (response) {
                showMessage(response.type, response.message);
                if (response.status) {
                    form.trigger('reset');
                    $('#input-id-asset-windows-credential-assignment').val(current_asset_id);
                    loadAssetWindowsCredentials(current_asset_id);
                }
            },
            error: function () {
                showMessage('error', 'Ocurrio un problema al procesar la solicitud.');
            }
        });
    });

    $(document).on('click', '.js-btn-release-asset-windows-credential', function () {
        const id_assignment = $(this).data('id');
        Swal.fire({
            title: 'Liberar credencial Windows',
            text: 'La credencial quedara desvinculada del activo.',
            icon: 'warning',
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: 'Liberar',
            cancelButtonText: 'Cancelar',
            customClass: { confirmButton: 'btn btn-danger m-2', cancelButton: 'btn btn-secondary m-2' },
            allowOutsideClick: false
        }).then((result) => {
            if (!result.value) return;
            $.ajax({
                url: base_url + '/administration/windows_credentials/release_asset_windows_credential',
                type: 'POST',
                dataType: 'json',
                data: { id_asset_windows_credential_assignment: id_assignment },
                success: function (response) {
                    showMessage(response.type, response.message);
                    if (response.status) {
                        loadAssetWindowsCredentials(current_asset_id);
                    }
                }
            });
        });
    });

    function loadAssetWindowsCredentials(id_asset) {
        $('#select-asset-windows-credentials').empty().append('<option disabled>Cargando credenciales...</option>');
        $('#tbody-asset-current-windows-credentials').html('<tr><td colspan="6" class="text-muted text-center">Cargando...</td></tr>');
        $('#tbody-asset-windows-credential-history').html('<tr><td colspan="5" class="text-muted text-center">Cargando...</td></tr>');
        $.ajax({
            url: base_url + '/administration/windows_credentials/asset_windows_credential_data',
            type: 'POST',
            dataType: 'json',
            data: { id_asset: id_asset },
            success: function (response) {
                if (!response.status) {
                    showMessage(response.type, response.message);
                    return;
                }
                renderAvailable(response.available || []);
                renderCurrent(response.current || []);
                renderHistory(response.history || []);
            }
        });
    }

    function renderAvailable(rows) {
        const $select = $('#select-asset-windows-credentials');
        $select.empty();
        if (rows.length === 0) {
            $select.append('<option disabled>No hay accesos de Windows disponibles</option>');
            return;
        }
        rows.forEach((item) => {
            const label = `${item.name_windows_credential} - ${item.username_windows_credential} (${item.network_address_windows_credential})`;
            $select.append(`<option value="${item.id_windows_credential}">${escapeHtml(label)}</option>`);
        });
    }

    function renderCurrent(rows) {
        const $tbody = $('#tbody-asset-current-windows-credentials');
        $tbody.empty();
        if (rows.length === 0) {
            $tbody.html('<tr><td colspan="6" class="text-muted text-center">Sin accesos de Windows asignados.</td></tr>');
            return;
        }
        rows.forEach((item, index) => {
            $tbody.append(`
                <tr>
                    <td>${formatRecordId(item, 'id_asset_windows_credential_assignment')}</td>
                    <td>${escapeHtml(item.name_windows_credential)}</td>
                    <td>${escapeHtml(item.network_address_windows_credential)}</td>
                    <td><span class="badge bg-info">${escapeHtml(item.username_windows_credential)}</span></td>
                    <td>${escapeHtml(item.date_assigned_asset_windows_credential_assignment)}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-danger js-btn-release-asset-windows-credential" data-id="${item.id_asset_windows_credential_assignment}">Liberar</button>
                    </td>
                </tr>
            `);
        });
    }

    function renderHistory(rows) {
        const $tbody = $('#tbody-asset-windows-credential-history');
        $tbody.empty();
        if (rows.length === 0) {
            $tbody.html('<tr><td colspan="5" class="text-muted text-center">Sin historial registrado.</td></tr>');
            return;
        }
        rows.forEach((item, index) => {
            $tbody.append(`
                <tr>
                    <td>${formatRecordId(item, 'id_asset_windows_credential_assignment_history')}</td>
                    <td>${escapeHtml(item.name_windows_credential)}</td>
                    <td>${actionBadge(item.action_asset_windows_credential_assignment_history)}</td>
                    <td>${escapeHtml(item.display_name || 'Sistema')}</td>
                    <td>${escapeHtml(item.date_created_asset_windows_credential_assignment_history)}</td>
                </tr>
            `);
        });
    }

    function actionBadge(action) {
        if (action === 'assigned') return '<span class="badge bg-success">Asignada</span>';
        if (action === 'released') return '<span class="badge bg-secondary">Liberada</span>';
        return `<span class="badge bg-info">${escapeHtml(action)}</span>`;
    }

    function escapeHtml(value) {
        return (value || '').toString()
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
});