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
            <li class="breadcrumb-item active" aria-current="page">Mantenimiento Preventivo</li>
          </ol>
        </nav>
        <h2 class="h4">Mantenimiento Preventivo</h2>
        <p class="mb-0">Programa y controla el mantenimiento preventivo para conservar la operatividad de los activos.</p>
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
          <button type="button" class="btn btn-sm btn-outline-gray-600">Exportar</button>
        </div>
      </div>
    </div>

    <?php
      $preventiveIndicators = $data['preventive_indicators'] ?? [];
      $totalPerformedPreventive = intval($preventiveIndicators['total_performed'] ?? 0);
      $assetsCurrentPreventive = intval($preventiveIndicators['assets_current'] ?? 0);
    ?>

    <section class="row g-3 mb-4 maintenance-status-summary" id="maintenancePreventiveStatusSummary">
      <div class="col-6 col-md-4 col-xl-2">
        <button type="button" class="card card-body border-0 shadow-sm w-100 h-100 text-start js-filter-maintenance-status active" data-status-key="">
          <span class="text-uppercase small fw-bold text-muted">Total</span>
          <strong class="h4 mb-0" data-status-count="all">0</strong>
          <span class="small text-muted">Cronograma activo</span>
        </button>
      </div>
      <div class="col-6 col-md-4 col-xl-2">
        <button type="button" class="card card-body border-0 shadow-sm w-100 h-100 text-start js-filter-maintenance-status" data-status-key="realized">
          <span class="badge bg-success mb-2 align-self-start">Realizados</span>
          <strong class="h4 mb-0"><?= number_format($totalPerformedPreventive); ?></strong>
          <span class="small text-muted">Preventivos completados</span>
        </button>
      </div>
      <div class="col-6 col-md-4 col-xl-2">
        <button type="button" class="card card-body border-0 shadow-sm w-100 h-100 text-start js-filter-maintenance-status" data-filter-column="current" data-status-key="current">
          <span class="badge bg-info text-dark mb-2 align-self-start">Vigentes</span>
          <strong class="h4 mb-0" data-status-count="current"><?= number_format($assetsCurrentPreventive); ?></strong>
          <span class="small text-muted">Activos al dia</span>
        </button>
      </div>
      <div class="col-6 col-md-4 col-xl-2">
        <button type="button" class="card card-body border-0 shadow-sm w-100 h-100 text-start js-filter-maintenance-status" data-status-key="scheduled">
          <span class="badge bg-primary mb-2 align-self-start">Programado</span>
          <strong class="h4 mb-0" data-status-count="scheduled">0</strong>
          <span class="small text-muted">En fecha futura</span>
        </button>
      </div>
      <div class="col-6 col-md-4 col-xl-2">
        <button type="button" class="card card-body border-0 shadow-sm w-100 h-100 text-start js-filter-maintenance-status" data-status-key="expiring">
          <span class="badge bg-warning text-dark mb-2 align-self-start">Proximo</span>
          <strong class="h4 mb-0" data-status-count="expiring">0</strong>
          <span class="small text-muted">Vence en 7 dias</span>
        </button>
      </div>
      <div class="col-6 col-md-4 col-xl-2">
        <button type="button" class="card card-body border-0 shadow-sm w-100 h-100 text-start js-filter-maintenance-status" data-status-key="pending">
          <span class="badge bg-danger mb-2 align-self-start">Pendiente</span>
          <strong class="h4 mb-0" data-status-count="pending">0</strong>
          <span class="small text-muted">Fecha vencida</span>
        </button>
      </div>
      <div class="col-6 col-md-4 col-xl-2">
        <button type="button" class="card card-body border-0 shadow-sm w-100 h-100 text-start js-filter-maintenance-status" data-status-key="realized">
          <span class="badge bg-success mb-2 align-self-start">Realizado</span>
          <strong class="h4 mb-0" data-status-count="realized">0</strong>
          <span class="small text-muted">Ejecutado hoy</span>
        </button>
      </div>
      <div class="col-6 col-md-4 col-xl-2">
        <button type="button" class="card card-body border-0 shadow-sm w-100 h-100 text-start js-filter-maintenance-status" data-status-key="reprogrammed">
          <span class="badge bg-info text-dark mb-2 align-self-start">Reprogramado</span>
          <strong class="h4 mb-0" data-status-count="reprogrammed">0</strong>
          <span class="small text-muted">Fecha ajustada</span>
        </button>
      </div>
      <div class="col-6 col-md-4 col-xl-2">
        <button type="button" class="card card-body border-0 shadow-sm w-100 h-100 text-start js-filter-maintenance-status" data-status-key="cancelled">
          <span class="badge bg-secondary mb-2 align-self-start">Anulado</span>
          <strong class="h4 mb-0" data-status-count="cancelled">0</strong>
          <span class="small text-muted">Fuera de vigencia</span>
        </button>
      </div>
    </section>

    <div class="card card-body border-0 shadow table-wrapper table-responsive">
      <table id="tableManageMaintenancesPreventive" class="table table-hover">
        <thead>
          <tr>
            <th class="border-gray-200">#</th>
            <th class="border-gray-200">Codigo de Inventario</th>
            <th class="border-gray-200">Tipo de Activo</th>
            <th class="border-gray-200">Categoria de Activo</th>
            <th class="border-gray-200">Ambiente/Sector</th>
            <th class="border-gray-200">Piso</th>
            <th class="border-gray-200">Tipo Responsable</th>
            <th class="border-gray-200">Frecuencia</th>
            <th class="border-gray-200">Estado</th>
            <th class="border-gray-200">Fecha Programada</th>
            <th class="border-gray-200">Proximo Mantenimiento</th>
            <th class="border-gray-200">Accion</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>

    <?php require('see.php'); ?>
    <?php require('create-or-edit.php'); ?>
    <?php require('responsible-company.php'); ?>
    <?php require('search-asset.php'); ?>
    <?php require('search-company.php'); ?>
    <?php require('execute.php'); ?>

    <?php require('Layouts/footer.php'); ?>

  </main>

  <?php require('Layouts/script.php'); ?>

</body>

</html>