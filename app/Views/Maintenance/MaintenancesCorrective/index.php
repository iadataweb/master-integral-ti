<!DOCTYPE html>
<html lang="en">

<?php require('Layouts/head.php'); ?>

<body>

  <?php require('Layouts/sidebar.php'); ?>

  <main class="content">

    <?php require('Layouts/header.php'); ?>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4 page-heading-actions">
      <div class="d-block mb-4 mb-md-0">
        <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
          <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
            <li class="breadcrumb-item">
              <span>
                <svg class="icon icon-xxs" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
              </span>
            </li>
            <li class="breadcrumb-item">Mantenimiento</li>
            <li class="breadcrumb-item active" aria-current="page">Mantenimiento Correctivo</li>
          </ol>
        </nav>
        <h2 class="h4">Mantenimiento Correctivo</h2>
        <p class="mb-0">Registra, controla y documenta reparaciones correctivas de activos.</p>
      </div>
      <div class="btn-toolbar mb-2 mb-md-0 page-action-toolbar">
        <button type="button" id="btnReceptionIncidents" class="btn btn-sm btn-outline-gray-600 d-inline-flex align-items-center">
          <i class="bx bx-inbox me-2"></i>
          Recepcion de Incidencias
        </button>
        <button type="button" id="btnNew" class="btn btn-sm btn-gray-800 d-inline-flex align-items-center">
          <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Nuevo
        </button>
      </div>
    </div>

    <div class="card card-body border-0 shadow table-wrapper table-responsive">
      <table id="tableManageMaintenancesCorrective" class="table table-hover">
        <thead>
          <tr>
            <th>#</th>
            <th>Incidencia</th>
            <th>Codigo Inventario</th>
            <th>Tipo Activo</th>
            <th>Categoria Activo</th>
            <th>Origen</th>
            <th>Responsable</th>
            <th>Estado</th>
            <th>Fecha Reparacion</th>
            <th>Repuestos</th>
            <th>Accion</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>

    <input type="hidden" id="preset-id-incident" value="<?= intval($data['preset_id_incident'] ?? 0); ?>">
    <input type="hidden" id="preset-id-asset" value="<?= intval($data['preset_id_asset'] ?? 0); ?>">

    <?php require('see.php'); ?>
    <?php require('create-or-edit.php'); ?>
    <?php require('responsible-company.php'); ?>
    <?php require('search-asset.php'); ?>
    <?php require('search-company.php'); ?>

    <div class="modal fade text-left" id="ModalReceptionIncidents" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Recepcion de Incidencias</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p class="text-muted mb-3">Revise las incidencias derivadas y decida si se aceptan como mantenimiento correctivo o se rechazan.</p>
            <div class="table-responsive">
              <table id="tableReceptionCorrectiveIncidents" class="table table-hover w-100">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Incidencia</th>
                    <th>Activo</th>
                    <th>Prioridad</th>
                    <th>Ubicacion</th>
                    <th>Fecha Derivacion</th>
                    <th>Accion</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>

    <?php require('Layouts/footer.php'); ?>

  </main>

  <?php require('Layouts/script.php'); ?>

</body>

</html>
