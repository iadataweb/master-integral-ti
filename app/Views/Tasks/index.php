<!DOCTYPE html>
<html lang="es">
<?php require('Layouts/head.php'); ?>
<body>
<?php require('Layouts/sidebar.php'); ?>
<main class="content">
<?php require('Layouts/header.php'); ?>
<?php $h = static fn($value) => htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); ?>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4 page-heading-actions">
      <div class="d-block mb-4 mb-md-0">
        <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
          <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
            <li class="breadcrumb-item">
              <span>
                <svg class="icon icon-xxs" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                  xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                  </path>
                </svg>
              </span>
            </li>
            
            <li class="breadcrumb-item active" aria-current="page">Tareas</li>
          </ol>
        </nav>
        <h2 class="h4">Tareas</h2>
        <p class="mb-0">Registra encargos y da seguimiento al trabajo pendiente.</p>
      </div>
      <div class="btn-toolbar mb-2 mb-md-0 page-action-toolbar">
        <?php if (!empty($_SESSION['module_permissions']['w_write_permission'])): ?>
        <button type="button" id="new-task" class="btn btn-sm btn-gray-800 d-inline-flex align-items-center">
          <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Nuevo
        </button>
        <?php endif; ?>

      </div>
    </div>
<div class="card card-body border-0 shadow table-wrapper table-responsive">
 <div class="row g-3 align-items-end mb-3">
  <div class="col-12 col-md-4"><label for="task-filter-status" class="form-label">Estado</label><select id="task-filter-status" class="form-select"><option value="">Todos</option><?php foreach (['Pendiente','En progreso','Completada','Cancelada'] as $value): ?><option><?= $value; ?></option><?php endforeach; ?><option value="overdue">Vencidas</option></select></div>
  <div class="col-12 col-md-4"><label for="task-filter-priority" class="form-label">Prioridad</label><select id="task-filter-priority" class="form-select"><option value="">Todas</option><option>Baja</option><option>Media</option><option>Alta</option></select></div>
 </div>
<table id="tableTasks" class="table table-hover w-100"><thead><tr><th>#</th><th>Tarea</th><th>Solicitante</th><th>Prioridad</th><th>Fecha límite</th><th>Estado</th><th>Acción</th></tr></thead><tbody>
</tbody></table></div>
<?php require('Layouts/footer.php'); ?>
</main>
<div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalTitle" aria-hidden="true" data-bs-backdrop="static"><div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"><div class="modal-content">
<div class="modal-header"><h2 class="modal-title h4" id="taskModalTitle">Nueva tarea</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
<div class="modal-body"><form id="taskForm"><input type="hidden" name="id_task" value="0"><fieldset id="taskFields"><div class="row g-3">
<?php foreach (['title'=>['Tarea','Ej.: Preparar equipos para capacitación',180,12], 'requester'=>['Solicitante','Persona o área que solicita el trabajo',150,12]] as $key=>$field): ?>
<div class="col-12 col-md-<?= $field[3]; ?>"><label for="task-<?= $key; ?>" class="form-label"><?= $field[0]; ?></label><input id="task-<?= $key; ?>" name="<?= $key; ?>" class="form-control" placeholder="<?= $field[1]; ?>" maxlength="<?= $field[2]; ?>" required></div>
<?php endforeach; ?>
<div class="col-12 col-md-4"><label for="task-priority" class="form-label">Prioridad</label><select id="task-priority" name="priority" class="form-select"><option>Baja</option><option selected>Media</option><option>Alta</option></select></div>
<div class="col-12 col-md-4"><label for="task-status" class="form-label">Estado</label><select id="task-status" name="status" class="form-select"><option>Pendiente</option><option>En progreso</option><option>Completada</option><option>Cancelada</option></select></div>
<div class="col-12 col-md-4"><label for="task-due" class="form-label">Fecha límite</label><input id="task-due" type="date" name="due_date" class="form-control"></div>
<div class="col-12"><label for="task-description" class="form-label">Descripción</label><textarea id="task-description" name="description" class="form-control" rows="3" maxlength="10000" placeholder="Describe el trabajo solicitado"></textarea></div>
<div class="col-12"><label for="task-result" class="form-label">Avance o resultado</label><textarea id="task-result" name="result" class="form-control" rows="3" maxlength="10000" placeholder="Registra el avance o el trabajo realizado"></textarea></div>
</div></fieldset></form><p id="task-created" class="text-muted small mt-3 mb-0" hidden></p></div>
<div class="modal-footer"><button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button><button type="submit" form="taskForm" id="task-save" class="btn btn-secondary">Guardar</button></div>
</div></div></div>
<?php require('Layouts/script.php'); ?>
</body></html>
