<!DOCTYPE html>
<html lang="en">

<?php require('Layouts/head.php'); ?>

<body>

  <?php require('Layouts/sidebar.php'); ?>

  <link rel="stylesheet" href="<?= assets(); ?>/css/components/inventory-networksegmentations-index.css?v=<?= filemtime(dirname(__DIR__, 3) . '/Assets/css/components/inventory-networksegmentations-index.css'); ?>">

  <main class="content">

    <?php require('Layouts/header.php'); ?>

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
            <li class="breadcrumb-item">Redes</li>
            <li class="breadcrumb-item active" aria-current="page">Segmentacion de Red</li>
          </ol>
        </nav>
        <h2 class="h4">Segmentacion de Red</h2>
        <p class="mb-0">Administra segmentos, rangos y direcciones IP institucionales.</p>
      </div>
      <div class="btn-toolbar mb-2 mb-md-0 page-action-toolbar">
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
      <table id="tableManageNetworkSegmentations" class="table table-hover">
        <thead>
          <tr>
            <th class="border-gray-200">#</th>
            <th class="border-gray-200">Nombre</th>
            <th class="border-gray-200">Red Base</th>
            <th class="border-gray-200">Mascara</th>
            <th class="border-gray-200">Gateway</th>
            <th class="border-gray-200">Rango Inicio</th>
            <th class="border-gray-200">Direcciones IP</th>
            <th class="border-gray-200">Estado</th>
            <th class="border-gray-200">Accion</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>

    <?php require('create-or-edit.php'); ?>
    <?php require('see.php'); ?>
    <?php require('ips.php'); ?>

    <?php require('Layouts/footer.php'); ?>

  </main>

  <?php require('Layouts/script.php'); ?>

</body>

</html>
