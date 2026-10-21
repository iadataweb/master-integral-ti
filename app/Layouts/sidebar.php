<?php
    $can_dashboard = role_can_read_module(MODULE_DASHBOARD);
    $can_administration = role_can_read_module(MODULE_ADMINISTRATION);
    $can_inventory = role_can_read_module(MODULE_INVENTORY);
    $can_maintenance_base = role_can_read_module(MODULE_MAINTENANCE);
    $can_movement = role_can_read_module(MODULE_MOVEMENT);
    $can_remote_access = role_can_read_module(MODULE_REMOTE_ACCESS);
    $can_help_desk = role_can_read_module(MODULE_HELP_DESK);
    $can_history = role_can_read_module(MODULE_HISTORY);

    $can_submenu = function (int $module, string $scope, string $submenu): bool {
        return submenu_permission_allowed($module, $scope, $submenu);
    };
    $any_allowed = function (array $items): bool {
        return in_array(true, $items, true);
    };

    $submenu_administration = [
        'Empleados' => $can_submenu(MODULE_ADMINISTRATION, 'Administracion', 'Empleados'),
        'Usuarios' => $can_submenu(MODULE_ADMINISTRATION, 'Administracion', 'Usuarios'),
        'Parametros' => $can_submenu(MODULE_ADMINISTRATION, 'Administracion', 'Parametros'),
    ];
    $submenu_inventory = [
        'Activos' => $can_submenu(MODULE_INVENTORY, 'Inventario', 'Activos'),
        'Parametros' => $can_submenu(MODULE_INVENTORY, 'Inventario', 'Parametros'),
    ];
    // Navigation groups retain existing permission identities and routes.
    $submenu_networks = [
        'Wi-Fi' => $can_submenu(MODULE_NETWORKS, 'Redes', 'Wi-Fi'),
        'Segmentos' => submenu_permission_allowed(MODULE_NETWORKS, 'Redes', 'Segmentos'),
    ];
    $can_networks = role_can_read_module(MODULE_NETWORKS) && $any_allowed($submenu_networks);

    $submenu_maintenance = [
        'Preventivo' => $can_submenu(MODULE_MAINTENANCE, 'Mantenimiento', 'Preventivo'),
        'Correctivo' => $can_submenu(MODULE_MAINTENANCE, 'Mantenimiento', 'Correctivo'),
        'Repuestos' => $can_submenu(MODULE_MAINTENANCE, 'Mantenimiento', 'Repuestos'),
        'Parametros' => $can_submenu(MODULE_MAINTENANCE, 'Mantenimiento', 'Parametros'),
    ];
    $submenu_movement = [
        'Activos' => $can_submenu(MODULE_MOVEMENT, 'Movimiento', 'Activos'),
        'Ubicacion de Activos' => $can_submenu(MODULE_MOVEMENT, 'Movimiento', 'Ubicacion de Activos'),
    ];
    $submenu_remote_access = [
        'Accesos' => $can_submenu(MODULE_REMOTE_ACCESS, 'Acceso Remoto', 'Accesos'),
    ];
    $submenu_help_desk = [
        'Incidencias' => $can_submenu(MODULE_HELP_DESK, 'Incidencia', 'Incidencias'),
        'Reportes' => $can_submenu(MODULE_HELP_DESK, 'Incidencia', 'Reportes'),
        'Parametros' => $can_submenu(MODULE_HELP_DESK, 'Incidencia', 'Parametros'),
    ];

    $can_administration = $can_administration && $any_allowed($submenu_administration);
    $can_inventory_main = $can_inventory && $any_allowed($submenu_inventory);
    $can_maintenance = $can_maintenance_base && $any_allowed($submenu_maintenance);
    $can_movement = $can_movement && $any_allowed($submenu_movement);
    $can_remote_access = $can_remote_access && $any_allowed($submenu_remote_access);
    $can_help_desk = $can_help_desk && $any_allowed($submenu_help_desk);

    $arrow_icon = '<svg class="icon icon-sm" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>';
?>

<nav class="navbar navbar-dark navbar-theme-primary px-4 col-12 d-lg-none">
    <a class="navbar-brand me-lg-5" href="<?= current_user_default_url(); ?>">
        <img class="navbar-brand-dark" src="<?= assets(); ?>/img/logo/logo_img.ico" alt="Volt logo" />
        <img class="navbar-brand-light" src="<?= assets(); ?>/img/logo/logo_img.ico" alt="Volt logo" />
    </a>
    <div class="d-flex align-items-center">
        <button class="navbar-toggler d-lg-none collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>

<nav id="sidebarMenu" class="sidebar d-lg-block bg-gray-800 text-white collapse" data-simplebar>
    <div class="sidebar-inner px-3 pt-3">
        <div class="d-flex d-md-none justify-content-end pb-2">
            <div class="collapse-close d-md-none">
                <a href="#sidebarMenu" data-bs-toggle="collapse" data-bs-target="#sidebarMenu"
                    aria-controls="sidebarMenu" aria-expanded="true" aria-label="Toggle navigation">
                    <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                </a>
            </div>
        </div>

        <ul class="nav flex-column pt-3 pt-md-0">
            <li class="nav-item">
                <div class="sidebar-brand d-flex flex-column align-items-center py-2 text-white">
                    <span class="sidebar-icon">
                        <img src="<?= assets(); ?>/img/logo/logo_img.ico" height="25" width="30" alt="Logo del sistema">
                    </span>
                    <span class="mt-1 ms-1 sidebar-text">Sistema Integral de TI</span>
                </div>
            </li>

            <li role="separator" class="dropdown-divider mt-4 mb-3 border-gray-700"></li>

            <?php if ($can_dashboard) { ?>
                <li class="nav-item">
                    <a href="<?= base_url(); ?>/dashboard" class="nav-link">
                        <span class="sidebar-icon">
                            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
                                <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
                            </svg>
                        </span>
                        <span class="sidebar-text">Inicio</span>
                    </a>
                </li>
            <?php } ?>









            <?php if (role_can_read_module(MODULE_TASKS)) { ?>
                <li class="nav-item">
                    <a href="<?= base_url(); ?>/tasks" class="nav-link">
                        <span class="sidebar-icon"><svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M9 5h10v15H5V5h2m2-2h6v4H9V3Zm0 9 2 2 4-4m-6 7h6"/></svg></span>
                        <span class="sidebar-text">Tareas</span>
                    </a>
                </li>
            <?php } ?>

            <?php if ($can_inventory_main) { ?>
                <li class="nav-item">
                    <span class="nav-link collapsed d-flex justify-content-between align-items-center"
                        data-bs-toggle="collapse" data-bs-target="#submenu-inventory">
                        <span>
                            <span class="sidebar-icon">
                                <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.362 1.093a.75.75 0 0 0-.724 0L2.523 5.018 10 9.143l7.477-4.125-7.115-3.925ZM18 6.443l-7.25 4v8.25l6.862-3.786A.75.75 0 0 0 18 14.25V6.443ZM9.25 18.693v-8.25l-7.25-4v7.807a.75.75 0 0 0 .388.657l6.862 3.786Z"></path>
                                </svg>
                            </span>
                            <span class="sidebar-text">Inventario</span>
                        </span>
                        <span class="link-arrow"><?= $arrow_icon; ?></span>
                    </span>
                    <div class="multi-level collapse" role="list" id="submenu-inventory" aria-expanded="false">
                        <ul class="flex-column nav">
                            <?php if ($submenu_inventory['Activos']) { ?><li class="nav-item"><a class="nav-link" href="<?= base_url(); ?>/inventory/assets/manage_assets"><span class="sidebar-text">Activos</span></a></li><?php } ?>
                            <?php if ($submenu_inventory['Parametros']) { ?><li class="nav-item"><a class="nav-link" href="<?= base_url(); ?>/inventory/parameters"><span class="sidebar-text">Parámetros</span></a></li><?php } ?>
                        </ul>
                    </div>
                </li>
            <?php } ?>

            <?php if ($can_movement) { ?>
                <li class="nav-item">
                    <span class="nav-link collapsed d-flex justify-content-between align-items-center"
                        data-bs-toggle="collapse" data-bs-target="#submenu-movement">
                        <span>
                            <span class="sidebar-icon">
                                <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path clip-rule="evenodd" fill-rule="evenodd" d="M13.2 2.24a.75.75 0 0 0 .04 1.06l2.1 1.95H6.75a.75.75 0 0 0 0 1.5h8.59l-2.1 1.95a.75.75 0 1 0 1.02 1.1l3.5-3.25a.75.75 0 0 0 0-1.1l-3.5-3.25a.75.75 0 0 0-1.06.04Zm-6.4 8a.75.75 0 0 0-1.06-.04l-3.5 3.25a.75.75 0 0 0 0 1.1l3.5 3.25a.75.75 0 1 0 1.02-1.1l-2.1-1.95h8.59a.75.75 0 0 0 0-1.5H4.66l2.1-1.95a.75.75 0 0 0 .04-1.06Z"></path>
                                </svg>
                            </span>
                            <span class="sidebar-text">Movimientos</span>
                        </span>
                        <span class="link-arrow"><?= $arrow_icon; ?></span>
                    </span>
                    <div class="multi-level collapse" role="list" id="submenu-movement" aria-expanded="false">
                        <ul class="flex-column nav">
                            <?php if ($submenu_movement['Activos']) { ?><li class="nav-item"><a class="nav-link" href="<?= base_url(); ?>/movement/movements_asset/manage_movements_asset"><span class="sidebar-text">Activos</span></a></li><?php } ?>
                            <?php if ($submenu_movement['Ubicacion de Activos']) { ?><li class="nav-item"><a class="nav-link" href="<?= base_url(); ?>/movement/movements_asset/asset_locations"><span class="sidebar-text">Ubicaciones</span></a></li><?php } ?>
                        </ul>
                    </div>
                </li>
            <?php } ?>

            <?php if ($can_help_desk) { ?>
                <li class="nav-item">
                    <span class="nav-link collapsed d-flex justify-content-between align-items-center"
                        data-bs-toggle="collapse" data-bs-target="#submenu-help-desk">
                        <span>
                            <span class="sidebar-icon">
                                <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path clip-rule="evenodd" fill-rule="evenodd" d="M15.75 3A2.25 2.25 0 0 1 18 5.25v1.214c0 .423-.277.788-.633 1.019A2.997 2.997 0 0 0 16 10c0 1.055.544 1.982 1.367 2.517.356.231.633.596.633 1.02v1.213A2.25 2.25 0 0 1 15.75 17H4.25A2.25 2.25 0 0 1 2 14.75v-1.213c0-.424.277-.789.633-1.02A2.998 2.998 0 0 0 4 10a2.997 2.997 0 0 0-1.367-2.517C2.277 7.252 2 6.887 2 6.463V5.25A2.25 2.25 0 0 1 4.25 3h11.5ZM13.5 7.396a.75.75 0 0 0-1.5 0v1.042a.75.75 0 0 0 1.5 0V7.396Zm0 4.167a.75.75 0 0 0-1.5 0v1.041a.75.75 0 0 0 1.5 0v-1.041Z"></path>
                                </svg>
                            </span>
                            <span class="sidebar-text">Mesa de ayuda</span>
                        </span>
                        <span class="link-arrow"><?= $arrow_icon; ?></span>
                    </span>
                    <div class="multi-level collapse" role="list" id="submenu-help-desk" aria-expanded="false">
                        <ul class="flex-column nav">
                                <?php if ($submenu_help_desk['Incidencias']) { ?><li class="nav-item"><a class="nav-link" href="<?= base_url(); ?>/help_desk/incidents/manage_incidents"><span class="sidebar-text">Incidencias</span></a></li><?php } ?>
                                <?php if ($submenu_help_desk['Reportes']) { ?><li class="nav-item"><a class="nav-link" href="<?= base_url(); ?>/help_desk/incidents/reports"><span class="sidebar-text">Reportes</span></a></li><?php } ?>
                                <?php if ($submenu_help_desk['Parametros']) { ?><li class="nav-item"><a class="nav-link" href="<?= base_url(); ?>/help_desk/parameters"><span class="sidebar-text">Parámetros</span></a></li><?php } ?>

                        </ul>
                    </div>
                </li>
            <?php } ?>

            <?php if ($can_maintenance) { ?>
                <li class="nav-item">
                    <span class="nav-link collapsed d-flex justify-content-between align-items-center"
                        data-bs-toggle="collapse" data-bs-target="#submenu-maintenance">
                        <span>
                            <span class="sidebar-icon">
                                <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path clip-rule="evenodd" fill-rule="evenodd" d="M14.5 10a4.5 4.5 0 0 0 4.284-5.882c-.105-.324-.51-.391-.752-.15L15.34 6.66a.454.454 0 0 1-.493.11 3.01 3.01 0 0 1-1.618-1.616.455.455 0 0 1 .11-.494l2.694-2.692c.24-.241.174-.647-.15-.752a4.5 4.5 0 0 0-5.873 4.575c.055.873-.128 1.808-.8 2.368l-7.23 6.024a2.724 2.724 0 1 0 3.837 3.837l6.024-7.23c.56-.672 1.495-.855 2.368-.8.096.007.193.01.291.01ZM5 16a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z"></path>
                                    <path d="M14.5 11.5c.173 0 .345-.007.514-.022l3.754 3.754a2.5 2.5 0 0 1-3.536 3.536l-4.41-4.41 2.172-2.607c.052-.063.147-.138.342-.196.202-.06.469-.087.777-.067.128.008.257.012.387.012ZM6 4.586l2.33 2.33a.452.452 0 0 1-.08.09L6.8 8.214 4.586 6H3.309a.5.5 0 0 1-.447-.276l-1.7-3.402a.5.5 0 0 1 .093-.577l.49-.49a.5.5 0 0 1 .577-.094l3.402 1.7A.5.5 0 0 1 6 3.31v1.277Z"></path>
                                </svg>
                            </span>
                            <span class="sidebar-text">Mantenimiento</span>
                        </span>
                        <span class="link-arrow"><?= $arrow_icon; ?></span>
                    </span>
                    <div class="multi-level collapse" role="list" id="submenu-maintenance" aria-expanded="false">
                        <ul class="flex-column nav">
                            <?php if ($submenu_maintenance['Preventivo']) { ?><li class="nav-item"><a class="nav-link" href="<?= base_url(); ?>/maintenance/maintenances_preventive/manage_maintenances_preventive"><span class="sidebar-text">Preventivo</span></a></li><?php } ?>
                            <?php if ($submenu_maintenance['Correctivo']) { ?><li class="nav-item"><a class="nav-link" href="<?= base_url(); ?>/maintenance/maintenances_corrective/manage_maintenances_corrective"><span class="sidebar-text">Correctivo</span></a></li><?php } ?>
                            <?php if ($submenu_maintenance['Repuestos']) { ?><li class="nav-item"><a class="nav-link" href="<?= base_url(); ?>/maintenance/spare_parts/manage_spare_parts"><span class="sidebar-text">Repuestos</span></a></li><?php } ?>
                            <?php if ($submenu_maintenance['Parametros']) { ?><li class="nav-item"><a class="nav-link" href="<?= base_url(); ?>/maintenance/parameters"><span class="sidebar-text">Parámetros</span></a></li><?php } ?>
                        </ul>
                    </div>
                </li>
            <?php } ?>

            <?php if ($can_networks) { ?>
                <li class="nav-item">
                    <button type="button" class="nav-link collapsed d-flex justify-content-between align-items-center w-100 border-0 bg-transparent text-start"
                        data-bs-toggle="collapse" data-bs-target="#submenu-networks" aria-controls="submenu-networks" aria-expanded="false">
                        <span><span class="sidebar-icon"><svg class="icon icon-xs me-2" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><rect x="9" y="2" width="6" height="6" rx="1"/><path d="M12 8v5M5 17v-4h14v4"/><rect x="2" y="17" width="6" height="5" rx="1"/><rect x="16" y="17" width="6" height="5" rx="1"/></svg></span><span class="sidebar-text">Redes</span></span>
                        <span class="link-arrow"><?= $arrow_icon; ?></span>
                    </button>
                    <div class="multi-level collapse" id="submenu-networks">
                        <ul class="flex-column nav">
                            <?php if ($submenu_networks['Wi-Fi']) { ?><li class="nav-item"><a class="nav-link" href="<?= base_url(); ?>/inventory/wifi_networks/manage_wifi_networks"><span class="sidebar-text">Wi-Fi</span></a></li><?php } ?>
                            <?php if ($submenu_networks['Segmentos']) { ?><li class="nav-item"><a class="nav-link" href="<?= base_url(); ?>/inventory/network_segmentations/manage_network_segmentations"><span class="sidebar-text">Segmentos</span></a></li><?php } ?>
                        </ul>
                    </div>
                </li>
            <?php } ?>

            <?php if ($can_remote_access) { ?>
                <li class="nav-item">
                    <span class="nav-link collapsed d-flex justify-content-between align-items-center"
                        data-bs-toggle="collapse" data-bs-target="#submenu-remote-access">
                        <span>
                            <span class="sidebar-icon">
                                <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 4.75A2.75 2.75 0 0 1 5.75 2h8.5A2.75 2.75 0 0 1 17 4.75v5.5A2.75 2.75 0 0 1 14.25 13H11v1.5h2.25a.75.75 0 0 1 0 1.5h-6.5a.75.75 0 0 1 0-1.5H9V13H5.75A2.75 2.75 0 0 1 3 10.25v-5.5ZM5.75 3.5c-.69 0-1.25.56-1.25 1.25v5.5c0 .69.56 1.25 1.25 1.25h8.5c.69 0 1.25-.56 1.25-1.25v-5.5c0-.69-.56-1.25-1.25-1.25h-8.5Z"></path>
                                    <path d="M11.9 6.1a.75.75 0 0 1 1.06 0l1.18 1.18a.75.75 0 0 1 0 1.06l-1.18 1.18a.75.75 0 1 1-1.06-1.06l.65-.65-.65-.65a.75.75 0 0 1 0-1.06Zm-3.8 0a.75.75 0 0 1 0 1.06l-.65.65.65.65a.75.75 0 1 1-1.06 1.06L5.86 8.34a.75.75 0 0 1 0-1.06L7.04 6.1A.75.75 0 0 1 8.1 6.1Z"></path>
                                </svg>
                            </span>
                            <span class="sidebar-text">Acceso remoto</span>
                        </span>
                        <span class="link-arrow"><?= $arrow_icon; ?></span>
                    </span>
                    <div class="multi-level collapse" role="list" id="submenu-remote-access" aria-expanded="false">
                        <ul class="flex-column nav">
                            <?php if ($submenu_remote_access['Accesos']) { ?><li class="nav-item"><a class="nav-link" href="<?= base_url(); ?>/remote_access/remote_accesses/manage_remote_accesses"><span class="sidebar-text">Accesos</span></a></li><?php } ?>
                        </ul>
                    </div>
                </li>
            <?php } ?>

            <?php if ($can_history) { ?>
                <li class="nav-item">
                    <a href="<?= base_url(); ?>/history" class="nav-link">
                        <span class="sidebar-icon">
                            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path clip-rule="evenodd" fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-13a.75.75 0 0 0-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 0 0 0-1.5h-3.25V5Z"></path>
                            </svg>
                        </span>
                        <span class="sidebar-text">Historial</span>
                    </a>
                </li>
            <?php } ?>

            <?php if ($can_administration) { ?>
                <li class="nav-item">
                    <span class="nav-link collapsed d-flex justify-content-between align-items-center"
                        data-bs-toggle="collapse" data-bs-target="#submenu-administration">
                        <span>
                            <span class="sidebar-icon">
                                <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path clip-rule="evenodd" fill-rule="evenodd" d="M7.84 1.804A1 1 0 0 1 8.82 1h2.36a1 1 0 0 1 .98.804l.331 1.652a6.993 6.993 0 0 1 1.929 1.115l1.598-.54a1 1 0 0 1 1.186.447l1.18 2.044a1 1 0 0 1-.205 1.251l-1.267 1.113a7.047 7.047 0 0 1 0 2.228l1.267 1.113a1 1 0 0 1 .206 1.25l-1.18 2.045a1 1 0 0 1-1.187.447l-1.598-.54a6.993 6.993 0 0 1-1.929 1.115l-.33 1.652a1 1 0 0 1-.98.804H8.82a1 1 0 0 1-.98-.804l-.331-1.652a6.993 6.993 0 0 1-1.929-1.115l-1.598.54a1 1 0 0 1-1.186-.447l-1.18-2.044a1 1 0 0 1 .205-1.251l1.267-1.114a7.05 7.05 0 0 1 0-2.227L1.821 7.773a1 1 0 0 1-.206-1.25l1.18-2.045a1 1 0 0 1 1.187-.447l1.598.54A6.992 6.992 0 0 1 7.51 3.456l.33-1.652ZM10 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"></path>
                                </svg>
                            </span>
                            <span class="sidebar-text">Administración</span>
                        </span>
                        <span class="link-arrow"><?= $arrow_icon; ?></span>
                    </span>
                    <div class="multi-level collapse" role="list" id="submenu-administration" aria-expanded="false">
                        <ul class="flex-column nav">
                            <?php if ($submenu_administration['Empleados']) { ?><li class="nav-item"><a class="nav-link" href="<?= base_url(); ?>/administration/employees/manage_employees"><span class="sidebar-text">Empleados</span></a></li><?php } ?>
                            <?php if ($submenu_administration['Usuarios']) { ?><li class="nav-item"><a class="nav-link" href="<?= base_url(); ?>/administration/users/manage_users"><span class="sidebar-text">Usuarios</span></a></li><?php } ?>
                            <?php if ($submenu_administration['Parametros']) { ?><li class="nav-item"><a class="nav-link" href="<?= base_url(); ?>/administration/parameters"><span class="sidebar-text">Parámetros</span></a></li><?php } ?>
                        </ul>
                    </div>
                </li>
            <?php } ?>

        </ul>
    </div>
</nav>
