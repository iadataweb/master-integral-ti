<?php
class TypesStorageModel extends Mysql
{
    public function show_types_storage()
    {
        $sql = "SELECT id_type_storage, name_type_storage, status_active_type_storage
                FROM types_storage
                WHERE status_deleted_type_storage = 0";
        return $this->select_all($sql);
    }

    public function show_type_storage(int $id_type_storage)
    {
        $sql = "SELECT name_type_storage, status_active_type_storage
                FROM types_storage
                WHERE id_type_storage = ?
                AND status_deleted_type_storage = 0";
        return $this->select($sql, [$id_type_storage]);
    }

    public function insert_type_storage(string $name_type_storage, int $status_active_type_storage)
    {
        $query_insert = "INSERT INTO types_storage(name_type_storage, status_active_type_storage) VALUES(?,?)";
        $data = [$name_type_storage, $status_active_type_storage];
        return $this->insert($query_insert, $data);
    }

    public function update_type_storage(int $id_type_storage, string $name_type_storage, int $status_active_type_storage)
    {
        $sql = "UPDATE types_storage SET name_type_storage = ?, status_active_type_storage = ? WHERE id_type_storage = ?";
        $data = [$name_type_storage, $status_active_type_storage, $id_type_storage];
        return $this->update($sql, $data);
    }

    public function delete_type_storage(int $id_type_storage)
    {
        $sql = "UPDATE types_storage SET status_deleted_type_storage = ? WHERE id_type_storage = ?";
        $data = [1, $id_type_storage];
        return $this->update($sql, $data);
    }

    public function combo_types_storage()
    {
        $sql = "SELECT id_type_storage, name_type_storage
                FROM types_storage
                WHERE status_active_type_storage = 1
                AND status_deleted_type_storage = 0";
        return $this->select_all($sql);
    }
}
