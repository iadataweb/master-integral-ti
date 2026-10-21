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
        check_permissions(MODULE_HELP_DESK);
        if (empty($_SESSION['module_permissions']['r_read_permission'])) {
            header("Location:" . base_url() . '/errors/forbidden');
            die();
        }

        $data['parameter_cards'] = $this->allowed_parameter_cards();
        $this->views->getView($this, "HelpDesk", "index", $data);
    }

    private function allowed_parameter_cards(): array
    {
        $cards = [
            [
                'title' => 'Tipos de Incidencia',
                'url' => '/help_desk/types_incident/manage_types_incidents',
                'icon' => 'clipboard',
                'scope' => 'Incidencia',
                'resource' => 'Tipos de Incidencia',
            ],
            [
                'title' => 'Prioridad de Incidencia',
                'url' => '/help_desk/priorities/manage_priorities',
                'icon' => 'tag',
                'scope' => 'Incidencia',
                'resource' => 'Prioridad de Incidencia',
            ],
        ];

        if ((int)($_SESSION['user_data']['id_role'] ?? 0) === ROLE_SUPER_ADMINISTRATOR) {
            return $cards;
        }

        return array_values(array_filter($cards, function ($card) {
            return parameter_permission_allowed(MODULE_HELP_DESK, $card['scope'], $card['resource'], 'Ver');
        }));
    }
}
