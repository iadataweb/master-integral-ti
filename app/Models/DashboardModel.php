<?php
class DashboardModel extends Mysql
{
    private bool $reportMode = false;

    public function dashboard_report_data(array $filters, ?int $user = null): array
    {
        $this->reportMode = true;
        try { return $this->dashboard_data($filters, $user); }
        finally { $this->reportMode = false; }
    }

    private function count_value(string $sql, array $params = []): int
    {
        $row = $this->select($sql, $params);
        return isset($row['total']) ? (int)$row['total'] : 0;
    }

    private function normalize_filters(array $filters): array
    {
        return [
            'date_start' => $this->normalize_date($filters['date_start'] ?? ''),
            'date_end' => $this->normalize_date($filters['date_end'] ?? ''),
            'status' => trim((string)($filters['status'] ?? 'all')),
            'asset_category' => (int)($filters['asset_category'] ?? 0),
            'incident_type' => (int)($filters['incident_type'] ?? 0),
        ];
    }

    private function normalize_date(string $value): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
            [$day, $month, $year] = explode('/', $value);
            return $year . '-' . $month . '-' . $day;
        }
        $timestamp = strtotime($value);
        return $timestamp ? date('Y-m-d', $timestamp) : null;
    }

    private function date_condition(string $field, array $filters, array &$params): string
    {
        $conditions = [];
        if (!empty($filters['date_start'])) {
            $conditions[] = "DATE({$field}) >= ?";
            $params[] = $filters['date_start'];
        }
        if (!empty($filters['date_end'])) {
            $conditions[] = "DATE({$field}) <= ?";
            $params[] = $filters['date_end'];
        }
        return $conditions ? ' AND ' . implode(' AND ', $conditions) : '';
    }

    public function dashboard_filters(): array
    {
        return [
            'asset_categories' => $this->select_all("SELECT id_category_asset, name_category_asset FROM categories_asset WHERE status_deleted_category_asset = 0 ORDER BY name_category_asset ASC"),
            'incident_types' => $this->select_all("SELECT id_type_incident, name_type_incident FROM types_incident WHERE status_deleted_type_incident = 0 ORDER BY name_type_incident ASC"),
            'statuses' => [
                ['id' => 'all', 'name' => 'Todos'],
                ['id' => 'active', 'name' => 'Activos'],
                ['id' => 'inactive', 'name' => 'Inactivos'],
                ['id' => 'open', 'name' => 'Abiertos'],
                ['id' => 'closed', 'name' => 'Cerrados'],
                ['id' => 'pending', 'name' => 'Pendientes'],
                ['id' => 'in_progress', 'name' => 'En proceso'],
                ['id' => 'low_stock', 'name' => 'Stock bajo'],
            ],
        ];
    }

    public function dashboard_data(array $raw_filters, ?int $id_user = null): array
    {
        $filters = $this->normalize_filters($raw_filters);
        return [
            'filters' => $filters,
            'summary' => $this->summary($filters, $id_user),
            'charts' => [
                'assets_by_category' => $this->assets_by_category($filters),
                'assets_by_environment' => $this->assets_by_environment_chart($filters),
                'asset_conditions' => $this->asset_conditions($filters),
                'movements_by_type' => $this->movements_by_type($filters),
                'incidents_by_status' => $this->incidents_by_status($filters, $id_user),
                'incidents_by_type' => $this->incidents_by_type($filters, $id_user),
                'maintenance_overview' => $this->maintenance_overview($filters),
                'spare_stock' => $this->spare_stock($filters),
                'monthly_activity' => $this->monthly_activity($filters, $id_user),
            ],
            'tables' => [
                'stored_assets_status' => $this->stored_assets_status($filters),
                'stored_assets_by_type_status' => $this->stored_assets_by_type_status($filters),
                'assets_by_type_flow' => $this->assets_by_type_flow($filters),
                'assets_by_type_condition' => $this->assets_by_type_condition($filters),
                'assets_by_environment' => $this->assets_by_environment($filters),
                'assets_by_floor' => $this->assets_by_floor($filters),
                'assets_without_movement' => $this->assets_without_movement($filters),
                'low_stock' => $this->low_stock_table($filters),
                'recent_incidents' => $this->recent_incidents($filters, $id_user),
            ],
        ];
    }

    private function summary(array $filters, ?int $id_user): array
    {
        $params = [];
        $assetWhere = "a.status_deleted_asset = 0" . $this->date_condition('a.date_created_asset', $filters, $params);
        if ($filters['asset_category'] > 0) {
            $assetWhere .= " AND a.id_category_asset_asset = ?";
            $params[] = $filters['asset_category'];
        }
        if ($filters['status'] === 'active') {
            $assetWhere .= " AND a.status_active_asset = 1";
        } elseif ($filters['status'] === 'inactive') {
            $assetWhere .= " AND a.status_active_asset = 0";
        }
        $assets = $this->count_value("SELECT COUNT(*) AS total FROM assets a WHERE {$assetWhere}", $params);
        $assetsWithoutMovement = $this->assets_without_movement_total($filters);

        $params = [];
        $movementWhere = "ma.status_deleted_movement_asset = 0" . $this->date_condition('COALESCE(ma.date_event_movement_asset, ma.date_created_movement_asset)', $filters, $params);
        $movements = $this->count_value("SELECT COUNT(*) AS total FROM movements_asset ma WHERE {$movementWhere}", $params);

        $params = [];
        $incidentWhere = "i.status_deleted_incident = 0" . $this->date_condition('COALESCE(TIMESTAMP(i.date_incident_date, i.time_incident_time), i.date_created_incident)', $filters, $params);
        if ($id_user !== null) {
            $incidentWhere .= " AND i.id_user_incident = ?";
            $params[] = $id_user;
        }
        if ($filters['asset_category'] > 0) {
            $incidentWhere .= " AND i.id_category_asset_incident = ?";
            $params[] = $filters['asset_category'];
        }
        if ($filters['incident_type'] > 0) {
            $incidentWhere .= " AND i.id_type_incident_incident = ?";
            $params[] = $filters['incident_type'];
        }
        if ($filters['status'] === 'open') {
            $incidentWhere .= " AND i.status_open_incident = 1";
        } elseif ($filters['status'] === 'closed') {
            $incidentWhere .= " AND i.status_open_incident = 0";
        } elseif ($filters['status'] === 'pending') {
            $incidentWhere .= " AND i.id_support_assigned_incident IS NULL AND i.status_open_incident = 1";
        } elseif ($filters['status'] === 'in_progress') {
            $incidentWhere .= " AND i.id_support_assigned_incident IS NOT NULL AND i.status_open_incident = 1";
        }
        $incidents = $this->count_value("SELECT COUNT(*) AS total FROM incidents i WHERE {$incidentWhere}", $params);
        $openIncidents = $this->count_value("SELECT COUNT(*) AS total FROM incidents i WHERE {$incidentWhere} AND i.status_open_incident = 1", $params);
        $closedIncidents = $this->count_value("SELECT COUNT(*) AS total FROM incidents i WHERE {$incidentWhere} AND i.status_open_incident = 0", $params);

        $params = [];
        $preventiveWhere = "mp.status_deleted_maintenance_preventive = 0" . $this->date_condition('COALESCE(mp.date_scheduled_maintenance_preventive, mp.date_created_maintenance_preventive)', $filters, $params);
        if ($filters['asset_category'] > 0) {
            $preventiveWhere .= " AND a.id_category_asset_asset = ?";
            $params[] = $filters['asset_category'];
        }
        $preventive = $this->count_value("SELECT COUNT(*) AS total FROM maintenances_preventive mp INNER JOIN assets a ON mp.id_asset_maintenance_preventive = a.id_asset WHERE {$preventiveWhere}", $params);

        $params = [];
        $correctiveWhere = "mc.status_deleted_maintenance_corrective = 0" . $this->date_condition('COALESCE(mc.date_repair_maintenance_corrective, mc.date_created_maintenance_corrective)', $filters, $params);
        if ($filters['asset_category'] > 0) {
            $correctiveWhere .= " AND a.id_category_asset_asset = ?";
            $params[] = $filters['asset_category'];
        }
        if ($filters['status'] === 'active' || $filters['status'] === 'open' || $filters['status'] === 'pending') {
            $correctiveWhere .= " AND mc.status_active_maintenance_corrective = 1";
        } elseif ($filters['status'] === 'inactive' || $filters['status'] === 'closed') {
            $correctiveWhere .= " AND mc.status_active_maintenance_corrective = 0";
        }
        $corrective = $this->count_value("SELECT COUNT(*) AS total FROM maintenances_corrective mc INNER JOIN assets a ON mc.id_asset_maintenance_corrective = a.id_asset WHERE {$correctiveWhere}", $params);

        $params = [];
        $spareWhere = "sp.status_deleted_spare_part = 0" . $this->date_condition('sp.date_created_spare_part', $filters, $params);
        if ($filters['status'] === 'low_stock') {
            $spareWhere .= " AND sp.stock_current_spare_part <= sp.stock_minimum_spare_part";
        }
        $spares = $this->count_value("SELECT COUNT(*) AS total FROM spare_parts sp WHERE {$spareWhere}", $params);
        $lowStock = $this->count_value("SELECT COUNT(*) AS total FROM spare_parts sp WHERE sp.status_deleted_spare_part = 0 AND sp.stock_current_spare_part <= sp.stock_minimum_spare_part");

        return [
            'assets' => $assets,
            'assets_without_movement' => $assetsWithoutMovement,
            'movements' => $movements,
            'incidents' => $incidents,
            'open_incidents' => $openIncidents,
            'closed_incidents' => $closedIncidents,
            'preventive' => $preventive,
            'corrective' => $corrective,
            'spares' => $spares,
            'low_stock' => $lowStock,
        ];
    }

    private function label_value_rows(string $sql, array $params = []): array
    {
        $rows = $this->select_all($sql, $params);
        return [
            'labels' => array_map(fn($row) => (string)$row['label'], $rows),
            'values' => array_map(fn($row) => (int)$row['total'], $rows),
            'rows' => $rows,
        ];
    }

    private function assets_by_category(array $filters): array
    {
        $params = [];
        $where = "a.status_deleted_asset = 0" . $this->date_condition('a.date_created_asset', $filters, $params);
        if ($filters['asset_category'] > 0) {
            $where .= " AND a.id_category_asset_asset = ?";
            $params[] = $filters['asset_category'];
        }
        return $this->label_value_rows("SELECT COALESCE(ca.name_category_asset, 'Sin categoria') AS label, COUNT(a.id_asset) AS total FROM assets a LEFT JOIN categories_asset ca ON a.id_category_asset_asset = ca.id_category_asset AND ca.status_deleted_category_asset = 0 WHERE {$where} GROUP BY label ORDER BY total DESC", $params);
    }

    private function asset_base_where(array $filters, array &$params): string
    {
        $where = "a.status_deleted_asset = 0" . $this->date_condition('a.date_created_asset', $filters, $params);
        if ($filters['asset_category'] > 0) {
            $where .= " AND a.id_category_asset_asset = ?";
            $params[] = $filters['asset_category'];
        }
        if ($filters['status'] === 'active') {
            $where .= " AND a.status_active_asset = 1";
        } elseif ($filters['status'] === 'inactive') {
            $where .= " AND a.status_active_asset = 0";
        }
        return $where;
    }

    private function assets_by_type_flow(array $filters): array
    {
        $params = [];
        $where = $this->asset_base_where($filters, $params);
        return $this->select_all("
            SELECT
                ta.id_type_asset,
                COALESCE(ta.name_type_asset, 'Sin tipo') AS type_asset,
                COUNT(a.id_asset) AS total_assets,
                SUM(CASE WHEN ma.id_type_movement_asset IN (1, 4) THEN 1 ELSE 0 END) AS available_storage,
                SUM(CASE WHEN ma.id_type_movement_asset = 2 THEN 1 ELSE 0 END) AS assigned_use,
                SUM(CASE WHEN ma.id_type_movement_asset = 3 THEN 1 ELSE 0 END) AS transferred_use,
                SUM(CASE WHEN ma.id_type_movement_asset = 5 THEN 1 ELSE 0 END) AS loaned_use,
                SUM(CASE WHEN mc.id_asset_maintenance_corrective IS NOT NULL OR (ma.id_type_movement_asset IS NOT NULL AND ma.id_type_movement_asset NOT IN (1, 2, 3, 4, 5)) THEN 1 ELSE 0 END) AS maintenance_other
            FROM assets a
            LEFT JOIN types_asset ta ON a.id_type_asset_asset = ta.id_type_asset AND ta.status_deleted_type_asset = 0
            LEFT JOIN movements_asset ma ON ma.id_asset_movement_asset = a.id_asset
                AND ma.status_record_current_movement_asset = 1
                AND ma.status_deleted_movement_asset = 0
            LEFT JOIN (
                SELECT DISTINCT id_asset_maintenance_corrective
                FROM maintenances_corrective
                WHERE status_deleted_maintenance_corrective = 0
                    AND status_active_maintenance_corrective = 1
            ) mc ON mc.id_asset_maintenance_corrective = a.id_asset
            WHERE {$where}
            GROUP BY ta.id_type_asset, COALESCE(ta.name_type_asset, 'Sin tipo')
            ORDER BY total_assets DESC, type_asset ASC
            ".($this->reportMode ? '' : 'LIMIT 12')."
        ", $params);
    }

    private function assets_by_type_condition(array $filters): array
    {
        $params = [];
        $where = $this->asset_base_where($filters, $params);
        return $this->select_all("
            SELECT
                ta.id_type_asset,
                COALESCE(ta.name_type_asset, 'Sin tipo') AS type_asset,
                COUNT(a.id_asset) AS total_assets,
                SUM(CASE WHEN LOWER(co.name_condition_asset) = 'operativo' THEN 1 ELSE 0 END) AS operative_assets,
                SUM(CASE WHEN LOWER(co.name_condition_asset) = 'inoperativo' THEN 1 ELSE 0 END) AS inoperative_assets,
                SUM(CASE WHEN LOWER(co.name_condition_asset) = 'mantenimiento' THEN 1 ELSE 0 END) AS maintenance_assets,
                SUM(CASE WHEN LOWER(co.name_condition_asset) = 'baja' THEN 1 ELSE 0 END) AS retired_assets,
                SUM(CASE WHEN co.name_condition_asset IS NULL OR LOWER(co.name_condition_asset) NOT IN ('operativo', 'inoperativo', 'mantenimiento', 'baja') THEN 1 ELSE 0 END) AS other_assets
            FROM assets a
            LEFT JOIN types_asset ta ON a.id_type_asset_asset = ta.id_type_asset AND ta.status_deleted_type_asset = 0
            LEFT JOIN conditions_asset co ON a.id_condition_asset_asset = co.id_condition_asset AND co.status_deleted_condition_asset = 0
            WHERE {$where}
            GROUP BY ta.id_type_asset, COALESCE(ta.name_type_asset, 'Sin tipo')
            ORDER BY total_assets DESC, type_asset ASC
            ".($this->reportMode ? '' : 'LIMIT 12')."
        ", $params);
    }

    private function assets_without_movement_total(array $filters): int
    {
        $params = [];
        $where = $this->asset_base_where($filters, $params);
        return $this->count_value("
            SELECT COUNT(a.id_asset) AS total
            FROM assets a
            LEFT JOIN movements_asset ma ON ma.id_asset_movement_asset = a.id_asset
                AND ma.status_record_current_movement_asset = 1
                AND ma.status_deleted_movement_asset = 0
            WHERE {$where}
                AND ma.id_movement_asset IS NULL
        ", $params);
    }

    private function assets_without_movement(array $filters): array
    {
        $params = [];
        $where = $this->asset_base_where($filters, $params);
        return $this->select_all("
            SELECT
                a.id_asset,
                a.code_inventory_asset,
                COALESCE(ta.name_type_asset, 'Sin tipo') AS name_type_asset,
                COALESCE(ca.name_category_asset, 'Sin categoria') AS name_category_asset,
                COALESCE(co.name_condition_asset, 'Sin condicion') AS name_condition_asset,
                a.date_created_asset
            FROM assets a
            LEFT JOIN types_asset ta ON a.id_type_asset_asset = ta.id_type_asset AND ta.status_deleted_type_asset = 0
            LEFT JOIN categories_asset ca ON a.id_category_asset_asset = ca.id_category_asset AND ca.status_deleted_category_asset = 0
            LEFT JOIN conditions_asset co ON a.id_condition_asset_asset = co.id_condition_asset AND co.status_deleted_condition_asset = 0
            LEFT JOIN movements_asset ma ON ma.id_asset_movement_asset = a.id_asset
                AND ma.status_record_current_movement_asset = 1
                AND ma.status_deleted_movement_asset = 0
            WHERE {$where}
                AND ma.id_movement_asset IS NULL
            ORDER BY a.date_created_asset DESC, a.id_asset DESC
            ".($this->reportMode ? '' : 'LIMIT 12')."
        ", $params);
    }

    private function stored_assets_status(array $filters): array
    {
        $params = [];
        $where = $this->asset_base_where($filters, $params);
        $row = $this->select("
            SELECT
                COUNT(a.id_asset) AS total_storage,
                SUM(CASE WHEN LOWER(co.name_condition_asset) = 'operativo' THEN 1 ELSE 0 END) AS available_storage,
                SUM(CASE WHEN LOWER(co.name_condition_asset) = 'operativo' THEN 1 ELSE 0 END) AS operative_storage,
                SUM(CASE WHEN LOWER(co.name_condition_asset) = 'inoperativo' THEN 1 ELSE 0 END) AS inoperative_storage,
                SUM(CASE WHEN LOWER(co.name_condition_asset) = 'mantenimiento' THEN 1 ELSE 0 END) AS maintenance_storage,
                SUM(CASE WHEN LOWER(co.name_condition_asset) = 'baja' THEN 1 ELSE 0 END) AS retired_storage,
                SUM(CASE WHEN co.name_condition_asset IS NULL OR LOWER(co.name_condition_asset) NOT IN ('operativo', 'inoperativo', 'mantenimiento', 'baja') THEN 1 ELSE 0 END) AS other_storage
            FROM assets a
            INNER JOIN movements_asset ma ON ma.id_asset_movement_asset = a.id_asset
                AND ma.status_record_current_movement_asset = 1
                AND ma.status_deleted_movement_asset = 0
            INNER JOIN environments e ON ma.id_space_movement_asset = e.id_environment
                AND e.status_deleted_environment = 0
                AND LOWER(e.name_environment) LIKE '%almac%'
            LEFT JOIN conditions_asset co ON a.id_condition_asset_asset = co.id_condition_asset
                AND co.status_deleted_condition_asset = 0
            WHERE {$where}
        ", $params);

        $defaults = [
            'total_storage' => 0,
            'available_storage' => 0,
            'operative_storage' => 0,
            'inoperative_storage' => 0,
            'maintenance_storage' => 0,
            'retired_storage' => 0,
            'other_storage' => 0,
        ];

        return array_map('intval', array_merge($defaults, $row ?: []));
    }

    private function stored_assets_by_type_status(array $filters): array
    {
        $params = [];
        $where = $this->asset_base_where($filters, $params);
        return $this->select_all("
            SELECT
                ta.id_type_asset,
                COALESCE(ta.name_type_asset, 'Sin tipo') AS type_asset,
                COUNT(a.id_asset) AS total_storage,
                SUM(CASE WHEN LOWER(co.name_condition_asset) = 'operativo' THEN 1 ELSE 0 END) AS available_storage,
                SUM(CASE WHEN LOWER(co.name_condition_asset) = 'operativo' THEN 1 ELSE 0 END) AS operative_storage,
                SUM(CASE WHEN LOWER(co.name_condition_asset) = 'inoperativo' THEN 1 ELSE 0 END) AS inoperative_storage,
                SUM(CASE WHEN LOWER(co.name_condition_asset) = 'mantenimiento' THEN 1 ELSE 0 END) AS maintenance_storage,
                SUM(CASE WHEN LOWER(co.name_condition_asset) = 'baja' THEN 1 ELSE 0 END) AS retired_storage,
                SUM(CASE WHEN co.name_condition_asset IS NULL OR LOWER(co.name_condition_asset) NOT IN ('operativo', 'inoperativo', 'mantenimiento', 'baja') THEN 1 ELSE 0 END) AS other_storage
            FROM assets a
            INNER JOIN movements_asset ma ON ma.id_asset_movement_asset = a.id_asset
                AND ma.status_record_current_movement_asset = 1
                AND ma.status_deleted_movement_asset = 0
            INNER JOIN environments e ON ma.id_space_movement_asset = e.id_environment
                AND e.status_deleted_environment = 0
                AND LOWER(e.name_environment) LIKE '%almac%'
            LEFT JOIN types_asset ta ON a.id_type_asset_asset = ta.id_type_asset
                AND ta.status_deleted_type_asset = 0
            LEFT JOIN conditions_asset co ON a.id_condition_asset_asset = co.id_condition_asset
                AND co.status_deleted_condition_asset = 0
            WHERE {$where}
            GROUP BY ta.id_type_asset, COALESCE(ta.name_type_asset, 'Sin tipo')
            ORDER BY total_storage DESC, type_asset ASC
            ".($this->reportMode ? '' : 'LIMIT 12')."
        ", $params);
    }

    private function assets_by_environment(array $filters): array
    {
        $params = [];
        $where = $this->asset_base_where($filters, $params);
        return $this->select_all("
            SELECT
                e.id_environment,
                COALESCE(e.name_environment, 'Sin ambiente registrado') AS name_environment,
                e.floor_environment,
                COUNT(a.id_asset) AS total_assets
            FROM assets a
            LEFT JOIN movements_asset ma ON ma.id_asset_movement_asset = a.id_asset
                AND ma.status_record_current_movement_asset = 1
                AND ma.status_deleted_movement_asset = 0
            LEFT JOIN environments e ON ma.id_space_movement_asset = e.id_environment
                AND e.status_deleted_environment = 0
            WHERE {$where}
            GROUP BY e.id_environment, COALESCE(e.name_environment, 'Sin ambiente registrado'), e.floor_environment
            ORDER BY total_assets DESC, name_environment ASC
            ".($this->reportMode ? '' : 'LIMIT 12')."
        ", $params);
    }

    private function assets_by_floor(array $filters): array
    {
        $params = [];
        $where = $this->asset_base_where($filters, $params);
        return $this->select_all("SELECT e.floor_environment, COUNT(DISTINCT a.id_asset) AS total_assets
            FROM assets a
            LEFT JOIN movements_asset ma ON ma.id_asset_movement_asset=a.id_asset
                AND ma.status_record_current_movement_asset=1 AND ma.status_deleted_movement_asset=0
                AND ma.status_annulled_movement_asset=0
            LEFT JOIN environments e ON e.id_environment=ma.id_space_movement_asset AND e.status_deleted_environment=0
            WHERE {$where}
            GROUP BY e.floor_environment
            ORDER BY e.floor_environment IS NULL, e.floor_environment ASC", $params);
    }

    private function assets_by_environment_chart(array $filters): array
    {
        $rows = $this->assets_by_environment($filters);
        return [
            'labels' => array_map(fn($row) => (string)$row['name_environment'], $rows),
            'values' => array_map(fn($row) => (int)$row['total_assets'], $rows),
            'rows' => $rows,
        ];
    }

    private function asset_conditions(array $filters): array
    {
        $params = [];
        $where = "a.status_deleted_asset = 0" . $this->date_condition('a.date_created_asset', $filters, $params);
        if ($filters['asset_category'] > 0) {
            $where .= " AND a.id_category_asset_asset = ?";
            $params[] = $filters['asset_category'];
        }
        return $this->label_value_rows("SELECT co.name_condition_asset AS label, COUNT(*) AS total FROM assets a INNER JOIN conditions_asset co ON a.id_condition_asset_asset = co.id_condition_asset WHERE {$where} GROUP BY co.name_condition_asset ORDER BY total DESC", $params);
    }

    private function movements_by_type(array $filters): array
    {
        $params = [];
        $where = "ma.status_deleted_movement_asset = 0" . $this->date_condition('COALESCE(ma.date_event_movement_asset, ma.date_created_movement_asset)', $filters, $params);
        return $this->label_value_rows("SELECT tm.name_type_movement AS label, COUNT(*) AS total FROM movements_asset ma INNER JOIN types_movement tm ON ma.id_type_movement_asset = tm.id_type_movement WHERE {$where} GROUP BY tm.name_type_movement ORDER BY total DESC", $params);
    }

    private function incident_base_where(array $filters, ?int $id_user, array &$params): string
    {
        $where = "i.status_deleted_incident = 0" . $this->date_condition('COALESCE(TIMESTAMP(i.date_incident_date, i.time_incident_time), i.date_created_incident)', $filters, $params);
        if ($id_user !== null) {
            $where .= " AND i.id_user_incident = ?";
            $params[] = $id_user;
        }
        if ($filters['asset_category'] > 0) {
            $where .= " AND i.id_category_asset_incident = ?";
            $params[] = $filters['asset_category'];
        }
        if ($filters['incident_type'] > 0) {
            $where .= " AND i.id_type_incident_incident = ?";
            $params[] = $filters['incident_type'];
        }
        if ($filters['status'] === 'open') {
            $where .= " AND i.status_open_incident = 1";
        } elseif ($filters['status'] === 'closed') {
            $where .= " AND i.status_open_incident = 0";
        } elseif ($filters['status'] === 'pending') {
            $where .= " AND i.id_support_assigned_incident IS NULL AND i.status_open_incident = 1";
        } elseif ($filters['status'] === 'in_progress') {
            $where .= " AND i.id_support_assigned_incident IS NOT NULL AND i.status_open_incident = 1";
        }
        return $where;
    }

    private function incidents_by_status(array $filters, ?int $id_user): array
    {
        $params = [];
        $where = $this->incident_base_where($filters, $id_user, $params);
        return [
            'labels' => ['Pendientes', 'En proceso', 'Cerradas'],
            'values' => [
                $this->count_value("SELECT COUNT(*) AS total FROM incidents i WHERE {$where} AND i.id_support_assigned_incident IS NULL AND i.status_open_incident = 1", $params),
                $this->count_value("SELECT COUNT(*) AS total FROM incidents i WHERE {$where} AND i.id_support_assigned_incident IS NOT NULL AND i.status_open_incident = 1", $params),
                $this->count_value("SELECT COUNT(*) AS total FROM incidents i WHERE {$where} AND i.status_open_incident = 0", $params),
            ],
        ];
    }

    private function incidents_by_type(array $filters, ?int $id_user): array
    {
        $params = [];
        $where = $this->incident_base_where($filters, $id_user, $params);
        return $this->label_value_rows("SELECT COALESCE(ti.name_type_incident, 'Sin tipo') AS label, COUNT(*) AS total FROM incidents i LEFT JOIN types_incident ti ON i.id_type_incident_incident = ti.id_type_incident WHERE {$where} GROUP BY label ORDER BY total DESC", $params);
    }

    private function maintenance_overview(array $filters): array
    {
        $params = [];
        $preventiveWhere = "mp.status_deleted_maintenance_preventive = 0" . $this->date_condition('COALESCE(mp.date_scheduled_maintenance_preventive, mp.date_created_maintenance_preventive)', $filters, $params);
        $preventive = $this->count_value("SELECT COUNT(*) AS total FROM maintenances_preventive mp WHERE {$preventiveWhere}", $params);

        $params = [];
        $historyWhere = "1=1" . $this->date_condition('COALESCE(hmp.date_performed_history_maintenance_preventive, hmp.date_created_history_maintenance_preventive)', $filters, $params);
        $preventiveDone = $this->count_value("SELECT COUNT(*) AS total FROM history_maintenances_preventive hmp WHERE {$historyWhere}", $params);

        $params = [];
        $correctiveWhere = "mc.status_deleted_maintenance_corrective = 0" . $this->date_condition('COALESCE(mc.date_repair_maintenance_corrective, mc.date_created_maintenance_corrective)', $filters, $params);
        $correctiveOpen = $this->count_value("SELECT COUNT(*) AS total FROM maintenances_corrective mc WHERE {$correctiveWhere} AND mc.status_active_maintenance_corrective = 1", $params);
        $correctiveClosed = $this->count_value("SELECT COUNT(*) AS total FROM maintenances_corrective mc WHERE {$correctiveWhere} AND mc.status_active_maintenance_corrective = 0", $params);

        return [
            'labels' => ['Preventivo programado', 'Preventivo realizado', 'Correctivo pendiente', 'Correctivo cerrado'],
            'values' => [$preventive, $preventiveDone, $correctiveOpen, $correctiveClosed],
        ];
    }

    private function spare_stock(array $filters): array
    {
        $params = [];
        $where = "sp.status_deleted_spare_part = 0" . $this->date_condition('sp.date_created_spare_part', $filters, $params);
        $rows = $this->select_all("SELECT sp.name_spare_part AS label, sp.stock_current_spare_part AS total FROM spare_parts sp WHERE {$where} ORDER BY sp.stock_current_spare_part DESC LIMIT 8", $params);
        return [
            'labels' => array_map(fn($row) => (string)$row['label'], $rows),
            'values' => array_map(fn($row) => (float)$row['total'], $rows),
            'rows' => $rows,
        ];
    }

    private function monthly_activity(array $filters, ?int $id_user): array
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $key = date('Y-m', strtotime("-{$i} month"));
            $months[$key] = ['label' => date('M Y', strtotime($key . '-01')), 'incidents' => 0, 'movements' => 0, 'corrective' => 0];
        }

        $params = [];
        $userWhere = '';
        if ($id_user !== null) {
            $userWhere = ' AND id_user_incident = ?';
            $params[] = $id_user;
        }
        $rows = $this->select_all("SELECT DATE_FORMAT(COALESCE(TIMESTAMP(date_incident_date, time_incident_time), date_created_incident), '%Y-%m') AS period, COUNT(*) AS total FROM incidents WHERE status_deleted_incident = 0 {$userWhere} GROUP BY period", $params);
        foreach ($rows as $row) {
            if (isset($months[$row['period']])) {
                $months[$row['period']]['incidents'] = (int)$row['total'];
            }
        }
        $rows = $this->select_all("SELECT DATE_FORMAT(COALESCE(date_event_movement_asset, date_created_movement_asset), '%Y-%m') AS period, COUNT(*) AS total FROM movements_asset WHERE status_deleted_movement_asset = 0 GROUP BY period");
        foreach ($rows as $row) {
            if (isset($months[$row['period']])) {
                $months[$row['period']]['movements'] = (int)$row['total'];
            }
        }
        $rows = $this->select_all("SELECT DATE_FORMAT(COALESCE(date_repair_maintenance_corrective, date_created_maintenance_corrective), '%Y-%m') AS period, COUNT(*) AS total FROM maintenances_corrective WHERE status_deleted_maintenance_corrective = 0 GROUP BY period");
        foreach ($rows as $row) {
            if (isset($months[$row['period']])) {
                $months[$row['period']]['corrective'] = (int)$row['total'];
            }
        }

        return [
            'labels' => array_column($months, 'label'),
            'series' => [
                ['name' => 'Incidencias', 'data' => array_column($months, 'incidents')],
                ['name' => 'Movimientos', 'data' => array_column($months, 'movements')],
                ['name' => 'Correctivos', 'data' => array_column($months, 'corrective')],
            ],
        ];
    }

    private function low_stock_table(array $filters): array
    {
        return $this->select_all("SELECT id_spare_part, code_spare_part, name_spare_part, stock_current_spare_part, stock_minimum_spare_part, unit_spare_part FROM spare_parts WHERE status_deleted_spare_part = 0 AND stock_current_spare_part <= stock_minimum_spare_part ORDER BY stock_current_spare_part ASC LIMIT 10");
    }

    private function recent_incidents(array $filters, ?int $id_user): array
    {
        $params = [];
        $where = $this->incident_base_where($filters, $id_user, $params);
        return $this->select_all("SELECT i.id_incident, i.title_incident, COALESCE(ti.name_type_incident, 'Sin tipo') AS name_type_incident, i.status_open_incident, i.date_created_incident FROM incidents i LEFT JOIN types_incident ti ON i.id_type_incident_incident = ti.id_type_incident WHERE {$where} ORDER BY i.date_created_incident DESC LIMIT 10", $params);
    }
}
