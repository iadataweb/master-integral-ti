<!DOCTYPE html>
<html lang="en">

<?php require('Layouts/head.php'); ?>

<body>

  <?php require('Layouts/sidebar.php'); ?>

  <main class="content">

    <?php require('Layouts/header.php'); ?>

    <?php
      $profile = $data['profile_data'] ?? [];
      $display_name = $profile['display_name'] ?? ($_SESSION['user_data']['display_name'] ?? 'Usuario');
      $name_user = $profile['name_user'] ?? '-';
      $name_role = $profile['name_role'] ?? ($_SESSION['user_data']['name_role'] ?? '-');
      $email = $profile['email_employee'] ?? '-';
      $theme = $profile['theme_preference_user'] ?? ($_SESSION['user_data']['theme_preference_user'] ?? 'light');
      $photo_url = profile_photo_url($profile['profile_photo_user'] ?? null);
    ?>

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
            <li class="breadcrumb-item active" aria-current="page">Perfil</li>
          </ol>
        </nav>
        <h2 class="h4">Perfil de Usuario</h2>
        <p class="mb-0">Administre su foto de perfil y visualice su informacion principal.</p>
      </div>
    </div>

    <section class="account-shell">
      <div class="profile-card">
        <div class="row align-items-center g-3">
          <div class="col-12 col-lg-auto d-flex justify-content-center">
            <div class="profile-photo-wrap">
              <img id="img-profile-preview" src="<?= $photo_url; ?>" alt="Foto de perfil">
            </div>
          </div>
          <div class="col-12 col-lg">
            <h3 class="h5 mb-2"><?= $display_name; ?></h3>
            <p class="profile-user-meta mb-1"><strong>Usuario:</strong> <?= $name_user; ?></p>
            <p class="profile-user-meta mb-1"><strong>Rol:</strong> <?= $name_role; ?></p>
            <p class="profile-user-meta mb-1"><strong>Email:</strong> <?= $email; ?></p>
            <p class="profile-user-meta mb-0"><strong>Tema actual:</strong> <?= $theme === 'dark' ? 'Oscuro' : 'Claro'; ?></p>
          </div>
        </div>
      </div>

      <div class="profile-card">
        <h4 class="h6 mb-3">Actualizar Foto de Perfil</h4>
        <form id="formProfilePhoto" enctype="multipart/form-data">
          <div class="row g-3 align-items-end">
            <div class="col-12 col-lg-7">
              <label for="input-profile-photo" class="form-label">Seleccionar imagen (JPG, PNG, WEBP, maximo 5 MB)</label>
              <input class="form-control" type="file" id="input-profile-photo" name="profile_photo" accept="image/jpeg,image/png,image/webp">
            </div>
            <div class="col-12 col-lg-5 d-flex flex-wrap gap-2 justify-content-lg-end">
              <button type="submit" class="btn btn-primary">Guardar Foto</button>
              <button type="button" id="btnDeleteProfilePhoto" class="btn btn-outline-danger">Eliminar Foto</button>
            </div>
          </div>
        </form>
      </div>
    </section>

    <?php require('Layouts/footer.php'); ?>

  </main>

  <?php require('Layouts/script.php'); ?>

</body>

</html>
