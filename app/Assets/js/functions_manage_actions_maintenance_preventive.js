document.addEventListener('DOMContentLoaded', function () {

    let form = $("#formActionMaintenancePreventive");
    let base_url = document.querySelector('meta[name="base-url"]').getAttribute('content');
    let tablaDataTable;
    let url;
    let url_submit;
    let url_read;
    let url_delete;
    let id_parameter_maintenance_preventive_action;
    let modal;

    url = base_url + "/maintenance/actions_maintenance_preventive/show_actions_maintenance_preventive";

    tablaDataTable = $('#tableManageActionsMaintenancePreventive').DataTable({
        "pagingType": "simple_numbers",
        "aProcessing": true,
        "aServerSide": true,
        "ajax": {
            "url": url,
            "dataSrc": ""
        },
        "language": languageDatatable(),
        "columns": [
            {"data":"id_parameter_maintenance_preventive_action"},
            {"data":"name_parameter_maintenance_preventive_action"},
            {"data":"status_active_parameter_maintenance_preventive_action"},
            {"data":"options"}
        ],
        "responsive": "true",
        "destroy": true,
        "idisplaylength": 10,
        "order": [[0, "desc"]]
    });

    $("#btnNew").click(function () {
        url_submit = base_url + '/maintenance/actions_maintenance_preventive/insert_action_maintenance_preventive';
        form.trigger("reset");
        $(".modal-title").text("Nuevo Registro");
        $("#btnSubmit").text("Guardar");
        $("#ModalCRUD").modal("show");
    });

    $(document).on("click", ".js-btn-view", function () {
        id_parameter_maintenance_preventive_action = $(this).data('id');
        modal = "view";
        show_data(id_parameter_maintenance_preventive_action);
    });

    $(document).on("click", ".js-btn-edit", function () {
        url_submit = base_url + '/maintenance/actions_maintenance_preventive/update_action_maintenance_preventive';
        id_parameter_maintenance_preventive_action = $(this).data('id');
        modal = "edit";
        show_data(id_parameter_maintenance_preventive_action);
    });

    function show_data(id_parameter_maintenance_preventive_action) {
        url_read = base_url + '/maintenance/actions_maintenance_preventive/show_action_maintenance_preventive';
        $.ajax({
            url: url_read,
            type: "POST",
            dataType: "json",
            data: {id_parameter_maintenance_preventive_action: id_parameter_maintenance_preventive_action},
            success: function (response) {
                if (response.status === false) {
                    showMessage(response.type, response.message);
                } else {
                    if (modal === "edit") {
                        $("#input-name-parameter-maintenance-preventive-action").val(response.result.name_parameter_maintenance_preventive_action);
                        $("#select-status-active-parameter-maintenance-preventive-action").val(response.result.status_active_parameter_maintenance_preventive_action);
                        $(".modal-title").text("Actualizar Registro");
                        $("#btnSubmit").text("Actualizar");
                        $("#ModalCRUD").modal("show");
                    }
                    if (modal === "view") {
                        $("#cell-name-parameter-maintenance-preventive-action").text(response.result.name_parameter_maintenance_preventive_action);
                        $("#cell-status-active-parameter-maintenance-preventive-action").text(
                            parseInt(response.result.status_active_parameter_maintenance_preventive_action, 10) === 1 ? "Activo" : "Inactivo"
                        );
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

    form.submit(function (e) {
        e.preventDefault();
        let form_data = new FormData(form[0]);
        form_data.append('id_parameter_maintenance_preventive_action', id_parameter_maintenance_preventive_action);
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
    });

    $(document).on("click", ".js-btn-delete", function () {
        id_parameter_maintenance_preventive_action = $(this).data('id');
        url_delete = base_url + "/maintenance/actions_maintenance_preventive/delete_action_maintenance_preventive";
        Swal.fire({
            title: "Esta seguro de eliminar el registro " + id_parameter_maintenance_preventive_action + "?",
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
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url_delete,
                    method: "POST",
                    dataType: "json",
                    data: {id_parameter_maintenance_preventive_action:id_parameter_maintenance_preventive_action},
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
