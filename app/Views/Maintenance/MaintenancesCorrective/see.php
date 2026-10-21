<div class="modal fade text-left" id="ModalSee" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel4">Datos del Registro</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="modal-detail-layout">
                    <section class="modal-detail-section">
                        <h6 class="modal-detail-section-title">Datos del mantenimiento</h6>
                        <div class="modal-detail-grid">
                            <article class="modal-detail-card">
                                <span class="modal-detail-label">Incidencia Relacionada</span>
                                <strong class="modal-detail-value" id="cell-id-incident-maintenance-corrective"></strong>
                            </article>
                            <article class="modal-detail-card">
                                <span class="modal-detail-label">Origen</span>
                                <strong class="modal-detail-value" id="cell-origin-maintenance-corrective"></strong>
                            </article>
                            <article class="modal-detail-card">
                                <span class="modal-detail-label">Responsable</span>
                                <strong class="modal-detail-value" id="cell-name-type-responsible-maintenance"></strong>
                            </article>
                            <article class="modal-detail-card">
                                <span class="modal-detail-label">Estado</span>
                                <strong class="modal-detail-value" id="cell-status-active-maintenance-corrective"></strong>
                            </article>
                            <article class="modal-detail-card">
                                <span class="modal-detail-label">Fecha de Reparacion</span>
                                <strong class="modal-detail-value" id="cell-date-repair-maintenance-corrective"></strong>
                            </article>
                            <article class="modal-detail-card">
                                <span class="modal-detail-label">Hora de Reparacion</span>
                                <strong class="modal-detail-value" id="cell-time-repair-maintenance-corrective"></strong>
                            </article>
                            <article class="modal-detail-card modal-detail-card-wide">
                                <span class="modal-detail-label">Fecha de Registro</span>
                                <strong class="modal-detail-value" id="cell-date-created-maintenance-corrective"></strong>
                            </article>
                        </div>
                    </section>

                    <section class="modal-detail-section">
                        <h6 class="modal-detail-section-title">Datos del activo</h6>
                        <div class="modal-detail-grid">
                            <article class="modal-detail-card">
                                <span class="modal-detail-label">Codigo de Inventario</span>
                                <strong class="modal-detail-value" id="cell-code-inventory-asset"></strong>
                            </article>
                            <article class="modal-detail-card">
                                <span class="modal-detail-label">Tipo de Activo</span>
                                <strong class="modal-detail-value" id="cell-name-type-asset"></strong>
                            </article>
                            <article class="modal-detail-card modal-detail-card-wide">
                                <span class="modal-detail-label">Categoria de Activo</span>
                                <strong class="modal-detail-value" id="cell-name-category-asset"></strong>
                            </article>
                        </div>
                    </section>

                    <section class="modal-detail-section">
                        <h6 class="modal-detail-section-title">Accion Realizada</h6>
                        <div class="modal-attachments-body">
                            <div class="modal-detail-value text-wrap" id="cell-action-maintenance-corrective"></div>
                        </div>
                    </section>

                    <section class="modal-detail-section">
                        <h6 class="modal-detail-section-title">Observacion</h6>
                        <div class="modal-attachments-body">
                            <div class="modal-detail-value text-wrap" id="cell-observation-maintenance-corrective"></div>
                        </div>
                    </section>

                    <section class="modal-attachments-panel">
                        <h6 class="modal-attachments-title">Repuestos Utilizados</h6>
                        <div class="modal-attachments-body">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0 modal-attachments-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 80px;">#</th>
                                            <th>Codigo</th>
                                            <th>Repuesto</th>
                                            <th>Cantidad</th>
                                            <th>Observacion</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-spare-parts-see-maintenance-corrective">
                                        <tr><td colspan="5" class="text-muted text-center">Sin registros.</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>

                    <section class="modal-attachments-panel">
                        <h6 class="modal-attachments-title">Archivos Adjuntos</h6>
                        <div class="modal-attachments-body">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0 modal-attachments-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 80px;">#</th>
                                            <th>Archivo</th>
                                            <th style="width: 130px;">Accion</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-attachments-maintenance-corrective">
                                        <tr><td colspan="3" class="text-muted text-center">Sin registros.</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
