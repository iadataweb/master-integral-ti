document.addEventListener('DOMContentLoaded', function () {

    let form = $("#formMaintenanceCorrective");
    let base_url = document.querySelector('meta[name="base-url"]').getAttribute('content');
    let tablaDataTable;
    let url;
    let url_submit;
    let url_read;
    let url_read_wanted;
    let url_search;
    let id_maintenance_corrective;
    let id_object;
    let modal;
    let field_type = "asset";
    let id_array = [];
    let tablaReceptionIncidents;
    let selectedSpareParts = [];

    url = base_url + "/maintenance/maintenances_corrective/show_maintenances_corrective";

    tablaDataTable = $('#tableManageMaintenancesCorrective').DataTable({
        "pagingType": "simple_numbers",
        "aProcessing": true,
        "aServerSide": true,
        "ajax": {
            "url": url,
            "dataSrc": ""
        },
        "language": languageDatatable(),
        "columns": [
            {"data":"id_maintenance_corrective"},
            {
                "data": null,
                "render": function (data) {
                    if (data.id_incident_maintenance_corrective) {
                        return `#${data.id_incident_maintenance_corrective}`;
                    }
                    return '<span class="badge bg-secondary">Sin incidencia</span>';
                }
            },
            {"data":"code_inventory_asset"},
            {"data":"name_type_asset"},
            {"data":"name_category_asset"},
            {"data":"name_maintenance_corrective_origin"},
            {"data":"name_type_responsible_maintenance"},
            {
                "data": null,
                "render": function (data) {
                    return parseInt(data.status_active_maintenance_corrective || 0, 10) === 1
                        ? '<span class="badge bg-warning text-dark">En proceso</span>'
                        : '<span class="badge bg-success">Culminado</span>';
                }
            },
            {
                "data": null,
                "render": function (data) {
                    return data.date_repair_maintenance_corrective || '<span class="text-muted">No definida</span>';
                }
            },
            {
                "data": null,
                "render": function (data) {
                    return `${data.spare_parts_count || 0} repuesto(s)`;
                }
            },
            {"data":"options"}
        ],
        "responsive": "true",
        "dom": "Bfrtilp",
        "buttons": [
            {
                extend: 'excelHtml5',
                text: '<i class="icon-file-excel"></i>',
                titleAttr: 'Exportar a Excel',
                className: 'btn btn-success',
                filename: 'Mantenimiento Correctivo',
                exportOptions: {
                    columns: [0,1,2,3,4,5,6,7,8,9]
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="icon-file-pdf"></i>',
                titleAttr: 'Exportar a PDF',
                className: 'btn btn-danger',
                filename: 'Mantenimiento Correctivo',
                exportOptions: {
                    columns: [0,1,2,3,4,5,6,7,8,9]
                }
            },
            {
                extend: 'print',
                text: '<i class="icon-print"></i>',
                titleAttr: 'Imprimir',
                className: 'btn btn-info',
                filename: 'Mantenimiento Correctivo',
                exportOptions: {
                    columns: [0,1,2,3,4,5,6,7,8,9]
                }
            },
        ],
        "destroy": true,
        "idisplaylength": 10,
        "order": [[8, "desc"]]
    });

    initialize_upload();

    $.post(base_url + "/maintenance/types_responsible_maintenance/combo", function(data) {
        $('#select-types-responsible-maintenance').html(data);
    });

    $.post(base_url + "/maintenance/maintenances_corrective/show_origins", function(data) {
        $('#select-origin-maintenance-corrective').html(data);
    });

    function load_spare_parts_combo() {
        $.post(base_url + "/maintenance/spare_parts/combo", function(data) {
            $('#select-spare-part-maintenance-corrective').html(data);
        });
    }
    load_spare_parts_combo();

    $.post(base_url + "/maintenance/states_maintenance/combo", function(data) {
        $('#select-status-maintenance').html(data);
    });

    $("#btnNew").click(function () {
        url_submit = base_url + '/maintenance/maintenances_corrective/insert_maintenance_corrective';
        id_array = [];
        id_object = "";
        form.trigger("reset");
        selectedSpareParts = [];
        render_selected_spare_parts();
        load_spare_parts_combo();
        clear_uploaded_files();
        $("#input-id-incident-maintenance-corrective").val("");
        $("#select-status-active-maintenance-corrective").val("1");
        $(".modal-title").text("Nuevo Registro");
        $("#btnSubmit").text("Guardar");
        $("#ModalCRUD").modal("show");
    });

    function load_reception_incidents_table() {
        tablaReceptionIncidents = $('#tableReceptionCorrectiveIncidents').DataTable({
            "pagingType": "simple_numbers",
            "aProcessing": true,
            "aServerSide": true,
            "destroy": true,
            "responsive": "true",
            "ajax": {
                "url": base_url + "/maintenance/maintenances_corrective/show_pending_incident_derivations",
                "method": "POST",
                "dataSrc": ""
            },
            "language": languageDatatable(),
            "columns": [
                {"data": "id_incident"},
                {
                    "data": null,
                    "render": function (data) {
                        return `<strong>#${data.id_incident}</strong><br><span>${data.title_incident || 'Sin titulo'}</span><br><small class="text-muted">${data.name_type_incident || ''}</small>`;
                    }
                },
                {
                    "data": null,
                    "render": function (data) {
                        return `<span>${data.code_inventory_asset || ''}</span><br><small class="text-muted">${data.name_type_asset || ''}</small>`;
                    }
                },
                {"data": "name_priority"},
                {"data": "display_location"},
                {"data": "date_derived_corrective_incident"},
                {"data": "options"}
            ],
            "order": [[5, "asc"]]
        });
    }

    $("#btnReceptionIncidents").click(function () {
        load_reception_incidents_table();
        $("#ModalReceptionIncidents").modal("show");
    });

    $(document).on("click", ".js-btn-accept-incident-derivation", function () {
        const idIncident = $(this).data("id");
        if (!window.confirm("Aceptar esta incidencia y generar un mantenimiento correctivo?")) {
            return;
        }
        $.post(base_url + "/maintenance/maintenances_corrective/accept_incident_derivation", {id_incident: idIncident}, function (response) {
            if (typeof response === "string") {
                response = JSON.parse(response);
            }
            showMessage(response.type, response.message);
            if (response.status) {
                tablaDataTable.ajax.reload(null, false);
                if (tablaReceptionIncidents) {
                    tablaReceptionIncidents.ajax.reload(null, false);
                }
            }
        }, "json").fail(function (error) {
            console.error("Error:", error);
            showMessage("error", "Ocurrio un problema al procesar la solicitud.");
        });
    });

    $(document).on("click", ".js-btn-reject-incident-derivation", function () {
        const idIncident = $(this).data("id");
        const observation = window.prompt("Motivo del rechazo (opcional):", "");
        if (observation === null) {
            return;
        }
        $.post(base_url + "/maintenance/maintenances_corrective/reject_incident_derivation", {id_incident: idIncident, observation: observation}, function (response) {
            if (typeof response === "string") {
                response = JSON.parse(response);
            }
            showMessage(response.type, response.message);
            if (response.status && tablaReceptionIncidents) {
                tablaReceptionIncidents.ajax.reload(null, false);
            }
        }, "json").fail(function (error) {
            console.error("Error:", error);
            showMessage("error", "Ocurrio un problema al procesar la solicitud.");
        });
    });

    $(document).on("click", ".js-btn-search-asset", function () {
        $("#ModalSearchAsset").modal("show");
        url_search = base_url + "/inventory/assets/show_assets_existing";
        $('#tableSearchAsset').DataTable({
            "pagingType": "simple_numbers",
            "aProcessing": true,
            "aServerSide": true,
            "ordering": false,
            "select": true,
            "responsive": "true",
            "destroy": true,
            "ajax": {
                "url": url_search,
                "method": "POST",
                "dataSrc": "",
                "data": {
                    "id_asset": id_object
                }
            },
            "language": languageDatatable(),
            "columns": [
                { "data": "id_asset" },
                { "data": null, "className": 'select-checkbox', "orderable": false, "defaultContent": "" },
                
                { "data": "code_inventory_asset" },
                { "data": "name_type_asset" },
                { "data": "name_brand" },
                { "data": "name_category_asset" },
                { "data": "name_condition_asset" },
                { "data": "date_created_asset" }
            ],
            "columnDefs": [
                { "className": "dt-head-left", "targets": "_all" }
            ],
            "createdRow": function (row, data) {
                $('td', row).eq(0).attr('data-id', data.id_asset);
            },
        });
    });

    function get_selected_id(table_id) {
        let selected_row = $(table_id).find('tr.selected');
        if (selected_row.length > 0) {
            let first_td = selected_row.find('td:first');
            let data_id_selected = $(first_td).attr('data-id');
            auto_complete_form(data_id_selected);
            let object = table_id.replace('#tableSearch', 'id_').toLowerCase();
            id_array[object] = data_id_selected;
        } else {
            showMessage("error", "No se ha seleccionado ninguna fila.");
        }
    }

    function auto_complete_form(id_data) {
        let dataToSend = {};
        if (field_type === "asset") {
            dataToSend = { id_asset: id_data };
        } else if (field_type === "space") {
            dataToSend = { id_space: id_data };
        }
        $.ajax({
            url: url_read_wanted,
            type: "POST",
            dataType: "json",
            data: dataToSend,
            success: function (response) {
                if (response.success == false) {
                    showMessage(response.type, response.message);
                } else {
                    if (field_type == "asset") {
                        $("#input-code-inventory-asset").val(response.result.code_inventory_asset);
                        $("#input-name-type-asset").val(response.result.name_type_asset);
                        $("#input-name-category-asset").val(response.result.name_category_asset);
                        $("#input-name-brand").val(response.result.name_brand);
                        $("#input-name-condition-asset").val(response.result.name_condition_asset);
                        $("#textarea-descriptions-asset").val(response.result.descriptions_asset);
                        $("#ModalSearchAsset").modal("hide");
                    } else if (field_type == "space") {
                        $("#input-name-campus").val(response.result.name_campus);
                        $("#input-name-space").val(response.result.name_space);
                        $("#input-floor-space").val(response.result.floor_space);
                        $("#ModalSearchSpace").modal("hide");
                    } else {
                        showMessage("error", "No se ha encontrado informacion.");
                    }
                }
            }
        });
    }

    $("#btnSubmitAddAsset").click(function () {
        url_read_wanted = base_url + "/inventory/assets/show_asset";
        field_type = "asset";
        get_selected_id("#tableSearchAsset");
    });

    $(document).on("click", ".js-btn-view", function () {
        id_maintenance_corrective = $(this).data('id');
        modal = "view";
        show_data(id_maintenance_corrective);
    });

    $(document).on("click", ".js-btn-execute", function () {
        url_submit = base_url + '/maintenance/maintenances_corrective/execute_maintenance_corrective';
        id_maintenance_corrective = $(this).data('id');
        $("#labelExecute").text("Registrar atencion realizada");
        $("#btnSubmitExecute").text("Guardar");
        $("#ModalExecute").modal("show");
    });

    $(document).on("click", ".js-btn-edit", function () {
        url_submit = base_url + '/maintenance/maintenances_corrective/update_maintenance_corrective';
        clear_uploaded_files();
        id_maintenance_corrective = $(this).data('id');
        modal = "edit";
        show_data(id_maintenance_corrective);
    });

    function show_data(id_maintenance_corrective) {
        url_read = base_url + '/maintenance/maintenances_corrective/show_maintenance_corrective';
        $.ajax({
            url: url_read,
            type: "POST",
            dataType: "json",
            data: {id_maintenance_corrective:id_maintenance_corrective},
            success: function (response) {
                if (response.status === false) {
                    showMessage(response.type, response.message);
                } else {
                    if (modal == "edit") {
                        $("#input-code-inventory-asset").val(response.result.code_inventory_asset);
                        $("#input-name-type-asset").val(response.result.name_type_asset);
                        $("#input-name-category-asset").val(response.result.name_category_asset);
                        $("#input-name-brand").val(response.result.name_brand);
                        $("#input-name-condition-asset").val(response.result.name_condition_asset);
                        $("#textarea-descriptions-asset").val(response.result.descriptions_asset);

                        $("#select-origin-maintenance-corrective").val(response.result.id_origin_maintenance_corrective || "");
                        $("#input-date-repair-maintenance-corrective").val(response.result.date_repair_maintenance_corrective);
                        $("#input-time-repair-maintenance-corrective").val((response.result.time_repair_maintenance_corrective || "").substring(0, 5));
                        $("#select-types-responsible-maintenance").val(response.result.id_type_responsible_maintenance);
                        $("#select-status-active-maintenance-corrective").val(response.result.status_active_maintenance_corrective || "1");
                        $("#input-id-incident-maintenance-corrective").val(response.result.id_incident_maintenance_corrective || "");
                        $("#textarea-action-maintenance-corrective").val(response.result.action_maintenance_corrective || response.result.description_maintenance_corrective);
                        $("#textarea-observation-maintenance-corrective").val(response.result.observation_maintenance_corrective);
                        selectedSpareParts = (response.spare_parts || []).map(item => ({
                            id_spare_part: item.id_spare_part,
                            code: item.code_spare_part,
                            name: item.name_spare_part,
                            unit: item.unit_spare_part,
                            quantity: item.quantity_maintenance_corrective_spare_part,
                            observation: item.observation_maintenance_corrective_spare_part || ''
                        }));
                        render_selected_spare_parts();

                        load_existing_attachments(response.attachments);

                        $(".modal-title").text("Actualizar Registro");
                        $("#btnSubmit").text("Actualizar");
                        $("#ModalCRUD").modal("show");

                        id_array.id_asset = response.result.id_asset;
                    }
                    if (modal == "view") {
                        $("#cell-code-inventory-asset").text(response.result.code_inventory_asset);
                        $("#cell-id-incident-maintenance-corrective").text(
                            response.result.id_incident_maintenance_corrective
                                ? `#${response.result.id_incident_maintenance_corrective}`
                                : "Sin incidencia"
                        );
                        $("#cell-name-type-asset").text(response.result.name_type_asset);
                        $("#cell-name-category-asset").text(response.result.name_category_asset);
                        $("#cell-name-type-responsible-maintenance").text(response.result.name_type_responsible_maintenance);
                        $("#cell-status-active-maintenance-corrective").text(
                            parseInt(response.result.status_active_maintenance_corrective || 0, 10) === 1
                                ? "En proceso"
                                : "Culminado"
                        );
                        $("#cell-origin-maintenance-corrective").text(response.result.name_maintenance_corrective_origin || "Sin origen");
                        $("#cell-date-repair-maintenance-corrective").text(response.result.date_repair_maintenance_corrective || "No definida");
                        $("#cell-time-repair-maintenance-corrective").text((response.result.time_repair_maintenance_corrective || "").substring(0, 5) || "No definida");
                        $("#cell-action-maintenance-corrective").text(response.result.action_maintenance_corrective || response.result.description_maintenance_corrective || "Ninguna.");
                        $("#cell-observation-maintenance-corrective").text(response.result.observation_maintenance_corrective);
                        $("#cell-date-created-maintenance-corrective").text(response.result.date_created_maintenance_corrective);
                        render_maintenance_spare_parts("#tbody-spare-parts-see-maintenance-corrective", response.spare_parts ?? []);
                        render_maintenance_attachments("#tbody-attachments-maintenance-corrective", response.attachments ?? []);
                        $(".modal-title").text("Datos del Registro");
                        $("#ModalSee").modal("show");
                    }
                }
            },
            error: function (error) {
                console.error("Error:", error);
                showMessage("error", "Ocurrio un problema al procesar la solicitud.");
            }
        });
    }

    $("#btnAddSparePartMaintenanceCorrective").click(function () {
        const option = $("#select-spare-part-maintenance-corrective option:selected");
        const id = parseInt(option.val() || "0", 10);
        const quantity = parseFloat($("#input-quantity-spare-part-maintenance-corrective").val() || "0");
        const stock = parseFloat(option.data("stock") || "0");
        if (!id || quantity <= 0) {
            showMessage("error", "Seleccione un repuesto e ingrese una cantidad valida.");
            return;
        }
        if (quantity > stock) {
            showMessage("error", "La cantidad supera el stock disponible del repuesto.");
            return;
        }
        const existingIndex = selectedSpareParts.findIndex(item => parseInt(item.id_spare_part, 10) === id);
        const item = {
            id_spare_part: id,
            code: option.data("code") || "",
            name: option.data("name") || option.text(),
            unit: option.data("unit") || "unidad",
            quantity: quantity,
            observation: $("#input-observation-spare-part-maintenance-corrective").val() || ""
        };
        if (existingIndex >= 0) {
            selectedSpareParts[existingIndex] = item;
        } else {
            selectedSpareParts.push(item);
        }
        $("#input-quantity-spare-part-maintenance-corrective").val("");
        $("#input-observation-spare-part-maintenance-corrective").val("");
        render_selected_spare_parts();
    });

    $(document).on("click", ".js-remove-spare-part-maintenance-corrective", function () {
        const index = parseInt($(this).data("index"), 10);
        selectedSpareParts.splice(index, 1);
        render_selected_spare_parts();
    });

    function render_selected_spare_parts() {
        const tbody = $("#tbody-spare-parts-maintenance-corrective");
        tbody.empty();
        $("#input-spare-parts-maintenance-corrective").val(JSON.stringify(selectedSpareParts));
        if (selectedSpareParts.length === 0) {
            tbody.append('<tr><td colspan="5" class="text-center text-muted">Sin repuestos registrados.</td></tr>');
            return;
        }
        selectedSpareParts.forEach((item, index) => {
            tbody.append(`
                <tr>
                    <td>${formatRecordId(item, 'id_spare_part')}</td>
                    <td>${escape_html(item.code)} - ${escape_html(item.name)}</td>
                    <td>${item.quantity} ${escape_html(item.unit)}</td>
                    <td>${escape_html(item.observation || 'Ninguna.')}</td>
                    <td><button type="button" class="btn btn-sm btn-danger js-remove-spare-part-maintenance-corrective" data-index="${index}">Quitar</button></td>
                </tr>
            `);
        });
    }

    function render_maintenance_spare_parts(tbodySelector, spareParts) {
        const $tbody = $(tbodySelector);
        $tbody.empty();
        if (!Array.isArray(spareParts) || spareParts.length === 0) {
            $tbody.append('<tr><td colspan="5" class="text-muted text-center">Sin registros.</td></tr>');
            return;
        }
        spareParts.forEach((item, index) => {
            $tbody.append(`
                <tr>
                    <td>${formatRecordId(item, 'id_maintenance_corrective_spare_part')}</td>
                    <td>${escape_html(item.code_spare_part)}</td>
                    <td>${escape_html(item.name_spare_part)}</td>
                    <td>${item.quantity_maintenance_corrective_spare_part} ${escape_html(item.unit_spare_part)}</td>
                    <td>${escape_html(item.observation_maintenance_corrective_spare_part || 'Ninguna.')}</td>
                </tr>
            `);
        });
    }

    function render_maintenance_attachments(tbodySelector, attachments) {
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

    function escape_html(value) {
        return $("<div>").text(value || "").html();
    }

    form.submit(function(e){
        e.preventDefault();
        if (form[0].checkValidity()) {
            let form_data = new FormData(form[0]);
            form_data = result_data(form_data);
            form_data.append('id_maintenance_corrective', id_maintenance_corrective);
            form_data.append("id_asset", id_array.id_asset ?? '');
            $.ajax({
                url: url_submit,
                type: "POST",
                data: form_data,
                processData: false,
                contentType: false,
                success: function (response) {
                    response = JSON.parse(response);
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
        } else {
            form[0].reportValidity();
        }
    });

    $("#formExecute").submit(function(e){
        e.preventDefault();
        let form_data = new FormData($("#formExecute")[0]);
        form_data.append('id_maintenance_corrective', id_maintenance_corrective);
        $.ajax({
            url: url_submit,
            type: "POST",
            data: form_data,
            processData: false,
            contentType: false,
            success: function (response) {
                response = JSON.parse(response);
                if (response.status === false) {
                    showMessage(response.type, response.message);
                } else {
                    showMessage(response.type, response.message);
                    $("#ModalExecute").modal("hide");
                    tablaDataTable.ajax.reload(null, false);
                }
            },
            error: function (error) {
                console.error("Error:", error);
                showMessage("error", "Ocurrio un problema al procesar la solicitud.");
            }
        });
    });

    function show_folder(id_maintenance_corrective) {
        let url_files = base_url + '/maintenance/maintenances_corrective/show_files';
        $.ajax({
            url: url_files,
            type: "POST",
            dataType: "html",
            data: {id_maintenance_corrective:id_maintenance_corrective},
            success: function (response) {
                $('#content-folder-ajax').html(response);
                $("#ModalFolder").modal("show");
            },
            error: function (error) {
                console.error("Error:", error);
                showMessage("error", "Ocurrio un problema al procesar la solicitud.");
            }
        });
    }

    $(document).on("click", ".js-btn-history", function() {
        id_object = $(this).data('id');
        window.open(base_url + '/maintenance/maintenances_corrective/history_maintenance_corrective/' + id_object);
    });
});
