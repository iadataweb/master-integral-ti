<?php
class PositionsModel extends Mysql
{
    public function show_positions()
    {
        $sql = "SELECT id_position, name_position, status_active_position FROM positions WHERE status_deleted_position = 0";
        return $this->select_all($sql);
    }

    public function show_position(int $id_position)
    {
        $sql = "SELECT name_position, status_active_position FROM positions WHERE id_position = ? AND status_deleted_position = 0";
        return $this->select($sql, [$id_position]);
    }

    public function insert_position(string $name_position, int $status_active_position)
    {
        $query_insert = "INSERT INTO positions(name_position, status_active_position) VALUES(?,?)";
        $data = [$name_position, $status_active_position];
        return $this->insert($query_insert, $data);
    }

    public function update_position(int $id_position, string $name_position, int $status_active_position)
    {
        $sql = "UPDATE positions SET name_position = ?, status_active_position = ? WHERE id_position = ?";
        $data = [$name_position, $status_active_position, $id_position];
        return $this->update($sql, $data);
    }

    public function delete_position(int $id_position)
    {
        $sql = "UPDATE positions SET status_deleted_position = ? WHERE id_position = ?";
        $data = [1, $id_position];
        return $this->update($sql, $data);
    }

    public function combo_positions()
    {
        $sql = "SELECT id_position, name_position FROM positions WHERE status_active_position = 1 AND status_deleted_position = 0";
        return $this->select_all($sql);
    }
}
