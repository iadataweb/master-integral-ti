<div class="modal fade text-left" id="ModalCRUD" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="labelWifiNetwork"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formWifiNetwork">
                    <input type="hidden" id="input-id-wifi-network" name="id_wifi_network">
                    <input type="hidden" id="input-id-asset-wifi-network" name="id_asset_wifi_network">

                    <section class="credential-section mb-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                            <h6 class="credential-section-title mb-0">Activo asignado</h6>
                            <button type="button" class="btn btn-sm btn-gray-800 js-btn-search-asset-wifi">
                                <i class="bx bx-search me-1"></i> Buscar activo
                            </button>
                        </div>
                        <div class="asset-selected-panel">
                            <div class="row">
                                <div class="col-12 col-lg-6 mb-3">
                                    <label class="form-label">Codigo de inventario</label>
                                    <input type="text" class="form-control" id="input-code-inventory-asset-wifi" readonly placeholder="Seleccione un activo">
                                </div>
                                <div class="col-12 col-lg-6 mb-3">
                                    <label class="form-label">Tipo de activo</label>
                                    <input type="text" class="form-control" id="input-type-asset-wifi" readonly>
                                </div>
                                <div class="col-12 col-lg-4 mb-3">
                                    <label class="form-label">Categoria</label>
                                    <input type="text" class="form-control" id="input-category-asset-wifi" readonly>
                                </div>
                                <div class="col-12 col-lg-4 mb-3">
                                    <label class="form-label">Marca</label>
                                    <input type="text" class="form-control" id="input-brand-asset-wifi" readonly>
                                </div>
                                <div class="col-12 col-lg-4 mb-3">
                                    <label class="form-label">Condicion</label>
                                    <input type="text" class="form-control" id="input-condition-asset-wifi" readonly>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="credential-section">
                        <h6 class="credential-section-title">Datos de red inalambrica</h6>
                        <div class="row">
                            <div class="col-12 col-lg-4 mb-3">
                                <label class="form-label" for="input-management-ip-wifi-network">Direccion IP de administracion</label>
                                <input type="text" class="form-control" id="input-management-ip-wifi-network" name="management_ip_wifi_network" placeholder="Ej. 192.168.1.1">
                            </div>
                            <div class="col-12 col-lg-5 mb-3">
                                <label class="form-label" for="input-ssid-wifi-network">SSID o nombre de red</label>
                                <input type="text" class="form-control" id="input-ssid-wifi-network" name="ssid_wifi_network" placeholder="Ej. WiFi Administracion">
                            </div>
                            <div class="col-12 col-lg-3 mb-3">
                                <label class="form-label" for="select-status-active-wifi-network">Estado</label>
                                <select class="form-select" id="select-status-active-wifi-network" name="status_active_wifi_network">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                            <div class="col-12 col-lg-8 mb-3">
                                <label class="form-label" for="input-password-wifi-network">Contrasena WiFi</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="input-password-wifi-network" name="password_wifi_network" placeholder="Ingrese o genere una contrasena segura">
                                    <button class="btn btn-outline-gray-600 js-toggle-password" type="button" data-target="#input-password-wifi-network">
                                        <i class="bx bx-show"></i>
                                    </button>
                                    <button class="btn btn-secondary" type="button" id="btnGenerateWifiPassword">
                                        <i class="bx bx-shield-quarter me-1"></i> Generar
                                    </button>
                                </div>
                                <small class="text-muted js-password-help">En edicion, deje vacio para mantener la contrasena actual.</small>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="textarea-description-wifi-network">Descripcion</label>
                                <textarea class="form-control" id="textarea-description-wifi-network" name="description_wifi_network" rows="4" placeholder="Detalle de ubicacion, cobertura, observaciones o notas tecnicas"></textarea>
                            </div>
                        </div>
                    </section>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" form="formWifiNetwork" class="btn btn-secondary ms-1">
                    <span id="btnSubmitWifiNetwork"></span>
                </button>
            </div>
        </div>
    </div>
</div>