<?php
class PrioritiesModel extends Mysql
{
    public function show_priorities()
    {
        $sql = "SELECT id_priority, name_priority FROM priorities WHERE status_active_priority = 1 AND status_deleted_priority = 0";
        return $this->select_all($sql);
    }

    public function show_priority(int $id_priority)
    {
        $sql = "SELECT name_priority FROM priorities WHERE id_priority = ? AND status_active_priority = 1 AND status_deleted_priority = 0";
        return $this->select($sql, [$id_priority]);
    }

    public function insert_priority(string $name_priority)
    {
        $query_insert = "INSERT INTO priorities(name_priority) VALUES(?)";
        $data = [$name_priority];
        return $this->insert($query_insert, $data);
    }

    public function update_priority(int $id_priority, string $name_priority)
    {
        $sql = "UPDATE priorities SET name_priority = ? WHERE id_priority = ?";
        $data = [$name_priority, $id_priority];
        return $this->update($sql, $data);
    }

    public function delete_priority(int $id_priority)
    {
        $sql = "UPDATE priorities SET status_deleted_priority = ? WHERE id_priority = ?";
        $data = [1, $id_priority];
        return $this->update($sql, $data);
    }

    public function combo_priorities()
    {
        $sql = "SELECT id_priority, name_priority FROM priorities WHERE status_active_priority = 1 AND status_deleted_priority = 0";
        return $this->select_all($sql);
    }
}
