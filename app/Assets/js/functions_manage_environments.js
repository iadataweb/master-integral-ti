document.addEventListener('DOMContentLoaded', function () {

    const form = $("#formEnvironment");
    const base_url = document.querySelector('meta[name="base-url"]').getAttribute('content');
    let tablaDataTable;
    let url_submit;
    let url_read;
    let url_delete;
    let id_environment;
    let modal;

    tablaDataTable = $('#tableManageEnvironments').DataTable({
        "pagingType": "simple_numbers",
        "aProcessing": true,
        "aServerSide": true,
        "ajax": {
            "url": base_url + "/administration/environments/show_environments",
            "dataSrc": ""
        },
        "language": languageDatatable(),
        "columns": [
            {"data": "id_environment"},
            {"data": "name_campus"},
            {"data": "name_environment"},
            {"data": "floor_environment"},
            {"data": "status_active_environment"},
            {"data": "options"}
        ],
        "responsive": true,
        "destroy": true,
        "idisplaylength": 10,
        "order": [[0, "desc"]]
    });

    $.post(base_url + "/administration/campuses/combo", function(data) {
        $('#select-campus').html(data);
    });

    $("#btnNew").click(function () {
        url_submit = base_url + '/administration/environments/insert_environment';
        id_environment = null;
        form.trigger("reset");
        $(".modal-title").text("Nuevo Registro");
        $("#btnSubmit").text("Guardar");
        $("#ModalCRUD").modal("show");
    });

    $(document).on("click", ".js-btn-view", function () {
        id_environment = $(this).data('id');
        modal = "view";
        show_data(id_environment);
    });

    $(document).on("click", ".js-btn-edit", function () {
        url_submit = base_url + '/administration/environments/update_environment';
        id_environment = $(this).data('id');
        modal = "edit";
        show_data(id_environment);
    });

    function show_data(id_environment) {
        url_read = base_url + '/administration/environments/show_environment';
        $.ajax({
            url: url_read,
            type: "POST",
            dataType: "json",
            data: {id_environment: id_environment},
            success: function (response) {
                if (response.status === false) {
                    showMessage(response.type, response.message);
                    return;
                }

                if (modal === "edit") {
                    $("#select-campus").val(response.result.id_campus_environment);
                    $("#input-name-environment").val(response.result.name_environment);
                    $("#input-floor-environment").val(response.result.floor_environment);
                    $("#textarea-use-environment").val(response.result.use_environment);
                    $("#select-status-active-environment").val(response.result.status_active_environment);
                    $(".modal-title").text("Actualizar Registro");
                    $("#btnSubmit").text("Actualizar");
                    $("#ModalCRUD").modal("show");
                    return;
                }

                if (modal === "view") {
                    $("#cell-name-campus").text(response.result.name_campus);
                    $("#cell-name-environment").text(response.result.name_environment);
                    $("#cell-floor-environment").text(response.result.floor_environment);
                    $("#cell-use-environment").text(response.result.use_environment || "Ninguna.");
                    $("#cell-status-active-environment").text(response.result.status_active_environment == 1 ? "Activo" : "Inactivo");
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

    form.submit(function(e) {
        e.preventDefault();
        const form_data = new FormData(form[0]);
        if (id_environment) {
            form_data.append('id_environment', id_environment);
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
            error: function (error) {
                console.error("Error:", error);
                showMessage("error", "Ocurrio un problema al procesar la solicitud.");
            }
        });
    });

    $(document).on("click", ".js-btn-delete", function () {
        id_environment = $(this).data('id');
        url_delete = base_url + "/administration/environments/delete_environment";
        Swal.fire({
            title: "Esta seguro de eliminar el ambiente " + id_environment + " del sistema?",
            text: "El registro pasara a la papelera.",
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
            if (!result.value) {
                return;
            }
            $.ajax({
                url: url_delete,
                method: "POST",
                dataType: "json",
                data: {id_environment: id_environment},
                success: function (response) {
                    if (response.status === false) {
                        showMessage(response.type, response.message);
                        return;
                    }
                    showMessage(response.type, response.message);
                    tablaDataTable.ajax.reload(null, false);
                }
            });
        });
    });
});
