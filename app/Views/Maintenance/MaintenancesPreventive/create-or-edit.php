<div class="modal fade text-left" id="ModalCRUD" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel4"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="formMaintenancePreventive" enctype="multipart/form-data">

                    <div class="custom-header-modal bg-secondary">
                        <div class="custom-header-title">
                            <h4 class="m-0">Activo</h4>
                        </div>
                        <div class="custom-header-btn">
                            <button type="button" class="btn btn-info js-btn-search-asset">Buscar</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-group">
                                <label for="input-code-inventory-asset" class="form-label">C&oacute;digo de Inventario</label>
                                <input type="text" id="input-code-inventory-asset" class="form-control" name="code_inventory_asset" disabled readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group">
                                <label for="input-name-type-asset" class="form-label">Tipo de Activo</label>
                                <input type="text" id="input-name-type-asset" class="form-control" name="name_type_asset" disabled readonly>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group">
                                <label for="input-name-category-asset" class="form-label">Categor&iacute;a de Activo</label>
                                <input type="text" id="input-name-category-asset" class="form-control" name="name_category_asset" disabled readonly>
                            </div>                            
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group">
                                <label for="input-name-brand" class="form-label">Marca de Activo</label>
                                <input type="text" id="input-name-brand" class="form-control" name="name_brand" disabled readonly>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group">
                                <label for="input-name-condition-asset" class="form-label">Condici&oacute;n de Activo</label>
                                <input type="text" id="input-name-condition-asset" class="form-control" name="name_condition_asset" disabled readonly>
                            </div>                            
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-group mandatory">
                                <label for="textarea-descriptions-asset" class="form-label">Descripciones</label>
                                <textarea class="form-control" id="textarea-descriptions-asset" rows="6"
                                    placeholder="Ingrese Descripciones" name="descriptions_asset" disabled readonly></textarea>
                            </div>
                        </div>                                        
                    </div>                    

                    <div class="custom-header-modal bg-secondary">
                        <div class="custom-header-title">
                            <h4 class="m-0">Programaci&oacute;n</h4>
                        </div>                        
                    </div>
                    
                    <div class="row">       

                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-date-scheduled-maintenance-preventive" class="form-label">Fecha Programada</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
                                    </span>
                                    <input data-datepicker="" class="form-control" id="input-date-scheduled-maintenance-preventive" type="text" placeholder="dd/mm/yyyy" name="date_scheduled_maintenance_preventive">                                               
                                </div>
                            </div>                            
                        </div>

                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="select-frequency-maintenance"
                                    class="form-label">Frecuencia</label>
                                <select class="form-select" id="select-frequency-maintenance" name="id_frequency_maintenance">
                                    <option selected disabled value="">Selecciona una opci&oacute;n</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-group mandatory">
                                <label for="select-types-responsible-maintenance"
                                    class="form-label">Tipo de Mantenimiento Preventivo</label>
                                <select class="form-select" id="select-types-responsible-maintenance" name="id_type_responsible_maintenance">
                                    <option selected disabled value="">Selecciona una opci&oacute;n</option>
                                </select>
                            </div>
                        </div>
                    </div>                                    

                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-group mandatory">
                                <label for="textarea-description-maintenance-preventive" class="form-label">Descripci&oacute;n</label>
                                <textarea class="form-control" id="textarea-description-maintenance-preventive" rows="6"
                                    placeholder="Ingrese Descripci&oacute;n" name="description_maintenance_preventive"></textarea>
                            </div>
                        </div>                                        
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-group mandatory">
                                <label for="textarea-observation-maintenance-preventive" class="form-label">Observaci&oacute;n</label>
                                <textarea class="form-control" id="textarea-observation-maintenance-preventive" rows="6"
                                    placeholder="Ingrese Observaci&oacute;n" name="observation_maintenance_preventive"></textarea>
                            </div>
                        </div>                                        
                    </div>

                    <div class="custom-header-modal bg-secondary">
                        <div class="custom-header-title">
                            <h4 class="m-0">Documentos Adicionales</h4>
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
                                                <th>Acci&oacute;n</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
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
                <button type="submit" form="formMaintenancePreventive" class="btn btn-secondary ms-1">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span id="btnSubmit" class="d-none d-sm-block"></span>
                </button>
            </div>
            
        </div>
    </div>
</div>
