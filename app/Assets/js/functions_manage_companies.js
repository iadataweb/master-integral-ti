document.addEventListener('DOMContentLoaded', function () {

    let form = $("#formCompany");
    let base_url = document.querySelector('meta[name="base-url"]').getAttribute('content');
    let tablaDataTable;
    let url;
    let url_submit;
    let url_read;
    let url_delete;
    var id_company;
    var modal;

    url = base_url + "/administration/companies/show_companies";

    tablaDataTable = $('#tableManageCompanies').DataTable({
        "pagingType": "simple_numbers",
        "aProcessing":true,
        "aServerSide":true,
        "ajax": {
            "url": url,
            "dataSrc":""
        },
        "language": languageDatatable(),
        "columns": [
            {"data":"id_company"},
            {"data":"name_company"},
            {"data":"ruc_company"},
            {"data":"cell_phone_company"},
            {"data":"telephone_company"},
            {"data":"email_company"},
            {"data":"status_active_company"},        
            {"data":"date_created_company"}, 
            {"data":"options"}
        ],
        "responsive":"true",
        "destroy": true,
        "idisplaylength": 10,
        "order":[[0,"desc"]] 
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA MOSTRAR MODAL DE AGREGAR NUEVO REGISTRO
    --------------------------------------------------------------*/
    $("#btnNew").click(function () {
        url_submit = base_url + '/administration/companies/insert_company';
        form.trigger("reset");
        $(".modal-title").text("Nuevo Registro");
        $("#btnSubmit").text("Guardar");
        $("#ModalCRUD").modal("show");
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA MOSTRAR MODAL DE REGISTRO
    --------------------------------------------------------------*/
    $(document).on("click", ".js-btn-view", function () {
        id_company = $(this).data('id');
        modal = "view";
        show_data(id_company, modal);
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA MOSTRAR MODAL DE EDITAR
    --------------------------------------------------------------*/
    $(document).on("click", ".js-btn-edit", function () {
        url_submit = base_url + '/administration/companies/update_company';
        id_company = $(this).data('id');
        modal = "edit";
        show_data(id_company, modal);
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA MOSTRAR LOS DATOS EN EL MODAL
    --------------------------------------------------------------*/
    function show_data(id_company) {
        url_read = base_url + '/administration/companies/show_company';
        $.ajax({
            url: url_read,
            type: "POST",
            dataType: "json",
            data: {id_company:id_company},
            success: function (response) {
                if (response.status === false) {
                    showMessage(response.type, response.message);
                } else {
                    if (modal == "edit") {
                        $("#input-name-company").val(response.result.name_company);
                        $("#input-ruc-company").val(response.result.ruc_company);
                        $("#input-cell-phone-company").val(response.result.cell_phone_company);
                        $("#input-telephone-company").val(response.result.telephone_company);
                        $("#input-email-company").val(response.result.email_company);
                        $("#textarea-description-company").val(response.result.description_company);
                        $("#textarea-observation-company").val(response.result.observation_company);
                        $("#select-status-active-company").val(response.result.status_active_company);
                        $(".modal-title").text("Actualizar Registro");
                        $("#btnSubmit").text("Actualizar");
                        $("#ModalCRUD").modal("show");
                    }
                    if (modal == "view") {
                        $("#cell-name-company").text(response.result.name_company);
                        $("#cell-ruc-company").text(response.result.ruc_company);
                        $("#cell-cell-phone-company").text(response.result.cell_phone_company);
                        $("#cell-telephone-company").text(response.result.telephone_company);
                        $("#cell-email-companye").text(response.result.email_company);
                        $("#cell-description-company").text(response.result.description_company);
                        $("#cell-observation-company").text(response.result.observation_company);
                        if (response.result.status_active_company == 1) {
                            $("#cell-status-active-company").text("Activo");
                        } else {
                            $("#cell-status-active-company").text("Inactivo");
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
        form_data.append('id_company', id_company);
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
            id_company = $(this).data('id');
            url_delete = base_url + "/administration/companies/delete_company";
            Swal.fire({
                title: "¿Está seguro de eliminar el registro " + id_company + " del sistema?",
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
                        data: {id_company:id_company},
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
