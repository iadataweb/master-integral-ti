document.addEventListener('DOMContentLoaded', function () {

    let form = $("#formEmployee");
    let base_url = document.querySelector('meta[name="base-url"]').getAttribute('content');
    let tablaDataTable;
    let url;
    let url_submit;
    let url_read;
    let url_delete;
    var id_employee;
    var modal;

    url = base_url + "/administration/employees/show_employees";

    tablaDataTable = $('#tableManageEmployees').DataTable({
        "pagingType": "simple_numbers",
        "aProcessing":true,
        "aServerSide":true,
        "ajax": {
            "url": url,
            "dataSrc":""
        },
        "language": languageDatatable(),
        "columns": [
            {"data":"id_employee"},
            {"data":"first_name_employee"},
            {"data":"middle_name_employee"},
            {"data":"paternal_last_name_employee"},
            {"data":"maternal_last_name_employee"},
            {"data":"status_active_employee"},
            {"data":"date_created_employee"},
            {"data":"options"}
        ],
        "responsive":"true",
        "destroy": true,
        "idisplaylength": 10,
        "order":[[0,"desc"]] 
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA LLENAR SELECT AREAS CON EL RESULTADO
    --------------------------------------------------------------*/
    $.post(base_url + "/administration/areas/combo", function(data) {
        $('#select-area').html(data);
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA LLENAR SELECT POSITIONS CON EL RESULTADO
    --------------------------------------------------------------*/
    $.post(base_url + "/administration/positions/combo", function(data) {
        $('#select-position').html(data);
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA LLENAR SELECT PROFESSIONAL TITLES CON EL RESULTADO
    --------------------------------------------------------------*/
    $.post(base_url + "/administration/professional_titles/combo", function(data) {
        $('#select-professional-title').html(data);
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA MOSTRAR MODAL DE AGREGAR NUEVO REGISTRO
    --------------------------------------------------------------*/
    $("#btnNew").click(function () {
        url_submit = base_url + '/administration/employees/insert_employee';
        form.trigger("reset");
        $(".modal-title").text("Nuevo Registro");
        $("#btnSubmit").text("Guardar");
        $("#ModalCRUD").modal("show");
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA MOSTRAR MODAL DE REGISTRO
    --------------------------------------------------------------*/
    $(document).on("click", ".js-btn-view", function () {
        id_employee = $(this).data('id');
        modal = "view";
        show_data(id_employee, modal);
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA MOSTRAR MODAL DE EDITAR
    --------------------------------------------------------------*/
    $(document).on("click", ".js-btn-edit", function () {
        url_submit = base_url + '/administration/employees/update_employee';
        id_employee = $(this).data('id');
        modal = "edit";
        show_data(id_employee, modal);
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA MOSTRAR LOS DATOS EN EL MODAL
    --------------------------------------------------------------*/
    function show_data(id_employee) {
        url_read = base_url + '/administration/employees/show_employee';
        $.ajax({
            url: url_read,
            type: "POST",
            dataType: "json",
            data: {id_employee:id_employee},
            success: function (response) {
                if (response.status === false) {
                    showMessage(response.type, response.message);
                } else {
                    if (modal == "edit") {
                        $("#input-first-name-employee").val(response.result.first_name_employee);
                        $("#input-middle-name-employee").val(response.result.middle_name_employee);
                        $("#input-paternal-last-name-employee").val(response.result.paternal_last_name_employee);
                        $("#input-maternal-last-name-employee").val(response.result.maternal_last_name_employee);
                        $("#select-preferred-name-employee").val(response.result.preferred_name_employee);
                        $("#input-dni-employee").val(response.result.dni_employee);
                        $("#input-cell-phone-employee").val(response.result.cell_phone_employee);
                        $("#input-email-employee").val(response.result.email_employee);
                        $("#select-area").val(response.result.id_area);
                        $("#select-position").val(response.result.id_position);
                        $("#select-professional-title").val(response.result.id_professional_title);
                        $("#select-status-active-employee").val(response.result.status_active_employee);
                        $(".modal-title").text("Actualizar Registro");
                        $("#btnSubmit").text("Actualizar");
                        $("#ModalCRUD").modal("show");
                    }
                    if (modal == "view") {
                        $("#cell-first-name-employee").text(response.result.first_name_employee);
                        $("#cell-middle-name-employee").text(response.result.middle_name_employee);
                        $("#cell-preferred-name-employee").text(response.result.preferred_name_employee);
                        $("#cell-paternal-last-name-employee").text(response.result.paternal_last_name_employee);
                        $("#cell-maternal-last-name-employee").text(response.result.maternal_last_name_employee);
                        $("#cell-dni-employee").text(response.result.dni_employee);
                        $("#cell-cell-phone-employee").text(response.result.cell_phone_employee);
                        $("#cell-email-employee").text(response.result.email_employee);
                        $("#cell-name-area").text(response.result.name_area);
                        $("#cell-name-position").text(response.result.name_position);
                        $("#cell-name-professional-title").text(response.result.name_professional_title);
                        if (response.result.status_active_employee == 1) {
                            $("#cell-status-active-employee").text("Activo");
                        } else {
                            $("#cell-status-active-employee").text("Inactivo");
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
        form_data.append('id_employee', id_employee);
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
            id_employee = $(this).data('id');
            url_delete = base_url + "/administration/employees/delete_employee";
            Swal.fire({
                title: "¿Está seguro de eliminar el registro " + id_employee + " del sistema?",
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
                        data: {id_employee:id_employee},
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
