document.addEventListener('DOMContentLoaded', function () {
    const baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content');
    const view = $('#ModalAssetConfidentialNotes');
    const editor = $('#ModalAssetConfidentialNoteEditor');
    const form = $('#formAssetConfidentialNotes');
    let assetId = null;
    let note = '';
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
        } else {
            to.modal('show');
        }
    }

    function loadNote() {
        $('#btn-new-confidential-note, #btn-edit-confidential-note, #btn-delete-confidential-note').addClass('d-none');
        $('#asset-confidential-note-content').text('Cargando…');
        $('#asset-confidential-note-date').text('');
        const requestedId = assetId;
        return $.ajax({
            url: baseUrl + '/inventory/assets/asset_confidential_note_data',
            type: 'POST', dataType: 'json', data: {id_asset: requestedId}
        }).done(function (response) {
            if (requestedId !== assetId) return;
            if (!response.status) {
                $('#asset-confidential-note-content').text('No se pudo consultar la nota.');
                showMessage(response.type, response.message);
                return;
            }
            note = response.confidential_note.confidential_note_asset || '';
            permissions = response.permissions || {};
            $('#asset-confidential-note-code').text(response.asset.code_inventory_asset || 'No definido');
            $('#asset-confidential-note-type').text(response.asset.name_type_asset || 'No definido');
            $('#asset-confidential-note-content').text(note || 'Sin nota privada registrada.');
            $('#asset-confidential-note-date').text(note ? 'Última actualización: ' + (response.confidential_note.date_updated_asset_confidential_note || 'No definida') : '');
            $('#btn-new-confidential-note').toggleClass('d-none', Boolean(note) || !permissions.create);
            $('#btn-edit-confidential-note').toggleClass('d-none', !note || !permissions.edit);
            $('#btn-delete-confidential-note').toggleClass('d-none', !note || !permissions.delete);
        }).fail(function () {
            $('#asset-confidential-note-content').text('No se pudo consultar la nota.');
            showMessage('error', 'No se pudo cargar la nota privada.');
        });
    }

    $(document).on('click', '.js-btn-asset-confidential-notes', function () {
        assetId = $(this).attr('data-id-asset');
        note = '';
        permissions = {};
        $('#asset-confidential-note-code, #asset-confidential-note-type').text('Cargando…');
        loadNote();
        switchModal($('#ModalMenu'), view);
    });

    $('#btn-new-confidential-note, #btn-edit-confidential-note').on('click', function () {
        if (busy) return;
        const creating = this.id === 'btn-new-confidential-note';
        if (!(creating ? permissions.create : permissions.edit)) return;
        form[0].reset();
        $('#input-id-asset-confidential-note').val(assetId);
        $('#input-confidential-note-operation').val(creating ? 'create' : 'update');
        $('#labelConfidentialNoteEditor').text(creating ? 'Nueva nota privada' : 'Editar nota privada');
        $('#btn-save-confidential-note').text(creating ? 'Guardar' : 'Actualizar');
        $('#confidential-note-editor-context').text($('#asset-confidential-note-code').text());
        $('#textarea-asset-confidential-note').val(creating ? '' : note);
        switchModal(view, editor);
    });
    editor.on('shown.bs.modal', function () { $('#textarea-asset-confidential-note').trigger('focus'); });
    $('.js-cancel-confidential-note').on('click', function () {
        if (busy) return;
        form[0].reset();
        switchModal(editor, view);
    });
    editor.on('hidden.bs.modal', function () { form[0].reset(); });

    form.on('submit', function (event) {
        event.preventDefault();
        if (busy) return;
        if (!$('#textarea-asset-confidential-note').val().trim()) {
            showMessage('error', 'Escribe el contenido de la nota.');
            return;
        }
        busy = true;
        editor.find('button').prop('disabled', true);
        $.ajax({
            url: baseUrl + '/inventory/assets/save_asset_confidential_note',
            type: 'POST', dataType: 'json', data: form.serialize()
        }).done(function (response) {
            if (response.status) {
                loadNote();
                editor.one('hidden.bs.modal', function () { showMessage(response.type, response.message); });
                switchModal(editor, view);
            } else {
                showMessage(response.type, response.message);
            }
        }).fail(function () {
            showMessage('error', 'No se pudo guardar la nota privada.');
        }).always(function () {
            busy = false;
            editor.find('button').prop('disabled', false);
        });
    });

    $('#btn-delete-confidential-note').on('click', function () {
        if (busy || !permissions.delete) return;
        Swal.fire({
            title: 'Eliminar nota privada', text: 'Se quitará el contenido privado de este activo.', icon: 'warning',
            showCancelButton: true, confirmButtonText: 'Eliminar', cancelButtonText: 'Cancelar',
            buttonsStyling: false, customClass: {confirmButton: 'btn btn-danger m-2', cancelButton: 'btn btn-primary m-2'}
        }).then(function (result) {
            if (!(result.isConfirmed || result.value) || busy) return;
            busy = true;
            $.ajax({url: baseUrl + '/inventory/assets/delete_asset_confidential_note', type: 'POST', dataType: 'json', data: {id_asset: assetId}})
                .done(function (response) { if (response.status) loadNote(); showMessage(response.type, response.message); })
                .fail(function () { showMessage('error', 'No se pudo eliminar la nota.'); })
                .always(function () { busy = false; });
        });
    });
});
