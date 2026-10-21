<?php
class BrandsModel extends Mysql
{
    public function show_brands()
    {
        $sql = "SELECT id_brand, name_brand, status_active_brand FROM brands WHERE status_deleted_brand = 0";
        return $this->select_all($sql);
    }

    public function show_brand(int $id_brand)
    {
        $sql = "SELECT name_brand, status_active_brand FROM brands WHERE id_brand = ? AND status_deleted_brand = 0";
        return $this->select($sql, [$id_brand]);
    }

    public function insert_brand(string $name_brand, int $status_active_brand)
    {
        $query_insert = "INSERT INTO brands(name_brand, status_active_brand) VALUES(?,?)";
        $data = [$name_brand, $status_active_brand];
        return $this->insert($query_insert, $data);
    }

    public function update_brand(int $id_brand, string $name_brand, int $status_active_brand)
    {
        $sql = "UPDATE brands SET name_brand = ?, status_active_brand = ? WHERE id_brand = ?";
        $data = [$name_brand, $status_active_brand, $id_brand];
        return $this->update($sql, $data);
    }

    public function delete_brand(int $id_brand)
    {
        $sql = "UPDATE brands SET status_deleted_brand = ? WHERE id_brand = ?";
        $data = [1, $id_brand];
        return $this->update($sql, $data);
    }

    public function combo_brands()
    {
        $sql = "SELECT id_brand, name_brand FROM brands WHERE status_active_brand = 1 AND status_deleted_brand = 0";
        return $this->select_all($sql);
    }
}
