<?php
class AreasModel extends Mysql
{
    public function show_areas()
    {
        $sql = "SELECT id_area, name_area, status_active_area FROM areas WHERE status_deleted_area = 0";
        return $this->select_all($sql);
    }

    public function show_area(int $id_area)
    {
        $sql = "SELECT name_area, status_active_area FROM areas WHERE id_area = ? AND status_deleted_area = 0";
        return $this->select($sql, [$id_area]);
    }

    public function insert_area(string $name_area, int $status_active_area)
    {
        $query_insert = "INSERT INTO areas(name_area, status_active_area) VALUES(?,?)";
        $data = [$name_area, $status_active_area];
        return $this->insert($query_insert, $data);
    }

    public function update_area(int $id_area, string $name_area, int $status_active_area)
    {
        $sql = "UPDATE areas SET name_area = ?, status_active_area = ? WHERE id_area = ?";
        $data = [$name_area, $status_active_area, $id_area];
        return $this->update($sql, $data);
    }

    public function delete_area(int $id_area)
    {
        $sql = "UPDATE areas SET status_deleted_area = ? WHERE id_area = ?";
        $data = [1, $id_area];
        return $this->update($sql, $data);
    }

    public function combo_areas()
    {
        $sql = "SELECT id_area, name_area FROM areas WHERE status_active_area = 1 AND status_deleted_area = 0";
        return $this->select_all($sql);
    }
}
