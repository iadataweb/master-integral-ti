<div class="modal fade text-left" id="ModalSearchAsset" tabindex="-1" role="dialog" aria-labelledby="movementAssetSearchTitle"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <h4 class="modal-title" id="movementAssetSearchTitle">Seleccionar activo pendiente</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
            <p class="text-muted">Selecciona un activo habilitado sin movimiento vigente. Para registrar otro movimiento de un activo ya ubicado, usa Mover.</p>

            <div class="card card-body border-0 shadow table-wrapper table-responsive">
                <table id="tableSearchAsset" class="table table-hover">
                    <thead>
                    <tr><th>#</th><th></th><th>Código</th><th>Tipo</th><th>Marca</th><th>Modelo</th><th>Serie</th><th>Situación actual</th><th>Ubicación actual</th></tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Cerrar</span>
                </button>
                <button type="submit" class="btn btn-secondary ms-1">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span id="btnSubmitAddAsset" class="d-none d-sm-block">Agregar</span>
                </button>
            </div>
            
        </div>
    </div>
</div>
