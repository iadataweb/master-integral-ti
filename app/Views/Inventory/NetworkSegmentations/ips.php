<div class="modal fade text-left" id="ModalIps" tabindex="-1" role="dialog" aria-labelledby="labelNetworkSegmentationIps"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="labelNetworkSegmentationIps">Direcciones IP Generadas</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="asset-panel">
                    <h6 class="asset-panel-title">Estado de direcciones IP</h6>
                    <div class="asset-panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0 asset-table">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">#</th>
                                        <th>Dirección IP</th>
                                        <th>Asignada a</th>
                                        <th style="width: 160px;">Estado</th>
                                        <th style="width: 220px;">Actualizar Estado</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-network-segmentation-ips">
                                    <tr><td colspan="5" class="text-muted text-center">Sin registros.</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
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
