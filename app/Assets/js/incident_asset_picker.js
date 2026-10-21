function incidentAssetPicker(onSelect, onClose) {
    const base = document.querySelector('meta[name="base-url"]').content;
    const modal = $('#ModalSearchAsset');
    modal.find('.modal-title').text('Seleccionar activo');
    const table = $('#tableSearchAsset').DataTable({
        destroy: true, responsive: true, pagingType: 'simple_numbers', ordering: false,
        select: { style: 'single' }, language: languageDatatable(),
        ajax: { url: base + '/inventory/assets/show_assets_existing', type: 'POST', dataSrc: '', data: { id_asset: 0 } },
        columns: [
            { data: 'id_asset' }, { data: null, defaultContent: '', className: 'select-checkbox', orderable: false },
            { data: 'code_inventory_asset' }, { data: 'name_type_asset' }, { data: 'name_brand' },
            { data: 'name_category_asset' }, { data: 'date_created_asset' }
        ]
    });
    let selectedAsset = null;
    const addButton = $('#btnSubmitAddAsset').closest('button').prop('disabled', false);
    addButton.off('click.incidentPicker').on('click.incidentPicker', function (event) {
        event.preventDefault();
        const asset = table.row({ selected: true }).data();
        if (!asset) { showMessage('warning', 'Selecciona un activo.'); return; }
        if (selectedAsset) return;
        selectedAsset = asset;
        addButton.prop('disabled', true);
        modal.modal('hide');
    });
    modal.off('hidden.bs.modal.incidentPicker').one('hidden.bs.modal.incidentPicker', function () { addButton.prop('disabled', false); if (selectedAsset) onSelect(selectedAsset); if (onClose) onClose(); });
    modal.modal('show');
}
