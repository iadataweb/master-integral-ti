<div class="modal fade text-left" id="ModalExecute" tabindex="-1" role="dialog" aria-labelledby="labelExecute"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id="labelExecute"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="formExecute" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-group mandatory">
                                <label for="select-status-maintenance" class="form-label">Estado</label>
                                <select class="form-select" id="select-status-maintenance" name="id_status_maintenance">
                                    <option selected disabled value="">Selecciona una opcion</option>
                                </select>
                            </div>
                        </div>                                               
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-date-performed-history-maintenance-preventive" class="form-label">Fecha realizada</label>
                                <input type="date" class="form-control" id="input-date-performed-history-maintenance-preventive" name="date_performed_history_maintenance_preventive">
                                <small class="text-muted">Se usara para calcular la fecha programada y el proximo mantenimiento.</small>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-time-performed-history-maintenance-preventive" class="form-label">Hora realizada</label>
                                <input type="time" class="form-control" id="input-time-performed-history-maintenance-preventive" name="time_performed_history_maintenance_preventive">
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info d-none" id="alert-external-preventive">
                        <strong>Mantenimiento externo.</strong> Las acciones realizadas se sustentaran mediante documentos externos. Verifique que la empresa responsable este relacionada al mantenimiento.
                    </div>
                    <div class="row" id="section-actions-preventive">
                        <div class="col-12 mb-3">
                            <div class="form-group mandatory">
                                <label class="form-label">Acciones Realizadas</label>
                                <div id="content-actions-maintenance-preventive-checks" class="border rounded p-3" style="max-height:220px; overflow:auto;">
                                    <div class="text-muted small">Cargando acciones...</div>
                                </div>
                                <input type="hidden" id="input-action-history-maintenance-preventive" name="action_history_maintenance_preventive" value="">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-group mandatory">
                                <label for="textarea-observation-history-maintenance-preventive" class="form-label">Observacion</label>
                                <textarea class="form-control" id="textarea-observation-history-maintenance-preventive" rows="6"
                                    placeholder="..." name="observation_history_maintenance_preventive"></textarea>
                            </div>
                        </div>
                    </div>

                    <div id="section-before-preventive">
                    <div class="custom-header-modal bg-secondary mb-2">
                        <div class="custom-header-title">
                            <h4 class="m-0">Fotografias Antes del Mantenimiento</h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div id="custom-btn-upload-table-before"></div>
                            <div class="table-responsive">
                                <table id="table-handler-before" class="table mb-0 custom-table-handler">
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
                    </div>

                    <div id="section-after-preventive">
                    <div class="custom-header-modal bg-secondary mb-2">
                        <div class="custom-header-title">
                            <h4 class="m-0">Fotografias Despues del Mantenimiento</h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div id="custom-btn-upload-table-after"></div>
                            <div class="table-responsive">
                                <table id="table-handler-after" class="table mb-0 custom-table-handler">
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
                    </div>

                    <div id="section-additional-documents-preventive">
                        <div class="custom-header-modal bg-secondary mb-2">
                            <div class="custom-header-title">
                                <h4 class="m-0" id="title-additional-documents-preventive">Documentos adicionales de la tarea realizada</h4>
                            </div>
                        </div>
                        <p class="text-muted small mb-2" id="help-additional-documents-preventive">Adjunte documentos opcionales que sustenten o complementen la tarea realizada, como informes, actas, formatos firmados u otras evidencias.</p>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div id="custom-btn-upload-table-additional"></div>
                                <div class="table-responsive">
                                    <table id="table-handler-additional" class="table mb-0 custom-table-handler">
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
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Cerrar</span>
                </button>
                <button type="submit" form="formExecute" class="btn btn-secondary ms-1">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span id="btnSubmitExecute" class="d-none d-sm-block">Ejecutar</span>
                </button>
            </div>

        </div>
    </div>
</div>
