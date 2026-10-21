document.addEventListener('DOMContentLoaded', function () {
    const base_url = document.querySelector('meta[name="base-url"]').getAttribute('content');
    const form = $('#formNetworkSegmentation');
    let url_submit = base_url + '/inventory/network_segmentations/insert_network_segmentation';
    let current_segment_id = null;

    const table = $('#tableManageNetworkSegmentations').DataTable({
        pagingType: 'simple_numbers',
        aProcessing: true,
        aServerSide: true,
        ajax: {
            url: base_url + '/inventory/network_segmentations/show_network_segmentations',
            dataSrc: ''
        },
        language: languageDatatable(),
        columns: [
            { data: 'id_network_segmentation' },
            { data: 'name_network_segmentation' },
            { data: 'network_address_network_segmentation' },
            { data: 'subnet_mask_network_segmentation' },
            { data: 'gateway_network_segmentation' },
            { data: 'ip_range_start_network_segmentation' },
            { data: 'ip_resume' },
            { data: 'status_active_network_segmentation' },
            { data: 'options' }
        ],
        responsive: true,
        dom: 'Bfrtilp',
        buttons: [
            { extend: 'excelHtml5', text: '<i class="icon-file-excel"></i>', titleAttr: 'Exportar a Excel', className: 'btn btn-success', filename: 'Segmentacion de Red' },
            { extend: 'pdfHtml5', text: '<i class="icon-file-pdf"></i>', titleAttr: 'Exportar a PDF', className: 'btn btn-danger', filename: 'Segmentacion de Red' },
            { extend: 'print', text: '<i class="icon-print"></i>', titleAttr: 'Imprimir', className: 'btn btn-info', filename: 'Segmentacion de Red' }
        ],
        destroy: true,
        order: [[0, 'desc']]
    });

    $('#btnNew').on('click', function () {
        current_segment_id = null;
        url_submit = base_url + '/inventory/network_segmentations/insert_network_segmentation';
        form.trigger('reset');
        $('#input-id-network-segmentation').val('');
        $('#labelNetworkSegmentation').text('Nuevo Registro');
        $('#btnSubmitNetworkSegmentation').text('Guardar');
        $('#ModalCRUD').modal('show');
    });

    $(document).on('click', '.js-btn-edit', function () {
        current_segment_id = $(this).data('id');
        url_submit = base_url + '/inventory/network_segmentations/update_network_segmentation';
        readSegment(current_segment_id, 'edit');
    });

    $(document).on('click', '.js-btn-view', function () {
        current_segment_id = $(this).data('id');
        readSegment(current_segment_id, 'view');
    });

    $(document).on('click', '.js-btn-ips', function () {
        current_segment_id = $(this).data('id');
        loadIps(current_segment_id);
        $('#ModalIps').modal('show');
    });

    function readSegment(id, mode) {
        $.ajax({
            url: base_url + '/inventory/network_segmentations/show_network_segmentation',
            type: 'POST',
            dataType: 'json',
            data: { id_network_segmentation: id },
            success: function (response) {
                if (!response.status) {
                    showMessage(response.type, response.message);
                    return;
                }
                const item = response.result;
                if (mode === 'edit') {
                    $('#input-id-network-segmentation').val(id);
                    $('#input-name-network-segmentation').val(item.name_network_segmentation);
                    $('#textarea-description-network-segmentation').val(item.description_network_segmentation);
                    $('#input-network-address-network-segmentation').val(item.network_address_network_segmentation);
                    $('#input-subnet-mask-network-segmentation').val(item.subnet_mask_network_segmentation);
                    $('#input-gateway-network-segmentation').val(item.gateway_network_segmentation);
                    $('#input-dns-network-segmentation').val(item.dns_network_segmentation);
                    $('#input-ip-range-start-network-segmentation').val(item.ip_range_start_network_segmentation);
                    $('#input-ip-range-end-network-segmentation').val(item.ip_range_end_network_segmentation);
                    $('#select-status-active-network-segmentation').val(item.status_active_network_segmentation);
                    $('#labelNetworkSegmentation').text('Actualizar Registro');
                    $('#btnSubmitNetworkSegmentation').text('Actualizar');
                    $('#ModalCRUD').modal('show');
                    return;
                }
                $('#see-name-network-segmentation').text(item.name_network_segmentation);
                $('#see-network-address-network-segmentation').text(item.network_address_network_segmentation);
                $('#see-subnet-mask-network-segmentation').text(item.subnet_mask_network_segmentation);
                $('#see-gateway-network-segmentation').text(item.gateway_network_segmentation || 'No definido');
                $('#see-dns-network-segmentation').text(item.dns_network_segmentation || 'No definido');
                $('#see-range-network-segmentation').text(`${item.ip_range_start_network_segmentation} - ${item.ip_range_end_network_segmentation}`);
                $('#see-description-network-segmentation').text(item.description_network_segmentation || 'Ninguna.');
                $('#see-status-network-segmentation').html(item.status_active_network_segmentation == 1 ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>');
                $('#ModalSee').modal('show');
            }
        });
    }

    form.on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: url_submit,
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
            title: 'Eliminar segmentacion',
            text: 'Se eliminara la segmentacion siempre que no tenga IPs asignadas.',
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
                url: base_url + '/inventory/network_segmentations/delete_network_segmentation',
                type: 'POST',
                dataType: 'json',
                data: { id_network_segmentation: id },
                success: function (response) {
                    showMessage(response.type, response.message);
                    if (response.status) table.ajax.reload(null, false);
                }
            });
        });
    });

    function loadIps(id) {
        $('#tbody-network-segmentation-ips').html('<tr><td colspan="5" class="text-center text-muted">Cargando...</td></tr>');
        $.ajax({
            url: base_url + '/inventory/network_segmentations/show_ips',
            type: 'POST',
            dataType: 'json',
            data: { id_network_segmentation: id },
            success: function (response) {
                const rows = response.result || [];
                const $tbody = $('#tbody-network-segmentation-ips');
                $tbody.empty();
                if (rows.length === 0) {
                    $tbody.html('<tr><td colspan="5" class="text-center text-muted">Sin direcciones generadas.</td></tr>');
                    return;
                }
                rows.forEach((item, index) => {
                    const disabled = item.status_network_segmentation_ip === 'assigned' ? 'disabled' : '';
                    const badge = statusBadge(item.status_network_segmentation_ip);
                    $tbody.append(`
                        <tr>
                            <td>${formatRecordId(item, 'id_network_segmentation_ip')}</td>
                            <td>${escapeHtml(item.ip_address_network_segmentation_ip)}</td>
                            <td>${item.assigned_asset_code ? escapeHtml(item.assigned_asset_code) + (item.assigned_equipment_user ? '<small class="d-block text-muted">' + escapeHtml(item.assigned_equipment_user) + '</small>' : '') : '—'}</td>
                            <td class="js-ip-status-badge" aria-live="polite">${badge}</td>
                            <td>
                                <select class="form-select form-select-sm js-select-ip-status" data-id="${item.id_network_segmentation_ip}" data-saved-status="${escapeHtml(item.status_network_segmentation_ip)}" ${disabled}>
                                    ${item.status_network_segmentation_ip === 'assigned' ? '<option value="assigned" selected>Asignada</option>' : ''}
                                    <option value="available" ${item.status_network_segmentation_ip === 'available' ? 'selected' : ''}>Disponible</option>
                                    <option value="reserved" ${item.status_network_segmentation_ip === 'reserved' ? 'selected' : ''}>Reservada</option>
                                    <option value="inactive" ${item.status_network_segmentation_ip === 'inactive' ? 'selected' : ''}>Inactiva</option>
                                </select>
                            </td>
                        </tr>
                    `);
                });
            }
        });
    }

    $(document).on('change', '.js-select-ip-status', function () {
        const $select = $(this);
        if ($select.data('saving')) return;
        const id = $select.data('id');
        const status = $select.val();
        const previousStatus = $select.attr('data-saved-status');
        $select.data('saving', true).prop('disabled', true);
        $.ajax({
            url: base_url + '/inventory/network_segmentations/update_ip_status',
            type: 'POST',
            dataType: 'json',
            data: { id_network_segmentation_ip: id, status_network_segmentation_ip: status },
            success: function (response) {
                if (response.status) {
                    $select.attr('data-saved-status', status);
                    $select.closest('tr').find('.js-ip-status-badge').html(statusBadge(status));
                    table.ajax.reload(null, false);
                } else {
                    $select.val(previousStatus);
                    showMessage(response.type, response.message);
                }
            },
            error: function () {
                $select.val(previousStatus);
                showMessage('error', 'No se pudo actualizar el estado de la IP.');
            },
            complete: function () {
                $select.data('saving', false).prop('disabled', false);
            }
        });
    });

    function statusBadge(status) {
        const map = {
            available: '<span class="badge bg-success">Disponible</span>',
            assigned: '<span class="badge bg-info">Asignada</span>',
            reserved: '<span class="badge bg-warning text-dark">Reservada</span>',
            inactive: '<span class="badge bg-secondary">Inactiva</span>'
        };
        return map[status] || '<span class="badge bg-secondary">No definido</span>';
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
