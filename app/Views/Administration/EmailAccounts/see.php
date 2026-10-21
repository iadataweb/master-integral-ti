<div class="modal fade text-left" id="ModalSee" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Datos del Correo</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="credential-layout">
                    <div class="credential-grid">
                        <div class="credential-card">
                            <span class="credential-label">Nombre</span>
                            <div class="credential-value" id="see-name-email-account"></div>
                        </div>
                        <div class="credential-card">
                            <span class="credential-label">Estado</span>
                            <div class="credential-value" id="see-status-email-account"></div>
                        </div>
                        <div class="credential-card">
                            <span class="credential-label">Correo electronico</span>
                            <div class="credential-value" id="see-email-email-account"></div>
                        </div>
                        <div class="credential-card">
                            <span class="credential-label">Telefono asociado</span>
                            <div class="credential-value" id="see-phone-email-account"></div>
                        </div>
                        <div class="credential-card">
                            <span class="credential-label">Contrasena</span>
                            <div class="input-group">
                                <input type="password" class="form-control" id="see-password-email-account" readonly>
                                <button class="btn btn-outline-gray-600 js-toggle-password" type="button" data-target="#see-password-email-account">
                                    <i class="bx bx-show"></i>
                                </button>
                            </div>
                        </div>
                        <div class="credential-card">
                            <span class="credential-label">Ultima actualizacion</span>
                            <div class="credential-value" id="see-date-updated-email-account"></div>
                        </div>
                    </div>
                    <div class="credential-section">
                        <h6 class="credential-section-title">Descripcion</h6>
                        <div class="credential-value" id="see-description-email-account"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
