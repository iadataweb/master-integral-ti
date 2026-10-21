<?php
$incident = $data['specific_incident'];
$escape = static fn($value) => htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
$isOpen = $data['incident_lifecycle']['is_open'];
$canWrite = !empty($data['incident_action_permissions']['can_register']);
$canDelete = !empty($data['incident_action_permissions']['can_delete']);
$derivation = $incident['corrective_derivation_status_incident'] ?? 'none';
$derivationLabels = ['none'=>'Sin derivación','pending'=>'Derivación pendiente','accepted'=>'Derivación aceptada','rejected'=>'Derivación rechazada','cancelled'=>'Derivación cancelada'];
?>
 <?php if (!$isOpen) { ?><div class="alert alert-secondary" role="status">Incidencia cerrada. Sus detalles son de solo lectura; debes reabrirla para realizar cambios.</div><?php } ?>
 <div class="card card-body border-0 shadow mb-4">
  <div class="card">
   <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h4 class="card-title m-0">Información de la incidencia Nro. <?= (int)$incident['id_incident'] ?></h4>
    <?= $incident['status_open_incident'] ?>
   </div>
   <div class="card-content"><div class="card-body">
    <div class="row">
     <div class="col-12 mb-3"><label for="input-title" class="form-label">Título</label><input id="input-title" class="form-control" value="<?= $escape($incident['title_incident']) ?>" disabled readonly></div>
     <?php foreach ([['Tipo',$incident['name_type_incident'] ?? 'Sin tipo'],['Prioridad',$incident['name_priority']],['Ubicación',$incident['display_location']],['Fecha de incidencia',$incident['date_incident']],['Hora de incidencia',$incident['time_incident']],['Fecha de registro',$incident['date_created_incident']]] as $index=>$field) { ?>
     <div class="col-12 col-md-6 mb-3"><label for="incident-information-<?= $index ?>" class="form-label"><?= $field[0] ?></label><input id="incident-information-<?= $index ?>" class="form-control" value="<?= $escape($field[1]) ?>" disabled readonly></div>
     <?php } ?>
     <div class="col-12 mb-3"><label for="textarea-description" class="form-label">Descripción</label><textarea id="textarea-description" class="form-control" rows="6" disabled readonly><?= $escape($incident['description_incident']) ?></textarea></div>
    </div>
    <?php if (!empty($data['attachments_incident'])) { ?>
    <label class="form-label">Documentos adicionales</label><div class="table-responsive"><table class="table mb-0">
     <thead><tr><th>#</th><th>Archivo</th><th>Acción</th></tr></thead><tbody>
     <?php foreach ($data['attachments_incident'] as $attachment) { ?>
     <tr><td><?= (int)$attachment['id_attachment_incident'] ?></td><td><?= $escape($attachment['name_attachment_incident']) ?></td><td><a class="btn btn-primary btn-sm" href="<?= $escape(base_url().'/'.$attachment['route_attachment_incident']) ?>" target="_blank" rel="noopener" aria-label="Ver archivo" title="Ver archivo"><svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></a></td></tr>
     <?php } ?></tbody></table></div><?php } ?>
   </div></div>
  </div>
 </div>
 <div id="incident-affected-asset" class="card card-body border-0 shadow mb-4"><div class="card">
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
   <h4 class="card-title m-0">Activo afectado</h4>
   <div class="d-flex flex-wrap gap-2">
   <?php if ($canWrite) { ?><button type="button" class="btn btn-primary btn-sm js-btn-search-asset">Relacionar</button><?php } ?>
   <?php if ($canDelete && !empty($incident['id_asset_incident'])) { ?><button type="button" class="btn btn-outline-secondary btn-sm" id="btn-limpiar-asset">Quitar</button><?php } ?>
   </div>
  </div>
  <div class="card-content"><div class="card-body">
   <div class="row">
    <?php foreach ([['Código de inventario','affected_code_inventory_asset'],['Categoría de activo','affected_name_category_asset'],['Tipo de activo','affected_name_type_asset'],['Marca','affected_name_brand'],['Modelo','affected_model_asset'],['Número de serie','affected_serial_number_asset']] as $index=>$field) { ?>
    <div class="col-12 col-md-6 mb-3"><label for="incident-asset-<?= $index ?>" class="form-label"><?= $field[0] ?></label><input id="incident-asset-<?= $index ?>" class="form-control" disabled readonly value="<?= $escape($incident[$field[1]] ?? '') ?>"></div>
    <?php } ?>
    <?php foreach (['affected_descriptions_asset'=>'Descripción','affected_observations_asset'=>'Observación'] as $key=>$label) { ?>
    <div class="col-12 mb-3"><label for="<?= $key ?>" class="form-label"><?= $label ?></label><textarea id="<?= $key ?>" class="form-control" rows="3" disabled readonly><?= $escape($incident[$key] ?? '') ?></textarea></div>
    <?php } ?>
   </div>
  <?php if (!empty($incident['id_asset_incident'])) { ?>
   <p class="small mb-2"><?= $escape($derivationLabels[$derivation] ?? $derivation) ?></p>
   <div class="d-flex flex-wrap gap-2">
   <?php if (!empty($incident['id_maintenance_corrective_incident'])) { ?>
    <a class="btn btn-outline-primary btn-sm" href="<?= base_url() ?>/maintenance/maintenances_corrective/manage_maintenances_corrective">Correctivo #<?= (int)$incident['id_maintenance_corrective_incident'] ?></a>
   <?php } elseif ($canWrite && in_array($derivation, ['none','rejected','cancelled'], true)) { ?>
    <button type="button" class="btn btn-outline-primary btn-sm js-btn-derive-corrective">Derivar a correctivo</button>
   <?php } elseif ($isOpen && !empty($_SESSION['module_permissions']['u_update_permission']) && $derivation === 'pending') { ?>
    <button type="button" class="btn btn-outline-secondary btn-sm js-btn-cancel-corrective-derivation">Cancelar derivación</button>
   <?php } ?></div>
  <?php } else { ?><p class="text-muted mb-0">Sin activo relacionado. La incidencia puede corresponder a un servicio, una aplicación o una red.</p><?php } ?>
 </div></div></div></div>
 <?php foreach (['diagnostic'=>'Diagnóstico','solution'=>'Solución'] as $field=>$label) { ?>
 <div class="card card-body border-0 shadow mb-4"><div class="card">
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
   <h4 class="card-title m-0"><?= $label ?></h4>
   <div class="d-flex gap-2">
    <?php if ($canWrite) { ?><button type="button" class="btn btn-primary btn-sm js-btn-add-<?= $field ?>"><i class="bx bx-link-alt"></i> Registrar</button><?php } ?>
    <?php if ($canDelete) { ?><button type="button" class="btn btn-danger btn-sm js-btn-clear-incident-field" data-field="<?= $field ?>"><i class="bx bx-trash"></i> Limpiar</button><?php } ?>
   </div>
  </div>
  <div class="card-content"><div class="card-body">
   <label class="form-label" for="view-textarea-<?= $field ?>-incident"><?= $label ?></label>
   <textarea id="view-textarea-<?= $field ?>-incident" class="form-control" rows="6" disabled readonly><?= $escape($incident['action_'.$field] ?? '') ?></textarea>
  </div></div>
 </div></div>
 <?php } ?>
 <div class="card card-body border-0 shadow mb-4"><div class="card">
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
   <h4 class="card-title m-0">Cierre de incidencia</h4>
   <div class="d-flex gap-2">
    <?php if ($canWrite) { ?><button type="button" class="btn btn-primary btn-sm js-btn-register-resolution"><i class="bx bx-link-alt"></i> Registrar</button><?php } ?>
    <?php if ($canDelete) { ?><button type="button" class="btn btn-danger btn-sm js-btn-clear-incident-field" data-field="resolution"><i class="bx bx-trash"></i> Limpiar</button><?php } ?>
   </div>
  </div>
  <div class="card-content"><div class="card-body">
   <p class="text-muted">Registra una solución o justificación antes de cerrar. La fecha y hora reales de resolución son opcionales.</p>
   <div class="row">
    <div class="col-12 col-md-6 mb-3"><label for="view-date-closed-incident" class="form-label">Fecha de resolución</label><input type="date" id="view-date-closed-incident" class="form-control" value="<?= $escape($incident['date_closed_incident'] ?? '') ?>" disabled readonly></div>
    <div class="col-12 col-md-6 mb-3"><label for="view-time-closed-incident" class="form-label">Hora de resolución</label><input type="time" id="view-time-closed-incident" class="form-control" value="<?= $escape(substr($incident['time_closed_incident'] ?? '',0,5)) ?>" disabled readonly></div>
   </div>
  </div></div>
 </div></div>
 <?php require('lifecycle.php'); ?>
 <?php require('events.php'); ?>
 <?php require('search-asset.php'); ?>
 <?php require('diagnostic.php'); ?>
 <?php require('solution.php'); ?>
 <?php require('resolution.php'); ?>
