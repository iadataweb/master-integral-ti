<?php
class ProfessionalTitlesModel extends Mysql
{
    public function show_professional_titles()
    {
        $sql = "SELECT id_professional_title, name_professional_title, abbreviation_professional_title, status_active_professional_title
                FROM professional_titles
                WHERE status_deleted_professional_title = 0";
        return $this->select_all($sql);
    }

    public function show_professional_title(int $id_professional_title)
    {
        $sql = "SELECT name_professional_title, abbreviation_professional_title, status_active_professional_title
                FROM professional_titles
                WHERE id_professional_title = ?
                AND status_deleted_professional_title = 0";
        return $this->select($sql, [$id_professional_title]);
    }

    public function insert_professional_title(string $name_professional_title, string $abbreviation_professional_title, int $status_active_professional_title)
    {
        $query_insert = "INSERT INTO professional_titles(name_professional_title, abbreviation_professional_title, status_active_professional_title) VALUES(?,?,?)";
        $data = [$name_professional_title, $abbreviation_professional_title, $status_active_professional_title];
        return $this->insert($query_insert, $data);
    }

    public function update_professional_title(int $id_professional_title, string $name_professional_title, string $abbreviation_professional_title, int $status_active_professional_title)
    {
        $sql = "UPDATE professional_titles
                SET name_professional_title = ?, abbreviation_professional_title = ?, status_active_professional_title = ?
                WHERE id_professional_title = ?";
        $data = [$name_professional_title, $abbreviation_professional_title, $status_active_professional_title, $id_professional_title];
        return $this->update($sql, $data);
    }

    public function delete_professional_title(int $id_professional_title)
    {
        $sql = "UPDATE professional_titles SET status_deleted_professional_title = ? WHERE id_professional_title = ?";
        $data = [1, $id_professional_title];
        return $this->update($sql, $data);
    }

    public function combo_professional_titles()
    {
        $sql = "SELECT id_professional_title, name_professional_title
                FROM professional_titles
                WHERE status_active_professional_title = 1
                AND status_deleted_professional_title = 0";
        return $this->select_all($sql);
    }
}
