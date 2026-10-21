document.addEventListener('DOMContentLoaded', function () {
    
    let base_url = document.querySelector('meta[name="base-url"]').getAttribute('content');
    let tablaDataTable;
    let url;
    let url_read;
    var id_movement_asset;
    var modal;
    let id_object = document.querySelector('#tableHistoryMovementAsset').dataset.assetId;

    url = base_url + "/movement/movements_asset/show_history_movement_asset";

    tablaDataTable = $('#tableHistoryMovementAsset').DataTable({
        "pagingType": "simple_numbers",
        "aProcessing":true,
        "aServerSide":true,
        "ajax": {
            "url": url,
            "dataSrc":"",
            "method": "POST",
            "data": {
                "id_asset": id_object
            }
        },
        "language": languageDatatable(),
        "columns": [
            {"data":"id_movement_asset"},
            {"data":"code_inventory_asset"},
            {"data":"name_type_asset"},
            {"data":"name_category_asset"},
            {"data":"name_type_movement"},
            {"data":"movement_record_state",render:$.fn.dataTable.render.text()},
            {"data":"responsible_employee"},
            {"data":"name_space"},
            {"data":"floor_space"},
            {"data":"date_event_movement_asset"},
            {"data":"time_event_movement_asset"},
            {"data":"expected_return_date_movement_asset"},
            {"data":"date_created_movement_asset"},
            {"data":"options"}
        ],
        "responsive":true,
        columnDefs:[{targets:0,responsivePriority:1},{targets:4,responsivePriority:2},{targets:5,responsivePriority:2},{targets:13,responsivePriority:1,orderable:false,searchable:false}],
        dom:'Bfrtilp',
        buttons:[{extend:'excelHtml5',exportOptions:{columns:':not(:last-child)'}},{extend:'pdfHtml5',orientation:'landscape',exportOptions:{columns:':not(:last-child)'}},{extend:'print',exportOptions:{columns:':not(:last-child)'}}],
        "destroy": true,
        "idisplaylength": 10,
        "order":[[0,"desc"]] 
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
    # FUNCIÓN PARA MOSTRAR LOS DATOS EN EL MODAL
    --------------------------------------------------------------*/
    function show_data(id_movement_asset) {
        url_read = base_url + '/movement/movements_asset/show_movement_asset';
        $.ajax({
            url: url_read,
            type: "POST",
            dataType: "json",
            data: {id_movement_asset:id_movement_asset},
            success: function (response) {
                if (response.status === false) {
                    showMessage(response.type, response.message);
                } else {                
                    if (modal == "view") {
                        $("#cell-code-inventory-asset").text(response.result.code_inventory_asset);
                        $("#cell-name-type-asset").text(response.result.name_type_asset);
                        $("#cell-name-category-asset").text(response.result.name_category_asset);
                        $("#cell-name-brand").text(response.result.name_brand);
                        $("#cell-name-condition-asset").text(response.result.name_condition_asset);

                        $("#cell-name-campus").text(response.result.name_campus);
                        $("#cell-name-space").text(response.result.name_space);
                        $("#cell-floor-space").text(response.result.floor_space);
                        $("#cell-floor-plan-movement-asset").text(response.result.name_floor_plan || "No definido");
                        $("#cell-coordinate-movement-asset").text(format_plan_coordinate(response.result.coordinate_x_movement_asset, response.result.coordinate_y_movement_asset));
                        const hasPlanLocation = response.result.current_file_floor_plan && response.result.coordinate_x_movement_asset !== null && response.result.coordinate_y_movement_asset !== null;
                        $("#btn-open-movement-plan-location").toggleClass("d-none", !hasPlanLocation).attr("href", base_url + "/movement/movements_asset/location_movement_asset/" + response.result.id_movement_asset);

                        $("#cell-name-type-movement").text(response.result.name_type_movement);
                        $("#cell-responsible-employee-movement-asset").text(response.result.responsible_employee);

                        $("#cell-date-event-movement-asset").text(response.result.date_event_movement_asset);
                        $("#cell-time-event-movement-asset").text(response.result.time_event_movement_asset || "No definido");
                        $("#cell-expected-return-date-movement-asset").text(response.result.expected_return_date_movement_asset);
                        $("#cell-document-code-movement-asset").text(response.result.document_code_movement_asset);
                        $("#cell-status-process-movement-asset").text(response.result.status_process_movement_asset);
                        const isLoan = String(response.result.id_type_movement) === "5";
                        $(".movement-loan-detail").toggleClass("d-none", !isLoan);
                        $("#cell-loan-recipient-type-movement-asset").text(response.result.loan_recipient_type_label || "No aplica");
                        $("#cell-loan-recipient-name-movement-asset").text(response.result.loan_recipient_name_movement_asset || "No registrado");
                        $("#cell-loan-recipient-document-movement-asset").text(response.result.loan_recipient_document_movement_asset || "No registrado");
                        $("#cell-loan-return-indefinite-movement-asset").text(Number(response.result.loan_return_indefinite_movement_asset) === 1 ? "Indeterminada" : "Con fecha definida");
                        $("#cell-date-created-movement-asset").text(response.result.date_created_movement_asset);
                        
                        render_rich_text("#cell-observations-movement-asset", response.result.observations_movement_asset);
                        render_movement_attachments("#tbody-attachments-movement", response.attachments ?? []);

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

    function render_movement_attachments(tbodySelector, attachments) {
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

    function format_plan_coordinate(x, y) {
        if (x === null || y === null || x === "" || y === "") {
            return "No definida";
        }
        return "X " + parseFloat(x).toFixed(2) + "% / Y " + parseFloat(y).toFixed(2) + "%";
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

});
