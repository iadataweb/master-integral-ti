document.addEventListener('DOMContentLoaded', function () {
    const baseUrl = document.querySelector('meta[name="base-url"]')?.getAttribute('content') || '';
    const form = $('#formFloorPlan');
    let table;
    let submitUrl = '';

    if (!baseUrl || typeof $ === 'undefined' || !$.fn.DataTable) {
        return;
    }

    loadCampuses();

    table = $('#tableManageFloorPlans').DataTable({
        pagingType: 'simple_numbers',
        aProcessing: true,
        ajax: {
            url: baseUrl + '/administration/floor_plans/show_floor_plans',
            dataSrc: ''
        },
        language: languageDatatable(),
        columns: [
            { data: 'id_floor_plan' },
            { data: 'name_floor_plan' },
            { data: 'name_campus' },
            { data: 'floor_floor_plan' },
            { data: 'version_floor_plan' },
            { data: 'status_active_floor_plan' },
            { data: 'file_link_floor_plan' },
            { data: 'options' }
        ],
        responsive: true,
        dom: 'Bfrtilp',
        buttons: [
            { extend: 'excelHtml5', text: '<i class="icon-file-excel"></i>', titleAttr: 'Exportar a Excel', className: 'btn btn-success', filename: 'Planos' },
            { extend: 'pdfHtml5', text: '<i class="icon-file-pdf"></i>', titleAttr: 'Exportar a PDF', className: 'btn btn-danger', filename: 'Planos' },
            { extend: 'print', text: '<i class="icon-print"></i>', titleAttr: 'Imprimir', className: 'btn btn-info' }
        ],
        destroy: true,
        idisplaylength: 10,
        order: [[0, 'desc']]
    });

    $('#btnNew').on('click', function () {
        submitUrl = baseUrl + '/administration/floor_plans/insert_floor_plan';
        form.trigger('reset');
        $('#input-id-floor-plan').val('');
        $('#input-file-floor-plan').prop('required', true);
        $('#file-floor-plan-helper').text('Adjunte el archivo PDF del plano.');
        $('#modal-title-floor-plan').text('Nuevo Registro');
        $('#btnSubmit').text('Guardar');
        $('#ModalCRUD').modal('show');
    });

    $(document).on('click', '.js-btn-edit', function () {
        const id = $(this).data('id');
        submitUrl = baseUrl + '/administration/floor_plans/update_floor_plan';
        loadFloorPlan(id, function (item) {
            form.trigger('reset');
            $('#input-id-floor-plan').val(item.id_floor_plan);
            $('#input-name-floor-plan').val(item.name_floor_plan);
            $('#textarea-description-floor-plan').val(item.description_floor_plan || '');
            $('#select-campus-floor-plan').val(item.id_campus_floor_plan);
            $('#input-floor-floor-plan').val(item.floor_floor_plan);
            $('#select-status-floor-plan').val(item.status_active_floor_plan);
            $('#input-file-floor-plan').prop('required', false);
            $('#file-floor-plan-helper').text('Opcional. Adjunte un nuevo PDF solo si desea reemplazar el plano y generar una version.');
            $('#modal-title-floor-plan').text('Editar Plano');
            $('#btnSubmit').text('Actualizar');
            $('#ModalCRUD').modal('show');
        });
    });

    $(document).on('click', '.js-btn-view', function () {
        const id = $(this).data('id');
        loadFloorPlan(id, function (item, versions) {
            const fileUrl = buildUrl(item.current_file_floor_plan);
            const imageUrl = buildUrl(item.current_image_floor_plan || item.current_file_floor_plan);
            $('#cell-name-floor-plan').text(item.name_floor_plan || '-');
            $('#cell-campus-floor-plan').text(item.name_campus || '-');
            $('#cell-floor-floor-plan').text(item.floor_floor_plan || '-');
            $('#cell-version-floor-plan').text('v' + (item.version_floor_plan || '1'));
            $('#cell-status-floor-plan').html(Number(item.status_active_floor_plan) === 1 ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>');
            $('#cell-file-floor-plan').html(fileUrl === '#' ? '<span class="text-muted">Sin archivo</span>' : '<a class="btn btn-sm btn-outline-primary" href="' + fileUrl + '" target="_blank" rel="noopener noreferrer">Abrir PDF</a>');
            $('#cell-description-floor-plan').text(item.description_floor_plan || 'Sin descripcion registrada.');
            $('#img-floor-plan-preview').attr('src', imageUrl === '#' ? '' : imageUrl).toggleClass('d-none', imageUrl === '#');
            renderVersions(versions || []);
            $('#ModalSee').modal('show');
        });
    });

    $(document).on('click', '.js-btn-delete', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Eliminar plano?',
            text: 'El registro se enviara a estado eliminado.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-danger m-2',
                cancelButton: 'btn btn-gray-600 m-2',
            },
        }).then(function (result) {
            if (!result.isConfirmed) {
                return;
            }
            $.post(baseUrl + '/administration/floor_plans/delete_floor_plan', { id_floor_plan: id }, function (response) {
                const data = parseJson(response);
                showMessage(data.type || 'success', data.message || 'Proceso completado.');
                if (data.status) {
                    table.ajax.reload(null, false);
                }
            });
        });
    });

    form.on('submit', function (e) {
        e.preventDefault();
        if (!this.checkValidity()) {
            this.reportValidity();
            return;
        }
        const formData = new FormData(this);
        $.ajax({
            url: submitUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                const data = parseJson(response);
                showMessage(data.type || 'success', data.message || 'Proceso completado.');
                if (data.status) {
                    $('#ModalCRUD').modal('hide');
                    table.ajax.reload(null, false);
                }
            },
            error: function () {
                showMessage('error', 'Ocurrio un error al procesar la solicitud.');
            }
        });
    });

    function loadCampuses() {
        $.post(baseUrl + '/administration/floor_plans/campuses', function (response) {
            const data = parseJson(response);
            const $select = $('#select-campus-floor-plan');
            $select.html('<option value="">Selecciona una opcion</option>');
            if (!data.status || !Array.isArray(data.result)) {
                return;
            }
            data.result.forEach(function (item) {
                $select.append('<option value="' + item.id_campus + '">' + escapeHtml(item.name_campus) + '</option>');
            });
        });
    }

    function loadFloorPlan(id, callback) {
        $.post(baseUrl + '/administration/floor_plans/show_floor_plan', { id_floor_plan: id }, function (response) {
            const data = parseJson(response);
            if (!data.status) {
                showMessage(data.type || 'error', data.message || 'No se pudo leer el registro.');
                return;
            }
            callback(data.result, data.versions || []);
        });
    }

    function renderVersions(versions) {
        const $tbody = $('#tbody-floor-plan-versions');
        $tbody.empty();
        if (!versions.length) {
            $tbody.append('<tr><td colspan="5" class="text-center text-muted">Sin registros.</td></tr>');
            return;
        }
        versions.forEach(function (item, index) {
            const fileUrl = buildUrl(item.route_file_floor_plan_version);
            const imageUrl = buildUrl(item.route_image_floor_plan_version || item.route_file_floor_plan_version);
            $tbody.append(
                '<tr>' +
                '<td>' + formatRecordId(item, 'id_floor_plan_version') + '</td>' +
                '<td>v' + escapeHtml(item.version_number_floor_plan_version) + '</td>' +
                '<td>' + escapeHtml(item.name_file_floor_plan_version || '-') + '</td>' +
                '<td>' + escapeHtml(item.date_created_floor_plan_version || '-') + '</td>' +
                '<td><div class="d-flex flex-wrap gap-1"><a class="btn btn-sm btn-outline-primary" href="' + fileUrl + '" target="_blank" rel="noopener noreferrer">PDF</a><a class="btn btn-sm btn-primary" href="' + imageUrl + '" target="_blank" rel="noopener noreferrer">Imagen</a></div></td>' +
                '</tr>'
            );
        });
    }

    function buildUrl(route) {
        const clean = (route || '').toString().trim();
        if (!clean) {
            return '#';
        }
        if (clean.startsWith('http://') || clean.startsWith('https://')) {
            return clean;
        }
        return baseUrl + '/' + clean.replace(/^\/+/, '');
    }

    function parseJson(response) {
        if (typeof response === 'object') {
            return response;
        }
        try {
            return JSON.parse(response);
        } catch (error) {
            return { status: false, type: 'error', message: 'Respuesta invalida del servidor.' };
        }
    }
});

function escapeHtml(value) {
    return (value || '').toString()
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
