<!DOCTYPE html>
<html lang="es">

<?php require('Layouts/head.php'); ?>

<body>

  <?php require('Layouts/sidebar.php'); ?>

  <main class="content">

    <?php require('Layouts/header.php'); ?>

    <?php
      $filters = $data['filters'] ?? ['types' => [], 'priorities' => []];
    ?>

    <link rel="stylesheet" href="<?= assets(); ?>/css/components/helpdesk-incidents-reports.css?v=<?= filemtime(dirname(__DIR__, 3) . '/Assets/css/components/helpdesk-incidents-reports.css'); ?>">

    <div class="incident-report-page">
      <section class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4 page-heading-actions">
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
              <li class="breadcrumb-item">Mesa de ayuda</li>
              <li class="breadcrumb-item active" aria-current="page">Reportes</li>
            </ol>
          </nav>
          <h1 class="h4">Reportes de incidencias</h1>
          <p class="mb-0">Genere reportes PDF filtrados para supervisión, seguimiento técnico y archivo documental.</p>
        </div>
      </section>

      <div class="row g-4">
        <div class="col-12">
          <form class="incident-report-card" method="GET" target="_blank" action="<?= base_url(); ?>/help_desk/incidents/print_report">
            <div class="row g-3">
              <div class="col-12 col-md-6">
                <label for="date-start" class="form-label">Fecha inicio de incidencia</label>
                <input type="date" class="form-control" id="date-start" name="date_start">
              </div>
              <div class="col-12 col-md-6">
                <label for="date-end" class="form-label">Fecha fin de incidencia</label>
                <input type="date" class="form-control" id="date-end" name="date_end">
              </div>
              <div class="col-12 col-md-4">
                <label for="status" class="form-label">Estado</label>
                <select class="form-select" id="status" name="status">
                  <option value="all">Todas</option>
                  <option value="open">Abiertas</option>
                  <option value="closed">Cerradas</option>
                </select>
              </div>
              <div class="col-12 col-md-4">
                <label for="id-type-incident" class="form-label">Tipo de incidencia</label>
                <select class="form-select" id="id-type-incident" name="id_type_incident">
                  <option value="0">Todos los tipos</option>
                  <?php foreach ($filters['types'] as $type) { ?>
                    <option value="<?= (int)$type['id_type_incident']; ?>"><?= htmlspecialchars($type['name_type_incident'], ENT_QUOTES, 'UTF-8'); ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="col-12 col-md-4">
                <label for="id-priority" class="form-label">Prioridad</label>
                <select class="form-select" id="id-priority" name="id_priority">
                  <option value="0">Todas las prioridades</option>
                  <?php foreach ($filters['priorities'] as $priority) { ?>
                    <option value="<?= (int)$priority['id_priority']; ?>"><?= htmlspecialchars($priority['name_priority'], ENT_QUOTES, 'UTF-8'); ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="col-12 col-md-8">
                <label for="asset-code" class="form-label">Código de inventario</label>
                <input type="text" class="form-control" id="asset-code" name="asset_code" placeholder="Ejemplo: INV-2026">
              </div>
              <div class="col-12 col-md-4">
                <label for="limit" class="form-label">Límite de registros</label>
                <select class="form-select" id="limit" name="limit">
                  <option value="25">25 registros</option>
                  <option value="50" selected>50 registros</option>
                  <option value="100">100 registros</option>
                  <option value="150">150 registros</option>
                </select>
              </div>
            </div>

            <div class="incident-report-actions mt-4">
              <button type="reset" class="btn btn-outline-gray-600">Limpiar filtros</button>
              <button type="submit" class="btn btn-gray-800 d-inline-flex align-items-center">
                <i class="bx bx-file me-2"></i>
                Generar PDF
              </button>
            </div>
          </form>
        </div>

      </div>
    </div>

    <?php require('Layouts/footer.php'); ?>

  </main>

  <?php require('Layouts/script.php'); ?>

</body>

</html>
