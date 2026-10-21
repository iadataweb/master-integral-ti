<div class="modal fade text-left" id="ModalFolder" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel4">Archivos</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="col-12 mb-2">
                    <div class="form-group">
                        <label for="company-column" class="form-label">Documentos Adicionales</label>                                                            
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Archivo</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>

                                <?php 
                                    
                                    if (!empty($data['attachments_maintenance_preventive']) && is_array($data['attachments_maintenance_preventive'])) { 

                                        $index_counter = 1;

                                        foreach ($data['attachments_maintenance_preventive'] as $attachment) {
                                ?>
                                        <tr>
                                            <td class="text-bold-500"><?= $index_counter ?></td>
                                            <td><?= $attachment['name_attachment_maintenance_preventive'] ?></td>
                                            <td>
                                                <a href="<?= base_url().'/'.$attachment['route_attachment_maintenance_preventive'] ?>" target="_blank" rel="noopener noreferrer" class="btn icon btn-primary btn-see">
                                                    <i class="bx bx-eye"></i>
                                                </a>
                                            </td>
                                        </tr>                                                                            

                                <?php
                                            $index_counter = $index_counter + 1;
                                        }
                                    } else {                                                                        
                                ?>
                                    <tr id="no-files-row"><td colspan="3" class="text-center text-muted">Ningún archivo disponible.</td></tr>
                                <?php
                                    }
                                ?>

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