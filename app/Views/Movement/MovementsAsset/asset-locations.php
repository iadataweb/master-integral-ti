<!DOCTYPE html>
<html lang="es">

<?php require('Layouts/head.php'); ?>

<body>
  <?php require('Layouts/sidebar.php'); ?>

  <link rel="stylesheet" href="<?= assets(); ?>/css/components/movement-movementsasset-asset-locations.css?v=<?= filemtime(dirname(__DIR__, 3) . '/Assets/css/components/movement-movementsasset-asset-locations.css'); ?>">

  <link rel="stylesheet" href="<?= assets(); ?>/css/components/inventory-assets-see.css?v=<?= filemtime(dirname(__DIR__, 3) . '/Assets/css/components/inventory-assets-see.css'); ?>">

  <main class="content">
    <?php require('Layouts/header.php'); ?>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4 page-heading-actions">
      <div class="d-block mb-4 mb-md-0">
        <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
          <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
            <li class="breadcrumb-item"><span><i class="bx bx-home"></i></span></li>
            <li class="breadcrumb-item">Movimientos</li>
            <li class="breadcrumb-item active" aria-current="page">Ubicaciones</li>
          </ol>
        </nav>
        <h2 class="h4">Ubicaciones</h2>
        <p class="mb-0">Consulta las ubicaciones en lista o sobre un plano institucional.</p>
      </div>
    <div class="btn-group" role="group" aria-label="Vista de ubicaciones"><button type="button" id="location-list-view" class="btn btn-primary" aria-pressed="true">Lista</button><button type="button" id="location-plan-view" class="btn btn-outline-primary" aria-pressed="false">Plano</button></div>
    </div>


    <section class="card mb-4"><div class="card-body">
      <div class="row g-3">
        <div class="col-12 col-md-6 col-xl-4"><label for="filter-campus" class="form-label">Establecimiento</label><select id="filter-campus" class="form-select"><option value="">Todos</option></select></div>
        <div class="col-12 col-md-6 col-xl-4"><label for="filter-floor" class="form-label">Piso</label><select id="filter-floor" class="form-select"><option value="">Todos</option></select></div>
        <div class="col-12 col-md-6 col-xl-4"><label for="filter-floor-plan" class="form-label">Plano</label><select id="filter-floor-plan" class="form-select"><option value="">Todos los planos</option></select></div>
        <div class="col-12 col-sm-6 col-xl-3"><label for="filter-type-asset" class="form-label">Tipo</label><select id="filter-type-asset" class="form-select"><option value="">Todos</option></select></div>
        <div class="col-12 col-sm-6 col-xl-3"><label for="filter-category-asset" class="form-label">Categoría</label><select id="filter-category-asset" class="form-select"><option value="">Todas</option></select></div>
        <div class="col-12 col-sm-6 col-xl-3"><label for="filter-space" class="form-label">Ambiente</label><select id="filter-space" class="form-select"><option value="">Todos</option></select></div>
        <div class="col-12 col-sm-6 col-xl-3"><label for="filter-status" class="form-label">Condición técnica</label><select id="filter-status" class="form-select"><option value="">Todas</option></select></div>
      </div>
      <div class="d-flex justify-content-end gap-2 mt-3"><button type="button" id="btnApplyAssetLocationFilters" class="btn btn-primary">Actualizar</button><button type="button" id="btnClearAssetLocationFilters" class="btn btn-outline-primary">Limpiar</button></div>
    </div></section>

    <section id="asset-location-list" class="card card-body border-0 shadow table-wrapper"><table id="tableAssetLocations" class="table table-hover align-middle w-100"><thead><tr><th>#</th><th>Activo</th><th>Situación</th><th>Ubicación</th><th>Responsable</th><th>Plano</th><th>Acciones</th></tr></thead><tbody></tbody></table><p class="small text-muted mb-0">La lista incluye activos sin plano o sin movimiento. El plano muestra solo los activos con coordenadas.</p></section>
    <section id="asset-location-plan" class="card d-none"><div class="card-body">
      <div class="d-flex justify-content-between flex-wrap gap-2 mb-3">
        <div>
          <h5 class="mb-1" id="asset-location-plan-title">Plano no seleccionado</h5>
          <p class="mb-0 text-muted" id="asset-location-summary">Seleccione un plano para visualizar activos ubicados.</p>
        </div>
      </div>
      <div class="asset-location-map" id="asset-location-map">
        <img id="img-asset-location-plan" class="asset-location-plan-image d-none" alt="Plano de ubicacion">
        <div id="asset-location-markers"></div>
        <div class="asset-location-empty" id="asset-location-empty">Seleccione un plano para visualizar los marcadores.</div>
      </div>
    </div></section>

    <div class="modal fade ui-record-detail" id="ModalAssetLocationDetail" tabindex="-1" aria-labelledby="ModalAssetLocationDetailLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="ModalAssetLocationDetailLabel">Detalle de Ubicaci&oacute;n del Activo</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <section class="asset-section">
              <h6 class="asset-section-title">Informaci&oacute;n del activo y ubicaci&oacute;n</h6>
              <div class="asset-meta-grid">
                <div class="asset-meta-card"><span class="asset-meta-label">C&oacute;digo de Inventario</span><div class="asset-meta-value" id="asset-location-detail-code">-</div></div>
                <div class="asset-meta-card"><span class="asset-meta-label">Tipo de Activo</span><div class="asset-meta-value" id="asset-location-detail-type">-</div></div>
                <div class="asset-meta-card"><span class="asset-meta-label">Categor&iacute;a</span><div class="asset-meta-value" id="asset-location-detail-category">-</div></div>
                <div class="asset-meta-card"><span class="asset-meta-label">Estado actual</span><div class="asset-meta-value" id="asset-location-detail-status">-</div></div>
                <div class="asset-meta-card"><span class="asset-meta-label">Ambiente</span><div class="asset-meta-value" id="asset-location-detail-space">-</div></div>
                <div class="asset-meta-card"><span class="asset-meta-label">&Uacute;ltimo Movimiento</span><div class="asset-meta-value" id="asset-location-detail-date">-</div></div>
                <div class="asset-meta-card"><span class="asset-meta-label">Coordenada</span><div class="asset-meta-value" id="asset-location-detail-coordinate">-</div></div>
              </div>
            </section>
          </div>
          <div class="modal-footer">
            <a id="asset-location-history" class="btn btn-outline-primary">Historial de movimientos</a>
            <?php if (!empty($_SESSION['module_permissions']['w_write_permission']) && submenu_action_permission_allowed(MODULE_MOVEMENT,'Movimiento','Activos','Crear')): ?><a id="asset-location-move" class="btn btn-primary">Mover</a><?php endif; ?>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>

    <?php require('Layouts/footer.php'); ?>
  </main>

  <?php require('Layouts/script.php'); ?>
</body>

</html>
