<link rel="stylesheet" href="<?= assets(); ?>/css/components/inventory-assets-asset-ips.css?v=<?= filemtime(dirname(__DIR__, 3) . '/Assets/css/components/inventory-assets-asset-ips.css'); ?>">

<div class="modal fade text-left" id="ModalAssetIps" tabindex="-1" role="dialog" aria-labelledby="labelAssetIps"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="labelAssetIps">Gestionar Direcciones IP</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="formAssetIps" class="mb-4">
                    <input type="hidden" id="input-id-asset-ip-assignment" name="id_asset">
                    <section class="asset-section">
                        <h6 class="asset-section-title">Nueva asignacion</h6>
                        <div class="row">
                            <div class="col-12 col-lg-8 mb-3">
                                <div class="mb-3">
                                    <label for="select-asset-ip-segmentation" class="form-label">Segmentacion de Red</label>
                                    <select class="form-select" id="select-asset-ip-segmentation"></select>
                                    <small class="text-muted">Seleccione una segmentacion para listar solo sus direcciones IP disponibles.</small>
                                </div>
                                <label for="select-asset-ip-addresses" class="form-label">Direcciones IP disponibles</label>
                                <select class="form-select" id="select-asset-ip-addresses" name="id_network_segmentation_ip[]" multiple size="6"></select>
                                <small class="text-muted">Puede seleccionar una o varias direcciones IP generadas desde Segmentacion de Red.</small>
                            </div>
                            <div class="col-12 col-lg-4 mb-3">
                                <div class="mb-3">
                                    <label for="select-asset-ip-interface-type" class="form-label">Tipo de interfaz</label>
                                    <select class="form-select" id="select-asset-ip-interface-type" name="id_type_network_interface" required></select>
                                </div>
                                <label for="textarea-observation-asset-ip-assignment" class="form-label">Observacion</label>
                                <textarea class="form-control" id="textarea-observation-asset-ip-assignment"
                                    name="observation_asset_ip_assignment" rows="6" placeholder="Detalle breve de la asignacion"></textarea>
                            </div>
                        </div>
                    </section>
                </form>

                <div class="asset-panel mb-3">
                    <h6 class="asset-panel-title">Direcciones IP asignadas</h6>
                    <div class="asset-panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0 asset-table">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">#</th>
                                        <th>Segmentacion</th>
                                        <th>Direccion IP</th>
                                        <th>Tipo de interfaz</th>
                                        <th>Fecha</th>
                                        <th style="width: 130px;">Accion</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-asset-current-ips">
                                    <tr><td colspan="6" class="text-muted text-center">Sin registros.</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="asset-panel">
                    <h6 class="asset-panel-title">Historial de asignaciones</h6>
                    <div class="asset-panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0 asset-table">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">#</th>
                                        <th>Segmentacion</th>
                                        <th>Direccion IP</th>
                                        <th>Tipo de interfaz</th>
                                        <th>Accion</th>
                                        <th>Usuario</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-asset-ip-history">
                                    <tr><td colspan="7" class="text-muted text-center">Sin registros.</td></tr>
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
                <button type="submit" form="formAssetIps" class="btn btn-secondary ms-1">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Asignar IP</span>
                </button>
            </div>
        </div>
    </div>
</div>

