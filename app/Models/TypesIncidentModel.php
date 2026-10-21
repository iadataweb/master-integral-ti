<?php
class TypesIncidentModel extends Mysql
{
    public function show_types_incidents()
    {
        $sql = "SELECT id_type_incident, name_type_incident, status_active_type_incident
                FROM types_incident
                WHERE status_deleted_type_incident = 0";
        return $this->select_all($sql);
    }

    public function show_type_incident(int $id_type_incident)
    {
        $sql = "SELECT name_type_incident, status_active_type_incident
                FROM types_incident
                WHERE id_type_incident = ?
                AND status_deleted_type_incident = 0";
        return $this->select($sql, [$id_type_incident]);
    }

    public function insert_type_incident(string $name_type_incident, int $status_active_type_incident)
    {
        $query_insert = "INSERT INTO types_incident(name_type_incident, status_active_type_incident) VALUES(?,?)";
        $data = [$name_type_incident, $status_active_type_incident];
        return $this->insert($query_insert, $data);
    }

    public function update_type_incident(int $id_type_incident, string $name_type_incident, int $status_active_type_incident)
    {
        $sql = "UPDATE types_incident SET name_type_incident = ?, status_active_type_incident = ? WHERE id_type_incident = ?";
        $data = [$name_type_incident, $status_active_type_incident, $id_type_incident];
        return $this->update($sql, $data);
    }

    public function delete_type_incident(int $id_type_incident)
    {
        $sql = "UPDATE types_incident SET status_deleted_type_incident = ? WHERE id_type_incident = ?";
        $data = [1, $id_type_incident];
        return $this->update($sql, $data);
    }

    public function combo_types_incidents()
    {
        $sql = "SELECT id_type_incident, name_type_incident
                FROM types_incident
                WHERE status_active_type_incident = 1
                AND status_deleted_type_incident = 0";
        return $this->select_all($sql);
    }
}
