<?php
class History extends Controllers
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

    public function history()
    {
        $this->guard_active_user();
        $this->guard_history_access();
        $data['css'] = [
            "/extensions/datatables/datatables.min.css"
        ];
        $data['script'] = [
            "/js/language_datatable.js",
            "/extensions/datatables/datatables.min.js"
        ];
        $data['page_functions_js'] = [
            "functions_history_assets.js"
        ];
        $this->views->getView($this, '', 'index', $data);
    }

    public function show_assets()
    {
        $this->guard_active_user();
        $this->guard_history_access();
        $assets = $this->model->assets();

        foreach ($assets as $index => $asset) {
            $assets[$index]['asset_summary'] = $this->asset_summary_html($asset);
            $assets[$index]['category_summary'] = $this->category_summary_html($asset);
            $assets[$index]['current_location'] = $this->location_html($asset);
            $assets[$index]['event_summary'] = $this->event_summary_html($asset);
            $assets[$index]['options'] = '<div class="history-action-wrap"><a class="btn btn-secondary history-action-btn" href="' . base_url() . '/history/timeline/view/' . (int)$asset['id_asset'] . '" title="Ver historial"><i class="bx bx-history"></i><span>Ver historial</span></a></div>';
        }

        echo json_encode($assets, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function timeline($params = '')
    {
        $this->guard_active_user();
        $this->guard_history_access();
        $id_asset = (int)explode(',', (string)$params)[0];
        if ($id_asset <= 0) {
            header('Location: ' . base_url() . '/history');
            die();
        }

        $data['asset'] = $this->model->asset($id_asset);
        if (!$data['asset']) {
            header('Location: ' . base_url() . '/history');
            die();
        }
        $data['events'] = $this->model->timeline($id_asset);
        $data['page_functions_js'] = [];
        $this->views->getView($this, '', 'timeline', $data);
    }

    private function guard_active_user(): void
    {
        if ($_SESSION['status_active_user'] === 0) {
            header('Location: ' . base_url() . '/account/logout');
            die();
        }
    }

    private function guard_history_access(): void
    {
        check_permissions(MODULE_HISTORY);
        if (empty($_SESSION['module_permissions']['r_read_permission'])) {
            header('Location: ' . current_user_default_url());
            die();
        }
    }

    private function h($value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }

    private function asset_summary_html(array $asset): string
    {
        $code = $this->h($asset['code_inventory_asset'] ?? '');
        $type = $this->h($asset['name_type_asset'] ?? 'Sin tipo');
        $brand = $this->h($asset['name_brand'] ?? '');
        $model = $this->h($asset['model_asset'] ?? '');
        return '<div class="history-asset-main"><strong>' . $code . '</strong><span>' . trim($type . ' - ' . $brand . ' ' . $model) . '</span></div>';
    }

    private function category_summary_html(array $asset): string
    {
        $category = $this->h($asset['name_category_asset'] ?? 'Sin categoria');
        $condition = $this->h($asset['name_condition_asset'] ?? 'Sin condicion');
        return '<div class="history-asset-main"><strong>' . $category . '</strong><span>' . $condition . '</span></div>';
    }

    private function location_html(array $asset): string
    {
        if (empty($asset['name_environment'])) {
            return '<span class="text-muted">Sin ubicacion</span>';
        }
        return 'Piso ' . $this->h($asset['floor_environment']) . ' - ' . $this->h($asset['name_environment']);
    }

    private function event_summary_html(array $asset): string
    {
        return '<div class="history-event-pills">'
            . '<span class="history-pill"><i class="bx bx-transfer-alt"></i>' . (int)$asset['total_movements'] . ' mov.</span>'
            . '<span class="history-pill"><i class="bx bx-error-circle"></i>' . (int)$asset['total_incidents'] . ' inc.</span>'
            . '<span class="history-pill"><i class="bx bx-calendar-check"></i>' . (int)$asset['total_preventive'] . ' prev.</span>'
            . '<span class="history-pill"><i class="bx bx-wrench"></i>' . (int)$asset['total_corrective'] . ' corr.</span>'
            . '</div>';
    }
}
