<div class="modal fade text-left" id="ModalCRUD" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="labelOsUserAccount"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formOsUserAccount">
                    <input type="hidden" id="input-id-os-user-account" name="id_os_user_account">
                    <input type="hidden" id="input-current-password-updated-at-os-user-account" name="current_password_updated_at_os_user_account">

                    <section class="credential-section mb-3">
                        <h6 class="credential-section-title">Datos de la cuenta</h6>
                        <div class="row">
                            <div class="col-12 col-lg-8 mb-3">
                                <label class="form-label" for="input-name-os-user-account">Nombre de la cuenta</label>
                                <input type="text" class="form-control" id="input-name-os-user-account" name="name_os_user_account" placeholder="Ej. Usuario de estacion de trabajo">
                            </div>
                            <div class="col-12 col-lg-4 mb-3">
                                <label class="form-label" for="select-status-active-os-user-account">Estado</label>
                                <select class="form-select" id="select-status-active-os-user-account" name="status_active_os_user_account">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                            <div class="col-12 col-lg-6 mb-3">
                                <label class="form-label" for="input-username-os-user-account">Usuario</label>
                                <input type="text" class="form-control" id="input-username-os-user-account" name="username_os_user_account" placeholder="Usuario del sistema operativo">
                            </div>
                            <div class="col-12 col-lg-6 mb-3">
                                <label class="form-label" for="input-password-os-user-account">Contrasena</label>
                                <input type="password" class="form-control" id="input-password-os-user-account" name="password_os_user_account" placeholder="Ingrese contrasena">
                                <small class="text-muted js-password-help">En edicion, deje vacio para mantener la contrasena actual.</small>
                            </div>
                            <div class="col-12 col-lg-6 mb-3">
                                <label class="form-label" for="input-password-updated-at-os-user-account">Fecha de actualizacion</label>
                                <input type="date" class="form-control" id="input-password-updated-at-os-user-account" name="password_updated_at_os_user_account">
                            </div>
                        </div>
                    </section>

                    <section class="credential-section">
                        <h6 class="credential-section-title">Descripcion</h6>
                        <textarea class="form-control" id="textarea-description-os-user-account" name="description_os_user_account" rows="4" placeholder="Ingrese descripcion, equipo asociado o notas de uso"></textarea>
                    </section>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" form="formOsUserAccount" class="btn btn-secondary ms-1">
                    <span id="btnSubmitOsUserAccount"></span>
                </button>
            </div>
        </div>
    </div>
</div>