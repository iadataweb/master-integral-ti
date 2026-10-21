<link rel="stylesheet" href="<?= assets(); ?>/css/components/maintenance-maintenancespreventive-see-history.css?v=<?= filemtime(dirname(__DIR__, 3) . '/Assets/css/components/maintenance-maintenancespreventive-see-history.css'); ?>">

<div class="modal fade text-left" id="ModalSeeHistory" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel4">Datos del Registro</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="history-layout">

                    <div class="history-meta-grid">
                        <div class="history-meta-card">
                            <span class="history-meta-label">Codigo de Inventario</span>
                            <div class="history-meta-value" id="cell-code-inventory-asset"></div>
                        </div>
                        <div class="history-meta-card">
                            <span class="history-meta-label">Tipo de Activo</span>
                            <div class="history-meta-value" id="cell-name-type-asset"></div>
                        </div>
                        <div class="history-meta-card">
                            <span class="history-meta-label">Categoria de Activo</span>
                            <div class="history-meta-value" id="cell-name-category-asset"></div>
                        </div>
                        <div class="history-meta-card">
                            <span class="history-meta-label">Tipo Responsable</span>
                            <div class="history-meta-value" id="cell-name-type-responsible-maintenance"></div>
                        </div>
                        <div class="history-meta-card">
                            <span class="history-meta-label">Frecuencia</span>
                            <div class="history-meta-value" id="cell-name-frequency-maintenance"></div>
                        </div>
                        <div class="history-meta-card">
                            <span class="history-meta-label">Fecha Programada</span>
                            <div class="history-meta-value" id="cell-date-scheduled-maintenance-preventive"></div>
                        </div>
                        <div class="history-meta-card">
                            <span class="history-meta-label">Fecha Realizado</span>
                            <div class="history-meta-value" id="cell-date-performed-history-maintenance-preventive"></div>
                        </div>
                        <div class="history-meta-card">
                            <span class="history-meta-label">Hora Realizado</span>
                            <div class="history-meta-value" id="cell-time-performed-history-maintenance-preventive"></div>
                        </div>
                    </div>

                    <div class="history-panel">
                        <h6 class="history-panel-title">Accion Realizado</h6>
                        <div class="history-panel-body">
                            <div id="cell-action-history-maintenance-preventive" class="text-wrap"></div>
                        </div>
                    </div>

                    <div class="history-panel">
                        <h6 class="history-panel-title">Observacion</h6>
                        <div class="history-panel-body">
                            <div id="cell-observation-history-maintenance-preventive" class="text-wrap"></div>
                        </div>
                    </div>

                    <div class="history-panel">
                        <h6 class="history-panel-title">Fotografias Antes</h6>
                        <div class="history-panel-body">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0 history-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 80px;">#</th>
                                            <th>Archivo</th>
                                            <th style="width: 130px;">Accion</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-attachments-before-history">
                                        <tr><td colspan="3" class="text-muted text-center">Sin registros.</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="history-panel">
                        <h6 class="history-panel-title">Fotografias Despues</h6>
                        <div class="history-panel-body">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0 history-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 80px;">#</th>
                                            <th>Archivo</th>
                                            <th style="width: 130px;">Accion</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-attachments-after-history">
                                        <tr><td colspan="3" class="text-muted text-center">Sin registros.</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="history-panel" id="panel-additional-documents-history">
                        <h6 class="history-panel-title">Documentos adicionales de la tarea realizada</h6>
                        <div class="history-panel-body">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0 history-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 80px;">#</th>
                                            <th>Archivo</th>
                                            <th style="width: 130px;">Accion</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-attachments-additional-history">
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
