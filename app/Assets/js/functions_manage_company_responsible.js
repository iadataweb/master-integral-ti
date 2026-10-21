document.addEventListener('DOMContentLoaded', function () {

    let form = $("#formMaintenancePreventiveCompany");
    let base_url = document.querySelector('meta[name="base-url"]').getAttribute('content');
    let tablaDataTableSearchCompany;
    let url;
    let url_submit;
    let url_search;
    let url_read;
    var id_maintenance;
    var id_company;
    var id_data_company;
    var modal;
    let id_object;
    let id_array = [];
    const isCorrective = window.location.pathname.includes("maintenances_corrective");
    const maintenancePath = isCorrective ? "maintenances_corrective" : "maintenances_preventive";
    const maintenanceCompanyPath = isCorrective ? "maintenances_corrective_company" : "maintenances_preventive_company";
    const maintenanceIdKey = isCorrective ? "id_maintenance_corrective" : "id_maintenance_preventive";

    url = base_url + "/inventory/assets/show_assets";

    /*--------------------------------------------------------------
    # FUNCIÓN PARA BUSCAR EMPRESA RESPONSABLE
    --------------------------------------------------------------*/
    $(document).on("click", ".js-btn-search-company", function () {
        console.log("Hola I");
        $("#ModalSearchCompany").modal("show");
        url_search = base_url + "/administration/companies/show_companies_existing";
        tablaDataTableSearchCompany = $('#tableSearchCompany').DataTable({
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
                    "id_company": id_object
                }
            },
            "language": languageDatatable(),
            "columns": [
                {"data":"id_company"},
                { "data": null, "className": 'select-checkbox', "orderable": false, "defaultContent": "" },
                
                {"data":"name_company"},
                {"data":"ruc_company"},
                {"data":"cell_phone_company"},
                {"data":"telephone_company"},
                {"data":"email_company"},       
                {"data":"date_created_company"}, 
                {"data":"options"}
            ],
            "columnDefs": [
                { "className": "dt-head-left", "targets": "_all" }
            ],
            "createdRow": function (row, data) {
                $('td', row).eq(0).attr('data-id', data.id_company);
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
            console.log("objeto es: " + object);
            id_array[object] = data_id_selected;
        } else {
            showMessage("error", "No se ha seleccionado ninguna fila.");
        }
    }

    /*--------------------------------------------------------------
    # FUNCIÓN PARA AUTOCOMPLETAR EL FORMULARIO
    --------------------------------------------------------------*/
    function auto_complete_form(id_data) {  
        show_data_one(id_data);  
        url_submit = base_url + '/maintenance/' + maintenancePath + '/relate_' + (isCorrective ? 'maintenance_corrective_company' : 'maintenance_preventive_company');
        var form_data = new FormData();
        form_data.append(maintenanceIdKey, id_maintenance ?? '');
        form_data.append("id_company", id_data ?? '');
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
                        $("#ModalSearchCompany").modal("hide");  
                    }
                },
                error: function (error) {
                    console.error("Error:", error);
                    showMessage("error", "Ocurrió un problema al procesar la solicitud.");
                }
        });
    }

    /*--------------------------------------------------------------
    # FUNCIÓN PARA MOSTRAR LOS DATOS EN EL MODAL
    --------------------------------------------------------------*/
    function show_data_one(id_company) {
        url_read = base_url + "/administration/companies/show_company";
        $.ajax({
            url: url_read,
            type: "POST",
            dataType: "json",
            data: {id_company:id_company},
            success: function (response) {
                if (response.success == false) {
                    showMessage(response.type, response.message);
                } else {

                    $("#input-name-company").val(response.result.name_company);
                    $("#input-ruc-company").val(response.result.ruc_company);
                    $("#input-cell-phone-company").val(response.result.cell_phone_company);
                    $("#input-telephone-company").val(response.result.telephone_company);
                    $("#input-email-company").val(response.result.email_company);
                    $("#textarea-description-company").val(response.result.description_company);
                    $("#textarea-observation-company").val(response.result.observation_company);                                      
                }
            },
            error: function (error) {
                console.error("Error:", error);
                showMessage("error", "Ocurrió un problema al procesar la solicitud.");
            }
        });
    }

    /*--------------------------------------------------------------
    # FUNCIÓN PARA MOSTRAR LOS DATOS EN EL MODAL
    --------------------------------------------------------------*/
    function show_data_two(id_maintenance) {
        url_read = base_url + "/maintenance/" + maintenanceCompanyPath + "/show_" + (isCorrective ? "maintenance_corrective_company" : "maintenance_preventive_company");
        let payload = {};
        payload[maintenanceIdKey] = id_maintenance;
        $.ajax({
            url: url_read,
            type: "POST",
            dataType: "json",
            data: payload,
            success: function (response) {
                if (response.success == false) {
                    showMessage(response.type, response.message);
                } else {
                    $("#input-name-company").val(response.result.name_company);
                    $("#input-ruc-company").val(response.result.ruc_company);
                    $("#input-cell-phone-company").val(response.result.cell_phone_company);
                    $("#input-telephone-company").val(response.result.telephone_company);
                    $("#input-email-company").val(response.result.email_company);
                    $("#textarea-description-company").val(response.result.description_company);
                    $("#textarea-observation-company").val(response.result.observation_company);                                      
                }
            },
            error: function (error) {
                console.error("Error:", error);
                showMessage("error", "Ocurrió un problema al procesar la solicitud.");
            }
        });
    }

    /*--------------------------------------------------------------
    # MÉTODO PARA EJECTUAR LA FUNCIÓN DE OBTENER EL DATA-ID
    --------------------------------------------------------------*/
    $("#btnSubmitAddCompany").click(function () {        
        get_selected_id("#tableSearchCompany");
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA MOSTRAR MODAL DE GESTIONAR EMPRESA RESPONSABLE
    --------------------------------------------------------------*/
    $(document).on("click", ".js-btn-company", function () {
        console.log("Modal Company");
        id_maintenance = $(this).data('id');
        show_data_two(id_maintenance);
        $("#labelResponsibleCompany").text("Empresa Responsable");
        $("#ModalResponsibleCompany").modal("show");
        // url_submit = base_url + '/inventory/assets/update_asset';
        // modal = "edit";
        // show_data(id_asset, modal);
    });

});
