<?php
class FrequenciesMaintenanceModel extends Mysql
{
    public function obtain_interval_frequency(int $id_frequency_maintenance)
    {
        $sql = "SELECT interval_frequency_maintenance
                FROM frequencies_maintenance
                WHERE id_frequency_maintenance = ?
                AND status_deleted_frequency_maintenance = 0";
        return $this->select($sql, [$id_frequency_maintenance]);
    }

    public function combo_frequencies_maintenance()
    {
        $sql = "SELECT id_frequency_maintenance, name_frequency_maintenance
                FROM frequencies_maintenance
                WHERE status_active_frequency_maintenance = 1
                AND status_deleted_frequency_maintenance = 0";
        return $this->select_all($sql);
    }
}
