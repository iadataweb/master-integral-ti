// document.addEventListener('DOMContentLoaded', function () {

//     let form;
//     let base_url = document.querySelector('meta[name="base-url"]').getAttribute('content');
//     const channel = new BroadcastChannel('tickets_updates');
//     let code_inventory_asset = window.location.href.match(/\/(\d+)$/)[1];
//     let url_read;
//     let id_object;
//     let id_array = [];

//     auto_complete_form(code_inventory_asset);

//     /*--------------------------------------------------------------
//     # FUNCIÓN PARA AUTOCOMPLETAR EL FORMULARIO
//     --------------------------------------------------------------*/
//     function auto_complete_form(code_inventory_asset) {
//         url_submit = base_url + '/scan_quick/relate_ticket_asset';
//         var form_data = new FormData();
//         form_data.append("code_inventory_asset", code_inventory_asset ?? '');
//         $.ajax({
//             url: url_read,
//             type: "POST",
//             dataType: "json",
//             data: form_data,
//             success: function (response) {
//                 if (response.success == false) {
//                     showMessage(response.type, response.message);
//                 } else {
//                     $("#input-code-inventory-asset").val(response.result.code_inventory_asset);
//                     $("#input-name-type-asset").val(response.result.name_type_asset);
//                     $("#input-name-category-asset").val(response.result.name_category_asset);
//                     $("#input-name-brand").val(response.result.name_brand);
//                     $("#input-name-condition-asset").val(response.result.name_condition_asset);
//                     $("#textarea-descriptions-asset").val(response.result.descriptions_asset);
//                     $("#ModalSearchAsset").modal("hide");                    
//                 }
//             }
//         });
//     }

// });
