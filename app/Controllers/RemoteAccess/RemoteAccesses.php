<?php
class RemoteAccesses extends Controllers
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

    public function manage_remote_accesses()
    {
        $this->ensureActiveUser();
        check_permissions(MODULE_REMOTE_ACCESS);
        if (empty($_SESSION['module_permissions']['r_read_permission'])) {
            header('Location:' . base_url() . '/errors/forbidden');
            die();
        }
        $data['css'] = ['/extensions/datatables/datatables.min.css'];
        $data['script'] = ['/js/language_datatable.js', '/extensions/datatables/datatables.min.js'];
        $data['page_functions_js'] = ['functions_manage_remote_accesses.js'];
        $this->views->getView($this, 'RemoteAccess', 'index', $data);
    }

    public function show_remote_accesses()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_REMOTE_ACCESS);
        if (empty($_SESSION['module_permissions']['r_read_permission'])) {
            echo json_encode([]);
            die();
        }
        $rows = $this->model->show_remote_accesses();
        foreach ($rows as &$row) {
            $row['status_active_remote_access'] = ((int)$row['status_active_remote_access'] === 1)
                ? '<span class="badge bg-success">Activo</span>'
                : '<span class="badge bg-danger">Inactivo</span>';
            $row['asset_summary'] = '<strong>' . $this->e($row['code_inventory_asset']) . '</strong><br><small class="text-muted">' . $this->e($row['name_type_asset'] ?: 'No definido') . ' - ' . $this->e($row['name_brand'] ?: 'Ninguna') . '</small>';
            $row['current_location'] = $row['current_location'] ?: 'No definido';
            $row['equipment_username'] = $row['equipment_username'] ?: 'No definido';
            $row['applications_summary'] = $this->renderApplicationsSummary($row['applications'] ?? []);
            $buttons = '';
            if (!empty($_SESSION['module_permissions']['r_read_permission'])) {
                $buttons .= '<button type="button" class="btn icon btn-secondary js-btn-view" data-id="' . (int)$row['id_remote_access'] . '" title="Ver"><i class="bx bx-eye"></i></button> ';
                $buttons .= '<button type="button" class="btn icon btn-info js-btn-history" data-id="' . (int)$row['id_remote_access'] . '" title="Historial"><i class="bx bx-history"></i></button> ';
            }
            if (!empty($_SESSION['module_permissions']['u_update_permission'])) {
                $buttons .= '<button type="button" class="btn icon btn-primary js-btn-edit" data-id="' . (int)$row['id_remote_access'] . '" title="Editar"><i class="bx bx-pencil"></i></button> ';
            }
            if (!empty($_SESSION['module_permissions']['d_delete_permission'])) {
                $buttons .= '<button type="button" class="btn icon btn-danger js-btn-delete" data-id="' . (int)$row['id_remote_access'] . '" title="Eliminar"><i class="bx bx-trash"></i></button>';
            }
            $row['options'] = '<div class="text-center">' . trim($buttons) . '</div>';
        }
        echo json_encode($rows, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function show_remote_access_applications()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_REMOTE_ACCESS);
        if (empty($_SESSION['module_permissions']['r_read_permission'])) {
            echo json_encode([]);
            die();
        }
        echo json_encode($this->model->show_remote_access_applications(), JSON_UNESCAPED_UNICODE);
        die();
    }

    public function show_remote_access()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_REMOTE_ACCESS);
        if (empty($_SESSION['module_permissions']['r_read_permission'])) {
            $this->json(false, 'error', 'No tienes permisos para leer el registro.');
        }
        $id = (int)($_POST['id_remote_access'] ?? 0);
        if ($id <= 0) {
            $this->json(false, 'error', 'No se recibio el identificador del registro.');
        }
        $item = $this->model->show_remote_access($id);
        if (!$item) {
            $this->json(false, 'error', 'No se pudo leer el registro.');
        }
        echo json_encode(['status' => true, 'result' => $item], JSON_UNESCAPED_UNICODE);
        die();
    }

    public function show_assets_for_remote_access()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_REMOTE_ACCESS);
        if (empty($_SESSION['module_permissions']['r_read_permission'])) {
            echo json_encode([]);
            die();
        }
        $currentRemoteAccessId = (int)($_GET['id_remote_access'] ?? $_POST['id_remote_access'] ?? 0);
        $rows = $this->model->show_assets_for_remote_access($currentRemoteAccessId);
        foreach ($rows as &$row) {
            $row['select_option'] = '<button type="button" class="btn icon btn-secondary js-select-asset-remote-access" title="Seleccionar"><i class="bx bx-check"></i></button>';
        }
        echo json_encode($rows, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function insert_remote_access()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_REMOTE_ACCESS);
        if (empty($_SESSION['module_permissions']['w_write_permission'])) {
            $this->json(false, 'error', 'No tienes permisos para registrar.');
        }
        $data = $this->sanitizePayload();
        $request = $this->model->insert_remote_access($data);
        if ($request === -1) {
            $this->json(false, 'warning', 'El activo seleccionado ya cuenta con un registro principal de acceso remoto. Use la opcion Editar para agregar o modificar aplicaciones.');
        }
        $request ? $this->json(true, 'success', 'Acceso remoto registrado correctamente.') : $this->json(false, 'error', 'No se pudo registrar.');
    }

    public function update_remote_access()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_REMOTE_ACCESS);
        if (empty($_SESSION['module_permissions']['u_update_permission'])) {
            $this->json(false, 'error', 'No tienes permisos para actualizar.');
        }
        $id = (int)($_POST['id_remote_access'] ?? 0);
        if ($id <= 0) {
            $this->json(false, 'error', 'No se recibio el identificador del registro.');
        }
        $data = $this->sanitizePayload();
        $request = $this->model->update_remote_access($id, $data);
        $request ? $this->json(true, 'success', 'Acceso remoto actualizado correctamente.') : $this->json(false, 'error', 'No se pudo actualizar. Verifique que el activo no tenga otro registro principal.');
    }

    public function delete_remote_access()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_REMOTE_ACCESS);
        if (empty($_SESSION['module_permissions']['d_delete_permission'])) {
            $this->json(false, 'error', 'No tienes permisos para eliminar.');
        }
        $id = (int)($_POST['id_remote_access'] ?? 0);
        if ($id <= 0) {
            $this->json(false, 'error', 'No se recibio el identificador del registro.');
        }
        $request = $this->model->delete_remote_access($id, (int)$_SESSION['user_data']['id_user']);
        $request ? $this->json(true, 'success', 'Acceso remoto eliminado correctamente.') : $this->json(false, 'error', 'No se pudo eliminar.');
    }

    public function show_remote_access_history()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_REMOTE_ACCESS);
        if (empty($_SESSION['module_permissions']['r_read_permission'])) {
            $this->json(false, 'error', 'No tienes permisos para leer el historial.');
        }
        $id = (int)($_POST['id_remote_access'] ?? 0);
        if ($id <= 0) {
            $this->json(false, 'error', 'No se recibio el identificador del registro.');
        }
        echo json_encode(['status' => true, 'history' => $this->model->show_remote_access_history($id)], JSON_UNESCAPED_UNICODE);
        die();
    }

    private function sanitizePayload(): array
    {
        $idAsset = (int)($_POST['id_asset_remote_access'] ?? 0);
        $status = isset($_POST['status_active_remote_access']) ? (int)$_POST['status_active_remote_access'] : 1;
        if ($idAsset <= 0) {
            $this->json(false, 'error', 'Seleccione el activo asociado al acceso remoto.');
        }
        $applications = $this->sanitizeApplications();
        return [
            'id_asset' => $idAsset,
            'status_active' => $status,
            'applications' => $applications,
            'id_user' => (int)$_SESSION['user_data']['id_user']
        ];
    }

    private function sanitizeApplications(): array
    {
        $ids = $_POST['id_remote_access_application_remote_access_detail'] ?? [];
        $codes = $_POST['access_code_remote_access_detail'] ?? [];
        $descriptions = $_POST['description_remote_access_detail'] ?? [];
        $observations = $_POST['observations_remote_access_detail'] ?? [];
        $statuses = $_POST['status_active_remote_access_detail'] ?? [];
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        $applications = [];
        $seen = [];
        foreach ($ids as $index => $idApplication) {
                $accessCode = trim($codes[$index] ?? '');
            if ($idApplication <= 0 && $accessCode === '') {
                continue;
            }
            if ($idApplication <= 0) {
                $this->json(false, 'error', 'Seleccione la aplicacion de acceso remoto en cada fila.');
            }
            if ($accessCode === '') {
                $this->json(false, 'error', 'Ingrese el codigo de acceso en cada aplicacion.');
            }
            if (isset($seen[$idApplication])) {
                $this->json(false, 'warning', 'No puede registrar la misma aplicacion dos veces para el mismo activo.');
            }
            $seen[$idApplication] = true;
            $applications[] = [
                'id_application' => $idApplication,
                'access_code' => $accessCode,
                'description' => trim($descriptions[$index] ?? ''),
                'observations' => trim($observations[$index] ?? ''),
                'status_active' => isset($statuses[$index]) ? (int)$statuses[$index] : 1
            ];
        }
        if (empty($applications)) {
            $this->json(false, 'error', 'Agregue al menos una aplicacion de acceso remoto.');
        }
        return $applications;
    }

    private function renderApplicationsSummary(array $applications): string
    {
        if (empty($applications)) {
            return '<span class="text-muted">Sin aplicaciones.</span>';
        }
        $html = '<div class="remote-app-summary">';
        foreach ($applications as $app) {
            $html .= '<div><span class="badge bg-primary me-1">' . $this->e($app['name_remote_access_application'] ?? 'Aplicacion') . '</span><span>' . $this->e($app['access_code_remote_access_detail'] ?? '') . '</span></div>';
        }
        $html .= '</div>';
        return $html;
    }

    private function ensureActiveUser()
    {
        if ($_SESSION['status_active_user'] === 0) {
            header('Location: ' . base_url() . '/account/logout');
            die();
        }
    }

    private function ensureActiveUserJson()
    {
        if ($_SESSION['status_active_user'] === 0) {
            $this->json(false, 'error', 'Tu cuenta de usuario no esta activa.');
        }
    }

    private function json(bool $status, string $type, string $message)
    {
        echo json_encode(['status' => $status, 'type' => $type, 'message' => $message], JSON_UNESCAPED_UNICODE);
        die();
    }

    private function e(?string $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}
