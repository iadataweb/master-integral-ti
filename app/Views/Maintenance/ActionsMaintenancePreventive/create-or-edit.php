<div class="modal fade text-left" id="ModalCRUD" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel4"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="formActionMaintenancePreventive">
                    <div class="row">
                        <div class="col-12 col-lg-8 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-name-parameter-maintenance-preventive-action" class="form-label">Accion estandar</label>
                                <input type="text" id="input-name-parameter-maintenance-preventive-action" class="form-control"
                                    placeholder="Ingrese accion estandar" name="name_parameter_maintenance_preventive_action">
                            </div>
                        </div>
                        <div class="col-12 col-lg-4 mb-3">
                            <div class="form-group mandatory">
                                <label for="select-status-active-parameter-maintenance-preventive-action" class="form-label">Estado</label>
                                <select class="form-select" id="select-status-active-parameter-maintenance-preventive-action" name="status_active_parameter_maintenance_preventive_action">
                                    <option selected disabled value="">Selecciona una opcion</option>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <span class="d-none d-sm-block">Cerrar</span>
                </button>
                <button type="submit" form="formActionMaintenancePreventive" class="btn btn-secondary ms-1">
                    <span id="btnSubmit" class="d-none d-sm-block"></span>
                </button>
            </div>

        </div>
    </div>
</div>
