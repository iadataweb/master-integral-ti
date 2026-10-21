<div class="modal fade" id="ModalSoftware" tabindex="-1" aria-labelledby="labelSoftware" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl"><div class="modal-content">
  <div class="modal-header"><h4 class="modal-title" id="labelSoftware">Programas instalados</h4><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
  <div class="modal-body">
   <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3"><p id="software-asset-context" class="text-muted mb-0"></p><button type="button" id="btn-new-asset-software" class="btn btn-primary d-none">Nuevo</button></div>
   <div class="table-responsive"><table class="table mb-0"><thead><tr><th>#</th><th>Programa</th><th>Versión</th><th>Tipo</th><th>Licencia</th><th>Arquitectura</th><th>Acción</th></tr></thead><tbody id="software-list"><tr><td colspan="7" class="text-center text-muted">Cargando…</td></tr></tbody></table></div>
  </div>
  <div class="modal-footer"><button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button></div>
 </div></div>
</div>
<div class="modal fade" id="ModalSoftwareEditor" tabindex="-1" aria-labelledby="labelSoftwareEditor" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg"><div class="modal-content">
  <div class="modal-header"><h4 class="modal-title" id="labelSoftwareEditor">Nuevo programa</h4><button type="button" class="btn-close js-cancel-software" aria-label="Cancelar"></button></div>
  <form id="formSoftware">
   <div class="modal-body">
    <p id="software-editor-context" class="text-muted"></p><input type="hidden" name="id_asset_software" id="software-asset-id"><input type="hidden" name="id_software" id="software-record-id">
    <div class="mb-3"><label for="software-name" class="form-label">Nombre del programa</label><input id="software-name" name="name_software" class="form-control" maxlength="200" required></div>
    <div class="row"><div class="col-md-6 mb-3"><label for="software-version" class="form-label">Versión</label><input id="software-version" name="version_software" class="form-control" maxlength="100"></div><div class="col-md-6 mb-3"><label for="software-type" class="form-label">Tipo de programa</label><select id="software-type" name="id_type_software_software" class="form-select" required></select></div>
    <div class="col-md-6 mb-3"><label for="software-license" class="form-label">Tipo de licencia</label><select id="software-license" name="id_type_license_software" class="form-select" required></select></div><div class="col-md-6 mb-3"><label for="software-architecture" class="form-label">Arquitectura</label><select id="software-architecture" name="id_architecture_software" class="form-select" required></select></div></div>
    <label for="software-description" class="form-label">Descripción</label><textarea id="software-description" name="description_software" class="form-control" rows="3" maxlength="5000"></textarea>
   </div>
   <div class="modal-footer"><button type="button" class="btn btn-primary js-cancel-software">Cancelar</button><button type="submit" id="btn-save-software" class="btn btn-primary">Guardar</button></div>
  </form>
 </div></div>
</div>
<div class="modal fade ui-record-detail" id="ModalSoftwareDetail" tabindex="-1" aria-labelledby="labelSoftwareDetail" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg"><div class="modal-content">
  <div class="modal-header"><h4 class="modal-title" id="labelSoftwareDetail">Detalle del programa</h4><button type="button" class="btn-close js-back-software" aria-label="Cerrar"></button></div>
  <div class="modal-body"><div class="asset-layout">
   <section class="asset-section"><h6 class="asset-section-title">Datos principales</h6><div id="software-detail-fields" class="asset-meta-grid"></div></section>
   <section class="asset-panel"><h6 class="asset-panel-title">Descripción</h6><div id="software-detail-description" class="asset-panel-body asset-detail-text"></div></section>
  </div></div>
  <div class="modal-footer"><button type="button" class="btn btn-primary js-back-software">Volver</button></div>
 </div></div>
</div>
