<?php
class ArchitecturesModel extends Mysql
{
    public function show_architectures()
    {
        $sql = "SELECT id_architecture, name_architecture, status_active_architecture
                FROM architectures
                WHERE status_deleted_architecture = 0";
        return $this->select_all($sql);
    }

    public function show_architecture(int $id_architecture)
    {
        $sql = "SELECT name_architecture, status_active_architecture
                FROM architectures
                WHERE id_architecture = ?
                AND status_deleted_architecture = 0";
        return $this->select($sql, [$id_architecture]);
    }

    public function insert_architecture(string $name_architecture, int $status_active_architecture)
    {
        $query_insert = "INSERT INTO architectures(name_architecture, status_active_architecture) VALUES(?,?)";
        $data = [$name_architecture, $status_active_architecture];
        return $this->insert($query_insert, $data);
    }

    public function update_architecture(int $id_architecture, string $name_architecture, int $status_active_architecture)
    {
        $sql = "UPDATE architectures SET name_architecture = ?, status_active_architecture = ? WHERE id_architecture = ?";
        $data = [$name_architecture, $status_active_architecture, $id_architecture];
        return $this->update($sql, $data);
    }

    public function delete_architecture(int $id_architecture)
    {
        $sql = "UPDATE architectures SET status_deleted_architecture = ? WHERE id_architecture = ?";
        $data = [1, $id_architecture];
        return $this->update($sql, $data);
    }

    public function combo_architectures()
    {
        $sql = "SELECT id_architecture, name_architecture
                FROM architectures
                WHERE status_active_architecture = 1
                AND status_deleted_architecture = 0";
        return $this->select_all($sql);
    }
}
