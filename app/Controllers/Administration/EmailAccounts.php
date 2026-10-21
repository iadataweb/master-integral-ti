<?php
class EmailAccounts extends Controllers
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
			enforce_parameter_permission(MODULE_ADMINISTRATION, 'Administracion', 'Correos Electronicos', 'Ver');
    }

    public function manage_email_accounts()
    {
        $this->ensureActiveUser();
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['r_read_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Correos Electronicos', 'Ver')) {
            header("Location:" . base_url() . '/errors/forbidden');
            die();
        }
        $data['css'] = ["/extensions/datatables/datatables.min.css"];
        $data['script'] = [
            "/js/language_datatable.js",
            "/extensions/datatables/datatables.min.js"
        ];
        $data['page_functions_js'] = ["functions_manage_email_accounts.js"];
        $this->views->getView($this, "Administration", "index", $data);
    }

    public function show_email_accounts()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['r_read_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Correos Electronicos', 'Ver')) {
            echo json_encode([]);
            die();
        }
        $data = $this->model->show_email_accounts();
        foreach ($data as &$row) {
            $row['status_email_account'] = $this->statusBadge($row['status_email_account']);
            $buttons = '';
            if ($_SESSION['module_permissions']['r_read_permission']) {
                $buttons .= '<button type="button" class="btn icon btn-secondary js-btn-view" data-id="' . $row['id_email_account'] . '" title="Ver"><i class="bx bx-eye"></i></button> ';
                $buttons .= '<button type="button" class="btn icon btn-info js-btn-history" data-id="' . $row['id_email_account'] . '" title="Historial"><i class="bx bx-history"></i></button> ';
            }
            if ($_SESSION['module_permissions']['u_update_permission']) {
                $buttons .= '<button type="button" class="btn icon btn-primary js-btn-edit" data-id="' . $row['id_email_account'] . '" title="Editar"><i class="bx bx-pencil"></i></button> ';
            }
            if ($_SESSION['module_permissions']['d_delete_permission']) {
                $buttons .= '<button type="button" class="btn icon btn-danger js-btn-delete" data-id="' . $row['id_email_account'] . '" title="Eliminar"><i class="bx bx-trash"></i></button>';
            }
            $row['options'] = '<div class="text-center">' . trim($buttons) . '</div>';
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function show_email_account()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['r_read_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Correos Electronicos', 'Ver')) {
            $this->json(false, "error", "No tienes permisos para leer el registro.");
        }
        $id = (int)($_POST['id_email_account'] ?? 0);
        if ($id <= 0) {
            $this->json(false, "error", "No se recibio el identificador del registro.");
        }
        $item = $this->model->show_email_account($id);
        if (!$item) {
            $this->json(false, "error", "No se pudo leer el registro.");
        }
        echo json_encode(["status" => true, "result" => $item], JSON_UNESCAPED_UNICODE);
        die();
    }

    public function insert_email_account()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['w_write_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Correos Electronicos', 'Crear')) {
            $this->json(false, "error", "No tienes permisos para registrar.");
        }
        $data = $this->sanitizePayload(true);
        if ($this->model->email_exists($data['email'])) {
            $this->json(false, "error", "El correo electronico ya existe.");
        }
        $request = $this->model->insert_email_account($data);
        $request ? $this->json(true, "success", "Registrado correctamente.") : $this->json(false, "error", "No se pudo registrar.");
    }

    public function update_email_account()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['u_update_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Correos Electronicos', 'Editar')) {
            $this->json(false, "error", "No tienes permisos para actualizar.");
        }
        $id = (int)($_POST['id_email_account'] ?? 0);
        if ($id <= 0) {
            $this->json(false, "error", "No se recibio el identificador del registro.");
        }
        $data = $this->sanitizePayload(false);
        if ($this->model->email_exists($data['email'], $id)) {
            $this->json(false, "error", "El correo electronico ya existe.");
        }
        $request = $this->model->update_email_account($id, $data);
        $request ? $this->json(true, "success", "Actualizado correctamente.") : $this->json(false, "error", "No se pudo actualizar.");
    }

    public function delete_email_account()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['d_delete_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Correos Electronicos', 'Eliminar')) {
            $this->json(false, "error", "No tienes permisos para eliminar.");
        }
        $id = (int)($_POST['id_email_account'] ?? 0);
        if ($id <= 0) {
            $this->json(false, "error", "No se recibio el identificador del registro.");
        }
        $request = $this->model->delete_email_account($id, (int)$_SESSION['user_data']['id_user']);
        $request ? $this->json(true, "success", "Eliminado correctamente.") : $this->json(false, "error", "No se pudo eliminar.");
    }

    public function show_email_account_history()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['r_read_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Correos Electronicos', 'Ver')) {
            $this->json(false, "error", "No tienes permisos para leer el historial.");
        }
        $id = (int)($_POST['id_email_account'] ?? 0);
        if ($id <= 0) {
            $this->json(false, "error", "No se recibio el identificador del registro.");
        }
        echo json_encode([
            "status" => true,
            "password_history" => $this->model->show_password_history($id)
        ], JSON_UNESCAPED_UNICODE);
        die();
    }

    public function asset_email_account_data()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_INVENTORY);
        if (empty($_SESSION['module_permissions']['r_read_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Correos Electronicos', 'Ver')) {
            $this->json(false, "error", "No tienes permisos para leer correos asignados al activo.");
        }
        $id_asset = (int)($_POST['id_asset'] ?? 0);
        if ($id_asset <= 0) {
            $this->json(false, "error", "No se pudo identificar el activo.");
        }
        echo json_encode([
            "status" => true,
            "available" => $this->model->available_asset_email_accounts($id_asset),
            "current" => $this->model->asset_current_email_accounts($id_asset),
            "history" => $this->model->asset_email_account_history($id_asset)
        ], JSON_UNESCAPED_UNICODE);
        die();
    }

    public function assign_asset_email_accounts()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_INVENTORY);
        if (empty($_SESSION['module_permissions']['u_update_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Correos Electronicos', 'Editar')) {
            $this->json(false, "error", "No tienes permisos para asignar correos al activo.");
        }
        $id_asset = (int)($_POST['id_asset'] ?? 0);
        $ids = $_POST['id_email_account'] ?? [];
        $observation = trim($_POST['observation_asset_email_account_assignment'] ?? '');
        if ($id_asset <= 0 || empty($ids) || !is_array($ids)) {
            $this->json(false, "error", "Seleccione al menos un correo electronico para asignar.");
        }
        $assigned = 0;
        foreach ($ids as $id_email_account) {
            $id_email_account = (int)$id_email_account;
            if ($id_email_account > 0 && $this->model->assign_asset_email_account($id_asset, $id_email_account, $observation, (int)$_SESSION['user_data']['id_user']) > 0) {
                $assigned++;
            }
        }
        $assigned > 0
            ? $this->json(true, "success", "Correos electronicos asignados correctamente: " . $assigned . ".")
            : $this->json(false, "error", "No se pudo asignar ningun correo electronico.");
    }

    public function release_asset_email_account()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_INVENTORY);
        if (empty($_SESSION['module_permissions']['u_update_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Correos Electronicos', 'Editar')) {
            $this->json(false, "error", "No tienes permisos para liberar correos del activo.");
        }
        $id_assignment = (int)($_POST['id_asset_email_account_assignment'] ?? 0);
        if ($id_assignment <= 0) {
            $this->json(false, "error", "No se pudo identificar la asignacion.");
        }
        $released = $this->model->release_asset_email_account($id_assignment, (int)$_SESSION['user_data']['id_user'], 'Liberacion desde el modulo de activos.');
        $released
            ? $this->json(true, "success", "Correo electronico liberado correctamente.")
            : $this->json(false, "error", "No se pudo liberar el correo electronico.");
    }
    private function sanitizePayload(bool $requirePassword): array
    {
        $name = trim($_POST['name_email_account'] ?? '');
        $email = trim($_POST['email_email_account'] ?? '');
        $phone = trim($_POST['phone_email_account'] ?? '');
        $password = trim($_POST['password_email_account'] ?? '');
        $description = trim($_POST['description_email_account'] ?? '');
        $status = trim($_POST['status_email_account'] ?? 'active');
        if ($name === '' || $email === '') {
            $this->json(false, "error", "Complete nombre y correo electronico.");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(false, "error", "Ingrese un correo electronico valido.");
        }
        if ($requirePassword && $password === '') {
            $this->json(false, "error", "Ingrese la contrasena.");
        }
        if (!in_array($status, ['active', 'inactive', 'suspended'], true)) {
            $status = 'active';
        }
        return [
            "name" => $name,
            "email" => $email,
            "phone" => $phone,
            "password" => $password,
            "description" => $description,
            "status" => $status,
            "id_user" => (int)$_SESSION['user_data']['id_user']
        ];
    }

    private function statusBadge(string $status): string
    {
        $map = [
            'active' => '<span class="badge bg-success">Activo</span>',
            'inactive' => '<span class="badge bg-secondary">Inactivo</span>',
            'suspended' => '<span class="badge bg-warning text-dark">Suspendido</span>'
        ];
        return $map[$status] ?? '<span class="badge bg-secondary">No definido</span>';
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
