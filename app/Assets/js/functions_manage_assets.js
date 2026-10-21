document.addEventListener('DOMContentLoaded', function () {

    let form = $("#formAsset");
    let base_url = document.querySelector('meta[name="base-url"]').getAttribute('content');
    let tablaDataTable;
    let url;
    let url_submit;
    let url_read;
    let url_delete;
    var id_asset;
    var modal;

    url = base_url + "/inventory/assets/show_assets";

    tablaDataTable = $('#tableManageAssets').DataTable({
        "pagingType": "simple_numbers",
        "aProcessing": true,
        "aServerSide": true,
        "ajax": {
            "url": url,
            "dataSrc": ""
        },
        "language": languageDatatable(),
        "columns": [
            { "data": "id_asset" },
            { "data": "code_inventory_asset" },
            { "data": "name_type_asset" },
            { "data": "name_brand" },
            { "data": "name_category_asset" },
            { "data": "name_condition_asset" },
            { "data": "date_created_asset" },
            { "data": "label_status" },
            { "data": "options" }
        ],
        "responsive": true,
        "columnDefs": [
            {targets: 7, responsivePriority: 1},
            {targets: 8, responsivePriority: 2, orderable: false, searchable: false},
            {targets: 1, responsivePriority: 3},
            {targets: 2, responsivePriority: 4}
        ],
        "dom": "Bfrtilp",
        "buttons": [
            {
                extend: 'excelHtml5',
                text: '<i class="icon-file-excel"></i>',
                titleAttr: 'Exportar a Excel',
                className: 'btn btn-success',
                filename: 'Inventario de Activos',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7]
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="icon-file-pdf"></i>',
                titleAttr: 'Exportar a PDF',
                className: 'btn btn-danger',
                filename: 'Inventario de Activos',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7]
                }
            },
            {
                extend: 'print',
                text: '<i class="icon-print"></i>',
                titleAttr: 'Imprimir',
                className: 'btn btn-info',
                filename: 'Inventario de Activos',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7]
                }
            },
        ],
        "destroy": true,
        "idisplaylength": 10,
        "order": [[0, "desc"]]
    });

    initialize_upload();

    $.post(base_url + "/inventory/categories_asset/combo", function (data) {
        $('#select-category-asset').html(data);
    });

    $("#select-category-asset").change(function () {
        cat_id = $(this).val();
        $.post(base_url + "/inventory/types_asset/combo", { cat_id: cat_id }, function (data) {
            $('#select-type-asset').html(data);
        });
    });

    $.post(base_url + "/inventory/brands/combo", function (data) {
        $('#select-brand').html(data);
    });

    $.post(base_url + "/inventory/conditions_asset/combo", function (data) {
        $('#select-condition-asset').html(data);
    });

    $("#btnNew").click(function () {
        url_submit = base_url + '/inventory/assets/insert_asset';
        form.trigger("reset");
        clear_uploaded_files();
        $(".modal-title").text("Nuevo Registro");
        $("#btnSubmit").text("Guardar");
        $("#ModalCRUD").modal("show");
    });

    $(document).on("click", ".js-btn-view", function () {
        id_asset = $(this).data('id');
        modal = "view";
        show_data(id_asset, modal);
    });

    $(document).on("click", ".js-btn-edit", function () {
        url_submit = base_url + '/inventory/assets/update_asset';
        clear_uploaded_files();
        id_asset = $(this).data('id');
        modal = "edit";
        show_data(id_asset, modal);
    });

    $(document).on("click", ".js-btn-menu", function () {
        id_asset = $(this).data('id');
        $("#mainMenu button").each(function () {
            $(this).attr("data-id-asset", id_asset);
        });
        $("#labelMenu").text("Opciones del activo");
        $("#ModalMenu").modal("show");
    });

    $(document).on("click", ".js-btn-code-inventory", function () {
        open_label_template_picker($(this).attr('data-id-asset'));
    });

    let labelTemplatesCache = null;

    function load_label_templates(callback) {
        if (labelTemplatesCache) {
            callback(labelTemplatesCache);
            return;
        }
        $.ajax({
            url: base_url + "/administration/label_templates/active_templates_json",
            type: "GET",
            dataType: "json",
            success: function (response) {
                labelTemplatesCache = Array.isArray(response) ? response : [];
                callback(labelTemplatesCache);
            },
            error: function () {
                labelTemplatesCache = [];
                callback(labelTemplatesCache);
            }
        });
    }

    function fill_label_template_selects(templates) {
        const options = ['<option value="">Seleccione una plantilla registrada</option>'].concat(templates.map(function (item) {
            return '<option value="' + item.id_label_template + '">' + escape_html(item.name_label_template) + '</option>';
        })).join('');
        $('.js-label-template-select').html(options);
    }

    function open_label_template_picker(assetId) {
        load_label_templates(function (templates) {
            fill_label_template_selects(templates);
            $("#input-label-template-picker-asset").val(assetId || '');
            $("#select-label-template-picker").val('');
            render_asset_label_preview("#label-template-preview-picker", null);
            $("#ModalMenu").modal("hide");
            
            $("#ModalLabelTemplatePicker").modal("show");
        });
    }

    $(document).on("change", ".js-label-template-select", function () {
        const target = this.id === "select-label-template-selection" ? "#label-template-preview-selection" : "#label-template-preview-picker";
        const templateId = $(this).val();
        const template = (labelTemplatesCache || []).find(function (item) {
            return String(item.id_label_template) === String(templateId);
        });
        render_asset_label_preview(target, template || null);
    });

    $(document).on("click", "#btnGenerateLabelWithTemplate", function () {
        const assetId = $("#input-label-template-picker-asset").val();
        const templateId = $("#select-label-template-picker").val() || '';
        if (!templateId) { showMessage('warning', 'Seleccione una plantilla registrada para generar las etiquetas.'); return; }
        const targetUrl = base_url + "/inventory/assets/code_inventory_asset_specific?id_asset=" + encodeURIComponent(assetId) + "&id_template=" + encodeURIComponent(templateId);
        window.open(targetUrl, '_blank');
    });

    let labelsTable = null;
    const selectedLabelAssets = new Set();
    let labelRows = [];

    function updateLabelSelection() {
        $('#label-selection-count').text(selectedLabelAssets.size + ' seleccionados');
        $('#label-generate-selected').prop('disabled', selectedLabelAssets.size === 0);
        $('#label-generate-pending').prop('disabled', !labelsTable || !labelsTable.rows({search: 'applied'}).data().toArray().some(row => Number(row.label_attached_asset) === 0));
    }

    function applyLabelFilters() {
        if (!labelsTable) return;
        const status = $('#label-status-filter').val();
        const exact = value => value ? '^' + $.fn.dataTable.util.escapeRegex(value) + '$' : '';
        labelsTable.column(8).search(status === 'pending' ? '^Pendiente$' : status === 'attached' ? '^Colocada$' : '', true, false);
        labelsTable.column(7).search(exact($('#label-category-filter').val()), true, false);
        labelsTable.column(3).search(exact($('#label-type-filter').val()), true, false).draw();
        updateLabelSelection();
    }

    function populateLabelTypes() {
        const category = $('#label-category-filter').val();
        const previous = $('#label-type-filter').val();
        const types = [...new Set(labelRows.filter(row => !category || row.name_category_asset === category).map(row => row.name_type_asset))].sort();
        $('#label-type-filter').empty().append(new Option('Todos', ''));
        types.forEach(type => $('#label-type-filter').append(new Option(type, type)));
        if (types.includes(previous)) $('#label-type-filter').val(previous);
    }

    $(document).on('click', '.js-btn-generate-labels-qr', function () {
        selectedLabelAssets.clear();
        $('#label-status-filter').val('pending');
        $('#label-category-filter, #label-type-filter').val('');
        load_label_templates(function (templates) {
            fill_label_template_selects(templates);
            $('#select-label-template-selection').val('');
            render_asset_label_preview('#label-template-preview-selection', null);
        });
        $('#ModalAssetLabels').modal('show');
    });

    $('#ModalAssetLabels').on('shown.bs.modal', function () {
        if (labelsTable) {
            labelsTable.search('');
            labelsTable.ajax.reload(function () { applyLabelFilters(); }, true);
            return;
        }
        labelsTable = $('#tableAssetLabels').DataTable({
            ajax: {url: base_url + '/inventory/assets/show_assets', dataSrc: function (rows) {
                labelRows = rows;
                const previous = $('#label-category-filter').val();
                $('#label-category-filter').empty().append(new Option('Todas', ''));
                [...new Set(rows.map(row => row.name_category_asset))].sort().forEach(category => $('#label-category-filter').append(new Option(category, category)));
                $('#label-category-filter').val(previous || '');
                populateLabelTypes();
                return rows;
            }},
            language: {...languageDatatable(), info: '_START_–_END_ de _TOTAL_ activos', infoEmpty: 'Sin activos', lengthMenu: 'Mostrar _MENU_', infoFiltered: '(de _MAX_)'},
            pageLength: 10,
            pagingType: 'simple_numbers',
            dom: '<"labels-table-controls"lf>rt<"labels-table-bottom"ip>',
            order: [[2, 'asc']],
            columns: [
                {data:'id_asset',responsivePriority:1},
                {data: 'id_asset', orderable: false, searchable: false, render: function (id) {
                    return '<input type="checkbox" class="form-check-input js-select-label-asset" data-id="' + Number(id) + '" aria-label="Seleccionar activo ' + Number(id) + '"' + (selectedLabelAssets.has(Number(id)) ? ' checked' : '') + '>';
                }},
                ...['code_inventory_asset', 'name_type_asset', 'name_brand', 'model_asset', 'serial_number_asset', 'name_category_asset'].map(key => ({data: key, defaultContent: '', render: $.fn.dataTable.render.text()})),
                {data: 'label_attached_asset', render: function (value, type) {
                    const attached = Number(value) === 1;
                    const text = attached ? 'Colocada' : 'Pendiente';
                    return type === 'display' ? '<span class="badge bg-' + (attached ? 'success' : 'warning') + '">' + text + '</span>' : text;
                }}
            ],
            drawCallback: function () {
                $('#tableAssetLabels .js-select-label-asset').each(function () { this.checked = selectedLabelAssets.has(Number(this.dataset.id)); });
                if (labelsTable) updateLabelSelection();
            },
            initComplete: function () { applyLabelFilters(); }
        });
    });

    $('#label-category-filter').on('change', function () { populateLabelTypes(); applyLabelFilters(); });
    $('#label-status-filter, #label-type-filter').on('change', applyLabelFilters);
    $(document).on('change', '.js-select-label-asset', function () {
        const id = Number(this.dataset.id);
        if (this.checked && selectedLabelAssets.size >= 500) {
            this.checked = false;
            showMessage('warning', 'Selecciona como máximo 500 activos por PDF.');
            return;
        }
        if (this.checked) selectedLabelAssets.add(id); else selectedLabelAssets.delete(id);
        updateLabelSelection();
    });
    $('#label-clear-selection').on('click', function () { selectedLabelAssets.clear(); labelsTable.draw(false); });
    $('#label-select-filtered').on('click', function () {
        const ids = labelsTable.rows({search: 'applied'}).data().toArray().map(row => Number(row.id_asset));
        if (new Set([...selectedLabelAssets, ...ids]).size > 500) {
            showMessage('warning', 'Hay más de 500 activos. Reduce los resultados con los filtros o la búsqueda.');
            return;
        }
        ids.forEach(id => selectedLabelAssets.add(id));
        labelsTable.draw(false);
    });

    function generateSelectedLabels(pendingOnly) {
        if (!$('#select-label-template-selection').val()) { showMessage('warning', 'Seleccione una plantilla registrada para generar las etiquetas.'); return; }
        if (!labelsTable) return;
        const ids = pendingOnly
            ? labelsTable.rows({search: 'applied'}).data().toArray().filter(row => Number(row.label_attached_asset) === 0).map(row => Number(row.id_asset))
            : [...selectedLabelAssets];
        if (!ids.length || ids.length > 500) {
            showMessage('warning', ids.length ? 'Reduce los resultados a un máximo de 500 activos por PDF.' : 'No hay activos para generar etiquetas.');
            return;
        }
        $('#label-selected-inputs').empty();
        ids.forEach(id => $('<input>', {type: 'hidden', name: 'asset_ids[]', value: id}).appendTo('#label-selected-inputs'));
        $('#label-selected-template').val($('#select-label-template-selection').val() || '');
        $('#label-selected-pending').val(pendingOnly ? '1' : '0');
        document.getElementById('formSelectedAssetLabels').submit();
    }
    $('#label-generate-selected').on('click', function () { generateSelectedLabels(false); });
    $('#label-generate-pending').on('click', function () { generateSelectedLabels(true); });

    $(document).on('click', '.js-btn-label-status', function () {
        const button = $(this);
        const attached = Number(button.attr('data-attached')) === 1;
        Swal.fire({
            title: attached ? 'Marcar etiqueta pendiente' : 'Confirmar etiqueta colocada',
            text: attached ? 'El activo volverá a aparecer entre las etiquetas pendientes.' : 'Confirma que la etiqueta está físicamente colocada en este equipo.',
            icon: 'question', showCancelButton: true, confirmButtonText: 'Confirmar', cancelButtonText: 'Cancelar',
            buttonsStyling: false, customClass: {confirmButton: 'btn btn-primary m-2', cancelButton: 'btn btn-secondary m-2'}
        }).then(function (result) {
            if (!result.value && !result.isConfirmed) return;
            button.prop('disabled', true);
            $.ajax({
                url: base_url + '/inventory/assets/update_label_status', type: 'POST', dataType: 'json',
                data: {id_asset: button.attr('data-id'), attached: attached ? '0' : '1', token: $('#asset-label-token').val()},
                success: function (response) {
                    showMessage(response.status ? 'success' : 'error', response.message);
                    if (response.status) {
                        tablaDataTable.ajax.reload(null, false);
                        if (labelsTable) labelsTable.ajax.reload(function () { applyLabelFilters(); }, false);
                    }
                },
                error: function () { showMessage('error', 'No se pudo actualizar el estado de la etiqueta.'); },
                complete: function () { button.prop('disabled', false); }
            });
        });
    });

    function render_asset_label_preview(target, template) {
        const $target = $(target);
        if (!template) {
            $target.html('<div class="asset-label-preview-empty">Seleccione una plantilla registrada. Si no hay plantillas disponibles, cree una en Administración > Parámetros > Etiquetas.</div>');
            return;
        }
        const route = build_asset_label_url(template.design_route_label_template || '');
        $target.html(`
            <img src="${route}" alt="Vista previa de plantilla">
            <div class="asset-label-preview-qr" style="left:${template.qr_x_percent_label_template}%;top:${template.qr_y_percent_label_template}%;width:${template.qr_width_percent_label_template}%;height:${template.qr_height_percent_label_template}%;"></div>
            <div class="asset-label-preview-code" style="left:${template.code_x_percent_label_template || 5}%;top:${template.code_y_percent_label_template || 75}%;width:${template.code_width_percent_label_template || 45}%;height:${template.code_height_percent_label_template || 12}%;font-size:${template.code_font_size_label_template || 8}px;">INV-20260511170229-5A4F</div>
        `);
    }

    function build_asset_label_url(route) {
        const clean = (route || '').toString().trim();
        if (clean.startsWith('http://') || clean.startsWith('https://')) return clean;
        return base_url + '/' + clean.replace(/^\/+/, '');
    }
    function show_data(id_asset) {
        url_read = base_url + '/inventory/assets/show_asset';
        $.ajax({
            url: url_read,
            type: "POST",
            dataType: "json",
            data: { id_asset: id_asset },
            success: function (response) {
                if (response.status === false) {
                    showMessage(response.type, response.message);
                    return;
                }

                if (modal == "edit") {
                    $("#select-category-asset").val(response.result.id_category_asset);
                    $.post(base_url + "/inventory/types_asset/combo", { cat_id: response.result.id_category_asset }, function (data) {
                        $('#select-type-asset').html(data);
                        $("#select-type-asset").val(response.result.id_type_asset);
                    });
                    $("#select-brand").val(response.result.id_brand);
                    $("#select-condition-asset").val(response.result.id_condition_asset);
                    $("#input-model-asset").val(response.result.model_asset);
                    $("#input-serial-number-asset").val(response.result.serial_number_asset);

                    if (response.result.entry_date_asset !== 'No definido') {
                        $("#input-entry-date-asset").val(response.result.entry_date_asset);
                    } else {
                        $("#input-entry-date-asset").val("");
                    }
                    if (response.result.acquisition_date_asset !== 'No definido') {
                        $("#input-acquisition-date-asset").val(response.result.acquisition_date_asset);
                    } else {
                        $("#input-acquisition-date-asset").val("");
                    }

                    $("#textarea-descriptions-asset").val(response.result.descriptions_asset);
                    $("#textarea-observations-asset").val(response.result.observations_asset);
                    $("#select-status-active-asset").val(response.result.status_active_asset);

                    load_existing_attachments(response.attachments);

                    $(".modal-title").text("Actualizar Registro");
                    $("#btnSubmit").text("Actualizar");
                    $("#ModalCRUD").modal("show");
                }

                if (modal == "view") {
                    $("#cell-code-inventory-asset").text(response.result.code_inventory_asset);
                    $("#cell-name-type-asset").text(response.result.name_type_asset);
                    $("#cell-name-category-asset").text(response.result.name_category_asset);
                    $("#cell-name-brand").text(response.result.name_brand);
                    $("#cell-name-condition-asset").text(response.result.name_condition_asset);
                    $("#cell-model-asset").text(response.result.model_asset);
                    $("#cell-serial-number-asset").text(response.result.serial_number_asset);
                    $("#cell-entry-date-asset").text(response.result.entry_date_asset);
                    $("#cell-acquisition-date-asset").text(response.result.acquisition_date_asset);
                    $("#cell-date-created-asset").text(response.result.date_created_asset);

                    render_rich_text("#cell-descriptions-asset", response.result.descriptions_asset);
                    render_rich_text("#cell-observations-asset", response.result.observations_asset);

                    if (response.result.status_active_asset == 1) {
                        $("#cell-status-active-asset").html('<span class="badge bg-success">Activo</span>');
                    } else {
                        $("#cell-status-active-asset").html('<span class="badge bg-danger">Inactivo</span>');
                    }

                    render_asset_attachments("#tbody-attachments-asset", response.attachments ?? []);
                    $(".modal-title").text("Datos del Registro");
                    $("#ModalSee").modal("show");
                }
            },
            error: function (error) {
                console.error("Error:", error);
                showMessage("error", "Ocurrio un problema al procesar la solicitud.");
            }
        });
    }

    function render_asset_attachments(tbodySelector, attachments) {
        const $tbody = $(tbodySelector);
        $tbody.empty();

        if (!Array.isArray(attachments) || attachments.length === 0) {
            $tbody.append('<tr><td colspan="3" class="text-muted text-center">Sin registros.</td></tr>');
            return;
        }

        attachments.forEach((item, index) => {
            const safeName = (item.name_attachment || "").toString();
            const safeUrl = (item.route_attachment || "").toString();
            const normalizedUrl = build_attachment_url(safeUrl);
            const disabledClass = normalizedUrl === "#" ? " disabled" : "";
            const row = `
                <tr>
                    <td>${formatRecordId(item, 'id_attachment')}</td>
                    <td>${escape_html(safeName)}</td>
                    <td>
                        <a href="${normalizedUrl}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary${disabledClass}">Abrir</a>
                    </td>
                </tr>
            `;
            $tbody.append(row);
        });
    }

    function build_attachment_url(routeAttachment) {
        const cleanRoute = (routeAttachment || "").toString().trim();
        if (cleanRoute === "") {
            return "#";
        }
        if (cleanRoute.startsWith("http://") || cleanRoute.startsWith("https://")) {
            return cleanRoute;
        }
        if (cleanRoute.startsWith("/")) {
            return base_url + cleanRoute;
        }
        return base_url + "/" + cleanRoute.replace(/^\/+/, "");
    }

    function render_rich_text(containerSelector, rawText) {
        const $container = $(containerSelector);
        const text = (rawText || "").toString().trim();

        if (text === "") {
            $container.text("Sin informacion registrada.");
            return;
        }

        const items = text
            .split(/,|\r?\n|;/)
            .map(item => item.trim())
            .filter(item => item.length > 0);

        if (items.length <= 1) {
            $container.text(text);
            return;
        }

        const listHtml = items.map(item => `<li>${escape_html(item)}</li>`).join("");
        $container.html(`<ul class="mb-0 ps-3">${listHtml}</ul>`);
    }

    function escape_html(value) {
        return (value || "").toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/\"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    form.submit(function (e) {
        e.preventDefault();
        var form_data = new FormData(form[0]);
        form_data = result_data(form_data);
        form_data.append('id_asset', id_asset);
        $.ajax({
            url: url_submit,
            type: "POST",
            data: form_data,
            processData: false,
            contentType: false,
            success: function (response) {
                var response = JSON.parse(response);
                if (response.status === false) {
                    showMessage(response.type, response.message);
                } else {
                    showMessage(response.type, response.message);
                    $("#ModalCRUD").modal("hide");
                    tablaDataTable.ajax.reload(null, false);
                }
            },
            error: function (error) {
                console.error("Error:", error);
                showMessage("error", "Ocurrio un problema al procesar la solicitud.");
            }
        });
    });

    $(document).on("click", ".js-btn-delete", function () {
        id_asset = $(this).data('id');
        url_delete = base_url + "/inventory/assets/delete_asset";
        Swal.fire({
            title: "Esta seguro de eliminar el registro " + id_asset + " del sistema?",
            text: "No volveras a usar este registro",
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: "Eliminar",
            cancelButtonText: "Cancelar",
            customClass: {
                confirmButton: "btn btn-danger m-2",
                cancelButton: "btn btn-danger m-2",
            },
            allowOutsideClick: false,
        }).then((resul) => {
            if (resul.value) {
                $.ajax({
                    url: url_delete,
                    method: "POST",
                    dataType: "json",
                    data: { id_asset: id_asset },
                    success: function (response) {
                        if (response.status === false) {
                            showMessage(response.type, response.message);
                        } else {
                            showMessage(response.type, response.message);
                            tablaDataTable.ajax.reload(null, false);
                        }
                    }
                });
            }
        });
    });
});
