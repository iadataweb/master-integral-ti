<?php
class ConditionsAssetModel extends Mysql
{
    public function show_conditions_asset()
    {
        $sql = "SELECT id_condition_asset, name_condition_asset, status_active_condition_asset
                FROM conditions_asset
                WHERE status_deleted_condition_asset = 0";
        return $this->select_all($sql);
    }

    public function show_condition_asset(int $id_condition_asset)
    {
        $sql = "SELECT name_condition_asset, status_active_condition_asset
                FROM conditions_asset
                WHERE id_condition_asset = ?
                AND status_deleted_condition_asset = 0";
        return $this->select($sql, [$id_condition_asset]);
    }

    public function insert_condition_asset(string $name_condition_asset, int $status_active_condition_asset)
    {
        $query_insert = "INSERT INTO conditions_asset(name_condition_asset, status_active_condition_asset) VALUES(?,?)";
        $data = [$name_condition_asset, $status_active_condition_asset];
        return $this->insert($query_insert, $data);
    }

    public function update_condition_asset(int $id_condition_asset, string $name_condition_asset, int $status_active_condition_asset)
    {
        $sql = "UPDATE conditions_asset SET name_condition_asset = ?, status_active_condition_asset = ? WHERE id_condition_asset = ?";
        $data = [$name_condition_asset, $status_active_condition_asset, $id_condition_asset];
        return $this->update($sql, $data);
    }

    public function delete_condition_asset(int $id_condition_asset)
    {
        $sql = "UPDATE conditions_asset SET status_deleted_condition_asset = ? WHERE id_condition_asset = ?";
        $data = [1, $id_condition_asset];
        return $this->update($sql, $data);
    }

    public function combo_conditions_asset()
    {
        $sql = "SELECT id_condition_asset, name_condition_asset
                FROM conditions_asset
                WHERE status_active_condition_asset = 1
                AND status_deleted_condition_asset = 0";
        return $this->select_all($sql);
    }
}
