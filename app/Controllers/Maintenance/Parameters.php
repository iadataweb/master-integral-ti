<?php

class Parameters extends Controllers
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

    public function parameters()
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

        $this->views->getView($this, "Maintenance", "index");
    }
}
