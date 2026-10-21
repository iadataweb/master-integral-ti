document.addEventListener('DOMContentLoaded', function () {
    const base_url = document.querySelector('meta[name="base-url"]').getAttribute('content');
    const form = $('#formAssetIps');
    let current_asset_id = null;
    let availableIps = [];

    $(document).on('click', '.js-btn-asset-ips', function () {
        current_asset_id = $(this).attr('data-id-asset');
        form.trigger('reset');
        $('#input-id-asset-ip-assignment').val(current_asset_id);
        availableIps = [];
        resetIpSelectors();
        loadAssetIps(current_asset_id);
        $('#ModalAssetIps').modal('show');
    });

    form.on('submit', function (event) {
        event.preventDefault();
        $.ajax({
            url: base_url + '/inventory/network_segmentations/assign_asset_ips',
            type: 'POST',
            dataType: 'json',
            data: form.serialize(),
            success: function (response) {
                showMessage(response.type, response.message);
                if (response.status) {
                    form.trigger('reset');
                    $('#input-id-asset-ip-assignment').val(current_asset_id);
                    availableIps = [];
                    resetIpSelectors();
                    loadAssetIps(current_asset_id);
                }
            },
            error: function () {
                showMessage('error', 'Ocurrio un problema al procesar la solicitud.');
            }
        });
    });

    $(document).on('click', '.js-btn-release-asset-ip', function () {
        const id_assignment = $(this).data('id');
        Swal.fire({
            title: 'Liberar direccion IP',
            text: 'La direccion quedara disponible para una nueva asignacion.',
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
                url: base_url + '/inventory/network_segmentations/release_asset_ip',
                type: 'POST',
                dataType: 'json',
                data: { id_asset_ip_assignment: id_assignment },
                success: function (response) {
                    showMessage(response.type, response.message);
                    if (response.status) {
                        loadAssetIps(current_asset_id);
                    }
                }
            });
        });
    });

    function loadAssetIps(id_asset) {
        $('#tbody-asset-current-ips').html('<tr><td colspan="6" class="text-muted text-center">Cargando...</td></tr>');
        $('#tbody-asset-ip-history').html('<tr><td colspan="7" class="text-muted text-center">Cargando...</td></tr>');
        $.ajax({
            url: base_url + '/inventory/network_segmentations/asset_ip_data',
            type: 'POST',
            dataType: 'json',
            data: { id_asset: id_asset },
            success: function (response) {
                if (!response.status) {
                    showMessage(response.type, response.message);
                    return;
                }
                const types = response.interface_types || [];
                const typeSelect = $('#select-asset-ip-interface-type');
                typeSelect.empty().append(new Option(types.length ? 'Seleccione un tipo de interfaz' : 'No hay tipos de interfaz activos', ''));
                types.forEach((type) => typeSelect.append(new Option(type.name_type_network_interface, type.id_type_network_interface)));
                availableIps = response.available || [];
                renderSegmentations(response.segmentations || [], availableIps);
                renderAvailable([]);
                renderCurrent(response.current || []);
                renderHistory(response.history || []);
            }
        });
    }

    $('#select-asset-ip-segmentation').on('change', function () {
        const idSegmentation = $(this).val();
        const rows = availableIps.filter((item) => String(item.id_network_segmentation) === String(idSegmentation));
        renderAvailable(rows);
    });

    function resetIpSelectors() {
        $('#select-asset-ip-interface-type').empty().append(new Option('Cargando tipos de interfaz...', ''));
        $('#select-asset-ip-segmentation').empty().append('<option value="">Cargando segmentaciones...</option>');
        $('#select-asset-ip-addresses').empty().append('<option disabled>Seleccione una segmentacion para ver sus IP disponibles</option>');
    }

    function renderSegmentations(segmentations, ips) {
        const $select = $('#select-asset-ip-segmentation');
        $select.empty();
        if (segmentations.length === 0) {
            $select.append('<option value="">No hay segmentaciones activas</option>');
            return;
        }
        $select.append('<option value="">Seleccione una segmentacion</option>');
        segmentations.forEach((item) => {
            const availableCount = ips.filter((ip) => String(ip.id_network_segmentation) === String(item.id_network_segmentation)).length;
            const label = `${item.name_network_segmentation} (${availableCount} IP disponibles)`;
            $select.append(`<option value="${item.id_network_segmentation}">${escapeHtml(label)}</option>`);
        });
    }

    function renderAvailable(rows) {
        const $select = $('#select-asset-ip-addresses');
        $select.empty();
        if (!$('#select-asset-ip-segmentation').val()) {
            $select.append('<option disabled>Seleccione una segmentacion para ver sus IP disponibles</option>');
            return;
        }
        if (rows.length === 0) {
            $select.append('<option disabled>No hay direcciones IP disponibles para esta segmentacion</option>');
            return;
        }
        rows.forEach((item) => {
            const label = `${item.name_network_segmentation} - ${item.ip_address_network_segmentation_ip}`;
            $select.append(`<option value="${item.id_network_segmentation_ip}">${escapeHtml(label)}</option>`);
        });
    }

    function renderCurrent(rows) {
        const $tbody = $('#tbody-asset-current-ips');
        $tbody.empty();
        if (rows.length === 0) {
            $tbody.html('<tr><td colspan="6" class="text-muted text-center">Sin direcciones IP asignadas.</td></tr>');
            return;
        }
        rows.forEach((item, index) => {
            $tbody.append(`
                <tr>
                    <td>${formatRecordId(item, 'id_asset_ip_assignment')}</td>
                    <td>${escapeHtml(item.name_network_segmentation)}</td>
                    <td><span class="badge bg-info">${escapeHtml(item.ip_address_network_segmentation_ip)}</span></td>
                    <td>${escapeHtml(item.name_type_network_interface || 'No definido')}</td>
                    <td>${escapeHtml(item.date_assigned_asset_ip_assignment)}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-danger js-btn-release-asset-ip" data-id="${item.id_asset_ip_assignment}">
                            Liberar
                        </button>
                    </td>
                </tr>
            `);
        });
    }

    function renderHistory(rows) {
        const $tbody = $('#tbody-asset-ip-history');
        $tbody.empty();
        if (rows.length === 0) {
            $tbody.html('<tr><td colspan="7" class="text-muted text-center">Sin historial registrado.</td></tr>');
            return;
        }
        rows.forEach((item, index) => {
            $tbody.append(`
                <tr>
                    <td>${formatRecordId(item, 'id_asset_ip_assignment_history')}</td>
                    <td>${escapeHtml(item.name_network_segmentation)}</td>
                    <td>${escapeHtml(item.ip_address_network_segmentation_ip)}</td>
                    <td>${escapeHtml(item.name_type_network_interface || 'No definido')}</td>
                    <td>${actionBadge(item.action_asset_ip_assignment_history)}</td>
                    <td>${escapeHtml(item.display_name || 'Sistema')}</td>
                    <td>${escapeHtml(item.date_created_asset_ip_assignment_history)}</td>
                </tr>
            `);
        });
    }

    function actionBadge(action) {
        if (action === 'assigned') {
            return '<span class="badge bg-success">Asignada</span>';
        }
        if (action === 'released') {
            return '<span class="badge bg-secondary">Liberada</span>';
        }
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


