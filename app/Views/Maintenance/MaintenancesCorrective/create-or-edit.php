<div class="modal fade text-left" id="ModalCRUD" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel4"></h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form id="formMaintenanceCorrective" enctype="multipart/form-data">

          <div class="custom-header-modal bg-secondary">
            <div class="custom-header-title"><h4 class="m-0">Activo</h4></div>
            <div class="custom-header-btn"><button type="button" class="btn btn-info js-btn-search-asset">Buscar</button></div>
          </div>

          <div class="row">
            <div class="col-12 mb-3">
              <label for="input-code-inventory-asset" class="form-label">Codigo de Inventario</label>
              <input type="text" id="input-code-inventory-asset" class="form-control" name="code_inventory_asset" disabled readonly>
            </div>
          </div>

          <div class="row">
            <div class="col-12 col-lg-6 mb-3">
              <label for="input-name-type-asset" class="form-label">Tipo de Activo</label>
              <input type="text" id="input-name-type-asset" class="form-control" name="name_type_asset" disabled readonly>
            </div>
            <div class="col-12 col-lg-6 mb-3">
              <label for="input-name-category-asset" class="form-label">Categoria de Activo</label>
              <input type="text" id="input-name-category-asset" class="form-control" name="name_category_asset" disabled readonly>
            </div>
          </div>

          <div class="row">
            <div class="col-12 col-lg-6 mb-3">
              <label for="input-name-brand" class="form-label">Marca de Activo</label>
              <input type="text" id="input-name-brand" class="form-control" name="name_brand" disabled readonly>
            </div>
            <div class="col-12 col-lg-6 mb-3">
              <label for="input-name-condition-asset" class="form-label">Condicion de Activo</label>
              <input type="text" id="input-name-condition-asset" class="form-control" name="name_condition_asset" disabled readonly>
            </div>
          </div>

          <div class="row">
            <div class="col-12 mb-3">
              <label for="textarea-descriptions-asset" class="form-label">Descripcion del Activo</label>
              <textarea class="form-control" id="textarea-descriptions-asset" rows="4" name="descriptions_asset" disabled readonly></textarea>
            </div>
          </div>

          <div class="custom-header-modal bg-secondary">
            <div class="custom-header-title"><h4 class="m-0">Atencion Correctiva</h4></div>
          </div>

          <div class="row">
            <input type="hidden" id="input-id-incident-maintenance-corrective" name="id_incident_maintenance_corrective">
            <div class="col-12 mb-3">
              <label for="select-origin-maintenance-corrective" class="form-label">Origen del Mantenimiento</label>
              <select class="form-select" id="select-origin-maintenance-corrective" name="id_origin_maintenance_corrective">
                <option selected disabled value="">Selecciona una opcion</option>
              </select>
            </div>
          </div>

          <div class="row g-3 align-items-end">
            <div class="col-12 col-md-4 mb-3">
              <label for="input-date-repair-maintenance-corrective" class="form-label">Fecha (opcional)</label>
              <div class="input-group">
                <span class="input-group-text">
                  <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
                </span>
                <input data-datepicker class="form-control" id="input-date-repair-maintenance-corrective" type="text" placeholder="dd/mm/yyyy" name="date_repair_maintenance_corrective">
              </div>
            </div>
            <div class="col-12 col-md-4 mb-3">
              <label for="input-time-repair-maintenance-corrective" class="form-label">Hora (opcional)</label>
              <input class="form-control" id="input-time-repair-maintenance-corrective" type="time" name="time_repair_maintenance_corrective">
            </div>
            <div class="col-12 col-md-4 mb-3">
              <label for="select-types-responsible-maintenance" class="form-label">Responsable</label>
              <select class="form-select" id="select-types-responsible-maintenance" name="id_type_responsible_maintenance">
                <option selected disabled value="">Selecciona una opcion</option>
              </select>
            </div>
          </div>

          <div class="row">
            <div class="col-12 mb-3">
              <label for="select-status-active-maintenance-corrective" class="form-label">Estado del Mantenimiento</label>
              <select class="form-select" id="select-status-active-maintenance-corrective" name="status_active_maintenance_corrective">
                <option value="1" selected>En proceso</option>
                <option value="0">Culminado</option>
              </select>
              <small class="text-muted">Si el mantenimiento proviene de una incidencia, al marcarlo como culminado se cerrara la incidencia relacionada.</small>
            </div>
          </div>

          <div class="row">
            <div class="col-12 mb-3">
              <label for="textarea-action-maintenance-corrective" class="form-label">Accion Realizada</label>
              <textarea class="form-control" id="textarea-action-maintenance-corrective" rows="5" name="action_maintenance_corrective" required></textarea>
            </div>
          </div>

          <div class="row">
            <div class="col-12 mb-3">
              <label for="textarea-observation-maintenance-corrective" class="form-label">Observacion</label>
              <textarea class="form-control" id="textarea-observation-maintenance-corrective" rows="5" name="observation_maintenance_corrective"></textarea>
            </div>
          </div>

          <div class="custom-header-modal bg-secondary">
            <div class="custom-header-title"><h4 class="m-0">Repuestos Utilizados</h4></div>
          </div>

          <div class="row align-items-end">
            <div class="col-12 col-lg-5 mb-3">
              <label for="select-spare-part-maintenance-corrective" class="form-label">Repuesto</label>
              <select class="form-select" id="select-spare-part-maintenance-corrective">
                <option selected disabled value="">Seleccione un repuesto</option>
              </select>
            </div>
            <div class="col-12 col-lg-2 mb-3">
              <label for="input-quantity-spare-part-maintenance-corrective" class="form-label">Cantidad</label>
              <input type="number" step="0.01" min="0.01" class="form-control" id="input-quantity-spare-part-maintenance-corrective">
            </div>
            <div class="col-12 col-lg-3 mb-3">
              <label for="input-observation-spare-part-maintenance-corrective" class="form-label">Observacion</label>
              <input type="text" class="form-control" id="input-observation-spare-part-maintenance-corrective">
            </div>
            <div class="col-12 col-lg-2 mb-3">
              <button type="button" class="btn btn-info w-100" id="btnAddSparePartMaintenanceCorrective">Agregar</button>
            </div>
          </div>
          <input type="hidden" id="input-spare-parts-maintenance-corrective" name="spare_parts_maintenance_corrective" value="[]">
          <div class="row">
            <div class="col-12 mb-3">
              <div class="table-responsive">
                <table class="table table-bordered mb-0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Repuesto</th>
                      <th>Cantidad</th>
                      <th>Observacion</th>
                      <th>Accion</th>
                    </tr>
                  </thead>
                  <tbody id="tbody-spare-parts-maintenance-corrective">
                    <tr><td colspan="5" class="text-center text-muted">Sin repuestos registrados.</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="custom-header-modal bg-secondary">
            <div class="custom-header-title"><h4 class="m-0">Documentos Adicionales</h4></div>
          </div>

          <div class="row">
            <div class="col-12 mb-3">
              <div id="custom-btn-upload-table"></div>
              <div class="table-responsive">
                <table id="table-handler" class="table mb-0 custom-table-handler">
                  <thead class="thead-dark">
                    <tr>
                      <th>#</th>
                      <th>Archivo</th>
                      <th>Accion</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
          </div>

        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><span>Cerrar</span></button>
        <button type="submit" form="formMaintenanceCorrective" class="btn btn-secondary ms-1"><span id="btnSubmit">Guardar</span></button>
      </div>
    </div>
  </div>
</div>
