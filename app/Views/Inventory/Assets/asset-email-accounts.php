<link rel="stylesheet" href="<?= assets(); ?>/css/components/inventory-assets-asset-email-accounts.css?v=<?= filemtime(dirname(__DIR__, 3) . '/Assets/css/components/inventory-assets-asset-email-accounts.css'); ?>">

<div class="modal fade text-left" id="ModalAssetEmailAccounts" tabindex="-1" role="dialog" aria-labelledby="labelAssetEmailAccounts"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="labelAssetEmailAccounts">Cuentas de correo</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formAssetEmailAccounts" class="mb-4">
                    <input type="hidden" id="input-id-asset-email-account-assignment" name="id_asset">
                    <section class="asset-section">
                        <h6 class="asset-section-title">Nueva asignacion</h6>
                        <div class="row">
                            <div class="col-12 col-lg-8 mb-3">
                                <label for="select-asset-email-accounts" class="form-label">Correos electronicos disponibles</label>
                                <select class="form-select" id="select-asset-email-accounts" name="id_email_account[]" multiple size="6"></select>
                                <small class="text-muted">Puede seleccionar una o varias cuentas de correo activas.</small>
                            </div>
                            <div class="col-12 col-lg-4 mb-3">
                                <label for="textarea-observation-asset-email-account-assignment" class="form-label">Observacion</label>
                                <textarea class="form-control" id="textarea-observation-asset-email-account-assignment"
                                    name="observation_asset_email_account_assignment" rows="6" placeholder="Detalle breve de la asignacion"></textarea>
                            </div>
                        </div>
                    </section>
                </form>

                <div class="asset-panel mb-3">
                    <h6 class="asset-panel-title">Correos electronicos asignados</h6>
                    <div class="asset-panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0 asset-table">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">#</th>
                                        <th>Nombre</th>
                                        <th>Correo</th>
                                        <th>Telefono</th>
                                        <th>Fecha</th>
                                        <th style="width: 130px;">Accion</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-asset-current-email-accounts">
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
                                        <th>Cuenta</th>
                                        <th>Correo</th>
                                        <th>Accion</th>
                                        <th>Usuario</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-asset-email-account-history">
                                    <tr><td colspan="6" class="text-muted text-center">Sin registros.</td></tr>
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
                <button type="submit" form="formAssetEmailAccounts" class="btn btn-secondary ms-1">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Asignar Correo</span>
                </button>
            </div>
        </div>
    </div>
</div>