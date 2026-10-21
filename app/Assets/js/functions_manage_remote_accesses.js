document.addEventListener('DOMContentLoaded', function () {
    const base_url = document.querySelector('meta[name="base-url"]').getAttribute('content');
    const form = $('#formRemoteAccess');
    let submitUrl = base_url + '/remote_access/remote_accesses/insert_remote_access';
    let tableSearchAsset = null;
    let applicationOptions = [];

    loadApplications();

    const table = $('#tableManageRemoteAccesses').DataTable({
        pagingType: 'simple_numbers',
        aProcessing: true,
        aServerSide: true,
        ajax: {
            url: base_url + '/remote_access/remote_accesses/show_remote_accesses',
            dataSrc: ''
        },
        language: languageDatatable(),
        columns: [
            { data: 'id_remote_access' },
            { data: 'asset_summary' },
            { data: 'equipment_username' },
            { data: 'current_location' },
            { data: 'applications_summary' },
            { data: 'status_active_remote_access' },
            { data: 'options' }
        ],
        responsive: true,
        dom: 'Bfrtilp',
        buttons: [
            { extend: 'excelHtml5', text: '<i class="icon-file-excel"></i>', titleAttr: 'Exportar a Excel', className: 'btn btn-success', filename: 'Acceso Remoto' },
            { extend: 'pdfHtml5', text: '<i class="icon-file-pdf"></i>', titleAttr: 'Exportar a PDF', className: 'btn btn-danger', filename: 'Acceso Remoto' },
            { extend: 'print', text: '<i class="icon-print"></i>', titleAttr: 'Imprimir', className: 'btn btn-info', filename: 'Acceso Remoto' }
        ],
        destroy: true,
        order: [[0, 'desc']]
    });

    $('#btnNew').on('click', function () {
        submitUrl = base_url + '/remote_access/remote_accesses/insert_remote_access';
        resetForm();
        addApplicationRow();
        $('#labelRemoteAccess').text('Nuevo Registro');
        $('#btnSubmitRemoteAccess').text('Guardar');
        $('#ModalCRUD').modal('show');
    });

    $('#btnAddRemoteApplication').on('click', function () {
        addApplicationRow();
    });

    $(document).on('click', '.btn-remove-remote-app', function () {
        if ($('.remote-app-row').length <= 1) {
            showMessage('warning', 'Debe conservar al menos una aplicacion.');
            return;
        }
        $(this).closest('.remote-app-row').remove();
        renumberApplicationRows();
    });

    $(document).on('click', '.js-btn-edit', function () {
        submitUrl = base_url + '/remote_access/remote_accesses/update_remote_access';
        readRemoteAccess($(this).data('id'), 'edit');
    });

    $(document).on('click', '.js-btn-view', function () {
        readRemoteAccess($(this).data('id'), 'view');
    });

    $(document).on('click', '.js-btn-history', function () {
        loadHistory($(this).data('id'));
    });

    $('.js-btn-search-asset-remote-access').on('click', function () {
        openAssetSearch();
    });

    $('#btnSubmitAddAssetRemoteAccess').on('click', function () {
        const row = getSelectedAssetRow();
        if (!row) {
            showMessage('warning', 'Seleccione un activo para agregar.');
            return;
        }
        fillAsset(row);
        $('#ModalSearchAssetRemoteAccess').modal('hide');
    });

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
            title: 'Eliminar acceso remoto',
            text: 'El registro principal y sus aplicaciones se moveran a eliminado.',
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
                url: base_url + '/remote_access/remote_accesses/delete_remote_access',
                type: 'POST',
                dataType: 'json',
                data: { id_remote_access: id },
                success: function (response) {
                    showMessage(response.type, response.message);
                    if (response.status) table.ajax.reload(null, false);
                },
                error: function () {
                    showMessage('error', 'No se pudo eliminar el registro.');
                }
            });
        });
    });

    function loadApplications() {
        $.ajax({
            url: base_url + '/remote_access/remote_accesses/show_remote_access_applications',
            type: 'GET',
            dataType: 'json',
            success: function (rows) {
                applicationOptions = rows || [];
                refreshApplicationSelects();
            }
        });
    }

    function applicationSelectHtml(selectedValue) {
        let html = '<select name="id_remote_access_application_remote_access_detail[]" class="form-control remote-app-select" required><option value="">Selecciona una opcion</option>';
        applicationOptions.forEach(function (item) {
            const selected = String(selectedValue || '') === String(item.id_remote_access_application) ? ' selected' : '';
            html += `<option value="${item.id_remote_access_application}"${selected}>${escapeHtml(item.name_remote_access_application)}</option>`;
        });
        html += '</select>';
        return html;
    }

    function addApplicationRow(item) {
        const row = item || {};
        const html = `<div class="remote-app-row">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong class="remote-app-title">Aplicacion</strong>
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-remote-app"><i class="bx bx-trash me-1"></i>Quitar</button>
            </div>
            <div class="row">
                <div class="col-12 col-lg-3 mb-3">
                    <label class="form-label">Aplicacion</label>
                    ${applicationSelectHtml(row.id_remote_access_application_remote_access_detail || row.id_application)}
                </div>
                <div class="col-12 col-lg-3 mb-3">
                    <label class="form-label">Codigo de acceso / identificador</label>
                    <input type="text" name="access_code_remote_access_detail[]" class="form-control" value="${escapeAttr(row.access_code_remote_access_detail || row.access_code || '')}" placeholder="Ej. 123 456 789" required>
                </div>
                <div class="col-12 col-lg-2 mb-3">
                    <label class="form-label">Estado</label>
                    <select name="status_active_remote_access_detail[]" class="form-control">
                        <option value="1"${String(row.status_active_remote_access_detail ?? 1) === '1' ? ' selected' : ''}>Activo</option>
                        <option value="0"${String(row.status_active_remote_access_detail ?? 1) === '0' ? ' selected' : ''}>Inactivo</option>
                    </select>
                </div>
                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label">Descripcion</label>
                    <input type="text" name="description_remote_access_detail[]" class="form-control" value="${escapeAttr(row.description_remote_access_detail || row.description || '')}" placeholder="Detalle del acceso remoto">
                </div>
                <div class="col-12 mb-2">
                    <label class="form-label">Observacion</label>
                    <textarea name="observations_remote_access_detail[]" class="form-control" rows="2" placeholder="Observaciones o consideraciones de soporte">${escapeHtml(row.observations_remote_access_detail || row.observations || '')}</textarea>
                </div>
            </div>
        </div>`;
        $('#remote-applications-container').append(html);
        renumberApplicationRows();
    }

    function renderApplicationRows(rows) {
        $('#remote-applications-container').empty();
        if (!rows || rows.length === 0) {
            addApplicationRow();
            return;
        }
        rows.forEach(addApplicationRow);
    }

    function refreshApplicationSelects() {
        $('.remote-app-select').each(function () {
            const selected = $(this).val();
            $(this).replaceWith(applicationSelectHtml(selected));
        });
    }

    function renumberApplicationRows() {
        $('.remote-app-row').each(function (index) {
            $(this).find('.remote-app-title').text('Aplicacion ' + (index + 1));
        });
    }

    function readRemoteAccess(id, mode) {
        $.ajax({
            url: base_url + '/remote_access/remote_accesses/show_remote_access',
            type: 'POST',
            dataType: 'json',
            data: { id_remote_access: id },
            success: function (response) {
                if (!response.status) {
                    showMessage(response.type, response.message);
                    return;
                }
                const item = response.result;
                if (mode === 'edit') {
                    resetForm();
                    $('#input-id-remote-access').val(item.id_remote_access);
                    fillAsset({
                        id_asset: item.id_asset_remote_access,
                        code_inventory_asset: item.code_inventory_asset,
                        name_type_asset: item.name_type_asset,
                        name_category_asset: item.name_category_asset,
                        name_brand: item.name_brand,
                        name_condition_asset: item.name_condition_asset,
                        equipment_username: item.equipment_username,
                        current_location: item.current_location
                    });
                    $('#select-status-active-remote-access').val(item.status_active_remote_access);
                    renderApplicationRows(item.applications || []);
                    $('#labelRemoteAccess').text('Actualizar Registro');
                    $('#btnSubmitRemoteAccess').text('Actualizar');
                    $('#ModalCRUD').modal('show');
                    return;
                }
                $('#see-code-inventory').text(item.code_inventory_asset || 'No definido');
                $('#see-equipment-username').text(item.equipment_username || 'No definido');
                $('#see-type-asset').text(item.name_type_asset || 'No definido');
                $('#see-category-asset').text(item.name_category_asset || 'No definido');
                $('#see-brand-asset').text(item.name_brand || 'Ninguna');
                $('#see-condition-asset').text(item.name_condition_asset || 'No definido');
                $('#see-current-location').text(item.current_location || 'No definido');
                $('#see-current-movement').text((item.current_movement_type || 'No definido') + (item.current_movement_date ? ' - ' + item.current_movement_date : ''));
                renderSeeApplications(item.applications || []);
                $('#ModalSee').modal('show');
            },
            error: function () {
                showMessage('error', 'Ocurrio un problema al leer el registro.');
            }
        });
    }

    function renderSeeApplications(rows) {
        const tbody = $('#tbody-see-remote-applications');
        tbody.empty();
        if (!rows || rows.length === 0) {
            tbody.html('<tr><td colspan="6" class="text-center text-muted">Sin aplicaciones registradas.</td></tr>');
            return;
        }
        rows.forEach(function (item) {
            const badge = item.status_active_remote_access_detail == 1 ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>';
            tbody.append(`<tr>
                <td>${formatRecordId(item, 'id_remote_access_detail')}</td>
                <td><strong>${escapeHtml(item.name_remote_access_application || 'No definido')}</strong></td>
                <td>${escapeHtml(item.access_code_remote_access_detail || 'No definido')}</td>
                <td>${escapeHtml(item.description_remote_access_detail || 'Ninguna')}</td>
                <td>${escapeHtml(item.observations_remote_access_detail || 'Ninguna')}</td>
                <td>${badge}</td>
            </tr>`);
        });
    }

    function openAssetSearch() {
        if (tableSearchAsset) {
            tableSearchAsset.ajax.reload();
            $('#ModalSearchAssetRemoteAccess').modal('show');
            return;
        }
        tableSearchAsset = $('#tableSearchAssetRemoteAccess').DataTable({
            pagingType: 'simple_numbers',
            ajax: {
                url: base_url + '/remote_access/remote_accesses/show_assets_for_remote_access',
                data: function (d) {
                    d.id_remote_access = $('#input-id-remote-access').val() || '';
                },
                dataSrc: ''
            },
            language: languageDatatable(),
            columns: [
                { data: 'select_option', orderable: false },
                { data: 'id_asset' },
                { data: 'code_inventory_asset' },
                { data: 'name_type_asset' },
                { data: 'name_brand' },
                { data: 'name_category_asset' },
                { data: 'name_condition_asset' },
                { data: 'equipment_username' },
                { data: 'current_location' }
            ],
            responsive: true,
            destroy: true,
            order: [[1, 'desc']]
        });
        $('#tableSearchAssetRemoteAccess tbody').on('click', 'tr', function () {
            $(this).toggleClass('selected').siblings().removeClass('selected');
        });
        $('#ModalSearchAssetRemoteAccess').modal('show');
    }

    function getSelectedAssetRow() {
        const selected = $('#tableSearchAssetRemoteAccess tbody tr.selected');
        if (selected.length === 0) return null;
        return tableSearchAsset.row(selected).data();
    }

    function fillAsset(asset) {
        $('#input-id-asset-remote-access').val(asset.id_asset || '');
        $('#input-code-inventory-asset-remote-access').val(asset.code_inventory_asset || '');
        $('#input-equipment-username-remote-access').val(asset.equipment_username || 'No definido');
        $('#input-type-asset-remote-access').val(asset.name_type_asset || '');
        $('#input-category-asset-remote-access').val(asset.name_category_asset || '');
        $('#input-brand-asset-remote-access').val(asset.name_brand || '');
        $('#input-current-location-remote-access').val(asset.current_location || 'No definido');
    }

    function resetForm() {
        form.trigger('reset');
        $('#input-id-remote-access').val('');
        fillAsset({});
        $('#remote-applications-container').empty();
    }

    function loadHistory(id) {
        $('#tbody-remote-access-history').html(rowLoading(8));
        $.ajax({
            url: base_url + '/remote_access/remote_accesses/show_remote_access_history',
            type: 'POST',
            dataType: 'json',
            data: { id_remote_access: id },
            success: function (response) {
                if (!response.status) {
                    showMessage(response.type, response.message);
                    return;
                }
                renderHistory(response.history);
                $('#ModalHistory').modal('show');
            },
            error: function () {
                showMessage('error', 'No se pudo leer el historial.');
            }
        });
    }

    function renderHistory(rows) {
        const tbody = $('#tbody-remote-access-history');
        tbody.empty();
        if (!rows || rows.length === 0) {
            tbody.html('<tr><td colspan="8" class="text-center text-muted">Sin registros.</td></tr>');
            return;
        }
        rows.forEach((item, index) => {
            tbody.append(`<tr>
                <td>${formatRecordId(item, 'id_remote_access_history')}</td>
                <td><span class="badge bg-info">${escapeHtml(item.action_remote_access_history || '')}</span></td>
                <td>${escapeHtml(item.previous_application_remote_access_history || 'No definido')}</td>
                <td>${escapeHtml(item.new_application_remote_access_history || 'No definido')}</td>
                <td>${escapeHtml(item.previous_access_code_remote_access_history || 'No definido')}</td>
                <td>${escapeHtml(item.new_access_code_remote_access_history || 'No definido')}</td>
                <td>${escapeHtml(item.display_name || 'Sistema')}</td>
                <td>${escapeHtml(item.date_created_remote_access_history || '')}</td>
            </tr>`);
        });
    }

    function rowLoading(cols) {
        return `<tr><td colspan="${cols}" class="text-center text-muted">Cargando...</td></tr>`;
    }

    function escapeHtml(value) {
        return (value || '').toString()
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function escapeAttr(value) {
        return escapeHtml(value).replace(/`/g, '&#096;');
    }
});
