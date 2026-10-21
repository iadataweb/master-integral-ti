<?php
class NetworkSegmentations extends Controllers
{
    private const MAX_GENERATED_IPS = 4096;

    public function __construct()
    {
        parent::__construct();
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (empty($_SESSION['login'])) {
            header('Location: '.base_url().'/login');
            die();
        }
        check_user_status($_SESSION['user_data']['id_user']);
			if (!submenu_permission_allowed(MODULE_NETWORKS, 'Redes', 'Segmentos')) { header('Location: '.base_url().'/errors/forbidden'); die(); }
    }

    public function manage_network_segmentations()
    {
        if ($_SESSION['status_active_user'] === 0) {
            header('Location: ' . base_url() . '/account/logout');
            die();
        }
        check_permissions(MODULE_NETWORKS);
        if (empty($_SESSION['module_permissions']['r_read_permission']) || !submenu_action_permission_allowed(MODULE_NETWORKS, 'Redes', 'Segmentos', 'Ver')) {
            header('Location:' . base_url() . '/errors/forbidden');
            die();
        }
        $data['css'] = [
            '/extensions/datatables/datatables.min.css'
        ];
        $data['script'] = [
            '/js/language_datatable.js',
            '/extensions/datatables/datatables.min.js'
        ];
        $data['page_functions_js'] = [
            'functions_manage_network_segmentations.js'
        ];
        $this->views->getView($this, 'Inventory', 'index', $data);
    }

    public function show_network_segmentations()
    {
        if (!$this->can_read()) {
            echo json_encode([]);
            return;
        }
        $data = $this->model->show_network_segmentations();
        foreach ($data as $i => $row) {
            $data[$i]['status_active_network_segmentation'] = ((int)$row['status_active_network_segmentation'] === 1)
                ? '<span class="badge bg-success">Activo</span>'
                : '<span class="badge bg-danger">Inactivo</span>';
            $data[$i]['ip_resume'] = '<div class="network-ip-badges">'
                . '<span class="badge bg-primary">' . (int)$row['total_ips'] . ' IPs</span> '
                . '<span class="badge bg-success">' . (int)$row['available_ips'] . ' disp.</span> '
                . '<span class="badge bg-info">' . (int)$row['assigned_ips'] . ' asig.</span> '
                . '<span class="badge bg-warning text-dark">' . (int)$row['reserved_ips'] . ' res.</span> '
                . '<span class="badge bg-secondary">' . (int)$row['inactive_ips'] . ' inac.</span>'
                . '</div>';

            $btnView = '<button type="button" class="btn icon btn-secondary js-btn-view" data-id="'.$row['id_network_segmentation'].'" title="Ver"><i class="bx bx-eye"></i></button>';
            $btnIps = '<button type="button" class="btn icon btn-info js-btn-ips" data-id="'.$row['id_network_segmentation'].'" title="Direcciones IP"><i class="bx bx-network-chart"></i></button>';
            $btnEdit = !empty($_SESSION['module_permissions']['u_update_permission']) && submenu_action_permission_allowed(MODULE_NETWORKS, 'Redes', 'Segmentos', 'Editar')
                ? '<button type="button" class="btn icon btn-primary js-btn-edit" data-id="'.$row['id_network_segmentation'].'" title="Editar"><i class="bx bx-pencil"></i></button>'
                : '';
            $btnDelete = !empty($_SESSION['module_permissions']['d_delete_permission']) && submenu_action_permission_allowed(MODULE_NETWORKS, 'Redes', 'Segmentos', 'Eliminar')
                ? '<button type="button" class="btn icon btn-danger js-btn-delete" data-id="'.$row['id_network_segmentation'].'" title="Eliminar"><i class="bx bx-trash"></i></button>'
                : '';
            $data[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnIps.' '.$btnEdit.' '.$btnDelete.'</div>';
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function show_network_segmentation()
    {
        if (!$this->can_read_json()) {
            return;
        }
        $id = (int)($_POST['id_network_segmentation'] ?? 0);
        if ($id <= 0) {
            $this->json_response(false, 'error', 'No se pudo identificar la segmentacion.');
            return;
        }
        $segment = $this->model->show_network_segmentation($id);
        if (!$segment) {
            $this->json_response(false, 'error', 'No se pudo leer el registro.');
            return;
        }
        echo json_encode(['status' => true, 'result' => $segment], JSON_UNESCAPED_UNICODE);
    }

    public function insert_network_segmentation()
    {
        if (!$this->can_write_json()) {
            return;
        }
        $data = $this->validated_payload();
        if (!$data['status']) {
            $this->json_response(false, 'error', $data['message']);
            return;
        }
        $id = $this->model->insert_network_segmentation($data['payload']);
        if ($id <= 0) {
            $this->json_response(false, 'error', 'No se pudo registrar la segmentacion.');
            return;
        }
        $generated = $this->generate_ips($id, $data['payload']['ip_range_start'], $data['payload']['ip_range_end']);
        $this->json_response(true, 'success', 'Segmentacion registrada correctamente. IPs generadas: '.$generated.'.');
    }

    public function update_network_segmentation()
    {
        if (!$this->can_update_json()) {
            return;
        }
        $id = (int)($_POST['id_network_segmentation'] ?? 0);
        if ($id <= 0) {
            $this->json_response(false, 'error', 'No se pudo identificar la segmentacion.');
            return;
        }
        $data = $this->validated_payload();
        if (!$data['status']) {
            $this->json_response(false, 'error', $data['message']);
            return;
        }
        $updated = $this->model->update_network_segmentation($id, $data['payload']);
        if (!$updated) {
            $this->json_response(false, 'error', 'No se pudo actualizar la segmentacion.');
            return;
        }
        $generated = $this->generate_ips($id, $data['payload']['ip_range_start'], $data['payload']['ip_range_end']);
        $this->model->mark_out_of_range_inactive($id, $data['payload']['ip_range_start'], $data['payload']['ip_range_end']);
        $this->json_response(true, 'success', 'Segmentacion actualizada correctamente. IPs nuevas generadas: '.$generated.'.');
    }

    public function delete_network_segmentation()
    {
        if (!$this->can_delete_json()) {
            return;
        }
        $id = (int)($_POST['id_network_segmentation'] ?? 0);
        if ($id <= 0) {
            $this->json_response(false, 'error', 'No se pudo identificar la segmentacion.');
            return;
        }
        if ($this->model->has_assigned_ips($id)) {
            $this->json_response(false, 'error', 'No se puede eliminar porque tiene direcciones IP asignadas.');
            return;
        }
        $deleted = $this->model->delete_network_segmentation($id);
        $this->json_response($deleted, $deleted ? 'success' : 'error', $deleted ? 'Eliminado correctamente.' : 'No se pudo eliminar el registro.');
    }

    public function show_ips()
    {
        if (!$this->can_read_json()) {
            return;
        }
        $id = (int)($_POST['id_network_segmentation'] ?? 0);
        $ips = $id > 0 ? $this->model->show_ips_by_segmentation($id) : [];
        echo json_encode(['status' => true, 'result' => $ips], JSON_UNESCAPED_UNICODE);
    }

    public function update_ip_status()
    {
        if (!$this->can_update_json()) {
            return;
        }
        $id_ip = (int)($_POST['id_network_segmentation_ip'] ?? 0);
        $status = trim($_POST['status_network_segmentation_ip'] ?? '');
        $allowed = ['available', 'reserved', 'inactive'];
        if ($id_ip <= 0 || !in_array($status, $allowed, true)) {
            $this->json_response(false, 'error', 'Estado de IP no valido.');
            return;
        }
        if ($this->model->active_assignment_exists($id_ip)) {
            $this->json_response(false, 'error', 'No se puede cambiar el estado de una IP asignada.');
            return;
        }
        $updated = $this->model->update_ip_status($id_ip, $status);
        $this->json_response($updated, $updated ? 'success' : 'error', $updated ? 'Estado actualizado correctamente.' : 'No se pudo actualizar el estado.');
    }

    public function available_ips()
    {
        if (!$this->can_read_json()) {
            return;
        }
        echo json_encode(['status' => true, 'result' => $this->model->available_ips()], JSON_UNESCAPED_UNICODE);
    }

    public function asset_ip_data()
    {
        if (!$this->can_read_json()) {
            return;
        }
        $id_asset = (int)($_POST['id_asset'] ?? 0);
        if ($id_asset <= 0) {
            $this->json_response(false, 'error', 'No se pudo identificar el activo.');
            return;
        }
        echo json_encode([
            'status' => true,
            'current' => $this->model->asset_current_ips($id_asset),
            'history' => $this->model->asset_ip_history($id_asset),
            'segmentations' => $this->model->active_segmentations_for_assignment(),
            'available' => $this->model->available_ips(),
            'interface_types' => $this->model->active_interface_types()
        ], JSON_UNESCAPED_UNICODE);
    }

    public function assign_asset_ips()
    {
        if (!$this->can_update_json()) {
            return;
        }
        $id_asset = (int)($_POST['id_asset'] ?? 0);
        $ip_ids = $_POST['id_network_segmentation_ip'] ?? [];
        $observation = trim($_POST['observation_asset_ip_assignment'] ?? '');
        if ($id_asset <= 0 || empty($ip_ids) || !is_array($ip_ids)) {
            $this->json_response(false, 'error', 'Seleccione al menos una direccion IP para asignar.');
            return;
        }
        $id_type = filter_var($_POST['id_type_network_interface'] ?? null, FILTER_VALIDATE_INT);
        if (!$id_type || !$this->model->valid_interface_type($id_type)) {
            $this->json_response(false, 'error', 'Seleccione un tipo de interfaz activo.');
            return;
        }
        $assigned = 0;
        foreach ($ip_ids as $id_ip) {
            $id_ip = (int)$id_ip;
            if ($id_ip <= 0 || $this->model->active_assignment_exists($id_ip)) {
                continue;
            }
            $ip = $this->model->get_ip($id_ip);
            if (!$ip || !in_array($ip['status_network_segmentation_ip'], ['available', 'reserved'], true)) {
                continue;
            }
            if ($this->model->assign_asset_ip($id_asset, $id_ip, $observation, (int)$_SESSION['user_data']['id_user'], $id_type) > 0) {
                $assigned++;
            }
        }
        $this->json_response($assigned > 0, $assigned > 0 ? 'success' : 'error', $assigned > 0 ? 'Direcciones IP asignadas correctamente: '.$assigned.'.' : 'No se pudo asignar ninguna direccion IP.');
    }

    public function release_asset_ip()
    {
        if (!$this->can_update_json()) {
            return;
        }
        $id_assignment = (int)($_POST['id_asset_ip_assignment'] ?? 0);
        if ($id_assignment <= 0) {
            $this->json_response(false, 'error', 'No se pudo identificar la asignacion.');
            return;
        }
        $released = $this->model->release_asset_ip($id_assignment, (int)$_SESSION['user_data']['id_user'], 'Liberacion desde el modulo de activos.');
        $this->json_response($released, $released ? 'success' : 'error', $released ? 'Direccion IP liberada correctamente.' : 'No se pudo liberar la direccion IP.');
    }

    private function validated_payload(): array
    {
        $payload = [
            'name' => trim($_POST['name_network_segmentation'] ?? ''),
            'description' => trim($_POST['description_network_segmentation'] ?? '') ?: 'Ninguna.',
            'network_address' => trim($_POST['network_address_network_segmentation'] ?? ''),
            'subnet_mask' => trim($_POST['subnet_mask_network_segmentation'] ?? ''),
            'gateway' => trim($_POST['gateway_network_segmentation'] ?? ''),
            'dns' => trim($_POST['dns_network_segmentation'] ?? ''),
            'ip_range_start' => trim($_POST['ip_range_start_network_segmentation'] ?? ''),
            'ip_range_end' => trim($_POST['ip_range_end_network_segmentation'] ?? ''),
            'status_active' => (int)($_POST['status_active_network_segmentation'] ?? 1)
        ];

        if ($payload['name'] === '') {
            return ['status' => false, 'message' => 'Ingrese el nombre de la segmentacion.'];
        }
        foreach (['network_address', 'subnet_mask', 'ip_range_start', 'ip_range_end'] as $field) {
            if (!filter_var($payload[$field], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                return ['status' => false, 'message' => 'Verifique las direcciones IPv4 ingresadas.'];
            }
        }
        if ($payload['gateway'] !== '' && !filter_var($payload['gateway'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return ['status' => false, 'message' => 'Verifique la direccion gateway.'];
        }
        $start = $this->ip_to_long($payload['ip_range_start']);
        $end = $this->ip_to_long($payload['ip_range_end']);
        if ($start === false || $end === false || $start > $end) {
            return ['status' => false, 'message' => 'El rango de direcciones IP no es valido.'];
        }
        if (($end - $start + 1) > self::MAX_GENERATED_IPS) {
            return ['status' => false, 'message' => 'El rango supera el limite de '.self::MAX_GENERATED_IPS.' direcciones IP por segmentacion.'];
        }
        return ['status' => true, 'payload' => $payload];
    }

    private function generate_ips(int $id_network_segmentation, string $start_ip, string $end_ip): int
    {
        $start = $this->ip_to_long($start_ip);
        $end = $this->ip_to_long($end_ip);
        $created = 0;
        if ($start === false || $end === false) {
            return 0;
        }
        for ($ip = $start; $ip <= $end; $ip++) {
            $address = long2ip($ip);
            if (!$this->model->ip_exists($id_network_segmentation, $address)) {
                if ($this->model->insert_ip($id_network_segmentation, $address) > 0) {
                    $created++;
                }
            }
        }
        return $created;
    }

    private function ip_to_long(string $ip): int|false
    {
        $long = ip2long($ip);
        return $long === false ? false : (int)sprintf('%u', $long);
    }

    private function can_read(): bool
    {
        if ($_SESSION['status_active_user'] === 0) {
            return false;
        }
        check_permissions(MODULE_NETWORKS);
        return !empty($_SESSION['module_permissions']['r_read_permission']) && submenu_permission_allowed(MODULE_NETWORKS, 'Redes', 'Segmentos');
    }

    private function can_read_json(): bool
    {
        if (!$this->can_read()) {
            $this->json_response(false, 'error', 'No tienes permisos para leer el registro.');
            return false;
        }
        return true;
    }

    private function can_write_json(): bool
    {
        check_permissions(MODULE_NETWORKS);
        if (empty($_SESSION['module_permissions']['w_write_permission']) || !submenu_action_permission_allowed(MODULE_NETWORKS, 'Redes', 'Segmentos', 'Crear')) {
            $this->json_response(false, 'error', 'No tienes permisos para registrar.');
            return false;
        }
        return true;
    }

    private function can_update_json(): bool
    {
        check_permissions(MODULE_NETWORKS);
        if (empty($_SESSION['module_permissions']['u_update_permission']) || !submenu_action_permission_allowed(MODULE_NETWORKS, 'Redes', 'Segmentos', 'Editar')) {
            $this->json_response(false, 'error', 'No tienes permisos para actualizar.');
            return false;
        }
        return true;
    }

    private function can_delete_json(): bool
    {
        check_permissions(MODULE_NETWORKS);
        if (empty($_SESSION['module_permissions']['d_delete_permission']) || !submenu_action_permission_allowed(MODULE_NETWORKS, 'Redes', 'Segmentos', 'Eliminar')) {
            $this->json_response(false, 'error', 'No tienes permisos para eliminar.');
            return false;
        }
        return true;
    }

    private function json_response(bool $status, string $type, string $message): void
    {
        echo json_encode(['status' => $status, 'type' => $type, 'message' => $message], JSON_UNESCAPED_UNICODE);
    }
}
