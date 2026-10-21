<?php
class AssetModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function data(string $code_inventory_asset)
    {
        $sql = "SELECT a.id_asset,
                       a.code_inventory_asset,
                       a.model_asset,
                       a.serial_number_asset,
                       a.entry_date_asset,
                       a.acquisition_date_asset,
                       a.date_created_asset,
                       a.status_active_asset,
                       a.status_movement_asset,
                       a.status_maintenance_asset,
                       a.descriptions_asset,
                       a.observations_asset,
                       ta.id_type_asset,
                       ta.name_type_asset,
                       ca.id_category_asset,
                       ca.name_category_asset,
                       b.id_brand,
                       b.name_brand,
                       coa.id_condition_asset,
                       coa.name_condition_asset
                FROM assets a
                INNER JOIN types_asset ta ON a.id_type_asset_asset = ta.id_type_asset
                INNER JOIN categories_asset ca ON a.id_category_asset_asset = ca.id_category_asset
                INNER JOIN brands b ON a.id_brand_asset = b.id_brand
                INNER JOIN conditions_asset coa ON a.id_condition_asset_asset = coa.id_condition_asset
                WHERE a.code_inventory_asset = ?
                  AND a.status_deleted_asset = 0
                LIMIT 1";
        return $this->select($sql, [$code_inventory_asset]);
    }

    public function quick_context(string $code_inventory_asset): array
    {
        $asset = $this->data($code_inventory_asset);
        if (!$asset) {
            return [
                'asset' => null,
                'attachments_asset' => [],
                'current_movement' => null,
                'previous_movement' => null,
                'recent_incidents' => [],
                'preventive_schedule' => null,
                'preventive_schedule_attachments' => [],
                'preventive_history' => [],
                'corrective_history' => [],
            ];
        }

        $id_asset = (int)$asset['id_asset'];
        $movements = $this->movements_by_asset($id_asset, 2);
        $preventiveSchedule = $this->preventive_schedule($id_asset);
        $preventiveHistory = $this->preventive_history($id_asset, 5);
        $correctiveHistory = $this->corrective_history($id_asset, 5);

        foreach ($preventiveHistory as &$history) {
            $history['attachments'] = $this->history_preventive_attachments((int)$history['id_history_maintenance_preventive']);
        }
        unset($history);

        foreach ($correctiveHistory as &$corrective) {
            $corrective['attachments'] = $this->corrective_attachments((int)$corrective['id_maintenance_corrective']);
            $corrective['spare_parts'] = $this->corrective_spare_parts((int)$corrective['id_maintenance_corrective']);
        }
        unset($corrective);

        return [
            'asset' => $asset,
            'attachments_asset' => $this->asset_attachments($id_asset),
            'current_movement' => $movements[0] ?? null,
            'previous_movement' => $movements[1] ?? null,
            'recent_incidents' => $this->recent_incidents($id_asset, 5),
            'preventive_schedule' => $preventiveSchedule,
            'preventive_schedule_attachments' => $preventiveSchedule ? $this->preventive_attachments((int)$preventiveSchedule['id_maintenance_preventive']) : [],
            'preventive_history' => $preventiveHistory,
            'corrective_history' => $correctiveHistory,
        ];
    }

    public function public_quick_access_status(): array
    {
        $rows = $this->select_all("SELECT setting_key, setting_value FROM system_settings WHERE setting_group = 'public_access'");
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        $enabled = (int)($settings['public_quick_access_enabled'] ?? 0) === 1;
        $mode = $settings['public_quick_access_mode'] ?? 'indeterminate';
        $starts = trim((string)($settings['public_quick_access_starts_at'] ?? ''));
        $expires = trim((string)($settings['public_quick_access_expires_at'] ?? ''));
        $now = time();
        $isActive = false;
        $status = 'Deshabilitado';

        if ($enabled) {
            if ($mode === 'timed') {
                $startTs = $starts !== '' ? strtotime($starts) : false;
                $expiresTs = $expires !== '' ? strtotime($expires) : false;
                if ($expiresTs && $now > $expiresTs) {
                    $status = 'Expirado';
                } elseif ($startTs && $now < $startTs) {
                    $status = 'Programado';
                } else {
                    $status = 'Activo';
                    $isActive = true;
                }
            } else {
                $status = 'Activo';
                $isActive = true;
            }
        }

        return [
            'enabled' => $enabled,
            'mode' => $mode,
            'starts_at' => $starts,
            'expires_at' => $expires,
            'status' => $status,
            'is_active' => $isActive
        ];
    }

    public function asset_attachments(int $id_asset): array
    {
        $sql = "SELECT id_attachment_asset,
                       route_attachment_asset AS route,
                       name_attachment_asset AS name,
                       date_created_attachment_asset AS created_at
                FROM attachments_asset
                WHERE id_asset = ?
                  AND status_deleted_attachment_asset = 0
                ORDER BY id_attachment_asset DESC";
        return $this->select_all($sql, [$id_asset]);
    }

    public function movements_by_asset(int $id_asset, int $limit = 2): array
    {
        $limit = max(1, min(10, $limit));
        $sql = "SELECT ma.id_movement_asset,
                       ma.date_event_movement_asset,
                       ma.date_created_movement_asset,
                       ma.observations_movement_asset,
                       ma.status_record_current_movement_asset,
                       tm.name_type_movement,
                       e.name_environment,
                       e.floor_environment,
                       c.name_campus
                FROM movements_asset ma
                INNER JOIN types_movement tm ON ma.id_type_movement_asset = tm.id_type_movement
                INNER JOIN environments e ON ma.id_space_movement_asset = e.id_environment
                LEFT JOIN campuses c ON e.id_campus_environment = c.id_campus
                WHERE ma.id_asset_movement_asset = ?
                  AND ma.status_deleted_movement_asset = 0
                ORDER BY ma.status_record_current_movement_asset DESC,
                         COALESCE(ma.date_event_movement_asset, DATE(ma.date_created_movement_asset)) DESC,
                         ma.id_movement_asset DESC
                LIMIT {$limit}";
        return $this->select_all($sql, [$id_asset]);
    }

    public function recent_incidents(int $id_asset, int $limit = 5): array
    {
        $limit = max(1, min(10, $limit));
        $sql = "SELECT i.id_incident,
                       i.title_incident,
                       i.description_incident,
                       i.status_open_incident,
                       TIMESTAMP(i.date_incident_date, i.time_incident_time) AS date_incident_incident,
                       i.date_created_incident,
                       i.datetime_closed_record,
                       COALESCE(ti.name_type_incident, 'Sin tipo') AS name_type_incident,
                       p.name_priority
                FROM incidents i
                LEFT JOIN types_incident ti ON i.id_type_incident_incident = ti.id_type_incident
                INNER JOIN priorities p ON i.id_priority_incident = p.id_priority
                WHERE i.id_asset_incident = ?
                  AND i.status_deleted_incident = 0
                ORDER BY COALESCE(TIMESTAMP(i.date_incident_date, i.time_incident_time), i.date_created_incident) DESC,
                         i.id_incident DESC
                LIMIT {$limit}";
        return $this->select_all($sql, [$id_asset]);
    }

    public function preventive_schedule(int $id_asset)
    {
        $sql = "SELECT mp.id_maintenance_preventive,
                       mp.date_scheduled_maintenance_preventive,
                       mp.date_next_maintenance_preventive,
                       mp.description_maintenance_preventive,
                       mp.observation_maintenance_preventive,
                       mp.status_active_maintenance_preventive,
                       fm.name_frequency_maintenance,
                       trm.name_type_responsible_maintenance
                FROM maintenances_preventive mp
                INNER JOIN frequencies_maintenance fm ON mp.id_frequency_maintenance_maintenance_preventive = fm.id_frequency_maintenance
                INNER JOIN types_responsible_maintenance trm ON mp.id_type_responsible_maintenance_maintenance_preventive = trm.id_type_responsible_maintenance
                WHERE mp.id_asset_maintenance_preventive = ?
                  AND mp.status_deleted_maintenance_preventive = 0
                ORDER BY mp.date_scheduled_maintenance_preventive ASC,
                         mp.id_maintenance_preventive DESC
                LIMIT 1";
        return $this->select($sql, [$id_asset]);
    }

    public function preventive_attachments(int $id_maintenance_preventive): array
    {
        $sql = "SELECT id_attachment_maintenance_preventive,
                       route_attachment_maintenance_preventive AS route,
                       name_attachment_maintenance_preventive AS name,
                       date_created_attachment_maintenance_preventive AS created_at
                FROM attachments_maintenance_preventive
                WHERE id_maintenance_preventive = ?
                  AND status_deleted_attachment_maintenance_preventive = 0
                ORDER BY id_attachment_maintenance_preventive DESC";
        return $this->select_all($sql, [$id_maintenance_preventive]);
    }

    public function preventive_history(int $id_asset, int $limit = 5): array
    {
        $limit = max(1, min(10, $limit));
        $sql = "SELECT hmp.id_history_maintenance_preventive,
                       hmp.id_maintenance_preventive,
                       hmp.date_scheduled_history_maintenance_preventive,
                       hmp.date_performed_history_maintenance_preventive,
                       hmp.action_history_maintenance_preventive,
                       hmp.observation_history_maintenance_preventive,
                       hmp.date_created_history_maintenance_preventive,
                       fm.name_frequency_maintenance,
                       sm.name_status_maintenance,
                       trm.name_type_responsible_maintenance
                FROM history_maintenances_preventive hmp
                INNER JOIN maintenances_preventive mp ON hmp.id_maintenance_preventive = mp.id_maintenance_preventive
                INNER JOIN frequencies_maintenance fm ON hmp.id_frequency_maintenance = fm.id_frequency_maintenance
                INNER JOIN states_maintenance sm ON hmp.id_status_maintenance = sm.id_status_maintenance
                INNER JOIN types_responsible_maintenance trm ON hmp.id_type_responsible_maintenance = trm.id_type_responsible_maintenance
                WHERE mp.id_asset_maintenance_preventive = ?
                  AND mp.status_deleted_maintenance_preventive = 0
                ORDER BY COALESCE(hmp.date_performed_history_maintenance_preventive, DATE(hmp.date_created_history_maintenance_preventive)) DESC,
                         hmp.id_history_maintenance_preventive DESC
                LIMIT {$limit}";
        return $this->select_all($sql, [$id_asset]);
    }

    public function history_preventive_attachments(int $id_history_maintenance_preventive): array
    {
        $sql = "SELECT id_attachment_history_maintenance_preventive,
                       type_attachment_history_maintenance_preventive AS attachment_type,
                       route_attachment_history_maintenance_preventive AS route,
                       name_attachment_history_maintenance_preventive AS name,
                       date_created_attachment_history_maintenance_preventive AS created_at
                FROM attachments_history_maintenance_preventive
                WHERE id_history_maintenance_preventive = ?
                  AND status_deleted_attachment_history_maintenance_preventive = 0
                ORDER BY id_attachment_history_maintenance_preventive ASC";
        return $this->select_all($sql, [$id_history_maintenance_preventive]);
    }

    public function corrective_history(int $id_asset, int $limit = 5): array
    {
        $limit = max(1, min(10, $limit));
        $sql = "SELECT mc.id_maintenance_corrective,
                       mc.id_incident_maintenance_corrective,
                       mc.date_repair_maintenance_corrective,
                       mc.time_repair_maintenance_corrective,
                       mc.action_maintenance_corrective,
                       mc.observation_maintenance_corrective,
                       mc.status_active_maintenance_corrective,
                       mc.date_created_maintenance_corrective,
                       COALESCE(mco.name_maintenance_corrective_origin, 'Sin origen') AS name_maintenance_corrective_origin,
                       trm.name_type_responsible_maintenance
                FROM maintenances_corrective mc
                INNER JOIN types_responsible_maintenance trm ON mc.id_type_responsible_maintenance_maintenance_corrective = trm.id_type_responsible_maintenance
                LEFT JOIN maintenance_corrective_origins mco ON mc.id_origin_maintenance_corrective = mco.id_maintenance_corrective_origin
                WHERE mc.id_asset_maintenance_corrective = ?
                  AND mc.status_deleted_maintenance_corrective = 0
                ORDER BY COALESCE(mc.date_repair_maintenance_corrective, DATE(mc.date_created_maintenance_corrective)) DESC,
                         mc.id_maintenance_corrective DESC
                LIMIT {$limit}";
        return $this->select_all($sql, [$id_asset]);
    }

    public function corrective_attachments(int $id_maintenance_corrective): array
    {
        $sql = "SELECT id_attachment_maintenance_corrective,
                       route_attachment_maintenance_corrective AS route,
                       name_attachment_maintenance_corrective AS name,
                       date_created_attachment_maintenance_corrective AS created_at
                FROM attachments_maintenance_corrective
                WHERE id_maintenance_corrective = ?
                  AND status_deleted_attachment_maintenance_corrective = 0
                ORDER BY id_attachment_maintenance_corrective DESC";
        return $this->select_all($sql, [$id_maintenance_corrective]);
    }

    public function corrective_spare_parts(int $id_maintenance_corrective): array
    {
        $sql = "SELECT sp.code_spare_part,
                       sp.name_spare_part,
                       sp.unit_spare_part,
                       mcsp.quantity_maintenance_corrective_spare_part
                FROM maintenance_corrective_spare_parts mcsp
                INNER JOIN spare_parts sp ON mcsp.id_spare_part = sp.id_spare_part
                WHERE mcsp.id_maintenance_corrective = ?
                  AND mcsp.status_deleted_maintenance_corrective_spare_part = 0
                ORDER BY mcsp.id_maintenance_corrective_spare_part ASC";
        return $this->select_all($sql, [$id_maintenance_corrective]);
    }
}
