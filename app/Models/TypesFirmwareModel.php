<?php
class TypesFirmwareModel extends Mysql
{
    public function show_types_firmware()
    {
        $sql = "SELECT id_type_firmware, name_type_firmware, status_active_type_firmware
                FROM types_firmware
                WHERE status_deleted_type_firmware = 0";
        return $this->select_all($sql);
    }

    public function show_type_firmware(int $id_type_firmware)
    {
        $sql = "SELECT name_type_firmware, status_active_type_firmware
                FROM types_firmware
                WHERE id_type_firmware = ?
                AND status_deleted_type_firmware = 0";
        return $this->select($sql, [$id_type_firmware]);
    }

    public function insert_type_firmware(string $name_type_firmware, int $status_active_type_firmware)
    {
        $query_insert = "INSERT INTO types_firmware(name_type_firmware, status_active_type_firmware) VALUES(?,?)";
        $data = [$name_type_firmware, $status_active_type_firmware];
        return $this->insert($query_insert, $data);
    }

    public function update_type_firmware(int $id_type_firmware, string $name_type_firmware, int $status_active_type_firmware)
    {
        $sql = "UPDATE types_firmware SET name_type_firmware = ?, status_active_type_firmware = ? WHERE id_type_firmware = ?";
        $data = [$name_type_firmware, $status_active_type_firmware, $id_type_firmware];
        return $this->update($sql, $data);
    }

    public function delete_type_firmware(int $id_type_firmware)
    {
        $sql = "UPDATE types_firmware SET status_deleted_type_firmware = ? WHERE id_type_firmware = ?";
        $data = [1, $id_type_firmware];
        return $this->update($sql, $data);
    }

    public function combo_types_firmware()
    {
        $sql = "SELECT id_type_firmware, name_type_firmware
                FROM types_firmware
                WHERE status_active_type_firmware = 1
                AND status_deleted_type_firmware = 0";
        return $this->select_all($sql);
    }
}
