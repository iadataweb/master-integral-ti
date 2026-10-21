<link rel="stylesheet" href="<?= assets(); ?>/css/components/inventory-assets-asset-confidential-notes.css?v=<?= filemtime(dirname(__DIR__, 3) . '/Assets/css/components/inventory-assets-asset-confidential-notes.css'); ?>">

<div class="modal fade" id="ModalAssetConfidentialNotes" tabindex="-1" aria-labelledby="labelAssetConfidentialNotes" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg"><div class="modal-content">
  <div class="modal-header"><h4 class="modal-title" id="labelAssetConfidentialNotes">Notas privadas</h4><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
  <div class="modal-body">
   <section class="asset-panel mb-3">
    <h6 class="asset-panel-title">Activo seleccionado</h6>
    <div class="asset-panel-body row g-3">
     <div class="col-12 col-md-6"><small class="confidential-note-muted d-block">Código de inventario</small><strong id="asset-confidential-note-code"></strong></div>
     <div class="col-12 col-md-6"><small class="confidential-note-muted d-block">Tipo de activo</small><strong id="asset-confidential-note-type"></strong></div>
    </div>
   </section>
   <section class="asset-section">
    <h6 class="asset-section-title">Nota privada</h6>
    <p class="text-muted small">Esta nota es privada y no se incluye en la consulta rápida mediante QR.</p>
    <div id="asset-confidential-note-content" class="confidential-note-content" role="status">Cargando…</div>
    <div id="asset-confidential-note-date" class="confidential-note-muted mt-3"></div>
   </section>
  </div>
  <div class="modal-footer">
   <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
   <button type="button" id="btn-delete-confidential-note" class="btn btn-danger d-none">Eliminar</button>
   <button type="button" id="btn-edit-confidential-note" class="btn btn-primary d-none">Editar</button>
   <button type="button" id="btn-new-confidential-note" class="btn btn-primary d-none">Nuevo</button>
  </div>
 </div></div>
</div>

<div class="modal fade" id="ModalAssetConfidentialNoteEditor" tabindex="-1" aria-labelledby="labelConfidentialNoteEditor" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg"><div class="modal-content">
  <div class="modal-header"><h4 class="modal-title" id="labelConfidentialNoteEditor">Nueva nota privada</h4><button type="button" class="btn-close js-cancel-confidential-note" aria-label="Cancelar"></button></div>
  <form id="formAssetConfidentialNotes">
   <div class="modal-body">
    <p id="confidential-note-editor-context" class="text-muted"></p>
    <input type="hidden" id="input-id-asset-confidential-note" name="id_asset">
    <input type="hidden" id="input-confidential-note-operation" name="operation">
    <label for="textarea-asset-confidential-note" class="form-label">Nota privada</label>
    <textarea class="form-control" id="textarea-asset-confidential-note" name="confidential_note_asset" rows="8" maxlength="5000" required placeholder="Escribe la información privada del activo."></textarea>
    <small class="text-muted">Máximo 5000 caracteres. Para quitar la nota utiliza Eliminar desde la consulta.</small>
   </div>
   <div class="modal-footer"><button type="button" class="btn btn-primary js-cancel-confidential-note">Cancelar</button><button type="submit" id="btn-save-confidential-note" class="btn btn-primary">Guardar</button></div>
  </form>
 </div></div>
</div>
