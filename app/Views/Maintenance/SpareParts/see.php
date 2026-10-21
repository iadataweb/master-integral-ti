<div class="modal fade text-left" id="ModalSee" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Datos del Repuesto</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="modal-detail-layout">
          <section class="modal-detail-section">
            <h6 class="modal-detail-section-title">Datos del repuesto</h6>
            <div class="modal-detail-grid">
              <article class="modal-detail-card">
                <span class="modal-detail-label">Codigo</span>
                <strong class="modal-detail-value" id="cell-code-spare-part"></strong>
              </article>
              <article class="modal-detail-card">
                <span class="modal-detail-label">Repuesto</span>
                <strong class="modal-detail-value" id="cell-name-spare-part"></strong>
              </article>
              <article class="modal-detail-card">
                <span class="modal-detail-label">Marca</span>
                <strong class="modal-detail-value" id="cell-brand-spare-part"></strong>
              </article>
              <article class="modal-detail-card">
                <span class="modal-detail-label">Modelo</span>
                <strong class="modal-detail-value" id="cell-model-spare-part"></strong>
              </article>
              <article class="modal-detail-card">
                <span class="modal-detail-label">Stock Actual</span>
                <strong class="modal-detail-value" id="cell-stock-current-spare-part"></strong>
              </article>
              <article class="modal-detail-card">
                <span class="modal-detail-label">Stock Minimo</span>
                <strong class="modal-detail-value" id="cell-stock-minimum-spare-part"></strong>
              </article>
            </div>
          </section>

          <section class="modal-detail-section">
            <h6 class="modal-detail-section-title">Descripcion</h6>
            <div class="modal-attachments-body">
              <div class="modal-detail-value text-wrap" id="cell-description-spare-part"></div>
            </div>
          </section>

          <section class="modal-attachments-panel">
            <h6 class="modal-attachments-title">Movimientos de Stock</h6>
            <div class="modal-attachments-body">
              <div class="table-responsive">
                <table class="table table-bordered mb-0 modal-attachments-table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Tipo</th>
                      <th>Cantidad</th>
                      <th>Stock Final</th>
                      <th>Fecha</th>
                    </tr>
                  </thead>
                  <tbody id="tbody-spare-part-movements">
                    <tr><td colspan="5" class="text-center text-muted">Sin registros.</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </section>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
