<?php
class Asset extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        if (!empty($_SESSION['login'])) {
            check_user_status($_SESSION['user_data']['id_user']);
        }
    }

    public function data($code_inventory_asset)
    {
        $_SESSION['return_url'] = base_url() . "/scan_quick/asset/data/" . $code_inventory_asset;
        $publicAccess = $this->model->public_quick_access_status();
        $isPublicRequest = empty($_SESSION['login']) && !empty($publicAccess['is_active']);

        if (empty($_SESSION['login']) && !$isPublicRequest) {
            header("Location: " . base_url() . "/login");
            exit;
        }

        if (!empty($_SESSION['login']) && $_SESSION['status_active_user'] === 0) {
            header("Location: " . base_url() . "/account/logout");
            exit;
        }

        $data['quick_context'] = $this->model->quick_context($code_inventory_asset);
        $data['data_quick'] = $data['quick_context']['asset'];
        $data['is_public_quick_access'] = $isPublicRequest;
        $data['public_quick_access'] = $publicAccess;
        $data['page_functions_js'] = [
            "functions_scan_quick.js"
        ];
        $this->views->getView($this, "ScanQuick", "index", $data);
    }
}
