<div class="modal fade text-left" id="ModalSee" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Datos de la Red WiFi</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="credential-layout">
                    <div class="credential-grid">
                        <div class="credential-card">
                            <span class="credential-label">Codigo de inventario</span>
                            <div class="credential-value" id="see-code-inventory-asset-wifi"></div>
                        </div>
                        <div class="credential-card">
                            <span class="credential-label">Estado</span>
                            <div class="credential-value" id="see-status-wifi-network"></div>
                        </div>
                        <div class="credential-card">
                            <span class="credential-label">Tipo de activo</span>
                            <div class="credential-value" id="see-type-asset-wifi"></div>
                        </div>
                        <div class="credential-card">
                            <span class="credential-label">Categoria</span>
                            <div class="credential-value" id="see-category-asset-wifi"></div>
                        </div>
                        <div class="credential-card">
                            <span class="credential-label">IP administracion</span>
                            <div class="credential-value" id="see-management-ip-wifi-network"></div>
                        </div>
                        <div class="credential-card">
                            <span class="credential-label">SSID</span>
                            <div class="credential-value" id="see-ssid-wifi-network"></div>
                        </div>
                        <div class="credential-card">
                            <span class="credential-label">Contrasena WiFi</span>
                            <div class="input-group">
                                <input type="password" class="form-control" id="see-password-wifi-network" readonly>
                                <button class="btn btn-outline-gray-600 js-toggle-password" type="button" data-target="#see-password-wifi-network">
                                    <i class="bx bx-show"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="credential-section">
                        <h6 class="credential-section-title">Descripcion</h6>
                        <div class="credential-value" id="see-description-wifi-network"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>