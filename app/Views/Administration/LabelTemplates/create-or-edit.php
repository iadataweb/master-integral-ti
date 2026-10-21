<div class="modal fade text-left" id="ModalCRUD" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="labelLabelTemplate"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formLabelTemplate" enctype="multipart/form-data">
                    <input type="hidden" id="input-id-label-template" name="id_label_template">
                    <input type="hidden" id="input-current-design-label-template">
                    <div class="row">
                        <div class="col-12 col-xl-5">
                            <section class="label-template-card mb-3">
                                <h6 class="fw-bold mb-3">Datos de la plantilla</h6>
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label class="form-label" for="input-name-label-template">Nombre de la plantilla</label>
                                        <input type="text" class="form-control" id="input-name-label-template" name="name_label_template" placeholder="Ej. Etiqueta QR 8.5 x 5.5">
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label" for="input-width-cm-label-template">Ancho (cm)</label>
                                        <input type="number" step="0.01" min="0.1" class="form-control js-preview-field" id="input-width-cm-label-template" name="width_cm_label_template" value="8.50">
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label" for="input-height-cm-label-template">Alto (cm)</label>
                                        <input type="number" step="0.01" min="0.1" class="form-control js-preview-field" id="input-height-cm-label-template" name="height_cm_label_template" value="5.50">
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label" for="select-status-active-label-template">Estado</label>
                                        <select class="form-select" id="select-status-active-label-template" name="status_active_label_template">
                                            <option value="1">Activo</option>
                                            <option value="0">Inactivo</option>
                                        </select>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label" for="input-design-label-template">Diseno base PNG</label>
                                        <input type="file" accept="image/png" class="form-control" id="input-design-label-template" name="design_label_template">
                                        <small class="text-muted js-file-help">En edicion, deje vacio para mantener el diseno actual.</small>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label" for="textarea-description-label-template">Descripcion</label>
                                        <textarea class="form-control" id="textarea-description-label-template" name="description_label_template" rows="3" placeholder="Detalle de uso, impresora, material o medidas"></textarea>
                                    </div>
                                </div>
                            </section>
                            <section class="label-template-card mb-3">
                                <h6 class="fw-bold mb-3">Posicion del QR</h6>
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label">X (%)</label>
                                        <input type="number" step="0.01" min="0" max="100" class="form-control js-preview-field" id="input-qr-x-label-template" name="qr_x_percent_label_template" value="60">
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Y (%)</label>
                                        <input type="number" step="0.01" min="0" max="100" class="form-control js-preview-field" id="input-qr-y-label-template" name="qr_y_percent_label_template" value="20">
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Ancho (%)</label>
                                        <input type="number" step="0.01" min="1" max="100" class="form-control js-preview-field" id="input-qr-width-label-template" name="qr_width_percent_label_template" value="30">
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Alto (%)</label>
                                        <input type="number" step="0.01" min="1" max="100" class="form-control js-preview-field" id="input-qr-height-label-template" name="qr_height_percent_label_template" value="45">
                                    </div>
                                </div>
                                <small class="text-muted">Puede arrastrar el recuadro QR sobre la vista previa para ajustar X/Y.</small>
                            </section>
                            <section class="label-template-card">
                                <h6 class="fw-bold mb-3">Codigo de inventario</h6>
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label">X (%)</label>
                                        <input type="number" step="0.01" min="0" max="100" class="form-control js-preview-field" id="input-code-x-label-template" name="code_x_percent_label_template" value="5">
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Y (%)</label>
                                        <input type="number" step="0.01" min="0" max="100" class="form-control js-preview-field" id="input-code-y-label-template" name="code_y_percent_label_template" value="75">
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Ancho (%)</label>
                                        <input type="number" step="0.01" min="1" max="100" class="form-control js-preview-field" id="input-code-width-label-template" name="code_width_percent_label_template" value="45">
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Alto (%)</label>
                                        <input type="number" step="0.01" min="1" max="100" class="form-control js-preview-field" id="input-code-height-label-template" name="code_height_percent_label_template" value="12">
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Tamano de texto (px)</label>
                                        <input type="number" step="0.5" min="4" max="24" class="form-control js-preview-field" id="input-code-font-size-label-template" name="code_font_size_label_template" value="8">
                                    </div>
                                </div>
                                <small class="text-muted">Puede arrastrar el texto del codigo sobre la vista previa para ajustar X/Y.</small>
                            </section>
                        </div>
                        <div class="col-12 col-xl-7">
                            <section class="label-template-card h-100">
                                <h6 class="fw-bold mb-3">Vista previa en tiempo real</h6>
                                <div class="label-template-preview" id="label-template-preview">
                                    <div class="label-template-empty">Cargue un PNG para visualizar la etiqueta y ajustar el QR.</div>
                                    <div class="label-template-qr" id="label-template-qr" style="display:none;"></div>
                                    <div class="label-template-code" id="label-template-code" style="display:none;">INV-20260511170229-5A4F</div>
                                </div>
                            </section>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" form="formLabelTemplate" class="btn btn-secondary ms-1">
                    <span id="btnSubmitLabelTemplate"></span>
                </button>
            </div>
        </div>
    </div>
</div>