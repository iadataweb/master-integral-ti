document.addEventListener('DOMContentLoaded', function () {
    const base_url = document.querySelector('meta[name="base-url"]').getAttribute('content');
    const form = $('#formAssetEmailAccounts');
    let current_asset_id = null;

    $(document).on('click', '.js-btn-asset-email-accounts', function () {
        current_asset_id = $(this).attr('data-id-asset');
        form.trigger('reset');
        $('#input-id-asset-email-account-assignment').val(current_asset_id);
        loadAssetEmailAccounts(current_asset_id);
        $('#ModalAssetEmailAccounts').modal('show');
    });

    form.on('submit', function (event) {
        event.preventDefault();
        $.ajax({
            url: base_url + '/administration/email_accounts/assign_asset_email_accounts',
            type: 'POST',
            dataType: 'json',
            data: form.serialize(),
            success: function (response) {
                showMessage(response.type, response.message);
                if (response.status) {
                    form.trigger('reset');
                    $('#input-id-asset-email-account-assignment').val(current_asset_id);
                    loadAssetEmailAccounts(current_asset_id);
                }
            },
            error: function () {
                showMessage('error', 'Ocurrio un problema al procesar la solicitud.');
            }
        });
    });

    $(document).on('click', '.js-btn-release-asset-email-account', function () {
        const id_assignment = $(this).data('id');
        Swal.fire({
            title: 'Liberar correo electronico',
            text: 'El correo quedara desvinculado del activo.',
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
                url: base_url + '/administration/email_accounts/release_asset_email_account',
                type: 'POST',
                dataType: 'json',
                data: { id_asset_email_account_assignment: id_assignment },
                success: function (response) {
                    showMessage(response.type, response.message);
                    if (response.status) {
                        loadAssetEmailAccounts(current_asset_id);
                    }
                }
            });
        });
    });

    function loadAssetEmailAccounts(id_asset) {
        $('#select-asset-email-accounts').empty().append('<option disabled>Cargando correos...</option>');
        $('#tbody-asset-current-email-accounts').html('<tr><td colspan="6" class="text-muted text-center">Cargando...</td></tr>');
        $('#tbody-asset-email-account-history').html('<tr><td colspan="6" class="text-muted text-center">Cargando...</td></tr>');
        $.ajax({
            url: base_url + '/administration/email_accounts/asset_email_account_data',
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
        const $select = $('#select-asset-email-accounts');
        $select.empty();
        if (rows.length === 0) {
            $select.append('<option disabled>No hay correos electronicos disponibles</option>');
            return;
        }
        rows.forEach((item) => {
            const label = `${item.name_email_account} - ${item.email_email_account}`;
            $select.append(`<option value="${item.id_email_account}">${escapeHtml(label)}</option>`);
        });
    }

    function renderCurrent(rows) {
        const $tbody = $('#tbody-asset-current-email-accounts');
        $tbody.empty();
        if (rows.length === 0) {
            $tbody.html('<tr><td colspan="6" class="text-muted text-center">Sin correos electronicos asignados.</td></tr>');
            return;
        }
        rows.forEach((item, index) => {
            $tbody.append(`
                <tr>
                    <td>${formatRecordId(item, 'id_asset_email_account_assignment')}</td>
                    <td>${escapeHtml(item.name_email_account)}</td>
                    <td><span class="badge bg-info">${escapeHtml(item.email_email_account)}</span></td>
                    <td>${escapeHtml(item.phone_email_account || 'No definido')}</td>
                    <td>${escapeHtml(item.date_assigned_asset_email_account_assignment)}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-danger js-btn-release-asset-email-account" data-id="${item.id_asset_email_account_assignment}">Liberar</button>
                    </td>
                </tr>
            `);
        });
    }

    function renderHistory(rows) {
        const $tbody = $('#tbody-asset-email-account-history');
        $tbody.empty();
        if (rows.length === 0) {
            $tbody.html('<tr><td colspan="6" class="text-muted text-center">Sin historial registrado.</td></tr>');
            return;
        }
        rows.forEach((item, index) => {
            $tbody.append(`
                <tr>
                    <td>${formatRecordId(item, 'id_asset_email_account_assignment_history')}</td>
                    <td>${escapeHtml(item.name_email_account)}</td>
                    <td>${escapeHtml(item.email_email_account)}</td>
                    <td>${actionBadge(item.action_asset_email_account_assignment_history)}</td>
                    <td>${escapeHtml(item.display_name || 'Sistema')}</td>
                    <td>${escapeHtml(item.date_created_asset_email_account_assignment_history)}</td>
                </tr>
            `);
        });
    }

    function actionBadge(action) {
        if (action === 'assigned') return '<span class="badge bg-success">Asignado</span>';
        if (action === 'released') return '<span class="badge bg-secondary">Liberado</span>';
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