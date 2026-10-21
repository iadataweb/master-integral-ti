document.addEventListener('DOMContentLoaded', function () {
    const base_url = document.querySelector('meta[name="base-url"]').getAttribute('content');
    const form = $('#formOsUserAccount');
    let submitUrl = base_url + '/administration/os_user_accounts/insert_os_user_account';

    const table = $('#tableManageOsUserAccounts').DataTable({
        pagingType: 'simple_numbers',
        aProcessing: true,
        aServerSide: true,
        ajax: {
            url: base_url + '/administration/os_user_accounts/show_os_user_accounts',
            dataSrc: ''
        },
        language: languageDatatable(),
        columns: [
            { data: 'id_os_user_account' },
            { data: 'name_os_user_account' },
            { data: 'username_os_user_account' },
            { data: 'password_updated_at_os_user_account' },
            { data: 'status_active_os_user_account' },
            { data: 'options' }
        ],
        responsive: true,
        dom: 'Bfrtilp',
        buttons: [
            { extend: 'excelHtml5', text: '<i class="icon-file-excel"></i>', titleAttr: 'Exportar a Excel', className: 'btn btn-success', filename: 'Cuentas de Usuario' },
            { extend: 'pdfHtml5', text: '<i class="icon-file-pdf"></i>', titleAttr: 'Exportar a PDF', className: 'btn btn-danger', filename: 'Cuentas de Usuario' },
            { extend: 'print', text: '<i class="icon-print"></i>', titleAttr: 'Imprimir', className: 'btn btn-info', filename: 'Cuentas de Usuario' }
        ],
        destroy: true,
        order: [[0, 'desc']]
    });

    $('#btnNew').on('click', function () {
        submitUrl = base_url + '/administration/os_user_accounts/insert_os_user_account';
        form.trigger('reset');
        $('#input-id-os-user-account').val('');
        $('#input-current-password-updated-at-os-user-account').val('');
        $('#labelOsUserAccount').text('Nuevo Registro');
        $('#btnSubmitOsUserAccount').text('Guardar');
        $('.js-password-help').hide();
        $('#ModalCRUD').modal('show');
    });

    $(document).on('click', '.js-btn-edit', function () {
        const id = $(this).data('id');
        submitUrl = base_url + '/administration/os_user_accounts/update_os_user_account';
        readAccount(id, 'edit');
    });

    $(document).on('click', '.js-btn-view', function () {
        readAccount($(this).data('id'), 'view');
    });

    $(document).on('click', '.js-btn-history', function () {
        loadHistory($(this).data('id'));
    });

    function readAccount(id, mode) {
        $.ajax({
            url: base_url + '/administration/os_user_accounts/show_os_user_account',
            type: 'POST',
            dataType: 'json',
            data: { id_os_user_account: id },
            success: function (response) {
                if (!response.status) {
                    showMessage(response.type, response.message);
                    return;
                }
                const item = response.result;
                if (mode === 'edit') {
                    form.trigger('reset');
                    $('#input-id-os-user-account').val(item.id_os_user_account);
                    $('#input-name-os-user-account').val(item.name_os_user_account);
                    $('#input-username-os-user-account').val(item.username_os_user_account);
                    $('#textarea-description-os-user-account').val(item.description_os_user_account);
                    $('#select-status-active-os-user-account').val(item.status_active_os_user_account);
                    $('#input-current-password-updated-at-os-user-account').val(item.password_updated_at_os_user_account || '');
                    $('#input-password-updated-at-os-user-account').val(toDateInput(item.password_updated_at_os_user_account));
                    $('#input-password-os-user-account').val('');
                    $('#labelOsUserAccount').text('Actualizar Registro');
                    $('#btnSubmitOsUserAccount').text('Actualizar');
                    $('.js-password-help').show();
                    $('#ModalCRUD').modal('show');
                    return;
                }
                $('#see-name-os-user-account').text(item.name_os_user_account || 'No definido');
                $('#see-username-os-user-account').text(item.username_os_user_account || 'No definido');
                $('#see-password-os-user-account').attr('type', 'password').val(item.password_plain_os_user_account || '');
                $('#see-password-updated-at-os-user-account').text(item.password_updated_at_os_user_account || 'No definido');
                $('#see-description-os-user-account').text(item.description_os_user_account || 'Ninguna.');
                $('#see-status-os-user-account').html(item.status_active_os_user_account == 1 ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>');
                $('#ModalSee').modal('show');
            },
            error: function () {
                showMessage('error', 'Ocurrio un problema al leer el registro.');
            }
        });
    }

    form.on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: submitUrl,
            type: 'POST',
            dataType: 'json',
            data: form.serialize(),
            success: function (response) {
                showMessage(response.type, response.message);
                if (response.status) {
                    $('#ModalCRUD').modal('hide');
                    table.ajax.reload(null, false);
                }
            },
            error: function () {
                showMessage('error', 'Ocurrio un problema al procesar la solicitud.');
            }
        });
    });

    $(document).on('click', '.js-btn-delete', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Eliminar cuenta',
            text: 'El registro se movera a eliminado.',
            icon: 'warning',
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar',
            customClass: { confirmButton: 'btn btn-danger m-2', cancelButton: 'btn btn-secondary m-2' },
            allowOutsideClick: false
        }).then((result) => {
            if (!result.value) return;
            $.ajax({
                url: base_url + '/administration/os_user_accounts/delete_os_user_account',
                type: 'POST',
                dataType: 'json',
                data: { id_os_user_account: id },
                success: function (response) {
                    showMessage(response.type, response.message);
                    if (response.status) table.ajax.reload(null, false);
                }
            });
        });
    });

    function loadHistory(id) {
        $('#tbody-os-user-password-history').html(rowLoading(5));
        $.ajax({
            url: base_url + '/administration/os_user_accounts/show_os_user_account_history',
            type: 'POST',
            dataType: 'json',
            data: { id_os_user_account: id },
            success: function (response) {
                if (!response.status) {
                    showMessage(response.type, response.message);
                    return;
                }
                renderPasswordHistory('#tbody-os-user-password-history', response.password_history, 'password_fingerprint_os_user_account_password_history', 'observation_os_user_account_password_history', 'date_created_os_user_account_password_history');
                $('#ModalHistory').modal('show');
            }
        });
    }

    $(document).on('click', '.js-toggle-password', function () {
        const target = $($(this).data('target'));
        const type = target.attr('type') === 'password' ? 'text' : 'password';
        target.attr('type', type);
        $(this).find('i').toggleClass('bx-show bx-hide');
    });

    function renderPasswordHistory(target, rows, fingerprintKey, observationKey, dateKey) {
        const tbody = $(target);
        tbody.empty();
        if (!rows || rows.length === 0) {
            tbody.html('<tr><td colspan="5" class="text-center text-muted">Sin registros.</td></tr>');
            return;
        }
        rows.forEach((item, index) => {
            tbody.append(`<tr>
                <td>${formatRecordId(item, 'id_os_user_account_password_history')}</td>
                <td><code>${escapeHtml((item[fingerprintKey] || '').substring(0, 16))}...</code></td>
                <td>${escapeHtml(item.display_name || 'Sistema')}</td>
                <td>${escapeHtml(item[observationKey] || '')}</td>
                <td>${escapeHtml(item[dateKey] || '')}</td>
            </tr>`);
        });
    }



    function rowLoading(cols) {
        return `<tr><td colspan="${cols}" class="text-center text-muted">Cargando...</td></tr>`;
    }

    function toDateInput(value) {
        return value ? value.substring(0, 10) : '';
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