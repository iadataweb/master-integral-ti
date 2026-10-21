<?php
class CategoriesModel extends Mysql
{
    public function show_categories()
    {
        $sql = "SELECT id_category, name_category FROM categories WHERE status_active_category = 1 AND status_deleted_category = 0";
        return $this->select_all($sql);
    }

    public function show_category(int $id_category)
    {
        $sql = "SELECT name_category FROM categories WHERE id_category = ? AND status_active_category = 1 AND status_deleted_category = 0";
        return $this->select($sql, [$id_category]);
    }

    public function insert_category(string $name_category)
    {
        $query_insert = "INSERT INTO categories(name_category) VALUES(?)";
        $data = [$name_category];
        return $this->insert($query_insert, $data);
    }

    public function update_category(int $id_category, string $name_category)
    {
        $sql = "UPDATE categories SET name_category = ? WHERE id_category = ?";
        $data = [$name_category, $id_category];
        return $this->update($sql, $data);
    }

    public function delete_category(int $id_category)
    {
        $sql = "UPDATE categories SET status_deleted_category = ? WHERE id_category = ?";
        $data = [1, $id_category];
        return $this->update($sql, $data);
    }

    public function combo_categories()
    {
        $sql = "SELECT id_category, name_category FROM categories WHERE status_active_category = 1 AND status_deleted_category = 0";
        return $this->select_all($sql);
    }
}
