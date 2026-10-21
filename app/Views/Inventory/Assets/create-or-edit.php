<div class="modal fade text-left" id="ModalCRUD" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel4"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="formAsset">
                    <div class="row">                        
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="select-category-asset"
                                    class="form-label">Categoría</label>
                                <select class="form-select" id="select-category-asset" name="id_category_asset">
                                    <option selected disabled value="">Selecciona una opción</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="select-type-asset"
                                    class="form-label">Tipo</label>
                                <select class="form-select" id="select-type-asset" name="id_type_asset">
                                    <option selected disabled value="">Selecciona una opción</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="select-brand"
                                    class="form-label">Marca</label>
                                <select class="form-select" id="select-brand" name="id_brand">
                                    <option selected disabled value="">Selecciona una opción</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="select-condition-asset"
                                    class="form-label">Condición</label>
                                <select class="form-select" id="select-condition-asset" name="id_condition_asset">
                                    <option selected disabled value="">Selecciona una opción</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-model-asset" class="form-label">Modelo</label>
                                <input type="text" id="input-model-asset" class="form-control" placeholder="Modelo" name="model_asset">
                            </div>
                        </div>

                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-serial-number-asset" class="form-label">Nro. Serie</label>
                                <input type="text" id="input-serial-number-asset" class="form-control"
                                    placeholder="Nro. Serie" name="serial_number_asset">
                            </div>                            
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-acquisition-date-asset" class="form-label">Fecha de Adquisición</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
                                    </span>
                                    <input data-datepicker="" class="form-control" id="input-acquisition-date-asset" type="text" placeholder="dd/mm/yyyy" name="acquisition_date_asset">                                               
                                </div>
                            </div>                            
                        </div>

                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-entry-date-asset" class="form-label">Fecha de Ingreso</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
                                    </span>
                                    <input data-datepicker="" class="form-control" id="input-entry-date-asset" type="text" placeholder="dd/mm/yyyy" name="entry_date_asset">                                               
                                </div>
                            </div>                            
                        </div>                    
                    </div>
                    
                    <div class="row">                    
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="select-status-active-asset"
                                    class="form-label">Estado</label>
                                <select class="form-select" id="select-status-active-asset" name="status_active_asset">
                                    <option selected disabled value="">Selecciona una opción</option>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>                               
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-group">
                                <label for="company-column" class="form-label">Documentos Adicionales</label>
                                <div id="custom-btn-upload-table"></div>
                                <div class="table-responsive">
                                    <table id="table-handler" class="table mb-0 custom-table-handler">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>#</th>
                                                <th>Archivo</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-group mandatory">
                                <label for="textarea-descriptions-asset" class="form-label">Descripciones</label>
                                <textarea class="form-control" id="textarea-descriptions-asset" rows="6"
                                    placeholder="Ingrese Descripciones" name="descriptions_asset"></textarea>
                            </div>
                        </div>                                        
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-group mandatory">
                                <label for="textarea-observations-asset" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="textarea-observations-asset" rows="6"
                                    placeholder="Ingrese Observaciones" name="observations_asset"></textarea>
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
                <button type="submit" form="formAsset" class="btn btn-secondary ms-1">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span id="btnSubmit" class="d-none d-sm-block"></span>
                </button>
            </div>
            
        </div>
    </div>
</div>