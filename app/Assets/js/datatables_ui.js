/* Presentación compartida de DataTables 1.x y 2.x, incluidas tablas en modales. */
(function ($) {
    if (!$) return;
    const exports = [
        ['.buttons-excel', 'icon-file-excel', 'Exportar a Excel'],
        ['.buttons-pdf', 'icon-file-pdf', 'Exportar a PDF'],
        ['.buttons-print', 'icon-print', 'Imprimir']
    ];
    function arrangeTable(event, settings) {
        const wrapper = settings && settings.nTableWrapper;
        if (!wrapper) return;
        const belongsToTable = element => $(element).closest('.dt-container, .dataTables_wrapper')[0] === wrapper;
        const buttons = $(wrapper).find('.dt-buttons').filter(function () { return belongsToTable(this); }).first();
        const search = $(wrapper).find('.dt-search, .dataTables_filter').filter(function () { return belongsToTable(this); }).first();
        const length = $(wrapper).find('.dt-length, .dataTables_length').filter(function () { return belongsToTable(this); }).first();
        let toolbar = $(wrapper).children('.ui-datatable-toolbar');
        if (!toolbar.length && (buttons.length || search.length)) {
            toolbar = $('<div class="ui-datatable-toolbar"></div>').prependTo(wrapper);
        }
        if (buttons.length) toolbar.append(buttons);

        if (search.length) toolbar.append(search);
        const info = $(wrapper).find('.dt-info, .dataTables_info').filter(function () { return belongsToTable(this); }).first();
        const paging = $(wrapper).find('.dt-paging, .dataTables_paginate').filter(function () { return belongsToTable(this); }).first();
        let footer = $(wrapper).children('.ui-datatable-footer');
        if (!footer.length && (info.length || length.length || paging.length)) footer = $('<div class="ui-datatable-footer"></div>').appendTo(wrapper);
        if (info.length) footer.append(info);
        if (length.length) footer.append(length);
        if (paging.length) footer.append(paging);
        exports.forEach(function ([selector, icon, label]) {
            buttons.find(selector).each(function () {
                $(this).html('<i class="' + icon + '" aria-hidden="true"></i>').attr({'aria-label': label, title: label});
            });
        });
        // Retirar solo las filas de distribución que quedaron vacías al agrupar controles.
        $(wrapper).children('.dt-layout-row, .row, .labels-table-controls, .movement-table-tools, .labels-table-bottom, .movement-table-bottom').each(function () {
            if (!this.textContent.trim() && !$(this).find('input, select, button, table, .dt-processing, .dataTables_processing').length) $(this).remove();
        });
    }
    $(document).on('preInit.dt.uiRecordId', function (event, settings) {
        // Alternar siempre ascendente/descendente; el estado vacío puede repetir
        // visualmente el orden original y hacer que un clic parezca inactivo.
        (settings.aoColumns || []).forEach(function (column) {
            if (column.bSortable !== false) column.asSorting = ['asc', 'desc'];
        });
        const first = settings && settings.aoColumns && settings.aoColumns[0];
        if (first && typeof first.mData === 'string' && /^id_/.test(first.mData)) first.responsivePriority = 1;
    });
    $(document).on('init.dt.uiRecordId', function (event, settings) {
        const first = settings && settings.aoColumns && settings.aoColumns[0];
        if (!first || typeof first.mData !== 'string' || !/^id_/.test(first.mData)) return;
        first.sTitle = '#';
        const title = $(settings.nTable).find('thead th').first();
        if (title.find('.dt-column-title').length) title.find('.dt-column-title').text('#');
        else title.text('#');
    });
    $(document).on('init.dt.uiTable', arrangeTable);

})(window.jQuery);
