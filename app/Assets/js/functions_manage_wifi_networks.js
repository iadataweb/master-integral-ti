document.addEventListener('DOMContentLoaded', function () {
    const base_url = document.querySelector('meta[name="base-url"]').getAttribute('content');
    const form = $('#formWifiNetwork');
    let submitUrl = base_url + '/inventory/wifi_networks/insert_wifi_network';
    let tableSearchAsset = null;

    const table = $('#tableManageWifiNetworks').DataTable({
        pagingType: 'simple_numbers',
        aProcessing: true,
        aServerSide: true,
        ajax: {
            url: base_url + '/inventory/wifi_networks/show_wifi_networks',
            dataSrc: ''
        },
        language: languageDatatable(),
        columns: [
            { data: 'id_wifi_network' },
            { data: 'asset_summary' },
            { data: 'management_ip_wifi_network' },
            { data: 'ssid_wifi_network' },
            { data: 'status_active_wifi_network' },
            { data: 'options' }
        ],
        responsive: true,
        dom: 'Bfrtilp',
        buttons: [
            { extend: 'excelHtml5', text: '<i class="icon-file-excel"></i>', titleAttr: 'Exportar a Excel', className: 'btn btn-success', filename: 'Redes WiFi' },
            { extend: 'pdfHtml5', text: '<i class="icon-file-pdf"></i>', titleAttr: 'Exportar a PDF', className: 'btn btn-danger', filename: 'Redes WiFi' },
            { extend: 'print', text: '<i class="icon-print"></i>', titleAttr: 'Imprimir', className: 'btn btn-info', filename: 'Redes WiFi' }
        ],
        destroy: true,
        order: [[0, 'desc']]
    });

    $('#btnNew').on('click', function () {
        submitUrl = base_url + '/inventory/wifi_networks/insert_wifi_network';
        resetForm();
        $('#labelWifiNetwork').text('Nuevo Registro');
        $('#btnSubmitWifiNetwork').text('Guardar');
        $('.js-password-help').hide();
        $('#ModalCRUD').modal('show');
    });

    $(document).on('click', '.js-btn-edit', function () {
        submitUrl = base_url + '/inventory/wifi_networks/update_wifi_network';
        readWifiNetwork($(this).data('id'), 'edit');
    });

    $(document).on('click', '.js-btn-view', function () {
        readWifiNetwork($(this).data('id'), 'view');
    });

    $(document).on('click', '.js-btn-history', function () {
        loadHistory($(this).data('id'));
    });

    $('.js-btn-search-asset-wifi').on('click', function () {
        openAssetSearch();
    });

    $('#btnSubmitAddAssetWifi').on('click', function () {
        const row = getSelectedAssetRow();
        if (!row) {
            showMessage('warning', 'Seleccione un activo para agregar.');
            return;
        }
        fillAsset(row);
        $('#ModalSearchAssetWifi').modal('hide');
    });

    $('#btnGenerateWifiPassword').on('click', function () {
        $('#input-password-wifi-network').val(generatePassword(18)).attr('type', 'text');
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
            title: 'Eliminar red WiFi',
            text: 'El registro se movera a eliminado y conservara su historial.',
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
                url: base_url + '/inventory/wifi_networks/delete_wifi_network',
                type: 'POST',
                dataType: 'json',
                data: { id_wifi_network: id },
                success: function (response) {
                    showMessage(response.type, response.message);
                    if (response.status) table.ajax.reload(null, false);
                }
            });
        });
    });

    $(document).on('click', '.js-toggle-password', function () {
        const target = $($(this).data('target'));
        const type = target.attr('type') === 'password' ? 'text' : 'password';
        target.attr('type', type);
        $(this).find('i').toggleClass('bx-show bx-hide');
    });

    function readWifiNetwork(id, mode) {
        $.ajax({
            url: base_url + '/inventory/wifi_networks/show_wifi_network',
            type: 'POST',
            dataType: 'json',
            data: { id_wifi_network: id },
            success: function (response) {
                if (!response.status) {
                    showMessage(response.type, response.message);
                    return;
                }
                const item = response.result;
                if (mode === 'edit') {
                    resetForm();
                    $('#input-id-wifi-network').val(item.id_wifi_network);
                    fillAsset({
                        id_asset: item.id_asset_wifi_network,
                        code_inventory_asset: item.code_inventory_asset,
                        name_type_asset: item.name_type_asset,
                        name_category_asset: item.name_category_asset,
                        name_brand: item.name_brand,
                        name_condition_asset: item.name_condition_asset
                    });
                    $('#input-management-ip-wifi-network').val(item.management_ip_wifi_network);
                    $('#input-ssid-wifi-network').val(item.ssid_wifi_network);
                    $('#input-password-wifi-network').val('');
                    $('#select-status-active-wifi-network').val(item.status_active_wifi_network);
                    $('#textarea-description-wifi-network').val(item.description_wifi_network);
                    $('#labelWifiNetwork').text('Actualizar Registro');
                    $('#btnSubmitWifiNetwork').text('Actualizar');
                    $('.js-password-help').show();
                    $('#ModalCRUD').modal('show');
                    return;
                }
                $('#see-code-inventory-asset-wifi').text(item.code_inventory_asset || 'No definido');
                $('#see-type-asset-wifi').text(item.name_type_asset || 'No definido');
                $('#see-category-asset-wifi').text(item.name_category_asset || 'No definido');
                $('#see-management-ip-wifi-network').text(item.management_ip_wifi_network || 'No definido');
                $('#see-ssid-wifi-network').text(item.ssid_wifi_network || 'No definido');
                $('#see-password-wifi-network').attr('type', 'password').val(item.password_plain_wifi_network || '');
                $('#see-description-wifi-network').text(item.description_wifi_network || 'Ninguna.');
                $('#see-status-wifi-network').html(item.status_active_wifi_network == 1 ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>');
                $('#ModalSee').modal('show');
            },
            error: function () {
                showMessage('error', 'Ocurrio un problema al leer el registro.');
            }
        });
    }

    function openAssetSearch() {
        if (tableSearchAsset) {
            tableSearchAsset.ajax.reload();
            $('#ModalSearchAssetWifi').modal('show');
            return;
        }
        tableSearchAsset = $('#tableSearchAssetWifi').DataTable({
            pagingType: 'simple_numbers',
            ajax: {
                url: base_url + '/inventory/wifi_networks/show_assets_for_wifi',
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
                { data: 'date_created_asset' }
            ],
            responsive: true,
            destroy: true,
            order: [[1, 'desc']]
        });
        $('#tableSearchAssetWifi tbody').on('click', 'tr', function () {
            $(this).toggleClass('selected').siblings().removeClass('selected');
        });
        $('#ModalSearchAssetWifi').modal('show');
    }

    function getSelectedAssetRow() {
        const selected = $('#tableSearchAssetWifi tbody tr.selected');
        if (selected.length === 0) return null;
        return tableSearchAsset.row(selected).data();
    }

    function fillAsset(asset) {
        $('#input-id-asset-wifi-network').val(asset.id_asset || '');
        $('#input-code-inventory-asset-wifi').val(asset.code_inventory_asset || '');
        $('#input-type-asset-wifi').val(asset.name_type_asset || '');
        $('#input-category-asset-wifi').val(asset.name_category_asset || '');
        $('#input-brand-asset-wifi').val(asset.name_brand || '');
        $('#input-condition-asset-wifi').val(asset.name_condition_asset || '');
    }

    function resetForm() {
        form.trigger('reset');
        $('#input-id-wifi-network').val('');
        fillAsset({});
        $('#input-password-wifi-network').attr('type', 'password');
    }

    function loadHistory(id) {
        $('#tbody-wifi-network-history').html(rowLoading(7));
        $.ajax({
            url: base_url + '/inventory/wifi_networks/show_wifi_network_history',
            type: 'POST',
            dataType: 'json',
            data: { id_wifi_network: id },
            success: function (response) {
                if (!response.status) {
                    showMessage(response.type, response.message);
                    return;
                }
                renderHistory(response.history);
                $('#ModalHistory').modal('show');
            }
        });
    }

    function renderHistory(rows) {
        const tbody = $('#tbody-wifi-network-history');
        tbody.empty();
        if (!rows || rows.length === 0) {
            tbody.html('<tr><td colspan="7" class="text-center text-muted">Sin registros.</td></tr>');
            return;
        }
        rows.forEach((item, index) => {
            const fingerprint = item.password_fingerprint_wifi_network_history
                ? `<code>${escapeHtml(item.password_fingerprint_wifi_network_history.substring(0, 16))}...</code>`
                : 'Sin cambio';
            tbody.append(`<tr>
                <td>${formatRecordId(item, 'id_wifi_network_history')}</td>
                <td><span class="badge bg-info">${escapeHtml(item.action_wifi_network_history || '')}</span></td>
                <td>${escapeHtml(item.previous_ssid_wifi_network_history || 'No definido')}</td>
                <td>${escapeHtml(item.new_ssid_wifi_network_history || 'No definido')}</td>
                <td>${fingerprint}</td>
                <td>${escapeHtml(item.display_name || 'Sistema')}</td>
                <td>${escapeHtml(item.date_created_wifi_network_history || '')}</td>
            </tr>`);
        });
    }

    function generatePassword(length) {
        const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%*-_';
        const values = new Uint32Array(length);
        window.crypto.getRandomValues(values);
        return Array.from(values, value => chars[value % chars.length]).join('');
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
});