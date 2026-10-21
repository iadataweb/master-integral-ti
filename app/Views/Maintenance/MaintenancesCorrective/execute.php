<div class="modal fade text-left" id="ModalExecute" tabindex="-1" role="dialog" aria-labelledby="labelExecute"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <h4 class="modal-title" id="labelExecute"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="formExecute">                    
                    <div class="row">                       
                        <div class="col-12 mb-3">
                            <div class="form-group mandatory">
                                <label for="select-status-maintenance"
                                    class="form-label">Estado</label>
                                <select class="form-select" id="select-status-maintenance" name="id_status_maintenance">
                                    <option selected disabled value="">Selecciona una opción</option>
                                </select>
                            </div>
                        </div>
                    </div>                               
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-group mandatory">
                                <label for="textarea-action-history-maintenance-preventive" class="form-label">Acción Realizado</label>
                                <textarea class="form-control" id="textarea-action-history-maintenance-preventive" rows="6"
                                    placeholder="..." name="action_history_maintenance_corrective"></textarea>
                            </div>
                        </div>                                        
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-group mandatory">
                                <label for="textarea-observation-history-maintenance-preventive" class="form-label">Observación</label>
                                <textarea class="form-control" id="textarea-observation-history-maintenance-preventive" rows="6"
                                    placeholder="..." name="observation_history_maintenance_corrective"></textarea>
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

