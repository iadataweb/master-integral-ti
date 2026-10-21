<?php
class WifiNetworks extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }
        check_user_status($_SESSION['user_data']['id_user']);
    }

    public function manage_wifi_networks()
    {
        $this->ensureActiveUser();
        check_permissions(MODULE_NETWORKS);
        if ((empty($_SESSION['module_permissions']['r_read_permission']) || !submenu_action_permission_allowed(MODULE_NETWORKS, 'Redes', 'Wi-Fi', 'Ver'))) {
            header("Location:" . base_url() . '/errors/forbidden');
            die();
        }
        $data['css'] = ["/extensions/datatables/datatables.min.css"];
        $data['script'] = [
            "/js/language_datatable.js",
            "/extensions/datatables/datatables.min.js"
        ];
        $data['page_functions_js'] = ["functions_manage_wifi_networks.js"];
        $this->views->getView($this, "Inventory", "index", $data);
    }

    public function show_wifi_networks()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_NETWORKS);
        if ((empty($_SESSION['module_permissions']['r_read_permission']) || !submenu_action_permission_allowed(MODULE_NETWORKS, 'Redes', 'Wi-Fi', 'Ver'))) {
            echo json_encode([]);
            die();
        }
        $data = $this->model->show_wifi_networks();
        foreach ($data as &$row) {
            $row['status_active_wifi_network'] = ((int)$row['status_active_wifi_network'] === 1)
                ? '<span class="badge bg-success">Activo</span>'
                : '<span class="badge bg-danger">Inactivo</span>';
            $row['management_ip_wifi_network'] = $row['management_ip_wifi_network'] ?: 'No definido';
            $row['asset_summary'] = '<strong>' . $row['code_inventory_asset'] . '</strong><br><small class="text-muted">' . $row['name_type_asset'] . ' - ' . $row['name_brand'] . '</small>';
            $buttons = '';
            if (!empty($_SESSION['module_permissions']['r_read_permission']) && submenu_action_permission_allowed(MODULE_NETWORKS, 'Redes', 'Wi-Fi', 'Ver')) {
                $buttons .= '<button type="button" class="btn icon btn-secondary js-btn-view" data-id="' . $row['id_wifi_network'] . '" title="Ver"><i class="bx bx-eye"></i></button> ';
                $buttons .= '<button type="button" class="btn icon btn-info js-btn-history" data-id="' . $row['id_wifi_network'] . '" title="Historial"><i class="bx bx-history"></i></button> ';
            }
            if (!empty($_SESSION['module_permissions']['u_update_permission']) && submenu_action_permission_allowed(MODULE_NETWORKS, 'Redes', 'Wi-Fi', 'Editar')) {
                $buttons .= '<button type="button" class="btn icon btn-primary js-btn-edit" data-id="' . $row['id_wifi_network'] . '" title="Editar"><i class="bx bx-pencil"></i></button> ';
            }
            if (!empty($_SESSION['module_permissions']['d_delete_permission']) && submenu_action_permission_allowed(MODULE_NETWORKS, 'Redes', 'Wi-Fi', 'Eliminar')) {
                $buttons .= '<button type="button" class="btn icon btn-danger js-btn-delete" data-id="' . $row['id_wifi_network'] . '" title="Eliminar"><i class="bx bx-trash"></i></button>';
            }
            $row['options'] = '<div class="text-center">' . trim($buttons) . '</div>';
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function show_wifi_network()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_NETWORKS);
        if ((empty($_SESSION['module_permissions']['r_read_permission']) || !submenu_action_permission_allowed(MODULE_NETWORKS, 'Redes', 'Wi-Fi', 'Ver'))) {
            $this->json(false, "error", "No tienes permisos para leer el registro.");
        }
        $id = (int)($_POST['id_wifi_network'] ?? 0);
        if ($id <= 0) {
            $this->json(false, "error", "No se recibio el identificador del registro.");
        }
        $item = $this->model->show_wifi_network($id);
        if (!$item) {
            $this->json(false, "error", "No se pudo leer el registro.");
        }
        echo json_encode(["status" => true, "result" => $item], JSON_UNESCAPED_UNICODE);
        die();
    }

    public function show_assets_for_wifi()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_NETWORKS);
        if ((empty($_SESSION['module_permissions']['r_read_permission']) || !submenu_action_permission_allowed(MODULE_NETWORKS, 'Redes', 'Wi-Fi', 'Ver'))) {
            echo json_encode([]);
            die();
        }
        $data = $this->model->show_assets_for_wifi();
        foreach ($data as &$row) {
            $row['select_option'] = '<button type="button" class="btn icon btn-secondary js-select-asset-wifi" title="Seleccionar"><i class="bx bx-check"></i></button>';
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function insert_wifi_network()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_NETWORKS);
        if ((empty($_SESSION['module_permissions']['w_write_permission']) || !submenu_action_permission_allowed(MODULE_NETWORKS, 'Redes', 'Wi-Fi', 'Crear'))) {
            $this->json(false, "error", "No tienes permisos para registrar.");
        }
        $data = $this->sanitizePayload(true);
        $request = $this->model->insert_wifi_network($data);
        $request ? $this->json(true, "success", "Red WiFi registrada correctamente.") : $this->json(false, "error", "No se pudo registrar.");
    }

    public function update_wifi_network()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_NETWORKS);
        if ((empty($_SESSION['module_permissions']['u_update_permission']) || !submenu_action_permission_allowed(MODULE_NETWORKS, 'Redes', 'Wi-Fi', 'Editar'))) {
            $this->json(false, "error", "No tienes permisos para actualizar.");
        }
        $id = (int)($_POST['id_wifi_network'] ?? 0);
        if ($id <= 0) {
            $this->json(false, "error", "No se recibio el identificador del registro.");
        }
        $data = $this->sanitizePayload(false);
        $request = $this->model->update_wifi_network($id, $data);
        $request ? $this->json(true, "success", "Red WiFi actualizada correctamente.") : $this->json(false, "error", "No se pudo actualizar.");
    }

    public function delete_wifi_network()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_NETWORKS);
        if ((empty($_SESSION['module_permissions']['d_delete_permission']) || !submenu_action_permission_allowed(MODULE_NETWORKS, 'Redes', 'Wi-Fi', 'Eliminar'))) {
            $this->json(false, "error", "No tienes permisos para eliminar.");
        }
        $id = (int)($_POST['id_wifi_network'] ?? 0);
        if ($id <= 0) {
            $this->json(false, "error", "No se recibio el identificador del registro.");
        }
        $request = $this->model->delete_wifi_network($id, (int)$_SESSION['user_data']['id_user']);
        $request ? $this->json(true, "success", "Red WiFi eliminada correctamente.") : $this->json(false, "error", "No se pudo eliminar.");
    }

    public function show_wifi_network_history()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_NETWORKS);
        if ((empty($_SESSION['module_permissions']['r_read_permission']) || !submenu_action_permission_allowed(MODULE_NETWORKS, 'Redes', 'Wi-Fi', 'Ver'))) {
            $this->json(false, "error", "No tienes permisos para leer el historial.");
        }
        $id = (int)($_POST['id_wifi_network'] ?? 0);
        if ($id <= 0) {
            $this->json(false, "error", "No se recibio el identificador del registro.");
        }
        echo json_encode([
            "status" => true,
            "history" => $this->model->show_wifi_network_history($id)
        ], JSON_UNESCAPED_UNICODE);
        die();
    }

    private function sanitizePayload(bool $requirePassword): array
    {
        $idAsset = (int)($_POST['id_asset_wifi_network'] ?? 0);
        $managementIp = trim($_POST['management_ip_wifi_network'] ?? '');
        $ssid = trim($_POST['ssid_wifi_network'] ?? '');
        $password = trim($_POST['password_wifi_network'] ?? '');
        $description = trim($_POST['description_wifi_network'] ?? '');
        $status = isset($_POST['status_active_wifi_network']) ? (int)$_POST['status_active_wifi_network'] : 1;
        if ($idAsset <= 0) {
            $this->json(false, "error", "Seleccione el activo asociado a la red WiFi.");
        }
        if ($ssid === '') {
            $this->json(false, "error", "Ingrese el SSID o nombre de red.");
        }
        if ($requirePassword && $password === '') {
            $this->json(false, "error", "Ingrese la contrasena WiFi.");
        }
        if ($managementIp !== '' && !filter_var($managementIp, FILTER_VALIDATE_IP)) {
            $this->json(false, "error", "Ingrese una direccion IP de administracion valida.");
        }
        return [
            "id_asset" => $idAsset,
            "management_ip" => $managementIp,
            "ssid" => $ssid,
            "password" => $password,
            "description" => $description,
            "status_active" => $status,
            "id_user" => (int)$_SESSION['user_data']['id_user']
        ];
    }

    private function ensureActiveUser()
    {
        if ($_SESSION['status_active_user'] === 0) {
            header("Location: " . base_url() . "/account/logout");
            die();
        }
    }

    private function ensureActiveUserJson()
    {
        if ($_SESSION['status_active_user'] === 0) {
            $this->json(false, "error", "Tu cuenta de usuario no esta activa.");
        }
    }

    private function json(bool $status, string $type, string $message)
    {
        echo json_encode(["status" => $status, "type" => $type, "message" => $message], JSON_UNESCAPED_UNICODE);
        die();
    }
}