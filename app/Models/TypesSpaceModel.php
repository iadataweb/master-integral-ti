<?php
class TypesSpaceModel extends Mysql
{
    public function show_types_space()
    {
        $sql = "SELECT id_type_space, name_type_space, status_active_type_space FROM types_space WHERE status_deleted_type_space = 0";
        return $this->select_all($sql);
    }

    public function show_type_space(int $id_type_space)
    {
        $sql = "SELECT name_type_space, status_active_type_space FROM types_space WHERE id_type_space = ? AND status_deleted_type_space = 0";
        return $this->select($sql, [$id_type_space]);
    }

    public function insert_type_space(string $name_type_space, int $status_active_type_space)
    {
        $query_insert = "INSERT INTO types_space(name_type_space, status_active_type_space) VALUES(?,?)";
        $data = [$name_type_space, $status_active_type_space];
        return $this->insert($query_insert, $data);
    }

    public function update_type_space(int $id_type_space, string $name_type_space, int $status_active_type_space)
    {
        $sql = "UPDATE types_space SET name_type_space = ?, status_active_type_space = ? WHERE id_type_space = ?";
        $data = [$name_type_space, $status_active_type_space, $id_type_space];
        return $this->update($sql, $data);
    }

    public function delete_type_space(int $id_type_space)
    {
        $sql = "UPDATE types_space SET status_deleted_type_space = ? WHERE id_type_space = ?";
        $data = [1, $id_type_space];
        return $this->update($sql, $data);
    }

    public function combo_types_space()
    {
        $sql = "SELECT id_type_space, name_type_space FROM types_space WHERE status_active_type_space = 1 AND status_deleted_type_space = 0";
        return $this->select_all($sql);
    }
}
