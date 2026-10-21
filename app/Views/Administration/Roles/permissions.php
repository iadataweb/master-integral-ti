<div class="modal fade text-left" id="ModalPermission" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel4"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="formPermission">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Módulo Grupo</th>
                                    <th>Ver</th>
                                    <th>Crear</th>
                                    <th>Actualizar</th>
                                    <th>Eliminar</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php

                                foreach ($data['permissions_modules'] as $index => $permission_module) {
                                    $r_check = $permission_module['r_read_permission'] === 1 ? " checked " : "";
                                    $w_check = $permission_module['w_write_permission'] === 1 ? " checked " : "";
                                    $u_check = $permission_module['u_update_permission'] === 1 ? " checked " : "";
                                    $d_check = $permission_module['d_delete_permission'] === 1 ? " checked " : "";
                                ?>
                                
                                <input type="hidden" class="form-control" value="<?= $permission_module['id_module']; ?>" name="modules[<?= $permission_module['id_module']; ?>][id_module]">

                                <tr>
                                    <td><?= (int)($permission_module['id_permission'] ?? $permission_module['id_module']); ?></td>
                                    <td><?= $permission_module['name_module']; ?></td>
                                    <td>
                                        <div class="form-check">
                                            <div class="checkbox d-flex">
                                                <input type="checkbox" class="form-check-input" name="modules[<?= $permission_module['id_module']; ?>][r]" <?= $r_check; ?>>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check">
                                            <div class="checkbox d-flex">
                                                <input type="checkbox" class="form-check-input" name="modules[<?= $permission_module['id_module']; ?>][w]" <?= $w_check; ?>>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check">
                                            <div class="checkbox d-flex">
                                                <input type="checkbox" class="form-check-input" name="modules[<?= $permission_module['id_module']; ?>][u]" <?= $u_check; ?>>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check">
                                            <div class="checkbox d-flex">
                                                <input type="checkbox" class="form-check-input" name="modules[<?= $permission_module['id_module']; ?>][d]" <?= $d_check; ?>>
                                            </div>
                                        </div>
                                    </td>                                                                                                                                                            
                                </tr>     

                                <?php
                                }
                                ?>
                                                                    
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Cerrar</span>
                </button>
                <button type="submit" form="formPermission" class="btn btn-secondary ms-1">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span id="btnSubmitPermission" class="d-none d-sm-block">Actualizar</span>
                </button>
            </div>
            
        </div>
    </div>
</div>