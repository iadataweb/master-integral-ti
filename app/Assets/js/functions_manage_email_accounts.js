document.addEventListener('DOMContentLoaded', function () {
    const base_url = document.querySelector('meta[name="base-url"]').getAttribute('content');
    const form = $('#formEmailAccount');
    let submitUrl = base_url + '/administration/email_accounts/insert_email_account';

    const table = $('#tableManageEmailAccounts').DataTable({
        pagingType: 'simple_numbers',
        aProcessing: true,
        aServerSide: true,
        ajax: {
            url: base_url + '/administration/email_accounts/show_email_accounts',
            dataSrc: ''
        },
        language: languageDatatable(),
        columns: [
            { data: 'id_email_account' },
            { data: 'name_email_account' },
            { data: 'email_email_account' },
            { data: 'phone_email_account' },
            { data: 'status_email_account' },
            { data: 'date_updated_email_account' },
            { data: 'options' }
        ],
        responsive: true,
        dom: 'Bfrtilp',
        buttons: [
            { extend: 'excelHtml5', text: '<i class="icon-file-excel"></i>', titleAttr: 'Exportar a Excel', className: 'btn btn-success', filename: 'Correos Electronicos' },
            { extend: 'pdfHtml5', text: '<i class="icon-file-pdf"></i>', titleAttr: 'Exportar a PDF', className: 'btn btn-danger', filename: 'Correos Electronicos' },
            { extend: 'print', text: '<i class="icon-print"></i>', titleAttr: 'Imprimir', className: 'btn btn-info', filename: 'Correos Electronicos' }
        ],
        destroy: true,
        order: [[0, 'desc']]
    });

    $('#btnNew').on('click', function () {
        submitUrl = base_url + '/administration/email_accounts/insert_email_account';
        form.trigger('reset');
        $('#input-id-email-account').val('');
        $('#labelEmailAccount').text('Nuevo Registro');
        $('#btnSubmitEmailAccount').text('Guardar');
        resetPasswordVisibility('#input-password-email-account');
        $('.js-password-help').hide();
        $('#ModalCRUD').modal('show');
    });

    $(document).on('click', '.js-btn-edit', function () {
        submitUrl = base_url + '/administration/email_accounts/update_email_account';
        readAccount($(this).data('id'), 'edit');
    });

    $(document).on('click', '.js-btn-view', function () {
        readAccount($(this).data('id'), 'view');
    });

    $(document).on('click', '.js-btn-history', function () {
        loadHistory($(this).data('id'));
    });

    function readAccount(id, mode) {
        $.ajax({
            url: base_url + '/administration/email_accounts/show_email_account',
            type: 'POST',
            dataType: 'json',
            data: { id_email_account: id },
            success: function (response) {
                if (!response.status) {
                    showMessage(response.type, response.message);
                    return;
                }
                const item = response.result;
                if (mode === 'edit') {
                    form.trigger('reset');
                    $('#input-id-email-account').val(item.id_email_account);
                    $('#input-name-email-account').val(item.name_email_account);
                    $('#input-email-email-account').val(item.email_email_account);
                    $('#input-phone-email-account').val(item.phone_email_account);
                    $('#textarea-description-email-account').val(item.description_email_account);
                    $('#select-status-email-account').val(item.status_email_account);
                    $('#input-password-email-account').val('');
                    $('#labelEmailAccount').text('Actualizar Registro');
                    $('#btnSubmitEmailAccount').text('Actualizar');
                    resetPasswordVisibility('#input-password-email-account');
                    $('.js-password-help').show();
                    $('#ModalCRUD').modal('show');
                    return;
                }
                $('#see-name-email-account').text(item.name_email_account || 'No definido');
                $('#see-email-email-account').text(item.email_email_account || 'No definido');
                $('#see-phone-email-account').text(item.phone_email_account || 'No definido');
                $('#see-password-email-account').val(item.password_plain_email_account || '');
                resetPasswordVisibility('#see-password-email-account');
                $('#see-date-updated-email-account').text(item.date_updated_email_account || 'No definido');
                $('#see-description-email-account').text(item.description_email_account || 'Ninguna.');
                $('#see-status-email-account').html(statusBadge(item.status_email_account));
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
            title: 'Eliminar correo',
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
                url: base_url + '/administration/email_accounts/delete_email_account',
                type: 'POST',
                dataType: 'json',
                data: { id_email_account: id },
                success: function (response) {
                    showMessage(response.type, response.message);
                    if (response.status) table.ajax.reload(null, false);
                }
            });
        });
    });

    function loadHistory(id) {
        $('#tbody-email-password-history').html(rowLoading(5));
        $.ajax({
            url: base_url + '/administration/email_accounts/show_email_account_history',
            type: 'POST',
            dataType: 'json',
            data: { id_email_account: id },
            success: function (response) {
                if (!response.status) {
                    showMessage(response.type, response.message);
                    return;
                }
                renderPasswordHistory('#tbody-email-password-history', response.password_history, 'password_fingerprint_email_account_password_history', 'observation_email_account_password_history', 'date_created_email_account_password_history');
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
                <td>${formatRecordId(item, 'id_email_account_password_history')}</td>
                <td><code>${escapeHtml((item[fingerprintKey] || '').substring(0, 16))}...</code></td>
                <td>${escapeHtml(item.display_name || 'Sistema')}</td>
                <td>${escapeHtml(item[observationKey] || '')}</td>
                <td>${escapeHtml(item[dateKey] || '')}</td>
            </tr>`);
        });
    }



    function statusBadge(status) {
        const map = {
            active: '<span class="badge bg-success">Activo</span>',
            inactive: '<span class="badge bg-secondary">Inactivo</span>',
            suspended: '<span class="badge bg-warning text-dark">Suspendido</span>'
        };
        return map[status] || '<span class="badge bg-secondary">No definido</span>';
    }

    function rowLoading(cols) {
        return `<tr><td colspan="${cols}" class="text-center text-muted">Cargando...</td></tr>`;
    }

    function resetPasswordVisibility(targetSelector) {
        const target = $(targetSelector);
        target.attr('type', 'password');
        $(`.js-toggle-password[data-target="${targetSelector}"] i`).removeClass('bx-hide').addClass('bx-show');
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
