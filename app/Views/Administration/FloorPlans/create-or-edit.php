<div class="modal fade text-left" id="ModalCRUD" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="modal-title-floor-plan"></h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formFloorPlan" enctype="multipart/form-data">
          <input type="hidden" id="input-id-floor-plan" name="id_floor_plan">

          <section class="asset-section">
            <h6 class="asset-section-title">Datos del plano</h6>
            <div class="row">
              <div class="col-12 col-lg-8 mb-3">
                <div class="form-group mandatory">
                  <label for="input-name-floor-plan" class="form-label">Nombre del plano</label>
                  <input type="text" id="input-name-floor-plan" class="form-control" name="name_floor_plan" placeholder="Ej. Plano primer piso" required>
                </div>
              </div>
              <div class="col-12 col-lg-4 mb-3">
                <div class="form-group mandatory">
                  <label for="select-status-floor-plan" class="form-label">Estado</label>
                  <select id="select-status-floor-plan" name="status_active_floor_plan" class="form-select" required>
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12 col-lg-8 mb-3">
                <div class="form-group mandatory">
                  <label for="select-campus-floor-plan" class="form-label">Sede / Establecimiento</label>
                  <select id="select-campus-floor-plan" name="id_campus_floor_plan" class="form-select" required>
                    <option value="">Selecciona una opcion</option>
                  </select>
                </div>
              </div>
              <div class="col-12 col-lg-4 mb-3">
                <div class="form-group mandatory">
                  <label for="input-floor-floor-plan" class="form-label">Piso o nivel</label>
                  <input type="number" id="input-floor-floor-plan" class="form-control" name="floor_floor_plan" placeholder="Ej. 1" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12 mb-3">
                <label for="textarea-description-floor-plan" class="form-label">Descripcion</label>
                <textarea id="textarea-description-floor-plan" name="description_floor_plan" class="form-control" rows="3" placeholder="Ingrese una descripcion breve"></textarea>
              </div>
            </div>
          </section>

          <section class="asset-section">
            <h6 class="asset-section-title">Archivo PDF</h6>
            <div class="row">
              <div class="col-12 mb-3">
                <label for="input-file-floor-plan" class="form-label">Plano en PDF</label>
                <input type="file" id="input-file-floor-plan" name="file_floor_plan" class="form-control" accept="application/pdf">
                <small class="text-muted" id="file-floor-plan-helper">Adjunte el archivo PDF del plano.</small>
              </div>
            </div>
          </section>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
        <button type="submit" form="formFloorPlan" class="btn btn-secondary ms-1">
          <span id="btnSubmit">Guardar</span>
        </button>
      </div>
    </div>
  </div>
</div>
