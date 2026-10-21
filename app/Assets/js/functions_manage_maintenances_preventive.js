document.addEventListener('DOMContentLoaded', function () {

    let form = $("#formMaintenancePreventive");
    let base_url = document.querySelector('meta[name="base-url"]').getAttribute('content');
    let tablaDataTable;
    let url;
    let url_submit;
    let url_read;
    let url_read_wanted;
    let url_search;
    var id_maintenance_preventive;
    let id_object;
    var modal;
    let id_array = [];
    let uploadedFilesBefore = [];
    let uploadedFilesAfter = [];
    let uploadedFilesAdditional = [];
    let currentPreventiveExecutionType = "interno";
    const statusColumnIndex = 8;
    const currentStatusColumnIndex = 12;

    url = base_url + "/maintenance/maintenances_preventive/show_maintenances_preventive";

    tablaDataTable = $('#tableManageMaintenancesPreventive').DataTable({
        "pagingType": "simple_numbers",
        "aProcessing":true,
        "aServerSide":true,
        "ajax": {
            "url": url,
            "dataSrc":""
        },
        "language": languageDatatable(),
        "columns": [
            {"data":"id_maintenance_preventive"},
            {"data":"code_inventory_asset"},
            {"data":"name_type_asset"},
            {"data":"name_category_asset"},

            {"data":"name_space"},
            {"data":"floor_space"},

            {"data":"name_type_responsible_maintenance"},
            {"data":"name_frequency_maintenance"},
            {
                "data": null,
                "render": function (data, type) {
                    const key = escape_html(data.maintenance_schedule_status_key || "scheduled");
                    if (type === "filter" || type === "sort") {
                        return key;
                    }
                    const label = escape_html(data.maintenance_schedule_status_label || "Programado");
                    const badgeClass = escape_html(data.maintenance_schedule_status_class || "bg-primary");
                    return `<span class="badge ${badgeClass}" data-status-key="${key}">${label}</span>`;
                }
            },
            {"data":"date_scheduled_maintenance_preventive"},
            {"data":"date_next_maintenance_preventive"},
            {"data":"options"},
            {"data":"maintenance_current_status_key", "visible": false, "searchable": true}
        ],
        "responsive":"true",
        "dom": "Bfrtilp",
        "buttons":[                
                {
                    extend:    'excelHtml5',
                    text:      '<i class="icon-file-excel"></i>',
                    titleAttr: 'Exportar a Excel',
                    className: 'btn btn-success',
                    filename:  'Programación de Mantenimiento Preventivo',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7,8,9,10]
                    }
                },
                {
                    extend:    'pdfHtml5',
                    text:      '<i class="icon-file-pdf"></i>',
                    titleAttr: 'Exportar a PDF',
                    className: 'btn btn-danger',
                    filename:  'Programación de Mantenimiento Preventivo',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7,8,9,10]
                    }
                },
                {
                    extend:    'print',
                    text:      '<i class="icon-print"></i>',
                    titleAttr: 'Imprimir',
                    className: 'btn btn-info',
                    filename:  'Programación de Mantenimiento Preventivo',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7,8,9,10]
                    }
                },
            ],
        "destroy": true,
        "idisplaylength": 10,
        "order":[[9,"asc"]] 
    });
    tablaDataTable.on("xhr.dt", function (_event, _settings, json) {
        update_preventive_status_summary(Array.isArray(json) ? json : []);
    });

    $(document).on("click", ".js-filter-maintenance-status", function () {
        const statusKey = $(this).data("status-key") || "";
        const filterColumn = $(this).data("filter-column") || "status";
        $(".js-filter-maintenance-status").removeClass("active");
        $(this).addClass("active");
        tablaDataTable.column(statusColumnIndex).search("");
        tablaDataTable.column(currentStatusColumnIndex).search("");
        if (filterColumn === "current") {
            tablaDataTable.column(currentStatusColumnIndex).search(statusKey).draw();
        } else {
            tablaDataTable.column(statusColumnIndex).search(statusKey).draw();
        }
    });

    function update_preventive_status_summary(rows) {
        const counts = {
            scheduled: 0,
            expiring: 0,
            pending: 0,
            realized: 0,
            reprogrammed: 0,
            cancelled: 0,
            current: 0
        };

        rows.forEach(function (row) {
            const key = row.maintenance_schedule_status_key || "scheduled";
            counts[key] = (counts[key] || 0) + 1;
            if ((row.maintenance_current_status_key || "") === "current") {
                counts.current += 1;
            }
        });

        $('[data-status-count="all"]').text(rows.length);
        Object.keys(counts).forEach(function (key) {
            $('[data-status-count="' + key + '"]').text(counts[key]);
        });
    }

    function escape_html(value) {
        return String(value ?? "")
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    initialize_upload();
    initialize_execute_uploads();

    /*--------------------------------------------------------------
    # FUNCIÓN PARA LLENAR SELECT FREQUENCY MAINTENANCE CON EL RESULTADO
    --------------------------------------------------------------*/
    $.post(base_url + "/maintenance/frequencies_maintenance/combo", function(data) {
        $('#select-frequency-maintenance').html(data);
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA LLENAR SELECT TYPES RESPONSIBLE MAINTENANCE CON EL RESULTADO
    --------------------------------------------------------------*/
    $.post(base_url + "/maintenance/types_responsible_maintenance/combo", function(data) {
        $('#select-types-responsible-maintenance').html(data);
    });

    $(document).on("change", "#select-types-responsible-maintenance", function () {
        const selectedText = $("#select-types-responsible-maintenance option:selected").text().trim().toLowerCase();
        const isExternal = selectedText === "externo";
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA LLENAR '' CON EL RESULTADO
    --------------------------------------------------------------*/
    $.post(base_url + "/maintenance/states_maintenance/combo", function(data) {
        $('#select-status-maintenance').html(data);
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA MOSTRAR MODAL DE AGREGAR NUEVO REGISTRO
    --------------------------------------------------------------*/
    $("#btnNew").click(function () {
        url_submit = base_url + '/maintenance/maintenances_preventive/insert_maintenance_preventive';
        id_array = [];
        id_object = "";
        form.trigger("reset");
        clear_uploaded_files();
        $(".modal-title").text("Nuevo Registro");
        $("#btnSubmit").text("Guardar");
        $("#ModalCRUD").modal("show");
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA BUSCAR EQUIPO
    --------------------------------------------------------------*/
    $(document).on("click", ".js-btn-search-asset", function () {
        $("#ModalSearchAsset").modal("show");
        url_search = base_url + "/inventory/assets/show_assets_without_scheduled_maintenance";
        tablaDataTableSearchAsset = $('#tableSearchAsset').DataTable({
            "pagingType": "simple_numbers",
            "aProcessing":true,
            "aServerSide":true,
            "ordering": false,
            "select": true,
            "responsive":"true",
            "destroy": true,
            "ajax": {
                "url": url_search,
                "method": "POST",
                "dataSrc":"",
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

    /*--------------------------------------------------------------
    # FUNCIÓN PARA OBTENER EL DATA-ID DE LA FILA SELECCIONADA
    --------------------------------------------------------------*/
    function get_selected_id(table_id) {
        var selected_row = $(table_id).find('tr.selected');
        if (selected_row.length > 0) {
            var first_td = selected_row.find('td:first');
            var data_id_selected = $(first_td).attr('data-id');
            auto_complete_form(data_id_selected);
            var object = table_id.replace('#tableSearch', 'id_').toLowerCase();
            id_array[object] = data_id_selected;
        } else {
            showMessage("error", "No se ha seleccionado ninguna fila.");
        }
    }

    /*--------------------------------------------------------------
    # FUNCIÓN PARA AUTOCOMPLETAR EL FORMULARIO
    --------------------------------------------------------------*/
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
                        showMessage("error", "No se ha encontrado información.");
                    }
                }
            }
        });
    }

    /*--------------------------------------------------------------
    # MÉTODO PARA EJECTUAR LA FUNCIÓN DE OBTENER EL DATA-ID
    --------------------------------------------------------------*/
    $("#btnSubmitAddAsset").click(function () {
        url_read_wanted = base_url + "/inventory/assets/show_asset";
        field_type = "asset";
        get_selected_id("#tableSearchAsset");
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA MOSTRAR MODAL DE REGISTRO
    --------------------------------------------------------------*/
    $(document).on("click", ".js-btn-view", function () {
        id_movement_asset = $(this).data('id');
        modal = "view";
        show_data(id_movement_asset, modal);
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA MOSTRAR MODAL DE EJECUTAR
    --------------------------------------------------------------*/
    $(document).on("click", ".js-btn-execute", function () {
        url_submit = base_url + '/maintenance/maintenances_preventive/execute_maintenance_preventive';
        id_maintenance_preventive = $(this).data('id');
        $("#formExecute")[0].reset();
        const currentDateTime = new Date();
        $("#input-date-performed-history-maintenance-preventive").val(format_date_local(currentDateTime));
        $("#input-time-performed-history-maintenance-preventive").val(format_time_local(currentDateTime));
        clear_execute_uploads();
        prepare_execute_modal(id_maintenance_preventive);
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA MOSTRAR MODAL DE ARCHIVOS
    --------------------------------------------------------------*/
    /*--------------------------------------------------------------
    # FUNCIÓN PARA MOSTRAR MODAL DE EDITAR
    --------------------------------------------------------------*/
    $(document).on("click", ".js-btn-edit", function () {
        url_submit = base_url + '/maintenance/maintenances_preventive/update_maintenance_preventive';
        clear_uploaded_files();
        id_maintenance_preventive = $(this).data('id');
        modal = "edit";
        show_data(id_maintenance_preventive, modal);
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA MOSTRAR LOS DATOS EN EL MODAL
    --------------------------------------------------------------*/
    function show_data(id_maintenance_preventive) {
        url_read = base_url + '/maintenance/maintenances_preventive/show_maintenance_preventive';
        $.ajax({
            url: url_read,
            type: "POST",
            dataType: "json",
            data: {id_maintenance_preventive:id_maintenance_preventive},
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

                        $("#input-date-scheduled-maintenance-preventive").val(response.result.date_scheduled_maintenance_preventive);
                        $("#select-frequency-maintenance").val(response.result.id_frequency_maintenance);
                        $("#select-types-responsible-maintenance").val(response.result.id_type_responsible_maintenance);
                        $("#textarea-description-maintenance-preventive").val(response.result.description_maintenance_preventive);
                        $("#textarea-observation-maintenance-preventive").val(response.result.observation_maintenance_preventive);
                        
                        load_existing_attachments(response.attachments);
                        
                        $(".modal-title").text("Actualizar Registro");
                        $("#btnSubmit").text("Actualizar");
                        $("#ModalCRUD").modal("show");

                        id_array.id_asset = response.result.id_asset;
                    }
                    if (modal == "view") {

                        $("#cell-code-inventory-asset").text(response.result.code_inventory_asset);
                        $("#cell-name-type-asset").text(response.result.name_type_asset);
                        $("#cell-name-category-asset").text(response.result.name_category_asset);
                        $("#cell-name-type-responsible-maintenance").text(response.result.name_type_responsible_maintenance);
                        $("#cell-name-frequency-maintenance").text(response.result.name_frequency_maintenance);
                        $("#cell-date-scheduled-maintenance-preventive").text(response.result.date_scheduled_maintenance_preventive);
                        $("#cell-date-next-maintenance-preventive").text(response.result.date_next_maintenance_preventive);
                        $("#cell-description-maintenance-preventive").text(response.result.description_maintenance_preventive);
                        $("#cell-observation-maintenance-preventive").text(response.result.observation_maintenance_preventive);
                        $("#cell-date-created-maintenance-preventive").text(response.result.date_created_maintenance_preventive);
                        render_maintenance_attachments("#tbody-attachments-maintenance-preventive", response.attachments ?? []);
                        $(".modal-title").text("Datos del Registro");
                        $("#ModalSee").modal("show");
                    }
                }
            },
            error: function (error) {
                console.error("Error:", error);
                showMessage("error", "Ocurrió un problema al procesar la solicitud.");
            }
        });
    }

    function prepare_execute_modal(idMaintenancePreventive) {
        $.ajax({
            url: base_url + '/maintenance/maintenances_preventive/show_maintenance_preventive',
            type: "POST",
            dataType: "json",
            data: {id_maintenance_preventive: idMaintenancePreventive},
            success: function (response) {
                if (response.status === false) {
                    showMessage(response.type, response.message);
                    return;
                }
                currentPreventiveExecutionType = (response.result.name_type_responsible_maintenance || "Interno").toString().toLowerCase();
                const isExternal = currentPreventiveExecutionType === "externo";
                $("#section-actions-preventive").toggleClass("d-none", isExternal);
                $("#section-before-preventive, #section-after-preventive").removeClass("d-none");
                $("#section-additional-documents-preventive").removeClass("d-none");
                $("#alert-external-preventive").toggleClass("d-none", !isExternal);
                if (isExternal) {
                    $("#input-action-history-maintenance-preventive").val("");
                    $("#title-additional-documents-preventive").text("Documentos adicionales de la tarea realizada");
                    $("#help-additional-documents-preventive").text("Adjunte documentos opcionales que sustenten o complementen la tarea realizada, como informes tecnicos, ordenes de servicio, certificados o facturas. Las fotografias de antes y despues pueden adjuntarse en sus secciones correspondientes.");
                    $("#labelExecute").text("Registrar Mantenimiento Preventivo Externo");
                    if (!response.result.id_company) {
                        showMessage("warning", "Este mantenimiento externo no tiene empresa responsable relacionada. Use el boton Empresa antes de registrar la ejecucion.");
                    }
                } else {
                    load_actions_maintenance_preventive_checks();
                    $("#title-additional-documents-preventive").text("Documentos adicionales de la tarea realizada");
                    $("#help-additional-documents-preventive").text("Adjunte documentos opcionales que sustenten o complementen la tarea realizada, como informes, actas, formatos firmados u otras evidencias.");
                    $("#labelExecute").text("Registrar Tarea Realizada");
                }
                $("#btnSubmitExecute").text("Guardar");
                $("#ModalExecute").modal("show");
            },
            error: function (error) {
                console.error("Error:", error);
                showMessage("error", "Ocurrio un problema al procesar la solicitud.");
            }
        });
    }

    /*--------------------------------------------------------------
    # FUNCIÓN PARA REGISTRAR O ACTUALIZAR REGISTRO
    --------------------------------------------------------------*/
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
        console.log("Formulario");
        if (form[0].checkValidity()) {
            var form_data = new FormData(form[0]);
            form_data = result_data(form_data);
            form_data.append('id_maintenance_preventive', id_maintenance_preventive);
            form_data.append("id_asset", id_array.id_asset ?? '');
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
                    showMessage("error", "Ocurrió un problema al procesar la solicitud.");
                }
            });
        } else {
            form[0].reportValidity();
        }
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA REGISTRAR O ACTUALIZAR REGISTRO
    --------------------------------------------------------------*/
    $("#formExecute").submit(function(e){                         
        e.preventDefault();
        const isExternal = currentPreventiveExecutionType === "externo";
        let actions_done = "";
        if (isExternal) {
            actions_done = "Mantenimiento preventivo externo sustentado con documentos adjuntos.";
        } else {
            actions_done = build_actions_text_from_checks();
            if (!actions_done) {
                showMessage("warning", "Debe seleccionar al menos una accion realizada.");
                return;
            }
        }
        $("#input-action-history-maintenance-preventive").val(actions_done);

        const datePerformed = $("#input-date-performed-history-maintenance-preventive").val();
        const timePerformed = $("#input-time-performed-history-maintenance-preventive").val();
        if (!datePerformed) {
            showMessage("warning", "Debe ingresar la fecha realizada.");
            return;
        }
        if (!timePerformed) {
            showMessage("warning", "Debe ingresar la hora realizada.");
            return;
        }

        var form_data = new FormData($("#formExecute")[0]);
        form_data.append('id_maintenance_preventive', id_maintenance_preventive);
        uploadedFilesBefore.forEach((file, index) => {
            form_data.append('document_before[' + index + ']', file);
        });
        uploadedFilesAfter.forEach((file, index) => {
            form_data.append('document_after[' + index + ']', file);
        });
        uploadedFilesAdditional.forEach((file, index) => {
            form_data.append('document_additional[' + index + ']', file);
        });
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
                    $("#ModalExecute").modal("hide");
                    clear_execute_uploads();
                    tablaDataTable.ajax.reload(null, false);
                }
            },
            error: function (error) {
                console.error("Error:", error);
                showMessage("error", "Ocurrió un problema al procesar la solicitud.");
            }
        });											     			
    });
    
    /*--------------------------------------------------------------
    # FUNCIÓN PARA MOSTRAR LOS ARCHIVOS ADJUNTO EN EL MODAL
    --------------------------------------------------------------*/
    function load_actions_maintenance_preventive_checks() {
        $.post(base_url + "/maintenance/actions_maintenance_preventive/combo_checks", function (data) {
            $("#content-actions-maintenance-preventive-checks").html(data);
        }).fail(function () {
            $("#content-actions-maintenance-preventive-checks").html('<div class="text-danger small">No se pudieron cargar las acciones.</div>');
        });
    }

    function build_actions_text_from_checks() {
        let labels = [];
        $("#content-actions-maintenance-preventive-checks .js-action-check:checked").each(function () {
            const label = $(this).closest(".form-check").find("label").text().trim();
            if (label !== "") {
                labels.push(label);
            }
        });
        return labels.join(", ");
    }

    function initialize_execute_uploads() {
        setup_execute_upload(
            "#custom-btn-upload-table-before",
            "#table-handler-before",
            "input-document-before",
            uploadedFilesBefore
        );
        setup_execute_upload(
            "#custom-btn-upload-table-after",
            "#table-handler-after",
            "input-document-after",
            uploadedFilesAfter
        );
        setup_execute_upload(
            "#custom-btn-upload-table-additional",
            "#table-handler-additional",
            "input-document-additional",
            uploadedFilesAdditional
        );
    }

    function setup_execute_upload(containerSelector, tableSelector, inputId, targetArray) {
        const container = document.querySelector(containerSelector);
        if (!container) {
            return;
        }
        container.innerHTML = `
            <span class="btn btn-gray-200 w-100 my-3" data-for="${inputId}">Seleccionar archivo</span>
            <input type="file" id="${inputId}" multiple style="display:none;">
        `;

        const trigger = container.querySelector(`span[data-for="${inputId}"]`);
        const input = container.querySelector(`#${inputId}`);

        trigger.addEventListener("click", function () {
            input.click();
        });

        input.addEventListener("change", function () {
            const files = Array.from(input.files || []);
            if (targetArray.length + files.length > 10) {
                showMessage("warning", "Solo se permiten un maximo de 10 archivos.");
                input.value = "";
                return;
            }
            files.forEach((file) => {
                if (file.size > 15 * 1024 * 1024) {
                    showMessage("warning", `El archivo ${file.name} excede el tamano maximo de 15 MB.`);
                    return;
                }
                targetArray.push(file);
            });
            input.value = "";
            refresh_execute_upload_table(tableSelector, targetArray);
        });

        $(document).on("click", `${tableSelector} .js-remove-upload-exec`, function () {
            const idx = parseInt($(this).data("index"), 10);
            if (!isNaN(idx)) {
                targetArray.splice(idx, 1);
                refresh_execute_upload_table(tableSelector, targetArray);
            }
        });

        refresh_execute_upload_table(tableSelector, targetArray);
    }

    function refresh_execute_upload_table(tableSelector, targetArray) {
        const tbody = document.querySelector(`${tableSelector} tbody`);
        if (!tbody) {
            return;
        }
        tbody.innerHTML = "";
        if (targetArray.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">Ningun archivo seleccionado.</td></tr>';
            return;
        }
        targetArray.forEach((file, index) => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>—</td>
                <td>${file.name}</td>
                <td><button type="button" class="btn btn-danger btn-sm js-remove-upload-exec" data-index="${index}">Eliminar</button></td>
            `;
            tbody.appendChild(tr);
        });
    }

    function clear_execute_uploads() {
        uploadedFilesBefore.length = 0;
        uploadedFilesAfter.length = 0;
        uploadedFilesAdditional.length = 0;
        refresh_execute_upload_table("#table-handler-before", uploadedFilesBefore);
        refresh_execute_upload_table("#table-handler-after", uploadedFilesAfter);
        refresh_execute_upload_table("#table-handler-additional", uploadedFilesAdditional);
        $("#input-action-history-maintenance-preventive").val("");
        $("#content-actions-maintenance-preventive-checks .js-action-check").prop("checked", false);
    }

    function format_date_local(date) {
        const pad = (value) => value.toString().padStart(2, "0");
        return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
    }

    function format_time_local(date) {
        const pad = (value) => value.toString().padStart(2, "0");
        return `${pad(date.getHours())}:${pad(date.getMinutes())}`;
    }

    function show_folder(id_maintenance_preventive) {
        url_files = base_url + '/maintenance/maintenances_preventive/show_files';
        $.ajax({
            url: url_files,
            type: "POST",
            dataType: "html",
            data: {id_maintenance_preventive:id_maintenance_preventive},
            success: function (response) {
                $('#content-folder-ajax').html(response);
                // $(".modal-title").text("HOLAAAAAAAAAAAAAAAAAAAAA");
                $("#ModalFolder").modal("show");
            },
            error: function (error) {
                console.error("Error:", error);
                showMessage("error", "Ocurrió un problema al procesar la solicitud.");
            }
        });
    }
    
    /*--------------------------------------------------------------
    # FUNCIÓN PARA VER HISTORIAL EN OTRA VENTANA
    --------------------------------------------------------------*/
    $(document).on("click", ".js-btn-history", function() {
        id_object = $(this).data('id');
        window.open(base_url + '/maintenance/maintenances_preventive/history_maintenance_preventive/' + id_object);
    });
});


