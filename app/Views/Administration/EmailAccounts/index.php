<!DOCTYPE html>
<html lang="en">

<?php require('Layouts/head.php'); ?>

<body>
  <?php require('Layouts/sidebar.php'); ?>

  <link rel="stylesheet" href="<?= assets(); ?>/css/components/administration-osuseraccounts-index.css?v=<?= filemtime(dirname(__DIR__, 3) . '/Assets/css/components/administration-osuseraccounts-index.css'); ?>">

  <main class="content">
    <?php require('Layouts/header.php'); ?>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4 page-heading-actions">
      <div class="d-block mb-4 mb-md-0">
        <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
          <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
            <li class="breadcrumb-item"><span><i class="bx bx-home"></i></span></li>
            <li class="breadcrumb-item">Administracion</li>
            <li class="breadcrumb-item active" aria-current="page">Correos Electronicos</li>
          </ol>
        </nav>
        <h2 class="h4">Correos Electronicos</h2>
        <p class="mb-0">Administra cuentas institucionales, telefonos asociados y cambios de contrasena.</p>
      </div>
      <div class="btn-toolbar mb-2 mb-md-0 page-action-toolbar">
        <button type="button" id="btnNew" class="btn btn-sm btn-gray-800 d-inline-flex align-items-center">
          <i class="bx bx-plus me-2"></i> Nuevo
        </button>
      </div>
    </div>

    <div class="card card-body border-0 shadow table-wrapper table-responsive">
      <table id="tableManageEmailAccounts" class="table table-hover">
        <thead>
          <tr>
            <th class="border-gray-200">#</th>
            <th class="border-gray-200">Nombre</th>
            <th class="border-gray-200">Correo</th>
            <th class="border-gray-200">Telefono</th>
            <th class="border-gray-200">Estado</th>
            <th class="border-gray-200">Actualizado</th>
            <th class="border-gray-200">Accion</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>

    <?php require('create-or-edit.php'); ?>
    <?php require('see.php'); ?>
    <?php require('history.php'); ?>
    <?php require('Layouts/footer.php'); ?>
  </main>

  <?php require('Layouts/script.php'); ?>
</body>

</html>
