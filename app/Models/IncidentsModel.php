<?php 
	class IncidentsModel extends Mysql
	{
		private $int_id_user;
		private $int_id_incident;
		private $int_id_attachment_incident;
		private $str_title;
		private $int_id_category;
		private $int_id_type;
		private $int_id_priority;
		private $str_description;
		private $int_status_deleted_incident;

		public function __construct()
		{
			parent::__construct();
			$this->ensure_schema();
		}

		private function ensure_schema(): void
		{
			try {
				$this->add_column_if_missing('incidents', 'corrective_derivation_status_incident', "VARCHAR(20) NOT NULL DEFAULT 'none' AFTER id_asset_incident");
				$this->add_column_if_missing('incidents', 'id_maintenance_corrective_incident', 'INT DEFAULT NULL AFTER corrective_derivation_status_incident');
				$this->add_column_if_missing('incidents', 'date_derived_corrective_incident', 'TIMESTAMP NULL DEFAULT NULL AFTER id_maintenance_corrective_incident');
				$this->add_column_if_missing('incidents', 'date_updated_corrective_derivation_incident', 'TIMESTAMP NULL DEFAULT NULL AFTER date_derived_corrective_incident');
				$this->add_column_if_missing('incidents', 'date_incident_date', 'DATE DEFAULT NULL AFTER id_type_incident_incident');
				$this->add_column_if_missing('incidents', 'time_incident_time', 'TIME DEFAULT NULL AFTER date_incident_date');
				if ($this->column_exists('incidents', 'date_incident_incident')) {
					$this->update("UPDATE incidents SET date_incident_date = COALESCE(date_incident_date, DATE(date_incident_incident)), time_incident_time = COALESCE(time_incident_time, TIME(date_incident_incident)) WHERE date_incident_incident IS NOT NULL");
				}

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
			} catch (Throwable $e) {
				// Older installations should keep running even if schema review is needed.
			}
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


		private function split_incident_datetime(?string $date_incident): array
		{
			if (empty($date_incident)) {
				return [null, null];
			}

			$date_incident = trim($date_incident);
			if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_incident)) {
				$timestamp = strtotime($date_incident);
				return $timestamp === false ? [null, null] : [date('Y-m-d', $timestamp), null];
			}

			$timestamp = strtotime($date_incident);
			if ($timestamp === false) {
				return [null, null];
			}

			return [date('Y-m-d', $timestamp), date('H:i:s', $timestamp)];
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

		public function insert_new_incident(int $id_user, string $title, int $id_type_incident, int $priority, int $space, ?string $date_incident, string $description){
			$this->int_id_user = $id_user;
			$this->str_title = $title;
			$this->int_id_type_incident = $id_type_incident;
			$this->int_id_priority = $priority ;
			$this->int_id_space = $space;
			$this->str_date_incident = $date_incident;
			$this->str_description = $description;
			$query_insert = "INSERT INTO incidents(id_user_incident, id_space_incident, id_type_incident_incident, id_priority_incident, title_incident, date_incident_date, time_incident_time, description_incident) VALUES(?,?,?,?,?,?,?,?)";
			[$incident_date, $incident_time] = $this->split_incident_datetime($this->str_date_incident);
			$data = [
				$this->int_id_user,
				$this->int_id_space,
				$this->int_id_type_incident,
				$this->int_id_priority,
				$this->str_title,
				$incident_date,
				$incident_time,
				$this->str_description
			];
			$request_insert = $this->insert($query_insert, $data);
            if ($request_insert) {
                $this->insert("INSERT INTO incident_events(id_incident,event_type,id_user,source_key) VALUES(?,'created',?,?)",
                    [$request_insert,$id_user,'created:'.$request_insert]);
            }
	        $return = $request_insert;
	        return $return;
		}

        public function valid_incident_asset(int $id_asset): bool
        {
            return (bool)$this->select('SELECT id_asset FROM assets WHERE id_asset=? AND status_active_asset=1 AND status_deleted_asset=0', [$id_asset]);
        }

        private function transition_incident(int $id, bool $open): bool
        {
            $pdo = $this->connection();
            $ownTransaction = !$pdo->inTransaction();
            if ($ownTransaction) $pdo->beginTransaction();
            else $pdo->exec('SAVEPOINT incident_transition');
            try {
                $incident = $this->select('SELECT * FROM incidents WHERE id_incident=? AND status_active_incident=1 AND status_deleted_incident=0 FOR UPDATE', [$id]);
                $valid = $incident && (int)$incident['status_open_incident'] !== (int)$open;
                if (!$open && $valid) $valid = trim((string)$incident['solution_incident']) !== '';
                if (!$valid) {
                    if ($ownTransaction) $pdo->rollBack();
                    else { $pdo->exec('ROLLBACK TO SAVEPOINT incident_transition'); $pdo->exec('RELEASE SAVEPOINT incident_transition'); }
                    return false;
                }
                $this->update('UPDATE incidents SET status_open_incident=?, datetime_closed_record='.($open ? 'NULL' : 'NOW()').' WHERE id_incident=?', [(int)$open,$id]);
                $event = $this->insert('INSERT INTO incident_events(id_incident,event_type,id_user,solution_snapshot,resolution_date,resolution_time) VALUES(?,?,?,?,?,?)', [
                    $id, $open ? 'reopened' : 'closed', $_SESSION['user_data']['id_user'] ?? null,
                    $incident['solution_incident'], $incident['date_closed_incident'], $incident['time_closed_incident']
                ]);
                if (!$event) throw new RuntimeException('No se pudo registrar el seguimiento de la incidencia.');
                if ($ownTransaction) $pdo->commit();
                else $pdo->exec('RELEASE SAVEPOINT incident_transition');
                return true;
            } catch (Throwable $e) {
                if ($ownTransaction && $pdo->inTransaction()) $pdo->rollBack();
                elseif (!$ownTransaction) { $pdo->exec('ROLLBACK TO SAVEPOINT incident_transition'); $pdo->exec('RELEASE SAVEPOINT incident_transition'); }
                throw $e;
            }
        }

        public function incident_events(int $id): array
        {
            return $this->select_all("SELECT history.*, history.id_incident_event AS event_id,
                COALESCE(NULLIF(TRIM(CONCAT_WS(' ', e.first_name_employee,e.middle_name_employee,e.paternal_last_name_employee,e.maternal_last_name_employee)), ''),u.name_user,
                    CASE WHEN history.id_user IS NULL THEN 'Sin usuario registrado' ELSE CONCAT('Usuario no disponible (#',history.id_user,')') END) AS actor_name,
                u.name_user AS actor_username
                FROM incident_events history LEFT JOIN users u ON u.id_user=history.id_user
                LEFT JOIN employees e ON e.id_employee=u.id_employee_user
                WHERE history.id_incident=? ORDER BY history.date_created DESC,history.id_incident_event DESC", [$id]);
        }

        private function is_incident_open(int $id_incident): bool
        {
            $incident = $this->select('SELECT status_open_incident FROM incidents WHERE id_incident = ? AND status_active_incident=1 AND status_deleted_incident=0', [$id_incident]);
            return $incident && (int) $incident['status_open_incident'] === 1;
        }

		public function update_incident(int $id_incident, string $title, int $id_type_incident, int $priority, int $space, ?string $date_incident, string $description){
            if (!$this->is_incident_open($id_incident)) { return false; }
			$this->int_id_incident = $id_incident;
			$this->str_title = $title;
			$this->int_id_type_incident = $id_type_incident;
			$this->int_id_priority = $priority;
			$this->int_id_space = $space;
			$this->str_date_incident = $date_incident;
			$this->str_description = $description;
			[$incident_date, $incident_time] = $this->split_incident_datetime($this->str_date_incident);
			$sql = "UPDATE incidents SET 
					id_space_incident = ?,
					id_type_incident_incident = ?, 
					id_priority_incident = ?, 
					title_incident = ?,
					date_incident_date = ?,
					time_incident_time = ?,
					description_incident = ? WHERE id_incident = ? AND status_open_incident = 1";
			$data = [
				$this->int_id_space,
				$this->int_id_type_incident,
				$this->int_id_priority,
				$this->str_title,
				$incident_date,
				$incident_time,
				$this->str_description,
				$this->int_id_incident
			];
			$request = $this->update($sql, $data);
			$return = $request;
	        return $return;
		}

		public function relate_incident_asset(int $id_incident, ?int $id_asset){
            if ($id_asset !== null && !$this->valid_incident_asset($id_asset)) return false;
            if (!$this->is_incident_open($id_incident)) { return false; }
			$this->int_id_incident = $id_incident;
			$this->int_id_asset = $id_asset;
			$sql = "UPDATE incidents SET id_asset_incident = ? WHERE id_incident = ? AND status_open_incident = 1";
			$data = [
				$this->int_id_asset,
				$this->int_id_incident
			];
			$request_update = $this->update($sql, $data);
	        return $request_update;
		}

        public function clear_incident_field(int $id_incident, string $field): bool
        {
            if ($field === 'resolution') { return (bool)$this->update_manual_closed_incident($id_incident, null, null); }
            if (!in_array($field, ['diagnostic', 'solution'], true)) { return false; }
            return $field === 'diagnostic'
                ? (bool)$this->save_incident_diagnostic($id_incident, '')
                : (bool)$this->save_incident_solution($id_incident, '');
        }

		public function save_incident_diagnostic(int $id_incident, string $diagnostic_incident){
            if (!$this->is_incident_open($id_incident)) { return false; }
			$this->int_id_incident = $id_incident;
			$this->str_diagnostic_incident = $diagnostic_incident;
			$sql = "UPDATE incidents SET diagnostic_incident = ? WHERE id_incident = ? AND status_open_incident = 1";
			$data = [
				$this->str_diagnostic_incident,
				$this->int_id_incident
			];
			$request_update = $this->update($sql, $data);
	        return $request_update;
		}

		public function save_incident_solution(int $id_incident, string $solution_incident){
            if (!$this->is_incident_open($id_incident)) { return false; }
			$this->int_id_incident = $id_incident;
			$this->str_solution_incident = $solution_incident;
			$sql = "UPDATE incidents SET solution_incident = ? WHERE id_incident = ? AND status_open_incident = 1";
			$data = [
				$this->str_solution_incident,
				$this->int_id_incident
			];
			$request_update = $this->update($sql, $data);
	        return $request_update;
		}

		public function delete_attachment_incident(int $id_attachment_incident){
			$this->int_id_attachment_incident = $id_attachment_incident;
			$query  = "DELETE FROM attachments_incident WHERE id_attachment_incident = ?";
			$data = [
				$this->int_id_attachment_incident
			];
	        $request = $this->delete($query, $data);
	        return $request;
		}

		public function insert_attachment_incident(int $id_incident, string $file_path, string $file_name) {
			$query_insert = "INSERT INTO attachments_incident(id_incident_attachment_incident, route_attachment_incident, name_attachment_incident) VALUES(?, ?, ?)";
			$data = [
				$id_incident,
                $file_path,
				$file_name
			];
            $request_insert = $this->insert($query_insert, $data);
            return $request_insert;
        }
		




        public function select_all_incidents()
        {
            return $this->select_all("SELECT t.id_incident,t.title_incident,t.status_open_incident,
                ti.name_type_incident,p.name_priority,
                CONCAT(e.name_environment, ' · Piso ', e.floor_environment) AS display_location,
                DATE_FORMAT(t.date_incident_date, '%d/%m/%Y') AS date_incident
                FROM incidents t LEFT JOIN types_incident ti ON ti.id_type_incident=t.id_type_incident_incident
                LEFT JOIN priorities p ON p.id_priority=t.id_priority_incident
                LEFT JOIN environments e ON e.id_environment=t.id_space_incident
                WHERE t.status_active_incident=1 AND t.status_deleted_incident=0 ORDER BY t.id_incident DESC");
        }

		public function show_specific_incident(int $id_incident){
			$this->int_id_incident = $id_incident;
			$sql = "SELECT 	t.id_incident,
							t.id_user_incident,
							t.id_asset_incident,
							t.corrective_derivation_status_incident,
							t.id_maintenance_corrective_incident,
							t.date_derived_corrective_incident,
							t.date_updated_corrective_derivation_incident,

							-- Nombre completo del usuario creador del incident
							CONCAT(
								pt.abbreviation_professional_title, ' ',
								CASE e.preferred_name_employee
									WHEN 1 THEN e.first_name_employee
									WHEN 2 THEN e.middle_name_employee
								END, ' ',
								e.paternal_last_name_employee, ' ',
								e.maternal_last_name_employee
							) AS user_display_name,
							e.email_employee AS user_email,


							-- Ubicación
							sp.id_environment AS id_space,
							CONCAT(
								'Piso ', sp.floor_environment, ' - ', sp.name_environment
							) AS display_location,

							t.title_incident,
							ti.id_type_incident,
							ti.name_type_incident,							
							p.id_priority,
							p.name_priority,
							t.description_incident,
							t.status_open_incident,
							t.date_created_incident,
							t.datetime_closed_record,
							t.date_closed_incident,
							t.time_closed_incident,
							t.date_incident_date,
							t.time_incident_time,
							TIMESTAMP(t.date_incident_date, t.time_incident_time) AS date_incident_incident,
							a.code_inventory_asset AS affected_code_inventory_asset,
							a.descriptions_asset AS affected_descriptions_asset,
							a.observations_asset AS affected_observations_asset,
							can.name_category_asset AS affected_name_category_asset,
							tan.name_type_asset AS affected_name_type_asset,							
							bn.name_brand AS affected_name_brand,
							a.model_asset AS affected_model_asset,
							a.serial_number_asset AS affected_serial_number_asset,
							
							t.diagnostic_incident AS action_diagnostic,
							t.solution_incident AS action_solution
					FROM incidents t 
					LEFT JOIN types_incident ti ON t.id_type_incident_incident = ti.id_type_incident
					INNER JOIN priorities p ON t.id_priority_incident = p.id_priority
					INNER JOIN environments sp ON t.id_space_incident = sp.id_environment

					-- Usuario creador del incident
					LEFT JOIN users u ON t.id_user_incident = u.id_user
					LEFT JOIN employees e ON u.id_employee_user = e.id_employee
					LEFT JOIN professional_titles pt ON e.id_professional_title_employee = pt.id_professional_title


					-- Activo afectado
					LEFT JOIN assets a ON t.id_asset_incident = a.id_asset
					LEFT JOIN categories_asset can ON a.id_category_asset_asset = can.id_category_asset
					LEFT JOIN types_asset tan ON a.id_type_asset_asset = tan.id_type_asset			
					LEFT JOIN brands bn ON a.id_brand_asset = bn.id_brand
					WHERE t.id_incident = ?";
			$data = [
				$this->int_id_incident
			];
			$request = $this->select($sql, $data);
			return $request;
		}

		public function show_attachments_incident(int $id_incident){
			$this->int_id_incident = $id_incident;
			$sql = "SELECT 	a.id_attachment_incident, 
							a.id_incident_attachment_incident,
							a.route_attachment_incident,
							a.name_attachment_incident
					FROM attachments_incident a 
					INNER JOIN incidents t ON a.id_incident_attachment_incident = t.id_incident
					WHERE a.id_incident_attachment_incident = ?";
			$data = [
				$this->int_id_incident
			];
			$request = $this->select_all($sql, $data);
			return $request;
		}



		public function report_filter_options(): array
		{
			return [
				'types' => $this->select_all("SELECT id_type_incident, name_type_incident FROM types_incident WHERE status_active_type_incident = 1 AND status_deleted_type_incident = 0 ORDER BY name_type_incident ASC"),
				'priorities' => $this->select_all("SELECT id_priority, name_priority FROM priorities WHERE status_active_priority = 1 AND status_deleted_priority = 0 ORDER BY id_priority ASC"),
			];
		}

		public function incident_report_data(array $filters): array
		{
			[$where, $params] = $this->incident_report_where($filters);
			$limit = max(1, min(150, (int)($filters['limit'] ?? 50)));

			$summary = $this->select("SELECT
					COUNT(*) AS total,
					SUM(CASE WHEN t.status_open_incident = 1 THEN 1 ELSE 0 END) AS open,
					SUM(CASE WHEN t.status_open_incident = 0 THEN 1 ELSE 0 END) AS closed,
					SUM(CASE WHEN t.id_asset_incident IS NOT NULL THEN 1 ELSE 0 END) AS with_asset
				FROM incidents t
				LEFT JOIN assets a ON t.id_asset_incident = a.id_asset
				$where", $params);

			$by_type = $this->select_all("SELECT COALESCE(ti.name_type_incident, 'No definido') AS label, COUNT(*) AS total
				FROM incidents t
				LEFT JOIN types_incident ti ON t.id_type_incident_incident = ti.id_type_incident
				LEFT JOIN assets a ON t.id_asset_incident = a.id_asset
				$where
				GROUP BY COALESCE(ti.name_type_incident, 'No definido')
				ORDER BY total DESC, label ASC", $params);

			$by_priority = $this->select_all("SELECT COALESCE(p.name_priority, 'No definido') AS label, COUNT(*) AS total
				FROM incidents t
				LEFT JOIN priorities p ON t.id_priority_incident = p.id_priority
				LEFT JOIN assets a ON t.id_asset_incident = a.id_asset
				$where
				GROUP BY COALESCE(p.name_priority, 'No definido')
				ORDER BY total DESC, label ASC", $params);

			$incidents = $this->select_all("SELECT
					t.id_incident,
					t.title_incident,
					t.description_incident,
					t.status_open_incident,
					t.date_created_incident,
					t.date_incident_date,
					t.time_incident_time,
					TIMESTAMP(t.date_incident_date, t.time_incident_time) AS date_incident_incident,
					t.datetime_closed_record,
					t.date_closed_incident,
					t.time_closed_incident,
					t.diagnostic_incident AS action_diagnostic,
					t.solution_incident AS action_solution,
					COALESCE(ti.name_type_incident, 'No definido') AS name_type_incident,
					COALESCE(p.name_priority, 'No definido') AS name_priority,
					CONCAT('Piso ', sp.floor_environment, ' - ', sp.name_environment) AS display_location,
					COALESCE(NULLIF(TRIM(CONCAT_WS(' ',e.first_name_employee,e.middle_name_employee,e.paternal_last_name_employee,e.maternal_last_name_employee)),''),u.name_user,'Usuario no disponible') AS user_display_name,
					a.code_inventory_asset AS affected_code_inventory_asset,
					tan.name_type_asset AS affected_name_type_asset,
					can.name_category_asset AS affected_name_category_asset,
					bn.name_brand AS affected_name_brand,
					a.model_asset AS affected_model_asset
				FROM incidents t
				LEFT JOIN types_incident ti ON t.id_type_incident_incident = ti.id_type_incident
				LEFT JOIN priorities p ON t.id_priority_incident = p.id_priority
				LEFT JOIN environments sp ON t.id_space_incident = sp.id_environment
				LEFT JOIN users u ON t.id_user_incident = u.id_user
				LEFT JOIN employees e ON u.id_employee_user = e.id_employee
				LEFT JOIN professional_titles pt ON e.id_professional_title_employee = pt.id_professional_title
				LEFT JOIN assets a ON t.id_asset_incident = a.id_asset
				LEFT JOIN categories_asset can ON a.id_category_asset_asset = can.id_category_asset
				LEFT JOIN types_asset tan ON a.id_type_asset_asset = tan.id_type_asset
				LEFT JOIN brands bn ON a.id_brand_asset = bn.id_brand
				$where
				ORDER BY t.date_incident_date DESC, t.time_incident_time DESC, t.id_incident DESC
				LIMIT $limit", $params);

            $ids=array_map(fn($row)=>(int)$row['id_incident'],$incidents);
            $attachments=[];
            if($ids){
                $marks=implode(',',array_fill(0,count($ids),'?'));
                foreach($this->select_all("SELECT id_incident_attachment_incident,id_attachment_incident,name_attachment_incident,route_attachment_incident FROM attachments_incident WHERE status_deleted_attachment_incident=0 AND id_incident_attachment_incident IN ($marks) ORDER BY id_attachment_incident",$ids) as $file){
                    $attachments[(int)$file['id_incident_attachment_incident']][]=$file;
                }

            }
            foreach($incidents as &$incident){
                $incident['report_attachments']=$attachments[(int)$incident['id_incident']]??[];
            }
            unset($incident);

			return [
				'summary' => $summary ?: ['total' => 0, 'open' => 0, 'closed' => 0, 'with_asset' => 0],
				'by_type' => $by_type,
				'by_priority' => $by_priority,
				'incidents' => $incidents,
				'filters_applied' => $this->incident_report_filter_labels($filters),
			];
		}

		private function incident_report_where(array $filters): array
		{
			$where = ["t.status_active_incident != 0", "t.status_deleted_incident = 0"];
			$params = [];

			if (!empty($filters['date_start'])) {
				$where[] = "t.date_incident_date >= ?";
				$params[] = $filters['date_start'];
			}
			if (!empty($filters['date_end'])) {
				$where[] = "t.date_incident_date <= ?";
				$params[] = $filters['date_end'];
			}
			if (($filters['status'] ?? 'all') === 'open') {
				$where[] = "t.status_open_incident = 1";
			} elseif (($filters['status'] ?? 'all') === 'closed') {
				$where[] = "t.status_open_incident = 0";
			}
			if (!empty($filters['id_type_incident'])) {
				$where[] = "t.id_type_incident_incident = ?";
				$params[] = (int)$filters['id_type_incident'];
			}
			if (!empty($filters['id_priority'])) {
				$where[] = "t.id_priority_incident = ?";
				$params[] = (int)$filters['id_priority'];
			}

			if (!empty($filters['asset_code'])) {
				$where[] = "a.code_inventory_asset LIKE ?";
				$params[] = '%' . $filters['asset_code'] . '%';
			}

			return ['WHERE ' . implode(' AND ', $where), $params];
		}

		private function incident_report_filter_labels(array $filters): array
		{
			$options = $this->report_filter_options();
			return [
				'Fecha inicio de incidencia' => !empty($filters['date_start']) ? date('d/m/Y', strtotime($filters['date_start'])) : 'Sin filtro',
				'Fecha fin de incidencia' => !empty($filters['date_end']) ? date('d/m/Y', strtotime($filters['date_end'])) : 'Sin filtro',
				'Estado' => $this->incident_report_status_label($filters['status'] ?? 'all'),
				'Tipo' => $this->find_report_label($options['types'], 'id_type_incident', 'name_type_incident', (int)($filters['id_type_incident'] ?? 0)),
				'Prioridad' => $this->find_report_label($options['priorities'], 'id_priority', 'name_priority', (int)($filters['id_priority'] ?? 0)),
				'Código de inventario' => !empty($filters['asset_code']) ? $filters['asset_code'] : 'Sin filtro',
				'Límite' => (string)max(1, min(150, (int)($filters['limit'] ?? 50))),
			];
		}

		private function incident_report_status_label(string $status): string
		{
			if ($status === 'open') {
				return 'Abiertas';
			}
			if ($status === 'closed') {
				return 'Cerradas';
			}
			return 'Todas';
		}

		private function find_report_label(array $rows, string $idField, string $labelField, int $id): string
		{
			if ($id <= 0) {
				return 'Todos';
			}
			foreach ($rows as $row) {
				if ((int)($row[$idField] ?? 0) === $id) {
					return (string)($row[$labelField] ?? 'No definido');
				}
			}
			return 'No definido';
		}

		public function derive_corrective(int $id_incident, int $id_user): bool
		{
            if (!$this->is_incident_open($id_incident)) { return false; }
			$incident = $this->show_specific_incident($id_incident);
			if (!$incident || empty($incident['id_asset_incident']) || intval($incident['status_open_incident']) !== 1) {
				return false;
			}
			if (in_array($incident['corrective_derivation_status_incident'] ?? 'none', ['pending', 'accepted'], true)) {
				return false;
			}
			$request = $this->update(
				"UPDATE incidents SET corrective_derivation_status_incident = 'pending', date_derived_corrective_incident = NOW(), date_updated_corrective_derivation_incident = NOW() WHERE id_incident = ? AND status_open_incident = 1",
				[$id_incident]
			);
			if ($request) {
				$this->log_corrective_derivation($id_incident, null, 'derived', 'Derivacion enviada a mantenimiento correctivo.', $id_user);
			}
			return (bool)$request;
		}

		public function cancel_corrective_derivation(int $id_incident, int $id_user): bool
		{
            if (!$this->is_incident_open($id_incident)) { return false; }
			$incident = $this->show_specific_incident($id_incident);
			if (!$incident || ($incident['corrective_derivation_status_incident'] ?? 'none') !== 'pending') {
				return false;
			}
			$request = $this->update(
				"UPDATE incidents SET corrective_derivation_status_incident = 'cancelled', date_updated_corrective_derivation_incident = NOW() WHERE id_incident = ? AND status_open_incident = 1",
				[$id_incident]
			);
			if ($request) {
				$this->log_corrective_derivation($id_incident, null, 'cancelled', 'Derivacion cancelada desde incidencia.', $id_user);
			}
			return (bool)$request;
		}

        public function close_incident(int $id_incident) { return $this->transition_incident($id_incident, false); }

		public function update_manual_closed_incident(int $id_incident, ?string $date_closed_incident, ?string $time_closed_incident){
            if (!$this->is_incident_open($id_incident)) { return false; }
			$this->int_id_incident = $id_incident;
			$sql = "UPDATE incidents SET date_closed_incident = ?, time_closed_incident = ? WHERE id_incident = ? AND status_open_incident = 1";
			$data = [
				$date_closed_incident,
				$time_closed_incident,
				$this->int_id_incident
			];
			$request = $this->update($sql, $data);
	        return $request;
		}

        public function reopen_incident(int $id_incident) { return $this->transition_incident($id_incident, true); }

		public function delete_incident(int $id_incident){
			$this->int_id_incident = $id_incident;
			$this->int_status_deleted_incident = 1;
			$sql = "UPDATE incidents SET status_deleted_incident = ? WHERE id_incident = ?";
			$data = [
				$this->int_status_deleted_incident,
				$this->int_id_incident
			];
			$request = $this->update($sql, $data);
			$return = $request;
	        return $return;
		}

	}
