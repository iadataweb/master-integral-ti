<?php

class Users extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }
        check_user_status((int)$_SESSION['user_data']['id_user']);
    }

    public function manage_users()
    {
        if ($_SESSION['status_active_user'] === 0) {
            header("Location: " . base_url() . "/account/logout");
            die();
        }
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['r_read_permission'])) {
            header("Location:" . base_url() . '/errors/forbidden');
            die();
        }
        $data['css'] = ["/css/styles.css", "/extensions/datatables/datatables.min.css"];
        $data['script'] = ["/js/language_datatable.js", "/extensions/datatables/datatables.min.js"];
        $data['page_functions_js'] = ["functions_manage_users.js"];
        $this->views->getView($this, "Administration", "index", $data);
    }

    public function show_users()
    {
        if ($_SESSION['status_active_user'] === 0) {
            header("Location: " . base_url() . "/account/logout");
            die();
        }
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['r_read_permission'])) {
            header("Location: " . base_url() . "/errors/forbidden");
            die();
        }
        $data = $this->model->show_users();
        for ($i = 0; $i < count($data); $i++) {
            $btnView = '';
            $btnEdit = '';
            $btnDelete = '';
            $isSuperUser = (int)$data[$i]['id_role_user'] === ROLE_SUPER_ADMINISTRATOR;
            $canManageSuper = $this->currentUserIsSuperAuthority();

            $data[$i]['status_active_user'] = (int)$data[$i]['status_active_user'] === 1
                ? '<span class="badge bg-success">Activo</span>'
                : '<span class="badge bg-danger">Inactivo</span>';

            if ($_SESSION['module_permissions']['r_read_permission']) {
                $btnView = '<button type="button" class="btn icon btn-secondary js-btn-view" data-id="' . $data[$i]['id_user'] . '" title="Ver"><i class="bx bx-eye"></i></button>';
            }
            if ($_SESSION['module_permissions']['u_update_permission']) {
                $btnEdit = ($isSuperUser && !$canManageSuper)
                    ? '<button type="button" class="btn icon btn-primary" disabled title="Super Administrador protegido"><i class="bx bx-pencil"></i></button>'
                    : '<button type="button" class="btn icon btn-primary js-btn-edit" data-id="' . $data[$i]['id_user'] . '" title="Editar"><i class="bx bx-pencil"></i></button>';
            }
            if ($_SESSION['module_permissions']['d_delete_permission']) {
                $btnDelete = $isSuperUser
                    ? '<button type="button" class="btn icon btn-danger" disabled title="Super Administrador protegido"><i class="bx bx-trash"></i></button>'
                    : '<button type="button" class="btn icon btn-danger js-btn-delete" data-id="' . $data[$i]['id_user'] . '" title="Eliminar"><i class="bx bx-trash"></i></button>';
            }
            $data[$i]['options'] = '<div class="text-center">' . $btnView . ' ' . $btnEdit . ' ' . $btnDelete . '</div>';
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function show_user()
    {
        if ($_SESSION['status_active_user'] === 0) {
            $this->json(false, "error", "Tu cuenta de usuario no esta activa.");
        }
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['r_read_permission'])) {
            $this->json(false, "error", "No tienes permisos para leer el registro.");
        }
        if (empty($_POST['id_user'])) {
            $this->json(false, "error", "Ocurrio un error, por favor, reinicia el navegador.");
        }
        $request_user = $this->model->show_user((int)$_POST['id_user']);
        if ($request_user) {
            echo json_encode(["status" => true, "result" => $request_user], JSON_UNESCAPED_UNICODE);
            die();
        }
        $this->json(false, "error", "No se pudo leer el registro.");
    }

    public function insert_user()
    {
        if ($_SESSION['status_active_user'] === 0) {
            $this->json(false, "error", "Tu cuenta de usuario no esta activa.");
        }
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['w_write_permission'])) {
            $this->json(false, "error", "No tienes permisos para registrar nuevo usuario.");
        }
        if (empty($_POST)) {
            $this->json(false, "error", "No se enviaron datos.");
        }
        if (empty($_POST['id_employee'])) {
            $this->json(false, "error", "Por favor, busca un empleado.");
        }
        if (empty($_POST['name_user'])) {
            $this->json(false, "error", "Por favor, ingrese el nombre de usuario.");
        }
        if (empty($_POST['password_user'])) {
            $this->json(false, "error", "Por favor, ingrese la contrasena.");
        }
        if (empty($_POST['id_role_user'])) {
            $this->json(false, "error", "Por favor, seleccione una opcion de rol.");
        }
        if (!isset($_POST['status_active_user'])) {
            $this->json(false, "error", "Por favor, seleccione una opcion de estado.");
        }

        $int_id_role_user = (int)$_POST['id_role_user'];
        if ($int_id_role_user === ROLE_SUPER_ADMINISTRATOR) {
            $this->json(false, "error", "Solo puede existir un usuario con el rol Super Administrador. Esta asignacion esta protegida.");
        }

        $int_id_employee = (int)$_POST['id_employee'];
        $str_name_user = trim((string)$_POST['name_user']);
        $str_password_user = password_hash(trim((string)$_POST['password_user']), PASSWORD_DEFAULT);
        $int_status_active_user = (int)$_POST['status_active_user'];

        if ($this->model->check_name_user_exists($str_name_user)) {
            $this->json(false, "error", "El nombre de usuario ya esta registrado.");
        }

        $request_user = $this->model->insert_user($int_id_employee, $str_name_user, $str_password_user, $int_id_role_user, $int_status_active_user);
        if ($request_user) {
            $this->json(true, "success", "Registrado correctamente.");
        }
        $this->json(false, "error", "No se pudo registrar nuevo usuario.");
    }

    public function update_user()
    {
        if ($_SESSION['status_active_user'] === 0) {
            $this->json(false, "error", "Tu cuenta de usuario no esta activa.");
        }
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['u_update_permission'])) {
            $this->json(false, "error", "No tienes permisos para actualizar el registro.");
        }
        if (empty($_POST)) {
            $this->json(false, "error", "No se enviaron datos.");
        }
        if (empty($_POST['id_user'])) {
            $this->json(false, "error", "Ocurrio un error, por favor, reinicia el navegador.");
        }

        $int_id_user = (int)$_POST['id_user'];
        $currentTarget = $this->model->basic_user($int_id_user);
        if (empty($currentTarget)) {
            $this->json(false, "error", "El usuario seleccionado no existe.");
        }
        $isTargetSuper = (int)$currentTarget['id_role_user'] === ROLE_SUPER_ADMINISTRATOR;

        if ($isTargetSuper && !$this->currentUserIsSuperAuthority()) {
            $this->json(false, "error", "El usuario Super Administrador no puede ser modificado por otros roles.");
        }
        if ($isTargetSuper && (int)$_SESSION['user_data']['id_user'] !== $int_id_user) {
            $this->json(false, "error", "El Super Administrador solo puede actualizar su propia cuenta.");
        }

        if (empty($_POST['id_employee'])) {
            $this->json(false, "error", "Por favor, busca un empleado.");
        }
        if (empty($_POST['name_user'])) {
            $this->json(false, "error", "Por favor, ingrese el nombre de usuario.");
        }
        if (empty($_POST['id_role_user'])) {
            $this->json(false, "error", "Por favor, seleccione una opcion de rol.");
        }
        if (!isset($_POST['status_active_user'])) {
            $this->json(false, "error", "Por favor, seleccione una opcion de estado.");
        }

        $int_id_role_user = (int)$_POST['id_role_user'];
        $int_status_active_user = (int)$_POST['status_active_user'];
        if ($int_id_role_user === ROLE_SUPER_ADMINISTRATOR && !$isTargetSuper) {
            $this->json(false, "error", "No se puede transferir ni asignar el rol Super Administrador a otro usuario.");
        }
        if ($isTargetSuper) {
            $int_id_role_user = ROLE_SUPER_ADMINISTRATOR;
            $int_status_active_user = 1;
        }
        if ($int_id_user === (int)$_SESSION['user_data']['id_user']) {
            if ($int_id_role_user !== (int)$currentTarget['id_role_user'] || $int_status_active_user !== 1) {
                $this->json(false, "error", "No puedes cambiar tu propio rol ni desactivar tu propia cuenta desde esta pantalla.");
            }
        }

        $str_password_user = trim((string)($_POST['password_user'] ?? '')) !== '' ? password_hash(trim((string)$_POST['password_user']), PASSWORD_DEFAULT) : '';
        $request_user = $this->model->update_user($int_id_user, (int)$_POST['id_employee'], trim((string)$_POST['name_user']), $str_password_user, $int_id_role_user, $int_status_active_user);
        if ($request_user) {
            $this->json(true, "success", "Actualizado correctamente.");
        }
        $this->json(false, "error", "No se pudo actualizar el registro.");
    }

    public function delete_user()
    {
        if ($_SESSION['status_active_user'] === 0) {
            $this->json(false, "error", "Tu cuenta de usuario no esta activa.");
        }
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['d_delete_permission'])) {
            $this->json(false, "error", "No tienes permisos para eliminar el registro.");
        }
        if (empty($_POST['id_user'])) {
            $this->json(false, "error", "Ocurrio un error, por favor, reinicia el navegador.");
        }

        $int_id_user = (int)$_POST['id_user'];
        $currentTarget = $this->model->basic_user($int_id_user);
        if (empty($currentTarget)) {
            $this->json(false, "error", "El usuario seleccionado no existe.");
        }
        if ((int)$currentTarget['id_role_user'] === ROLE_SUPER_ADMINISTRATOR) {
            $this->json(false, "error", "El Super Administrador es un usuario protegido y no puede ser eliminado.");
        }
        if ($int_id_user === (int)$_SESSION['user_data']['id_user']) {
            $this->json(false, "error", "No puedes eliminar tu propia cuenta de usuario.");
        }

        $request_user = $this->model->delete_user($int_id_user);
        if ($request_user) {
            $this->json(true, "success", "Eliminado correctamente.");
        }
        $this->json(false, "error", "No se pudo eliminar el registro.");
    }

    public function show_end_users()
    {
        $data = $this->model->show_end_users();
        $html = "<option selected disabled value=''>Selecciona una opcion</option>";
        if (is_array($data) && count($data) > 0) {
            foreach ($data as $row) {
                $html .= "<option value='" . $row['id_user'] . "'>" . $row['display_name'] . "</option>";
            }
        }
        echo $html;
    }

    private function currentUserIsSuperAuthority(): bool
    {
        return (int)($_SESSION['user_data']['id_role'] ?? 0) === ROLE_SUPER_ADMINISTRATOR;
    }







    private function json(bool $status, string $type, string $message): void
    {
        echo json_encode(["status" => $status, "type" => $type, "message" => $message], JSON_UNESCAPED_UNICODE);
        die();
    }
}
?>