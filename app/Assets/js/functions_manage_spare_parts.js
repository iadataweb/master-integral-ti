document.addEventListener('DOMContentLoaded', function () {
    const base_url = document.querySelector('meta[name="base-url"]').getAttribute('content');
    const form = $("#formSparePart");
    let url_submit = "";
    let id_spare_part = 0;

    $.post(base_url + "/inventory/brands/combo", function (data) {
        $('#select-brand-spare-part').html(data);
    });

    const tablaDataTable = $('#tableManageSpareParts').DataTable({
        pagingType: "simple_numbers",
        aProcessing: true,
        aServerSide: true,
        ajax: {
            url: base_url + "/maintenance/spare_parts/show_spare_parts",
            dataSrc: ""
        },
        language: languageDatatable(),
        columns: [
            {"data":"id_spare_part"},
            {"data":"code_spare_part"},
            {"data":"name_spare_part"},
            {"data":"brand_spare_part"},
            {"data":"model_spare_part"},
            {
                "data": null,
                "render": function (data) {
                    return `${data.stock_current_spare_part} ${data.unit_spare_part}`;
                }
            },
            {"data":"stock_status"},
            {"data":"status_active_spare_part"},
            {"data":"options"}
        ],
        responsive: "true",
        dom: "Bfrtilp",
        buttons: [
            {extend: 'excelHtml5', text: '<i class="icon-file-excel"></i>', titleAttr: 'Exportar a Excel', className: 'btn btn-success', filename: 'Repuestos'},
            {extend: 'pdfHtml5', text: '<i class="icon-file-pdf"></i>', titleAttr: 'Exportar a PDF', className: 'btn btn-danger', filename: 'Repuestos'},
            {extend: 'print', text: '<i class="icon-print"></i>', titleAttr: 'Imprimir', className: 'btn btn-info', filename: 'Repuestos'}
        ],
        destroy: true,
        idisplaylength: 10,
        order: [[0, "desc"]]
    });

    $("#btnNew").click(function () {
        id_spare_part = 0;
        url_submit = base_url + "/maintenance/spare_parts/insert_spare_part";
        form.trigger("reset");
        $("#input-unit-spare-part").val("");
        $("#input-stock-current-spare-part").val("0");
        $("#input-stock-minimum-spare-part").val("0");
        $(".js-stock-initial-field").show();
        $(".modal-title").text("Nuevo Repuesto");
        $("#btnSubmit").text("Guardar");
        $("#ModalCRUD").modal("show");
    });

    $(document).on("click", ".js-btn-edit", function () {
        id_spare_part = $(this).data("id");
        url_submit = base_url + "/maintenance/spare_parts/update_spare_part";
        read_spare_part(id_spare_part, "edit");
    });

    $(document).on("click", ".js-btn-view", function () {
        read_spare_part($(this).data("id"), "view");
    });

    $(document).on("click", ".js-btn-stock", function () {
        id_spare_part = $(this).data("id");
        $("#formStockMovement").trigger("reset");
        $("#ModalStock").modal("show");
    });

    $(document).on("click", ".js-btn-delete", function () {
        const id = $(this).data("id");
        if (!confirm("Desea eliminar este repuesto?")) {
            return;
        }
        $.post(base_url + "/maintenance/spare_parts/delete_spare_part", {id_spare_part: id}, function (response) {
            response = JSON.parse(response);
            showMessage(response.type, response.message);
            if (response.status) {
                tablaDataTable.ajax.reload(null, false);
            }
        });
    });

    function read_spare_part(id, mode) {
        $.ajax({
            url: base_url + "/maintenance/spare_parts/show_spare_part",
            type: "POST",
            dataType: "json",
            data: {id_spare_part: id},
            success: function (response) {
                if (!response.status) {
                    showMessage(response.type, response.message);
                    return;
                }
                const item = response.result;
                if (mode === "edit") {
                    $("#input-name-spare-part").val(item.name_spare_part);
                    $("#select-brand-spare-part").val(item.id_brand_spare_part);
                    $("#input-model-spare-part").val(item.model_spare_part);
                    $("#input-unit-spare-part").val(item.unit_spare_part);
                    $("#input-stock-minimum-spare-part").val(item.stock_minimum_spare_part);
                    $("#textarea-description-spare-part").val(item.description_spare_part);
                    $("#select-status-active-spare-part").val(item.status_active_spare_part);
                    $(".js-stock-initial-field").hide();
                    $(".modal-title").text("Actualizar Repuesto");
                    $("#btnSubmit").text("Actualizar");
                    $("#ModalCRUD").modal("show");
                    return;
                }

                $("#cell-code-spare-part").text(item.code_spare_part);
                $("#cell-name-spare-part").text(item.name_spare_part);
                $("#cell-brand-spare-part").text(item.brand_spare_part);
                $("#cell-model-spare-part").text(item.model_spare_part);
                $("#cell-stock-current-spare-part").text(`${item.stock_current_spare_part} ${item.unit_spare_part}`);
                $("#cell-stock-minimum-spare-part").text(`${item.stock_minimum_spare_part} ${item.unit_spare_part}`);
                $("#cell-description-spare-part").text(item.description_spare_part || "Ninguna.");
                render_movements(response.movements || []);
                $("#ModalSee").modal("show");
            }
        });
    }

    function render_movements(items) {
        const tbody = $("#tbody-spare-part-movements");
        tbody.empty();
        if (!items.length) {
            tbody.append('<tr><td colspan="5" class="text-center text-muted">Sin registros.</td></tr>');
            return;
        }
        items.forEach((item, index) => {
            tbody.append(`<tr>
                <td>${formatRecordId(item, 'id_spare_part_movement')}</td>
                <td>${item.type_spare_part_movement}</td>
                <td>${item.quantity_spare_part_movement}</td>
                <td>${item.stock_after_spare_part_movement}</td>
                <td>${item.date_created_spare_part_movement}</td>
            </tr>`);
        });
    }

    form.submit(function (e) {
        e.preventDefault();
        if (!form[0].checkValidity()) {
            form[0].reportValidity();
            return;
        }
        const form_data = new FormData(form[0]);
        form_data.append("id_spare_part", id_spare_part);
        $.ajax({
            url: url_submit,
            type: "POST",
            data: form_data,
            processData: false,
            contentType: false,
            success: function (response) {
                response = JSON.parse(response);
                showMessage(response.type, response.message);
                if (response.status) {
                    $("#ModalCRUD").modal("hide");
                    tablaDataTable.ajax.reload(null, false);
                }
            }
        });
    });

    $("#formStockMovement").submit(function (e) {
        e.preventDefault();
        const form_data = new FormData($("#formStockMovement")[0]);
        form_data.append("id_spare_part", id_spare_part);
        $.ajax({
            url: base_url + "/maintenance/spare_parts/register_stock_movement",
            type: "POST",
            data: form_data,
            processData: false,
            contentType: false,
            success: function (response) {
                response = JSON.parse(response);
                showMessage(response.type, response.message);
                if (response.status) {
                    $("#ModalStock").modal("hide");
                    tablaDataTable.ajax.reload(null, false);
                }
            }
        });
    });
});
