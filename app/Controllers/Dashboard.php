<?php

class Dashboard extends Controllers
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

    public function dashboard()
    {
        $this->guard_active_user();
        
            check_permissions(MODULE_DASHBOARD);
            if (empty($_SESSION['module_permissions']['r_read_permission'])) {
                header('Location: ' . current_user_default_url());
                die();
            }
        

        $data['css'] = [
            '/css/iconly.css'
        ];
        $data['script'] = [
            '/extensions/apexcharts/apexcharts.min.js'
        ];
        $data['page_functions_js'] = [
            'functions_dashboard.js'
        ];
        $this->views->getView($this, '', 'index', $data);
    }

    public function dashboard_filters()
    {
        $this->guard_active_user_json();
        
            check_permissions(MODULE_DASHBOARD);
            if (empty($_SESSION['module_permissions']['r_read_permission'])) {
                $this->json_response([
                    'status' => false,
                    'type' => 'error',
                    'message' => 'No tienes permisos para visualizar filtros del dashboard.'
                ]);
            }
        
        $this->json_response([
            'status' => true,
            'result' => $this->model->dashboard_filters()
        ]);
    }

    public function dashboard_data()
    {
        $this->guard_active_user_json();
        
            check_permissions(MODULE_DASHBOARD);
            if (empty($_SESSION['module_permissions']['r_read_permission'])) {
                $this->json_response([
                    'status' => false,
                    'type' => 'error',
                    'message' => 'No tienes permisos para visualizar el dashboard.'
                ]);
            }
        

        $data = $this->model->dashboard_data($this->read_dashboard_filters(), null);
        $this->json_response([
            'status' => true,
            'result' => $data
        ]);
    }

    public function print_report()
    {
        $this->guard_active_user();
        
            check_permissions(MODULE_DASHBOARD);
            if (empty($_SESSION['module_permissions']['r_read_permission'])) {
                header('Location: ' . current_user_default_url());
                die();
            }
        

        $filters = $this->read_dashboard_filters();
        $data = $this->model->dashboard_report_data($filters, null);
        $filterLabels = [
            'Período: '.$this->report_period($data['filters'])
        ];
        require_once __DIR__.'/../Libraries/Reports/DashboardReport.php';
        $pdf = new DashboardReport();
        $pdf->render($data,$filterLabels,(string)($_SESSION['user_data']['display_name'] ?? 'Usuario del sistema'));
        $pdf->Output('reporte-gestion-ti.pdf', 'I');
        die();
    }

    public function show_category_data()
    {
        $this->guard_active_user_json();
        check_permissions(MODULE_DASHBOARD);
        if (empty($_SESSION['module_permissions']['r_read_permission'])) {
            $this->json_response(['status'=>false,'type'=>'error','message'=>'No tienes permisos para visualizar el dashboard.']);
        }
        $data = $this->model->dashboard_data([], null);
        $chart = $data['charts']['incidents_by_type'];
        $total = array_sum($chart['values']);
        $result = [];
        foreach ($chart['labels'] as $index => $label) {
            $value = (int)($chart['values'][$index] ?? 0);
            $result[] = [
                'name_category' => $label,
                'total' => $value,
                'porcentaje' => $total > 0 ? round(($value / $total) * 100, 1) : 0,
            ];
        }
        $this->json_response(['result' => $result]);
    }



    private function read_dashboard_filters(): array
    {
        return [
            'date_start' => trim((string)($_POST['date_start'] ?? $_GET['date_start'] ?? '')),
            'date_end' => trim((string)($_POST['date_end'] ?? $_GET['date_end'] ?? '')),
            'status' => trim((string)($_POST['status'] ?? $_GET['status'] ?? 'all')),
            'asset_category' => (int)($_POST['asset_category'] ?? $_GET['asset_category'] ?? 0),
            'incident_type' => (int)($_POST['incident_type'] ?? $_GET['incident_type'] ?? 0),
        ];
    }

    private function guard_active_user(): void
    {
        if ($_SESSION['status_active_user'] === 0) {
            header('Location: ' . base_url() . '/account/logout');
            die();
        }
    }

    private function guard_active_user_json(): void
    {
        if ($_SESSION['status_active_user'] === 0) {
            $this->json_response([
                'status' => false,
                'type' => 'error',
                'message' => 'Tu cuenta de usuario no esta activa.'
            ]);
        }
    }

    private function json_response(array $response): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        die();
    }

    private function report_period(array $filters): string
    {
        if (empty($filters['date_start']) && empty($filters['date_end'])) return 'Sin límite de fechas';
        $start = !empty($filters['date_start']) ? $this->report_date($filters['date_start'], false) : 'Sin fecha inicial';
        $end = !empty($filters['date_end']) ? $this->report_date($filters['date_end'], false) : 'Actualidad';
        return $start . ' al ' . $end;
    }

    private function report_status_label(string $status): string
    {
        $labels = [
            'all' => 'Todos',
            'active' => 'Activos',
            'inactive' => 'Inactivos',
            'open' => 'Abiertos',
            'closed' => 'Cerrados',
            'pending' => 'Pendientes',
            'in_progress' => 'En proceso',
            'low_stock' => 'Stock bajo',
        ];
        return $labels[$status] ?? $status;
    }

    private function report_date($value, bool $withTime = true): string
    {
        if (empty($value)) {
            return 'No definido';
        }
        $time = strtotime((string)$value);
        return $time ? date($withTime ? 'd/m/Y H:i' : 'd/m/Y', $time) : (string)$value;
    }

}
