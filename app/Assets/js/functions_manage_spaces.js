document.addEventListener('DOMContentLoaded', function () {

    let form = $("#formSpace");
    let base_url = document.querySelector('meta[name="base-url"]').getAttribute('content');
    let tablaDataTable;
    let url;
    let url_submit;
    let url_read;
    let url_delete;
    var id_space;
    var modal;

    url = base_url + "/administration/spaces/show_spaces";

    tablaDataTable = $('#tableManageSpaces').DataTable({
        "pagingType": "simple_numbers",
        "aProcessing":true,
        "aServerSide":true,
        "ajax": {
            "url": url,
            "dataSrc":""
        },
        "language": languageDatatable(),
        "columns": [
            {"data":"id_space"},
            {"data":"name_type_space"},
            {"data":"name_campus"},
            {"data":"name_space"},
            {"data":"floor_space"},
            {"data":"status_active_space"},
            {"data":"options"}
        ],
        "responsive":"true",
        "destroy": true,
        "idisplaylength": 10,
        "order":[[0,"desc"]] 
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA LLENAR SELECT CAMPUSES CON EL RESULTADO
    --------------------------------------------------------------*/
    $.post(base_url + "/administration/campuses/combo", function(data) {
        $('#select-campus').html(data);
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA LLENAR SELECT TYPES SPACE CON EL RESULTADO
    --------------------------------------------------------------*/
    $.post(base_url + "/administration/types_space/combo", function(data) {
        $('#select-type-space').html(data);
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA MOSTRAR MODAL DE AGREGAR NUEVO REGISTRO
    --------------------------------------------------------------*/
    $("#btnNew").click(function () {
        url_submit = base_url + '/administration/spaces/insert_space';
        form.trigger("reset");
        $(".modal-title").text("Nuevo Registro");
        $("#btnSubmit").text("Guardar");
        $("#ModalCRUD").modal("show");
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA MOSTRAR MODAL DE REGISTRO
    --------------------------------------------------------------*/
    $(document).on("click", ".js-btn-view", function () {
        id_space = $(this).data('id');
        modal = "view";
        show_data(id_space, modal);
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA MOSTRAR MODAL DE EDITAR
    --------------------------------------------------------------*/
    $(document).on("click", ".js-btn-edit", function () {
        url_submit = base_url + '/administration/spaces/update_space';
        id_space = $(this).data('id');
        modal = "edit";
        show_data(id_space, modal);
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA MOSTRAR LOS DATOS EN EL MODAL
    --------------------------------------------------------------*/
    function show_data(id_space) {
        url_read = base_url + '/administration/spaces/show_space';
        $.ajax({
            url: url_read,
            type: "POST",
            dataType: "json",
            data: {id_space:id_space},
            success: function (response) {
                if (response.status === false) {
                    showMessage(response.type, response.message);
                } else {
                    if (modal == "edit") {
                        $("#select-type-space").val(response.result.id_type_space_space);
                        $("#select-campus").val(response.result.id_campus_space);
                        $("#input-name-space").val(response.result.name_space);
                        $("#input-floor-space").val(response.result.floor_space);
                        $("#textarea-use-space").val(response.result.use_space);
                        $("#select-status-active-space").val(response.result.status_active_space);
                        $(".modal-title").text("Actualizar Registro");
                        $("#btnSubmit").text("Actualizar");
                        $("#ModalCRUD").modal("show");
                    }
                    if (modal == "view") {
                        $("#cell-name-type-space").text(response.result.name_type_space);
                        $("#cell-name-campus").text(response.result.name_campus);
                        $("#cell-name-space").text(response.result.name_space);
                        $("#cell-floor-space").text(response.result.floor_space);
                        $("#cell-use-space").text(response.result.use_space);
                        if (response.result.status_active_space == 1) {
                            $("#cell-status-active-space").text("Activo");
                        } else {
                            $("#cell-status-active-space").text("Inactivo");
                        }
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

    /*--------------------------------------------------------------
    # FUNCIÓN PARA REGISTRAR O ACTUALIZAR REGISTRO
    --------------------------------------------------------------*/
    form.submit(function(e){                         
        e.preventDefault();
        var form_data = new FormData(form[0]);
        form_data.append('id_space', id_space);
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
    }); 


    /*--------------------------------------------------------------
    # FUNCIÓN PARA ELIMINAR REGISTRO
    --------------------------------------------------------------*/
        $(document).on("click", ".js-btn-delete", function () {
            id_space = $(this).data('id');
            url_delete = base_url + "/administration/spaces/delete_space";
            Swal.fire({
                title: "¿Está seguro de eliminar el registro " + id_space + " del sistema?",
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
                        data: {id_space:id_space},
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
