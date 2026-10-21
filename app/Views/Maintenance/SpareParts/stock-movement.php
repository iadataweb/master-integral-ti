<div class="modal fade text-left" id="ModalStock" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Movimiento de Stock</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formStockMovement">
          <div class="mb-3">
            <label for="select-type-spare-part-movement" class="form-label">Tipo de Movimiento</label>
            <select id="select-type-spare-part-movement" class="form-select" name="type_spare_part_movement">
              <option value="IN">Ingreso</option>
              <option value="OUT">Salida</option>
              <option value="ADJUSTMENT">Ajuste</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="input-quantity-spare-part-movement" class="form-label">Cantidad</label>
            <input type="number" step="0.01" min="0.01" id="input-quantity-spare-part-movement" class="form-control" name="quantity_spare_part_movement" required>
          </div>
          <div class="mb-3">
            <label for="textarea-reason-spare-part-movement" class="form-label">Motivo</label>
            <textarea id="textarea-reason-spare-part-movement" class="form-control" name="reason_spare_part_movement" rows="3"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
        <button type="submit" form="formStockMovement" class="btn btn-secondary ms-1">Guardar</button>
      </div>
    </div>
  </div>
</div>
