<div class="modal fade text-left" id="ModalSee" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Datos del Plano</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="asset-layout">
          <section class="asset-section">
            <h6 class="asset-section-title">Informacion general</h6>
            <div class="floor-plan-meta">
              <div class="floor-plan-card">
                <span class="floor-plan-label">Nombre</span>
                <div class="floor-plan-value" id="cell-name-floor-plan"></div>
              </div>
              <div class="floor-plan-card">
                <span class="floor-plan-label">Sede</span>
                <div class="floor-plan-value" id="cell-campus-floor-plan"></div>
              </div>
              <div class="floor-plan-card">
                <span class="floor-plan-label">Piso</span>
                <div class="floor-plan-value" id="cell-floor-floor-plan"></div>
              </div>
              <div class="floor-plan-card">
                <span class="floor-plan-label">Version actual</span>
                <div class="floor-plan-value" id="cell-version-floor-plan"></div>
              </div>
              <div class="floor-plan-card">
                <span class="floor-plan-label">Estado</span>
                <div class="floor-plan-value" id="cell-status-floor-plan"></div>
              </div>
              <div class="floor-plan-card">
                <span class="floor-plan-label">Archivo</span>
                <div class="floor-plan-value" id="cell-file-floor-plan"></div>
              </div>
            </div>
          </section>

          <section class="asset-section">
            <h6 class="asset-section-title">Descripcion</h6>
            <div id="cell-description-floor-plan" class="text-wrap"></div>
          </section>

          <section class="asset-section">
            <h6 class="asset-section-title">Vista del plano</h6>
            <div class="floor-plan-preview">
              <img id="img-floor-plan-preview" class="floor-plan-preview-image" alt="Vista optimizada del plano">
            </div>
          </section>

          <section class="asset-section">
            <h6 class="asset-section-title">Versiones</h6>
            <div class="table-responsive">
              <table class="table table-bordered mb-0">
                <thead>
                  <tr>
                    <th style="width:80px;">#</th>
                    <th>Version</th>
                    <th>Archivo</th>
                    <th>Fecha</th>
                    <th style="width:120px;">Accion</th>
                  </tr>
                </thead>
                <tbody id="tbody-floor-plan-versions">
                  <tr><td colspan="5" class="text-center text-muted">Sin registros.</td></tr>
                </tbody>
              </table>
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
