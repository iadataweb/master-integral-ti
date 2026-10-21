<?php

class TypesLicenseModel extends Mysql
{
    public function show_types_license()
    {
        $sql = "SELECT id_type_license, name_type_license, status_active_type_license
                FROM types_license
                WHERE status_deleted_type_license = 0";
        return $this->select_all($sql);
    }

    public function show_type_license(int $id_type_license)
    {
        $sql = "SELECT name_type_license, status_active_type_license
                FROM types_license
                WHERE id_type_license = ?
                AND status_deleted_type_license = 0";
        return $this->select($sql, [$id_type_license]);
    }

    public function insert_type_license(string $name_type_license, int $status_active_type_license)
    {
        $query_insert = "INSERT INTO types_license(name_type_license, status_active_type_license) VALUES(?,?)";
        $data = [$name_type_license, $status_active_type_license];
        return $this->insert($query_insert, $data);
    }

    public function update_type_license(int $id_type_license, string $name_type_license, int $status_active_type_license)
    {
        $sql = "UPDATE types_license SET name_type_license = ?, status_active_type_license = ? WHERE id_type_license = ?";
        $data = [$name_type_license, $status_active_type_license, $id_type_license];
        return $this->update($sql, $data);
    }

    public function delete_type_license(int $id_type_license)
    {
        $sql = "UPDATE types_license SET status_deleted_type_license = ? WHERE id_type_license = ?";
        $data = [1, $id_type_license];
        return $this->update($sql, $data);
    }

    public function combo_types_license()
    {
        $sql = "SELECT id_type_license, name_type_license
                FROM types_license
                WHERE status_active_type_license = 1
                AND status_deleted_type_license = 0";
        return $this->select_all($sql);
    }
}
