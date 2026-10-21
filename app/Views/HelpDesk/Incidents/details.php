<?php
$incident = $data['specific_incident'];
$escape = static fn($value) => htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
$isOpen = $data['incident_lifecycle']['is_open'];
$canWrite = !empty($data['incident_action_permissions']['can_register']);
$canDelete = !empty($data['incident_action_permissions']['can_delete']);
$derivation = $incident['corrective_derivation_status_incident'] ?? 'none';
$derivationLabels = ['none'=>'Sin derivación','pending'=>'Derivación pendiente','accepted'=>'Derivación aceptada','rejected'=>'Derivación rechazada','cancelled'=>'Derivación cancelada'];
?>
<!DOCTYPE html><html lang="es">
<?php require('Layouts/head.php'); ?>
<body>
<?php require('Layouts/sidebar.php'); ?>
<main class="content">
 <?php require('Layouts/header.php'); ?>
 <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 py-4">
  <div>
   <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2"><li class="breadcrumb-item">Mesa de ayuda</li><li class="breadcrumb-item"><span>Incidencias</span></li><li class="breadcrumb-item active">#<?= (int)$incident['id_incident'] ?></li></ol></nav>
   <h2 class="h4">Detalle de incidencia</h2>
   <p class="mb-0">Consulta la incidencia, documenta su atención y revisa su seguimiento.</p>
  </div>
  <a class="btn btn-outline-secondary btn-sm" href="<?= base_url() ?>/help_desk/incidents/manage_incidents">Volver</a>
 </div>
 <div id="incident-details-content">
  <?php require('detail-content.php'); ?>
 </div>
 <?php require('Layouts/footer.php'); ?>
</main>
<?php require('Layouts/script.php'); ?>
</body></html>
