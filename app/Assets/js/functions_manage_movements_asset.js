document.addEventListener('DOMContentLoaded', function () {

    let form = $("#formMovementAsset");
    let base_url = document.querySelector('meta[name="base-url"]').getAttribute('content');
    let tablaDataTable;
    let tablaDataTableSearchAsset;
    let tablaDataTableSearchSpace;
    let url;
    let url_submit;
    let url_read;
    let url_read_wanted;
    let url_search;
    let field_type;
    let id_movement_asset;
    let id_object;
    let modal;
    let id_array = {};

    url = base_url + "/movement/movements_asset/show_movements_asset";

    const access = document.getElementById('movement-access').dataset;
    const esc = value => $('<div>').text(value ?? '').html();
    $('#btnNew').toggle(access.create === '1');
    const fillFilter = (selector, values) => {
        const selected = $(selector).val();
        $(selector).empty().append(new Option('Todos', ''));
        [...new Set(values.filter(Boolean))].sort().forEach(value => $(selector).append(new Option(value, value)));
        $(selector).val(selected || '');
    };
    $.fn.dataTable.ext.search.push(function (settings, data, index) {
        if (settings.nTable.id !== 'tableManageMovementsAsset') return true;
        const row = settings.aoData[index]._aData;
        return (!$('#movement-situation-filter').val() || row.situation === $('#movement-situation-filter').val())
            && (!$('#movement-location-filter').val() || row.name_space === $('#movement-location-filter').val())
            && (!$('#movement-responsible-filter').val() || row.responsible_receiver === $('#movement-responsible-filter').val())
            && (!$('#movement-return-filter').val() || ($('#movement-return-filter').val() === 'loan' ? Number(row.id_type_movement_asset) === 5 : row.return_status === 'Vencido'));
    });
    tablaDataTable = $('#tableManageMovementsAsset').DataTable({
        ajax: {url: url, dataSrc: function (rows) {
            fillFilter('#movement-location-filter', rows.map(row => row.name_space));
            fillFilter('#movement-responsible-filter', rows.map(row => row.responsible_receiver));
            return rows;
        }},
        language: languageDatatable(), pagingType: 'simple_numbers', responsive: true,
        dom: '<"movement-table-tools"Bf>rt<"movement-table-bottom"ilp>',
        columns: [
            {data:'id_asset_movement_register',responsivePriority:1},
            {data:'code_inventory_asset', render: function (value,type,row) {
                const detail = [row.name_type_asset,row.name_brand,row.model_asset,row.serial_number_asset].filter(Boolean).join(' · ');
                return type === 'display' ? '<strong>'+esc(value)+'</strong><small class="d-block text-muted">'+esc(detail)+'</small>' : value+' '+detail;
            }},
            {data:'situation',render:function(value,type){return type === 'display' ? '<span class="badge bg-primary">'+esc(value)+'</span>' : value;}},
            {data:'name_space',defaultContent:'',render:function(value,type,row){const detail=[row.name_campus,row.floor_space !== null ? 'Piso '+row.floor_space : ''].filter(Boolean).join(' · ');return type === 'display' ? esc(value)+'<small class="d-block text-muted">'+esc(detail)+'</small>' : (value||'')+' '+detail;}},
            {data:'responsible_receiver',render:$.fn.dataTable.render.text()},
            {data:'date_event_movement_asset',defaultContent:'',render:function(value,type,row){const date=(value||'Sin fecha')+' '+(row.time_event_movement_asset||'');return type === 'display' ? esc(row.name_type_movement)+'<small class="d-block text-muted">'+esc(date)+'</small>' : date;}},
            {data:'return_status',render:function(value,type,row){const text=value==='No aplica'?'—':value+' '+(row.expected_return_date_movement_asset||'');return type === 'display' ? '<span class="'+(value==='Vencido'?'text-danger fw-bold':'')+'">'+esc(text)+'</span>' : text;}},
            {data:null,orderable:false,searchable:false,render:function(row){return '<div class="d-flex gap-1"><button type="button" class="btn btn-sm btn-secondary js-btn-view" data-id="'+Number(row.id_movement_asset)+'" title="Ver detalle">Ver</button>'+(access.create==='1'?'<button type="button" class="btn btn-sm btn-primary js-btn-move" data-id="'+Number(row.id_movement_asset)+'" data-id-asset="'+Number(row.id_asset)+'">Mover</button>':'')+'<button type="button" class="btn btn-sm btn-outline-primary js-movement-actions" data-id="'+Number(row.id_movement_asset)+'">Más</button></div>';}}
        ],
        columnDefs:[{targets:0,responsivePriority:1},{targets:7,responsivePriority:2},{targets:3,responsivePriority:3}],
        buttons:[{extend:'excelHtml5',text:'Excel',exportOptions:{columns:[0,1,2,3,4,5,6]}},{extend:'pdfHtml5',text:'PDF',orientation:'landscape',exportOptions:{columns:[0,1,2,3,4,5,6]}},{extend:'print',text:'Imprimir',exportOptions:{columns:[0,1,2,3,4,5,6]}}],
        order:[[5,'desc']]
    });
    $('#movement-situation-filter, #movement-location-filter, #movement-responsible-filter, #movement-return-filter').on('change',function(){tablaDataTable.draw();});
    $(document).on('click','.js-movement-actions',function(){
        const row=tablaDataTable.rows().data().toArray().find(row=>Number(row.id_movement_asset)===Number(this.dataset.id));
        const action=(cls,label)=>'<button type="button" class="btn btn-outline-primary '+cls+'" data-id="'+Number(row.id_movement_asset)+'" data-id-asset="'+Number(row.id_asset)+'">'+label+'</button>';
        $('#movement-actions-list').html('<a class="btn btn-outline-primary" href="'+base_url+'/movement/movements_asset/history_movement_asset/'+Number(row.id_asset)+'">Historial de movimientos</a>'+action('js-btn-format','Documento')+(row.id_floor_plan && row.coordinate_x_movement_asset !== null ? action('js-btn-location','Ver en plano'):'')+(access.edit==='1'?action('js-btn-edit','Corregir datos'):'')+(access.annul==='1'?action('js-movement-annul','Anular movimiento'):''));
        $('#movementActionsTitle').text('Más opciones');
        $('#movementActionsContext').text(row.code_inventory_asset);
        $('#ModalMovementActions').modal('show');
    });
    $('#movement-actions-list').on('click','button',function(){$('#ModalMovementActions').modal('hide');});
    $(document).on('click','.js-movement-annul',function(){
        const id=this.dataset.id;
        Swal.fire({title:'Anular movimiento',text:'Se conservará el evento y se restaurará la situación anterior. Indica el motivo.',input:'textarea',showCancelButton:true,confirmButtonText:'Anular',cancelButtonText:'Cancelar',inputValidator:value=>value.trim().length<5?'Escribe un motivo de al menos cinco caracteres.':undefined}).then(function(result){
            if (!result.value) return;
            $.post(base_url+'/movement/movements_asset/annul_movement',{id_movement_asset:id,reason:result.value,movement_token:form.find('[name="movement_token"]').val()},null,'json').done(function(response){showMessage(response.type,response.message);if(response.status)tablaDataTable.ajax.reload(null,false);}).fail(function(){showMessage('error','No se pudo anular el movimiento.');});
        });
    });

    initialize_upload();

    let step = 0;
    let allowedTypes = [];
    let origin = null;
    let lookups = {types:[],plans:[]};
    function loadWorkflowLookups() {
        return $.getJSON(base_url+'/movement/movements_asset/movement_lookups').done(function(data){
            if(!data.status) return;
            lookups=data;
            $('#select-type-movement').empty().append(new Option('Selecciona una operación',''));
            data.types.forEach(row=>$('#select-type-movement').append(new Option(row.name_type_movement,row.id_type_movement)));
            $('#select-floor-plan-movement-asset').empty().append(new Option('Sin plano seleccionado',''));
            data.plans.forEach(row=>$('<option>',{value:row.id_floor_plan,text:row.name_floor_plan+' · '+row.name_campus+' / Piso '+row.floor_floor_plan}).attr('data-image',row.current_image_floor_plan||row.current_file_floor_plan||'').appendTo('#select-floor-plan-movement-asset'));
            constrainTypes();
        }).fail(function(){showMessage('error','No se pudieron cargar las opciones del movimiento.');});
    }
    function constrainTypes() {
        $('#select-type-movement option').each(function(){this.disabled=this.value!=='' && !allowedTypes.includes(Number(this.value));});
    }
    function currentOrigin(context) {
        origin=context.current||null;
        allowedTypes=context.allowed_types||[];
        constrainTypes();
        const text=origin ? 'Origen: '+origin.name_campus+' · '+origin.name_space+' / Piso '+origin.floor_space+' · '+origin.name_type_movement+' · '+(origin.manual_responsible_movement_asset||origin.loan_recipient_name_movement_asset||origin.responsible_employee||'Sin responsable') : 'Sin movimiento previo. Puedes registrar directamente la situación existente del activo.';
        $('#movement-origin-summary').text(text);
    }
    function resetNewDestination() {
        id_array.id_space='';
        $('#input-name-space,#input-floor-space,#input-use-space,#input-name-campus').val('');
        $('#select-floor-plan-movement-asset').val('');clear_plan_location();set_plan_preview('');
        $('#input-manual-responsible-movement-asset,#textarea-observations-movement-asset,#input-expected-return-date-movement-asset').val('');
        const parts=Object.fromEntries(new Intl.DateTimeFormat('en-GB',{timeZone:lookups.timezone||'America/Lima',year:'numeric',month:'2-digit',day:'2-digit',hour:'2-digit',minute:'2-digit',hourCycle:'h23'}).formatToParts(new Date()).map(part=>[part.type,part.value]));
        $('#input-date-event-movement-asset').val(parts.day+'/'+parts.month+'/'+parts.year);
        $('#input-time-event-movement-asset').val(parts.hour+':'+parts.minute);
    }
    function prepareNewMovement(context) {
        modal='new';currentOrigin(context);
        id_array.id_asset=context.id_asset;
        id_array.id_movement_asset=context.current?context.current.id_movement_asset:'';
        url_submit=base_url+'/movement/movements_asset/'+(context.current?'move_movement_asset':'insert_movement_asset');
        resetNewDestination();
        $('#select-type-movement').val(context.current && Number(context.current.id_type_movement)===5?'4':'');
        if(!context.current)$('#input-date-event-movement-asset,#input-time-event-movement-asset').val('');
        if(context.return_destination && context.current && Number(context.current.id_type_movement)===5){
            const previous=context.return_destination;id_array.id_space=previous.id_space;
            $('#input-name-space').val(previous.name_space);$('#input-floor-space').val(previous.floor_space);$('#input-use-space').val(previous.use_space || previous.use_environment || '');$('#input-name-campus').val(previous.name_campus);
        }
        $('.js-btn-search-asset').toggle(!context.current);$('#movement-correction-group').addClass('d-none');$('#movement-correction-reason').prop('required',false).val('');
        update_movement_type_ui();goStep(0);
    }
    function validateStep(number) {
        if(number===1 && !$('#select-type-movement').val()){showMessage('warning','Selecciona el tipo de movimiento.');return false;}
        if(number===0 && !id_array.id_asset){showMessage('warning','Selecciona un activo.');return false;}
        if(number===2 && !id_array.id_space){showMessage('warning','Selecciona el destino.');return false;}
        let valid=true;
        form.find('.movement-step[data-step="'+number+'"] :input[required]').filter(':visible').each(function(){if(!this.checkValidity()){this.reportValidity();valid=false;return false;}});
        return valid;
    }
    function goStep(number) {
        step=number;
        form.find('.movement-step').addClass('d-none').filter('[data-step="'+step+'"]').removeClass('d-none');
        form.find('.movement-steps li').removeClass('active').removeAttr('aria-current').filter('[data-step="'+step+'"]').addClass('active').attr('aria-current','step');
        $('#movement-prev').toggle(step>0);$('#movement-next').toggle(step<4);$('#movement-save').toggle(step===4);
        if(step===4){
            form.find('.movement-step[data-step="4"] > p').text(modal==='edit'?'La corrección conserva los datos anteriores y registra el motivo.':'Registrar crea un nuevo evento y conserva el historial del activo.');
            const values=[['Activo',$('#input-code-inventory-asset').val()],['Origen',origin?origin.name_space:'Sin movimiento previo'],['Operación',$('#select-type-movement option:selected').text()],['Destino',[$('#input-name-campus').val(),$('#input-name-space').val(),'Piso '+$('#input-floor-space').val()].join(' · ')],['Responsable',$('#input-manual-responsible-movement-asset').val()||$('#input-loan-recipient-name').val()||'Sin responsable'],['Fecha y hora',[$('#input-date-event-movement-asset').val()||'Fecha no definida',$('#input-time-event-movement-asset').val()||'Hora no definida'].join(' · ')],['Retorno',Number($('#select-type-movement').val())===5?($('#check-loan-return-indefinite').is(':checked')?'Indeterminado':$('#input-expected-return-date-movement-asset').val()):'No aplica']];
            if(modal==='edit') values.push(['Motivo',$('#movement-correction-reason').val()]);
            $('#movement-review').empty();values.forEach(([label,value])=>$('#movement-review').append('<div><span>'+esc(label)+'</span><strong>'+esc(value||'No definido')+'</strong></div>'));
            $('#btnSubmit').text(modal==='edit'?'Confirmar corrección':'Confirmar movimiento');
        }
        document.querySelector('#ModalCRUD .modal-body').scrollTop=0;
    }
    $('#movement-next').on('click',function(){if(validateStep(step))goStep(step+1);});
    $('#movement-prev').on('click',function(){goStep(step-1);});

    $('#btnNew').prop('disabled',true);
    const lookupReady = loadWorkflowLookups();
    lookupReady.done(function(data){
        $('#btnNew').prop('disabled',!data.status);
        const assetId=Number(new URLSearchParams(window.location.search).get('asset'));
        if(data.status && assetId>0 && access.create==='1') {
            $('#btnNew').trigger('click');
            $.post(base_url+'/movement/movements_asset/asset_context',{id_asset:assetId},null,'json').done(function(context){
                if(!context.status){showMessage('warning',context.message);return;}
                const asset=context.result;
                $('#input-code-inventory-asset').val(asset.code_inventory_asset);$('#input-name-type-asset').val(asset.name_type_asset);$('#input-name-category-asset').val(asset.name_category_asset);$('#input-name-brand').val(asset.name_brand);$('#input-name-condition-asset').val(asset.name_condition_asset);$('#textarea-descriptions-asset').val(asset.descriptions_asset);
                prepareNewMovement(asset);
            });
        }
    });

    $("#select-loan-recipient-type").on("change", update_loan_type_ui);
    $("#check-loan-return-indefinite").on("change", update_movement_type_ui);
    $("#select-floor-plan-movement-asset").on("change", function () {
        const selectedImage = $(this).find(":selected").data("image") || $(this).find(":selected").data("file") || "";
        set_plan_preview(selectedImage);
        if (!$(this).val()) {
            clear_plan_location();
        }
    });
    $("#btn-clear-plan-location").on("click", function () {
        clear_plan_location();
    });
    $("#movement-plan-overlay").on("click", function (event) {
        if (!$("#select-floor-plan-movement-asset").val()) {
            showMessage("warning", "Seleccione un plano antes de marcar la ubicacion.");
            return;
        }
        const image = document.getElementById("img-movement-floor-plan");
        if (!image || image.classList.contains("d-none")) {
            showMessage("warning", "Seleccione un plano antes de marcar la ubicacion.");
            return;
        }
        const rect = image.getBoundingClientRect();
        if (!rect.width || !rect.height) {
            return;
        }
        const x = ((event.clientX - rect.left) / rect.width) * 100;
        const y = ((event.clientY - rect.top) / rect.height) * 100;
        set_plan_marker(x, y);
    });

    $("#btnNew").click(function () {
        url_submit = base_url + '/movement/movements_asset/insert_movement_asset';
        id_array = {};
        id_object = "";
        form.trigger("reset");
        clear_uploaded_files();
        $("#input-status-process-movement-asset").val("Registrado");
        $("#select-floor-plan-movement-asset").val("");
        clear_plan_location();
        set_plan_preview("");
        update_movement_type_ui();
        modal='new';origin=null;allowedTypes=[1,2,3,4,5];constrainTypes();
        $('#input-date-event-movement-asset,#input-time-event-movement-asset').val('').prop('required',false).prop('disabled',false);
        $('#movement-origin-summary').text('Selecciona un activo para consultar su situación actual.');
        $('.js-btn-search-asset').show();$('#movement-correction-group').addClass('d-none');$('#movement-correction-reason').prop('required',false).val('');
        $('#movementCrudTitle').text('Registrar movimiento');goStep(0);
        $("#btnSubmit").text("Guardar");
        $("#ModalCRUD").modal("show");
    });

    $(document).on("click", ".js-btn-search-asset", function () {
        $("#ModalCRUD").modal("hide");
        $("#ModalSearchAsset").modal("show");
        url_search = base_url + "/movement/movements_asset/available_assets";
        $('#btnSubmitAddAsset').prop('disabled', true);
        tablaDataTableSearchAsset = $('#tableSearchAsset').DataTable({
            "pagingType": "simple_numbers",
            "aProcessing": true,
            "aServerSide": true,
            "ordering": false,
            "select": {style: "single"},
            "responsive": true,
            "destroy": true,
            "ajax": {
                "url": url_search,
                "method": "POST",
                "dataSrc": "",
                "data": {
                    "id_asset": id_object
                }
            },
            "language": languageDatatable(),
            "columns": [
                {data:'id_asset',responsivePriority:1},
                {data:null,className:'select-checkbox',orderable:false,defaultContent:''},
                {data:'code_inventory_asset',render:$.fn.dataTable.render.text()},
                {data:'name_type_asset',render:$.fn.dataTable.render.text()},
                {data:'name_brand',render:$.fn.dataTable.render.text()},
                {data:'model_asset',defaultContent:'',render:$.fn.dataTable.render.text()},
                {data:'serial_number_asset',defaultContent:'',render:$.fn.dataTable.render.text()},
                {data:'situation',render:$.fn.dataTable.render.text()},
                {data:'name_space',defaultContent:'Sin ubicación',render:$.fn.dataTable.render.text()}
            ],
            "columnDefs": [
                { "className": "dt-head-left", "targets": "_all" }
            ],
            "createdRow": function (row, data) {
                $('td', row).eq(0).attr('data-id', data.id_asset);
            },
            drawCallback: function () {
                const selected = this.api().rows({selected:true}).count();
                $('#btnSubmitAddAsset').prop('disabled', selected === 0);
            }
        });
        $('#tableSearchAsset').off('select.dt.pendingAsset deselect.dt.pendingAsset').on('select.dt.pendingAsset deselect.dt.pendingAsset', function () {
            $('#btnSubmitAddAsset').prop('disabled', tablaDataTableSearchAsset.rows({selected:true}).count() === 0);
        });
    });

    $(document).on("click", ".js-btn-search-space", function () {
        $("#ModalCRUD").modal("hide");
        $("#ModalSearchSpace").modal("show");
        url_search = base_url + "/movement/movements_asset/available_spaces";
        tablaDataTableSearchSpace = $('#tableSearchSpace').DataTable({
            "pagingType": "simple_numbers",
            "aProcessing": true,
            "aServerSide": true,
            "ordering": false,
            "select": {style: "single"},
            "responsive": true,
            "destroy": true,
            "ajax": {
                "url": url_search,
                "dataSrc": ""
            },
            "language": languageDatatable(),
            "columns": [
                { "data": "id_space" },
                { "data": null, "className": 'select-checkbox', "orderable": false, "defaultContent": "" },
                
                { "data": "floor_space" },
                { "data": "name_space" },
                { "data": "name_campus" }
            ],
            "columnDefs": [
                { "className": "dt-head-left", "targets": "_all" }
            ],
            "createdRow": function (row, data) {
                $('td', row).eq(0).attr('data-id', data.id_space);
            },
        });
    });

    $('#ModalSearchAsset,#ModalSearchSpace').on('hidden.bs.modal',function(){if($('#ModalCRUD').hasClass('show'))return;$('#ModalCRUD').modal('show');});

    function get_selected_id(table_id, buttonSelector) {
        let selected_row = $(table_id).find('tr.selected');
        if (selected_row.length > 0) {
            let first_td = selected_row.find('td:first');
            let data_id_selected = $(first_td).attr('data-id');
            set_search_button_loading(buttonSelector, true);
            auto_complete_form(data_id_selected, buttonSelector);
            let object = table_id.replace('#tableSearch', 'id_').toLowerCase();
            id_array[object] = data_id_selected;
        } else {
            showMessage("error", "No se ha seleccionado ninguna fila.");
            set_search_button_loading(buttonSelector, false);
        }
    }

    function auto_complete_form(id_data, buttonSelector) {
        let dataToSend = {};
        if (field_type === "asset") {
            dataToSend = { id_asset: id_data };
        } else if (field_type === "space") {
            dataToSend = { id_space: id_data };
        }
        $.ajax({
            url: url_read_wanted,
            type: "POST",
            dataType: "json",
            data: dataToSend,
            success: function (response) {
                if (response.status === false || response.success === false) {
                    showMessage(response.type, response.message);
                } else {
                    if (field_type == "asset") {
                        if (response.result.current) {
                            showMessage('warning','Este activo ya tiene un movimiento vigente. Actualiza la lista y usa Mover.');
                            tablaDataTableSearchAsset.ajax.reload();return;
                        }
                        $("#input-code-inventory-asset").val(response.result.code_inventory_asset);
                        $("#input-name-type-asset").val(response.result.name_type_asset);
                        $("#input-name-category-asset").val(response.result.name_category_asset);
                        $("#input-name-brand").val(response.result.name_brand);
                        $("#input-name-condition-asset").val(response.result.name_condition_asset);
                        $("#textarea-descriptions-asset").val(response.result.descriptions_asset);
                        prepareNewMovement(response.result);
                        $('#movementCrudTitle').text('Registrar movimiento');
                        $("#ModalSearchAsset").modal("hide");
                        $('#ModalCRUD').modal('show');
                    } else if (field_type == "space") {
                        $("#input-name-campus").val(response.result.name_campus);
                        $("#input-name-space").val(response.result.name_space);
                        $("#input-floor-space").val(response.result.floor_space);
                        const space=response.result;
                        lookups.plans.forEach(function(plan){
                            const matching=String(plan.name_campus)===String(space.name_campus) && Number(plan.floor_floor_plan)===Number(space.floor_space);
                            $('#select-floor-plan-movement-asset option[value="'+Number(plan.id_floor_plan)+'"]').prop('disabled',!matching);
                        });
                        $('#select-floor-plan-movement-asset').val('');clear_plan_location();set_plan_preview('');
                        $("#ModalSearchSpace").modal("hide");$('#ModalCRUD').modal('show');
                    } else {
                        showMessage("error", "No se ha encontrado informacion.");
                    }
                }
            },
            complete: function () {
                set_search_button_loading(buttonSelector, false);
            }
        });
    }

    $("#btnSubmitAddAsset").click(function () {
        if ($(this).prop("disabled")) {
            return;
        }
        url_read_wanted = base_url + "/movement/movements_asset/asset_context";
        field_type = "asset";
        get_selected_id("#tableSearchAsset", "#btnSubmitAddAsset");
    });

    $("#btnSubmitAddSpace").click(function () {
        if ($(this).prop("disabled")) {
            return;
        }
        url_read_wanted = base_url + "/movement/movements_asset/space_context";
        field_type = "space";
        get_selected_id("#tableSearchSpace", "#btnSubmitAddSpace");
    });

    function set_search_button_loading(buttonSelector, isLoading) {
        const $button = $(buttonSelector);
        if ($button.length === 0) {
            return;
        }

        const $label = $button.find(".btn-label");
        if (!$button.data("original-label")) {
            $button.data("original-label", ($label.text() || $button.text()).trim() || "Agregar");
        }

        $button.prop("disabled", isLoading);
        if (isLoading) {
            $label.text("Agregando...");
            $button.addClass("disabled");
        } else {
            $label.text($button.data("original-label"));
            $button.removeClass("disabled");
        }
    }

    $(document).on("click", ".js-btn-view", function () {
        id_movement_asset = $(this).data('id');
        modal = "view";
        show_data(id_movement_asset);
    });

    $(document).on("click", ".js-btn-edit", function () {
        url_submit = base_url + '/movement/movements_asset/update_movement_asset';
        id_movement_asset = $(this).data('id');
        id_object = $(this).data('id-asset');
        modal = "edit";
        show_data(id_movement_asset);
    });

    $(document).on("click", ".js-btn-move", function () {
        url_submit = base_url + '/movement/movements_asset/move_movement_asset';
        id_movement_asset = $(this).data('id');
        id_object = $(this).data('id-asset');
        modal = "move";
        show_data(id_movement_asset);
    });

    $(document).on("click", ".js-btn-format", function () {
        const idMovement = $(this).data('id');
        window.open(base_url + '/movement/movements_asset/format_movement_asset/' + idMovement, '_blank');
    });

    $(document).on("click", ".js-btn-location", function () {
        const idMovement = $(this).data('id');
        $.post(base_url + "/movement/movements_asset/show_movement_plan_location", { id_movement_asset: idMovement }, function (response) {
            const data = typeof response === "object" ? response : JSON.parse(response);
            if (!data.status) {
                showMessage(data.type || "warning", data.message || "No se ha definido una ubicacion en plano.");
                return;
            }
            window.open(base_url + "/movement/movements_asset/location_movement_asset/" + idMovement, "_blank");
        }).fail(function () {
            showMessage("error", "No se pudo verificar la ubicacion en plano.");
        });
    });

    $("#select-type-movement").on("change", update_movement_type_ui);

    function show_data(id_movement_asset) {
        url_read = base_url + '/movement/movements_asset/show_movement_asset';
        $.ajax({
            url: url_read,
            type: "POST",
            dataType: "json",
            data: { id_movement_asset: id_movement_asset },
            success: function (response) {
                if (response.status === false) {
                    showMessage(response.type, response.message);
                    return;
                }

                if (modal == "edit" || modal == "move") {
                    id_array={id_movement_asset:response.result.id_movement_asset,id_asset:response.result.id_asset,id_space:response.result.id_space};
                    fill_form(response.result);

                    if (modal == "edit") {
                        load_existing_attachments((response.attachments ?? []).map(file=>({...file,name_attachment:esc(file.name_attachment)})));
                        $("#table-handler .js-remove-existing").hide();
                        origin=response.result;allowedTypes=[1,2,3,4,5];constrainTypes();
                        $('.js-btn-search-asset').hide();$('#movement-correction-group').removeClass('d-none');$('#movement-correction-reason').prop('required',true).val('');
                        $('#movement-origin-summary').text('Corrección del movimiento vigente. El activo no puede cambiar.');goStep(0);
                        $('#movementCrudTitle').text('Corregir datos');
                        $("#btnSubmit").text("Actualizar");
                    } else {
                        clear_uploaded_files();
                        $.post(base_url+'/movement/movements_asset/asset_context',{id_asset:response.result.id_asset},null,'json').done(function(context){if(context.status)prepareNewMovement(context.result);});
                        $('#movementCrudTitle').text('Registrar movimiento');
                        $("#btnSubmit").text("Registrar Movimiento");
                    }

                    $("#ModalCRUD").modal("show");
                    id_array.id_movement_asset = response.result.id_movement_asset;
                    id_array.id_asset = response.result.id_asset;
                    id_array.id_space = response.result.id_space;
                }

                if (modal == "view") {
                    $("#cell-code-inventory-asset").text(response.result.code_inventory_asset);
                    $("#cell-name-type-asset").text(response.result.name_type_asset);
                    $("#cell-name-category-asset").text(response.result.name_category_asset);
                    $("#cell-name-brand").text(response.result.name_brand);
                    $("#cell-name-condition-asset").text(response.result.name_condition_asset);
                    $("#cell-name-campus").text(response.result.name_campus);
                    $("#cell-name-space").text(response.result.name_space);
                    $("#cell-floor-space").text(response.result.floor_space);
                    $("#cell-name-type-movement").text(response.result.name_type_movement);
                    $("#cell-responsible-employee-movement-asset").text(response.result.responsible_receiver || response.result.responsible_employee);
                    $("#cell-date-event-movement-asset").text(response.result.date_event_movement_asset);
                    $("#cell-time-event-movement-asset").text(response.result.time_event_movement_asset || "No definido");
                    $("#cell-expected-return-date-movement-asset").text(response.result.expected_return_date_movement_asset);
                    $("#cell-document-code-movement-asset").text(response.result.document_code_movement_asset);
                    $("#cell-status-process-movement-asset").text(response.result.status_process_movement_asset);
                    $("#cell-floor-plan-movement-asset").text(response.result.name_floor_plan || "No definido");
                    $("#cell-coordinate-movement-asset").text(format_plan_coordinate(response.result.coordinate_x_movement_asset, response.result.coordinate_y_movement_asset));
                    const hasPlanLocation = response.result.current_file_floor_plan && response.result.coordinate_x_movement_asset !== null && response.result.coordinate_y_movement_asset !== null;
                    $("#btn-open-movement-plan-location").toggleClass("d-none", !hasPlanLocation).attr("href", base_url + "/movement/movements_asset/location_movement_asset/" + response.result.id_movement_asset);
                    const isLoan = String(response.result.id_type_movement) === "5";
                    $(".movement-loan-detail").toggleClass("d-none", !isLoan);
                    $("#cell-loan-recipient-type-movement-asset").text(response.result.loan_recipient_type_label || "No aplica");
                    $("#cell-loan-recipient-name-movement-asset").text(response.result.loan_recipient_name_movement_asset || "No registrado");
                    $("#cell-loan-recipient-document-movement-asset").text(response.result.loan_recipient_document_movement_asset || "No registrado");
                    $("#cell-loan-return-indefinite-movement-asset").text(Number(response.result.loan_return_indefinite_movement_asset) === 1 ? "Indeterminada" : "Con fecha definida");
                    $("#cell-date-created-movement-asset").text(response.result.date_created_movement_asset);

                    render_rich_text("#cell-observations-movement-asset", response.result.observations_movement_asset);
                    render_movement_attachments("#tbody-attachments-movement", response.attachments ?? []);

                    $("#ModalSee .modal-title").text("Detalle del movimiento");
                    $("#ModalSee").modal("show");
                }
            },
            error: function (error) {
                console.error("Error:", error);
                showMessage("error", "Ocurrio un problema al procesar la solicitud.");
            }
        });
    }

    function fill_form(result) {
        form.trigger("reset");
        clear_uploaded_files();

        $("#textarea-descriptions-asset").val(result.descriptions_asset || '');
        $("#input-code-inventory-asset").val(result.code_inventory_asset);
        $("#input-name-type-asset").val(result.name_type_asset);
        $("#input-name-category-asset").val(result.name_category_asset);
        $("#input-name-brand").val(result.name_brand);
        $("#input-name-condition-asset").val(result.name_condition_asset);

        $("#input-name-campus").val(result.name_campus);
        $("#input-name-space").val(result.name_space);
        $("#input-floor-space").val(result.floor_space);

        $("#select-floor-plan-movement-asset").val(result.id_floor_plan_movement_asset || "");
        const selectedImage = $("#select-floor-plan-movement-asset").find(":selected").data("image") || result.current_image_floor_plan || result.current_file_floor_plan || "";
        set_plan_preview(selectedImage);
        if (result.coordinate_x_movement_asset !== null && result.coordinate_y_movement_asset !== null) {
            set_plan_marker(result.coordinate_x_movement_asset, result.coordinate_y_movement_asset);
        } else {
            clear_plan_location(false);
        }

        $("#select-type-movement").val(result.id_type_movement);
        $("#input-manual-responsible-movement-asset").val(result.manual_responsible_movement_asset || result.responsible_employee || "");
        $("#input-status-process-movement-asset").val(result.status_process_movement_asset || "Registrado");
        $("#select-loan-recipient-type").val(result.loan_recipient_type_movement_asset || "");
        $("#input-loan-recipient-name").val(result.loan_recipient_name_movement_asset || (Number(result.id_type_movement)===5 ? result.manual_responsible_movement_asset || result.responsible_employee : "") || "");
        $("#input-loan-recipient-document").val(result.loan_recipient_document_movement_asset || "");
        $("#check-loan-return-indefinite").prop("checked", Number(result.loan_return_indefinite_movement_asset) === 1);
        if (result.date_event_movement_asset !== 'No definido') {
            $("#input-date-event-movement-asset").val(format_date_for_input(result.date_event_movement_asset));
        } else {
            $("#input-date-event-movement-asset").val("");
        }
        $("#input-time-event-movement-asset").val(format_time_input(result.time_event_movement_asset));
        if (result.expected_return_date_movement_asset !== 'No aplica') {
            $("#input-expected-return-date-movement-asset").val(result.expected_return_date_movement_asset);
        } else {
            $("#input-expected-return-date-movement-asset").val("");
        }
        $("#textarea-observations-movement-asset").val(result.observations_movement_asset);
        update_movement_type_ui();
    }

    function update_movement_type_ui() {
        const type = String($("#select-type-movement").val() || "");
        const isLoan = type === "5";
        const isIndefiniteReturn = $("#check-loan-return-indefinite").is(":checked");
        const helperText = {
            "1": "Ingreso: registra la ubicacion inicial del activo.",
            "2": "Asignacion: puede registrar solo ambiente, almacen o responsable si corresponde.",
            "3": "Transferencia: seleccione un ambiente diferente al vigente.",
            "4": "Devolucion o almacenamiento: registra el retorno del activo a un ambiente o almacen.",
            "5": "Prestamo: registre quien recibe el activo y defina si la devolucion tiene fecha o queda indeterminada."
        };
        const locationConfig = {
            "1": {
                title: "Ubicacion inicial",
                helper: "Seleccione el ambiente o almacen donde ingresa inicialmente el activo.",
                code: "Codigo de ubicacion",
                name: "Ambiente / almacen inicial"
            },
            "2": {
                title: "Ambiente de asignacion",
                helper: "Seleccione el ambiente interno o ubicacion fisica donde quedara asignado el activo. El responsable es opcional.",
                code: "Codigo de ambiente",
                name: "Ambiente asignado"
            },
            "3": {
                title: "Ambiente destino",
                helper: "Seleccione un ambiente diferente al vigente para mantener la trazabilidad de la transferencia.",
                code: "Codigo de destino",
                name: "Ambiente destino"
            },
            "4": {
                title: "Retorno / almacen",
                helper: "Seleccione el almacen, ambiente de retorno o ubicacion donde quedara resguardado el activo.",
                code: "Codigo de retorno",
                name: "Almacen / ambiente de retorno"
            },
            "5": {
                title: "Ubicacion de control del prestamo",
                helper: "Seleccione la ubicacion interna responsable del control del prestamo; el receptor se registra en Datos del Prestamo.",
                code: "Codigo de ubicacion de control",
                name: "Ubicacion de control"
            }
        };
        const location = locationConfig[type] || {
            title: "Ubicacion / Ambiente",
            helper: "Seleccione la ubicacion interna, ambiente o almacen donde quedara registrado el activo.",
            code: "Codigo de Ubicacion",
            name: "Ambiente / Almacen"
        };

        $(".movement-field-manual-responsible").toggleClass("d-none", type === "" || isLoan);
        if(isLoan)$("#input-manual-responsible-movement-asset").val("");
        $("#input-manual-responsible-movement-asset").prop("required", false);
        $(".movement-field-manual-responsible .form-group").removeClass("mandatory");
        $("#space-section-title").text(location.title);
        $("#space-flow-helper").text(location.helper);
        $("#label-name-space").text(location.name);

        $(".movement-loan-section").toggleClass("d-none", !isLoan);
        $("#select-loan-recipient-type").prop("required", isLoan);
        $("#label-date-event-movement-asset").text(isLoan ? "Fecha de entrega (opcional)" : "Fecha del evento (opcional)");

        $(".movement-field-return-date").toggleClass("d-none", !isLoan);
        $("#input-expected-return-date-movement-asset")
            .prop("required", isLoan && !isIndefiniteReturn)
            .prop("disabled", isLoan && isIndefiniteReturn);
        $(".movement-field-return-date .form-group").toggleClass("mandatory", isLoan && !isIndefiniteReturn);

        if (!isLoan) {
            $("#input-expected-return-date-movement-asset").val("");
            $("#select-loan-recipient-type").val("");
            $("#input-loan-recipient-name").val("");
            $("#input-loan-recipient-document").val("");
            $("#check-loan-return-indefinite").prop("checked", false);
        }
        if (isLoan && isIndefiniteReturn) {
            $("#input-expected-return-date-movement-asset").val("");
        }
        update_loan_type_ui();
        $("#movement-type-helper").text(helperText[type] || "Seleccione el tipo de movimiento para ver los datos requeridos.");
    }

    function update_loan_type_ui() {
        const isLoan = String($("#select-type-movement").val() || "") === "5";
        const loanType = String($("#select-loan-recipient-type").val() || "");
        const needsName = isLoan && ["internal", "external_person", "external_company"].includes(loanType);
        const labelByType = {
            "internal": "Persona interna que recibe",
            "external_person": "Persona externa que recibe",
            "external_company": "Empresa o institucion que recibe"
        };

        $("#input-loan-recipient-name").prop("required", needsName);
        $("#input-loan-recipient-name")
            .closest(".form-group")
            .toggleClass("mandatory", needsName)
            .find("label")
            .text(labelByType[loanType] || "Persona, Empresa o Institucion");
    }

    function render_movement_attachments(tbodySelector, attachments) {
        const $tbody = $(tbodySelector);
        $tbody.empty();

        if (!Array.isArray(attachments) || attachments.length === 0) {
            $tbody.append('<tr><td colspan="3" class="text-muted text-center">Sin registros.</td></tr>');
            return;
        }

        attachments.forEach((item, index) => {
            const safeName = (item.name_attachment || "").toString();
            const safeUrl = (item.route_attachment || "").toString();
            const normalizedUrl = build_attachment_url(safeUrl);
            const disabledClass = normalizedUrl === "#" ? " disabled" : "";
            const row = `
                <tr>
                    <td>${formatRecordId(item, 'id_attachment')}</td>
                    <td>${escape_html(safeName)}</td>
                    <td>
                        <a href="${normalizedUrl}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary${disabledClass}">Abrir</a>
                    </td>
                </tr>
            `;
            $tbody.append(row);
        });
    }

    function set_plan_preview(routeFile) {
        const cleanRoute = (routeFile || "").toString().trim();
        const hasFile = cleanRoute !== "";
        $("#img-movement-floor-plan").attr("src", hasFile ? build_attachment_url(cleanRoute) : "").toggleClass("d-none", !hasFile);
        $("#movement-plan-picker").toggleClass("has-plan", hasFile);
        $("#movement-plan-empty").toggleClass("d-none", hasFile);
    }

    function set_plan_marker(x, y) {
        const safeX = Math.max(0, Math.min(100, parseFloat(x)));
        const safeY = Math.max(0, Math.min(100, parseFloat(y)));
        if (Number.isNaN(safeX) || Number.isNaN(safeY)) {
            clear_plan_location();
            return;
        }
        $("#input-coordinate-x-movement-asset").val(safeX.toFixed(2));
        $("#input-coordinate-y-movement-asset").val(safeY.toFixed(2));
        $("#movement-plan-marker")
            .removeClass("d-none")
            .css({ left: safeX + "%", top: safeY + "%" });
        $("#movement-plan-coordinate-label").text(format_plan_coordinate(safeX, safeY));
    }

    function clear_plan_location(clearSelect = true) {
        if (clearSelect) {
            $("#select-floor-plan-movement-asset").val("");
            set_plan_preview("");
        }
        $("#input-coordinate-x-movement-asset").val("");
        $("#input-coordinate-y-movement-asset").val("");
        $("#movement-plan-marker").addClass("d-none").css({ left: "", top: "" });
        $("#movement-plan-coordinate-label").text("Coordenada no definida.");
    }

    function format_time_input(value) {
        const time = (value || "").toString();
        if (time === "" || time === "No definido") {
            return "";
        }
        return time.substring(0, 5);
    }

    function format_date_for_input(value) {
        const date = (value || "").toString();
        const match = date.match(/^(\d{4})-(\d{2})-(\d{2})$/);
        return match ? match[3] + "/" + match[2] + "/" + match[1] : date;
    }

    function format_plan_coordinate(x, y) {
        if (x === null || y === null || x === "" || y === "") {
            return "No definida";
        }
        return "X " + parseFloat(x).toFixed(2) + "% / Y " + parseFloat(y).toFixed(2) + "%";
    }

    function build_attachment_url(routeAttachment) {
        const cleanRoute = (routeAttachment || "").toString().trim();
        if (cleanRoute === "") {
            return "#";
        }
        if (cleanRoute.startsWith("http://") || cleanRoute.startsWith("https://")) {
            return cleanRoute;
        }
        if (cleanRoute.startsWith("/")) {
            return base_url + cleanRoute;
        }
        return base_url + "/" + cleanRoute.replace(/^\/+/, "");
    }

    function render_rich_text(containerSelector, rawText) {
        const $container = $(containerSelector);
        const text = (rawText || "").toString().trim();

        if (text === "") {
            $container.text("Sin informacion registrada.");
            return;
        }

        const items = text
            .split(/,|\r?\n|;/)
            .map(item => item.trim())
            .filter(item => item.length > 0);

        if (items.length <= 1) {
            $container.text(text);
            return;
        }

        const listHtml = items.map(item => `<li>${escape_html(item)}</li>`).join("");
        $container.html(`<ul class="mb-0 ps-3">${listHtml}</ul>`);
    }

    function escape_html(value) {
        return (value || "").toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/\"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    form.attr('novalidate','novalidate');
    form.submit(function (e) {
        e.preventDefault();
        for(let index=0;index<4;index++){goStep(index);if(!validateStep(index))return;}goStep(4);
        if (form[0].checkValidity()) {
            let form_data = new FormData(form[0]);
            form_data = result_data(form_data);
            form_data.append("id_movement_asset", id_array.id_movement_asset ?? '');
            form_data.append("id_asset", id_array.id_asset ?? '');
            form_data.append("id_space", id_array.id_space ?? '');
            $("#movement-save").prop("disabled",true);
            $.ajax({
                url: url_submit,
                dataType: "json",
                complete: function(){ $("#movement-save").prop("disabled",false); },
                type: "POST",
                data: form_data,
                processData: false,
                contentType: false,
                success: function (response) {
                    response = typeof response === "string" ? JSON.parse(response) : response;
                    if (response.status === false) {
                        showMessage(response.type, response.message);
                    } else {
                        showMessage(response.type, response.message);
                        $("#ModalCRUD").modal("hide");
                        clear_uploaded_files();
                        tablaDataTable.ajax.reload(null, false);
                    }
                },
                error: function (error) {
                    console.error("Error:", error);
                    showMessage("error", "Ocurrio un problema al procesar la solicitud.");
                }
            });
        } else {
            form[0].reportValidity();
        }
    });

    $(document).on("click", ".js-btn-history", function () {
        id_object = $(this).data('id-asset');
        window.open(base_url + '/movement/movements_asset/history_movement_asset/' + id_object);
    });
});
