<?php
class LabelTemplates extends Controllers
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
			enforce_parameter_permission(MODULE_ADMINISTRATION, 'Administracion', 'Etiquetas', 'Ver');
    }

    public function manage_label_templates()
    {
        $this->ensureActiveUser();
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['r_read_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Etiquetas', 'Ver')) {
            header("Location:" . base_url() . '/errors/forbidden');
            die();
        }
        $data['css'] = ["/extensions/datatables/datatables.min.css"];
        $data['script'] = [
            "/js/language_datatable.js",
            "/extensions/datatables/datatables.min.js"
        ];
        $data['page_functions_js'] = ["functions_manage_label_templates.js"];
        $this->views->getView($this, "Administration", "index", $data);
    }

    public function show_label_templates()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['r_read_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Etiquetas', 'Ver')) {
            echo json_encode([]);
            die();
        }
        $data = $this->model->show_label_templates();
        foreach ($data as &$row) {
            $row['size_label_template'] = number_format((float)$row['width_cm_label_template'], 2) . ' x ' . number_format((float)$row['height_cm_label_template'], 2) . ' cm';
            $row['qr_position_label_template'] = 'X ' . number_format((float)$row['qr_x_percent_label_template'], 2) . '% / Y ' . number_format((float)$row['qr_y_percent_label_template'], 2) . '%';
            $row['code_position_label_template'] = 'X ' . number_format((float)($row['code_x_percent_label_template'] ?? 5), 2) . '% / Y ' . number_format((float)($row['code_y_percent_label_template'] ?? 75), 2) . '%';
            $row['status_active_label_template'] = ((int)$row['status_active_label_template'] === 1)
                ? '<span class="badge bg-success">Activo</span>'
                : '<span class="badge bg-danger">Inactivo</span>';
            $buttons = '';
            if ($_SESSION['module_permissions']['r_read_permission']) {
                $buttons .= '<button type="button" class="btn icon btn-secondary js-btn-view" data-id="' . $row['id_label_template'] . '" title="Ver"><i class="bx bx-eye"></i></button> ';
            }
            if ($_SESSION['module_permissions']['u_update_permission']) {
                $buttons .= '<button type="button" class="btn icon btn-primary js-btn-edit" data-id="' . $row['id_label_template'] . '" title="Editar"><i class="bx bx-pencil"></i></button> ';
            }
            if ($_SESSION['module_permissions']['d_delete_permission']) {
                $buttons .= '<button type="button" class="btn icon btn-danger js-btn-delete" data-id="' . $row['id_label_template'] . '" title="Eliminar"><i class="bx bx-trash"></i></button>';
            }
            $row['options'] = '<div class="text-center">' . trim($buttons) . '</div>';
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function combo_active()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_INVENTORY);
        $data = $this->model->active_label_templates();
        echo '<option selected value="">Seleccione una plantilla registrada</option>';
        foreach ($data as $row) {
            echo '<option value="' . (int)$row['id_label_template'] . '">' . htmlspecialchars($row['name_label_template'], ENT_QUOTES, 'UTF-8') . '</option>';
        }
        die();
    }

    public function active_templates_json()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_INVENTORY);
        echo json_encode($this->model->active_label_templates(), JSON_UNESCAPED_UNICODE);
        die();
    }

    public function show_label_template()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['r_read_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Etiquetas', 'Ver')) {
            $this->json(false, "error", "No tienes permisos para leer el registro.");
        }
        $id = (int)($_POST['id_label_template'] ?? 0);
        if ($id <= 0) {
            $this->json(false, "error", "No se recibio el identificador del registro.");
        }
        $item = $this->model->show_label_template($id);
        if (!$item) {
            $this->json(false, "error", "No se pudo leer el registro.");
        }
        echo json_encode(["status" => true, "result" => $item], JSON_UNESCAPED_UNICODE);
        die();
    }

    public function insert_label_template()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['w_write_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Etiquetas', 'Crear')) {
            $this->json(false, "error", "No tienes permisos para registrar.");
        }
        $data = $this->sanitizePayload(true);
        $request = $this->model->insert_label_template($data);
        $request ? $this->json(true, "success", "Plantilla registrada correctamente.") : $this->json(false, "error", "No se pudo registrar.");
    }

    public function update_label_template()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['u_update_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Etiquetas', 'Editar')) {
            $this->json(false, "error", "No tienes permisos para actualizar.");
        }
        $id = (int)($_POST['id_label_template'] ?? 0);
        if ($id <= 0) {
            $this->json(false, "error", "No se recibio el identificador del registro.");
        }
        $data = $this->sanitizePayload(false);
        $request = $this->model->update_label_template($id, $data);
        $request ? $this->json(true, "success", "Plantilla actualizada correctamente.") : $this->json(false, "error", "No se pudo actualizar.");
    }

    public function delete_label_template()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['d_delete_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Etiquetas', 'Eliminar')) {
            $this->json(false, "error", "No tienes permisos para eliminar.");
        }
        $id = (int)($_POST['id_label_template'] ?? 0);
        if ($id <= 0) {
            $this->json(false, "error", "No se recibio el identificador del registro.");
        }
        $request = $this->model->delete_label_template($id);
        $request ? $this->json(true, "success", "Plantilla eliminada correctamente.") : $this->json(false, "error", "No se pudo eliminar.");
    }

    private function sanitizePayload(bool $requireFile): array
    {
        $name = trim($_POST['name_label_template'] ?? '');
        $width = (float)($_POST['width_cm_label_template'] ?? 0);
        $height = (float)($_POST['height_cm_label_template'] ?? 0);
        $description = trim($_POST['description_label_template'] ?? '');
        $status = isset($_POST['status_active_label_template']) ? (int)$_POST['status_active_label_template'] : 1;
        $qrX = $this->clampPercent((float)($_POST['qr_x_percent_label_template'] ?? 60));
        $qrY = $this->clampPercent((float)($_POST['qr_y_percent_label_template'] ?? 20));
        $qrW = $this->clampPercent((float)($_POST['qr_width_percent_label_template'] ?? 30), 1, 100);
        $qrH = $this->clampPercent((float)($_POST['qr_height_percent_label_template'] ?? 45), 1, 100);
        $codeX = $this->clampPercent((float)($_POST['code_x_percent_label_template'] ?? 5));
        $codeY = $this->clampPercent((float)($_POST['code_y_percent_label_template'] ?? 75));
        $codeW = $this->clampPercent((float)($_POST['code_width_percent_label_template'] ?? 45), 1, 100);
        $codeH = $this->clampPercent((float)($_POST['code_height_percent_label_template'] ?? 12), 1, 100);
        $codeFontSize = max(4, min(24, (float)($_POST['code_font_size_label_template'] ?? 8))); 

        if ($name === '') {
            $this->json(false, "error", "Ingrese el nombre de la plantilla.");
        }
        if ($width <= 0 || $height <= 0) {
            $this->json(false, "error", "Ingrese ancho y alto de etiqueta validos.");
        }
        if ($requireFile && (empty($_FILES['design_label_template']) || (int)$_FILES['design_label_template']['size'] <= 0)) {
            $this->json(false, "error", "Adjunte el diseno base de la etiqueta en PNG.");
        }

        $route = $this->uploadDesignFile();
        return [
            "name" => $name,
            "width_cm" => $width,
            "height_cm" => $height,
            "description" => $description,
            "status_active" => $status,
            "design_route" => $route,
            "qr_x" => $qrX,
            "qr_y" => $qrY,
            "qr_width" => $qrW,
            "qr_height" => $qrH,
            "code_x" => $codeX,
            "code_y" => $codeY,
            "code_width" => $codeW,
            "code_height" => $codeH,
            "code_font_size" => $codeFontSize
        ];
    }

    private function uploadDesignFile(): ?string
    {
        if (empty($_FILES['design_label_template']) || (int)$_FILES['design_label_template']['size'] <= 0) {
            return null;
        }
        $file = $_FILES['design_label_template'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($extension !== 'png') {
            $this->json(false, "error", "El diseno base debe estar en formato PNG.");
        }
        if ((int)$file['size'] > 5 * 1024 * 1024) {
            $this->json(false, "error", "El archivo PNG no debe superar 5 MB.");
        }
        $uploadDir = 'Assets/uploads/label_templates/' . date('Ymd') . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $filename = 'label_template_' . date('His') . '_' . bin2hex(random_bytes(4)) . '.png';
        $path = $uploadDir . $filename;
        if (!move_uploaded_file($file['tmp_name'], $path)) {
            $this->json(false, "error", "No se pudo guardar el diseno base.");
        }
        return $path;
    }

    private function clampPercent(float $value, float $min = 0, float $max = 100): float
    {
        return max($min, min($max, $value));
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