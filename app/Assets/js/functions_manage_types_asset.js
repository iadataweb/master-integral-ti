document.addEventListener('DOMContentLoaded', function () {

    let form = $("#formTypeAsset");
    let base_url = document.querySelector('meta[name="base-url"]').getAttribute('content');
    let tablaDataTable;
    let url;
    let url_submit;
    let url_read;
    let url_delete;
    var id_type_asset;
    var modal;

    url = base_url + "/inventory/types_asset/show_types_asset";

    tablaDataTable = $('#tableManageTypesAsset').DataTable({
        "pagingType": "simple_numbers",
        "aProcessing":true,
        "aServerSide":true,
        "ajax": {
            "url": url,
            "dataSrc":""
        },
        "language": languageDatatable(),
        "columns": [
            {"data":"id_type_asset"},
            {"data":"name_type_asset"},
            {"data":"name_category_asset"},
            {"data":"status_active_type_asset"},
            {"data":"options"}
        ],
        "responsive":"true",
        "destroy": true,
        "idisplaylength": 10,
        "order":[[0,"desc"]] 
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA LLENAR SELECT CATEGORÍAS DE ACTIVO
    --------------------------------------------------------------*/
    $.post(base_url + "/inventory/categories_asset/combo", function(data) {
        $("#select-category-asset-type-asset").html(data);
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA MOSTRAR MODAL DE AGREGAR NUEVO REGISTRO
    --------------------------------------------------------------*/
    $("#btnNew").click(function () {
        url_submit = base_url + '/inventory/types_asset/insert_type_asset';
        form.trigger("reset");
        id_type_asset = null;
        $("#select-category-asset-type-asset").prop("disabled", false);
        $("#input-name-type-asset").prop("disabled", false);
        $("#select-status-active-type-asset").prop("disabled", false);
        $(".modal-title").text("Nuevo Registro");
        $("#btnSubmit").text("Guardar");
        $("#ModalCRUD").modal("show");
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA MOSTRAR MODAL DE REGISTRO
    --------------------------------------------------------------*/
    $(document).on("click", ".js-btn-view", function () {
        id_type_asset = $(this).data('id');
        modal = "view";
        show_data(id_type_asset, modal);
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA MOSTRAR MODAL DE EDITAR
    --------------------------------------------------------------*/
    $(document).on("click", ".js-btn-edit", function () {
        url_submit = base_url + '/inventory/types_asset/update_type_asset';
        id_type_asset = $(this).data('id');
        modal = "edit";
        show_data(id_type_asset, modal);
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA MOSTRAR LOS DATOS EN EL MODAL
    --------------------------------------------------------------*/
    function show_data(id_type_asset) {
        url_read = base_url + '/inventory/types_asset/show_type_asset';
        $.ajax({
            url: url_read,
            type: "POST",
            dataType: "json",
            data: {id_type_asset:id_type_asset},
            success: function (response) {
                if (response.status === false) {
                    showMessage(response.type, response.message);
                } else {
                    if (modal == "edit") {
                        $("#select-category-asset-type-asset").val(response.result.id_category_asset_type_asset);
                        $("#input-name-type-asset").val(response.result.name_type_asset);
                        $("#select-status-active-type-asset").val(response.result.status_active_type_asset);
                        $(".modal-title").text("Actualizar Registro");
                        $("#btnSubmit").text("Actualizar");
                        $("#ModalCRUD").modal("show");
                    }
                    if (modal == "view") {
                        $("#cell-category-asset-type-asset").text(response.result.name_category_asset || $("#select-category-asset-type-asset option[value='" + response.result.id_category_asset_type_asset + "']").text());
                        $("#cell-name-type-asset").text(response.result.name_type_asset);
                        if (response.result.status_active_type_asset == 1) {
                            $("#cell-status-active-type-asset").text("Activo");
                        } else {
                            $("#cell-status-active-type-asset").text("Inactivo");
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
        form_data.append('id_type_asset', id_type_asset);
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
        id_type_asset = $(this).data('id');
        url_delete = base_url + "/inventory/types_asset/delete_type_asset";
        Swal.fire({
            title: "¿Está seguro de eliminar el registro " + id_type_asset + " del sistema?",
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
                    data: {id_type_asset:id_type_asset},
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
