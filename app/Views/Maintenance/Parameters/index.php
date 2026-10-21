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
            <li class="breadcrumb-item">Mantenimiento</li>
            <li class="breadcrumb-item active" aria-current="page">Parámetros</li>
          </ol>
        </nav>
        <h2 class="h4">Parámetros</h2>
        <p class="mb-0">Configura acciones estándar para el mantenimiento preventivo.</p>
      </div>
    </div>

    <div class="row">
      <div class="col-12 col-sm-6 col-xl-4 mb-4">
        <a href="<?= base_url(); ?>/maintenance/actions_maintenance_preventive/manage_actions_maintenance_preventive" class="custom-btn-parameter">
          <div class="card text-center shadow-sm h-100 border-0">
            <div class="card-body">
              <svg class="icon icon-md" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M10 2a1 1 0 0 1 1 1v1h4a1 1 0 1 1 0 2h-1v2h1a1 1 0 1 1 0 2h-1v2h1a1 1 0 1 1 0 2h-4v1a1 1 0 1 1-2 0v-1H5a1 1 0 1 1 0-2h1v-2H5a1 1 0 1 1 0-2h1V6H5a1 1 0 1 1 0-2h4V3a1 1 0 0 1 1-1Zm2 4H8v2h4V6Zm0 4H8v2h4v-2Z"></path>
              </svg>
              <h6 class="mt-3">Acciones Preventivas</h6>
            </div>
          </div>
        </a>
      </div>
    </div>

    <?php require('Layouts/footer.php'); ?>

  </main>

  <?php require('Layouts/script.php'); ?>

</body>

</html>
