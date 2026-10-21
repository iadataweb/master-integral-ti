<!DOCTYPE html>
<html lang="es">

<?php require('Layouts/head.php'); ?>

<?php
$publicAccess = $data['public_access'] ?? [
    'enabled' => false,
    'mode' => 'indeterminate',
    'starts_at' => '',
    'expires_at' => '',
    'status' => 'Deshabilitado'
];

function sc_h($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function sc_badge_class(string $status): string
{
    $status = strtolower($status);
    if (in_array($status, ['activo'], true)) {
        return 'bg-success';
    }
    if (in_array($status, ['programado'], true)) {
        return 'bg-info';
    }
    if (in_array($status, ['expirado'], true)) {
        return 'bg-danger';
    }
    return 'bg-secondary';
}

function sc_datetime_input($value): string
{
    if (empty($value) || $value === '0000-00-00 00:00:00') {
        return '';
    }
    $timestamp = strtotime((string)$value);
    return $timestamp ? date('Y-m-d\TH:i', $timestamp) : '';
}
?>

<body>
  <?php require('Layouts/sidebar.php'); ?>

  <main class="content">
    <?php require('Layouts/header.php'); ?>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4">
      <div class="d-block mb-4 mb-md-0">
        <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
          <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
            <li class="breadcrumb-item"><span><i class="bx bx-home"></i></span></li>
            <li class="breadcrumb-item">Administracion</li>
            <li class="breadcrumb-item active" aria-current="page">Configuracion del Sistema</li>
          </ol>
        </nav>
        <h2 class="h4">Configuracion del Sistema</h2>
        <p class="mb-0">Administre las opciones generales y el acceso público del sistema.</p>
      </div>
    </div>

    <link rel="stylesheet" href="<?= assets(); ?>/css/components/administration-systemconfiguration-index.css?v=<?= filemtime(dirname(__DIR__, 3) . '/Assets/css/components/administration-systemconfiguration-index.css'); ?>">


    <section class="system-config-grid">

      <article class="system-card">
        <div class="system-card-header">
          <div class="system-card-title">
            <div class="system-card-icon"><i class="bx bx-qr-scan"></i></div>
            <div>
              <h3>Acceso Publico a Consulta Rapida</h3>
              <p class="mb-0">Controle el acceso sin autenticacion a enlaces QR de activos.</p>
            </div>
          </div>
          <span id="public-access-status-badge" class="badge <?= sc_badge_class($publicAccess['status']); ?>"><?= sc_h($publicAccess['status']); ?></span>
        </div>

        <form id="formPublicQuickAccess">
          <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" id="public-quick-access-enabled" name="public_quick_access_enabled" <?= !empty($publicAccess['enabled']) ? 'checked' : ''; ?>>
            <label class="form-check-label" for="public-quick-access-enabled">Permitir acceso publico sin iniciar sesion</label>
          </div>
          <div class="mb-3">
            <label class="form-label">Vigencia del acceso</label>
            <select class="form-control" id="public-quick-access-mode" name="public_quick_access_mode">
              <option value="indeterminate" <?= ($publicAccess['mode'] ?? '') === 'indeterminate' ? 'selected' : ''; ?>>Tiempo indeterminado</option>
              <option value="timed" <?= ($publicAccess['mode'] ?? '') === 'timed' ? 'selected' : ''; ?>>Tiempo determinado</option>
            </select>
          </div>
          <div class="row js-public-access-dates">
            <div class="col-12 col-lg-6 mb-3">
              <label class="form-label">Fecha y hora de inicio</label>
              <input type="datetime-local" class="form-control" name="public_quick_access_starts_at" id="public-quick-access-starts-at" value="<?= sc_h(sc_datetime_input($publicAccess['starts_at'] ?? '')); ?>">
            </div>
            <div class="col-12 col-lg-6 mb-3">
              <label class="form-label">Fecha y hora de expiracion</label>
              <input type="datetime-local" class="form-control" name="public_quick_access_expires_at" id="public-quick-access-expires-at" value="<?= sc_h(sc_datetime_input($publicAccess['expires_at'] ?? '')); ?>">
            </div>
          </div>
          <button type="submit" class="btn btn-primary">
            <i class="bx bx-save me-1"></i> Guardar configuracion
          </button>
        </form>
      </article>



      

      
    </section>

    <?php require('Layouts/footer.php'); ?>
  </main>

  <?php require('Layouts/script.php'); ?>
</body>
</html>
