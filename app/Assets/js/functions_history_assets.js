document.addEventListener('DOMContentLoaded', function () {
    const baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content');
    const table = $('#tableHistoryAssets').DataTable({
        pagingType: 'simple_numbers',
        aProcessing: true,
        aServerSide: true,
        ajax: {
            url: baseUrl + '/history/show_assets',
            dataSrc: ''
        },
        language: languageDatatable(),
        columns: [
            { data: 'id_asset' },
            { data: 'asset_summary' },
            { data: 'category_summary' },
            { data: 'current_location' },
            { data: 'event_summary', orderable: false },
            { data: 'date_created_asset' },
            { data: 'options', orderable: false, searchable: false }
        ],
        responsive: true,
        dom: 'Bfrtilp',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="icon-file-excel"></i>',
                titleAttr: 'Exportar a Excel',
                className: 'btn btn-success',
                filename: 'Historial de Activos',
                exportOptions: { columns: [0, 1, 2, 3, 4, 5], stripHtml: true }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="icon-file-pdf"></i>',
                titleAttr: 'Exportar a PDF',
                className: 'btn btn-danger',
                filename: 'Historial de Activos',
                exportOptions: { columns: [0, 1, 2, 3, 4, 5], stripHtml: true }
            },
            {
                extend: 'print',
                text: '<i class="icon-print"></i>',
                titleAttr: 'Imprimir',
                className: 'btn btn-info',
                filename: 'Historial de Activos',
                exportOptions: { columns: [0, 1, 2, 3, 4, 5], stripHtml: true }
            }
        ],
        destroy: true,
        iDisplayLength: 10,
        order: [[0, 'desc']]
    });

    table.on('xhr.dt', function (_event, _settings, json) {
        updateHistorySummary(Array.isArray(json) ? json : []);
    });

    function updateHistorySummary(rows) {
        const totals = rows.reduce(function (acc, row) {
            acc.assets += 1;
            acc.movements += parseInt(row.total_movements || 0, 10);
            acc.events += parseInt(row.total_incidents || 0, 10)
                + parseInt(row.total_preventive || 0, 10)
                + parseInt(row.total_corrective || 0, 10);
            return acc;
        }, { assets: 0, movements: 0, events: 0 });

        $('#historyTotalAssets').text(totals.assets);
        $('#historyTotalMovements').text(totals.movements);
        $('#historyTotalEvents').text(totals.events);
    }
});
