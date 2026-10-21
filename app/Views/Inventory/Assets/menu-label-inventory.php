<div class="modal fade" id="ModalAssetLabels" tabindex="-1" aria-labelledby="assetLabelsTitle" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="assetLabelsTitle">Generar etiquetas</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <p class="labels-intro">Selecciona los activos y elige una plantilla para generar el PDF.</p>
        <section class="labels-section" aria-labelledby="labelsFiltersHeading">
          <h5 id="labelsFiltersHeading" class="labels-section-title">Filtrar activos</h5>
        <div class="row g-3">
          <div class="col-md-4"><label class="form-label" for="label-status-filter">Etiqueta</label><select class="form-select" id="label-status-filter"><option value="pending">Pendientes</option><option value="all">Todas</option><option value="attached">Colocadas</option></select></div>
          <div class="col-md-4"><label class="form-label" for="label-category-filter">Categoría</label><select class="form-select" id="label-category-filter"><option value="">Todas</option></select></div>
          <div class="col-md-4"><label class="form-label" for="label-type-filter">Tipo</label><select class="form-select" id="label-type-filter"><option value="">Todos</option></select></div>
        </div>
        </section>
        <section class="labels-section labels-assets" aria-labelledby="labelsAssetsHeading">
          <div class="labels-section-header">
            <h5 id="labelsAssetsHeading" class="labels-section-title">Seleccionar activos</h5>
            <span id="label-selection-count" class="labels-selection-count" role="status">0 seleccionados</span>
          </div>
        <div class="labels-selection-actions">
          <button type="button" class="btn btn-sm btn-outline-primary" id="label-select-filtered">Seleccionar resultados</button>
          <button type="button" class="btn btn-sm btn-secondary" id="label-clear-selection">Limpiar selección</button>
        </div>
        <div class="table-responsive"><table id="tableAssetLabels" class="table table-hover w-100">
          <thead><tr><th>#</th><th>Elegir</th><th>Código</th><th>Tipo</th><th>Marca</th><th>Modelo</th><th>Serie</th><th>Categoría</th><th>Etiqueta</th></tr></thead><tbody></tbody>
        </table></div>
        <p class="labels-help">La selección se conserva al buscar y cambiar de página.</p>
        </section>
        <section class="labels-section labels-template" aria-labelledby="labelsTemplateHeading">
          <h5 id="labelsTemplateHeading" class="labels-section-title">Elegir plantilla</h5>
          <div class="labels-template-layout">
            <div class="labels-template-field">
              <label for="select-label-template-selection" class="form-label">Plantilla de etiqueta</label>
              <select class="form-select js-label-template-select" id="select-label-template-selection"><option value="">Seleccione una plantilla registrada</option></select>
              <p class="labels-help">Confirma la colocación desde el inventario.</p>
            </div>
            <div class="labels-template-field">
              <div class="form-label" id="labelsPreviewHeading">Vista previa</div>
              <div class="asset-label-preview" id="label-template-preview-selection" role="region" aria-labelledby="labelsPreviewHeading"></div>
            </div>
          </div>
        </section>
        <form id="formSelectedAssetLabels" method="post" target="_blank" action="<?= base_url(); ?>/inventory/assets/generate_selected_labels">
          <div id="label-selected-inputs"></div><input type="hidden" name="id_template" id="label-selected-template"><input type="hidden" name="pending_only" id="label-selected-pending">
        </form>
      </div>
      <div class="modal-footer labels-footer">
        <div class="labels-footer-note">Hasta 500 etiquetas por PDF.<br><span>«Pendientes» usa los resultados filtrados.</span></div>
        <div class="labels-footer-actions">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-outline-primary" id="label-generate-pending" title="Generar todas las pendientes de los resultados filtrados">Generar pendientes</button>
        <button type="button" class="btn btn-primary" id="label-generate-selected" disabled>Generar seleccionadas</button>
        </div>
      </div>
    </div>
  </div>
</div>
<input type="hidden" id="asset-label-token" value="<?= htmlspecialchars($_SESSION['asset_label_token'], ENT_QUOTES, 'UTF-8'); ?>">
<?php require('labels-qr.php'); ?>
