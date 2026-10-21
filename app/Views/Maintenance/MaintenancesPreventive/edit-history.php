<div class="modal fade text-left" id="ModalEditHistory" tabindex="-1" role="dialog" aria-labelledby="labelEditHistory"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id="labelEditHistory">Corregir Registro</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="formEditHistoryMaintenancePreventive" enctype="multipart/form-data">
                    <input type="hidden" id="input-edit-history-id" name="id_history_maintenance_preventive">

                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="select-edit-history-status" class="form-label">Estado</label>
                                <select class="form-select" id="select-edit-history-status" name="id_status_maintenance">
                                    <option selected disabled value="">Selecciona una opcion</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-3 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-edit-history-date-performed" class="form-label">Fecha realizada</label>
                                <input type="date" class="form-control" id="input-edit-history-date-performed" name="date_performed_history_maintenance_preventive" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-3 mb-3">
                            <div class="form-group">
                                <label for="input-edit-history-time-performed" class="form-label">Hora realizada</label>
                                <input type="time" class="form-control" id="input-edit-history-time-performed" name="time_performed_history_maintenance_preventive">
                            </div>
                        </div>
                    </div>

                    <div class="row" id="section-edit-history-internal-actions">
                        <div class="col-12 mb-3">
                            <div class="form-group">
                                <label for="textarea-edit-history-action-current" class="form-label">Acciones registradas actualmente</label>
                                <textarea class="form-control" id="textarea-edit-history-action-current" rows="4" readonly placeholder="Sin acciones registradas."></textarea>
                                <small class="text-muted">Este campo es informativo y muestra el dato anterior registrado.</small>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="form-group mandatory">
                                <label class="form-label">Lista de opciones de acciones realizadas</label>
                                <div id="content-edit-actions-maintenance-preventive-checks" class="border rounded p-3" style="max-height:220px; overflow:auto;">
                                    <div class="text-muted small">Cargando acciones...</div>
                                </div>
                                <input type="hidden" id="input-edit-history-action" name="action_history_maintenance_preventive" value="">
                                <small class="text-muted">Seleccione las acciones correctas. Si no selecciona ninguna, se conservara el dato anterior.</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-group mandatory">
                                <label for="textarea-edit-history-observation" class="form-label">Observacion</label>
                                <textarea class="form-control" id="textarea-edit-history-observation" name="observation_history_maintenance_preventive" rows="6" placeholder="..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="custom-header-modal bg-secondary mb-2">
                        <div class="custom-header-title">
                            <h4 class="m-0">Fotografias Antes del Mantenimiento</h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div id="custom-btn-upload-table-edit-before"></div>
                            <div class="table-responsive">
                                <table id="table-handler-edit-before" class="table mb-0 custom-table-handler">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Archivo</th>
                                            <th>Accion</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="custom-header-modal bg-secondary mb-2">
                        <div class="custom-header-title">
                            <h4 class="m-0">Fotografias Despues del Mantenimiento</h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div id="custom-btn-upload-table-edit-after"></div>
                            <div class="table-responsive">
                                <table id="table-handler-edit-after" class="table mb-0 custom-table-handler">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Archivo</th>
                                            <th>Accion</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="custom-header-modal bg-secondary mb-2">
                        <div class="custom-header-title">
                            <h4 class="m-0">Documentos adicionales de la tarea realizada</h4>
                        </div>
                    </div>
                    <p class="text-muted small mb-2">Adjunte documentos opcionales que sustenten o complementen la tarea realizada, como informes tecnicos, ordenes de servicio, certificados, fotografias o facturas.</p>
                    <div class="row">
                        <div class="col-12 mb-0">
                            <div id="custom-btn-upload-table-edit-additional"></div>
                            <div class="table-responsive">
                                <table id="table-handler-edit-additional" class="table mb-0 custom-table-handler">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Archivo</th>
                                            <th>Accion</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Cerrar</span>
                </button>
                <button type="submit" form="formEditHistoryMaintenancePreventive" class="btn btn-secondary ms-1" id="btn-submit-edit-history">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Guardar</span>
                </button>
            </div>

        </div>
    </div>
</div>