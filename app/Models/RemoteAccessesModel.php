<?php
class RemoteAccessesModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    private function baseSelect(): string
    {
        return "SELECT ra.*,
                       a.code_inventory_asset, a.model_asset, a.serial_number_asset,
                       ta.name_type_asset, ca.name_category_asset,
                       COALESCE(b.name_brand, 'Ninguna') AS name_brand,
                       COALESCE(co.name_condition_asset, 'No definido') AS name_condition_asset,
                       COALESCE(aeu.username_asset_equipment_user, 'No definido') AS equipment_username,
                       COALESCE(env.name_environment, 'No definido') AS current_location,
                       COALESCE(tm.name_type_movement, 'No definido') AS current_movement_type,
                       ma.date_created_movement_asset AS current_movement_date
                FROM remote_accesses ra
                INNER JOIN assets a ON a.id_asset = ra.id_asset_remote_access
                LEFT JOIN types_asset ta ON ta.id_type_asset = a.id_type_asset_asset
                LEFT JOIN categories_asset ca ON ca.id_category_asset = a.id_category_asset_asset
                LEFT JOIN brands b ON b.id_brand = a.id_brand_asset
                LEFT JOIN conditions_asset co ON co.id_condition_asset = a.id_condition_asset_asset
                LEFT JOIN asset_equipment_users aeu ON aeu.id_asset_asset_equipment_user = a.id_asset
                LEFT JOIN movements_asset ma ON ma.id_asset_movement_asset = a.id_asset
                    AND ma.status_record_current_movement_asset = 1
                    AND ma.status_deleted_movement_asset = 0
                LEFT JOIN environments env ON env.id_environment = ma.id_space_movement_asset
                LEFT JOIN types_movement tm ON tm.id_type_movement = ma.id_type_movement_asset";
    }

    public function show_remote_accesses(): array
    {
        $sql = $this->baseSelect() . " WHERE ra.status_deleted_remote_access = 0";
        $params = [];
        $sql .= " ORDER BY ra.id_remote_access DESC";
        $rows = $this->select_all($sql, $params);
        foreach ($rows as &$row) {
            $row['applications'] = $this->show_remote_access_details((int)$row['id_remote_access']);
        }
        return $rows;
    }

    public function show_remote_access(int $id): array|false
    {
        $sql = $this->baseSelect() . " WHERE ra.id_remote_access = ? AND ra.status_deleted_remote_access = 0";
        $row = $this->select($sql, [$id]);
        if (!$row) {
            return false;
        }
        $row['applications'] = $this->show_remote_access_details($id);
        return $row;
    }

    public function show_remote_access_details(int $id): array
    {
        $sql = "SELECT d.*, app.name_remote_access_application
                FROM remote_access_details d
                INNER JOIN remote_access_applications app
                    ON app.id_remote_access_application = d.id_remote_access_application_remote_access_detail
                WHERE d.id_remote_access = ?
                  AND d.status_deleted_remote_access_detail = 0";
        $params = [$id];
        $sql .= " ORDER BY app.name_remote_access_application ASC";
        return $this->select_all($sql, $params);
    }

    public function show_remote_access_applications(bool $onlyActive = true): array
    {
        $sql = "SELECT * FROM remote_access_applications WHERE status_deleted_remote_access_application = 0";
        if ($onlyActive) {
            $sql .= " AND status_active_remote_access_application = 1";
        }
        $sql .= " ORDER BY name_remote_access_application ASC";
        return $this->select_all($sql);
    }

    public function show_assets_for_remote_access(int $currentRemoteAccessId = 0): array
    {
        $sql = "SELECT a.id_asset, a.code_inventory_asset, a.model_asset, a.serial_number_asset,
                       ta.name_type_asset, ca.name_category_asset,
                       COALESCE(b.name_brand, 'Ninguna') AS name_brand,
                       COALESCE(co.name_condition_asset, 'No definido') AS name_condition_asset,
                       COALESCE(aeu.username_asset_equipment_user, 'No definido') AS equipment_username,
                       COALESCE(env.name_environment, 'No definido') AS current_location,
                       a.date_created_asset
                FROM assets a
                LEFT JOIN types_asset ta ON ta.id_type_asset = a.id_type_asset_asset
                LEFT JOIN categories_asset ca ON ca.id_category_asset = a.id_category_asset_asset
                LEFT JOIN brands b ON b.id_brand = a.id_brand_asset
                LEFT JOIN conditions_asset co ON co.id_condition_asset = a.id_condition_asset_asset
                LEFT JOIN asset_equipment_users aeu ON aeu.id_asset_asset_equipment_user = a.id_asset
                LEFT JOIN movements_asset ma ON ma.id_asset_movement_asset = a.id_asset
                    AND ma.status_record_current_movement_asset = 1
                    AND ma.status_deleted_movement_asset = 0
                LEFT JOIN environments env ON env.id_environment = ma.id_space_movement_asset
                WHERE a.status_deleted_asset = 0
                  AND NOT EXISTS (
                      SELECT 1
                      FROM remote_accesses ra_filter
                      WHERE ra_filter.id_asset_remote_access = a.id_asset
                        AND ra_filter.status_deleted_remote_access = 0
                        AND ra_filter.id_remote_access <> ?
                  )
                ORDER BY a.id_asset DESC";
        return $this->select_all($sql, [$currentRemoteAccessId]);
    }

    public function asset_has_remote_access(int $idAsset, int $excludeId = 0): bool
    {
        $sql = "SELECT id_remote_access FROM remote_accesses
                WHERE id_asset_remote_access = ?
                  AND status_deleted_remote_access = 0";
        $params = [$idAsset];
        if ($excludeId > 0) {
            $sql .= " AND id_remote_access <> ?";
            $params[] = $excludeId;
        }
        return !empty($this->select($sql, $params));
    }

    public function insert_remote_access(array $data): int
    {
        if ($this->asset_has_remote_access((int)$data['id_asset'])) {
            return -1;
        }
        $sql = "INSERT INTO remote_accesses(
                    id_asset_remote_access,
                    id_remote_access_application_remote_access,
                    access_code_remote_access,
                    description_remote_access,
                    status_active_remote_access,
                    id_user_created_remote_access
                ) VALUES (?, NULL, NULL, NULL, ?, ?)";
        $id = (int)$this->insert($sql, [
            $data['id_asset'],
            $data['status_active'],
            $data['id_user']
        ]);
        if ($id > 0) {
            $this->save_details($id, $data['applications'], $data['id_user'], false);
            $this->insert_history($id, null, $this->summary_details($data['applications']), null, null, 'created', 'Registro principal de acceso remoto creado.', $data['id_user']);
        }
        return $id;
    }

    public function update_remote_access(int $id, array $data): bool
    {
        $current = $this->show_remote_access($id);
        if (!$current || $this->asset_has_remote_access((int)$data['id_asset'], $id)) {
            return false;
        }
        $updated = $this->update(
            "UPDATE remote_accesses SET
                id_asset_remote_access = ?,
                status_active_remote_access = ?,
                id_user_updated_remote_access = ?
             WHERE id_remote_access = ?",
            [$data['id_asset'], $data['status_active'], $data['id_user'], $id]
        );
        if ($updated) {
            $previousSummary = $this->summary_details($current['applications'] ?? []);
            $this->save_details($id, $data['applications'], $data['id_user'], true);
            $this->insert_history($id, $previousSummary, $this->summary_details($data['applications']), null, null, 'updated', 'Aplicaciones de acceso remoto actualizadas.', $data['id_user']);
        }
        return $updated;
    }

    public function delete_remote_access(int $id, int $id_user): bool
    {
        $deleted = $this->update(
            "UPDATE remote_accesses SET status_deleted_remote_access = 1, id_user_updated_remote_access = ? WHERE id_remote_access = ?",
            [$id_user, $id]
        );
        if ($deleted) {
            $this->update(
                "UPDATE remote_access_details SET status_deleted_remote_access_detail = 1, id_user_updated_remote_access_detail = ? WHERE id_remote_access = ?",
                [$id_user, $id]
            );
            $this->insert_history($id, null, null, null, null, 'deleted', 'Registro eliminado logicamente.', $id_user);
        }
        return $deleted;
    }

    public function show_remote_access_history(int $id): array
    {
        $sql = "SELECT h.*, TRIM(CONCAT(
                    COALESCE(pt.abbreviation_professional_title, ''), ' ',
                    COALESCE(
                        CASE e.preferred_name_employee
                            WHEN 1 THEN e.first_name_employee
                            WHEN 2 THEN e.middle_name_employee
                            ELSE e.first_name_employee
                        END,
                        e.first_name_employee,
                        u.name_user,
                        'Sistema'
                    ), ' ',
                    COALESCE(e.paternal_last_name_employee, ''), ' ',
                    COALESCE(e.maternal_last_name_employee, '')
                )) AS display_name
                FROM remote_access_history h
                LEFT JOIN users u ON u.id_user = h.id_user_remote_access_history
                LEFT JOIN employees e ON e.id_employee = u.id_employee_user
                LEFT JOIN professional_titles pt ON pt.id_professional_title = e.id_professional_title_employee
                WHERE h.id_remote_access = ?
                ORDER BY h.date_created_remote_access_history DESC";
        return $this->select_all($sql, [$id]);
    }

    private function save_details(int $id, array $applications, int $idUser, bool $replace): void
    {
        if ($replace) {
            $this->update(
                "UPDATE remote_access_details SET status_deleted_remote_access_detail = 1, id_user_updated_remote_access_detail = ? WHERE id_remote_access = ?",
                [$idUser, $id]
            );
        }
        $sql = "INSERT INTO remote_access_details(
                    id_remote_access,
                    id_remote_access_application_remote_access_detail,
                    access_code_remote_access_detail,
                    description_remote_access_detail,
                    observations_remote_access_detail,
                    status_active_remote_access_detail,
                    id_user_created_remote_access_detail
                ) VALUES (?, ?, ?, ?, ?, ?, ?)";
        foreach ($applications as $item) {
            $this->insert($sql, [
                $id,
                $item['id_application'],
                $item['access_code'],
                $item['description'],
                $item['observations'],
                $item['status_active'],
                $idUser
            ]);
        }
    }

    private function summary_details(array $applications): string
    {
        $labels = [];
        foreach ($applications as $item) {
            $name = $item['name_remote_access_application'] ?? $this->application_name((int)($item['id_application'] ?? $item['id_remote_access_application_remote_access_detail'] ?? 0));
            $code = $item['access_code'] ?? $item['access_code_remote_access_detail'] ?? '';
            $labels[] = trim(($name ?: 'Aplicacion') . ': ' . $code);
        }
        return implode(' | ', array_filter($labels));
    }

    private function application_name(int $id): ?string
    {
        $row = $this->select("SELECT name_remote_access_application FROM remote_access_applications WHERE id_remote_access_application = ?", [$id]);
        return $row['name_remote_access_application'] ?? null;
    }

    private function insert_history(int $id, ?string $previousApplication, ?string $newApplication, ?string $previousCode, ?string $newCode, string $action, string $detail, int $id_user): int
    {
        $sql = "INSERT INTO remote_access_history(
                    id_remote_access,
                    previous_application_remote_access_history,
                    new_application_remote_access_history,
                    previous_access_code_remote_access_history,
                    new_access_code_remote_access_history,
                    action_remote_access_history,
                    detail_remote_access_history,
                    id_user_remote_access_history
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        return (int)$this->insert($sql, [$id, $previousApplication, $newApplication, $previousCode, $newCode, $action, $detail, $id_user]);
    }
}

