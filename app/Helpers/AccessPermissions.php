<?php
require_once __DIR__.'/../Core/RoleAccess.php';

    function role_permissions_map(?int $id_role = null): array
    {
        $id_role = $id_role ?? (int)($_SESSION['user_data']['id_role'] ?? 0);
        return RoleAccess::modules($id_role);
    }

    function role_can_read_module(int $id_module, ?int $id_role = null): bool
    {
        $permissions = role_permissions_map($id_role);
        return !empty($permissions[$id_module]['r_read_permission']);
    }


    function current_user_default_url(): string
    {
        $id_role = (int)($_SESSION['user_data']['id_role'] ?? 0);
        $routes = [
            [MODULE_DASHBOARD, '', '', "/dashboard"],
            [MODULE_TASKS, '', '', "/tasks"],
            [MODULE_ADMINISTRATION, 'Administracion', 'Empleados', "/administration/employees/manage_employees"],
            [MODULE_ADMINISTRATION, 'Administracion', 'Usuarios', "/administration/users/manage_users"],
            [MODULE_ADMINISTRATION, 'Administracion', 'Parametros', "/administration/parameters"],
            [MODULE_INVENTORY, 'Inventario', 'Activos', "/inventory/assets/manage_assets"],
            [MODULE_NETWORKS, 'Redes', 'Wi-Fi', "/inventory/wifi_networks/manage_wifi_networks"],
            [MODULE_NETWORKS, 'Redes', 'Segmentos', "/inventory/network_segmentations/manage_network_segmentations"],
            [MODULE_INVENTORY, 'Inventario', 'Parametros', "/inventory/parameters"],
            [MODULE_MAINTENANCE, 'Mantenimiento', 'Preventivo', "/maintenance/maintenances_preventive/manage_maintenances_preventive"],
            [MODULE_MAINTENANCE, 'Mantenimiento', 'Correctivo', "/maintenance/maintenances_corrective/manage_maintenances_corrective"],
            [MODULE_MAINTENANCE, 'Mantenimiento', 'Repuestos', "/maintenance/spare_parts/manage_spare_parts"],
            [MODULE_MAINTENANCE, 'Mantenimiento', 'Parametros', "/maintenance/parameters"],
            [MODULE_MOVEMENT, 'Movimiento', 'Activos', "/movement/movements_asset/manage_movements_asset"],
            [MODULE_MOVEMENT, 'Movimiento', 'Ubicacion de Activos', "/movement/movements_asset/asset_locations"],
            [MODULE_REMOTE_ACCESS, 'Acceso Remoto', 'Accesos', "/remote_access/remote_accesses/manage_remote_accesses"],
            [MODULE_HELP_DESK, 'Incidencia', 'Incidencias', "/help_desk/incidents/manage_incidents"],
            [MODULE_HELP_DESK, 'Incidencia', 'Reportes', "/help_desk/incidents/reports"],
            [MODULE_HELP_DESK, 'Incidencia', 'Parametros', "/help_desk/parameters"],
            [MODULE_HISTORY, '', '', "/history"],
        ];
        foreach ($routes as $route_config) {
            [$module, $scope, $submenu, $route] = $route_config;
            if (!role_can_read_module((int)$module, $id_role)) {
                continue;
            }
            if ($scope !== '' && !submenu_permission_allowed((int)$module, $scope, $submenu, $id_role)) {
                continue;
            }
            return base_url() . $route;
        }
        return base_url() . "/errors/forbidden";
    }

    function check_permissions(int $id_module){
        require_once ("Models/PermissionsModel.php");
        if(!empty($_SESSION['user_data'])){
            $id_role = $_SESSION['user_data']['id_role'];
            $data_permissions = role_permissions_map((int)$id_role);
            $permissions = [];
            $permissions_modules = [];
            if(count($data_permissions) > 0 ){
                $permissions = $data_permissions;
                $permissions_modules = isset($data_permissions[$id_module]) ? $data_permissions[$id_module] : [];
            }
            $_SESSION['permissions'] = $permissions;
            $_SESSION['module_permissions'] = $permissions_modules;
        }
    }

    function check_additional_permissions(int $id_module){
        require_once ("Models/AdditionalPermissionsModel.php");
        $object_additional_permissions = new AdditionalPermissionsModel();
        if(!empty($_SESSION['user_data'])){
            $id_role = $_SESSION['user_data']['id_role'];
            $data_additional_permissions = $object_additional_permissions->extract_additional_permissions($id_role, $id_module);
            $_SESSION['additional_permissions_module'] = $data_additional_permissions;
        }
    }



    function additional_control_allowed(int $id_module, string $name_control, ?int $id_role = null): bool
    {
        return RoleAccess::allowed($id_role ?? (int)($_SESSION['user_data']['id_role'] ?? 0), $id_module, $name_control);
    }

    function enforce_additional_control(int $id_module, string $name_control): void
    {
        if (!additional_control_allowed($id_module, $name_control)) {
            header("Location:" . base_url() . "/errors/forbidden");
            die();
        }
    }

    function additional_controls_allowed_map(int $id_module, ?int $id_role = null): array
    {
        return RoleAccess::controls($id_role ?? (int)($_SESSION['user_data']['id_role'] ?? 0), $id_module);
    }

    function submenu_control_name(string $scope, string $submenu): string
    {
        return 'Submenu: ' . trim($scope) . ': ' . trim($submenu);
    }

    function submenu_action_control_name(string $scope, string $submenu, string $action): string
    {
        return 'Submenu: ' . trim($scope) . ': ' . trim($submenu) . ': ' . trim($action);
    }


    function submenu_action_permission_allowed(int $id_module, string $scope, string $submenu, string $action = 'Ver', ?int $id_role = null): bool
    {
        $id_role = $id_role ?? (int)($_SESSION['user_data']['id_role'] ?? 0);
        if ($id_role === ROLE_SUPER_ADMINISTRATOR) {
            return true;
        }
        if ($id_role <= 0) {
            return false;
        }
        $action_permission = additional_control_allowed($id_module, submenu_action_control_name($scope, $submenu, $action), $id_role);
        if ($action_permission) {
            return true;
        }
        return $action === 'Ver' && additional_control_allowed($id_module, submenu_control_name($scope, $submenu), $id_role);
    }

    function submenu_permission_allowed(int $id_module, string $scope, string $submenu, ?int $id_role = null): bool
    {
        return submenu_action_permission_allowed($id_module, $scope, $submenu, 'Ver', $id_role);
    }
    function parameter_control_name(string $scope, string $resource, string $action): string
    {
        return 'Parametro: ' . trim($scope) . ': ' . trim($resource) . ': ' . trim($action);
    }

    function parameter_permission_allowed(int $id_module, string $scope, string $resource, string $action, ?int $id_role = null): bool
    {
        $id_role = $id_role ?? (int)($_SESSION['user_data']['id_role'] ?? 0);
        if ($id_role === ROLE_SUPER_ADMINISTRATOR) {
            return true;
        }
        if ($id_role <= 0) {
            return false;
        }
        return additional_control_allowed($id_module, parameter_control_name($scope, $resource, $action), $id_role);
    }

    function parameter_section_permission_allowed(int $id_module, string $scope, string $resource, string $action, ?int $id_role = null): bool
    {
        if (!submenu_permission_allowed($id_module, $scope, 'Parametros', $id_role)) {
            return false;
        }
        return parameter_permission_allowed($id_module, $scope, $resource, $action, $id_role);
    }

    function enforce_parameter_permission(int $id_module, string $scope, string $resource, string $action): void
    {
        if (!parameter_section_permission_allowed($id_module, $scope, $resource, $action)) {
            header("Location:" . base_url() . "/errors/forbidden");
            die();
        }
    }

    function parameter_action_map(int $id_module, string $scope, string $resource, ?int $id_role = null): array
    {
        $actions = ['Ver', 'Crear', 'Editar', 'Eliminar'];
        $map = [];
        foreach ($actions as $action) {
            $map[$action] = parameter_permission_allowed($id_module, $scope, $resource, $action, $id_role);
        }
        return $map;
    }

