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
            <li class="breadcrumb-item">Mi Cuenta</li>
            <li class="breadcrumb-item active" aria-current="page">Cambiar Contraseña</li>
          </ol>
        </nav>
        <h2 class="h4">Cambiar Contraseña</h2>
        <p class="mb-0">Actualiza la contraseña de tu cuenta para proteger el acceso al sistema.</p>
      </div>
    </div>

    <div class="card card-body border-0 shadow">
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Ingresar nueva contraseña</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form id="formChangePassword" class="form" enctype="multipart/form-data" data-parsley-validate>
                                <div class="row">
                                    <div class="col-md-6 col-12 mb-2">
                                        <div class="form-group mandatory">
                                            <label for="input-current-password" class="form-label">Contraseña Actual</label>
                                            <input type="password" id="input-current-password" class="form-control"
                                                placeholder="Contraseña Actual" name="current_password"
                                                data-parsley-required="true" />
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12 mb-2">
                                        <div class="form-group mandatory">
                                            <label for="input-new-password" class="form-label">Nueva Contraseña</label>
                                            <input type="password" id="input-new-password" class="form-control"
                                                placeholder="Nueva Contraseña" name="new_password"
                                                data-parsley-required="true" />
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12 mb-2">
                                        <div class="form-group mandatory">
                                            <label for="input-repeat-password" class="form-label">Repetir Nueva Contraseña</label>
                                            <input type="password" id="input-repeat-password" class="form-control"
                                                placeholder="Repetir nueva contraseña" name="repeat_password"
                                                data-parsley-required="true" />
                                        </div>
                                    </div>                                                                                                                                                    
                                </div>
                                <div class="row">
                                    <div class="col-12 d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary me-1 mb-1">
                                            Guardar Cambios
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require('Layouts/footer.php'); ?>

  </main>

  <?php require('Layouts/script.php'); ?>

</body>

</html>