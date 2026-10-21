<?php
class CampusesModel extends Mysql
{
    public function show_campuses()
    {
        $sql = "SELECT id_campus, name_campus, address_campus, status_active_campus FROM campuses WHERE status_deleted_campus = 0";
        return $this->select_all($sql);
    }

    public function show_campus(int $id_campus)
    {
        $sql = "SELECT name_campus, address_campus, status_active_campus FROM campuses WHERE id_campus = ? AND status_deleted_campus = 0";
        return $this->select($sql, [$id_campus]);
    }

    public function insert_campus(string $name_campus, string $address_campus, int $status_active_campus)
    {
        $query_insert = "INSERT INTO campuses(name_campus, address_campus, status_active_campus) VALUES(?,?,?)";
        $data = [$name_campus, $address_campus, $status_active_campus];
        return $this->insert($query_insert, $data);
    }

    public function update_campus(int $id_campus, string $name_campus, string $address_campus, int $status_active_campus)
    {
        $sql = "UPDATE campuses SET name_campus = ?, address_campus = ?, status_active_campus = ? WHERE id_campus = ?";
        $data = [$name_campus, $address_campus, $status_active_campus, $id_campus];
        return $this->update($sql, $data);
    }

    public function delete_campus(int $id_campus)
    {
        $sql = "UPDATE campuses SET status_deleted_campus = ? WHERE id_campus = ?";
        $data = [1, $id_campus];
        return $this->update($sql, $data);
    }

    public function combo_campuses()
    {
        $sql = "SELECT id_campus, name_campus FROM campuses WHERE status_active_campus = 1 AND status_deleted_campus = 0";
        return $this->select_all($sql);
    }
}
