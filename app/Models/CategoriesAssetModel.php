<?php
class CategoriesAssetModel extends Mysql
{
    public function show_categories_asset()
    {
        $sql = "SELECT id_category_asset, name_category_asset, status_active_category_asset
                FROM categories_asset
                WHERE status_deleted_category_asset = 0";
        return $this->select_all($sql);
    }

    public function show_category_asset(int $id_category_asset)
    {
        $sql = "SELECT name_category_asset, status_active_category_asset
                FROM categories_asset
                WHERE id_category_asset = ?
                AND status_deleted_category_asset = 0";
        return $this->select($sql, [$id_category_asset]);
    }

    public function insert_category_asset(string $name_category_asset, int $status_active_category_asset)
    {
        $query_insert = "INSERT INTO categories_asset(name_category_asset, status_active_category_asset) VALUES(?,?)";
        $data = [$name_category_asset, $status_active_category_asset];
        return $this->insert($query_insert, $data);
    }

    public function update_category_asset(int $id_category_asset, string $name_category_asset, int $status_active_category_asset)
    {
        $sql = "UPDATE categories_asset SET name_category_asset = ?, status_active_category_asset = ? WHERE id_category_asset = ?";
        $data = [$name_category_asset, $status_active_category_asset, $id_category_asset];
        return $this->update($sql, $data);
    }

    public function delete_category_asset(int $id_category_asset)
    {
        $sql = "UPDATE categories_asset SET status_deleted_category_asset = ? WHERE id_category_asset = ?";
        $data = [1, $id_category_asset];
        return $this->update($sql, $data);
    }

    public function combo_categories_asset()
    {
        $sql = "SELECT id_category_asset, name_category_asset
                FROM categories_asset
                WHERE status_active_category_asset = 1
                AND status_deleted_category_asset = 0";
        return $this->select_all($sql);
    }
}
