document.addEventListener('DOMContentLoaded', function () {

    let base_url = document.querySelector('meta[name="base-url"]').getAttribute('content');
    let tablaDataTable;
    let url;
    let url_read;
    let id_maintenance_corrective;
    let modal;
    let id_object = window.location.href.match(/\/(\d+)$/)[1];

    url = base_url + "/maintenance/maintenances_corrective/show_history_maintenances_corrective";

    tablaDataTable = $('#tableHistoryMaintenanceCorrective').DataTable({
        "pagingType": "simple_numbers",
        "aProcessing": true,
        "aServerSide": true,
        "ajax": {
            "url": url,
            "dataSrc": "",
            "method": "POST",
            "data": {
                "id_maintenance_corrective": id_object
            }
        },
        "language": languageDatatable(),
        "columns": [
            {"data":"id_maintenance_corrective"},
            {
                "data": null,
                "render": function (data) {
                    return data.id_incident_maintenance_corrective ? `#${data.id_incident_maintenance_corrective}` : '<span class="badge bg-secondary">Sin incidencia</span>';
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
        "destroy": true,
        "idisplaylength": 10,
        "order": [[0,"desc"]]
    });

    $(document).on("click", ".js-btn-view-history", function () {
        id_maintenance_corrective = $(this).data('id');
        modal = "view";
        show_data(id_maintenance_corrective, modal);
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
                        $("#cell-observation-maintenance-corrective").text(response.result.observation_maintenance_corrective || "Ninguna.");
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
            const normalizedUrl = build_attachment_url((item.route_attachment || "").toString());
            const disabledClass = normalizedUrl === "#" ? " disabled" : "";
            $tbody.append(`
                <tr>
                    <td>${formatRecordId(item, 'id_attachment')}</td>
                    <td>${escape_html(safeName)}</td>
                    <td><a href="${normalizedUrl}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary${disabledClass}">Abrir</a></td>
                </tr>
            `);
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

});
