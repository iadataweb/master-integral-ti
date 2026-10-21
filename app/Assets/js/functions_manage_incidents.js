document.addEventListener('DOMContentLoaded', function () {
    const base = document.querySelector('meta[name="base-url"]').content;
    const form = $('#formIncident');
    const channel = new BroadcastChannel('incidents_updates');
    let editingId = null;
    const escapeText = $.fn.dataTable.render.text();
    const table = $('#tableManageIncidents').DataTable({
        ajax: { url: base + '/help_desk/incidents/show_incidents', dataSrc: '' },
        language: languageDatatable(), responsive: true, order: [[0, 'desc']], pagingType: 'simple_numbers',
        dom: 'Bfrtilp',
        buttons: ['excelHtml5','pdfHtml5','print'].map(function (type) {
            return { extend: type, className: 'btn btn-primary', title: 'Incidencias', exportOptions: { columns: [0,1,2,3,4,5,6] } };
        }),
        columnDefs: [{ targets: 0, responsivePriority: 1 }, { targets: 7, responsivePriority: 2 }, { targets: 1, responsivePriority: 3 }, { targets: 5, responsivePriority: 4 }],
        columns: [
            { data: 'id_incident' }, { data: 'title_incident', render: escapeText },
            { data: 'name_type_incident', render: escapeText }, { data: 'name_priority', render: escapeText },
            { data: 'display_location', render: escapeText }, { data: 'status_open_incident' },
            { data: 'date_incident' }, { data: 'options', orderable: false, searchable: false }
        ]
    });
    initialize_upload();
    const combos = Promise.all([
        ['#select-type-incident', '/help_desk/types_incident/combo'],
        ['#select-priority', '/help_desk/priorities/combo'],
        ['#select-space', '/administration/environments/combo']
    ].map(function (entry) { return $.post(base + entry[1]).then(function (html) { $(entry[0]).html(html); }); }));

    $('#btnNew').on('click', function () {
        editingId = null;
        form[0].reset(); clear_uploaded_files();
        $('#ModalCRUD .modal-title').text('Nueva incidencia'); $('#btnSubmit').text('Guardar');
        $('#ModalCRUD').modal('show');
    });
    $(document).on('click', '.js-btn-edit', function () {
        const id = $(this).data('id');
        $.post(base + '/help_desk/incidents/show_incident', { id_incident: id }, null, 'json').done(async function (response) {
            if (!response.status) { showMessage(response.type, response.message); return; }
            const item = response.result;
            if (Number(item.status_open_incident) !== 1) { showMessage('warning', 'Reabre la incidencia para editarla.'); return; }
            try { await combos; } catch (_) { showMessage('error', 'No se pudieron cargar las opciones.'); return; }
            editingId = id; form[0].reset(); clear_uploaded_files();
            $('#input-title').val(item.title_incident); $('#select-type-incident').val(item.id_type_incident);
            $('#select-priority').val(item.id_priority); $('#select-space').val(item.id_space);
            const date = (item.date_incident_date || '').split('-');
            $('#input-date-incident').val(date.length === 3 ? date.reverse().join('/') : '');
            $('#input-time-incident').val((item.time_incident_time || '').slice(0, 5));
            $('#textarea-description').val(item.description_incident);
            load_existing_attachments(response.attachments);
            $('#ModalCRUD .modal-title').text('Editar incidencia'); $('#btnSubmit').text('Guardar');
            $('#ModalCRUD').modal('show');
        }).fail(function () { showMessage('error', 'No se pudo cargar la incidencia.'); });
    });
    form.on('submit', function (event) {
        event.preventDefault();
        const data = result_data(new FormData(this));
        if (editingId) data.set('id_incident', editingId);
        $('#btnSubmit').prop('disabled', true);
        $.ajax({ url: base + '/help_desk/incidents/' + (editingId ? 'update_incident' : 'insert_incident'),
            type: 'POST', data: data, dataType: 'json', processData: false, contentType: false
        }).done(function (response) {
            showMessage(response.type, response.message);
            if (response.status) { $('#ModalCRUD').modal('hide'); table.ajax.reload(null, false); }
        }).fail(function () { showMessage('error', 'No se pudo guardar la incidencia.'); })
          .always(function () { $('#btnSubmit').prop('disabled', false); });
    });
    $(document).on('click', '.js-btn-open-incident-details', function () {
        window.location.href = base + '/help_desk/incidents/incident_details/' + $(this).data('url-details');
    });
    $(document).on('click', '.js-btn-more', function () {
        const id = $(this).data('id');
        const row = table.rows().data().toArray().find(function (item) { return Number(item.id_incident) === Number(id); });
        $('#incident-more-id').text('#' + id);
        $('#incident-more-details').attr('href', base + '/help_desk/incidents/incident_details/' + id + '#incident-tracking');
        $('#incident-more-delete').data('id', id).toggleClass('d-none', !row || !row.can_delete);
        $('#ModalIncidentMore').modal('show');
    });
    $('#incident-more-delete').on('click', function () {
        const id = $(this).data('id');
        Swal.fire({ title: '¿Enviar la incidencia #' + id + ' a la papelera?', icon: 'warning', showCancelButton: true,
            confirmButtonText: 'Eliminar', cancelButtonText: 'Cancelar'
        }).then(function (result) {
            if (!(result.isConfirmed || result.value)) return;
            $.post(base + '/help_desk/incidents/delete_incident', { id_incident: id }, null, 'json').done(function (response) {
                showMessage(response.type, response.message);
                if (response.status) { $('#ModalIncidentMore').modal('hide'); table.ajax.reload(null, false); }
            }).fail(function () { showMessage('error', 'No se pudo eliminar la incidencia.'); });
        });
    });
    channel.onmessage = function (event) { if (event.data.action === 'reloadDataTable') table.ajax.reload(null, false); };
});
