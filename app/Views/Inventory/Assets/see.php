<link rel="stylesheet" href="<?= assets(); ?>/css/components/inventory-assets-see.css?v=<?= filemtime(dirname(__DIR__, 3) . '/Assets/css/components/inventory-assets-see.css'); ?>">

<div class="modal fade text-left ui-record-detail" id="ModalSee" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel4">Datos del Registro</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="asset-layout">

                    <section class="asset-section">
                        <h6 class="asset-section-title">Datos principales</h6>
                        <div class="asset-meta-grid">
                            <div class="asset-meta-card">
                                <span class="asset-meta-label">Codigo de Inventario</span>
                                <div class="asset-meta-value" id="cell-code-inventory-asset"></div>
                            </div>
                            <div class="asset-meta-card">
                                <span class="asset-meta-label">Estado</span>
                                <div class="asset-meta-value" id="cell-status-active-asset"></div>
                            </div>
                            <div class="asset-meta-card">
                                <span class="asset-meta-label">Tipo de Activo</span>
                                <div class="asset-meta-value" id="cell-name-type-asset"></div>
                            </div>
                            <div class="asset-meta-card">
                                <span class="asset-meta-label">Categoria</span>
                                <div class="asset-meta-value" id="cell-name-category-asset"></div>
                            </div>
                            <div class="asset-meta-card">
                                <span class="asset-meta-label">Marca</span>
                                <div class="asset-meta-value" id="cell-name-brand"></div>
                            </div>
                            <div class="asset-meta-card">
                                <span class="asset-meta-label">Condicion</span>
                                <div class="asset-meta-value" id="cell-name-condition-asset"></div>
                            </div>
                        </div>
                    </section>

                    <section class="asset-section">
                        <h6 class="asset-section-title">Detalles adicionales</h6>
                        <div class="asset-meta-grid">
                            <div class="asset-meta-card">
                                <span class="asset-meta-label">Modelo</span>
                                <div class="asset-meta-value" id="cell-model-asset"></div>
                            </div>
                            <div class="asset-meta-card">
                                <span class="asset-meta-label">Numero de Serie</span>
                                <div class="asset-meta-value" id="cell-serial-number-asset"></div>
                            </div>
                            <div class="asset-meta-card">
                                <span class="asset-meta-label">Fecha de Ingreso</span>
                                <div class="asset-meta-value" id="cell-entry-date-asset"></div>
                            </div>
                            <div class="asset-meta-card">
                                <span class="asset-meta-label">Fecha de Adquisicion</span>
                                <div class="asset-meta-value" id="cell-acquisition-date-asset"></div>
                            </div>
                            <div class="asset-meta-card">
                                <span class="asset-meta-label">Fecha de Registro</span>
                                <div class="asset-meta-value" id="cell-date-created-asset"></div>
                            </div>
                        </div>
                    </section>

                    <div class="asset-panel">
                        <h6 class="asset-panel-title">Descripcion del Activo</h6>
                        <div class="asset-panel-body">
                            <div id="cell-descriptions-asset" class="text-wrap"></div>
                        </div>
                    </div>

                    <div class="asset-panel">
                        <h6 class="asset-panel-title">Observaciones</h6>
                        <div class="asset-panel-body">
                            <div id="cell-observations-asset" class="text-wrap"></div>
                        </div>
                    </div>

                    <div class="asset-panel">
                        <h6 class="asset-panel-title">Archivos Adjuntos</h6>
                        <div class="asset-panel-body">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0 asset-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 80px;">#</th>
                                            <th>Archivo</th>
                                            <th style="width: 130px;">Accion</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-attachments-asset">
                                        <tr><td colspan="3" class="text-muted text-center">Sin registros.</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Cerrar</span>
                </button>
            </div>
        </div>
    </div>
</div>
