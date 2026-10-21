<?php

class ActionsMaintenancePreventiveModel extends Mysql
{
    private static $schemaInitialized = false;

    public function __construct()
    {
        parent::__construct();
        $this->ensure_schema();
    }

    private function ensure_schema(): void
    {
        if (self::$schemaInitialized) {
            return;
        }

        $sql = "CREATE TABLE IF NOT EXISTS parameters_maintenance_preventive_actions (
                    id_parameter_maintenance_preventive_action INT NOT NULL AUTO_INCREMENT,
                    name_parameter_maintenance_preventive_action VARCHAR(255) NOT NULL,
                    status_active_parameter_maintenance_preventive_action TINYINT NOT NULL DEFAULT 1,
                    status_deleted_parameter_maintenance_preventive_action TINYINT NOT NULL DEFAULT 0,
                    date_created_parameter_maintenance_preventive_action TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    date_updated_parameter_maintenance_preventive_action TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (id_parameter_maintenance_preventive_action)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        try {
            $this->update($sql);
        } catch (Throwable $e) {
            // Keep app running even if schema sync fails.
        }

        self::$schemaInitialized = true;
    }

    public function show_actions_maintenance_preventive()
    {
        $sql = "SELECT id_parameter_maintenance_preventive_action,
                       name_parameter_maintenance_preventive_action,
                       status_active_parameter_maintenance_preventive_action
                FROM parameters_maintenance_preventive_actions
                WHERE status_deleted_parameter_maintenance_preventive_action = 0
                ORDER BY id_parameter_maintenance_preventive_action DESC";
        return $this->select_all($sql);
    }

    public function show_action_maintenance_preventive(int $id)
    {
        $sql = "SELECT name_parameter_maintenance_preventive_action,
                       status_active_parameter_maintenance_preventive_action
                FROM parameters_maintenance_preventive_actions
                WHERE id_parameter_maintenance_preventive_action = ?
                  AND status_deleted_parameter_maintenance_preventive_action = 0";
        return $this->select($sql, [$id]);
    }

    public function insert_action_maintenance_preventive(string $name, int $status)
    {
        $query = "INSERT INTO parameters_maintenance_preventive_actions(
                    name_parameter_maintenance_preventive_action,
                    status_active_parameter_maintenance_preventive_action
                  ) VALUES(?,?)";
        return $this->insert($query, [$name, $status]);
    }

    public function update_action_maintenance_preventive(int $id, string $name, int $status)
    {
        $sql = "UPDATE parameters_maintenance_preventive_actions
                SET name_parameter_maintenance_preventive_action = ?,
                    status_active_parameter_maintenance_preventive_action = ?
                WHERE id_parameter_maintenance_preventive_action = ?";
        return $this->update($sql, [$name, $status, $id]);
    }

    public function delete_action_maintenance_preventive(int $id)
    {
        $sql = "UPDATE parameters_maintenance_preventive_actions
                SET status_deleted_parameter_maintenance_preventive_action = ?
                WHERE id_parameter_maintenance_preventive_action = ?";
        return $this->update($sql, [1, $id]);
    }

    public function combo_actions_maintenance_preventive()
    {
        $sql = "SELECT id_parameter_maintenance_preventive_action,
                       name_parameter_maintenance_preventive_action
                FROM parameters_maintenance_preventive_actions
                WHERE status_active_parameter_maintenance_preventive_action = 1
                  AND status_deleted_parameter_maintenance_preventive_action = 0
                ORDER BY name_parameter_maintenance_preventive_action ASC";
        return $this->select_all($sql);
    }

    public function show_actions_maintenance_preventive_by_ids(array $ids)
    {
        if (empty($ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT id_parameter_maintenance_preventive_action,
                       name_parameter_maintenance_preventive_action
                FROM parameters_maintenance_preventive_actions
                WHERE id_parameter_maintenance_preventive_action IN ($placeholders)
                  AND status_active_parameter_maintenance_preventive_action = 1
                  AND status_deleted_parameter_maintenance_preventive_action = 0
                ORDER BY name_parameter_maintenance_preventive_action ASC";
        return $this->select_all($sql, $ids);
    }
}
