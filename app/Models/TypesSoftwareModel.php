<?php
class TypesSoftwareModel extends Mysql
{
    public function show_types_software()
    {
        $sql = "SELECT id_type_software, name_type_software, status_active_type_software
                FROM types_software
                WHERE status_deleted_type_software = 0";
        return $this->select_all($sql);
    }

    public function show_type_software(int $id_type_software)
    {
        $sql = "SELECT name_type_software, status_active_type_software
                FROM types_software
                WHERE id_type_software = ?
                AND status_deleted_type_software = 0";
        return $this->select($sql, [$id_type_software]);
    }

    public function insert_type_software(string $name_type_software, int $status_active_type_software)
    {
        $query_insert = "INSERT INTO types_software(name_type_software, status_active_type_software) VALUES(?,?)";
        $data = [$name_type_software, $status_active_type_software];
        return $this->insert($query_insert, $data);
    }

    public function update_type_software(int $id_type_software, string $name_type_software, int $status_active_type_software)
    {
        $sql = "UPDATE types_software SET name_type_software = ?, status_active_type_software = ? WHERE id_type_software = ?";
        $data = [$name_type_software, $status_active_type_software, $id_type_software];
        return $this->update($sql, $data);
    }

    public function delete_type_software(int $id_type_software)
    {
        $sql = "UPDATE types_software SET status_deleted_type_software = ? WHERE id_type_software = ?";
        $data = [1, $id_type_software];
        return $this->update($sql, $data);
    }

    public function combo_types_software()
    {
        $sql = "SELECT id_type_software, name_type_software
                FROM types_software
                WHERE status_active_type_software = 1
                AND status_deleted_type_software = 0";
        return $this->select_all($sql);
    }
}
