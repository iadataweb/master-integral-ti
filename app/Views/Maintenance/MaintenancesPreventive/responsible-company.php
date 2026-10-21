<div class="modal fade text-left" id="ModalResponsibleCompany" tabindex="-1" role="dialog" aria-labelledby="labelResponsibleCompany"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <h4 class="modal-title" id="labelResponsibleCompany"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="formMaintenancePreventiveCompany">

                    <div class="custom-header-modal bg-secondary">
                        <div class="custom-header-title">
                            <h4 class="m-0">Empresa</h4>
                        </div>
                        <div class="custom-header-btn">
                            <button type="button" class="btn btn-info js-btn-search-company">Buscar</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-group">
                                <label for="input-name-company" class="form-label">Nombre</label>
                                <input type="text" id="input-name-company" class="form-control" name="name_company" disabled readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group">
                                <label for="input-ruc-company" class="form-label">RUC</label>
                                <input type="text" id="input-ruc-company" class="form-control" name="ruc_company" disabled readonly>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group">
                                <label for="input-cell-phone-company" class="form-label">Número de Celular</label>
                                <input type="text" id="input-cell-phone-company" class="form-control" name="cell_phone_company" disabled readonly>
                            </div>                            
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group">
                                <label for="input-telephone-company" class="form-label">Número de Teléfono</label>
                                <input type="text" id="input-telephone-company" class="form-control" name="telephone_company" disabled readonly>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group">
                                <label for="input-telephone-company" class="form-label">Correo Electrónico</label>
                                <input type="text" id="input-telephone-company" class="form-control" name="telephone_company" disabled readonly>
                            </div>                            
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-group mandatory">
                                <label for="textarea-description-company" class="form-label">Descripción</label>
                                <textarea class="form-control" id="textarea-description-company" rows="6"
                                    name="description_company" disabled readonly></textarea>
                            </div>
                        </div>                                        
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-group mandatory">
                                <label for="textarea-observation-company" class="form-label">Observación</label>
                                <textarea class="form-control" id="textarea-observation-company" rows="6"
                                    name="observation_company" disabled readonly></textarea>
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
            </div>
            
        </div>
    </div>
</div>