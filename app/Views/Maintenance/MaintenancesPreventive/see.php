<div class="modal fade text-left" id="ModalSee" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4"
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
                                <td style="width: 250px;" class="text-bold-500">Código de Inventario</td>
                                <td>
                                    <span id="cell-code-inventory-asset"></span>
                                </td>
                            </tr>

                            <tr>
                                <td style="width: 250px;" class="text-bold-500">Tipo de Activo</td>
                                <td>
                                    <span id="cell-name-type-asset"></span>
                                </td>
                            </tr>

                            <tr>
                                <td style="width: 250px;" class="text-bold-500">Categoría de Activo</td>
                                <td>
                                    <span id="cell-name-category-asset"></span>
                                </td>
                            </tr>

                            <tr>
                                <td style="width: 250px;" class="text-bold-500">Tipo Responsable</td>
                                <td>
                                    <span id="cell-name-type-responsible-maintenance"></span>
                                </td>
                            </tr>

                            <tr>
                                <td style="width: 250px;" class="text-bold-500">Frecuencia</td>
                                <td>
                                    <span id="cell-name-frequency-maintenance"></span>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 250px;" class="text-bold-500">Fecha Programada</td>
                                <td>
                                    <span id="cell-date-scheduled-maintenance-preventive"></span>
                                </td>
                            </tr>                            
                            <tr>
                                <td style="width: 250px;" class="text-bold-500">Próximo Mantenimiento</td>
                                <td>
                                    <span id="cell-date-next-maintenance-preventive"></span>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 250px;" class="text-bold-500">Descripción</td>
                                <td>
                                    <span id="cell-description-maintenance-preventive" class="text-wrap"></span>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 250px;" class="text-bold-500">Observación</td>
                                <td>
                                    <span id="cell-observation-maintenance-preventive" class="text-wrap"></span>
                                </td>
                            </tr>    
                            <tr>
                                <td style="width: 250px;" class="text-bold-500">Fecha de Registro</td>
                                <td>
                                    <span id="cell-date-created-maintenance-preventive"></span>
                                </td>
                            </tr>                        
                        </tbody>
                    </table>
                </div>

                <div class="modal-attachments-panel mt-3">
                    <h6 class="modal-attachments-title">Archivos Adjuntos</h6>
                    <div class="modal-attachments-body">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0 modal-attachments-table">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">#</th>
                                        <th>Archivo</th>
                                        <th style="width: 130px;">Accion</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-attachments-maintenance-preventive">
                                    <tr><td colspan="3" class="text-muted text-center">Sin registros.</td></tr>
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
