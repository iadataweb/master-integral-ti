<?php
class MaintenancesCorrectiveModel extends Mysql
{
    private static $schemaInitialized = false;

    public function __construct()
    {
        parent::__construct();
        $this->ensure_schema();
    }

    private function ensure_schema(): void
    {
        if (self::$schemaInitialized) {
            return;
        }

        $statements = [
            "CREATE TABLE IF NOT EXISTS maintenance_corrective_origins (
              id_maintenance_corrective_origin INT NOT NULL AUTO_INCREMENT,
              name_maintenance_corrective_origin VARCHAR(120) NOT NULL,
              description_maintenance_corrective_origin TEXT NULL,
              status_active_maintenance_corrective_origin TINYINT NOT NULL DEFAULT 1,
              status_deleted_maintenance_corrective_origin TINYINT NOT NULL DEFAULT 0,
              date_created_maintenance_corrective_origin TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              date_updated_maintenance_corrective_origin TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (id_maintenance_corrective_origin)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS maintenances_corrective (
              id_maintenance_corrective INT NOT NULL AUTO_INCREMENT,
              id_asset_maintenance_corrective INT NOT NULL,
              id_incident_maintenance_corrective INT DEFAULT NULL,
              id_origin_maintenance_corrective INT DEFAULT NULL,
              id_frequency_maintenance_maintenance_corrective INT NOT NULL,
              id_type_responsible_maintenance_maintenance_corrective INT NOT NULL,
              date_scheduled_maintenance_corrective DATE DEFAULT NULL,
              date_next_maintenance_corrective DATE DEFAULT NULL,
              date_repair_maintenance_corrective DATE DEFAULT NULL,
              time_repair_maintenance_corrective TIME DEFAULT NULL,
              action_maintenance_corrective TEXT NULL,
              description_maintenance_corrective TEXT NOT NULL,
              observation_maintenance_corrective TEXT NOT NULL,
              status_active_maintenance_corrective TINYINT NOT NULL DEFAULT 1,
              status_deleted_maintenance_corrective TINYINT NOT NULL DEFAULT 0,
              date_created_maintenance_corrective TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              date_updated_maintenance_corrective TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (id_maintenance_corrective)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS spare_parts (
              id_spare_part INT NOT NULL AUTO_INCREMENT,
              code_spare_part VARCHAR(60) NOT NULL,
              name_spare_part VARCHAR(150) NOT NULL,
              id_brand_spare_part INT DEFAULT NULL,
              model_spare_part VARCHAR(100) DEFAULT NULL,
              unit_spare_part VARCHAR(30) NOT NULL DEFAULT 'unidad',
              stock_current_spare_part DECIMAL(10,2) NOT NULL DEFAULT 0,
              stock_minimum_spare_part DECIMAL(10,2) NOT NULL DEFAULT 0,
              description_spare_part TEXT NULL,
              status_active_spare_part TINYINT NOT NULL DEFAULT 1,
              status_deleted_spare_part TINYINT NOT NULL DEFAULT 0,
              date_created_spare_part TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              date_updated_spare_part TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (id_spare_part),
              UNIQUE KEY uk_spare_parts_code (code_spare_part),
              KEY idx_spare_parts_brand (id_brand_spare_part)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS spare_part_movements (
              id_spare_part_movement INT NOT NULL AUTO_INCREMENT,
              id_spare_part INT NOT NULL,
              id_maintenance_corrective INT DEFAULT NULL,
              type_spare_part_movement ENUM('IN','OUT','ADJUSTMENT') NOT NULL,
              quantity_spare_part_movement DECIMAL(10,2) NOT NULL DEFAULT 0,
              reason_spare_part_movement TEXT NULL,
              stock_after_spare_part_movement DECIMAL(10,2) NOT NULL DEFAULT 0,
              date_created_spare_part_movement TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (id_spare_part_movement),
              KEY idx_spm_spare_part (id_spare_part),
              KEY idx_spm_maintenance (id_maintenance_corrective)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS maintenance_corrective_spare_parts (
              id_maintenance_corrective_spare_part INT NOT NULL AUTO_INCREMENT,
              id_maintenance_corrective INT NOT NULL,
              id_spare_part INT NOT NULL,
              quantity_maintenance_corrective_spare_part DECIMAL(10,2) NOT NULL DEFAULT 1,
              observation_maintenance_corrective_spare_part TEXT NULL,
              status_deleted_maintenance_corrective_spare_part TINYINT NOT NULL DEFAULT 0,
              date_created_maintenance_corrective_spare_part TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              date_updated_maintenance_corrective_spare_part TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (id_maintenance_corrective_spare_part),
              KEY idx_mcsp_maintenance (id_maintenance_corrective),
              KEY idx_mcsp_spare_part (id_spare_part)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS history_maintenances_corrective (
              id_history_maintenance_corrective INT NOT NULL AUTO_INCREMENT,
              id_maintenance_corrective INT NOT NULL,
              id_frequency_maintenance INT NOT NULL,
              id_status_maintenance INT NOT NULL,
              id_type_responsible_maintenance INT NOT NULL,
              date_scheduled_history_maintenance_corrective DATE NOT NULL,
              date_performed_history_maintenance_corrective DATE DEFAULT NULL,
              action_history_maintenance_corrective TEXT NOT NULL,
              observation_history_maintenance_corrective TEXT NOT NULL,
              date_created_history_maintenance_corrective TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              date_updated_history_maintenance_corrective TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (id_history_maintenance_corrective)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS maintenances_corrective_company (
              id_maintenance_corrective_company INT NOT NULL AUTO_INCREMENT,
              id_maintenance_corrective INT NOT NULL,
              id_company INT NOT NULL,
              status_active_maintenance_corrective_company TINYINT NOT NULL DEFAULT 1,
              status_deleted_maintenance_corrective_company TINYINT NOT NULL DEFAULT 0,
              date_created_maintenance_corrective_company TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              date_updated_maintenance_corrective_company TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (id_maintenance_corrective_company)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS history_maintenances_corrective_company (
              id_history_maintenance_corrective_company INT NOT NULL AUTO_INCREMENT,
              id_history_maintenance_corrective INT NOT NULL,
              id_company INT NOT NULL,
              status_active_history_maintenance_corrective_company TINYINT NOT NULL DEFAULT 1,
              status_deleted_history_maintenance_corrective_company TINYINT NOT NULL DEFAULT 0,
              date_created_history_maintenance_corrective_company TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              date_updated_history_maintenance_corrective_company TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (id_history_maintenance_corrective_company)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS attachments_maintenance_corrective (
              id_attachment_maintenance_corrective INT NOT NULL AUTO_INCREMENT,
              id_maintenance_corrective INT NOT NULL,
              route_attachment_maintenance_corrective VARCHAR(255) NOT NULL,
              name_attachment_maintenance_corrective VARCHAR(255) NOT NULL,
              status_active_attachment_maintenance_corrective TINYINT NOT NULL DEFAULT 1,
              status_deleted_attachment_maintenance_corrective TINYINT NOT NULL DEFAULT 0,
              date_created_attachment_maintenance_corrective TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              date_updated_attachment_maintenance_corrective TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (id_attachment_maintenance_corrective)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        ];

        try {
            foreach ($statements as $sql) {
                $this->update($sql);
            }
            $this->add_column_if_missing('maintenances_corrective', 'id_origin_maintenance_corrective', 'INT DEFAULT NULL AFTER id_incident_maintenance_corrective');
            $this->add_column_if_missing('maintenances_corrective', 'date_repair_maintenance_corrective', 'DATE DEFAULT NULL AFTER date_next_maintenance_corrective');
            $this->add_column_if_missing('maintenances_corrective', 'time_repair_maintenance_corrective', 'TIME DEFAULT NULL AFTER date_repair_maintenance_corrective');
            $this->add_column_if_missing('maintenances_corrective', 'action_maintenance_corrective', 'TEXT NULL AFTER time_repair_maintenance_corrective');
            $this->add_column_if_missing('incidents', 'corrective_derivation_status_incident', "VARCHAR(20) NOT NULL DEFAULT 'none' AFTER id_asset_incident");
            $this->add_column_if_missing('incidents', 'id_maintenance_corrective_incident', 'INT DEFAULT NULL AFTER corrective_derivation_status_incident');
            $this->add_column_if_missing('incidents', 'date_derived_corrective_incident', 'TIMESTAMP NULL DEFAULT NULL AFTER id_maintenance_corrective_incident');
            $this->add_column_if_missing('incidents', 'date_updated_corrective_derivation_incident', 'TIMESTAMP NULL DEFAULT NULL AFTER date_derived_corrective_incident');
            $this->update("CREATE TABLE IF NOT EXISTS incident_corrective_derivation_history (
              id_incident_corrective_derivation_history INT NOT NULL AUTO_INCREMENT,
              id_incident INT NOT NULL,
              id_maintenance_corrective INT DEFAULT NULL,
              action_incident_corrective_derivation_history VARCHAR(30) NOT NULL,
              observation_incident_corrective_derivation_history TEXT NULL,
              id_user_incident_corrective_derivation_history INT DEFAULT NULL,
              date_created_incident_corrective_derivation_history TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (id_incident_corrective_derivation_history),
              KEY idx_icdh_incident (id_incident),
              KEY idx_icdh_maintenance (id_maintenance_corrective)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $this->seed_origins();
        } catch (Throwable $e) {
            // Keep endpoints alive even if an older database needs manual review.
        }

        self::$schemaInitialized = true;
    }

    private function column_exists(string $table, string $column): bool
    {
        $row = $this->select("SHOW COLUMNS FROM {$table} LIKE ?", [$column]);
        return !empty($row);
    }

    private function add_column_if_missing(string $table, string $column, string $definition): void
    {
        if (!$this->column_exists($table, $column)) {
            $this->update("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}");
        }
    }

    private function seed_origins(): void
    {
        $legacy = [
            ['Por incidencia reportada', 'Incidencia derivada', 'Mantenimiento correctivo generado desde una incidencia derivada.', 0],
            ['Por solicitud o requerimiento interno', 'Solicitud interna', 'Mantenimiento correctivo solicitado por un area o usuario interno.', 1],
            ['Por deteccion durante revision o diagnostico tecnico', 'Hallazgo durante inspeccion tecnica', 'Mantenimiento correctivo generado por hallazgo durante una inspeccion tecnica.', 1],
            ['Hallazgo durante inspeccion tecnica.', 'Hallazgo durante inspeccion tecnica', 'Mantenimiento correctivo generado por hallazgo durante una inspeccion tecnica.', 1],
            ['Revision preventiva.', 'Revision preventiva', 'Mantenimiento correctivo generado durante una revision preventiva.', 1],
            ['Solicitud interna.', 'Solicitud interna', 'Mantenimiento correctivo solicitado por un area o usuario interno.', 1],
            ['Otras necesidades operativas.', 'Otras necesidades operativas', 'Mantenimiento correctivo generado por una necesidad operativa no clasificada.', 1]
        ];

        foreach ($legacy as $origin) {
            $exists_new = $this->select(
                "SELECT id_maintenance_corrective_origin FROM maintenance_corrective_origins WHERE name_maintenance_corrective_origin = ? LIMIT 1",
                [$origin[1]]
            );
            $exists_old = $this->select(
                "SELECT id_maintenance_corrective_origin FROM maintenance_corrective_origins WHERE name_maintenance_corrective_origin = ? LIMIT 1",
                [$origin[0]]
            );
            if ($exists_old && !$exists_new) {
                $this->update(
                    "UPDATE maintenance_corrective_origins
                     SET name_maintenance_corrective_origin = ?,
                         description_maintenance_corrective_origin = ?,
                         status_active_maintenance_corrective_origin = ?,
                         status_deleted_maintenance_corrective_origin = 0
                     WHERE id_maintenance_corrective_origin = ?",
                    [$origin[1], $origin[2], $origin[3], intval($exists_old['id_maintenance_corrective_origin'])]
                );
            } elseif ($exists_old && $exists_new) {
                $this->update(
                    "UPDATE maintenance_corrective_origins
                     SET status_active_maintenance_corrective_origin = 0,
                         status_deleted_maintenance_corrective_origin = 1
                     WHERE id_maintenance_corrective_origin = ?",
                    [intval($exists_old['id_maintenance_corrective_origin'])]
                );
            }
        }

        $origins = [
            ['Hallazgo durante inspeccion tecnica', 'Mantenimiento correctivo generado por hallazgo durante una inspeccion tecnica.'],
            ['Revision preventiva', 'Mantenimiento correctivo generado durante una revision preventiva.'],
            ['Solicitud interna', 'Mantenimiento correctivo solicitado por un area o usuario interno.'],
            ['Otras necesidades operativas', 'Mantenimiento correctivo generado por una necesidad operativa no clasificada.']
        ];
        foreach ($origins as $origin) {
            $exists = $this->select(
                "SELECT id_maintenance_corrective_origin FROM maintenance_corrective_origins WHERE name_maintenance_corrective_origin = ? LIMIT 1",
                [$origin[0]]
            );
            if (!$exists) {
                $this->insert(
                    "INSERT INTO maintenance_corrective_origins(name_maintenance_corrective_origin, description_maintenance_corrective_origin) VALUES(?,?)",
                    $origin
                );
            } else {
                $this->update(
                    "UPDATE maintenance_corrective_origins
                     SET description_maintenance_corrective_origin = ?,
                         status_active_maintenance_corrective_origin = 1,
                         status_deleted_maintenance_corrective_origin = 0
                     WHERE id_maintenance_corrective_origin = ?",
                    [$origin[1], intval($exists['id_maintenance_corrective_origin'])]
                );
            }
        }
    }

    private function default_frequency_id(): int
    {
        $row = $this->select("SELECT id_frequency_maintenance FROM frequencies_maintenance WHERE status_active_frequency_maintenance = 1 AND status_deleted_frequency_maintenance = 0 ORDER BY id_frequency_maintenance ASC LIMIT 1");
        return isset($row['id_frequency_maintenance']) ? intval($row['id_frequency_maintenance']) : 1;
    }

    private function default_type_responsible_id(): int
    {
        $row = $this->select("SELECT id_type_responsible_maintenance FROM types_responsible_maintenance WHERE status_active_type_responsible_maintenance = 1 AND status_deleted_type_responsible_maintenance = 0 ORDER BY id_type_responsible_maintenance ASC LIMIT 1");
        return isset($row['id_type_responsible_maintenance']) ? intval($row['id_type_responsible_maintenance']) : 1;
    }

    private function origin_incident_id(): ?int
    {
        $row = $this->select("SELECT id_maintenance_corrective_origin FROM maintenance_corrective_origins WHERE status_deleted_maintenance_corrective_origin = 0 AND LOWER(name_maintenance_corrective_origin) LIKE '%incidencia%' ORDER BY id_maintenance_corrective_origin ASC LIMIT 1");
        return isset($row['id_maintenance_corrective_origin']) ? intval($row['id_maintenance_corrective_origin']) : null;
    }

    private function log_corrective_derivation(int $id_incident, ?int $id_maintenance_corrective, string $action, ?string $observation, ?int $id_user): void
    {
        $trackingSourceId = $this->insert(
            "INSERT INTO incident_corrective_derivation_history(id_incident, id_maintenance_corrective, action_incident_corrective_derivation_history, observation_incident_corrective_derivation_history, id_user_incident_corrective_derivation_history) VALUES(?,?,?,?,?)",
            [$id_incident, $id_maintenance_corrective, $action, $observation, $id_user]
        );
            $this->insert("INSERT INTO incident_events(id_incident,event_type,id_user,solution_snapshot,source_key) VALUES(?,?,?,?,?)",
                [$id_incident,$action,$id_user,$observation,'derivation:'.$trackingSourceId]);
    }

    private function refresh_asset_maintenance_status(int $id_asset): void
    {
        if ($id_asset <= 0) {
            return;
        }
        $row = $this->select(
            "SELECT COUNT(*) AS total
             FROM maintenances_corrective
             WHERE id_asset_maintenance_corrective = ?
               AND status_active_maintenance_corrective = 1
               AND status_deleted_maintenance_corrective = 0",
            [$id_asset]
        );
        $has_active = !empty($row['total']) && intval($row['total']) > 0 ? 1 : 0;
        $this->update("UPDATE assets SET status_maintenance_asset = ? WHERE id_asset = ?", [$has_active, $id_asset]);
    }

    private function close_related_incident_if_completed(int $id_incident, int $id_maintenance_corrective): void
    {
        if ($id_incident <= 0 || $id_maintenance_corrective <= 0) {
            return;
        }
        $incident = $this->select(
            "SELECT status_open_incident FROM incidents WHERE id_incident = ? AND status_deleted_incident = 0",
            [$id_incident]
        );
        if (!$incident || intval($incident['status_open_incident']) !== 1) {
            return;
        }
        $this->update(
            "UPDATE incidents SET status_open_incident = 0, datetime_closed_record = COALESCE(datetime_closed_record, NOW()), date_updated_corrective_derivation_incident = NOW() WHERE id_incident = ?",
            [$id_incident]
        );
        $this->log_corrective_derivation($id_incident, $id_maintenance_corrective, 'closed', 'Incidencia cerrada automaticamente al culminar mantenimiento correctivo.', null);
    }

    public function show_origins(): array
    {
        return $this->select_all(
            "SELECT id_maintenance_corrective_origin, name_maintenance_corrective_origin
             FROM maintenance_corrective_origins
             WHERE status_active_maintenance_corrective_origin = 1
               AND status_deleted_maintenance_corrective_origin = 0
             ORDER BY FIELD(
                 name_maintenance_corrective_origin,
                 'Hallazgo durante inspeccion tecnica',
                 'Revision preventiva',
                 'Solicitud interna',
                 'Otras necesidades operativas'
             ), name_maintenance_corrective_origin ASC"
        );
    }

    public function show_maintenances_corrective()
    {
        $sql = "SELECT mc.id_maintenance_corrective,
                       mc.id_incident_maintenance_corrective,
                       mc.status_active_maintenance_corrective,
                       mc.date_repair_maintenance_corrective,
                       mc.time_repair_maintenance_corrective,
                       mc.action_maintenance_corrective,
                       COALESCE(mco.name_maintenance_corrective_origin, 'Sin origen') AS name_maintenance_corrective_origin,
                       a.id_asset,
                       a.code_inventory_asset,
                       ta.name_type_asset,
                       ca.name_category_asset,
                       s.name_environment AS name_space,
                       s.floor_environment AS floor_space,
                       trm.name_type_responsible_maintenance,
                       (
                           SELECT COUNT(*)
                           FROM maintenance_corrective_spare_parts mcsp
                           WHERE mcsp.id_maintenance_corrective = mc.id_maintenance_corrective
                             AND mcsp.status_deleted_maintenance_corrective_spare_part = 0
                       ) AS spare_parts_count
                FROM maintenances_corrective mc
                INNER JOIN assets a ON mc.id_asset_maintenance_corrective = a.id_asset
                INNER JOIN types_asset ta ON a.id_type_asset_asset = ta.id_type_asset
                INNER JOIN categories_asset ca ON a.id_category_asset_asset = ca.id_category_asset
                LEFT JOIN movements_asset ma ON ma.id_asset_movement_asset = a.id_asset
                    AND ma.status_record_current_movement_asset = 1
                    AND ma.status_deleted_movement_asset = 0
                LEFT JOIN environments s ON ma.id_space_movement_asset = s.id_environment
                INNER JOIN types_responsible_maintenance trm ON mc.id_type_responsible_maintenance_maintenance_corrective = trm.id_type_responsible_maintenance
                LEFT JOIN maintenance_corrective_origins mco ON mc.id_origin_maintenance_corrective = mco.id_maintenance_corrective_origin
                WHERE mc.status_deleted_maintenance_corrective = 0
                ORDER BY COALESCE(mc.date_repair_maintenance_corrective, mc.date_created_maintenance_corrective) DESC, mc.id_maintenance_corrective DESC";
        try {
            return $this->select_all($sql);
        } catch (Throwable $e) {
            return [];
        }
    }

    public function show_pending_incident_derivations(): array
    {
        $sql = "SELECT i.id_incident,
                       i.title_incident,
                       i.description_incident,
                       i.date_created_incident,
                       i.date_derived_corrective_incident,
                       ti.name_type_incident,
                       p.name_priority,
                       a.id_asset,
                       a.code_inventory_asset,
                       ta.name_type_asset,
                       ca.name_category_asset,
                       CONCAT('Piso ', e.floor_environment, ' - ', e.name_environment) AS display_location,
                       CONCAT(pt.abbreviation_professional_title, ' ',
                           CASE emp.preferred_name_employee
                               WHEN 1 THEN emp.first_name_employee
                               WHEN 2 THEN emp.middle_name_employee
                           END, ' ', emp.paternal_last_name_employee, ' ', emp.maternal_last_name_employee
                       ) AS user_display_name
                FROM incidents i
                INNER JOIN assets a ON i.id_asset_incident = a.id_asset
                INNER JOIN types_asset ta ON a.id_type_asset_asset = ta.id_type_asset
                INNER JOIN categories_asset ca ON a.id_category_asset_asset = ca.id_category_asset
                INNER JOIN types_incident ti ON i.id_type_incident_incident = ti.id_type_incident
                INNER JOIN priorities p ON i.id_priority_incident = p.id_priority
                INNER JOIN environments e ON i.id_space_incident = e.id_environment
                LEFT JOIN users u ON i.id_user_incident = u.id_user
                LEFT JOIN employees emp ON u.id_employee_user = emp.id_employee
                LEFT JOIN professional_titles pt ON emp.id_professional_title_employee = pt.id_professional_title
                WHERE i.status_deleted_incident = 0
                  AND i.status_open_incident = 1
                  AND i.corrective_derivation_status_incident = 'pending'
                ORDER BY i.date_derived_corrective_incident ASC, i.id_incident ASC";
        return $this->select_all($sql);
    }

    public function reject_incident_derivation(int $id_incident, int $id_user, string $observation = ''): bool
    {
        $incident = $this->select("SELECT corrective_derivation_status_incident FROM incidents WHERE id_incident = ? AND status_deleted_incident = 0", [$id_incident]);
        if (!$incident || $incident['corrective_derivation_status_incident'] !== 'pending') {
            return false;
        }
        $request = $this->update(
            "UPDATE incidents SET corrective_derivation_status_incident = 'rejected', date_updated_corrective_derivation_incident = NOW() WHERE id_incident = ?",
            [$id_incident]
        );
        if ($request) {
            $this->log_corrective_derivation($id_incident, null, 'rejected', $observation ?: 'Derivacion rechazada desde mantenimiento correctivo.', $id_user);
        }
        return (bool)$request;
    }

    public function accept_incident_derivation(int $id_incident, int $id_user): int
    {
        $incident = $this->select(
            "SELECT id_incident, id_asset_incident, title_incident, description_incident, corrective_derivation_status_incident
             FROM incidents
             WHERE id_incident = ? AND status_deleted_incident = 0 AND status_open_incident = 1",
            [$id_incident]
        );
        if (!$incident || $incident['corrective_derivation_status_incident'] !== 'pending' || empty($incident['id_asset_incident'])) {
            return 0;
        }

        $id_maintenance = (int)$this->insert_maintenance_corrective(
            intval($incident['id_asset_incident']),
            $id_incident,
            $this->origin_incident_id(),
            null,
            null,
            $this->default_type_responsible_id(),
            'Pendiente de atencion correctiva por incidencia #' . $id_incident . ': ' . ($incident['title_incident'] ?? ''),
            $incident['description_incident'] ?? 'Derivado desde incidencia.',
            []
        );

        if ($id_maintenance > 0) {
            $this->update(
                "UPDATE incidents SET corrective_derivation_status_incident = 'accepted', id_maintenance_corrective_incident = ?, date_updated_corrective_derivation_incident = NOW() WHERE id_incident = ?",
                [$id_maintenance, $id_incident]
            );
            $this->log_corrective_derivation($id_incident, $id_maintenance, 'accepted', 'Derivacion aceptada y correctivo generado.', $id_user);
        }
        return $id_maintenance;
    }

    public function show_history_maintenances_corrective(int $id_maintenance_corrective)
    {
        $maintenance = $this->select(
            "SELECT id_asset_maintenance_corrective FROM maintenances_corrective WHERE id_maintenance_corrective = ? AND status_deleted_maintenance_corrective = 0",
            [$id_maintenance_corrective]
        );
        if (!$maintenance) {
            return [];
        }

        $sql = "SELECT mc.id_maintenance_corrective,
                       mc.id_incident_maintenance_corrective,
                       mc.status_active_maintenance_corrective,
                       mc.date_repair_maintenance_corrective,
                       mc.time_repair_maintenance_corrective,
                       mc.action_maintenance_corrective,
                       mc.observation_maintenance_corrective,
                       mc.date_created_maintenance_corrective,
                       COALESCE(mco.name_maintenance_corrective_origin, 'Sin origen') AS name_maintenance_corrective_origin,
                       a.code_inventory_asset,
                       ta.name_type_asset,
                       ca.name_category_asset,
                       trm.name_type_responsible_maintenance,
                       (
                           SELECT COUNT(*)
                           FROM maintenance_corrective_spare_parts mcsp
                           WHERE mcsp.id_maintenance_corrective = mc.id_maintenance_corrective
                             AND mcsp.status_deleted_maintenance_corrective_spare_part = 0
                       ) AS spare_parts_count
                FROM maintenances_corrective mc
                INNER JOIN assets a ON mc.id_asset_maintenance_corrective = a.id_asset
                INNER JOIN types_asset ta ON a.id_type_asset_asset = ta.id_type_asset
                INNER JOIN categories_asset ca ON a.id_category_asset_asset = ca.id_category_asset
                INNER JOIN types_responsible_maintenance trm ON mc.id_type_responsible_maintenance_maintenance_corrective = trm.id_type_responsible_maintenance
                LEFT JOIN maintenance_corrective_origins mco ON mc.id_origin_maintenance_corrective = mco.id_maintenance_corrective_origin
                WHERE mc.id_asset_maintenance_corrective = ?
                  AND mc.status_deleted_maintenance_corrective = 0
                ORDER BY COALESCE(mc.date_repair_maintenance_corrective, mc.date_created_maintenance_corrective) DESC, mc.id_maintenance_corrective DESC";
        return $this->select_all($sql, [intval($maintenance['id_asset_maintenance_corrective'])]);
    }

    public function show_history_maintenance_corrective_one(int $id_history_maintenance_corrective)
    {
        $sql = "SELECT a.code_inventory_asset,
                       ta.name_type_asset,
                       ca.name_category_asset,
                       trm.name_type_responsible_maintenance,
                       hmc.date_scheduled_history_maintenance_corrective,
                       hmc.date_performed_history_maintenance_corrective,
                       hmc.action_history_maintenance_corrective,
                       hmc.observation_history_maintenance_corrective
                FROM history_maintenances_corrective hmc
                INNER JOIN maintenances_corrective mc ON hmc.id_maintenance_corrective = mc.id_maintenance_corrective
                INNER JOIN assets a ON mc.id_asset_maintenance_corrective = a.id_asset
                INNER JOIN types_asset ta ON a.id_type_asset_asset = ta.id_type_asset
                INNER JOIN categories_asset ca ON a.id_category_asset_asset = ca.id_category_asset
                INNER JOIN types_responsible_maintenance trm ON hmc.id_type_responsible_maintenance = trm.id_type_responsible_maintenance
                WHERE hmc.id_history_maintenance_corrective = ?";
        return $this->select($sql, [$id_history_maintenance_corrective]);
    }

    public function show_maintenance_corrective(int $id_maintenance_corrective)
    {
        $sql = "SELECT a.id_asset,
                       a.code_inventory_asset,
                       a.descriptions_asset,
                       a.observations_asset,
                       ta.name_type_asset,
                       ca.name_category_asset,
                       b.name_brand,
                       coa.name_condition_asset,
                       trm.id_type_responsible_maintenance,
                       trm.name_type_responsible_maintenance,
                       mc.id_incident_maintenance_corrective,
                       mc.id_origin_maintenance_corrective,
                       mc.status_active_maintenance_corrective,
                       COALESCE(mco.name_maintenance_corrective_origin, 'Sin origen') AS name_maintenance_corrective_origin,
                       mc.date_repair_maintenance_corrective,
                       mc.time_repair_maintenance_corrective,
                       mc.action_maintenance_corrective,
                       mc.description_maintenance_corrective,
                       mc.observation_maintenance_corrective,
                       mc.date_created_maintenance_corrective
                FROM maintenances_corrective mc
                INNER JOIN assets a ON mc.id_asset_maintenance_corrective = a.id_asset
                INNER JOIN types_asset ta ON a.id_type_asset_asset = ta.id_type_asset
                INNER JOIN categories_asset ca ON a.id_category_asset_asset = ca.id_category_asset
                INNER JOIN brands b ON a.id_brand_asset = b.id_brand
                INNER JOIN conditions_asset coa ON a.id_condition_asset_asset = coa.id_condition_asset
                INNER JOIN types_responsible_maintenance trm ON mc.id_type_responsible_maintenance_maintenance_corrective = trm.id_type_responsible_maintenance
                LEFT JOIN maintenance_corrective_origins mco ON mc.id_origin_maintenance_corrective = mco.id_maintenance_corrective_origin
                WHERE mc.id_maintenance_corrective = ?
                  AND mc.status_deleted_maintenance_corrective = 0";
        return $this->select($sql, [$id_maintenance_corrective]);
    }

    public function current_incident_id(int $id_maintenance_corrective): ?int
    {
        $row = $this->select(
            "SELECT id_incident_maintenance_corrective FROM maintenances_corrective WHERE id_maintenance_corrective = ? AND status_deleted_maintenance_corrective = 0",
            [$id_maintenance_corrective]
        );
        return !empty($row['id_incident_maintenance_corrective']) ? intval($row['id_incident_maintenance_corrective']) : null;
    }

    public function show_spare_parts_by_maintenance(int $id_maintenance_corrective): array
    {
        $sql = "SELECT mcsp.id_maintenance_corrective_spare_part,
                       mcsp.id_spare_part,
                       sp.code_spare_part,
                       sp.name_spare_part,
                       sp.unit_spare_part,
                       mcsp.quantity_maintenance_corrective_spare_part,
                       mcsp.observation_maintenance_corrective_spare_part
                FROM maintenance_corrective_spare_parts mcsp
                INNER JOIN spare_parts sp ON mcsp.id_spare_part = sp.id_spare_part
                WHERE mcsp.id_maintenance_corrective = ?
                  AND mcsp.status_deleted_maintenance_corrective_spare_part = 0
                ORDER BY mcsp.id_maintenance_corrective_spare_part ASC";
        return $this->select_all($sql, [$id_maintenance_corrective]);
    }

    public function show_files(int $id_maintenance_corrective)
    {
        $sql = "SELECT a.id_attachment_maintenance_corrective,
                       a.id_maintenance_corrective,
                       a.route_attachment_maintenance_corrective,
                       a.name_attachment_maintenance_corrective
                FROM attachments_maintenance_corrective a
                INNER JOIN maintenances_corrective mc ON a.id_maintenance_corrective = mc.id_maintenance_corrective
                WHERE a.id_maintenance_corrective = ?";
        return $this->select_all($sql, [$id_maintenance_corrective]);
    }

    public function insert_maintenance_corrective(
        int $id_asset,
        ?int $id_incident_maintenance_corrective,
        ?int $id_origin_maintenance_corrective,
        ?string $date_repair_maintenance_corrective,
        ?string $time_repair_maintenance_corrective,
        int $id_type_responsible_maintenance,
        string $action_maintenance_corrective,
        string $observation_maintenance_corrective,
        array $spare_parts = [],
        int $status_active_maintenance_corrective = 1
    ) {
        $status_active_maintenance_corrective = $status_active_maintenance_corrective === 0 ? 0 : 1;
        $id_frequency = $this->default_frequency_id();
        $query_insert = "INSERT INTO maintenances_corrective(
                            id_asset_maintenance_corrective,
                            id_incident_maintenance_corrective,
                            id_origin_maintenance_corrective,
                            id_frequency_maintenance_maintenance_corrective,
                            id_type_responsible_maintenance_maintenance_corrective,
                            date_scheduled_maintenance_corrective,
                            date_next_maintenance_corrective,
                            date_repair_maintenance_corrective,
                            time_repair_maintenance_corrective,
                            action_maintenance_corrective,
                            description_maintenance_corrective,
                            observation_maintenance_corrective,
                            status_active_maintenance_corrective
                        ) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $data = [
            $id_asset,
            $id_incident_maintenance_corrective,
            $id_origin_maintenance_corrective,
            $id_frequency,
            $id_type_responsible_maintenance,
            null,
            null,
            $date_repair_maintenance_corrective,
            $time_repair_maintenance_corrective,
            $action_maintenance_corrective,
            $action_maintenance_corrective ?: 'Ninguna.',
            $observation_maintenance_corrective,
            $status_active_maintenance_corrective
        ];
        $request_insert = $this->insert($query_insert, $data);
        if ($request_insert) {
            if (!$this->sync_spare_parts(intval($request_insert), $spare_parts)) {
                return 0;
            }
            $this->refresh_asset_maintenance_status($id_asset);
            if ($status_active_maintenance_corrective === 0 && !empty($id_incident_maintenance_corrective)) {
                $this->close_related_incident_if_completed(intval($id_incident_maintenance_corrective), intval($request_insert));
            }
        }
        return $request_insert;
    }

    public function update_maintenance_corrective(
        int $id_maintenance_corrective,
        int $id_asset,
        ?int $id_incident_maintenance_corrective,
        ?int $id_origin_maintenance_corrective,
        ?string $date_repair_maintenance_corrective,
        ?string $time_repair_maintenance_corrective,
        int $id_type_responsible_maintenance,
        string $action_maintenance_corrective,
        string $observation_maintenance_corrective,
        array $spare_parts = [],
        int $status_active_maintenance_corrective = 1
    ) {
        $status_active_maintenance_corrective = $status_active_maintenance_corrective === 0 ? 0 : 1;
        $row = $this->select(
            "SELECT id_asset_maintenance_corrective FROM maintenances_corrective WHERE id_maintenance_corrective = ?",
            [$id_maintenance_corrective]
        );
        $old_asset_id = isset($row['id_asset_maintenance_corrective']) ? intval($row['id_asset_maintenance_corrective']) : 0;

        $sql_update = "UPDATE maintenances_corrective SET
                        id_asset_maintenance_corrective = ?,
                        id_incident_maintenance_corrective = ?,
                        id_origin_maintenance_corrective = ?,
                        id_type_responsible_maintenance_maintenance_corrective = ?,
                        date_scheduled_maintenance_corrective = ?,
                        date_next_maintenance_corrective = ?,
                        date_repair_maintenance_corrective = ?,
                        time_repair_maintenance_corrective = ?,
                        action_maintenance_corrective = ?,
                        description_maintenance_corrective = ?,
                        observation_maintenance_corrective = ?,
                        status_active_maintenance_corrective = ?
                    WHERE id_maintenance_corrective = ?";
        $data = [
            $id_asset,
            $id_incident_maintenance_corrective,
            $id_origin_maintenance_corrective,
            $id_type_responsible_maintenance,
            null,
            null,
            $date_repair_maintenance_corrective,
            $time_repair_maintenance_corrective,
            $action_maintenance_corrective,
            $action_maintenance_corrective ?: 'Ninguna.',
            $observation_maintenance_corrective,
            $status_active_maintenance_corrective,
            $id_maintenance_corrective
        ];
        $request_update = $this->update($sql_update, $data);
        if ($request_update && !$this->sync_spare_parts($id_maintenance_corrective, $spare_parts)) {
            return false;
        }

        if ($old_asset_id > 0 && $old_asset_id !== $id_asset) {
            $this->refresh_asset_maintenance_status($old_asset_id);
        }
        $this->refresh_asset_maintenance_status($id_asset);

        if ($status_active_maintenance_corrective === 0 && !empty($id_incident_maintenance_corrective)) {
            $this->close_related_incident_if_completed(intval($id_incident_maintenance_corrective), $id_maintenance_corrective);
        }

        return $request_update;
    }

    private function sync_spare_parts(int $id_maintenance_corrective, array $spare_parts): bool
    {
        $current = $this->show_spare_parts_by_maintenance($id_maintenance_corrective);
        $restoredBySpare = [];
        foreach ($current as $item) {
            $id = intval($item['id_spare_part']);
            $restoredBySpare[$id] = ($restoredBySpare[$id] ?? 0) + floatval($item['quantity_maintenance_corrective_spare_part']);
        }

        $requiredBySpare = [];
        foreach ($spare_parts as $item) {
            $id_spare_part = intval($item['id_spare_part'] ?? 0);
            $quantity = floatval($item['quantity'] ?? 0);
            if ($id_spare_part <= 0 || $quantity <= 0) {
                continue;
            }
            $requiredBySpare[$id_spare_part] = ($requiredBySpare[$id_spare_part] ?? 0) + $quantity;
        }

        foreach ($requiredBySpare as $id_spare_part => $quantity) {
            $spare = $this->select(
                "SELECT stock_current_spare_part FROM spare_parts WHERE id_spare_part = ? AND status_deleted_spare_part = 0 AND status_active_spare_part = 1",
                [$id_spare_part]
            );
            if (!$spare) {
                return false;
            }
            $available = floatval($spare['stock_current_spare_part']) + ($restoredBySpare[$id_spare_part] ?? 0);
            if ($available < $quantity) {
                return false;
            }
        }

        foreach ($current as $item) {
            $this->update(
                "UPDATE spare_parts SET stock_current_spare_part = stock_current_spare_part + ? WHERE id_spare_part = ?",
                [floatval($item['quantity_maintenance_corrective_spare_part']), intval($item['id_spare_part'])]
            );
        }
        $this->delete("DELETE FROM maintenance_corrective_spare_parts WHERE id_maintenance_corrective = ?", [$id_maintenance_corrective]);

        foreach ($spare_parts as $item) {
            $id_spare_part = intval($item['id_spare_part'] ?? 0);
            $quantity = floatval($item['quantity'] ?? 0);
            $observation = trim($item['observation'] ?? '') ?: null;
            if ($id_spare_part <= 0 || $quantity <= 0) {
                continue;
            }

            $spare = $this->select(
                "SELECT stock_current_spare_part FROM spare_parts WHERE id_spare_part = ? AND status_deleted_spare_part = 0 AND status_active_spare_part = 1",
                [$id_spare_part]
            );
            if (!$spare) {
                return false;
            }
            $stock_current = floatval($spare['stock_current_spare_part']);
            if ($stock_current < $quantity) {
                return false;
            }
            $stock_after = $stock_current - $quantity;

            $this->insert(
                "INSERT INTO maintenance_corrective_spare_parts(id_maintenance_corrective, id_spare_part, quantity_maintenance_corrective_spare_part, observation_maintenance_corrective_spare_part) VALUES(?,?,?,?)",
                [$id_maintenance_corrective, $id_spare_part, $quantity, $observation]
            );
            $this->update(
                "UPDATE spare_parts SET stock_current_spare_part = ? WHERE id_spare_part = ?",
                [$stock_after, $id_spare_part]
            );
            $this->insert(
                "INSERT INTO spare_part_movements(id_spare_part, id_maintenance_corrective, type_spare_part_movement, quantity_spare_part_movement, reason_spare_part_movement, stock_after_spare_part_movement) VALUES(?,?,?,?,?,?)",
                [$id_spare_part, $id_maintenance_corrective, 'OUT', $quantity, 'Uso en mantenimiento correctivo #' . $id_maintenance_corrective, $stock_after]
            );
        }
        return true;
    }

    public function execute_maintenance_corrective(
        int $id_maintenance_corrective,
        int $id_status_maintenance,
        string $action_history_maintenance_corrective,
        string $observation_history_maintenance_corrective
    ) {
        $request_search = $this->select(
            "SELECT id_frequency_maintenance_maintenance_corrective,
                    id_type_responsible_maintenance_maintenance_corrective,
                    id_asset_maintenance_corrective,
                    COALESCE(date_repair_maintenance_corrective, date_scheduled_maintenance_corrective, CURRENT_DATE()) AS date_scheduled_maintenance_corrective,
                    id_incident_maintenance_corrective
             FROM maintenances_corrective
             WHERE id_maintenance_corrective = ?",
            [$id_maintenance_corrective]
        );

        $id_frequency = isset($request_search['id_frequency_maintenance_maintenance_corrective'])
            ? intval($request_search['id_frequency_maintenance_maintenance_corrective'])
            : $this->default_frequency_id();
        $id_type_responsible = intval($request_search['id_type_responsible_maintenance_maintenance_corrective']);
        $date_scheduled = $request_search['date_scheduled_maintenance_corrective'] ?: date('Y-m-d');
        $date_performed = date('Y-m-d');

        $history_id = $this->insert(
            "INSERT INTO history_maintenances_corrective(id_maintenance_corrective, id_frequency_maintenance, id_status_maintenance, id_type_responsible_maintenance, date_scheduled_history_maintenance_corrective, date_performed_history_maintenance_corrective, action_history_maintenance_corrective, observation_history_maintenance_corrective) VALUES(?,?,?,?,?,?,?,?)",
            [$id_maintenance_corrective, $id_frequency, $id_status_maintenance, $id_type_responsible, $date_scheduled, $date_performed, $action_history_maintenance_corrective, $observation_history_maintenance_corrective]
        );

        $company = $this->select("SELECT id_company FROM maintenances_corrective_company WHERE id_maintenance_corrective = ?", [$id_maintenance_corrective]);
        if ($company && isset($company['id_company'])) {
            $this->insert(
                "INSERT INTO history_maintenances_corrective_company(id_history_maintenance_corrective, id_company) VALUES(?,?)",
                [$history_id, intval($company['id_company'])]
            );
        }

        $request_update = $this->update(
            "UPDATE maintenances_corrective SET status_active_maintenance_corrective = ? WHERE id_maintenance_corrective = ?",
            [0, $id_maintenance_corrective]
        );
        if ($request_update && !empty($request_search['id_asset_maintenance_corrective'])) {
            $this->refresh_asset_maintenance_status(intval($request_search['id_asset_maintenance_corrective']));
        }
        if ($request_update && !empty($request_search['id_incident_maintenance_corrective'])) {
            $this->close_related_incident_if_completed(intval($request_search['id_incident_maintenance_corrective']), $id_maintenance_corrective);
        }
        return $request_update;
    }

    public function relate_maintenance_corrective_company(int $id_maintenance_corrective, int $id_company)
    {
        $request_search = $this->select("SELECT id_maintenance_corrective_company FROM maintenances_corrective_company WHERE id_maintenance_corrective = ?", [$id_maintenance_corrective]);
        if ($request_search) {
            return $this->update(
                "UPDATE maintenances_corrective_company SET id_maintenance_corrective = ?, id_company = ? WHERE id_maintenance_corrective_company = ?",
                [$id_maintenance_corrective, $id_company, intval($request_search['id_maintenance_corrective_company'])]
            );
        }
        return $this->insert("INSERT INTO maintenances_corrective_company(id_maintenance_corrective, id_company) VALUES(?,?)", [$id_maintenance_corrective, $id_company]);
    }

    public function insert_attachment_maintenance_corrective(int $id_maintenance_corrective, string $file_path, string $file_name)
    {
        return $this->insert(
            "INSERT INTO attachments_maintenance_corrective(id_maintenance_corrective, route_attachment_maintenance_corrective, name_attachment_maintenance_corrective) VALUES(?, ?, ?)",
            [$id_maintenance_corrective, $file_path, $file_name]
        );
    }

    public function delete_attachment_maintenance_corrective(int $id_attachment_maintenance_corrective)
    {
        return $this->delete("DELETE FROM attachments_maintenance_corrective WHERE id_attachment_maintenance_corrective = ?", [$id_attachment_maintenance_corrective]);
    }

    public function delete_maintenance_corrective(int $id_maintenance_corrective)
    {
        $row = $this->select(
            "SELECT id_asset_maintenance_corrective FROM maintenances_corrective WHERE id_maintenance_corrective = ?",
            [$id_maintenance_corrective]
        );
        $request = $this->update("UPDATE maintenances_corrective SET status_deleted_maintenance_corrective = ? WHERE id_maintenance_corrective = ?", [1, $id_maintenance_corrective]);
        if ($request && !empty($row['id_asset_maintenance_corrective'])) {
            $this->refresh_asset_maintenance_status(intval($row['id_asset_maintenance_corrective']));
        }
        return $request;
    }
}
