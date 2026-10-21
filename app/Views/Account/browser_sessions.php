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
                <svg class="icon icon-xxs" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
              </span>
            </li>
            <li class="breadcrumb-item">Mi Cuenta</li>
            <li class="breadcrumb-item active" aria-current="page">Sesiones del Navegador</li>
          </ol>
        </nav>
        <h2 class="h4">Sesiones del Navegador</h2>
        <p class="mb-0">Revise y administre sus sesiones activas en otros navegadores o dispositivos.</p>
      </div>
      <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" id="btnCloseOtherSessions" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center">
          <i class="bx bx-log-out-circle me-1"></i>
          Cerrar Otras Sesiones
        </button>
      </div>
    </div>

    <link rel="stylesheet" href="<?= assets(); ?>/css/components/account-browser_sessions.css?v=<?= filemtime(dirname(__DIR__, 2) . '/Assets/css/components/account-browser_sessions.css'); ?>">

    <section class="current-session-card" aria-labelledby="current-session-title">
      <div class="current-session-header">
        <div class="current-session-title">
          <div class="current-session-icon"><i class="bx bx-desktop"></i></div>
          <div>
            <h3 id="current-session-title">Sesion actual</h3>
            <p class="mb-0">Registro activo del navegador o dispositivo que esta utilizando.</p>
          </div>
        </div>
        <span class="badge bg-success" id="current-session-status">Activa</span>
      </div>
      <div class="current-session-grid">
        <div class="current-session-item">
          <span>Sesion</span>
          <strong id="current-session-id">Cargando...</strong>
        </div>
        <div class="current-session-item">
          <span>IP</span>
          <strong id="current-session-ip">Cargando...</strong>
        </div>
        <div class="current-session-item">
          <span>Ultima actividad</span>
          <strong id="current-session-activity">Cargando...</strong>
        </div>
        <div class="current-session-item">
          <span>Navegador / dispositivo</span>
          <strong id="current-session-agent">Cargando...</strong>
        </div>
      </div>
    </section>

    <div class="card card-body border-0 shadow table-wrapper table-responsive">
      <table id="tableBrowserSessions" class="table table-hover">
        <thead>
          <tr>
            <th class="border-gray-200">#</th>
            <th class="border-gray-200">Sesion</th>
            <th class="border-gray-200">IP</th>
            <th class="border-gray-200">Navegador / Dispositivo</th>
            <th class="border-gray-200">Ultima Actividad</th>
            <th class="border-gray-200">Estado</th>
            <th class="border-gray-200">Accion</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
    <?php require('Layouts/footer.php'); ?>

  </main>

  <?php require('Layouts/script.php'); ?>

</body>

</html>
