document.addEventListener('DOMContentLoaded', function () {
    const baseUrl=document.querySelector('meta[name="base-url"]').content;
    const list=$('#ModalSoftware'), editor=$('#ModalSoftwareEditor'), detail=$('#ModalSoftwareDetail'), form=$('#formSoftware');
    let assetId=null, rows=[], catalogs={}, permissions={}, busy=false;
    const fields=[['licenses','#software-license','id_type_license_software','name_type_license'],['types','#software-type','id_type_software_software','name_type_software'],['architectures','#software-architecture','id_architecture_software','name_architecture']];
    function switchModal(from,to) {
        if (!from.is(':visible')) {to.modal('show'); return;}
        const hide=()=>from.modal('hide');
        from.one('shown.bs.modal',hide).one('hidden.bs.modal',function(){from.off('shown.bs.modal',hide);to.modal('show');});
        from.modal('hide');
    }
    function messageRow(text) { $('#software-list').empty().append($('<tr>').append($('<td>',{colspan:7,class:'text-center text-muted py-4',text:text}))); }
    function action(label,cls,id,icon) {
        return $('<button>',{type:'button',class:'btn btn-sm btn-primary '+cls,'data-id':id,title:label,'aria-label':label}).append($('<i>',{class:'bx '+icon,'aria-hidden':'true'}));
    }
    function loadList() {
        $('#btn-new-asset-software').addClass('d-none');
        messageRow('Cargando…');
        const requested=assetId;
        return $.ajax({url:baseUrl+'/inventory/softwares/show_softwares_by_asset',type:'POST',dataType:'json',data:{id_asset_software:requested}})
            .done(function(response){
                if (requested!==assetId) return;
                if (!response.status) {messageRow('No se pudo consultar el programa.');showMessage(response.type,response.message);return;}
                rows=response.result||[]; catalogs=response.catalogs||{};permissions=response.permissions||{};
                $('#software-asset-context').text(response.asset.code_inventory_asset+' · '+response.asset.name_type_asset);
                $('#btn-new-asset-software').toggleClass('d-none',!permissions.create);
                if (!rows.length) {messageRow('Sin software registrado para este activo.');return;}
                $('#software-list').empty();
                rows.forEach(function(row){
                    const tr=$('<tr>');
                    [row.id_software,row.name_software,row.version_software||'No definida',row.name_type_software||'No definido',row.name_type_license||'No definida',row.name_architecture||'No definida'].forEach(value=>tr.append($('<td>',{text:value})));
                    const actions=$('<div>',{class:'d-flex justify-content-end gap-1'}).append(action('Ver','js-view-asset-software',row.id_software,'bx-eye'));
                    if (permissions.edit) actions.append(action('Editar','js-edit-asset-software',row.id_software,'bx-pencil'));
                    if (permissions.delete) actions.append(action('Eliminar','js-delete-asset-software',row.id_software,'bx-trash').removeClass('btn-primary').addClass('btn-danger'));
                    tr.append($('<td>').append(actions));$('#software-list').append(tr);
                });
            }).fail(function(){messageRow('No se pudo consultar el programa.');showMessage('error','No se pudo cargar el programa del equipo.');});
    }
    $(document).on('click','.js-btn-software',function(){
        assetId=$(this).attr('data-id-asset');rows=[];catalogs={};permissions={};$('#software-asset-context').text('Cargando…');loadList();switchModal($('#ModalMenu'),list);
    });
    function openEditor(row) {
        if (busy || !(row?permissions.edit:permissions.create)) return;
        form[0].reset();
        $('#software-asset-id').val(assetId);$('#software-record-id').val(row?row.id_software:'');
        $('#labelSoftwareEditor').text(row?'Editar programa':'Nuevo programa');$('#btn-save-software').text(row?'Actualizar':'Guardar');
        $('#software-editor-context').text($('#software-asset-context').text());
        fields.forEach(function([catalog,selector,id,name]){
            const select=$(selector).empty().append(new Option('Seleccione una opción',''));
            (catalogs[catalog]||[]).forEach(option=>select.append(new Option(option.name,option.id)));
            if (row) {
                if (!(catalogs[catalog]||[]).some(option=>Number(option.id)===Number(row[id]))) select.append(new Option(row[name]||'Valor anterior',row[id]));
                select.val(row[id]);
            }
        });
        if (row) {$('#software-name').val(row.name_software);$('#software-version').val(row.version_software);$('#software-description').val(row.description_software);}
        switchModal(list,editor);
    }
    $('#btn-new-asset-software').on('click',()=>openEditor(null));
    $('#software-list').on('click','.js-edit-asset-software',function(){const row=rows.find(item=>Number(item.id_software)===Number(this.dataset.id));if(row)openEditor(row);});
    $('.js-cancel-software').on('click',function(){if(!busy){form[0].reset();switchModal(editor,list);}});
    editor.on('shown.bs.modal',()=>$('#software-name').trigger('focus')).on('hidden.bs.modal',()=>form[0].reset());
    $('#software-list').on('click','.js-view-asset-software',function(){
        const row=rows.find(item=>Number(item.id_software)===Number(this.dataset.id));if(!row)return;
        const values=[['#',row.id_software],['Programa',row.name_software],['Versión',row.version_software],['Tipo',row.name_type_software],['Licencia',row.name_type_license],['Arquitectura',row.name_architecture]];
        const container=$('#software-detail-fields').empty();
        values.forEach(function([name,value]){
            container.append($('<div>',{class:'asset-meta-card'}).append(
                $('<span>',{class:'asset-meta-label',text:name}),
                $('<div>',{class:'asset-meta-value',text:value==null||value===''?'No definido':String(value)})
            ));
        });
        $('#software-detail-description').text(row.description_software || 'Sin descripción.');
        switchModal(list,detail);
    });
    $('.js-back-software').on('click',()=>switchModal(detail,list));
    form.on('submit',function(event){
        event.preventDefault();if(busy)return;
        if(!$('#software-name').val().trim()){showMessage('error','Escribe el nombre del programa.');return;}
        busy=true;editor.find('button').prop('disabled',true);
        $.ajax({url:baseUrl+'/inventory/softwares/save_software',type:'POST',dataType:'json',data:form.serialize()})
            .done(function(response){
                if(response.status){loadList();editor.one('hidden.bs.modal',()=>showMessage(response.type,response.message));switchModal(editor,list);}
                else showMessage(response.type,response.message);
            }).fail(()=>showMessage('error','No se pudo guardar el programa.'))
            .always(function(){busy=false;editor.find('button').prop('disabled',false);});
    });
    $('#software-list').on('click','.js-delete-asset-software',function(){
        if(busy||!permissions.delete)return;
        const id=this.dataset.id;
        Swal.fire({title:'Eliminar programa',text:'Se retirará este registro del activo.',icon:'warning',showCancelButton:true,confirmButtonText:'Eliminar',cancelButtonText:'Cancelar',buttonsStyling:false,customClass:{confirmButton:'btn btn-danger m-2',cancelButton:'btn btn-primary m-2'}})
            .then(function(result){
                if(!(result.isConfirmed||result.value)||busy)return;busy=true;
                $.ajax({url:baseUrl+'/inventory/softwares/delete_software',type:'POST',dataType:'json',data:{id_asset_software:assetId,id_software:id}})
                    .done(function(response){if(response.status)loadList();showMessage(response.type,response.message);})
                    .fail(()=>showMessage('error','No se pudo eliminar el programa.')).always(()=>busy=false);
            });
    });
});
