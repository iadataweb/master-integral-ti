<?php

class MaintenancesCorrective extends Controllers
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

    private function normalize_date_optional(?string $value): ?string
    {
        $value = trim($value ?? '');
        if ($value === '') {
            return null;
        }
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
            [$day, $month, $year] = explode('/', $value);
            return $year . '-' . $month . '-' . $day;
        }
        $timestamp = strtotime($value);
        return $timestamp ? date('Y-m-d', $timestamp) : null;
    }

    private function normalize_time_optional(?string $value): ?string
    {
        $value = trim($value ?? '');
        if ($value === '') {
            return null;
        }
        return preg_match('/^\d{2}:\d{2}/', $value) ? substr($value, 0, 5) : null;
    }

    private function read_spare_parts_payload(): array
    {
        $raw = $_POST['spare_parts_maintenance_corrective'] ?? '[]';
        $items = json_decode($raw, true);
        return is_array($items) ? $items : [];
    }

    public function manage_maintenances_corrective()
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
        $data['script'] = [
            "/js/language_datatable.js",
            "/js/upload_handler.js",
            "/extensions/datatables/datatables.min.js"
        ];
        $data['page_functions_js'] = [
            "functions_manage_maintenances_corrective.js",
            "functions_manage_company_responsible.js"
        ];
        $data['preset_id_incident'] = intval($_GET['id_incident'] ?? 0);
        $data['preset_id_asset'] = intval($_GET['id_asset'] ?? 0);
        $this->views->getView($this, "Maintenance", "index", $data);
    }

    public function show_maintenances_corrective()
    {
        if ($_SESSION['status_active_user'] === 0) {
            header("Location: " . base_url() . "/account/logout");
            die();
        }
        check_permissions(MODULE_MAINTENANCE);
        if (empty($_SESSION['module_permissions']['r_read_permission'])) {
            header("Location: " . base_url() . "/errors/forbidden");
            die();
        }

        $data = $this->model->show_maintenances_corrective();
        for ($i = 0; $i < count($data); $i++) {
            $btnView = '';
            $btnCompany = '';
            $btnHistory = '';

            if ($_SESSION['module_permissions']['r_read_permission']) {
                $btnView = '<button type="button" class="btn icon btn-secondary js-btn-view" data-id="' . $data[$i]['id_maintenance_corrective'] . '" title="Ver"><i class="bx bx-eye"></i></button>';
                $btnHistory = '<button type="button" class="btn icon btn-danger js-btn-history" data-id="' . $data[$i]['id_maintenance_corrective'] . '" title="Historial"><i class="bx bx-history"></i></button>';
            }
            if ($_SESSION['module_permissions']['u_update_permission']) {
                $btnEdit = '<button type="button" class="btn icon btn-primary js-btn-edit" data-id="' . $data[$i]['id_maintenance_corrective'] . '" title="Editar"><i class="bx bx-pencil"></i></button>';
                if ($data[$i]['name_type_responsible_maintenance'] === "Externo") {
                    $btnCompany = '<button type="button" class="btn icon btn-primary js-btn-company" data-id="' . $data[$i]['id_maintenance_corrective'] . '" title="Empresa"><i class="bx bxs-business"></i></button>';
                }
            }

            $data[$i]['options'] = '<div class="text-center">' . $btnView . ' ' . ($btnEdit ?? '') . ' ' . $btnCompany . ' ' . $btnHistory . '</div>';
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function history_maintenance_corrective()
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
        $data['script'] = [
            "/js/language_datatable.js",
            "/extensions/datatables/datatables.min.js"
        ];
        $data['page_functions_js'] = ["functions_history_maintenance_corrective.js"];
        $this->views->getView($this, "Maintenance", "history", $data);
    }

    public function show_history_maintenances_corrective()
    {
        if ($_SESSION['status_active_user'] === 0) {
            header("Location: " . base_url() . "/account/logout");
            die();
        }
        check_permissions(MODULE_MAINTENANCE);
        if (empty($_SESSION['module_permissions']['r_read_permission'])) {
            header("Location: " . base_url() . "/errors/forbidden");
            die();
        }

        $int_id_maintenance_corrective = intval($_POST['id_maintenance_corrective'] ?? 0);
        $data = $this->model->show_history_maintenances_corrective($int_id_maintenance_corrective);
        for ($i = 0; $i < count($data); $i++) {
            $btnView = '';
            if ($_SESSION['module_permissions']['r_read_permission']) {
                $btnView = '<button type="button" class="btn icon btn-secondary js-btn-view-history" data-id="' . $data[$i]['id_maintenance_corrective'] . '" title="Ver"><i class="bx bx-eye"></i></button>';
            }
            $data[$i]['options'] = '<div class="text-center">' . $btnView . '</div>';
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function show_history_maintenance_corrective_one()
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
        if (empty($_POST['id_history_maintenance_corrective'])) {
            echo json_encode(["status" => false, "type" => "error", "message" => "Ocurrio un error, por favor, reinicia el navegador."], JSON_UNESCAPED_UNICODE);
            return;
        }

        $id = intval($_POST['id_history_maintenance_corrective']);
        $result = $this->model->show_history_maintenance_corrective_one($id);
        if ($result) {
            echo json_encode(["status" => true, "result" => $result], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(["status" => false, "type" => "error", "message" => "No se pudo leer el registro."], JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function show_maintenance_corrective()
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
        if (empty($_POST['id_maintenance_corrective'])) {
            echo json_encode(["status" => false, "type" => "error", "message" => "Ocurrio un error, por favor, reinicia el navegador."], JSON_UNESCAPED_UNICODE);
            return;
        }

        $id = intval($_POST['id_maintenance_corrective']);
        $result = $this->model->show_maintenance_corrective($id);
        if ($result) {
            $attachments = $this->model->show_files($id);
            $spare_parts = $this->model->show_spare_parts_by_maintenance($id);
            echo json_encode([
                "status" => true,
                "result" => $result,
                "spare_parts" => $spare_parts,
                "attachments" => normalized_attachment($attachments, 'id_attachment_maintenance_corrective', 'name_attachment_maintenance_corrective', 'route_attachment_maintenance_corrective')
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(["status" => false, "type" => "error", "message" => "No se pudo leer el registro."], JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function show_origins()
    {
        if ($_SESSION['status_active_user'] === 0) {
            echo '<option selected disabled value="">Cuenta inactiva</option>';
            die();
        }
        check_permissions(MODULE_MAINTENANCE);
        if (empty($_SESSION['module_permissions']['r_read_permission'])) {
            echo '<option selected disabled value="">Sin permisos</option>';
            die();
        }
        $origins = $this->model->show_origins();
        $html = '<option selected disabled value="">Selecciona una opcion</option>';
        foreach ($origins as $origin) {
            $html .= '<option value="' . intval($origin['id_maintenance_corrective_origin']) . '">' . htmlspecialchars($origin['name_maintenance_corrective_origin'], ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo $html;
        die();
    }

    public function show_pending_incident_derivations()
    {
        if ($_SESSION['status_active_user'] === 0) {
            echo json_encode([], JSON_UNESCAPED_UNICODE);
            return;
        }
        check_permissions(MODULE_MAINTENANCE);
        if (empty($_SESSION['module_permissions']['r_read_permission'])) {
            echo json_encode([], JSON_UNESCAPED_UNICODE);
            return;
        }

        $data = $this->model->show_pending_incident_derivations();
        for ($i = 0; $i < count($data); $i++) {
            $id = intval($data[$i]['id_incident']);
            $btnView = '<a href="' . base_url() . '/help_desk/incidents/incident_details/' . $id . '" target="_blank" rel="noopener noreferrer" class="btn icon btn-secondary" title="Ver incidencia"><i class="bx bx-link-external"></i></a>';
            $btnAccept = '';
            $btnReject = '';
            if (!empty($_SESSION['module_permissions']['w_write_permission']) || !empty($_SESSION['module_permissions']['u_update_permission'])) {
                $btnAccept = '<button type="button" class="btn icon btn-success js-btn-accept-incident-derivation" data-id="' . $id . '" title="Aceptar y generar correctivo"><i class="bx bx-check"></i></button>';
                $btnReject = '<button type="button" class="btn icon btn-danger js-btn-reject-incident-derivation" data-id="' . $id . '" title="Rechazar derivacion"><i class="bx bx-x"></i></button>';
            }
            $data[$i]['options'] = '<div class="text-center">' . $btnView . ' ' . $btnAccept . ' ' . $btnReject . '</div>';
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function accept_incident_derivation()
    {
        if ($_SESSION['status_active_user'] === 0) {
            echo json_encode(["status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa."], JSON_UNESCAPED_UNICODE);
            return;
        }
        check_permissions(MODULE_MAINTENANCE);
        if (empty($_SESSION['module_permissions']['w_write_permission']) && empty($_SESSION['module_permissions']['u_update_permission'])) {
            echo json_encode(["status" => false, "type" => "error", "message" => "No tienes permisos para aceptar derivaciones."], JSON_UNESCAPED_UNICODE);
            return;
        }
        $id_incident = intval($_POST['id_incident'] ?? 0);
        if ($id_incident <= 0) {
            echo json_encode(["status" => false, "type" => "error", "message" => "No se recibio la incidencia."], JSON_UNESCAPED_UNICODE);
            return;
        }

        $id_maintenance = $this->model->accept_incident_derivation($id_incident, intval($_SESSION['user_data']['id_user']));
        if ($id_maintenance > 0) {
            echo json_encode(["status" => true, "type" => "success", "message" => "Derivacion aceptada. Se genero el mantenimiento correctivo #" . $id_maintenance . "."], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(["status" => false, "type" => "error", "message" => "No se pudo aceptar la derivacion. Verifique que siga pendiente y tenga activo relacionado."], JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function reject_incident_derivation()
    {
        if ($_SESSION['status_active_user'] === 0) {
            echo json_encode(["status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa."], JSON_UNESCAPED_UNICODE);
            return;
        }
        check_permissions(MODULE_MAINTENANCE);
        if (empty($_SESSION['module_permissions']['u_update_permission'])) {
            echo json_encode(["status" => false, "type" => "error", "message" => "No tienes permisos para rechazar derivaciones."], JSON_UNESCAPED_UNICODE);
            return;
        }
        $id_incident = intval($_POST['id_incident'] ?? 0);
        if ($id_incident <= 0) {
            echo json_encode(["status" => false, "type" => "error", "message" => "No se recibio la incidencia."], JSON_UNESCAPED_UNICODE);
            return;
        }

        $request = $this->model->reject_incident_derivation($id_incident, intval($_SESSION['user_data']['id_user']), trim($_POST['observation'] ?? ''));
        if ($request) {
            echo json_encode(["status" => true, "type" => "success", "message" => "Derivacion rechazada correctamente."], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(["status" => false, "type" => "error", "message" => "No se pudo rechazar la derivacion."], JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function insert_maintenance_corrective()
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

        if (empty($_POST['id_asset'])) {
            echo json_encode(["status" => false, "type" => "error", "message" => "Por favor, seleccione un activo."], JSON_UNESCAPED_UNICODE);
            return;
        }
        if (empty($_POST['id_type_responsible_maintenance'])) {
            echo json_encode(["status" => false, "type" => "error", "message" => "Por favor, seleccione el responsable."], JSON_UNESCAPED_UNICODE);
            return;
        }
        if (empty(trim($_POST['action_maintenance_corrective'] ?? ''))) {
            echo json_encode(["status" => false, "type" => "error", "message" => "Por favor, ingrese la accion realizada."], JSON_UNESCAPED_UNICODE);
            return;
        }

        $id_asset = intval($_POST['id_asset']);
        $id_incident = null;
        $id_origin = !empty($_POST['id_origin_maintenance_corrective']) ? intval($_POST['id_origin_maintenance_corrective']) : null;
        $date_repair = $this->normalize_date_optional($_POST['date_repair_maintenance_corrective'] ?? null);
        $time_repair = $this->normalize_time_optional($_POST['time_repair_maintenance_corrective'] ?? null);
        $id_responsible = intval($_POST['id_type_responsible_maintenance']);
        $action = trim($_POST['action_maintenance_corrective'] ?? '') ?: 'Ninguna.';
        $observation = trim($_POST['observation_maintenance_corrective'] ?? '') ?: 'Ninguna.';
        $status_active = intval($_POST['status_active_maintenance_corrective'] ?? 1) === 0 ? 0 : 1;
        $spare_parts = $this->read_spare_parts_payload();

        $files = isset($_FILES['document']) && $_FILES['document']['size'] > 0 ? $_FILES['document'] : null;
        if ($files) {
            $response = check_files($files, 10);
            if ($response != "succes") {
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                return;
            }
        }

        $id = $this->model->insert_maintenance_corrective(
            $id_asset,
            $id_incident,
            $id_origin,
            $date_repair,
            $time_repair,
            $id_responsible,
            $action,
            $observation,
            $spare_parts,
            $status_active
        );

        if ($id) {
            if ($files) {
                upload_attachments_maintenance_corrective($files, $id);
            }
            echo json_encode(["status" => true, "type" => "success", "message" => "Registrado correctamente."], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(["status" => false, "type" => "error", "message" => "No se pudo registrar. Verifique el stock disponible de los repuestos seleccionados."], JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function update_maintenance_corrective()
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

        if (empty($_POST['id_maintenance_corrective']) || empty($_POST['id_asset'])) {
            echo json_encode(["status" => false, "type" => "error", "message" => "Ocurrio un error, por favor, reinicia el navegador."], JSON_UNESCAPED_UNICODE);
            return;
        }
        if (empty($_POST['id_type_responsible_maintenance'])) {
            echo json_encode(["status" => false, "type" => "error", "message" => "Por favor, seleccione el responsable."], JSON_UNESCAPED_UNICODE);
            return;
        }
        if (empty(trim($_POST['action_maintenance_corrective'] ?? ''))) {
            echo json_encode(["status" => false, "type" => "error", "message" => "Por favor, ingrese la accion realizada."], JSON_UNESCAPED_UNICODE);
            return;
        }

        $id = intval($_POST['id_maintenance_corrective']);
        $id_asset = intval($_POST['id_asset']);
        $id_incident = $this->model->current_incident_id($id);
        $id_origin = !empty($_POST['id_origin_maintenance_corrective']) ? intval($_POST['id_origin_maintenance_corrective']) : null;
        $date_repair = $this->normalize_date_optional($_POST['date_repair_maintenance_corrective'] ?? null);
        $time_repair = $this->normalize_time_optional($_POST['time_repair_maintenance_corrective'] ?? null);
        $id_responsible = intval($_POST['id_type_responsible_maintenance']);
        $action = trim($_POST['action_maintenance_corrective'] ?? '') ?: 'Ninguna.';
        $observation = trim($_POST['observation_maintenance_corrective'] ?? '') ?: 'Ninguna.';
        $status_active = intval($_POST['status_active_maintenance_corrective'] ?? 1) === 0 ? 0 : 1;
        $spare_parts = $this->read_spare_parts_payload();

        $files = isset($_FILES['document']) && $_FILES['document']['size'] > 0 ? $_FILES['document'] : null;
        if ($files) {
            $response = check_files($files, 10);
            if ($response != "succes") {
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                return;
            }
        }

        $request = $this->model->update_maintenance_corrective(
            $id,
            $id_asset,
            $id_incident,
            $id_origin,
            $date_repair,
            $time_repair,
            $id_responsible,
            $action,
            $observation,
            $spare_parts,
            $status_active
        );

        if ($request) {
            if (!empty($_POST['delete_attachments'])) {
                $delete_attachments = $_POST['delete_attachments'];
                $request_attachments = $this->model->show_files($id);
                $attachments_map = [];
                foreach ($request_attachments as $item) {
                    $attachments_map[$item['id_attachment_maintenance_corrective']] = $item['route_attachment_maintenance_corrective'];
                }
                foreach ($delete_attachments as $aid) {
                    if (isset($attachments_map[$aid])) {
                        delete_file($attachments_map[$aid]);
                        $this->model->delete_attachment_maintenance_corrective($aid);
                    }
                }
            }
            if ($files) {
                upload_attachments_maintenance_corrective($files, $id);
            }
            echo json_encode(["status" => true, "type" => "success", "message" => "Actualizado correctamente."], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(["status" => false, "type" => "error", "message" => "No se pudo actualizar. Verifique el stock disponible de los repuestos seleccionados."], JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function show_files()
    {
        if ($_SESSION['status_active_user'] === 0) {
            header("Location: " . base_url() . "/account/logout");
            die();
        }
        $id = intval($_POST['id_maintenance_corrective'] ?? 0);
        $data['attachments_maintenance_corrective'] = $this->model->show_files($id);
        modal_folder_maintenance_corrective($data);
        die();
    }

    public function execute_maintenance_corrective()
    {
        if ($_SESSION['status_active_user'] === 0) {
            echo json_encode(["status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa."], JSON_UNESCAPED_UNICODE);
            return;
        }
        check_permissions(MODULE_MAINTENANCE);
        if (!$_SESSION['module_permissions']['u_update_permission']) {
            echo json_encode(["status" => false, "type" => "error", "message" => "No tienes permisos para registrar la atencion."], JSON_UNESCAPED_UNICODE);
            return;
        }

        if (empty($_POST['id_maintenance_corrective']) || empty($_POST['id_status_maintenance'])) {
            echo json_encode(["status" => false, "type" => "error", "message" => "Por favor, complete los datos requeridos."], JSON_UNESCAPED_UNICODE);
            return;
        }
        if (empty($_POST['action_history_maintenance_corrective']) || empty($_POST['observation_history_maintenance_corrective'])) {
            echo json_encode(["status" => false, "type" => "error", "message" => "Por favor, ingrese la accion y observacion."], JSON_UNESCAPED_UNICODE);
            return;
        }

        $request = $this->model->execute_maintenance_corrective(
            intval($_POST['id_maintenance_corrective']),
            intval($_POST['id_status_maintenance']),
            trim($_POST['action_history_maintenance_corrective']),
            trim($_POST['observation_history_maintenance_corrective'])
        );

        if ($request) {
            echo json_encode(["status" => true, "type" => "success", "message" => "Atencion registrada correctamente."], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(["status" => false, "type" => "error", "message" => "No se pudo registrar la atencion."], JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function relate_maintenance_corrective_company()
    {
        if ($_SESSION['status_active_user'] === 0) {
            echo json_encode(["status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa."], JSON_UNESCAPED_UNICODE);
            return;
        }
        check_permissions(MODULE_MAINTENANCE);
        if (empty($_SESSION['module_permissions']['u_update_permission'])) {
            echo json_encode(["status" => false, "type" => "error", "message" => "No tienes permisos para actualizar el registro."], JSON_UNESCAPED_UNICODE);
            return;
        }

        if (empty($_POST['id_maintenance_corrective']) || empty($_POST['id_company'])) {
            echo json_encode(["status" => false, "type" => "error", "message" => "Por favor, complete los datos requeridos."], JSON_UNESCAPED_UNICODE);
            return;
        }

        $request = $this->model->relate_maintenance_corrective_company(
            intval($_POST['id_maintenance_corrective']),
            intval($_POST['id_company'])
        );

        if ($request) {
            echo json_encode(["status" => true, "type" => "success", "message" => "Registrado correctamente."], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(["status" => false, "type" => "error", "message" => "No se pudo relacionar la empresa al mantenimiento correctivo."], JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function delete_maintenance_corrective()
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

        if (empty($_POST['id_maintenance_corrective'])) {
            echo json_encode(["status" => false, "type" => "error", "message" => "Ocurrio un error, por favor, reinicia el navegador."], JSON_UNESCAPED_UNICODE);
            return;
        }

        $request = $this->model->delete_maintenance_corrective(intval($_POST['id_maintenance_corrective']));
        if ($request) {
            echo json_encode(["status" => true, "type" => "success", "message" => "Eliminado correctamente."], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(["status" => false, "type" => "error", "message" => "No se pudo eliminar el registro."], JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}
