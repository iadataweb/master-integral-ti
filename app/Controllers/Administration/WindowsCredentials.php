<?php
class WindowsCredentials extends Controllers
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
			enforce_parameter_permission(MODULE_ADMINISTRATION, 'Administracion', 'Credenciales Windows', 'Ver');
    }

    public function manage_windows_credentials()
    {
        $this->ensureActiveUser();
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['r_read_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Credenciales Windows', 'Ver')) {
            header("Location:" . base_url() . '/errors/forbidden');
            die();
        }
        $data['css'] = ["/extensions/datatables/datatables.min.css"];
        $data['script'] = [
            "/js/language_datatable.js",
            "/extensions/datatables/datatables.min.js"
        ];
        $data['page_functions_js'] = ["functions_manage_windows_credentials.js"];
        $this->views->getView($this, "Administration", "index", $data);
    }

    public function show_windows_credentials()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['r_read_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Credenciales Windows', 'Ver')) {
            echo json_encode([]);
            die();
        }
        $data = $this->model->show_windows_credentials();
        foreach ($data as &$row) {
            $row['status_active_windows_credential'] = ((int)$row['status_active_windows_credential'] === 1)
                ? '<span class="badge bg-success">Activo</span>'
                : '<span class="badge bg-danger">Inactivo</span>';
            $row['password_updated_at_windows_credential'] = $row['password_updated_at_windows_credential'] ?: 'No definido';
            $buttons = '';
            if ($_SESSION['module_permissions']['r_read_permission']) {
                $buttons .= '<button type="button" class="btn icon btn-secondary js-btn-view" data-id="' . $row['id_windows_credential'] . '" title="Ver"><i class="bx bx-eye"></i></button> ';
                $buttons .= '<button type="button" class="btn icon btn-info js-btn-history" data-id="' . $row['id_windows_credential'] . '" title="Historial"><i class="bx bx-history"></i></button> ';
            }
            if ($_SESSION['module_permissions']['u_update_permission']) {
                $buttons .= '<button type="button" class="btn icon btn-primary js-btn-edit" data-id="' . $row['id_windows_credential'] . '" title="Editar"><i class="bx bx-pencil"></i></button> ';
            }
            if ($_SESSION['module_permissions']['d_delete_permission']) {
                $buttons .= '<button type="button" class="btn icon btn-danger js-btn-delete" data-id="' . $row['id_windows_credential'] . '" title="Eliminar"><i class="bx bx-trash"></i></button>';
            }
            $row['options'] = '<div class="text-center">' . trim($buttons) . '</div>';
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function show_windows_credential()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['r_read_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Credenciales Windows', 'Ver')) {
            $this->json(false, "error", "No tienes permisos para leer el registro.");
        }
        $id = (int)($_POST['id_windows_credential'] ?? 0);
        if ($id <= 0) {
            $this->json(false, "error", "No se recibio el identificador del registro.");
        }
        $item = $this->model->show_windows_credential($id);
        if (!$item) {
            $this->json(false, "error", "No se pudo leer el registro.");
        }
        echo json_encode(["status" => true, "result" => $item], JSON_UNESCAPED_UNICODE);
        die();
    }

    public function insert_windows_credential()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['w_write_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Credenciales Windows', 'Crear')) {
            $this->json(false, "error", "No tienes permisos para registrar.");
        }
        $data = $this->sanitizePayload(true);
        $request = $this->model->insert_windows_credential($data);
        $request ? $this->json(true, "success", "Registrado correctamente.") : $this->json(false, "error", "No se pudo registrar.");
    }

    public function update_windows_credential()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['u_update_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Credenciales Windows', 'Editar')) {
            $this->json(false, "error", "No tienes permisos para actualizar.");
        }
        $id = (int)($_POST['id_windows_credential'] ?? 0);
        if ($id <= 0) {
            $this->json(false, "error", "No se recibio el identificador del registro.");
        }
        $data = $this->sanitizePayload(false);
        $request = $this->model->update_windows_credential($id, $data);
        $request ? $this->json(true, "success", "Actualizado correctamente.") : $this->json(false, "error", "No se pudo actualizar.");
    }

    public function delete_windows_credential()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['d_delete_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Credenciales Windows', 'Eliminar')) {
            $this->json(false, "error", "No tienes permisos para eliminar.");
        }
        $id = (int)($_POST['id_windows_credential'] ?? 0);
        if ($id <= 0) {
            $this->json(false, "error", "No se recibio el identificador del registro.");
        }
        $request = $this->model->delete_windows_credential($id, (int)$_SESSION['user_data']['id_user']);
        $request ? $this->json(true, "success", "Eliminado correctamente.") : $this->json(false, "error", "No se pudo eliminar.");
    }

    public function show_windows_credential_history()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['r_read_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Credenciales Windows', 'Ver')) {
            $this->json(false, "error", "No tienes permisos para leer el historial.");
        }
        $id = (int)($_POST['id_windows_credential'] ?? 0);
        if ($id <= 0) {
            $this->json(false, "error", "No se recibio el identificador del registro.");
        }
        echo json_encode([
            "status" => true,
            "password_history" => $this->model->show_password_history($id)
        ], JSON_UNESCAPED_UNICODE);
        die();
    }

    public function asset_windows_credential_data()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_INVENTORY);
        if (empty($_SESSION['module_permissions']['r_read_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Credenciales Windows', 'Ver')) {
            $this->json(false, "error", "No tienes permisos para leer asignaciones del activo.");
        }
        $id_asset = (int)($_POST['id_asset'] ?? 0);
        if ($id_asset <= 0) {
            $this->json(false, "error", "No se pudo identificar el activo.");
        }
        echo json_encode([
            "status" => true,
            "available" => $this->model->available_asset_windows_credentials($id_asset),
            "current" => $this->model->asset_current_windows_credentials($id_asset),
            "history" => $this->model->asset_windows_credential_history($id_asset)
        ], JSON_UNESCAPED_UNICODE);
        die();
    }

    public function assign_asset_windows_credentials()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_INVENTORY);
        if (empty($_SESSION['module_permissions']['u_update_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Credenciales Windows', 'Editar')) {
            $this->json(false, "error", "No tienes permisos para asignar credenciales al activo.");
        }
        $id_asset = (int)($_POST['id_asset'] ?? 0);
        $ids = $_POST['id_windows_credential'] ?? [];
        $observation = trim($_POST['observation_asset_windows_credential_assignment'] ?? '');
        if ($id_asset <= 0 || empty($ids) || !is_array($ids)) {
            $this->json(false, "error", "Seleccione al menos una credencial Windows para asignar.");
        }
        $assigned = 0;
        foreach ($ids as $id_windows_credential) {
            $id_windows_credential = (int)$id_windows_credential;
            if ($id_windows_credential > 0 && $this->model->assign_asset_windows_credential($id_asset, $id_windows_credential, $observation, (int)$_SESSION['user_data']['id_user']) > 0) {
                $assigned++;
            }
        }
        $assigned > 0
            ? $this->json(true, "success", "Credenciales Windows asignadas correctamente: " . $assigned . ".")
            : $this->json(false, "error", "No se pudo asignar ninguna credencial Windows.");
    }

    public function release_asset_windows_credential()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_INVENTORY);
        if (empty($_SESSION['module_permissions']['u_update_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Credenciales Windows', 'Editar')) {
            $this->json(false, "error", "No tienes permisos para liberar credenciales del activo.");
        }
        $id_assignment = (int)($_POST['id_asset_windows_credential_assignment'] ?? 0);
        if ($id_assignment <= 0) {
            $this->json(false, "error", "No se pudo identificar la asignacion.");
        }
        $released = $this->model->release_asset_windows_credential($id_assignment, (int)$_SESSION['user_data']['id_user'], 'Liberacion desde el modulo de activos.');
        $released
            ? $this->json(true, "success", "Credencial Windows liberada correctamente.")
            : $this->json(false, "error", "No se pudo liberar la credencial Windows.");
    }
    private function sanitizePayload(bool $requirePassword): array
    {
        $name = trim($_POST['name_windows_credential'] ?? '');
        $network = trim($_POST['network_address_windows_credential'] ?? '');
        $username = trim($_POST['username_windows_credential'] ?? '');
        $password = trim($_POST['password_windows_credential'] ?? '');
        $description = trim($_POST['description_windows_credential'] ?? '');
        $status = isset($_POST['status_active_windows_credential']) ? (int)$_POST['status_active_windows_credential'] : 1;
        $updatedAt = trim($_POST['password_updated_at_windows_credential'] ?? '');
        if ($name === '' || $network === '' || $username === '') {
            $this->json(false, "error", "Complete nombre, direccion y usuario.");
        }
        if ($requirePassword && $password === '') {
            $this->json(false, "error", "Ingrese la contrasena.");
        }
        if ($updatedAt !== '') {
            $updatedAt = date('Y-m-d H:i:s', strtotime($updatedAt));
        } elseif ($password !== '') {
            $updatedAt = date('Y-m-d H:i:s');
        } else {
            $updatedAt = $_POST['current_password_updated_at_windows_credential'] ?? null;
        }
        return [
            "name" => $name,
            "network_address" => $network,
            "username" => $username,
            "password" => $password,
            "description" => $description,
            "password_updated_at" => $updatedAt,
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
