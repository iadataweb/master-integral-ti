<?php
class SpareParts extends Controllers
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

    public function manage_spare_parts()
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
        $data['page_functions_js'] = ["functions_manage_spare_parts.js"];
        $this->views->getView($this, "Maintenance", "index", $data);
    }

    public function show_spare_parts()
    {
        check_permissions(MODULE_MAINTENANCE);
        if (empty($_SESSION['module_permissions']['r_read_permission'])) {
            header("Location:" . base_url() . '/errors/forbidden');
            die();
        }
        $data = $this->model->show_spare_parts();
        foreach ($data as $i => $row) {
            $stock = floatval($row['stock_current_spare_part']);
            $minimum = floatval($row['stock_minimum_spare_part']);
            $data[$i]['stock_status'] = $stock <= $minimum
                ? '<span class="badge bg-warning text-dark">Stock bajo</span>'
                : '<span class="badge bg-success">Disponible</span>';
            $data[$i]['status_active_spare_part'] = intval($row['status_active_spare_part']) === 1
                ? '<span class="badge bg-success">Activo</span>'
                : '<span class="badge bg-danger">Inactivo</span>';
            $buttons = '';
            if ($_SESSION['module_permissions']['r_read_permission']) {
                $buttons .= '<button type="button" class="btn icon btn-secondary js-btn-view" data-id="' . $row['id_spare_part'] . '" title="Ver"><i class="bx bx-show-alt"></i></button> ';
            }
            if ($_SESSION['module_permissions']['u_update_permission']) {
                $buttons .= '<button type="button" class="btn icon btn-primary js-btn-edit" data-id="' . $row['id_spare_part'] . '" title="Editar"><i class="bx bx-pencil"></i></button> ';
                $buttons .= '<button type="button" class="btn icon btn-tertiary js-btn-stock" data-id="' . $row['id_spare_part'] . '" title="Movimiento de stock"><i class="bx bx-transfer"></i></button> ';
            }
            if ($_SESSION['module_permissions']['d_delete_permission']) {
                $buttons .= '<button type="button" class="btn icon btn-danger js-btn-delete" data-id="' . $row['id_spare_part'] . '" title="Eliminar"><i class="bx bx-trash"></i></button>';
            }
            $data[$i]['options'] = '<div class="text-center">' . $buttons . '</div>';
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function show_spare_part()
    {
        $id = intval($_POST['id_spare_part'] ?? 0);
        $result = $this->model->show_spare_part($id);
        if ($result) {
            echo json_encode([
                "status" => true,
                "result" => $result,
                "movements" => $this->model->show_movements($id)
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(["status" => false, "type" => "error", "message" => "No se pudo leer el repuesto."], JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function combo()
    {
        $items = $this->model->combo();
        $html = '<option selected disabled value="">Seleccione un repuesto</option>';
        foreach ($items as $item) {
            $label = $item['code_spare_part'] . ' - ' . $item['name_spare_part'] . ' (Stock: ' . number_format(floatval($item['stock_current_spare_part']), 2) . ' ' . $item['unit_spare_part'] . ')';
            $html .= '<option value="' . intval($item['id_spare_part']) . '" data-name="' . htmlspecialchars($item['name_spare_part'], ENT_QUOTES, 'UTF-8') . '" data-code="' . htmlspecialchars($item['code_spare_part'], ENT_QUOTES, 'UTF-8') . '" data-unit="' . htmlspecialchars($item['unit_spare_part'], ENT_QUOTES, 'UTF-8') . '" data-stock="' . floatval($item['stock_current_spare_part']) . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo $html;
        die();
    }

    public function insert_spare_part()
    {
        if (!$this->can_write()) {
            return;
        }
        if (empty(trim($_POST['name_spare_part'] ?? '')) || empty($_POST['id_brand'])) {
            echo json_encode(["status" => false, "type" => "error", "message" => "Ingrese el nombre del repuesto y seleccione una marca."], JSON_UNESCAPED_UNICODE);
            return;
        }
        $brand = $this->model->show_brand(intval($_POST['id_brand']));
        if (!$brand) {
            echo json_encode(["status" => false, "type" => "error", "message" => "Seleccione una marca válida."], JSON_UNESCAPED_UNICODE);
            return;
        }
        $id = $this->model->insert_spare_part(
            trim($_POST['name_spare_part']),
            intval($brand['id_brand']),
            trim($_POST['model_spare_part'] ?? '') ?: 'Ninguna.',
            trim($_POST['unit_spare_part'] ?? '') ?: 'unidad',
            floatval($_POST['stock_current_spare_part'] ?? 0),
            floatval($_POST['stock_minimum_spare_part'] ?? 0),
            trim($_POST['description_spare_part'] ?? '') ?: 'Ninguna.',
            intval($_POST['status_active_spare_part'] ?? 1)
        );
        echo json_encode($id ? ["status" => true, "type" => "success", "message" => "Registrado correctamente."] : ["status" => false, "type" => "error", "message" => "No se pudo registrar."], JSON_UNESCAPED_UNICODE);
        die();
    }

    public function update_spare_part()
    {
        if (!$this->can_update()) {
            return;
        }
        $id = intval($_POST['id_spare_part'] ?? 0);
        if ($id <= 0 || empty(trim($_POST['name_spare_part'] ?? '')) || empty($_POST['id_brand'])) {
            echo json_encode(["status" => false, "type" => "error", "message" => "Complete los datos requeridos."], JSON_UNESCAPED_UNICODE);
            return;
        }
        $brand = $this->model->show_brand(intval($_POST['id_brand']));
        if (!$brand) {
            echo json_encode(["status" => false, "type" => "error", "message" => "Seleccione una marca válida."], JSON_UNESCAPED_UNICODE);
            return;
        }
        $ok = $this->model->update_spare_part(
            $id,
            trim($_POST['name_spare_part']),
            intval($brand['id_brand']),
            trim($_POST['model_spare_part'] ?? '') ?: 'Ninguna.',
            trim($_POST['unit_spare_part'] ?? '') ?: 'unidad',
            floatval($_POST['stock_minimum_spare_part'] ?? 0),
            trim($_POST['description_spare_part'] ?? '') ?: 'Ninguna.',
            intval($_POST['status_active_spare_part'] ?? 1)
        );
        echo json_encode($ok ? ["status" => true, "type" => "success", "message" => "Actualizado correctamente."] : ["status" => false, "type" => "error", "message" => "No se pudo actualizar."], JSON_UNESCAPED_UNICODE);
        die();
    }

    public function register_stock_movement()
    {
        if (!$this->can_update()) {
            return;
        }
        $ok = $this->model->register_stock_movement(
            intval($_POST['id_spare_part'] ?? 0),
            trim($_POST['type_spare_part_movement'] ?? 'IN'),
            floatval($_POST['quantity_spare_part_movement'] ?? 0),
            trim($_POST['reason_spare_part_movement'] ?? '') ?: 'Movimiento manual'
        );
        echo json_encode($ok ? ["status" => true, "type" => "success", "message" => "Movimiento registrado correctamente."] : ["status" => false, "type" => "error", "message" => "No se pudo registrar el movimiento. Verifique el stock."], JSON_UNESCAPED_UNICODE);
        die();
    }

    public function delete_spare_part()
    {
        if (empty($_SESSION['module_permissions']['d_delete_permission'])) {
            echo json_encode(["status" => false, "type" => "error", "message" => "No tienes permisos para eliminar."], JSON_UNESCAPED_UNICODE);
            return;
        }
        $ok = $this->model->delete_spare_part(intval($_POST['id_spare_part'] ?? 0));
        echo json_encode($ok ? ["status" => true, "type" => "success", "message" => "Eliminado correctamente."] : ["status" => false, "type" => "error", "message" => "No se pudo eliminar."], JSON_UNESCAPED_UNICODE);
        die();
    }

    private function can_write(): bool
    {
        check_permissions(MODULE_MAINTENANCE);
        if (empty($_SESSION['module_permissions']['w_write_permission'])) {
            echo json_encode(["status" => false, "type" => "error", "message" => "No tienes permisos para registrar."], JSON_UNESCAPED_UNICODE);
            return false;
        }
        return true;
    }

    private function can_update(): bool
    {
        check_permissions(MODULE_MAINTENANCE);
        if (empty($_SESSION['module_permissions']['u_update_permission'])) {
            echo json_encode(["status" => false, "type" => "error", "message" => "No tienes permisos para actualizar."], JSON_UNESCAPED_UNICODE);
            return false;
        }
        return true;
    }
}
