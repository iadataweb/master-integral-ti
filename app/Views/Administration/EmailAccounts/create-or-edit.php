<div class="modal fade text-left" id="ModalCRUD" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="labelEmailAccount"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEmailAccount">
                    <input type="hidden" id="input-id-email-account" name="id_email_account">

                    <section class="credential-section mb-3">
                        <h6 class="credential-section-title">Datos de la cuenta</h6>
                        <div class="row">
                            <div class="col-12 col-lg-8 mb-3">
                                <label class="form-label" for="input-name-email-account">Nombre</label>
                                <input type="text" class="form-control" id="input-name-email-account" name="name_email_account" placeholder="Nombre del responsable o cuenta">
                            </div>
                            <div class="col-12 col-lg-4 mb-3">
                                <label class="form-label" for="select-status-email-account">Estado</label>
                                <select class="form-select" id="select-status-email-account" name="status_email_account">
                                    <option value="active">Activo</option>
                                    <option value="inactive">Inactivo</option>
                                    <option value="suspended">Suspendido</option>
                                </select>
                            </div>
                            <div class="col-12 col-lg-6 mb-3">
                                <label class="form-label" for="input-email-email-account">Correo electronico</label>
                                <input type="email" class="form-control" id="input-email-email-account" name="email_email_account" placeholder="usuario@dominio.com">
                            </div>
                            <div class="col-12 col-lg-6 mb-3">
                                <label class="form-label" for="input-phone-email-account">Telefono asociado</label>
                                <input type="text" class="form-control" id="input-phone-email-account" name="phone_email_account" placeholder="Telefono o anexo">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="input-password-email-account">Contrasena</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="input-password-email-account" name="password_email_account" placeholder="Ingrese contrasena">
                                    <button class="btn btn-outline-gray-600 js-toggle-password" type="button" data-target="#input-password-email-account" aria-label="Mostrar u ocultar contrasena">
                                        <i class="bx bx-show"></i>
                                    </button>
                                </div>
                                <small class="text-muted js-password-help">En edicion, deje vacio para mantener la contrasena actual.</small>
                            </div>
                        </div>
                    </section>

                    <section class="credential-section">
                        <h6 class="credential-section-title">Descripcion</h6>
                        <textarea class="form-control" id="textarea-description-email-account" name="description_email_account" rows="4" placeholder="Ingrese descripcion, finalidad o notas"></textarea>
                    </section>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" form="formEmailAccount" class="btn btn-secondary ms-1">
                    <span id="btnSubmitEmailAccount"></span>
                </button>
            </div>
        </div>
    </div>
</div>
