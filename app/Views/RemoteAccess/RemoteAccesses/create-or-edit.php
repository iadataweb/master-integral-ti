<div class="modal fade" id="ModalCRUD" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="h5 modal-title" id="labelRemoteAccess">Nuevo Registro</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="formRemoteAccess" autocomplete="off">
        <input type="hidden" id="input-id-remote-access" name="id_remote_access">
        <input type="hidden" id="input-id-asset-remote-access" name="id_asset_remote_access">
        <div class="modal-body">
          <section class="remote-section mb-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
              <h3 class="remote-section-title mb-0">Activo asociado</h3>
              <button type="button" class="btn btn-primary js-btn-search-asset-remote-access"><i class="bx bx-search me-2"></i>Buscar activo</button>
            </div>
            <div class="asset-selected-panel mb-3">
              <div class="remote-grid">
                <div class="form-group"><label class="form-label">Codigo de Inventario</label><input type="text" id="input-code-inventory-asset-remote-access" class="form-control" readonly></div>
                <div class="form-group"><label class="form-label">Usuario del Equipo</label><input type="text" id="input-equipment-username-remote-access" class="form-control" readonly></div>
                <div class="form-group"><label class="form-label">Tipo de Activo</label><input type="text" id="input-type-asset-remote-access" class="form-control" readonly></div>
                <div class="form-group"><label class="form-label">Categoria de Activo</label><input type="text" id="input-category-asset-remote-access" class="form-control" readonly></div>
                <div class="form-group"><label class="form-label">Marca</label><input type="text" id="input-brand-asset-remote-access" class="form-control" readonly></div>
                <div class="form-group"><label class="form-label">Ubicacion Actual</label><input type="text" id="input-current-location-remote-access" class="form-control" readonly></div>
              </div>
            </div>
          </section>
          <section class="remote-section">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
              <div>
                <h3 class="remote-section-title mb-1">Aplicaciones de acceso remoto</h3>
                <small class="text-muted">Cada activo tiene un solo registro principal, pero puede contener varias aplicaciones.</small>
              </div>
              <button type="button" id="btnAddRemoteApplication" class="btn btn-outline-primary"><i class="bx bx-plus me-2"></i>Agregar aplicacion</button>
            </div>
            <div class="row mb-3">
              <div class="col-12 col-lg-4">
                <label for="select-status-active-remote-access" class="form-label">Estado del registro</label>
                <select id="select-status-active-remote-access" name="status_active_remote_access" class="form-control">
                  <option value="1">Activo</option>
                  <option value="0">Inactivo</option>
                </select>
              </div>
            </div>
            <div id="remote-applications-container"></div>
          </section>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" id="btnSubmitRemoteAccess" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>
