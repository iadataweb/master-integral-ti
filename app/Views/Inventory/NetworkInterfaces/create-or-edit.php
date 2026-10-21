<div class="modal fade text-left" id="ModalCRUD" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel4"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="formCategoryAsset">
                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-mac-address-network-interface" class="form-label">Dirección MAC</label>
                                <input type="text" id="input-mac-address-network-interface" class="form-control" placeholder="Dirección MAC" name="mac_address_network_interface">
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-ip-address-network-interface" class="form-label">Dirección IP</label>
                                <input type="text" id="input-ip-address-network-interface" class="form-control" placeholder="Dirección IP" name="ip_address_network_interface">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-subnet-mask-network-interface" class="form-label">Máscara de Subred</label>
                                <input type="text" id="input-subnet-mask-network-interface" class="form-control" placeholder="Máscara de Subred" name="subnet_mask_network_interface">
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-gateway-network-interface" class="form-label">Puerta de Enlace</label>
                                <input type="text" id="input-gateway-network-interface" class="form-control" placeholder="Puerta de Enlace" name="gateway_network_interface">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-subnet-mask-network-interface" class="form-label">DNS primario</label>
                                <input type="text" id="input-subnet-mask-network-interface" class="form-control" placeholder="DNS primario" name="subnet_mask_network_interface">
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-gateway-network-interface" class="form-label">DNS secundario</label>
                                <input type="text" id="input-gateway-network-interface" class="form-control" placeholder="DNS secundario" name="gateway_network_interface">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="select-type-asset"
                                    class="form-label">Tipo</label>
                                <select class="form-select" id="select-type-asset" name="id_type_asset">
                                    <option selected disabled value="">Selecciona una opción</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="select-status-active-category-asset"
                                    class="form-label">Estado</label>
                                <select class="form-select" id="select-status-active-category-asset" name="status_active_category_asset">
                                    <option selected disabled value="">Selecciona una opción</option>
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
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Cerrar</span>
                </button>
                <button type="submit" form="formCategoryAsset" class="btn btn-secondary ms-1">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span id="btnSubmit" class="d-none d-sm-block"></span>
                </button>
            </div>
            
        </div>
    </div>
</div>