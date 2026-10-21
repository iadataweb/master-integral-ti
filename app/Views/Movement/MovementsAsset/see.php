<link rel="stylesheet" href="<?= assets(); ?>/css/components/movement-movementsasset-see.css?v=<?= filemtime(dirname(__DIR__, 3) . '/Assets/css/components/movement-movementsasset-see.css'); ?>">

<div class="modal fade text-left" id="ModalSee" tabindex="-1" role="dialog" aria-labelledby="movementSeeTitle"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="movementSeeTitle">Datos del Registro</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="asset-layout">

                    <section class="asset-section">
                        <h6 class="asset-section-title">Datos del activo</h6>
                        <div class="asset-meta-grid">
                            <div class="asset-meta-card">
                                <span class="asset-meta-label">Codigo de Inventario</span>
                                <div class="asset-meta-value" id="cell-code-inventory-asset"></div>
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
                        <h6 class="asset-section-title">Ubicacion del movimiento</h6>
                        <div class="asset-meta-grid">
                            <div class="asset-meta-card">
                                <span class="asset-meta-label">Codigo de Ambiente</span>
                                
                            </div>
                            <div class="asset-meta-card">
                                <span class="asset-meta-label">Establecimiento</span>
                                <div class="asset-meta-value" id="cell-name-campus"></div>
                            </div>
                            <div class="asset-meta-card">
                                <span class="asset-meta-label">Ambiente</span>
                                <div class="asset-meta-value" id="cell-name-space"></div>
                            </div>
                            <div class="asset-meta-card">
                                <span class="asset-meta-label">Piso</span>
                                <div class="asset-meta-value" id="cell-floor-space"></div>
                            </div>
                            <div class="asset-meta-card">
                                <span class="asset-meta-label">Plano</span>
                                <div class="asset-meta-value" id="cell-floor-plan-movement-asset"></div>
                            </div>
                            <div class="asset-meta-card">
                                <span class="asset-meta-label">Coordenada</span>
                                <div class="asset-meta-value">
                                    <span id="cell-coordinate-movement-asset"></span>
                                    <a href="#" target="_blank" rel="noopener noreferrer" id="btn-open-movement-plan-location" class="btn btn-sm btn-outline-primary ms-2 d-none">Ver en plano</a>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="asset-section">
                        <h6 class="asset-section-title">Datos del movimiento</h6>
                        <div class="asset-meta-grid">
                            <div class="asset-meta-card">
                                <span class="asset-meta-label">Tipo de Movimiento</span>
                                <div class="asset-meta-value" id="cell-name-type-movement"></div>
                            </div>
                            <div class="asset-meta-card">
                                <span class="asset-meta-label">Responsable que recibe</span>
                                <div class="asset-meta-value" id="cell-responsible-employee-movement-asset"></div>
                            </div>
                            <div class="asset-meta-card">
                                <span class="asset-meta-label">Fecha de Movimiento</span>
                                <div class="asset-meta-value" id="cell-date-event-movement-asset"></div>
                            </div>
                            <div class="asset-meta-card">
                                <span class="asset-meta-label">Hora de Movimiento</span>
                                <div class="asset-meta-value" id="cell-time-event-movement-asset"></div>
                            </div>
                            <div class="asset-meta-card">
                                <span class="asset-meta-label">Retorno Esperado</span>
                                <div class="asset-meta-value" id="cell-expected-return-date-movement-asset"></div>
                            </div>
                            <div class="asset-meta-card">
                                <span class="asset-meta-label">Documento</span>
                                <div class="asset-meta-value" id="cell-document-code-movement-asset"></div>
                            </div>
                            <div class="asset-meta-card">
                                <span class="asset-meta-label">Estado del Movimiento</span>
                                <div class="asset-meta-value" id="cell-status-process-movement-asset"></div>
                            </div>
                            <div class="asset-meta-card movement-loan-detail">
                                <span class="asset-meta-label">Tipo de Prestamo</span>
                                <div class="asset-meta-value" id="cell-loan-recipient-type-movement-asset"></div>
                            </div>
                            <div class="asset-meta-card movement-loan-detail">
                                <span class="asset-meta-label">Persona / Empresa</span>
                                <div class="asset-meta-value" id="cell-loan-recipient-name-movement-asset"></div>
                            </div>
                            <div class="asset-meta-card movement-loan-detail">
                                <span class="asset-meta-label">Documento / Referencia</span>
                                <div class="asset-meta-value" id="cell-loan-recipient-document-movement-asset"></div>
                            </div>
                            <div class="asset-meta-card movement-loan-detail">
                                <span class="asset-meta-label">Devolucion</span>
                                <div class="asset-meta-value" id="cell-loan-return-indefinite-movement-asset"></div>
                            </div>
                            <div class="asset-meta-card">
                                <span class="asset-meta-label">Fecha de Registro</span>
                                <div class="asset-meta-value" id="cell-date-created-movement-asset"></div>
                            </div>
                        </div>
                    </section>

                    <div class="asset-panel">
                        <h6 class="asset-panel-title">Observaciones</h6>
                        <div class="asset-panel-body">
                            <div id="cell-observations-movement-asset" class="text-wrap"></div>
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
                                    <tbody id="tbody-attachments-movement">
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
