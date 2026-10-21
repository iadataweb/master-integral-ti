<?php
class HistoryModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function assets(): array
    {
        $sql = "SELECT a.id_asset,
                       a.code_inventory_asset,
                       a.model_asset,
                       a.serial_number_asset,
                       a.status_active_asset,
                       a.date_created_asset,
                       ta.name_type_asset,
                       ca.name_category_asset,
                       b.name_brand,
                       co.name_condition_asset,
                       env.name_environment,
                       env.floor_environment,
                       (SELECT COUNT(*) FROM movements_asset ma WHERE ma.id_asset_movement_asset = a.id_asset AND ma.status_deleted_movement_asset = 0) AS total_movements,
                       (SELECT COUNT(*) FROM incidents i WHERE i.id_asset_incident = a.id_asset AND i.status_deleted_incident = 0) AS total_incidents,
                       (SELECT COUNT(*)
                        FROM history_maintenances_preventive hmp
                        INNER JOIN maintenances_preventive mp ON hmp.id_maintenance_preventive = mp.id_maintenance_preventive
                        WHERE mp.id_asset_maintenance_preventive = a.id_asset
                          AND mp.status_deleted_maintenance_preventive = 0) AS total_preventive,
                       (SELECT COUNT(*) FROM maintenances_corrective mc WHERE mc.id_asset_maintenance_corrective = a.id_asset AND mc.status_deleted_maintenance_corrective = 0) AS total_corrective
                FROM assets a
                INNER JOIN types_asset ta ON a.id_type_asset_asset = ta.id_type_asset
                INNER JOIN categories_asset ca ON a.id_category_asset_asset = ca.id_category_asset
                INNER JOIN brands b ON a.id_brand_asset = b.id_brand
                INNER JOIN conditions_asset co ON a.id_condition_asset_asset = co.id_condition_asset
                LEFT JOIN movements_asset ma ON ma.id_asset_movement_asset = a.id_asset
                    AND ma.status_record_current_movement_asset = 1
                    AND ma.status_deleted_movement_asset = 0
                LEFT JOIN environments env ON ma.id_space_movement_asset = env.id_environment
                WHERE a.status_deleted_asset = 0
                ORDER BY a.id_asset DESC";
        return $this->select_all($sql);
    }

    public function asset(int $id_asset)
    {
        $sql = "SELECT a.*,
                       ta.name_type_asset,
                       ca.name_category_asset,
                       b.name_brand,
                       co.name_condition_asset
                FROM assets a
                INNER JOIN types_asset ta ON a.id_type_asset_asset = ta.id_type_asset
                INNER JOIN categories_asset ca ON a.id_category_asset_asset = ca.id_category_asset
                INNER JOIN brands b ON a.id_brand_asset = b.id_brand
                INNER JOIN conditions_asset co ON a.id_condition_asset_asset = co.id_condition_asset
                WHERE a.id_asset = ?
                  AND a.status_deleted_asset = 0";
        return $this->select($sql, [$id_asset]);
    }

    public function timeline(int $id_asset): array
    {
        $asset = $this->asset($id_asset);
        if (!$asset) {
            return [];
        }

        $events = [];
        $events[] = $this->event(
            'inventory',
            'Registro de inventario',
            $asset['date_created_asset'],
            $asset['code_inventory_asset'],
            'Alta del activo en inventario.',
            [
                'Codigo de inventario' => $asset['code_inventory_asset'],
                'Tipo de activo' => $asset['name_type_asset'],
                'Categoria' => $asset['name_category_asset'],
                'Marca' => $asset['name_brand'],
                'Modelo' => $asset['model_asset'],
                'Numero de serie' => $asset['serial_number_asset'],
                'Condicion' => $asset['name_condition_asset'],
                'Descripcion' => $asset['descriptions_asset'],
                'Observaciones' => $asset['observations_asset'],
            ],
            $this->asset_attachments($id_asset)
        );

        foreach ($this->movement_events($id_asset) as $row) {
            $events[] = $this->event(
                'movement',
                'Movimiento de activo',
                $row['date_event_movement_asset'] ?: $row['date_created_movement_asset'],
                $row['name_type_movement'],
                'Movimiento registrado hacia ' . trim('Piso ' . $row['floor_environment'] . ' - ' . $row['name_environment']),
                [
                    'Tipo de movimiento' => $row['name_type_movement'],
                    'Responsable' => trim($row['responsible_employee'] ?? '') ?: 'No asignado',
                    'Estado del movimiento' => $row['status_process_movement_asset'],
                    'Documento' => $row['document_code_movement_asset'],
                    'Tipo de prestamo' => $this->loan_recipient_type_label($row['loan_recipient_type_movement_asset'] ?? ''),
                    'Persona o entidad que recibe' => $row['loan_recipient_name_movement_asset'],
                    'Documento o referencia' => $row['loan_recipient_document_movement_asset'],
                    'Devolucion' => ((int)($row['loan_return_indefinite_movement_asset'] ?? 0) === 1 ? 'Indeterminada' : 'Con fecha definida'),
                    'Campus' => $row['name_campus'],
                    'Ambiente' => trim('Piso ' . $row['floor_environment'] . ' - ' . $row['name_environment']),
                    'Fecha de evento' => $row['date_event_movement_asset'],
                    'Retorno esperado' => $row['expected_return_date_movement_asset'],
                    'Observaciones' => $row['observations_movement_asset'],
                ],
                $this->movement_attachments((int)$row['id_movement_asset'])
            );
        }

        foreach ($this->select_all("SELECT mc.*,u.name_user FROM movement_changes mc INNER JOIN movements_asset ma ON ma.id_movement_asset=mc.id_movement_asset LEFT JOIN users u ON u.id_user=mc.id_user WHERE ma.id_asset_movement_asset=? ORDER BY mc.id_movement_change", [$id_asset]) as $change) {
            $before=json_decode($change['previous_data'],true) ?: [];
            $after=json_decode($change['new_data'],true) ?: [];
            $events[]=$this->event('movement',$change['action_change']==='annulled'?'Movimiento anulado':'Movimiento corregido',$change['date_created_change'],'Movimiento #'.$change['id_movement_asset'],$change['reason_change'],[
                'Registrado por'=>$change['name_user'] ?: 'Usuario #'.$change['id_user'],
                'Motivo'=>$change['reason_change'],
                'Destino anterior (ID)'=>$before['id_space_movement_asset'] ?? 'No definido',
                'Destino corregido (ID)'=>$after['id_space_movement_asset'] ?? 'No aplica',
                'Movimiento restaurado'=>$after['restored_movement'] ?? 'No aplica'
            ],[]);
        }

        foreach ($this->incident_events($id_asset) as $row) {
            $events[] = $this->event(
                'incident',
                'Incidencia reportada',
                ($row['date_incident_incident'] ?? null) ?: $row['date_created_incident'],
                '#' . $row['id_incident'] . ' - ' . $row['title_incident'],
                $row['description_incident'],
                [
                    'Tipo de incidencia' => $row['name_type_incident'],
                    'Prioridad' => $row['name_priority'],
                    'Estado' => ((int)$row['status_open_incident'] === 1 ? 'Abierta' : 'Cerrada'),
                    'Responsable soporte' => $row['support_display_name'],
                    'Diagnostico' => $row['diagnostic_incident'],
                    'Solucion' => $row['solution_incident'],
                    'Fecha cierre' => $row['datetime_closed_record'],
                ],
                $this->incident_attachments((int)$row['id_incident']),
                base_url() . '/help_desk/incidents/incident_details/' . (int)$row['id_incident']
            );
        }

        foreach ($this->preventive_history_events($id_asset) as $row) {
            $events[] = $this->event(
                'preventive_done',
                'Mantenimiento preventivo realizado',
                $row['date_performed_history_maintenance_preventive'] ?: $row['date_created_history_maintenance_preventive'],
                $row['name_status_maintenance'],
                $row['action_history_maintenance_preventive'],
                [
                    'Estado' => $row['name_status_maintenance'],
                    'Frecuencia' => $row['name_frequency_maintenance'],
                    'Responsable' => $row['name_type_responsible_maintenance'],
                    'Fecha programada' => $row['date_scheduled_history_maintenance_preventive'],
                    'Fecha realizada' => $row['date_performed_history_maintenance_preventive'],
                    'Observacion' => $row['observation_history_maintenance_preventive'],
                ],
                $this->preventive_history_attachments((int)$row['id_history_maintenance_preventive'])
            );
        }

        foreach ($this->corrective_events($id_asset) as $row) {
            $events[] = $this->event(
                'corrective',
                'Mantenimiento correctivo',
                $row['date_repair_maintenance_corrective'] ?: $row['date_created_maintenance_corrective'],
                $row['name_maintenance_corrective_origin'],
                $row['action_maintenance_corrective'],
                [
                    'Origen' => $row['name_maintenance_corrective_origin'],
                    'Responsable' => $row['name_type_responsible_maintenance'],
                    'Fecha reparacion' => $row['date_repair_maintenance_corrective'],
                    'Hora reparacion' => $row['time_repair_maintenance_corrective'],
                    'Estado' => ((int)$row['status_active_maintenance_corrective'] === 1 ? 'Pendiente' : 'Finalizado'),
                    'Incidencia vinculada' => $row['id_incident_maintenance_corrective'],
                    'Observacion' => $row['observation_maintenance_corrective'],
                ],
                $this->corrective_attachments((int)$row['id_maintenance_corrective'])
            );
        }

        foreach ($this->corrective_history_events($id_asset) as $row) {
            $events[] = $this->event(
                'corrective_done',
                'Ejecucion correctiva registrada',
                $row['date_performed_history_maintenance_corrective'] ?: $row['date_created_history_maintenance_corrective'],
                $row['name_status_maintenance'],
                $row['action_history_maintenance_corrective'],
                [
                    'Estado' => $row['name_status_maintenance'],
                    'Responsable' => $row['name_type_responsible_maintenance'],
                    'Fecha programada' => $row['date_scheduled_history_maintenance_corrective'],
                    'Fecha realizada' => $row['date_performed_history_maintenance_corrective'],
                    'Observacion' => $row['observation_history_maintenance_corrective'],
                ],
                []
            );
        }

        usort($events, function ($a, $b) {
            return strtotime($b['date_sort']) <=> strtotime($a['date_sort']);
        });

        return $events;
    }

    private function event(string $type, string $title, $date, string $subtitle, $description, array $details, array $attachments = [], ?string $url = null): array
    {
        $dateSort = $date ?: date('Y-m-d H:i:s');
        return compact('type', 'title', 'date', 'dateSort', 'subtitle', 'description', 'details', 'attachments', 'url') + ['date_sort' => $dateSort];
    }

    private function movement_events(int $id_asset): array
    {
        return $this->select_all("SELECT ma.*, tm.name_type_movement, env.name_environment, env.floor_environment, c.name_campus,
                   CONCAT(COALESCE(e.first_name_employee, ''), ' ', COALESCE(e.middle_name_employee, ''), ' ', COALESCE(e.paternal_last_name_employee, ''), ' ', COALESCE(e.maternal_last_name_employee, '')) AS responsible_employee
            FROM movements_asset ma
            INNER JOIN types_movement tm ON ma.id_type_movement_asset = tm.id_type_movement
            INNER JOIN environments env ON ma.id_space_movement_asset = env.id_environment
            LEFT JOIN campuses c ON env.id_campus_environment = c.id_campus
            LEFT JOIN employees e ON ma.id_responsible_employee_movement_asset = e.id_employee
            WHERE ma.id_asset_movement_asset = ? AND ma.status_deleted_movement_asset = 0", [$id_asset]);
    }

    private function movement_attachments(int $id_movement_asset): array
    {
        return $this->select_all("SELECT route_attachment_movement_asset AS route, name_attachment_movement_asset AS name
            FROM attachments_movement_asset
            WHERE id_movement_asset = ? AND status_deleted_attachment_movement_asset = 0", [$id_movement_asset]);
    }

    private function loan_recipient_type_label(?string $type): string
    {
        $labels = [
            'internal' => 'Personal interno',
            'external_person' => 'Persona externa',
            'external_company' => 'Empresa o institucion externa'
        ];
        return $labels[$type] ?? 'No aplica';
    }

    private function incident_events(int $id_asset): array
    {
        return $this->select_all("SELECT i.*, TIMESTAMP(i.date_incident_date, i.time_incident_time) AS date_incident_incident,
                   COALESCE(ti.name_type_incident, 'Sin tipo') AS name_type_incident, p.name_priority,
                   CONCAT(COALESCE(pt.abbreviation_professional_title, ''), ' ', COALESCE(e.first_name_employee, ''), ' ', COALESCE(e.paternal_last_name_employee, '')) AS support_display_name
            FROM incidents i
            LEFT JOIN types_incident ti ON i.id_type_incident_incident = ti.id_type_incident
            INNER JOIN priorities p ON i.id_priority_incident = p.id_priority
            LEFT JOIN users u ON i.id_support_assigned_incident = u.id_user
            LEFT JOIN employees e ON u.id_employee_user = e.id_employee
            LEFT JOIN professional_titles pt ON e.id_professional_title_employee = pt.id_professional_title
            WHERE i.id_asset_incident = ? AND i.status_deleted_incident = 0", [$id_asset]);
    }

    private function preventive_history_events(int $id_asset): array
    {
        return $this->select_all("SELECT hmp.*, fm.name_frequency_maintenance, sm.name_status_maintenance, trm.name_type_responsible_maintenance
            FROM history_maintenances_preventive hmp
            INNER JOIN maintenances_preventive mp ON hmp.id_maintenance_preventive = mp.id_maintenance_preventive
            INNER JOIN frequencies_maintenance fm ON hmp.id_frequency_maintenance = fm.id_frequency_maintenance
            INNER JOIN states_maintenance sm ON hmp.id_status_maintenance = sm.id_status_maintenance
            INNER JOIN types_responsible_maintenance trm ON hmp.id_type_responsible_maintenance = trm.id_type_responsible_maintenance
            WHERE mp.id_asset_maintenance_preventive = ? AND mp.status_deleted_maintenance_preventive = 0", [$id_asset]);
    }

    private function corrective_events(int $id_asset): array
    {
        return $this->select_all("SELECT mc.*, COALESCE(mco.name_maintenance_corrective_origin, 'Sin origen') AS name_maintenance_corrective_origin, trm.name_type_responsible_maintenance
            FROM maintenances_corrective mc
            INNER JOIN types_responsible_maintenance trm ON mc.id_type_responsible_maintenance_maintenance_corrective = trm.id_type_responsible_maintenance
            LEFT JOIN maintenance_corrective_origins mco ON mc.id_origin_maintenance_corrective = mco.id_maintenance_corrective_origin
            WHERE mc.id_asset_maintenance_corrective = ? AND mc.status_deleted_maintenance_corrective = 0", [$id_asset]);
    }

    private function corrective_history_events(int $id_asset): array
    {
        return $this->select_all("SELECT hmc.*, sm.name_status_maintenance, trm.name_type_responsible_maintenance
            FROM history_maintenances_corrective hmc
            INNER JOIN maintenances_corrective mc ON hmc.id_maintenance_corrective = mc.id_maintenance_corrective
            INNER JOIN states_maintenance sm ON hmc.id_status_maintenance = sm.id_status_maintenance
            INNER JOIN types_responsible_maintenance trm ON hmc.id_type_responsible_maintenance = trm.id_type_responsible_maintenance
            WHERE mc.id_asset_maintenance_corrective = ? AND mc.status_deleted_maintenance_corrective = 0", [$id_asset]);
    }

    private function asset_attachments(int $id_asset): array
    {
        return $this->select_all("SELECT route_attachment_asset AS route, name_attachment_asset AS name FROM attachments_asset WHERE id_asset = ? AND status_deleted_attachment_asset = 0", [$id_asset]);
    }

    private function incident_attachments(int $id_incident): array
    {
        return $this->select_all("SELECT route_attachment_incident AS route, name_attachment_incident AS name FROM attachments_incident WHERE id_incident_attachment_incident = ?", [$id_incident]);
    }

    private function preventive_history_attachments(int $id_history): array
    {
        return $this->select_all("SELECT type_attachment_history_maintenance_preventive AS type, route_attachment_history_maintenance_preventive AS route, name_attachment_history_maintenance_preventive AS name FROM attachments_history_maintenance_preventive WHERE id_history_maintenance_preventive = ? AND status_deleted_attachment_history_maintenance_preventive = 0", [$id_history]);
    }

    private function corrective_attachments(int $id_maintenance_corrective): array
    {
        return $this->select_all("SELECT route_attachment_maintenance_corrective AS route, name_attachment_maintenance_corrective AS name FROM attachments_maintenance_corrective WHERE id_maintenance_corrective = ? AND status_deleted_attachment_maintenance_corrective = 0", [$id_maintenance_corrective]);
    }
}
