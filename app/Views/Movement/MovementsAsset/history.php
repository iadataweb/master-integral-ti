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
            <li class="breadcrumb-item active" aria-current="page">Historial de movimientos</li>
          </ol>
        </nav>
        <h2 class="h4">Historial de movimientos</h2>
        <p class="mb-0">Consulta los movimientos de este activo y sus cambios de ubicación y asignación.</p>
      </div>      
    </div>
    <div class="card card-body border-0 shadow table-wrapper table-responsive">
      <p class="mb-3"><strong><?= htmlspecialchars($data['history_asset']['code_inventory_asset'], ENT_QUOTES, 'UTF-8'); ?></strong> <span class="text-muted">· Activo #<?= (int)$data['history_asset']['id_asset']; ?></span></p>
      <table data-asset-id="<?= (int)$data['history_asset']['id_asset']; ?>" id="tableHistoryMovementAsset" class="table table-hover">
        <thead>
          <tr>
            <th class="border-gray-200">#</th>
            <th class="border-gray-200">Código de Inventario</th>
            <th class="border-gray-200">Tipo de Activo</th>
            <th class="border-gray-200">Categoría de Activo</th>
            <th class="border-gray-200">Tipo de Movimiento</th>
            <th class="border-gray-200">Estado</th>
            <th class="border-gray-200">Responsable</th>
            <th class="border-gray-200">Ambiente</th>
            <th class="border-gray-200">Piso</th>
            <th class="border-gray-200">Fecha de Movimiento</th>
            <th class="border-gray-200">Hora de Movimiento</th>
            <th class="border-gray-200">Retorno Esperado</th>
            <th class="border-gray-200">Fecha de Registro</th>
            <th class="border-gray-200">Acción</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>

    <?php require('see.php'); ?>

    <?php require('Layouts/footer.php'); ?>

  </main>

  <?php require('Layouts/script.php'); ?>

</body>

</html>
