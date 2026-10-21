document.addEventListener('DOMContentLoaded', function () {
    const baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content');
    const view = $('#ModalAssetEquipmentUser');
    const editor = $('#ModalEquipmentUserEditor');
    const form = $('#formAssetEquipmentUser');
    let assetId = null;
    let currentUser = null;
    let permissions = {};
    let busy = false;

    function switchModal(from, to) {
        if (from.is(':visible')) {
            const hideWhenShown = function () { from.modal('hide'); };
            from.one('shown.bs.modal', hideWhenShown);
            from.one('hidden.bs.modal', function () {
                from.off('shown.bs.modal', hideWhenShown);
                to.modal('show');
            });
            from.modal('hide');
        } else { to.modal('show'); }
    }
    function loadUser() {
        $('#btn-new-equipment-user, #btn-edit-equipment-user, #asset-equipment-user-details').addClass('d-none');
        $('#current-asset-equipment-user').text('Cargando…');
        const requestedId = assetId;
        return $.ajax({url: baseUrl + '/inventory/assets/asset_equipment_user_data', type: 'POST', dataType: 'json', data: {id_asset: requestedId}})
            .done(function (response) {
                if (requestedId !== assetId) return;
                if (!response.status) {
                    $('#current-asset-equipment-user').text('No se pudo consultar el usuario.');
                    showMessage(response.type, response.message);
                    return;
                }
                currentUser = response.equipment_user || null;
                permissions = response.permissions || {};
                $('#asset-equipment-user-code').text(response.asset.code_inventory_asset || 'No definido');
                $('#asset-equipment-user-type').text(response.asset.name_type_asset || 'No definido');
                $('#current-asset-equipment-user').text(currentUser ? currentUser.username_asset_equipment_user : 'Sin usuario configurado.');
                $('#current-asset-equipment-user-date').text(currentUser ? 'Última actualización: ' + (currentUser.date_updated_asset_equipment_user || 'No definida') : '');
                $('#asset-equipment-user-updated-by').text(currentUser ? 'Actualizado por: ' + (currentUser.updated_by?.trim() || 'No definido') : '');
                $('#asset-equipment-user-details').toggleClass('d-none', !currentUser);
                $('#btn-new-equipment-user').toggleClass('d-none', Boolean(currentUser) || !permissions.create);
                $('#btn-edit-equipment-user').toggleClass('d-none', !currentUser || !permissions.edit);
            }).fail(function () {
                $('#current-asset-equipment-user').text('No se pudo consultar el usuario.');
                showMessage('error', 'No se pudo cargar el usuario del equipo.');
            });
    }
    $(document).on('click', '.js-btn-asset-equipment-user', function () {
        assetId = $(this).attr('data-id-asset');
        currentUser = null;
        permissions = {};
        $('#asset-equipment-user-code, #asset-equipment-user-type').text('Cargando…');
        loadUser();
        switchModal($('#ModalMenu'), view);
    });
    $('#btn-new-equipment-user, #btn-edit-equipment-user').on('click', function () {
        const creating = this.id === 'btn-new-equipment-user';
        if (busy || !(creating ? permissions.create : permissions.edit)) return;
        form[0].reset();
        $('#input-id-asset-equipment-user').val(assetId);
        $('#input-equipment-user-operation').val(creating ? 'create' : 'update');
        $('#labelEquipmentUserEditor').text(creating ? 'Nuevo usuario del equipo' : 'Editar usuario del equipo');
        $('#btn-save-equipment-user').text(creating ? 'Guardar' : 'Actualizar');
        $('#equipment-user-editor-context').text($('#asset-equipment-user-code').text());
        $('#input-username-asset-equipment-user').val(creating ? '' : currentUser.username_asset_equipment_user);
        switchModal(view, editor);
    });
    editor.on('shown.bs.modal', function () { $('#input-username-asset-equipment-user').trigger('focus'); });
    editor.on('hidden.bs.modal', function () { form[0].reset(); });
    $('.js-cancel-equipment-user').on('click', function () { if (!busy) { form[0].reset(); switchModal(editor, view); } });
    form.on('submit', function (event) {
        event.preventDefault();
        if (busy) return;
        if (!$('#input-username-asset-equipment-user').val().trim()) { showMessage('error', 'Escribe el nombre de usuario.'); return; }
        busy = true;
        editor.find('button').prop('disabled', true);
        $.ajax({url: baseUrl + '/inventory/assets/save_asset_equipment_user', type: 'POST', dataType: 'json', data: form.serialize()})
            .done(function (response) {
                if (response.status) {
                    loadUser();
                    editor.one('hidden.bs.modal', function () { showMessage(response.type, response.message); });
                    switchModal(editor, view);
                } else { showMessage(response.type, response.message); }
            }).fail(function () { showMessage('error', 'No se pudo guardar el usuario del equipo.'); })
            .always(function () { busy = false; editor.find('button').prop('disabled', false); });
    });
});
