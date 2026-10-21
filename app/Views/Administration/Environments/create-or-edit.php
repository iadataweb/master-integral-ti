<div class="modal fade text-left" id="ModalCRUD" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel4"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="formEnvironment">
                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="select-campus" class="form-label">Establecimiento</label>
                                <select class="form-select" id="select-campus" name="id_campus_environment">
                                    <option selected disabled value="">Selecciona una opcion</option>
                                </select>
                            </div>
                        </div>
                        </div>

                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-name-environment" class="form-label">Ambiente</label>
                                <input type="text" id="input-name-environment" class="form-control"
                                    placeholder="Nombre del ambiente" name="name_environment">
                            </div>
                        </div>
                        <div class="col-12 col-lg-3 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-floor-environment" class="form-label">Piso</label>
                                <input type="number" id="input-floor-environment" class="form-control"
                                    placeholder="Piso" name="floor_environment">
                            </div>
                        </div>
                        <div class="col-12 col-lg-3 mb-3">
                            <div class="form-group mandatory">
                                <label for="select-status-active-environment" class="form-label">Estado</label>
                                <select class="form-select" id="select-status-active-environment" name="status_active_environment">
                                    <option selected disabled value="">Selecciona una opcion</option>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-group">
                                <label for="textarea-use-environment" class="form-label">Uso / descripcion</label>
                                <textarea class="form-control" id="textarea-use-environment" rows="5"
                                    placeholder="Describe el uso principal del ambiente" name="use_environment"></textarea>
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
                <button type="submit" form="formEnvironment" class="btn btn-secondary ms-1">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span id="btnSubmit" class="d-none d-sm-block"></span>
                </button>
            </div>

        </div>
    </div>
</div>
