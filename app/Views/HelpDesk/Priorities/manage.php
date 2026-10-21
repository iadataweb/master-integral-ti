<!DOCTYPE html>
<html lang="es">
<?php require('Layouts/head.php'); ?>
<body>
  <?php require('Layouts/sidebar.php'); ?>
  <main class="content">
    <?php require('Layouts/header.php'); ?>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4">
      <div class="d-block mb-4 mb-md-0">
        <nav aria-label="breadcrumb"><ol class="breadcrumb breadcrumb-transparent">
          <li class="breadcrumb-item"><span>Inicio</span></li>
          <li class="breadcrumb-item">Mesa de ayuda</li>
          <li class="breadcrumb-item"><span>Parámetros</span></li>
          <li class="breadcrumb-item active" aria-current="page">Prioridades</li>
        </ol></nav>
        <h2 class="h4">Prioridades</h2>
        <p class="mb-0">Configura las prioridades para la atención de incidencias.</p>
      </div>
      <div class="btn-toolbar"><button type="button" id="btnNew" class="btn btn-sm btn-gray-800">Nuevo</button></div>
    </div>
    <div class="card card-body table-wrapper table-responsive">
      <table id="tableManagePriorities" class="table table-hover w-100">
        <thead><tr><th>#</th><th>Nombre</th><th>Acción</th></tr></thead><tbody></tbody>
      </table>
    </div>
    <?php require('see.php'); ?>
    <?php require('create-or-edit.php'); ?>
    <?php require('Layouts/footer.php'); ?>
  </main>
  <?php require('Layouts/script.php'); ?>
</body>
</html>
