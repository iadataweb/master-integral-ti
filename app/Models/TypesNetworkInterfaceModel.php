<?php
class TypesNetworkInterfaceModel extends Mysql
{
    public function show_types_network_interface()
    {
        $sql = "SELECT id_type_network_interface, name_type_network_interface, status_active_type_network_interface
                FROM types_network_interface
                WHERE status_deleted_type_network_interface = 0";
        return $this->select_all($sql);
    }

    public function show_type_network_interface(int $id_type_network_interface)
    {
        $sql = "SELECT name_type_network_interface, status_active_type_network_interface
                FROM types_network_interface
                WHERE id_type_network_interface = ?
                AND status_deleted_type_network_interface = 0";
        return $this->select($sql, [$id_type_network_interface]);
    }

    public function insert_type_network_interface(string $name_type_network_interface, int $status_active_type_network_interface)
    {
        $query_insert = "INSERT INTO types_network_interface(name_type_network_interface, status_active_type_network_interface) VALUES(?,?)";
        $data = [$name_type_network_interface, $status_active_type_network_interface];
        return $this->insert($query_insert, $data);
    }

    public function update_type_network_interface(int $id_type_network_interface, string $name_type_network_interface, int $status_active_type_network_interface)
    {
        $sql = "UPDATE types_network_interface
                SET name_type_network_interface = ?, status_active_type_network_interface = ?
                WHERE id_type_network_interface = ?";
        $data = [$name_type_network_interface, $status_active_type_network_interface, $id_type_network_interface];
        return $this->update($sql, $data);
    }

    public function delete_type_network_interface(int $id_type_network_interface)
    {
        $sql = "UPDATE types_network_interface SET status_deleted_type_network_interface = ? WHERE id_type_network_interface = ?";
        $data = [1, $id_type_network_interface];
        return $this->update($sql, $data);
    }

    public function combo_types_network_interface()
    {
        $sql = "SELECT id_type_network_interface, name_type_network_interface
                FROM types_network_interface
                WHERE status_active_type_network_interface = 1
                AND status_deleted_type_network_interface = 0";
        return $this->select_all($sql);
    }
}
