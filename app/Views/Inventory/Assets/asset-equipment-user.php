<link rel="stylesheet" href="<?= assets(); ?>/css/components/inventory-assets-asset-equipment-user.css?v=<?= filemtime(dirname(__DIR__, 3) . '/Assets/css/components/inventory-assets-asset-equipment-user.css'); ?>">
<div class="modal fade" id="ModalAssetEquipmentUser" tabindex="-1" aria-labelledby="labelAssetEquipmentUser" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg"><div class="modal-content">
  <div class="modal-header"><h4 class="modal-title" id="labelAssetEquipmentUser">Usuario del equipo</h4><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
  <div class="modal-body">
   <section class="asset-panel mb-3"><h6 class="asset-panel-title">Activo seleccionado</h6><div class="asset-panel-body"><div class="row g-3">
    <div class="col-12 col-md-6"><small class="equipment-user-muted d-block">Código de inventario</small><strong id="asset-equipment-user-code"></strong></div>
    <div class="col-12 col-md-6"><small class="equipment-user-muted d-block">Tipo de activo</small><strong id="asset-equipment-user-type"></strong></div>
   </div></div></section>
   <section class="asset-section"><h6 class="asset-section-title">Usuario del equipo</h6>
    <p id="current-asset-equipment-user" class="fw-semibold mb-3" role="status">Cargando…</p>
    <div id="asset-equipment-user-details" class="d-none">
     <div id="current-asset-equipment-user-date" class="equipment-user-muted"></div>
     <div id="asset-equipment-user-updated-by" class="equipment-user-muted"></div>
    </div>
   </section>
  </div>
  <div class="modal-footer"><button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button><button type="button" id="btn-new-equipment-user" class="btn btn-primary d-none">Nuevo</button><button type="button" id="btn-edit-equipment-user" class="btn btn-primary d-none">Editar</button></div>
 </div></div>
</div>
<div class="modal fade" id="ModalEquipmentUserEditor" tabindex="-1" aria-labelledby="labelEquipmentUserEditor" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg"><div class="modal-content">
  <div class="modal-header"><h4 class="modal-title" id="labelEquipmentUserEditor">Nuevo usuario del equipo</h4><button type="button" class="btn-close js-cancel-equipment-user" aria-label="Cancelar"></button></div>
  <form id="formAssetEquipmentUser">
   <div class="modal-body">
    <p id="equipment-user-editor-context" class="text-muted"></p>
    <input type="hidden" id="input-id-asset-equipment-user" name="id_asset"><input type="hidden" id="input-equipment-user-operation" name="operation">
    <div class="mb-3"><label for="input-username-asset-equipment-user" class="form-label">Nombre de usuario</label><input type="text" class="form-control" id="input-username-asset-equipment-user" name="username_asset_equipment_user" maxlength="150" placeholder="Ej. usuario.equipo" required></div>
   </div>
   <div class="modal-footer"><button type="button" class="btn btn-primary js-cancel-equipment-user">Cancelar</button><button type="submit" id="btn-save-equipment-user" class="btn btn-primary">Guardar</button></div>
  </form>
 </div></div>
</div>
