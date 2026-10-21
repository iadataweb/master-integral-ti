<div class="modal fade text-left" id="ModalSelectAsset" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel4"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="formSelectAsset">

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
                                <label for="input-code-inventory-asset" class="form-label">Código de Inventario</label>
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
                                <label for="input-name-category-asset" class="form-label">Categoría de Activo</label>
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
                                <label for="input-name-condition-asset" class="form-label">Condición de Activo</label>
                                <input type="text" id="input-name-condition-asset" class="form-control" name="name_condition_asset" disabled readonly>
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

                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Cerrar</span>
                </button>
                <button type="submit" form="formSelectAsset" class="btn btn-secondary ms-1">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span id="btnSubmit" class="d-none d-sm-block"></span>
                </button>
            </div>
            
        </div>
    </div>
</div>