<div class="modal fade text-left" id="ModalCRUD" tabindex="-1" role="dialog" aria-labelledby="labelNetworkSegmentation"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="labelNetworkSegmentation"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="formNetworkSegmentation">
                    <input type="hidden" id="input-id-network-segmentation" name="id_network_segmentation">

                    <section class="asset-section mb-3">
                        <h6 class="asset-section-title">Datos de la segmentacion</h6>
                        <div class="row">
                            <div class="col-12 col-lg-8 mb-3">
                                <label for="input-name-network-segmentation" class="form-label">Nombre de la segmentacion</label>
                                <input type="text" class="form-control" id="input-name-network-segmentation"
                                    name="name_network_segmentation" placeholder="Ej. Red Administrativa">
                            </div>
                            <div class="col-12 col-lg-4 mb-3">
                                <label for="select-status-active-network-segmentation" class="form-label">Estado</label>
                                <select class="form-select" id="select-status-active-network-segmentation" name="status_active_network_segmentation">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="textarea-description-network-segmentation" class="form-label">Descripcion</label>
                            <textarea class="form-control" id="textarea-description-network-segmentation"
                                name="description_network_segmentation" rows="3" placeholder="Ingrese una descripcion breve"></textarea>
                        </div>
                    </section>

                    <section class="asset-section">
                        <h6 class="asset-section-title">Configuracion de red</h6>
                        <div class="row">
                            <div class="col-12 col-lg-6 mb-3">
                                <label for="input-network-address-network-segmentation" class="form-label">Red Base</label>
                                <input type="text" class="form-control" id="input-network-address-network-segmentation"
                                    name="network_address_network_segmentation" placeholder="192.168.1.0">
                            </div>
                            <div class="col-12 col-lg-6 mb-3">
                                <label for="input-subnet-mask-network-segmentation" class="form-label">Mascara de subred</label>
                                <input type="text" class="form-control" id="input-subnet-mask-network-segmentation"
                                    name="subnet_mask_network_segmentation" placeholder="255.255.255.0">
                            </div>
                            <div class="col-12 col-lg-6 mb-3">
                                <label for="input-gateway-network-segmentation" class="form-label">Gateway</label>
                                <input type="text" class="form-control" id="input-gateway-network-segmentation"
                                    name="gateway_network_segmentation" placeholder="192.168.1.1">
                            </div>
                            <div class="col-12 col-lg-6 mb-3">
                                <label for="input-dns-network-segmentation" class="form-label">DNS</label>
                                <input type="text" class="form-control" id="input-dns-network-segmentation"
                                    name="dns_network_segmentation" placeholder="8.8.8.8, 1.1.1.1">
                            </div>
                            <div class="col-12 col-lg-6 mb-3">
                                <label for="input-ip-range-start-network-segmentation" class="form-label">IP inicial disponible</label>
                                <input type="text" class="form-control" id="input-ip-range-start-network-segmentation"
                                    name="ip_range_start_network_segmentation" placeholder="192.168.1.10">
                            </div>
                            <div class="col-12 col-lg-6 mb-3">
                                <label for="input-ip-range-end-network-segmentation" class="form-label">IP final disponible</label>
                                <input type="text" class="form-control" id="input-ip-range-end-network-segmentation"
                                    name="ip_range_end_network_segmentation" placeholder="192.168.1.250">
                            </div>
                        </div>
                    </section>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Cerrar</span>
                </button>
                <button type="submit" form="formNetworkSegmentation" class="btn btn-secondary ms-1">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span id="btnSubmitNetworkSegmentation" class="d-none d-sm-block"></span>
                </button>
            </div>
        </div>
    </div>
</div>
