<!DOCTYPE html>
<html lang="en">

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
            <li class="breadcrumb-item">Administracion</li>
            <li class="breadcrumb-item">Parametros</li>
            <li class="breadcrumb-item active" aria-current="page">Ambientes</li>
          </ol>
        </nav>
        <h2 class="h4">Ambientes</h2>
        <p class="mb-0">Administra los ambientes fisicos disponibles para incidencias, activos y movimientos.</p>
      </div>
      <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" id="btnNew" class="btn btn-sm btn-gray-800 d-inline-flex align-items-center">
          <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Nuevo
        </button>
        <div class="btn-group ms-2 ms-lg-3">
          <button type="button" class="btn btn-sm btn-outline-gray-600 js-btn-trash" data-trash-module="administration/environments">Papelera</button>
        </div>
      </div>
    </div>

    <div class="card card-body border-0 shadow table-wrapper table-responsive">
      <table id="tableManageEnvironments" class="table table-hover">
        <thead>
          <tr>
            <th class="border-gray-200">#</th>
            <th class="border-gray-200">Establecimiento</th>
            <th class="border-gray-200">Ambiente</th>
            <th class="border-gray-200">Piso</th>
            <th class="border-gray-200">Estado</th>
            <th class="border-gray-200">Accion</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>

    <?php require('see.php'); ?>
    <?php require('create-or-edit.php'); ?>

    <?php require('Layouts/footer.php'); ?>

  </main>

  <?php require('Layouts/script.php'); ?>

</body>

</html>
