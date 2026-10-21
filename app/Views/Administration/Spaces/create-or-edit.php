<div class="modal fade text-left" id="ModalCRUD" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel4"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="formSpace">
                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="select-campus"
                                    class="form-label">Establecimiento</label>
                                <select class="form-select" id="select-campus" name="id_campus_space">
                                    <option selected disabled value="">Selecciona una opción</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="select-type-space"
                                    class="form-label">Tipo de Espacio</label>
                                <select class="form-select" id="select-type-space" name="id_type_space_space">
                                    <option selected disabled value="">Selecciona una opción</option>
                                </select>
                            </div>
                        </div>                        
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-name-space" class="form-label">Nombre</label>
                                <input type="text" id="input-name-space" class="form-control"
                                    placeholder="Nombre" name="name_space">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-floor-space" class="form-label">Piso</label>
                                <input type="text" id="input-floor-space" class="form-control"
                                    placeholder="Piso" name="floor_space">
                            </div>                            
                        </div>

                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="select-status-active-space"
                                    class="form-label">Estado</label>
                                <select class="form-select" id="select-status-active-space" name="status_active_space">
                                    <option selected disabled value="">Selecciona una opción</option>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-group mandatory">
                                <label for="textarea-use-space" class="form-label">Descripción</label>
                                <textarea class="form-control" id="textarea-use-space" rows="6"
                                    placeholder="Ingrese Descripción" name="use_space"></textarea>
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
                <button type="submit" form="formSpace" class="btn btn-secondary ms-1">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span id="btnSubmit" class="d-none d-sm-block"></span>
                </button>
            </div>
            
        </div>
    </div>
</div>