document.addEventListener('DOMContentLoaded', function () {
    const baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content');
    const channel = new BroadcastChannel('incidents_updates');
    const incidentId = window.location.pathname.match(/\/(\d+)\/?$/)[1];

    function submitAction(action, data, onSuccess, notifySuccess = true) {
        return $.ajax({
            url: baseUrl + '/help_desk/incidents/' + action,
            type: 'POST',
            dataType: 'json',
            timeout: 20000,
            data: data,
            processData: !(data instanceof FormData),
            contentType: data instanceof FormData ? false : 'application/x-www-form-urlencoded; charset=UTF-8'
        }).done(function (response) {
            if (!response.status || notifySuccess) showMessage(response.type, response.message);
            if (response.status) {
                channel.postMessage({ action: 'reloadDataTable' });
                if (onSuccess) onSuccess(response);
            }
        }).fail(function () {
            showMessage('error', 'Ocurrió un problema al procesar la solicitud.');
        });
    }

    let lifecyclePending = false;
    function changeLifecycle(action, form) {
        if (lifecyclePending) return;
        lifecyclePending = true;
        const buttons = $('#formIncidentClose button, #formIncidentReopen button');
        buttons.prop('disabled', true).attr('aria-busy', 'true');
        const data = new FormData(form);
        data.set('id_incident', incidentId);
        submitAction(action, data, function (response) {
            const previousAnchor = document.getElementById('incident-lifecycle');
            const previousTop = previousAnchor ? previousAnchor.getBoundingClientRect().top : null;
            $('#incident-details-content').html(response.html);
            const currentAnchor = document.getElementById('incident-lifecycle');
            if (previousTop !== null && currentAnchor) {
                // Preserve the current reading position when action buttons change height.
                window.scrollBy({ top: currentAnchor.getBoundingClientRect().top - previousTop, behavior: 'instant' });
            }
            showMessage(response.type, response.message);
        }, false).always(function () {
            lifecyclePending = false;
            $('#formIncidentClose button, #formIncidentReopen button').prop('disabled', false).removeAttr('aria-busy');
        });
    }
    $(document).on('click', '.js-btn-close', function (event) {
        event.preventDefault();
        changeLifecycle('close_incident', document.getElementById('formIncidentClose'));
    });
    $(document).on('submit', '#formIncidentReopen', function (event) {
        event.preventDefault();
        changeLifecycle('reopen_incident', this);
    });
    $(document).on('click', '.js-btn-register-resolution', function () {
        $('#input-date-closed-incident').val($('#view-date-closed-incident').val());
        $('#input-time-closed-incident').val($('#view-time-closed-incident').val());
        $('#ModalIncidentResolution').modal('show');
    });
    $(document).on('submit', '#formManualClosedIncident', function (event) {
        event.preventDefault();
        const data = new FormData(this);
        data.set('id_incident', incidentId);
        submitAction('update_manual_closed_incident', data, function (response) {
            $('#view-date-closed-incident').val(data.get('date_closed_incident'));
            $('#view-time-closed-incident').val(data.get('time_closed_incident'));
            $('#ModalIncidentResolution').one('hidden.bs.modal', function () {
                showMessage(response.type, response.message);
            }).modal('hide');
        }, false);
    });

    ['diagnostic', 'solution'].forEach(function (field) {
        const name = field === 'diagnostic' ? 'Diagnostic' : 'Solution';
        const label = field === 'diagnostic' ? 'Diagnóstico' : 'Solución';
        $(document).on('click', '.js-btn-add-' + field, function () {
            $('#textarea-' + field + '-incident').val($('#view-textarea-' + field + '-incident').val());
            $('#Modal' + name + ' .modal-title').text(label);
            $('#Modal' + name + ' #btnSubmit').text('Guardar');
            $('#Modal' + name).modal('show');
        });
        $(document).on('submit', '#form' + name, function (event) {
            event.preventDefault();
            const data = new FormData(this);
            data.set('id_incident', incidentId);
            submitAction('save_incident_' + field, data, function () {
                $('#view-textarea-' + field + '-incident').val(data.get(field + '_incident'));
                $('#Modal' + name).modal('hide');
            });
        });
    });

    $(document).on('click', '.js-btn-clear-incident-field', function () {
        const field = $(this).data('field');
        submitAction('clear_incident_field', { id_incident: incidentId, field: field }, function () {
            if (field === 'resolution') {
                $('#view-date-closed-incident, #view-time-closed-incident, #input-date-closed-incident, #input-time-closed-incident').val('');
            } else {
                $('#view-textarea-' + field + '-incident, #textarea-' + field + '-incident').val('');
            }
        });
    });

    function reloadDetails() { setTimeout(function () { window.location.reload(); }, 700); }
    $(document).on('click', '.js-btn-derive-corrective', function () {
        submitAction('derive_corrective', { id_incident: incidentId }, reloadDetails);
    });
    $(document).on('click', '.js-btn-cancel-corrective-derivation', function () {
        submitAction('cancel_corrective_derivation', { id_incident: incidentId }, reloadDetails);
    });

    let assetRelationPending = false;
    function relateAsset(idAsset) {
        if (assetRelationPending) return;
        assetRelationPending = true;
        $('#incident-affected-asset button').prop('disabled', true);
        submitAction('relate_incident_asset', {id_incident: incidentId, id_asset: idAsset}, function (response) {
            const previous = document.getElementById('incident-affected-asset');
            const previousTop = previous ? previous.getBoundingClientRect().top : null;
            const updated = $('<div>').append($.parseHTML(response.html || '', document, false)).find('#incident-affected-asset');
            if (!previous || updated.length !== 1) {
                showMessage('error', 'Se guardó la relación, pero no se pudo actualizar su visualización.');
                return;
            }
            $(previous).replaceWith(updated);
            const current = document.getElementById('incident-affected-asset');
            if (previousTop !== null && current) window.scrollBy({top: current.getBoundingClientRect().top - previousTop, behavior: 'instant'});
            showMessage(response.type, response.message);
        }, false).always(function () {
            assetRelationPending = false;
            $('#incident-affected-asset button').prop('disabled', false);
        });
    }
    $(document).on('click', '.js-btn-search-asset', function () {
        if (!assetRelationPending) incidentAssetPicker(function (asset) { relateAsset(asset.id_asset); });
    });
    $(document).on('click', '#btn-limpiar-asset', function () { relateAsset(''); });
});
