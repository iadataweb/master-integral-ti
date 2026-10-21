<nav class="navbar navbar-top navbar-expand navbar-dashboard navbar-dark ps-0 pe-2 pb-0">
    <div class="container-fluid px-0">
        <div class="d-flex justify-content-end w-100" id="navbarSupportedContent">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item me-2">
                    <button id="btn-theme-toggle" type="button" class="btn btn-sm btn-outline-gray-600 d-inline-flex align-items-center" title="Cambiar tema" aria-label="Cambiar tema">
                        <svg id="icon-theme-toggle" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M2 12h2m16 0h2M5 5l1.5 1.5m11 11L19 19M5 19l1.5-1.5m11-11L19 5"/></svg>
                    </button>
                </li>


                <li class="nav-item dropdown ms-lg-3">
                    <span class="nav-link dropdown-toggle pt-1 px-0" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="media d-flex align-items-center">
                            <img class="avatar rounded-circle" alt="Foto de perfil"
                                src="<?= profile_photo_url($_SESSION['user_data']['profile_photo_user'] ?? null); ?>">
                            <div class="media-body ms-2 text-dark align-items-center d-none d-lg-block">
                                <span class="mb-0 font-small fw-bold text-gray-900"><?= $_SESSION['user_data']['display_name']; ?></span>
                                <p class="mb-0 font-small fw-bold text-gray-900"><?= $_SESSION['user_data']['name_role']; ?></p>
                            </div>
                        </div>
                    </span>
                    <div class="dropdown-menu dashboard-dropdown dropdown-menu-end mt-2 py-1">
                        <a class="dropdown-item d-flex align-items-center" href="<?= base_url(); ?>/account/profile">
                            <svg class="dropdown-icon text-gray-400 me-2" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2Z"/></svg>
                            Perfil
                        </a>
                        <a class="dropdown-item d-flex align-items-center" href="<?= base_url(); ?>/account/browser_sessions">
                            <svg class="dropdown-icon text-gray-400 me-2" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="13" height="16" rx="2"/><rect x="10" y="7" width="11" height="14" rx="2"/></svg>
                            Sesiones del Navegador
                        </a>
                        <a class="dropdown-item d-flex align-items-center" href="<?= base_url(); ?>/account/change_password">
                            <svg class="dropdown-icon text-gray-400 me-2" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3 M12 14v3"/></svg>
                            Cambiar Contraseña
                        </a>
                        <div role="separator" class="dropdown-divider my-1"></div>
                        <a class="dropdown-item d-flex align-items-center" href="<?= base_url(); ?>/account/logout">
                            <svg class="dropdown-icon text-danger me-2" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 4H4v16h5 M10 12h11 M17 8l4 4-4 4"/></svg>
                            Cerrar Sesión
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
