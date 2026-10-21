<div class="modal fade text-left" id="ModalCRUD" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Nuevo Repuesto</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formSparePart">
          <div class="row">
            <div class="col-12 col-lg-6 mb-3">
              <label for="input-name-spare-part" class="form-label">Nombre del Repuesto</label>
              <input type="text" id="input-name-spare-part" class="form-control" name="name_spare_part" placeholder="Nombre del repuesto" required>
            </div>
            <div class="col-12 col-lg-6 mb-3">
              <label for="input-unit-spare-part" class="form-label">Unidad</label>
              <input type="text" id="input-unit-spare-part" class="form-control" name="unit_spare_part" placeholder="Unidad">
            </div>
          </div>
          <div class="row">
            <div class="col-12 col-lg-6 mb-3">
              <label for="select-brand-spare-part" class="form-label">Marca</label>
              <select class="form-select" id="select-brand-spare-part" name="id_brand" required>
                <option selected disabled value="">Selecciona una opción</option>
              </select>
            </div>
            <div class="col-12 col-lg-6 mb-3">
              <label for="input-model-spare-part" class="form-label">Modelo</label>
              <input type="text" id="input-model-spare-part" class="form-control" name="model_spare_part" placeholder="Modelo">
            </div>
          </div>
          <div class="row">
            <div class="col-12 col-lg-4 mb-3 js-stock-initial-field">
              <label for="input-stock-current-spare-part" class="form-label">Stock Inicial</label>
              <input type="number" step="0.01" min="0" id="input-stock-current-spare-part" class="form-control" name="stock_current_spare_part" placeholder="Stock inicial" value="0">
            </div>
            <div class="col-12 col-lg-4 mb-3">
              <label for="input-stock-minimum-spare-part" class="form-label">Stock Mínimo</label>
              <input type="number" step="0.01" min="0" id="input-stock-minimum-spare-part" class="form-control" name="stock_minimum_spare_part" placeholder="Stock mínimo" value="0">
            </div>
            <div class="col-12 col-lg-4 mb-3">
              <label for="select-status-active-spare-part" class="form-label">Estado</label>
              <select id="select-status-active-spare-part" class="form-select" name="status_active_spare_part">
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="col-12 mb-3">
              <label for="textarea-description-spare-part" class="form-label">Descripción</label>
              <textarea id="textarea-description-spare-part" class="form-control" name="description_spare_part" placeholder="Ingrese una descripción" rows="4"></textarea>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
        <button type="submit" form="formSparePart" class="btn btn-secondary ms-1"><span id="btnSubmit">Guardar</span></button>
      </div>
    </div>
  </div>
</div>
