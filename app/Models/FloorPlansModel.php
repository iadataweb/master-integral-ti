<?php
class FloorPlansModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show_floor_plans(): array
    {
        $sql = "SELECT fp.id_floor_plan, fp.name_floor_plan, fp.description_floor_plan, fp.floor_floor_plan,
                       fp.current_file_floor_plan, fp.current_file_name_floor_plan, fp.current_image_floor_plan,
                       fp.current_image_name_floor_plan, fp.current_image_width_floor_plan, fp.current_image_height_floor_plan,
                       fp.version_floor_plan, fp.status_active_floor_plan, fp.date_updated_floor_plan, c.name_campus
                FROM floor_plans fp
                INNER JOIN campuses c ON c.id_campus = fp.id_campus_floor_plan
                WHERE fp.status_deleted_floor_plan = 0
                ORDER BY fp.id_floor_plan DESC";
        return $this->select_all($sql);
    }

    public function show_floor_plan(int $id): array|false
    {
        $sql = "SELECT fp.*, c.name_campus
                FROM floor_plans fp
                INNER JOIN campuses c ON c.id_campus = fp.id_campus_floor_plan
                WHERE fp.id_floor_plan = ? AND fp.status_deleted_floor_plan = 0";
        return $this->select($sql, [$id]);
    }

    public function combo_floor_plans(?int $id_campus = null, ?int $floor = null): array
    {
        $where = "fp.status_deleted_floor_plan = 0 AND fp.status_active_floor_plan = 1";
        $params = [];
        if ($id_campus !== null && $id_campus > 0) {
            $where .= " AND fp.id_campus_floor_plan = ?";
            $params[] = $id_campus;
        }
        if ($floor !== null) {
            $where .= " AND fp.floor_floor_plan = ?";
            $params[] = $floor;
        }
        $sql = "SELECT fp.id_floor_plan, fp.name_floor_plan, fp.floor_floor_plan, fp.current_file_floor_plan,
                       fp.current_image_floor_plan, fp.current_image_width_floor_plan, fp.current_image_height_floor_plan,
                       c.name_campus
                FROM floor_plans fp
                INNER JOIN campuses c ON c.id_campus = fp.id_campus_floor_plan
                WHERE $where
                ORDER BY c.name_campus ASC, fp.floor_floor_plan ASC, fp.name_floor_plan ASC";
        return $this->select_all($sql, $params);
    }

    public function insert_floor_plan(array $data): int
    {
        $sql = "INSERT INTO floor_plans(
                    name_floor_plan, description_floor_plan, id_campus_floor_plan, floor_floor_plan,
                    current_file_floor_plan, current_file_name_floor_plan, current_image_floor_plan,
                    current_image_name_floor_plan, current_image_width_floor_plan, current_image_height_floor_plan,
                    version_floor_plan, status_active_floor_plan, id_user_created_floor_plan
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?)";
        $id = (int)$this->insert($sql, [
            $data['name'],
            $data['description'],
            $data['id_campus'],
            $data['floor'],
            $data['file_route'],
            $data['file_name'],
            $data['image_route'],
            $data['image_name'],
            $data['image_width'],
            $data['image_height'],
            $data['status_active'],
            $data['id_user']
        ]);
        if ($id > 0) {
            $this->insert_version(
                $id,
                1,
                $data['file_route'],
                $data['file_name'],
                $data['image_route'],
                $data['image_width'],
                $data['image_height'],
                'Registro inicial.',
                $data['id_user']
            );
        }
        return $id;
    }

    public function update_floor_plan(int $id, array $data): bool
    {
        $current = $this->show_floor_plan($id);
        if (!$current) {
            return false;
        }

        $newVersion = (int)$current['version_floor_plan'];
        $params = [
            $data['name'],
            $data['description'],
            $data['id_campus'],
            $data['floor'],
            $data['status_active'],
            $data['id_user']
        ];
        $fileSet = "";
        if (!empty($data['file_route'])) {
            $newVersion++;
            $fileSet = ", current_file_floor_plan = ?, current_file_name_floor_plan = ?, current_image_floor_plan = ?, current_image_name_floor_plan = ?, current_image_width_floor_plan = ?, current_image_height_floor_plan = ?, version_floor_plan = ?";
            $params[] = $data['file_route'];
            $params[] = $data['file_name'];
            $params[] = $data['image_route'];
            $params[] = $data['image_name'];
            $params[] = $data['image_width'];
            $params[] = $data['image_height'];
            $params[] = $newVersion;
        }
        $params[] = $id;

        $sql = "UPDATE floor_plans
                SET name_floor_plan = ?, description_floor_plan = ?, id_campus_floor_plan = ?,
                    floor_floor_plan = ?, status_active_floor_plan = ?, id_user_updated_floor_plan = ?
                    $fileSet
                WHERE id_floor_plan = ? AND status_deleted_floor_plan = 0";
        $updated = $this->update($sql, $params);
        if ($updated && !empty($data['file_route'])) {
            $this->insert_version(
                $id,
                $newVersion,
                $data['file_route'],
                $data['file_name'],
                $data['image_route'],
                $data['image_width'],
                $data['image_height'],
                'Archivo reemplazado.',
                $data['id_user']
            );
        }
        return $updated;
    }

    public function delete_floor_plan(int $id, int $id_user): bool
    {
        $sql = "UPDATE floor_plans
                SET status_deleted_floor_plan = 1, id_user_updated_floor_plan = ?
                WHERE id_floor_plan = ?";
        return $this->update($sql, [$id_user, $id]);
    }

    public function show_versions(int $id): array
    {
        $sql = "SELECT *
                FROM floor_plan_versions
                WHERE id_floor_plan = ?
                ORDER BY version_number_floor_plan_version DESC, id_floor_plan_version DESC";
        return $this->select_all($sql, [$id]);
    }

    public function campuses(): array
    {
        $sql = "SELECT id_campus, name_campus
                FROM campuses
                WHERE status_deleted_campus = 0 AND status_active_campus = 1
                ORDER BY name_campus ASC";
        return $this->select_all($sql);
    }

    private function insert_version(int $id, int $version, string $route, string $name, string $image_route, ?int $image_width, ?int $image_height, string $observation, int $id_user): int
    {
        $sql = "INSERT INTO floor_plan_versions(
                    id_floor_plan, version_number_floor_plan_version, route_file_floor_plan_version,
                    name_file_floor_plan_version, route_image_floor_plan_version, image_width_floor_plan_version,
                    image_height_floor_plan_version, observation_floor_plan_version, id_user_floor_plan_version
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        return (int)$this->insert($sql, [$id, $version, $route, $name, $image_route, $image_width, $image_height, $observation, $id_user]);
    }
}
?>