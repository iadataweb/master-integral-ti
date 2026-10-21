<?php

class ActionsMaintenancePreventive extends Controllers
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

    public function manage_actions_maintenance_preventive()
    {
        if ($_SESSION['status_active_user'] === 0) {
            header("Location: " . base_url() . "/account/logout");
            die();
        }
        check_permissions(MODULE_MAINTENANCE);
        if (empty($_SESSION['module_permissions']['r_read_permission'])) {
            header("Location:" . base_url() . '/errors/forbidden');
            die();
        }

        $data['css'] = ["/extensions/datatables/datatables.min.css"];
        $data['script'] = ["/js/language_datatable.js", "/extensions/datatables/datatables.min.js"];
        $data['page_functions_js'] = ["functions_manage_actions_maintenance_preventive.js"];
        $this->views->getView($this, "Maintenance", "index", $data);
    }

    public function show_actions_maintenance_preventive()
    {
        if ($_SESSION['status_active_user'] === 0) {
            header("Location: " . base_url() . "/account/logout");
            die();
        }
        check_permissions(MODULE_MAINTENANCE);
        if (empty($_SESSION['module_permissions']['r_read_permission'])) {
            header("Location:" . base_url() . '/errors/forbidden');
            die();
        }

        $data = $this->model->show_actions_maintenance_preventive();
        for ($i = 0; $i < count($data); $i++) {
            $btnView = '';
            $btnEdit = '';
            $btnDelete = '';

            if (intval($data[$i]['status_active_parameter_maintenance_preventive_action']) === 1) {
                $data[$i]['status_active_parameter_maintenance_preventive_action'] = '<span class="badge bg-success">Activo</span>';
            } else {
                $data[$i]['status_active_parameter_maintenance_preventive_action'] = '<span class="badge bg-danger">Inactivo</span>';
            }

            if ($_SESSION['module_permissions']['r_read_permission']) {
                $btnView = '<button type="button" class="btn icon btn-secondary js-btn-view" data-id="' . $data[$i]['id_parameter_maintenance_preventive_action'] . '" title="Ver"><i class="bx bx-eye"></i></button>';
            }
            if ($_SESSION['module_permissions']['u_update_permission']) {
                $btnEdit = '<button type="button" class="btn icon btn-primary js-btn-edit" data-id="' . $data[$i]['id_parameter_maintenance_preventive_action'] . '" title="Editar"><i class="bx bx-pencil"></i></button>';
            }
            if ($_SESSION['module_permissions']['d_delete_permission']) {
                $btnDelete = '<button type="button" class="btn icon btn-danger js-btn-delete" data-id="' . $data[$i]['id_parameter_maintenance_preventive_action'] . '" title="Eliminar"><i class="bx bx-trash"></i></button>';
            }

            $data[$i]['options'] = '<div class="text-center">' . $btnView . ' ' . $btnEdit . ' ' . $btnDelete . '</div>';
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function show_action_maintenance_preventive()
    {
        if ($_SESSION['status_active_user'] === 0) {
            echo json_encode(["status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa."], JSON_UNESCAPED_UNICODE);
            return;
        }
        check_permissions(MODULE_MAINTENANCE);
        if (!$_SESSION['module_permissions']['r_read_permission']) {
            echo json_encode(["status" => false, "type" => "error", "message" => "No tienes permisos para leer el registro."], JSON_UNESCAPED_UNICODE);
            return;
        }
        if (empty($_POST['id_parameter_maintenance_preventive_action'])) {
            echo json_encode(["status" => false, "type" => "error", "message" => "Ocurrio un error, por favor, reinicia el navegador."], JSON_UNESCAPED_UNICODE);
            return;
        }

        $id = intval($_POST['id_parameter_maintenance_preventive_action']);
        $result = $this->model->show_action_maintenance_preventive($id);
        if ($result) {
            echo json_encode(["status" => true, "result" => $result], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(["status" => false, "type" => "error", "message" => "No se pudo leer el registro."], JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function insert_action_maintenance_preventive()
    {
        if ($_SESSION['status_active_user'] === 0) {
            echo json_encode(["status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa."], JSON_UNESCAPED_UNICODE);
            return;
        }
        check_permissions(MODULE_MAINTENANCE);
        if (!$_SESSION['module_permissions']['w_write_permission']) {
            echo json_encode(["status" => false, "type" => "error", "message" => "No tienes permisos para registrar."], JSON_UNESCAPED_UNICODE);
            return;
        }

        $name = trim($_POST['name_parameter_maintenance_preventive_action'] ?? '');
        if ($name === '') {
            echo json_encode(["status" => false, "type" => "error", "message" => "Por favor, ingrese el nombre de la acción."], JSON_UNESCAPED_UNICODE);
            return;
        }
        if (!isset($_POST['status_active_parameter_maintenance_preventive_action'])) {
            echo json_encode(["status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de estado."], JSON_UNESCAPED_UNICODE);
            return;
        }

        $status = intval($_POST['status_active_parameter_maintenance_preventive_action']);
        $request = $this->model->insert_action_maintenance_preventive($name, $status);
        if ($request) {
            echo json_encode(["status" => true, "type" => "success", "message" => "Registrado correctamente."], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(["status" => false, "type" => "error", "message" => "No se pudo registrar la acción."], JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function update_action_maintenance_preventive()
    {
        if ($_SESSION['status_active_user'] === 0) {
            echo json_encode(["status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa."], JSON_UNESCAPED_UNICODE);
            return;
        }
        check_permissions(MODULE_MAINTENANCE);
        if (!$_SESSION['module_permissions']['u_update_permission']) {
            echo json_encode(["status" => false, "type" => "error", "message" => "No tienes permisos para actualizar el registro."], JSON_UNESCAPED_UNICODE);
            return;
        }

        if (empty($_POST['id_parameter_maintenance_preventive_action'])) {
            echo json_encode(["status" => false, "type" => "error", "message" => "Ocurrio un error, por favor, reinicia el navegador."], JSON_UNESCAPED_UNICODE);
            return;
        }

        $name = trim($_POST['name_parameter_maintenance_preventive_action'] ?? '');
        if ($name === '') {
            echo json_encode(["status" => false, "type" => "error", "message" => "Por favor, ingrese el nombre de la acción."], JSON_UNESCAPED_UNICODE);
            return;
        }
        if (!isset($_POST['status_active_parameter_maintenance_preventive_action'])) {
            echo json_encode(["status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de estado."], JSON_UNESCAPED_UNICODE);
            return;
        }

        $id = intval($_POST['id_parameter_maintenance_preventive_action']);
        $status = intval($_POST['status_active_parameter_maintenance_preventive_action']);
        $request = $this->model->update_action_maintenance_preventive($id, $name, $status);

        if ($request) {
            echo json_encode(["status" => true, "type" => "success", "message" => "Actualizado correctamente."], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(["status" => false, "type" => "error", "message" => "No se pudo actualizar el registro."], JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function delete_action_maintenance_preventive()
    {
        if ($_SESSION['status_active_user'] === 0) {
            echo json_encode(["status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa."], JSON_UNESCAPED_UNICODE);
            return;
        }
        check_permissions(MODULE_MAINTENANCE);
        if (!$_SESSION['module_permissions']['d_delete_permission']) {
            echo json_encode(["status" => false, "type" => "error", "message" => "No tienes permisos para eliminar el registro."], JSON_UNESCAPED_UNICODE);
            return;
        }
        if (empty($_POST['id_parameter_maintenance_preventive_action'])) {
            echo json_encode(["status" => false, "type" => "error", "message" => "Ocurrio un error, por favor, reinicia el navegador."], JSON_UNESCAPED_UNICODE);
            return;
        }

        $id = intval($_POST['id_parameter_maintenance_preventive_action']);
        $request = $this->model->delete_action_maintenance_preventive($id);
        if ($request) {
            echo json_encode(["status" => true, "type" => "success", "message" => "Eliminado correctamente."], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(["status" => false, "type" => "error", "message" => "No se pudo eliminar el registro."], JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function combo_checks()
    {
        if ($_SESSION['status_active_user'] === 0) {
            echo '<div class="text-danger small">Tu cuenta no está activa.</div>';
            return;
        }
        check_permissions(MODULE_MAINTENANCE);
        if (empty($_SESSION['module_permissions']['r_read_permission'])) {
            echo '<div class="text-danger small">Sin permisos para leer acciones.</div>';
            return;
        }

        $data = $this->model->combo_actions_maintenance_preventive();
        if (!is_array($data) || count($data) === 0) {
            echo '<div class="text-muted small">No hay acciones registradas en Parámetros.</div>';
            return;
        }

        $html = '';
        foreach ($data as $row) {
            $id = intval($row['id_parameter_maintenance_preventive_action']);
            $name = htmlspecialchars($row['name_parameter_maintenance_preventive_action'], ENT_QUOTES, 'UTF-8');
            $html .= '<div class="form-check mb-2">';
            $html .= '<input class="form-check-input js-action-check" type="checkbox" name="ids_actions_maintenance_preventive[]" value="' . $id . '" id="check-action-preventive-' . $id . '">';
            $html .= '<label class="form-check-label" for="check-action-preventive-' . $id . '">' . $name . '</label>';
            $html .= '</div>';
        }
        echo $html;
    }
}
