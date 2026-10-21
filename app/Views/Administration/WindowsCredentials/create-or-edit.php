<div class="modal fade text-left" id="ModalCRUD" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="labelWindowsCredential"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formWindowsCredential">
                    <input type="hidden" id="input-id-windows-credential" name="id_windows_credential">
                    <input type="hidden" id="input-current-password-updated-at-windows-credential" name="current_password_updated_at_windows_credential">

                    <section class="credential-section mb-3">
                        <h6 class="credential-section-title">Datos de acceso</h6>
                        <div class="row">
                            <div class="col-12 col-lg-8 mb-3">
                                <label class="form-label" for="input-name-windows-credential">Nombre</label>
                                <input type="text" class="form-control" id="input-name-windows-credential" name="name_windows_credential" placeholder="Ej. Servidor de archivos">
                            </div>
                            <div class="col-12 col-lg-4 mb-3">
                                <label class="form-label" for="select-status-active-windows-credential">Estado</label>
                                <select class="form-select" id="select-status-active-windows-credential" name="status_active_windows_credential">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                            <div class="col-12 col-lg-6 mb-3">
                                <label class="form-label" for="input-network-address-windows-credential">Direccion de red o internet</label>
                                <input type="text" class="form-control" id="input-network-address-windows-credential" name="network_address_windows_credential" placeholder="192.168.1.10, dominio.local o URL">
                            </div>
                            <div class="col-12 col-lg-6 mb-3">
                                <label class="form-label" for="input-username-windows-credential">Usuario</label>
                                <input type="text" class="form-control" id="input-username-windows-credential" name="username_windows_credential" placeholder="Usuario">
                            </div>
                            <div class="col-12 col-lg-6 mb-3">
                                <label class="form-label" for="input-password-windows-credential">Contrasena</label>
                                <input type="password" class="form-control" id="input-password-windows-credential" name="password_windows_credential" placeholder="Ingrese contrasena">
                                <small class="text-muted js-password-help">En edicion, deje vacio para mantener la contrasena actual.</small>
                            </div>
                            <div class="col-12 col-lg-6 mb-3">
                                <label class="form-label" for="input-password-updated-at-windows-credential">Fecha de actualizacion</label>
                                <input type="date" class="form-control" id="input-password-updated-at-windows-credential" name="password_updated_at_windows_credential">
                            </div>
                        </div>
                    </section>

                    <section class="credential-section">
                        <h6 class="credential-section-title">Descripcion</h6>
                        <textarea class="form-control" id="textarea-description-windows-credential" name="description_windows_credential" rows="4" placeholder="Ingrese descripcion, alcance o notas de uso"></textarea>
                    </section>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" form="formWindowsCredential" class="btn btn-secondary ms-1">
                    <span id="btnSubmitWindowsCredential"></span>
                </button>
            </div>
        </div>
    </div>
</div>
