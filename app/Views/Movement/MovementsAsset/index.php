<!DOCTYPE html>
<html lang="es">

<?php require('Layouts/head.php'); ?>

<body>

  <?php require('Layouts/sidebar.php'); ?>

  <main class="content">

    <?php require('Layouts/header.php'); ?>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4">
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
            <li class="breadcrumb-item">Movimientos</li>
            <li class="breadcrumb-item active" aria-current="page">Movimientos de activos</li>
          </ol>
        </nav>
        <h2 class="h4">Movimientos de activos</h2>
        <p class="mb-0">Consulta la situación vigente de cada activo y registra sus cambios.</p>
      </div>
      <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" id="btnNew" class="btn btn-sm btn-gray-800 d-inline-flex align-items-center">
          <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Nuevo
        </button>

      </div>
    </div>
    <div class="card card-body border-0 shadow table-wrapper table-responsive">
      <div class="row g-3 mb-3">
        <div class="col-md-3"><label for="movement-situation-filter" class="form-label">Situación</label><select id="movement-situation-filter" class="form-select"><option value="">Todas</option><option>Ingresado</option><option>Asignado</option><option>Transferido</option><option>Prestado</option><option>Devuelto</option></select></div>
        <div class="col-md-3"><label for="movement-location-filter" class="form-label">Ubicación</label><select id="movement-location-filter" class="form-select"><option value="">Todas</option></select></div>
        <div class="col-md-3"><label for="movement-responsible-filter" class="form-label">Responsable</label><select id="movement-responsible-filter" class="form-select"><option value="">Todos</option></select></div>
        <div class="col-md-3"><label for="movement-return-filter" class="form-label">Retorno</label><select id="movement-return-filter" class="form-select"><option value="">Todos</option><option value="loan">Préstamos</option><option value="overdue">Vencidos</option></select></div>
      </div>
      <table id="tableManageMovementsAsset" class="table table-hover w-100">
        <thead><tr><th>#</th><th>Activo</th><th>Situación</th><th>Ubicación</th><th>Responsable</th><th>Último movimiento</th><th>Retorno</th><th>Acciones</th></tr></thead><tbody></tbody>
      </table>
    </div>

    <div id="movement-access" hidden data-create="<?= !empty($_SESSION['module_permissions']['w_write_permission']) && submenu_action_permission_allowed(MODULE_MOVEMENT, 'Movimiento', 'Activos', 'Crear') ? '1' : '0'; ?>" data-edit="<?= !empty($_SESSION['module_permissions']['u_update_permission']) && submenu_action_permission_allowed(MODULE_MOVEMENT, 'Movimiento', 'Activos', 'Editar') ? '1' : '0'; ?>" data-annul="<?= !empty($_SESSION['module_permissions']['d_delete_permission']) && submenu_action_permission_allowed(MODULE_MOVEMENT, 'Movimiento', 'Activos', 'Eliminar') ? '1' : '0'; ?>"></div>
    <div class="modal fade ui-options-modal" id="ModalMovementActions" tabindex="-1" aria-labelledby="movementActionsTitle" aria-hidden="true"><div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg"><div class="modal-content"><div class="modal-header"><div><h5 id="movementActionsTitle" class="modal-title">Más opciones</h5><p id="movementActionsContext" class="small text-muted mb-0"></p></div><button class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div><div class="modal-body ui-options-list" id="movement-actions-list"></div><div class="modal-footer"><button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button></div></div></div></div>
    <?php require('see.php'); ?>
    <?php require('create-or-edit.php'); ?>
    <?php require('search-asset.php'); ?>
    <?php require('search-space.php'); ?>

    <?php require('Layouts/footer.php'); ?>

  </main>

  <?php require('Layouts/script.php'); ?>

</body>

</html>
