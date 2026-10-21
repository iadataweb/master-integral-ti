<?php

    class Parameters extends Controllers{
        
        public function __construct()
        {
            parent::__construct();
            if (session_status() !== PHP_SESSION_ACTIVE) session_start();
            if(empty($_SESSION['login']))
            {
                header('Location: '.base_url().'/login');
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
            check_permissions(MODULE_INVENTORY);
            if(empty($_SESSION['module_permissions']['r_read_permission'])){
                header("Location:".base_url().'/errors/forbidden');
                die();
            }
            $data['parameter_cards'] = $this->allowed_parameter_cards();
            $this->views->getView($this, "Inventory", "index", $data);
        }

        private function allowed_parameter_cards(): array
        {
            $cards = [
                ['title' => 'Categorias de Activo', 'description' => 'Administra categorias usadas para clasificar activos.', 'url' => '/inventory/categories_asset/manage_categories_asset', 'icon' => 'bx bx-category', 'scope' => 'Inventario', 'resource' => 'Categorias de Activo'],
                ['title' => 'Tipos de Activo', 'description' => 'Gestiona los tipos o clases de activos registrados.', 'url' => '/inventory/types_asset/manage_types_asset', 'icon' => 'bx bx-package', 'scope' => 'Inventario', 'resource' => 'Tipos de Activo'],
                ['title' => 'Marcas de Activo', 'description' => 'Administra marcas de equipos y dispositivos.', 'url' => '/inventory/brands/manage_brands', 'icon' => 'bx bx-purchase-tag', 'scope' => 'Inventario', 'resource' => 'Marcas de Activo'],
                ['title' => 'Condiciones de Activo', 'description' => 'Configura condiciones tecnicas de los activos.', 'url' => '/inventory/conditions_asset/manage_conditions_asset', 'icon' => 'bx bx-check-shield', 'scope' => 'Inventario', 'resource' => 'Condiciones de Activo'],
                ['title' => 'Tipos de Licencia', 'description' => 'Define tipos de licencia de software.', 'url' => '/inventory/types_license/manage_types_license', 'icon' => 'bx bx-certification', 'scope' => 'Inventario', 'resource' => 'Tipos de Licencia'],
                ['title' => 'Tipos de Software', 'description' => 'Gestiona clasificaciones de software.', 'url' => '/inventory/types_software/manage_types_software', 'icon' => 'bx bx-desktop', 'scope' => 'Inventario', 'resource' => 'Tipos de Software'],
                ['title' => 'Arquitecturas de Bit', 'description' => 'Administra arquitecturas de sistema y procesador.', 'url' => '/inventory/architectures/manage_architectures', 'icon' => 'bx bx-chip', 'scope' => 'Inventario', 'resource' => 'Arquitecturas de Bit'],
                ['title' => 'Tipos de Firmware', 'description' => 'Configura tipos de firmware registrados.', 'url' => '/inventory/types_firmware/manage_types_firmware', 'icon' => 'bx bx-cog', 'scope' => 'Inventario', 'resource' => 'Tipos de Firmware'],
                ['title' => 'Tipos de Interfaz de Red', 'description' => 'Administra tipos de interfaces de red.', 'url' => '/inventory/types_network_interface/manage_types_network_interface', 'icon' => 'bx bx-network-chart', 'scope' => 'Inventario', 'resource' => 'Tipos de Interfaz de Red'],
                ['title' => 'Versiones IP', 'description' => 'Configura versiones del protocolo de internet.', 'url' => '/inventory/versions_ip/manage_versions_ip', 'icon' => 'bx bx-globe', 'scope' => 'Inventario', 'resource' => 'Versiones IP'],
            ];

            if ((int)($_SESSION['user_data']['id_role'] ?? 0) === ROLE_SUPER_ADMINISTRATOR) {
                return $cards;
            }

            return array_values(array_filter($cards, function ($card) {
                return parameter_permission_allowed(MODULE_INVENTORY, $card['scope'], $card['resource'], 'Ver');
            }));
        }

    }
?>
