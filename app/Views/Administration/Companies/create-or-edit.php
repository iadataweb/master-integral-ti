<div class="modal fade text-left" id="ModalCRUD" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel4"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="formCompany">
                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-name-company" class="form-label">Nombre</label>
                                <input type="text" id="input-name-company" class="form-control"
                                    placeholder="Nombre" name="name_company">
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-ruc-company" class="form-label">RUC</label>
                                <input type="text" id="input-ruc-company" class="form-control"
                                    placeholder="RUC" name="ruc_company">
                            </div>                            
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-cell-phone-company" class="form-label">Número de Celular</label>
                                <input type="text" id="input-cell-phone-company" class="form-control"
                                    placeholder="Número de Celular" name="cell_phone_company">
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-telephone-company" class="form-label">Número de Teléfono</label>
                                <input type="text" id="input-telephone-company" class="form-control"
                                    placeholder="Número de Teléfono" name="telephone_company">
                            </div>                            
                        </div>
                    </div>
                    
                    <div class="row">                        
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-email-company" class="form-label">Correo Electrónico</label>
                                <input type="text" id="input-email-company" class="form-control"
                                    placeholder="Correo Electrónico" name="email_company">
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="select-status-active-company"
                                    class="form-label">Estado</label>
                                <select class="form-select" id="select-status-active-company" name="status_active_company">
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
                                <label for="textarea-description-company" class="form-label">Descripción</label>
                                <textarea class="form-control" id="textarea-description-company" rows="6"
                                    placeholder="..." name="description_company"></textarea>
                            </div>
                        </div>                                        
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-group mandatory">
                                <label for="textarea-observation-company" class="form-label">Observación</label>
                                <textarea class="form-control" id="textarea-observation-company" rows="6"
                                    placeholder="..." name="observation_company"></textarea>
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
                <button type="submit" form="formCompany" class="btn btn-secondary ms-1">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span id="btnSubmit" class="d-none d-sm-block"></span>
                </button>
            </div>
            
        </div>
    </div>
</div>