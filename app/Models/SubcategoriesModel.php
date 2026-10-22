<?php
    class SubcategoriesModel extends Mysql {
        public $intIdcategoria;
        
        public function show_subcategories()
        {
            $sql = "SELECT s.id_subcategory, s.name_subcategory, c.name_category
                    FROM subcategories s
                    INNER JOIN categories c ON s.id_category_subcategory = c.id_category
                    WHERE s.status_active_subcategory = 1
                    AND s.status_deleted_subcategory = 0";
            return $this->select_all($sql);
        }

        public function show_subcategory(int $id_subcategory)
        {
            $sql = "SELECT s.id_category_subcategory, s.name_subcategory, c.name_category
                    FROM subcategories s
                    INNER JOIN categories c ON s.id_category_subcategory = c.id_category
                    WHERE s.id_subcategory = ?
                    AND status_active_subcategory = 1
                    AND status_deleted_subcategory = 0";
            return $this->select($sql, [$id_subcategory]);
        }

        public function insert_subcategory(int $id_category, string $name_subcategory)
        {
            $query_insert = "INSERT INTO subcategories(id_category_subcategory, name_subcategory) VALUES(?,?)";
            $data = [$id_category, $name_subcategory];
            return $this->insert($query_insert, $data);
        }

        public function update_subcategory(int $id_subcategory, int $id_category, string $name_subcategory)
        {
            $sql = "UPDATE subcategories SET id_category_subcategory = ?, name_subcategory = ? WHERE id_subcategory = ?";
            $data = [$id_category, $name_subcategory, $id_subcategory];
            return $this->update($sql, $data);
        }

        public function delete_subcategory(int $id_subcategory)
        {
            $sql = "UPDATE subcategories SET status_deleted_subcategory = ? WHERE id_subcategory = ?";
            $data = [1, $id_subcategory];
            return $this->update($sql, $data);
        }

        public function combo_subcategories($idcategoria)
        {
            $sql = "SELECT * FROM subcategories WHERE id_category_subcategory = ? AND status_active_subcategory != 0";
            return $this->select_all($sql, [$idcategoria]);
        }
}
