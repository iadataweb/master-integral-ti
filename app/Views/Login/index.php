<!DOCTYPE html>
<html lang="en">

<?php require('Layouts/head.php'); ?>

<body class="login-page">

    <main>

        <!-- Section -->
        <section class="login-shell min-vh-100 d-flex align-items-center py-4">
            <div class="container">
                <div class="row justify-content-center form-bg-image">
                    <div class="col-12 d-flex align-items-center justify-content-center">
                        <div class="login-card bg-white border-0 rounded border-light p-4 p-lg-5 w-100 fmxw-500">
                            <div class="text-center text-md-center mb-4 mt-md-0">
                                <h1 class="mb-0 h3">Iniciar sesión</h1>
                            </div>
                            <form id="formLogin" class="mt-4">
                                <!-- Form -->
                                <div class="form-group mb-4">
                                    <label for="name_user">Nombre de usuario</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon1">
                                            <svg class="icon icon-xs text-gray-600" fill="currentColor" 
                                                viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path 
                                                    d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z">
                                                </path>
                                            </svg>
                                        </span>
                                        <input type="text" class="form-control" placeholder="Nombre de usuario"
                                            name="name_user" id="name_user" autofocus required>
                                    </div>
                                </div>
                                <!-- End of Form -->
                                <div class="form-group">
                                    <!-- Form -->
                                    <div class="form-group mb-4">
                                        <label for="password">Contraseña</label>
                                        <div class="input-group">
                                            <span class="input-group-text" id="basic-addon2">
                                                <svg class="icon icon-xs text-gray-600" fill="currentColor"
                                                    viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd"
                                                        d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            </span>
                                            <input type="password" placeholder="Contraseña" class="form-control"
                                                name="password" id="password" autocomplete="current-password" required>
                                            <button type="button" class="btn btn-outline-gray-600 d-inline-flex align-items-center justify-content-center" id="toggle-login-password" aria-controls="password" aria-label="Mostrar contraseña" aria-pressed="false" title="Mostrar contraseña">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                    <path id="login-password-slash" d="m3 3 18 18" style="display:none"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-gray-800">Ingresar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php require('Layouts/script.php'); ?>

</body>

</html>
