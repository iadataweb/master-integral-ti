document.addEventListener('DOMContentLoaded', function () {
    const base_url = document.querySelector('meta[name="base-url"]').getAttribute('content');
    const form = $('#formWindowsCredential');
    let submitUrl = base_url + '/administration/windows_credentials/insert_windows_credential';

    const table = $('#tableManageWindowsCredentials').DataTable({
        pagingType: 'simple_numbers',
        aProcessing: true,
        aServerSide: true,
        ajax: {
            url: base_url + '/administration/windows_credentials/show_windows_credentials',
            dataSrc: ''
        },
        language: languageDatatable(),
        columns: [
            { data: 'id_windows_credential' },
            { data: 'name_windows_credential' },
            { data: 'network_address_windows_credential' },
            { data: 'username_windows_credential' },
            { data: 'password_updated_at_windows_credential' },
            { data: 'status_active_windows_credential' },
            { data: 'options' }
        ],
        responsive: true,
        dom: 'Bfrtilp',
        buttons: [
            { extend: 'excelHtml5', text: '<i class="icon-file-excel"></i>', titleAttr: 'Exportar a Excel', className: 'btn btn-success', filename: 'Credenciales Windows' },
            { extend: 'pdfHtml5', text: '<i class="icon-file-pdf"></i>', titleAttr: 'Exportar a PDF', className: 'btn btn-danger', filename: 'Credenciales Windows' },
            { extend: 'print', text: '<i class="icon-print"></i>', titleAttr: 'Imprimir', className: 'btn btn-info', filename: 'Credenciales Windows' }
        ],
        destroy: true,
        order: [[0, 'desc']]
    });

    $('#btnNew').on('click', function () {
        submitUrl = base_url + '/administration/windows_credentials/insert_windows_credential';
        form.trigger('reset');
        $('#input-id-windows-credential').val('');
        $('#input-current-password-updated-at-windows-credential').val('');
        $('#labelWindowsCredential').text('Nuevo Registro');
        $('#btnSubmitWindowsCredential').text('Guardar');
        $('.js-password-help').hide();
        $('#ModalCRUD').modal('show');
    });

    $(document).on('click', '.js-btn-edit', function () {
        const id = $(this).data('id');
        submitUrl = base_url + '/administration/windows_credentials/update_windows_credential';
        readCredential(id, 'edit');
    });

    $(document).on('click', '.js-btn-view', function () {
        readCredential($(this).data('id'), 'view');
    });

    $(document).on('click', '.js-btn-history', function () {
        loadHistory($(this).data('id'));
    });

    function readCredential(id, mode) {
        $.ajax({
            url: base_url + '/administration/windows_credentials/show_windows_credential',
            type: 'POST',
            dataType: 'json',
            data: { id_windows_credential: id },
            success: function (response) {
                if (!response.status) {
                    showMessage(response.type, response.message);
                    return;
                }
                const item = response.result;
                if (mode === 'edit') {
                    form.trigger('reset');
                    $('#input-id-windows-credential').val(item.id_windows_credential);
                    $('#input-name-windows-credential').val(item.name_windows_credential);
                    $('#input-network-address-windows-credential').val(item.network_address_windows_credential);
                    $('#input-username-windows-credential').val(item.username_windows_credential);
                    $('#textarea-description-windows-credential').val(item.description_windows_credential);
                    $('#select-status-active-windows-credential').val(item.status_active_windows_credential);
                    $('#input-current-password-updated-at-windows-credential').val(item.password_updated_at_windows_credential || '');
                    $('#input-password-updated-at-windows-credential').val(toDateInput(item.password_updated_at_windows_credential));
                    $('#input-password-windows-credential').val('');
                    $('#labelWindowsCredential').text('Actualizar Registro');
                    $('#btnSubmitWindowsCredential').text('Actualizar');
                    $('.js-password-help').show();
                    $('#ModalCRUD').modal('show');
                    return;
                }
                $('#see-name-windows-credential').text(item.name_windows_credential || 'No definido');
                $('#see-network-address-windows-credential').text(item.network_address_windows_credential || 'No definido');
                $('#see-username-windows-credential').text(item.username_windows_credential || 'No definido');
                $('#see-password-windows-credential').attr('type', 'password').val(item.password_plain_windows_credential || '');
                $('#see-password-updated-at-windows-credential').text(item.password_updated_at_windows_credential || 'No definido');
                $('#see-description-windows-credential').text(item.description_windows_credential || 'Ninguna.');
                $('#see-status-windows-credential').html(item.status_active_windows_credential == 1 ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>');
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
            title: 'Eliminar credencial',
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
                url: base_url + '/administration/windows_credentials/delete_windows_credential',
                type: 'POST',
                dataType: 'json',
                data: { id_windows_credential: id },
                success: function (response) {
                    showMessage(response.type, response.message);
                    if (response.status) table.ajax.reload(null, false);
                }
            });
        });
    });

    function loadHistory(id) {
        $('#tbody-windows-password-history').html(rowLoading(5));
        $.ajax({
            url: base_url + '/administration/windows_credentials/show_windows_credential_history',
            type: 'POST',
            dataType: 'json',
            data: { id_windows_credential: id },
            success: function (response) {
                if (!response.status) {
                    showMessage(response.type, response.message);
                    return;
                }
                renderPasswordHistory('#tbody-windows-password-history', response.password_history, 'password_fingerprint_windows_credential_password_history', 'observation_windows_credential_password_history', 'date_created_windows_credential_password_history');
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
                <td>${formatRecordId(item, 'id_windows_credential_password_history')}</td>
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
