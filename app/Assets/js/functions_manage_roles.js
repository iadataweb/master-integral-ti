document.addEventListener('DOMContentLoaded', function () {

    let form = $("#formRole");
    let base_url = document.querySelector('meta[name="base-url"]').getAttribute('content');
    let tablaDataTable;
    let url;
    let url_submit;
    let url_read;
    let url_delete;
    var id_role;
    var modal;

    url = base_url + "/administration/roles/show_roles";

    tablaDataTable = $('#tableManageRoles').DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "ajax": {
            "url": url,
            "dataSrc": ""
        },
        "language": languageDatatable(),
        "columns": [
            {"data": "id_role"},
            {"data": "name_role"},
            {"data": "status_active_role"},
            {"data": "protected_role"},
            {"data": "options"}
        ],
        "responsive": true,
        "destroy": true,
        "idisplaylength": 10,
        "order": [[0, "desc"]]
    });

    $("#btnNew").click(function () {
        url_submit = base_url + '/administration/roles/insert_role';
        id_role = null;
        form.trigger("reset");
        $(".modal-title").text("Nuevo Registro");
        $("#btnSubmit").text("Guardar");
        $("#ModalCRUD").modal("show");
    });

    $(document).on("click", ".js-btn-view", function () {
        id_role = $(this).data('id');
        modal = "view";
        show_data(id_role, modal);
        $(".modal-title").text("Datos del Registro");
        $("#ModalSee").modal("show");
    });

    $(document).on("click", ".js-btn-edit", function () {
        url_submit = base_url + '/administration/roles/update_role';
        id_role = $(this).data('id');
        modal = "edit";
        show_data(id_role, modal);
        $(".modal-title").text("Actualizar Registro");
        $("#btnSubmit").text("Actualizar");
        $("#ModalCRUD").modal("show");
    });

    function show_data(id_role) {
        url_read = base_url + '/administration/roles/show_role';
        $.ajax({
            url: url_read,
            type: "POST",
            dataType: "json",
            data: {id_role: id_role},
            success: function (response) {
                if (response.status === false) {
                    showMessage(response.type, response.message);
                    return;
                }
                if (modal == "edit") {
                    $("#input-name-role").val(response.result.name_role);
                    $("#textarea-description-role").val(response.result.description_role);
                    $("#select-status-active-role").val(response.result.status_active_role);
                }
                if (modal == "view") {
                    $("#cell-name-role").text(response.result.name_role);
                    $("#cell-description-role").text(response.result.description_role);
                    $("#cell-status-active-role").text(response.result.status_active_role == 1 ? "Activo" : "Inactivo");
                }
            },
            error: function (error) {
                console.error("Error:", error);
                showMessage("error", "Ocurrio un problema al procesar la solicitud.");
            }
        });
    }

    form.submit(function(e) {
        e.preventDefault();
        var form_data = new FormData(form[0]);
        if (id_role) {
            form_data.append('id_role', id_role);
        }
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
                    return;
                }
                showMessage(response.type, response.message);
                $("#ModalCRUD").modal("hide");
                tablaDataTable.ajax.reload(null, false);
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);
                showMessage("error", "Ocurrio un problema al procesar la solicitud.");
            }
        });
    });

    $(document).on("click", ".js-btn-delete", function () {
        id_role = $(this).data('id');
        url_delete = base_url + "/administration/roles/delete_role";
        Swal.fire({
            title: "Eliminar rol",
            text: "Esta accion desactivara el rol seleccionado.",
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: "Eliminar",
            cancelButtonText: "Cancelar",
            customClass: {
                confirmButton: "btn btn-danger m-2",
                cancelButton: "btn btn-primary m-2",
            },
            allowOutsideClick: false,
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url_delete,
                    method: "POST",
                    dataType: "json",
                    data: {id_role: id_role},
                    success: function (response) {
                        showMessage(response.type, response.message);
                        if (response.status === true) {
                            tablaDataTable.ajax.reload(null, false);
                        }
                    }
                });
            }
        });
    });

});
