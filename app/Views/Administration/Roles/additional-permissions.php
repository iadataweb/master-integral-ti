<div class="modal fade text-left" id="ModalAdditionalPermission" tabindex="-1" role="dialog" aria-labelledby="labelAdditionalPermission"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <h4 class="modal-title" id="labelAdditionalPermission">Actualizar Permisos Adicionales</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="formAdditionalPermission">

                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <?php
                            $modules = [];
                            foreach ($data['additional_permissions'] as $additional_permission) {
                                $modules[$additional_permission['name_module']][] = $additional_permission;
                            }

                            foreach ($modules as $module_name => $permissions) {
                            ?>

                            <tr>
                                <th>#</th>
                                <th>Módulo <?= $module_name; ?></th>
                                <th>Si</th>
                                <th>No</th>
                            </tr>

                            <?php
                            foreach ($permissions as $index => $additional_permission) {
                                $yes_check = $additional_permission['allow_additional_permission'] === 1 ? " checked " : "";
                                $not_check = $additional_permission['allow_additional_permission'] === 0 ? " checked " : "";
                            ?>
                            
                            <input type="hidden" class="form-control" value="<?= $additional_permission['id_control']; ?>" name="controls[<?= $additional_permission['id_control']; ?>][id_control]">

                            <tr>
                                <td><?= (int)($additional_permission['id_additional_permission'] ?? $additional_permission['id_control']); ?></td>
                                <td><?= $additional_permission['name_control']; ?></td>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="controls[<?= $additional_permission['id_control']; ?>][allow]" value="1" <?= $yes_check; ?>>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="controls[<?= $additional_permission['id_control']; ?>][allow]" value="0" <?= $not_check; ?>>
                                    </div>
                                </td>                                                                                                                                                          
                            </tr>

                            <?php
                            }}
                            ?>
                                                                    
                        </table>
                    </div>

                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Cerrar</span>
                </button>
                <button type="submit" form="formAdditionalPermission" class="btn btn-secondary ms-1">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span id="btnSubmitAdditionalPermission" class="d-none d-sm-block">Actualizar</span>
                </button>
            </div>
            
        </div>
    </div>
</div>