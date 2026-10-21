<?php
class OsUserAccounts extends Controllers
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
			enforce_parameter_permission(MODULE_ADMINISTRATION, 'Administracion', 'Cuentas de Usuario', 'Ver');
    }

    public function manage_os_user_accounts()
    {
        $this->ensureActiveUser();
        check_permissions(MODULE_ADMINISTRATION);
        if (!parameter_section_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Cuentas de Usuario', 'Ver')) {
            header("Location:" . base_url() . '/errors/forbidden');
            die();
        }
        $data['css'] = ["/extensions/datatables/datatables.min.css"];
        $data['script'] = [
            "/js/language_datatable.js",
            "/extensions/datatables/datatables.min.js"
        ];
        $data['page_functions_js'] = ["functions_manage_os_user_accounts.js"];
        $this->views->getView($this, "Administration", "index", $data);
    }

    public function show_os_user_accounts()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_ADMINISTRATION);
        if (!parameter_section_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Cuentas de Usuario', 'Ver')) {
            echo json_encode([]);
            die();
        }
        $data = $this->model->show_os_user_accounts();
        foreach ($data as &$row) {
            $row['status_active_os_user_account'] = ((int)$row['status_active_os_user_account'] === 1)
                ? '<span class="badge bg-success">Activo</span>'
                : '<span class="badge bg-danger">Inactivo</span>';
            $row['password_updated_at_os_user_account'] = $row['password_updated_at_os_user_account'] ?: 'No definido';
            $buttons = '';
            if (parameter_section_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Cuentas de Usuario', 'Ver')) {
                $buttons .= '<button type="button" class="btn icon btn-secondary js-btn-view" data-id="' . $row['id_os_user_account'] . '" title="Ver"><i class="bx bx-eye"></i></button> ';
                $buttons .= '<button type="button" class="btn icon btn-info js-btn-history" data-id="' . $row['id_os_user_account'] . '" title="Historial"><i class="bx bx-history"></i></button> ';
            }
            if (parameter_section_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Cuentas de Usuario', 'Editar')) {
                $buttons .= '<button type="button" class="btn icon btn-primary js-btn-edit" data-id="' . $row['id_os_user_account'] . '" title="Editar"><i class="bx bx-pencil"></i></button> ';
            }
            if (parameter_section_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Cuentas de Usuario', 'Eliminar')) {
                $buttons .= '<button type="button" class="btn icon btn-danger js-btn-delete" data-id="' . $row['id_os_user_account'] . '" title="Eliminar"><i class="bx bx-trash"></i></button>';
            }
            $row['options'] = '<div class="text-center">' . trim($buttons) . '</div>';
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function show_os_user_account()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_ADMINISTRATION);
        if (!parameter_section_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Cuentas de Usuario', 'Ver')) {
            $this->json(false, "error", "No tienes permisos para leer el registro.");
        }
        $id = (int)($_POST['id_os_user_account'] ?? 0);
        if ($id <= 0) {
            $this->json(false, "error", "No se recibio el identificador del registro.");
        }
        $item = $this->model->show_os_user_account($id);
        if (!$item) {
            $this->json(false, "error", "No se pudo leer el registro.");
        }
        echo json_encode(["status" => true, "result" => $item], JSON_UNESCAPED_UNICODE);
        die();
    }

    public function insert_os_user_account()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_ADMINISTRATION);
        if (!parameter_section_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Cuentas de Usuario', 'Crear')) {
            $this->json(false, "error", "No tienes permisos para registrar.");
        }
        $data = $this->sanitizePayload(true);
        $request = $this->model->insert_os_user_account($data);
        $request ? $this->json(true, "success", "Registrado correctamente.") : $this->json(false, "error", "No se pudo registrar.");
    }

    public function update_os_user_account()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_ADMINISTRATION);
        if (!parameter_section_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Cuentas de Usuario', 'Editar')) {
            $this->json(false, "error", "No tienes permisos para actualizar.");
        }
        $id = (int)($_POST['id_os_user_account'] ?? 0);
        if ($id <= 0) {
            $this->json(false, "error", "No se recibio el identificador del registro.");
        }
        $data = $this->sanitizePayload(false);
        $request = $this->model->update_os_user_account($id, $data);
        $request ? $this->json(true, "success", "Actualizado correctamente.") : $this->json(false, "error", "No se pudo actualizar.");
    }

    public function delete_os_user_account()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_ADMINISTRATION);
        if (!parameter_section_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Cuentas de Usuario', 'Eliminar')) {
            $this->json(false, "error", "No tienes permisos para eliminar.");
        }
        $id = (int)($_POST['id_os_user_account'] ?? 0);
        if ($id <= 0) {
            $this->json(false, "error", "No se recibio el identificador del registro.");
        }
        $request = $this->model->delete_os_user_account($id, (int)$_SESSION['user_data']['id_user']);
        $request ? $this->json(true, "success", "Eliminado correctamente.") : $this->json(false, "error", "No se pudo eliminar.");
    }

    public function show_os_user_account_history()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_ADMINISTRATION);
        if (!parameter_section_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Cuentas de Usuario', 'Ver')) {
            $this->json(false, "error", "No tienes permisos para leer el historial.");
        }
        $id = (int)($_POST['id_os_user_account'] ?? 0);
        if ($id <= 0) {
            $this->json(false, "error", "No se recibio el identificador del registro.");
        }
        echo json_encode([
            "status" => true,
            "password_history" => $this->model->show_password_history($id)
        ], JSON_UNESCAPED_UNICODE);
        die();
    }

    private function sanitizePayload(bool $requirePassword): array
    {
        $name = trim($_POST['name_os_user_account'] ?? '');
        $username = trim($_POST['username_os_user_account'] ?? '');
        $password = trim($_POST['password_os_user_account'] ?? '');
        $description = trim($_POST['description_os_user_account'] ?? '');
        $status = isset($_POST['status_active_os_user_account']) ? (int)$_POST['status_active_os_user_account'] : 1;
        $updatedAt = trim($_POST['password_updated_at_os_user_account'] ?? '');
        if ($name === '' || $username === '') {
            $this->json(false, "error", "Complete nombre de cuenta y usuario.");
        }
        if ($requirePassword && $password === '') {
            $this->json(false, "error", "Ingrese la contrasena.");
        }
        if ($updatedAt !== '') {
            $updatedAt = date('Y-m-d H:i:s', strtotime($updatedAt));
        } elseif ($password !== '') {
            $updatedAt = date('Y-m-d H:i:s');
        } else {
            $updatedAt = $_POST['current_password_updated_at_os_user_account'] ?? null;
        }
        return [
            "name" => $name,
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