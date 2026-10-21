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
            check_permissions(MODULE_ADMINISTRATION);
            if(empty($_SESSION['module_permissions']['r_read_permission'])){
                header("Location:".base_url().'/errors/forbidden');
                die();
            }
            if (!submenu_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Parametros')) {
                header("Location:".base_url().'/errors/forbidden');
                die();
            }
            $data['parameter_cards'] = $this->allowed_parameter_cards();
            $this->views->getView($this, "Administration", "index", $data);
        }

        private function allowed_parameter_cards(): array
        {
            $cards = [
                [
                    'title' => 'Establecimientos',
                    'description' => 'Administra sedes o establecimientos institucionales.',
                    'url' => '/administration/campuses/manage_campuses',
                    'icon' => 'bx bx-buildings',
                    'scope' => 'Administracion',
                    'resource' => 'Establecimientos'
                ],
                [
                    'title' => 'Areas',
                    'description' => 'Gestiona areas internas de la institucion.',
                    'url' => '/administration/areas/manage_areas',
                    'icon' => 'bx bx-grid-alt',
                    'scope' => 'Administracion',
                    'resource' => 'Areas'
                ],
                [
                    'title' => 'Ambientes',
                    'description' => 'Define ambientes, oficinas y espacios fisicos.',
                    'url' => '/administration/environments/manage_environments',
                    'icon' => 'bx bx-layout',
                    'scope' => 'Administracion',
                    'resource' => 'Ambientes'
                ],
                [
                    'title' => 'Empresas',
                    'description' => 'Registra empresas, proveedores o entidades vinculadas.',
                    'url' => '/administration/companies/manage_companies',
                    'icon' => 'bx bx-building-house',
                    'scope' => 'Administracion',
                    'resource' => 'Empresas'
                ],
                [
                    'title' => 'Cargos',
                    'description' => 'Administra cargos laborales y funcionales.',
                    'url' => '/administration/positions/manage_positions',
                    'icon' => 'bx bx-briefcase',
                    'scope' => 'Administracion',
                    'resource' => 'Cargos'
                ],
                [
                    'title' => 'Titulos Profesionales',
                    'description' => 'Configura titulos profesionales del personal.',
                    'url' => '/administration/professional_titles/manage_professional_titles',
                    'icon' => 'bx bx-id-card',
                    'scope' => 'Administracion',
                    'resource' => 'Titulos Profesionales'
                ],
                [
                    'title' => 'Roles',
                    'description' => 'Gestiona roles, accesos y permisos del sistema.',
                    'url' => '/administration/roles/manage_roles',
                    'icon' => 'bx bx-shield-quarter',
                    'scope' => 'Administracion',
                    'resource' => 'Roles'
                ],
                [
                    'title' => 'Configuracion del Sistema',
                    'description' => 'Centraliza configuraciones generales y seguridad.',
                    'url' => '/administration/system_configuration/manage_system_configuration',
                    'icon' => 'bx bx-cog',
                    'scope' => 'Administracion',
                    'resource' => 'Configuracion del Sistema'
                ],
                [
                    'title' => 'Planos',
                    'description' => 'Administra planos institucionales para ubicacion.',
                    'url' => '/administration/floor_plans/manage_floor_plans',
                    'icon' => 'bx bx-map-alt',
                    'scope' => 'Administracion',
                    'resource' => 'Planos'
                ],
                [
                    'title' => 'Cuentas de Usuario',
                    'description' => 'Administra cuentas de acceso a sistemas operativos.',
                    'url' => '/administration/os_user_accounts/manage_os_user_accounts',
                    'icon' => 'bx bx-user-pin',
                    'scope' => 'Administracion',
                    'resource' => 'Cuentas de Usuario'
                ],
                [
                    'title' => 'Credenciales Windows',
                    'description' => 'Gestiona credenciales de acceso Windows.',
                    'url' => '/administration/windows_credentials/manage_windows_credentials',
                    'icon' => 'bx bx-lock-alt',
                    'scope' => 'Administracion',
                    'resource' => 'Credenciales Windows'
                ],
                [
                    'title' => 'Correos Electronicos',
                    'description' => 'Administra cuentas de correo institucionales.',
                    'url' => '/administration/email_accounts/manage_email_accounts',
                    'icon' => 'bx bx-envelope',
                    'scope' => 'Administracion',
                    'resource' => 'Correos Electronicos'
                ],
                [
                    'title' => 'Etiquetas',
                    'description' => 'Configura plantillas para etiquetas de activos.',
                    'url' => '/administration/label_templates/manage_label_templates',
                    'icon' => 'bx bx-purchase-tag-alt',
                    'scope' => 'Administracion',
                    'resource' => 'Etiquetas'
                ],
            ];

            if ((int)($_SESSION['user_data']['id_role'] ?? 0) === ROLE_SUPER_ADMINISTRATOR) {
                return $cards;
            }

            return array_values(array_filter($cards, function ($card) {
                return parameter_permission_allowed(MODULE_ADMINISTRATION, $card['scope'], $card['resource'], 'Ver');
            }));
        }

    }
?>
