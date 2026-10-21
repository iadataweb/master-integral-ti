<div class="modal fade text-left" id="ModalLabelTemplatePicker" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="labelLabelTemplatePicker">Seleccionar Plantilla</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="input-label-template-picker-asset">
                <div class="mb-3">
                    <label for="select-label-template-picker" class="form-label">Plantilla de etiqueta</label>
                    <select class="form-select js-label-template-select" id="select-label-template-picker"></select>
                    <small class="text-muted">Registre su plantilla personalizada en Administración > Parámetros > Etiquetas.</small>
                </div>
                <div class="asset-label-preview" id="label-template-preview-picker">
                    <div class="asset-label-preview-empty">Seleccione una plantilla para visualizar la posicion del QR.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-secondary ms-1" id="btnGenerateLabelWithTemplate">Generar PDF</button>
            </div>
        </div>
    </div>
</div>