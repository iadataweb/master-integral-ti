<?php
class TypesAssetModel extends Mysql
{
    public function show_types_asset()
    {
        $sql = "SELECT ta.id_type_asset, ta.id_category_asset_type_asset, ta.name_type_asset, ca.name_category_asset, ta.status_active_type_asset
                FROM types_asset ta
                INNER JOIN categories_asset ca ON ta.id_category_asset_type_asset = ca.id_category_asset
                WHERE ta.status_deleted_type_asset = 0";
        return $this->select_all($sql);
    }

    public function show_type_asset(int $id_type_asset)
    {
        $sql = "SELECT ta.id_category_asset_type_asset, ta.name_type_asset, ca.name_category_asset, ta.status_active_type_asset
                FROM types_asset ta
                INNER JOIN categories_asset ca ON ta.id_category_asset_type_asset = ca.id_category_asset
                WHERE ta.id_type_asset = ?
                AND ta.status_deleted_type_asset = 0";
        return $this->select($sql, [$id_type_asset]);
    }

    public function insert_type_asset(int $id_category_asset_type_asset, string $name_type_asset, int $status_active_type_asset)
    {
        $query_insert = "INSERT INTO types_asset(id_category_asset_type_asset, name_type_asset, status_active_type_asset) VALUES(?,?,?)";
        $data = [$id_category_asset_type_asset, $name_type_asset, $status_active_type_asset];
        return $this->insert($query_insert, $data);
    }

    public function update_type_asset(int $id_type_asset, int $id_category_asset_type_asset, string $name_type_asset, int $status_active_type_asset)
    {
        $sql = "UPDATE types_asset
                SET id_category_asset_type_asset = ?, name_type_asset = ?, status_active_type_asset = ?
                WHERE id_type_asset = ?";
        $data = [$id_category_asset_type_asset, $name_type_asset, $status_active_type_asset, $id_type_asset];
        return $this->update($sql, $data);
    }

    public function delete_type_asset(int $id_type_asset)
    {
        $sql = "UPDATE types_asset SET status_deleted_type_asset = ? WHERE id_type_asset = ?";
        $data = [1, $id_type_asset];
        return $this->update($sql, $data);
    }

    public function combo_types_asset($idcategoria)
    {
        $sql = "SELECT id_type_asset, name_type_asset
                FROM types_asset
                WHERE id_category_asset_type_asset = ?
                AND status_active_type_asset = 1
                AND status_deleted_type_asset = 0";
        return $this->select_all($sql, [$idcategoria]);
    }
}
