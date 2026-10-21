<?php
class FloorPlans extends Controllers
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
			enforce_parameter_permission(MODULE_ADMINISTRATION, 'Administracion', 'Planos', 'Ver');
    }

    public function manage_floor_plans()
    {
        $this->ensureActiveUser();
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['r_read_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Planos', 'Ver')) {
            header("Location:" . base_url() . '/errors/forbidden');
            die();
        }
        $data['css'] = ["/extensions/datatables/datatables.min.css"];
        $data['script'] = [
            "/js/language_datatable.js",
            "/extensions/datatables/datatables.min.js"
        ];
        $data['page_functions_js'] = ["functions_manage_floor_plans.js"];
        $this->views->getView($this, "Administration", "index", $data);
    }

    public function show_floor_plans()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['r_read_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Planos', 'Ver')) {
            echo json_encode([]);
            die();
        }
        $data = $this->model->show_floor_plans();
        foreach ($data as &$row) {
            $row['status_active_floor_plan'] = ((int)$row['status_active_floor_plan'] === 1)
                ? '<span class="badge bg-success">Activo</span>'
                : '<span class="badge bg-secondary">Inactivo</span>';
            $row['file_link_floor_plan'] = !empty($row['current_file_floor_plan'])
                ? '<a class="btn btn-sm btn-outline-primary" href="' . base_url() . '/' . $row['current_file_floor_plan'] . '" target="_blank" rel="noopener noreferrer">Abrir</a>'
                : '<span class="text-muted">Sin archivo</span>';
            $buttons = '';
            if ($_SESSION['module_permissions']['r_read_permission']) {
                $buttons .= '<button type="button" class="btn icon btn-secondary js-btn-view" data-id="' . $row['id_floor_plan'] . '" title="Ver"><i class="bx bx-eye"></i></button> ';
            }
            if ($_SESSION['module_permissions']['u_update_permission']) {
                $buttons .= '<button type="button" class="btn icon btn-info js-btn-edit" data-id="' . $row['id_floor_plan'] . '" title="Editar"><i class="bx bx-pencil"></i></button> ';
            }
            if ($_SESSION['module_permissions']['d_delete_permission']) {
                $buttons .= '<button type="button" class="btn icon btn-danger js-btn-delete" data-id="' . $row['id_floor_plan'] . '" title="Eliminar"><i class="bx bx-trash"></i></button>';
            }
            $row['options'] = '<div class="text-center">' . trim($buttons) . '</div>';
        }
        unset($row);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function show_floor_plan()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['r_read_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Planos', 'Ver')) {
            $this->json(false, "error", "No tienes permisos para leer el registro.");
        }
        $id = (int)($_POST['id_floor_plan'] ?? 0);
        if ($id <= 0) {
            $this->json(false, "error", "No se recibio el identificador del plano.");
        }
        $item = $this->model->show_floor_plan($id);
        if (!$item) {
            $this->json(false, "error", "No se pudo leer el registro.");
        }
        echo json_encode([
            "status" => true,
            "result" => $item,
            "versions" => $this->model->show_versions($id)
        ], JSON_UNESCAPED_UNICODE);
        die();
    }

    public function combo()
    {
        $this->ensureActiveUserJson();
        $idCampus = isset($_POST['id_campus']) ? (int)$_POST['id_campus'] : null;
        $floor = isset($_POST['floor']) && $_POST['floor'] !== '' ? (int)$_POST['floor'] : null;
        $plans = $this->model->combo_floor_plans($idCampus, $floor);
        $html = '<option selected value="">Sin plano seleccionado</option>';
        foreach ($plans as $plan) {
            $label = $plan['name_floor_plan'] . ' - ' . $plan['name_campus'] . ' / Piso ' . $plan['floor_floor_plan'];
            $html .= '<option value="' . (int)$plan['id_floor_plan'] . '" data-file="' . htmlspecialchars($plan['current_file_floor_plan'] ?? '', ENT_QUOTES, 'UTF-8') . '" data-image="' . htmlspecialchars($plan['current_image_floor_plan'] ?? '', ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo $html;
        die();
    }

    public function active_plans()
    {
        $this->ensureActiveUserJson();
        echo json_encode([
            "status" => true,
            "result" => $this->model->combo_floor_plans()
        ], JSON_UNESCAPED_UNICODE);
        die();
    }

    public function campuses()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_ADMINISTRATION);
        echo json_encode([
            "status" => true,
            "result" => $this->model->campuses()
        ], JSON_UNESCAPED_UNICODE);
        die();
    }

    public function insert_floor_plan()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['w_write_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Planos', 'Crear')) {
            $this->json(false, "error", "No tienes permisos para registrar.");
        }
        $data = $this->sanitizePayload(true);
        $request = $this->model->insert_floor_plan($data);
        $request ? $this->json(true, "success", "Plano registrado correctamente.") : $this->json(false, "error", "No se pudo registrar el plano.");
    }

    public function update_floor_plan()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['u_update_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Planos', 'Editar')) {
            $this->json(false, "error", "No tienes permisos para actualizar.");
        }
        $id = (int)($_POST['id_floor_plan'] ?? 0);
        if ($id <= 0) {
            $this->json(false, "error", "No se recibio el identificador del plano.");
        }
        $data = $this->sanitizePayload(false);
        $request = $this->model->update_floor_plan($id, $data);
        $request ? $this->json(true, "success", "Plano actualizado correctamente.") : $this->json(false, "error", "No se pudo actualizar el plano.");
    }

    public function delete_floor_plan()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['d_delete_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Planos', 'Eliminar')) {
            $this->json(false, "error", "No tienes permisos para eliminar.");
        }
        $id = (int)($_POST['id_floor_plan'] ?? 0);
        if ($id <= 0) {
            $this->json(false, "error", "No se recibio el identificador del plano.");
        }
        $request = $this->model->delete_floor_plan($id, (int)$_SESSION['user_data']['id_user']);
        $request ? $this->json(true, "success", "Plano eliminado correctamente.") : $this->json(false, "error", "No se pudo eliminar el plano.");
    }

    private function sanitizePayload(bool $requireFile): array
    {
        $name = trim($_POST['name_floor_plan'] ?? '');
        $description = trim($_POST['description_floor_plan'] ?? '');
        $idCampus = (int)($_POST['id_campus_floor_plan'] ?? 0);
        $floor = trim($_POST['floor_floor_plan'] ?? '');
        $status = (int)($_POST['status_active_floor_plan'] ?? 1);
        if ($name === '' || $idCampus <= 0 || $floor === '') {
            $this->json(false, "error", "Complete nombre, sede y piso del plano.");
        }
        $fileData = $this->storePdf($requireFile);
        return [
            "name" => $name,
            "description" => $description,
            "id_campus" => $idCampus,
            "floor" => (int)$floor,
            "status_active" => $status === 1 ? 1 : 0,
            "file_route" => $fileData['route'],
            "file_name" => $fileData['name'],
            "image_route" => $fileData['image_route'],
            "image_name" => $fileData['image_name'],
            "image_width" => $fileData['image_width'],
            "image_height" => $fileData['image_height'],
            "id_user" => (int)$_SESSION['user_data']['id_user']
        ];
    }

    private function storePdf(bool $required): array
    {
        $empty = ["route" => "", "name" => "", "image_route" => "", "image_name" => "", "image_width" => null, "image_height" => null];
        if (empty($_FILES['file_floor_plan']) || empty($_FILES['file_floor_plan']['name'])) {
            if ($required) {
                $this->json(false, "error", "Adjunte el archivo PDF del plano.");
            }
            return $empty;
        }
        $file = $_FILES['file_floor_plan'];
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
            $this->json(false, "error", "No se pudo leer el archivo adjunto.");
        }
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $mime = mime_content_type($file['tmp_name']);
        if ($extension !== 'pdf' || !in_array($mime, ['application/pdf', 'application/octet-stream'], true)) {
            $this->json(false, "error", "Solo se permiten archivos PDF.");
        }
        $this->validateSinglePagePdf($file['tmp_name']);

        $dir = 'Assets/uploads/floor_plans/' . date('Ymd') . '/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($file['name']));
        $baseName = 'PLAN_' . date('YmdHis') . '_' . uniqid();
        $route = $dir . $baseName . '.pdf';
        if (!move_uploaded_file($file['tmp_name'], $route)) {
            $this->json(false, "error", "No se pudo guardar el archivo PDF.");
        }

        $image = $this->convertPdfToImage($route, $dir, $baseName);
        return [
            "route" => $route,
            "name" => $safeName,
            "image_route" => $image['route'],
            "image_name" => $image['name'],
            "image_width" => $image['width'],
            "image_height" => $image['height']
        ];
    }

    private function validateSinglePagePdf(string $pdfPath): void
    {
        $pdfInfo = $this->requireCommand('pdfinfo');
        $output = [];
        $code = 0;
        exec($pdfInfo . ' ' . escapeshellarg($pdfPath) . ' 2>&1', $output, $code);
        if ($code !== 0) {
            $this->json(false, "error", "No se pudo validar el PDF del plano. Verifique que el archivo no este danado.");
        }
        $info = implode("\n", $output);
        if (!preg_match('/Pages:\s+(\d+)/i', $info, $matches)) {
            $this->json(false, "error", "No se pudo identificar la cantidad de paginas del PDF.");
        }
        if ((int)$matches[1] !== 1) {
            $this->json(false, "error", "Solo se permiten planos PDF de una pagina.");
        }
    }

    private function convertPdfToImage(string $pdfRoute, string $dir, string $baseName): array
    {
        $converter = $this->requireCommand('pdftoppm');
        $absolutePdf = realpath($pdfRoute);
        $absoluteDir = realpath($dir);
        if (!$absolutePdf || !$absoluteDir) {
            @unlink($pdfRoute);
            $this->json(false, "error", "No se pudo preparar el archivo PDF para generar la imagen.");
        }
        $outputBase = $absoluteDir . DIRECTORY_SEPARATOR . $baseName;
        $output = [];
        $code = 0;
        exec($converter . ' -f 1 -l 1 -singlefile -r 150 -scale-to 2400 -png ' . escapeshellarg($absolutePdf) . ' ' . escapeshellarg($outputBase) . ' 2>&1', $output, $code);
        $imagePath = $outputBase . '.png';
        if ($code !== 0 || !is_file($imagePath)) {
            @unlink($pdfRoute);
            $this->json(false, "error", "No se pudo convertir el plano PDF a imagen. Verifique la configuracion del servidor.");
        }
        $this->optimizePng($imagePath);
        $size = @getimagesize($imagePath);
        if (!$size) {
            @unlink($pdfRoute);
            @unlink($imagePath);
            $this->json(false, "error", "La imagen generada del plano no es valida.");
        }
        $imageName = $baseName . '.png';
        return [
            "route" => $dir . $imageName,
            "name" => $imageName,
            "width" => (int)$size[0],
            "height" => (int)$size[1]
        ];
    }

    private function optimizePng(string $imagePath): void
    {
        if (!function_exists('imagecreatefrompng')) {
            return;
        }
        $image = @imagecreatefrompng($imagePath);
        if (!$image) {
            return;
        }
        imagesavealpha($image, true);
        imagepng($image, $imagePath, 6);
        imagedestroy($image);
    }

    private function requireCommand(string $command): string
    {
        if (!preg_match('/^[A-Za-z0-9_-]+$/', $command)) {
            $this->json(false, "error", "Comando de conversion no valido.");
        }
        $path = trim((string)shell_exec('command -v ' . $command . ' 2>/dev/null'));
        if ($path === '') {
            $this->json(false, "error", "El servidor no tiene habilitado el conversor de PDF a imagen. Instale poppler-utils y vuelva a intentar.");
        }
        return escapeshellcmd($path);
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
?>
