<?php
class VersionsIpModel extends Mysql
{
    public function show_versions_ip()
    {
        $sql = "SELECT id_version_ip, name_version_ip, status_active_version_ip FROM versions_ip WHERE status_deleted_version_ip = 0";
        return $this->select_all($sql);
    }

    public function show_version_ip(int $id_version_ip)
    {
        $sql = "SELECT name_version_ip, status_active_version_ip FROM versions_ip WHERE id_version_ip = ? AND status_deleted_version_ip = 0";
        return $this->select($sql, [$id_version_ip]);
    }

    public function insert_version_ip(string $name_version_ip, int $status_active_version_ip)
    {
        $query_insert = "INSERT INTO versions_ip(name_version_ip, status_active_version_ip) VALUES(?,?)";
        $data = [$name_version_ip, $status_active_version_ip];
        return $this->insert($query_insert, $data);
    }

    public function update_version_ip(int $id_version_ip, string $name_version_ip, int $status_active_version_ip)
    {
        $sql = "UPDATE versions_ip SET name_version_ip = ?, status_active_version_ip = ? WHERE id_version_ip = ?";
        $data = [$name_version_ip, $status_active_version_ip, $id_version_ip];
        return $this->update($sql, $data);
    }

    public function delete_version_ip(int $id_version_ip)
    {
        $sql = "UPDATE versions_ip SET status_deleted_version_ip = ? WHERE id_version_ip = ?";
        $data = [1, $id_version_ip];
        return $this->update($sql, $data);
    }

    public function combo_versions_ip()
    {
        $sql = "SELECT id_version_ip, name_version_ip FROM versions_ip WHERE status_active_version_ip = 1 AND status_deleted_version_ip = 0";
        return $this->select_all($sql);
    }
}
