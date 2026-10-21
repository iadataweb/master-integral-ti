<?php
class SystemConfiguration extends Controllers
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
			enforce_parameter_permission(MODULE_ADMINISTRATION, 'Administracion', 'Configuracion del Sistema', 'Ver');
    }

    public function manage_system_configuration()
    {
        $this->ensureActiveUser();
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['r_read_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Configuracion del Sistema', 'Ver')) {
            header("Location:" . base_url() . '/errors/forbidden');
            die();
        }

        $data['public_access'] = $this->model->public_access_status();
        $data['page_functions_js'] = ["functions_system_configuration.js"];
        $this->views->getView($this, "Administration", "index", $data);
    }

    public function save_public_quick_access()
    {
        $this->ensureActiveUserJson();
        check_permissions(MODULE_ADMINISTRATION);
        if (empty($_SESSION['module_permissions']['u_update_permission']) && empty($_SESSION['module_permissions']['w_write_permission'])) {
            $this->json(false, "error", "No tienes permisos para actualizar la configuracion.");
        }

        $enabled = isset($_POST['public_quick_access_enabled']) ? 1 : 0;
        $mode = trim($_POST['public_quick_access_mode'] ?? 'indeterminate');
        $startsAt = trim($_POST['public_quick_access_starts_at'] ?? '');
        $expiresAt = trim($_POST['public_quick_access_expires_at'] ?? '');

        if (!in_array($mode, ['indeterminate', 'timed'], true)) {
            $this->json(false, "error", "Seleccione una vigencia valida.");
        }
        if ($mode === 'timed' && $enabled === 1) {
            if ($startsAt === '' || $expiresAt === '') {
                $this->json(false, "error", "Defina fecha y hora de inicio y expiracion.");
            }
            $startTs = strtotime($startsAt);
            $expiresTs = strtotime($expiresAt);
            if (!$startTs || !$expiresTs || $expiresTs <= $startTs) {
                $this->json(false, "error", "La fecha de expiracion debe ser mayor a la fecha de inicio.");
            }
            $startsAt = date('Y-m-d H:i:s', $startTs);
            $expiresAt = date('Y-m-d H:i:s', $expiresTs);
        } else {
            $startsAt = '';
            $expiresAt = '';
        }

        $this->model->update_public_access([
            'enabled' => $enabled,
            'mode' => $mode,
            'starts_at' => $startsAt,
            'expires_at' => $expiresAt
        ], (int)$_SESSION['user_data']['id_user']);

        $this->json(true, "success", "Configuracion de acceso publico actualizada correctamente.", [
            'public_access' => $this->model->public_access_status()
        ]);
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

    private function json(bool $status, string $type, string $message, array $extra = [])
    {
        echo json_encode(array_merge([
            "status" => $status,
            "type" => $type,
            "message" => $message
        ], $extra), JSON_UNESCAPED_UNICODE);
        die();
    }
}
?>
