<!DOCTYPE html>
<html lang="en">

<?php require('Layouts/head.php'); ?>

<body>
  <?php require('Layouts/sidebar.php'); ?>

  <link rel="stylesheet" href="<?= assets(); ?>/css/components/movement-movementsasset-location.css?v=<?= filemtime(dirname(__DIR__, 3) . '/Assets/css/components/movement-movementsasset-location.css'); ?>">

  <main class="content">
    <?php require('Layouts/header.php'); ?>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4 page-heading-actions">
      <div class="d-block mb-4 mb-md-0">
        <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
          <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
            <li class="breadcrumb-item"><span><i class="bx bx-home"></i></span></li>
            <li class="breadcrumb-item">Movimiento</li>
            <li class="breadcrumb-item active" aria-current="page">Ubicacion en Plano</li>
          </ol>
        </nav>
        <h2 class="h4">Ubicacion del Activo en Plano</h2>
        <p class="mb-0"><?= htmlspecialchars($data['movement']['code_inventory_asset']); ?> - <?= htmlspecialchars($data['movement']['name_type_asset']); ?></p>
      </div>
      <div class="btn-toolbar mb-2 mb-md-0 page-action-toolbar">
        <a href="<?= base_url(); ?>/<?= htmlspecialchars($data['movement']['current_file_floor_plan']); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary">
          <i class="bx bx-file me-1"></i> Abrir PDF
        </a>
      </div>
    </div>

    <section class="asset-section plan-summary-section mb-4">
      <h6 class="asset-section-title">Resumen del activo</h6>
      <div class="asset-meta-grid">
        <div class="asset-meta-card"><span class="asset-meta-label">Codigo de Inventario</span><div class="asset-meta-value"><?= htmlspecialchars($data['movement']['code_inventory_asset']); ?></div></div>
        <div class="asset-meta-card"><span class="asset-meta-label">Tipo de Activo</span><div class="asset-meta-value"><?= htmlspecialchars($data['movement']['name_type_asset']); ?></div></div>
        <div class="asset-meta-card"><span class="asset-meta-label">Categoria</span><div class="asset-meta-value"><?= htmlspecialchars($data['movement']['name_category_asset']); ?></div></div>
        <div class="asset-meta-card"><span class="asset-meta-label">Ambiente</span><div class="asset-meta-value"><?= htmlspecialchars($data['movement']['name_space']); ?></div></div>
        <div class="asset-meta-card"><span class="asset-meta-label">Plano</span><div class="asset-meta-value"><?= htmlspecialchars($data['movement']['name_floor_plan']); ?></div></div>
        <div class="asset-meta-card"><span class="asset-meta-label">Coordenada</span><div class="asset-meta-value">X <?= number_format((float)$data['movement']['coordinate_x_movement_asset'], 2); ?>% / Y <?= number_format((float)$data['movement']['coordinate_y_movement_asset'], 2); ?>%</div></div>
      </div>
    </section>

    <section class="card card-body border-0 shadow">
      <div class="plan-location-view">
        <?php
          $planImageRoute = !empty($data['movement']['current_image_floor_plan']) ? $data['movement']['current_image_floor_plan'] : $data['movement']['current_file_floor_plan'];
          $coordinateX = (float)$data['movement']['coordinate_x_movement_asset'];
          $coordinateY = (float)$data['movement']['coordinate_y_movement_asset'];
          $labelOffsetX = $coordinateX > 75 ? -92 : 92;
          $labelOffsetY = $coordinateY < 18 ? 44 : -44;
          $lineLength = max(1, sqrt(($labelOffsetX * $labelOffsetX) + ($labelOffsetY * $labelOffsetY)) - 13);
          $lineAngle = atan2($labelOffsetY, $labelOffsetX) * 180 / pi();
        ?>
        <img src="<?= base_url(); ?>/<?= htmlspecialchars($planImageRoute); ?>" class="plan-location-image" alt="Plano del activo">
        <button type="button" class="plan-location-point" style="left: <?= number_format($coordinateX, 2, '.', ''); ?>%; top: <?= number_format($coordinateY, 2, '.', ''); ?>%; --label-x: <?= (int)$labelOffsetX; ?>px; --label-y: <?= (int)$labelOffsetY; ?>px; --line-length: <?= number_format($lineLength, 0, '.', ''); ?>px; --line-angle: <?= number_format($lineAngle, 2, '.', ''); ?>deg;" data-bs-toggle="modal" data-bs-target="#ModalPlanLocationDetail" aria-label="Ver detalle de ubicacion del activo">
          <span class="plan-location-line"></span>
          <span class="plan-location-marker"><i class="bx bx-map-pin"></i></span>
          <span class="plan-location-label"><?= htmlspecialchars($data['movement']['name_type_asset']); ?></span>
        </button>
      </div>
    </section>

    <div class="modal fade" id="ModalPlanLocationDetail" tabindex="-1" aria-labelledby="ModalPlanLocationDetailLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="ModalPlanLocationDetailLabel">Detalle de Ubicaci&oacute;n del Activo</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <section class="plan-detail-section">
              <h6 class="plan-detail-section-title">Informaci&oacute;n del activo y ubicaci&oacute;n</h6>
              <div class="plan-detail-grid">
                <div class="plan-detail-card"><span class="plan-detail-label">C&oacute;digo de Inventario</span><div class="plan-detail-value"><?= htmlspecialchars($data['movement']['code_inventory_asset'] ?? 'No definido'); ?></div></div>
                <div class="plan-detail-card"><span class="plan-detail-label">Tipo de Activo</span><div class="plan-detail-value"><?= htmlspecialchars($data['movement']['name_type_asset'] ?? 'No definido'); ?></div></div>
                <div class="plan-detail-card"><span class="plan-detail-label">Categor&iacute;a</span><div class="plan-detail-value"><?= htmlspecialchars($data['movement']['name_category_asset'] ?? 'No definido'); ?></div></div>
                <div class="plan-detail-card"><span class="plan-detail-label">Condici&oacute;n</span><div class="plan-detail-value"><?= htmlspecialchars($data['movement']['name_condition_asset'] ?? 'No definido'); ?></div></div>
                <div class="plan-detail-card"><span class="plan-detail-label">Ambiente</span><div class="plan-detail-value"><?= htmlspecialchars($data['movement']['name_space'] ?? 'No definido'); ?></div></div>
                <div class="plan-detail-card"><span class="plan-detail-label">Plano</span><div class="plan-detail-value"><?= htmlspecialchars($data['movement']['name_floor_plan'] ?? 'No definido'); ?></div></div>
                <div class="plan-detail-card"><span class="plan-detail-label">Fecha de Movimiento</span><div class="plan-detail-value"><?= htmlspecialchars($data['movement']['date_movement_asset'] ?? 'No definido'); ?></div></div>
                <div class="plan-detail-card"><span class="plan-detail-label">Coordenada</span><div class="plan-detail-value">X <?= number_format((float)$data['movement']['coordinate_x_movement_asset'], 2); ?>% / Y <?= number_format((float)$data['movement']['coordinate_y_movement_asset'], 2); ?>%</div></div>
              </div>
            </section>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-gray-800" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>

    <?php require('Layouts/footer.php'); ?>
  </main>

  <?php require('Layouts/script.php'); ?>
</body>

</html>






