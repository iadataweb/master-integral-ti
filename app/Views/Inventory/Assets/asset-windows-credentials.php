<link rel="stylesheet" href="<?= assets(); ?>/css/components/inventory-assets-asset-windows-credentials.css?v=<?= filemtime(dirname(__DIR__, 3) . '/Assets/css/components/inventory-assets-asset-windows-credentials.css'); ?>">

<div class="modal fade text-left" id="ModalAssetWindowsCredentials" tabindex="-1" role="dialog" aria-labelledby="labelAssetWindowsCredentials"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="labelAssetWindowsCredentials">Acceso a Windows</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formAssetWindowsCredentials" class="mb-4">
                    <input type="hidden" id="input-id-asset-windows-credential-assignment" name="id_asset">
                    <section class="asset-section">
                        <h6 class="asset-section-title">Nueva asignacion</h6>
                        <div class="row">
                            <div class="col-12 col-lg-8 mb-3">
                                <label for="select-asset-windows-credentials" class="form-label">Accesos de Windows disponibles</label>
                                <select class="form-select" id="select-asset-windows-credentials" name="id_windows_credential[]" multiple size="6"></select>
                                <small class="text-muted">Puede seleccionar una o varias accesos de Windows activos.</small>
                            </div>
                            <div class="col-12 col-lg-4 mb-3">
                                <label for="textarea-observation-asset-windows-credential-assignment" class="form-label">Observacion</label>
                                <textarea class="form-control" id="textarea-observation-asset-windows-credential-assignment"
                                    name="observation_asset_windows_credential_assignment" rows="6" placeholder="Detalle breve de la asignacion"></textarea>
                            </div>
                        </div>
                    </section>
                </form>

                <div class="asset-panel mb-3">
                    <h6 class="asset-panel-title">Accesos de Windows asignados</h6>
                    <div class="asset-panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0 asset-table">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">#</th>
                                        <th>Credencial</th>
                                        <th>Direccion</th>
                                        <th>Usuario</th>
                                        <th>Fecha</th>
                                        <th style="width: 130px;">Accion</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-asset-current-windows-credentials">
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
                                        <th>Credencial</th>
                                        <th>Accion</th>
                                        <th>Usuario</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-asset-windows-credential-history">
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
                <button type="submit" form="formAssetWindowsCredentials" class="btn btn-secondary ms-1">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Asignar Credencial</span>
                </button>
            </div>
        </div>
    </div>
</div>