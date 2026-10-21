<div class="modal fade text-left" id="ModalSeeHistory" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel4">Datos del Registro</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <tbody>
                            <tr>
                                <td style="width: 250px;" class="text-bold-500">Codigo de Inventario</td>
                                <td><span id="cell-code-inventory-asset"></span></td>
                            </tr>
                            <tr>
                                <td style="width: 250px;" class="text-bold-500">Tipo de Activo</td>
                                <td><span id="cell-name-type-asset"></span></td>
                            </tr>
                            <tr>
                                <td style="width: 250px;" class="text-bold-500">Categoria de Activo</td>
                                <td><span id="cell-name-category-asset"></span></td>
                            </tr>
                            <tr>
                                <td style="width: 250px;" class="text-bold-500">Tipo Responsable</td>
                                <td><span id="cell-name-type-responsible-maintenance"></span></td>
                            </tr>
                            <tr>
                                <td style="width: 250px;" class="text-bold-500">Fecha de Incidencia</td>
                                <td><span id="cell-date-incident-history-maintenance-corrective"></span></td>
                            </tr>
                            <tr>
                                <td style="width: 250px;" class="text-bold-500">Fecha Realizado</td>
                                <td><span id="cell-date-performed-history-maintenance-corrective"></span></td>
                            </tr>
                            <tr>
                                <td style="width: 250px;" class="text-bold-500">Accion Realizada</td>
                                <td><span id="cell-action-history-maintenance-corrective" class="text-wrap"></span></td>
                            </tr>
                            <tr>
                                <td style="width: 250px;" class="text-bold-500">Observacion</td>
                                <td><span id="cell-observation-history-maintenance-corrective" class="text-wrap"></span></td>
                            </tr>
                        </tbody>
                    </table>
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
