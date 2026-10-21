<?php
class CapacityUnitsModel extends Mysql
{
    public function show_capacity_units()
    {
        $sql = "SELECT id_capacity_unit, name_capacity_unit, status_active_capacity_unit
                FROM capacity_units
                WHERE status_deleted_capacity_unit = 0";
        return $this->select_all($sql);
    }

    public function show_capacity_unit(int $id_capacity_unit)
    {
        $sql = "SELECT name_capacity_unit, status_active_capacity_unit
                FROM capacity_units
                WHERE id_capacity_unit = ?
                AND status_deleted_capacity_unit = 0";
        return $this->select($sql, [$id_capacity_unit]);
    }

    public function insert_capacity_unit(string $name_capacity_unit, int $status_active_capacity_unit)
    {
        $query_insert = "INSERT INTO capacity_units(name_capacity_unit, status_active_capacity_unit) VALUES(?,?)";
        $data = [$name_capacity_unit, $status_active_capacity_unit];
        return $this->insert($query_insert, $data);
    }

    public function update_capacity_unit(int $id_capacity_unit, string $name_capacity_unit, int $status_active_capacity_unit)
    {
        $sql = "UPDATE capacity_units SET name_capacity_unit = ?, status_active_capacity_unit = ? WHERE id_capacity_unit = ?";
        $data = [$name_capacity_unit, $status_active_capacity_unit, $id_capacity_unit];
        return $this->update($sql, $data);
    }

    public function delete_capacity_unit(int $id_capacity_unit)
    {
        $sql = "UPDATE capacity_units SET status_deleted_capacity_unit = ? WHERE id_capacity_unit = ?";
        $data = [1, $id_capacity_unit];
        return $this->update($sql, $data);
    }

    public function combo_capacity_units()
    {
        $sql = "SELECT id_capacity_unit, name_capacity_unit
                FROM capacity_units
                WHERE status_active_capacity_unit = 1
                AND status_deleted_capacity_unit = 0";
        return $this->select_all($sql);
    }
}
