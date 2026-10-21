<?php 
class MaintenancesPreventiveModel extends Mysql
{
		private static $schemaInitialized = false;
		private $int_id_movement_asset;
		private $str_name_movement_asset;
		private $int_status_active_movement_asset;
		private $int_status_deleted_movement_asset;

		public function __construct()
		{
			parent::__construct();
			$this->ensure_schema();
		}

		private function ensure_schema()
		{
			if (self::$schemaInitialized) {
				return;
			}

			$queries = [
				"CREATE TABLE IF NOT EXISTS parameters_maintenance_preventive_actions (
					id_parameter_maintenance_preventive_action INT NOT NULL AUTO_INCREMENT,
					name_parameter_maintenance_preventive_action VARCHAR(255) NOT NULL,
					status_active_parameter_maintenance_preventive_action TINYINT NOT NULL DEFAULT 1,
					status_deleted_parameter_maintenance_preventive_action TINYINT NOT NULL DEFAULT 0,
					date_created_parameter_maintenance_preventive_action TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
					date_updated_parameter_maintenance_preventive_action TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
					PRIMARY KEY (id_parameter_maintenance_preventive_action)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
				"ALTER TABLE history_maintenances_preventive ADD COLUMN time_performed_history_maintenance_preventive TIME DEFAULT NULL AFTER date_performed_history_maintenance_preventive",
				"UPDATE history_maintenances_preventive SET time_performed_history_maintenance_preventive = TIME(date_performed_history_maintenance_preventive) WHERE time_performed_history_maintenance_preventive IS NULL AND date_performed_history_maintenance_preventive IS NOT NULL",
				"ALTER TABLE history_maintenances_preventive MODIFY date_performed_history_maintenance_preventive DATE DEFAULT NULL",
				"CREATE TABLE IF NOT EXISTS attachments_history_maintenance_preventive (
					id_attachment_history_maintenance_preventive INT NOT NULL AUTO_INCREMENT,
					id_history_maintenance_preventive INT NOT NULL,
					type_attachment_history_maintenance_preventive VARCHAR(20) NOT NULL,
					route_attachment_history_maintenance_preventive VARCHAR(255) NOT NULL,
					name_attachment_history_maintenance_preventive VARCHAR(255) NOT NULL,
					status_active_attachment_history_maintenance_preventive TINYINT NOT NULL DEFAULT 1,
					status_deleted_attachment_history_maintenance_preventive TINYINT NOT NULL DEFAULT 0,
					date_created_attachment_history_maintenance_preventive TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
					date_updated_attachment_history_maintenance_preventive TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
					PRIMARY KEY (id_attachment_history_maintenance_preventive),
					KEY idx_ahmp_history (id_history_maintenance_preventive),
					CONSTRAINT fk_ahmp_history FOREIGN KEY (id_history_maintenance_preventive) REFERENCES history_maintenances_preventive(id_history_maintenance_preventive)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
			];

			foreach ($queries as $sql) {
				try {
					$this->update($sql);
				} catch (Throwable $e) {
					// Keep runtime flow stable if a schema sync query fails.
				}
			}

			self::$schemaInitialized = true;
		}

		private function preventive_schedule_status(array $row): array
		{
			$today = new DateTime(date('Y-m-d'));
			$scheduledDate = null;
			$daysDiff = null;

			if (!empty($row['date_scheduled_maintenance_preventive'])) {
				try {
					$scheduledDate = new DateTime(substr($row['date_scheduled_maintenance_preventive'], 0, 10));
					$daysDiff = (int) $today->diff($scheduledDate)->format('%r%a');
				} catch (Throwable $e) {
					$scheduledDate = null;
				}
			}

			$isInactive = intval($row['status_active_maintenance_preventive'] ?? 1) === 0;
			$lastPerformedDate = !empty($row['last_performed_date']) ? substr($row['last_performed_date'], 0, 10) : '';
			$createdAt = strtotime($row['date_created_maintenance_preventive'] ?? '');
			$updatedAt = strtotime($row['date_updated_maintenance_preventive'] ?? '');
			$historyCount = intval($row['history_count'] ?? 0);
			$wasReprogrammed = $historyCount === 0 && $createdAt && $updatedAt && ($updatedAt - $createdAt) > 60;

			if ($isInactive) {
				return ['key' => 'cancelled', 'label' => 'Anulado', 'class' => 'bg-secondary', 'days_diff' => $daysDiff];
			}

			if ($lastPerformedDate === date('Y-m-d')) {
				return ['key' => 'realized', 'label' => 'Realizado', 'class' => 'bg-success', 'days_diff' => $daysDiff];
			}

			if ($scheduledDate && $daysDiff < 0) {
				return ['key' => 'pending', 'label' => 'Pendiente', 'class' => 'bg-danger', 'days_diff' => $daysDiff];
			}

			if ($scheduledDate && $daysDiff <= 7) {
				return ['key' => 'expiring', 'label' => 'Proximo a vencer', 'class' => 'bg-warning text-dark', 'days_diff' => $daysDiff];
			}

			if ($wasReprogrammed) {
				return ['key' => 'reprogrammed', 'label' => 'Reprogramado', 'class' => 'bg-info text-dark', 'days_diff' => $daysDiff];
			}

			return ['key' => 'scheduled', 'label' => 'Programado', 'class' => 'bg-primary', 'days_diff' => $daysDiff];
		}

		public function show_maintenances_preventive()
		{
            $sql = "SELECT mp.id_maintenance_preventive, a.code_inventory_asset, ta.name_type_asset, 
					ca.name_category_asset, s.name_environment AS name_space, s.floor_environment AS floor_space, trm.name_type_responsible_maintenance, fm.name_frequency_maintenance, mp.date_scheduled_maintenance_preventive, 
					mp.date_next_maintenance_preventive, mp.status_active_maintenance_preventive, mp.date_created_maintenance_preventive, mp.date_updated_maintenance_preventive,
					COALESCE(hmp.history_count, 0) AS history_count, hmp.last_performed_date, hmp.last_scheduled_history_date
					FROM maintenances_preventive mp 
					INNER JOIN assets a ON mp.id_asset_maintenance_preventive = a.id_asset
					INNER JOIN types_asset ta ON a.id_type_asset_asset = ta.id_type_asset
					INNER JOIN categories_asset ca ON a.id_category_asset_asset = ca.id_category_asset

					INNER JOIN movements_asset ma ON ma.id_asset_movement_asset = a.id_asset
						AND ma.status_record_current_movement_asset = 1
						AND ma.status_deleted_movement_asset = 0

					INNER JOIN environments s ON ma.id_space_movement_asset = s.id_environment
					
					INNER JOIN frequencies_maintenance fm ON mp.id_frequency_maintenance_maintenance_preventive = fm.id_frequency_maintenance
					INNER JOIN types_responsible_maintenance trm ON mp.id_type_responsible_maintenance_maintenance_preventive = trm.id_type_responsible_maintenance

					LEFT JOIN maintenances_preventive_company mpc ON mp.id_maintenance_preventive = mpc.id_maintenance_preventive
					LEFT JOIN (
						SELECT id_maintenance_preventive,
							COUNT(*) AS history_count,
							MAX(date_performed_history_maintenance_preventive) AS last_performed_date,
							MAX(date_scheduled_history_maintenance_preventive) AS last_scheduled_history_date
						FROM history_maintenances_preventive
						GROUP BY id_maintenance_preventive
					) hmp ON hmp.id_maintenance_preventive = mp.id_maintenance_preventive

					WHERE mp.status_deleted_maintenance_preventive = 0 ORDER BY mp.date_scheduled_maintenance_preventive ASC";
			$request = $this->select_all($sql);
			foreach ($request as $index => $row) {
				$status = $this->preventive_schedule_status($row);
				$isCurrent = intval($row['status_active_maintenance_preventive'] ?? 1) === 1
					&& intval($row['history_count'] ?? 0) > 0
					&& !empty($row['last_performed_date'])
					&& !empty($row['date_scheduled_maintenance_preventive'])
					&& substr($row['last_performed_date'], 0, 10) <= date('Y-m-d')
					&& substr($row['date_scheduled_maintenance_preventive'], 0, 10) >= date('Y-m-d');
				$request[$index]['maintenance_schedule_status_key'] = $status['key'];
				$request[$index]['maintenance_schedule_status_label'] = $status['label'];
				$request[$index]['maintenance_schedule_status_class'] = $status['class'];
				$request[$index]['maintenance_schedule_days_diff'] = $status['days_diff'];
				$request[$index]['maintenance_current_status_key'] = $isCurrent ? 'current' : '';
			}
			return $request;
		}

		public function show_preventive_indicators()
		{
			$sql_performed = "SELECT COUNT(*) AS total
				FROM history_maintenances_preventive hmp
				INNER JOIN maintenances_preventive mp ON hmp.id_maintenance_preventive = mp.id_maintenance_preventive
				WHERE mp.status_deleted_maintenance_preventive = 0
				  AND hmp.id_status_maintenance = 1
				  AND hmp.date_performed_history_maintenance_preventive IS NOT NULL";
			$performed = $this->select($sql_performed);

			$sql_current = "SELECT COUNT(DISTINCT mp.id_asset_maintenance_preventive) AS total
				FROM maintenances_preventive mp
				INNER JOIN (
					SELECT id_maintenance_preventive,
						   MAX(date_performed_history_maintenance_preventive) AS last_performed_date
					FROM history_maintenances_preventive
					WHERE id_status_maintenance = 1
					  AND date_performed_history_maintenance_preventive IS NOT NULL
					GROUP BY id_maintenance_preventive
				) hmp ON hmp.id_maintenance_preventive = mp.id_maintenance_preventive
				WHERE mp.status_deleted_maintenance_preventive = 0
				  AND mp.status_active_maintenance_preventive = 1
				  AND hmp.last_performed_date <= CURDATE()
				  AND mp.date_scheduled_maintenance_preventive >= CURDATE()";
			$current = $this->select($sql_current);

			return [
				'total_performed' => intval($performed['total'] ?? 0),
				'assets_current' => intval($current['total'] ?? 0)
			];
		}

		public function show_history_maintenances_preventive(int $id_maintenance_preventive)
		{
			$this->int_id_maintenance_preventive = $id_maintenance_preventive;
			$sql = "SELECT hmp.id_history_maintenance_preventive, a.code_inventory_asset, ta.name_type_asset, 
					ca.name_category_asset, trm.name_type_responsible_maintenance, fm.name_frequency_maintenance, 
					hmp.date_scheduled_history_maintenance_preventive, hmp.date_performed_history_maintenance_preventive, hmp.time_performed_history_maintenance_preventive, sm.name_status_maintenance
					FROM history_maintenances_preventive hmp 
					INNER JOIN maintenances_preventive mp ON hmp.id_maintenance_preventive = mp.id_maintenance_preventive

					INNER JOIN assets a ON mp.id_asset_maintenance_preventive = a.id_asset
					INNER JOIN types_asset ta ON a.id_type_asset_asset = ta.id_type_asset
					INNER JOIN categories_asset ca ON a.id_category_asset_asset = ca.id_category_asset

					INNER JOIN frequencies_maintenance fm ON hmp.id_frequency_maintenance = fm.id_frequency_maintenance
					INNER JOIN states_maintenance sm ON hmp.id_status_maintenance = sm.id_status_maintenance
					INNER JOIN types_responsible_maintenance trm ON hmp.id_type_responsible_maintenance = trm.id_type_responsible_maintenance

					LEFT JOIN history_maintenances_preventive_company hmpc ON hmp.id_history_maintenance_preventive = hmpc.id_history_maintenance_preventive

					WHERE mp.id_maintenance_preventive = $this->int_id_maintenance_preventive";
			$request = $this->select_all($sql);
			return $request;
		}

		public function show_history_maintenance_preventive_one(int $id_history_maintenance_preventive)
		{
			$this->int_id_history_maintenance_preventive = $id_history_maintenance_preventive;
			$sql = "SELECT hmp.id_history_maintenance_preventive, hmp.id_status_maintenance, a.code_inventory_asset, ta.name_type_asset, 
					ca.name_category_asset, trm.name_type_responsible_maintenance, fm.name_frequency_maintenance, hmp.date_scheduled_history_maintenance_preventive, 
					hmp.date_performed_history_maintenance_preventive, hmp.time_performed_history_maintenance_preventive, hmp.action_history_maintenance_preventive, hmp.observation_history_maintenance_preventive
					FROM history_maintenances_preventive hmp 
					INNER JOIN maintenances_preventive mp ON hmp.id_maintenance_preventive = mp.id_maintenance_preventive
					INNER JOIN assets a ON mp.id_asset_maintenance_preventive = a.id_asset
					INNER JOIN types_asset ta ON a.id_type_asset_asset = ta.id_type_asset
					INNER JOIN categories_asset ca ON a.id_category_asset_asset = ca.id_category_asset
					INNER JOIN frequencies_maintenance fm ON hmp.id_frequency_maintenance = fm.id_frequency_maintenance
					INNER JOIN types_responsible_maintenance trm ON hmp.id_type_responsible_maintenance = trm.id_type_responsible_maintenance
					WHERE hmp.id_history_maintenance_preventive = $this->int_id_history_maintenance_preventive";
			$request = $this->select($sql);
			return $request;
		}

		public function update_history_maintenance_preventive(
			int $id_history_maintenance_preventive,
			int $id_status_maintenance,
			string $date_performed_history_maintenance_preventive,
			?string $time_performed_history_maintenance_preventive,
			string $action_history_maintenance_preventive,
			string $observation_history_maintenance_preventive
		){
			$history_context = $this->select(
				"SELECT hmp.id_maintenance_preventive, hmp.id_frequency_maintenance
				 FROM history_maintenances_preventive hmp
				 WHERE hmp.id_history_maintenance_preventive = ?",
				[$id_history_maintenance_preventive]
			);

			if (!$history_context) {
				return false;
			}

			$query_update = "UPDATE history_maintenances_preventive
							SET id_status_maintenance = ?,
								date_performed_history_maintenance_preventive = ?,
								time_performed_history_maintenance_preventive = ?,
								action_history_maintenance_preventive = ?,
								observation_history_maintenance_preventive = ?
							WHERE id_history_maintenance_preventive = ?";
			$request_update = $this->update($query_update, [
				$id_status_maintenance,
				$date_performed_history_maintenance_preventive,
				$time_performed_history_maintenance_preventive,
				$action_history_maintenance_preventive,
				$observation_history_maintenance_preventive,
				$id_history_maintenance_preventive
			]);

			if ($request_update === false) {
				return false;
			}

			return $this->recalculate_preventive_schedule_from_performed_date(
				intval($history_context['id_maintenance_preventive']),
				intval($history_context['id_frequency_maintenance']),
				$date_performed_history_maintenance_preventive
			);
		}

		private function recalculate_preventive_schedule_from_performed_date(int $id_maintenance_preventive, int $id_frequency_maintenance, string $date_performed_history_maintenance_preventive)
		{
			$date_scheduled_maintenance_preventive = calculate_next_maintenance_date(
				$date_performed_history_maintenance_preventive,
				$id_frequency_maintenance
			);
			$date_next_maintenance_preventive = calculate_next_maintenance_date(
				$date_scheduled_maintenance_preventive,
				$id_frequency_maintenance
			);

			$query_update_maintenance = "UPDATE maintenances_preventive
									 SET date_scheduled_maintenance_preventive = ?,
										 date_next_maintenance_preventive = ?
									 WHERE id_maintenance_preventive = ?";
			return $this->update($query_update_maintenance, [
				$date_scheduled_maintenance_preventive,
				$date_next_maintenance_preventive,
				$id_maintenance_preventive
			]);
		}
		public function show_maintenance_preventive(int $id_maintenance_preventive)
		{
			$this->int_id_maintenance_preventive = $id_maintenance_preventive;
			$sql = "SELECT a.id_asset, a.code_inventory_asset, a.descriptions_asset, a.observations_asset, ta.name_type_asset, 
					ca.name_category_asset, b.name_brand, coa.name_condition_asset, trm.id_type_responsible_maintenance, trm.name_type_responsible_maintenance,
					fm.id_frequency_maintenance, fm.name_frequency_maintenance, mp.date_scheduled_maintenance_preventive, 
					mp.date_next_maintenance_preventive, mp.description_maintenance_preventive, mp.observation_maintenance_preventive, mp.date_created_maintenance_preventive,
					c.id_company, c.name_company, c.ruc_company
					FROM maintenances_preventive mp 
					INNER JOIN assets a ON mp.id_asset_maintenance_preventive = a.id_asset
					INNER JOIN types_asset ta ON a.id_type_asset_asset = ta.id_type_asset
					INNER JOIN categories_asset ca ON a.id_category_asset_asset = ca.id_category_asset
					INNER JOIN brands b ON a.id_brand_asset = b.id_brand
					INNER JOIN conditions_asset coa ON a.id_condition_asset_asset = coa.id_condition_asset
					INNER JOIN frequencies_maintenance fm ON mp.id_frequency_maintenance_maintenance_preventive = fm.id_frequency_maintenance
					INNER JOIN types_responsible_maintenance trm ON mp.id_type_responsible_maintenance_maintenance_preventive = trm.id_type_responsible_maintenance
					LEFT JOIN maintenances_preventive_company mpc ON mp.id_maintenance_preventive = mpc.id_maintenance_preventive
					LEFT JOIN companies c ON mpc.id_company = c.id_company
					WHERE mp.id_maintenance_preventive = $this->int_id_maintenance_preventive AND mp.status_deleted_maintenance_preventive = 0";
			$request = $this->select($sql);
			return $request;
		}

		public function show_files(int $id_maintenance_preventive){
			$this->int_id_maintenance_preventive = $id_maintenance_preventive;
			$sql = "SELECT 	a.id_attachment_maintenance_preventive, 
							a.id_maintenance_preventive,
							a.route_attachment_maintenance_preventive,
							a.name_attachment_maintenance_preventive
					FROM attachments_maintenance_preventive a 
					INNER JOIN maintenances_preventive mp ON a.id_maintenance_preventive = mp.id_maintenance_preventive
					WHERE a.id_maintenance_preventive = ?";
			$data = [
				$this->int_id_maintenance_preventive
			];
			$request = $this->select_all($sql, $data);
			return $request;
		}

		public function insert_maintenance_preventive(int $id_asset, string $date_scheduled_maintenance_preventive, string $date_next_maintenance_preventive, int $id_frequency_maintenance, int $id_type_responsible_maintenance, string $description_maintenance_preventive, string $observation_maintenance_preventive){
			$this->int_id_asset = $id_asset;
			$this->str_date_scheduled_maintenance_preventive = $date_scheduled_maintenance_preventive;
			$this->str_date_next_maintenance_preventive = $date_next_maintenance_preventive;
			$this->int_id_frequency_maintenance = $id_frequency_maintenance;
			$this->int_id_type_responsible_maintenance = $id_type_responsible_maintenance;
			$this->str_description_maintenance_preventive = $description_maintenance_preventive;
			$this->str_observation_maintenance_preventive = $observation_maintenance_preventive;
			$this->int_status_maintenance_asset = 1;
			$query_insert = "INSERT INTO maintenances_preventive(id_asset_maintenance_preventive, id_frequency_maintenance_maintenance_preventive, id_type_responsible_maintenance_maintenance_preventive, date_scheduled_maintenance_preventive, date_next_maintenance_preventive, description_maintenance_preventive, observation_maintenance_preventive) VALUES(?,?,?,?,?,?,?)";
			$data = [
				$this->int_id_asset,
				$this->int_id_frequency_maintenance,
				$this->int_id_type_responsible_maintenance,
				$this->str_date_scheduled_maintenance_preventive,
				$this->str_date_next_maintenance_preventive,
				$this->str_description_maintenance_preventive,
				$this->str_observation_maintenance_preventive
			];
			$request_insert = $this->insert($query_insert, $data);
			$sql_update_status = "UPDATE assets SET status_maintenance_asset = ? WHERE id_asset = ?";
			$data = [
				$this->int_status_maintenance_asset,
                $this->int_id_asset
			];
			$request_update = $this->update($sql_update_status, $data);
	        return $request_insert;
		}

		public function update_maintenance_preventive(int $id_maintenance_preventive, int $id_asset, string $date_scheduled_maintenance_preventive, string $date_next_maintenance_preventive, int $id_frequency_maintenance, int $id_type_responsible_maintenance, string $description_maintenance_preventive, string $observation_maintenance_preventive){
			$this->int_id_maintenance_preventive = $id_maintenance_preventive;
			$this->int_id_asset = $id_asset;
			$this->str_date_scheduled_maintenance_preventive = $date_scheduled_maintenance_preventive;
			$this->str_date_next_maintenance_preventive = $date_next_maintenance_preventive;
			$this->int_id_frequency_maintenance = $id_frequency_maintenance;
			$this->int_id_type_responsible_maintenance = $id_type_responsible_maintenance;
			$this->str_description_maintenance_preventive = $description_maintenance_preventive;
			$this->str_observation_maintenance_preventive = $observation_maintenance_preventive;
			
			// Obtener activo actual
			$sql_query = "SELECT id_asset_maintenance_preventive FROM maintenances_preventive mp
						WHERE id_maintenance_preventive = $this->int_id_maintenance_preventive";
			$row = $this->select($sql_query);
			$old_asset_id = $row["id_asset_maintenance_preventive"];

			// Actualizar el registro
			$sql_update = "UPDATE maintenances_preventive SET 
					id_asset_maintenance_preventive = ?,
					id_frequency_maintenance_maintenance_preventive = ?, 
					id_type_responsible_maintenance_maintenance_preventive = ?, 
					date_scheduled_maintenance_preventive = ?,
					date_next_maintenance_preventive = ?,
					description_maintenance_preventive = ?,
					observation_maintenance_preventive = ? 
					WHERE id_maintenance_preventive = ?";
			$data = [
				$this->int_id_asset,
				$this->int_id_frequency_maintenance,
				$this->int_id_type_responsible_maintenance,
				$this->str_date_scheduled_maintenance_preventive,
				$this->str_date_next_maintenance_preventive,
				$this->str_description_maintenance_preventive,
				$this->str_observation_maintenance_preventive,
				$this->int_id_maintenance_preventive
			];
			$request_update = $this->update($sql_update, $data);

			// Actualizar estados
			if ($old_asset_id !== $id_asset) {

				// Desactivar anterior
				$this->int_status_maintenance_asset = 0;
				$this->int_old_asset_id = $old_asset_id;
				$sql_update_status = "UPDATE assets 
						SET status_maintenance_asset = ?
						WHERE id_asset = ?";
				$data = [
					$this->int_status_maintenance_asset,
					$this->int_old_asset_id
				];
				$request_update_status = $this->update($sql_update_status, $data);

				// Activar nuevo
				$this->int_status_maintenance_asset = 0;
				$this->int_old_asset_id = $old_asset_id;
				$sql_update_status = "UPDATE assets 
						SET status_maintenance_asset = ?
						WHERE id_asset = ?";
				$data = [
					$this->int_status_maintenance_asset,
					$this->int_old_asset_id
				];

				$request_update_status = $this->update($sql_update_status, $data);
				
				if ($request_update_status) {
					$this->int_status_maintenance_asset = 1;
					$sql_update_status = "UPDATE assets 
							SET status_maintenance_asset = ?
							WHERE id_asset = ?";
					$data = [
						$this->int_status_maintenance_asset,
						$this->int_id_asset
					];

					$request_update_status = $this->update($sql_update_status, $data);
				}
			}	
			
			return $request_update;
		}

		public function execute_maintenance_preventive(
			int $id_maintenance_preventive, 
			int $id_status_maintenance, 
			string $action_history_maintenance_preventive, 
			string $observation_history_maintenance_preventive,
			string $date_performed_history_maintenance_preventive,
			string $time_performed_history_maintenance_preventive
			){
			$this->int_id_maintenance_preventive = $id_maintenance_preventive;
			$this->int_id_status_maintenance = $id_status_maintenance;
			$this->str_action_history_maintenance_preventivetive = $action_history_maintenance_preventive;
			$this->str_observation_history_maintenance_preventive = $observation_history_maintenance_preventive;
			$this->src_date_performed_history_maintenance_preventive = $date_performed_history_maintenance_preventive;
			$this->src_time_performed_history_maintenance_preventive = $time_performed_history_maintenance_preventive;

			$sql_search = "SELECT id_frequency_maintenance_maintenance_preventive, id_type_responsible_maintenance_maintenance_preventive, 
							date_scheduled_maintenance_preventive
							FROM maintenances_preventive 
							WHERE id_maintenance_preventive = $this->int_id_maintenance_preventive";
			$request_search = $this->select($sql_search);

			$this->int_id_frequency_maintenance_maintenance_preventive = $request_search['id_frequency_maintenance_maintenance_preventive'];
			$this->int_id_type_responsible_maintenance_maintenance_preventive = $request_search['id_type_responsible_maintenance_maintenance_preventive'];
			$this->src_date_scheduled_maintenance_preventive = $request_search['date_scheduled_maintenance_preventive'];

			$query_insert = "INSERT INTO history_maintenances_preventive(
										 id_maintenance_preventive, 
										 id_frequency_maintenance, 
										 id_status_maintenance, 
										 id_type_responsible_maintenance,
										 date_scheduled_history_maintenance_preventive,
										 date_performed_history_maintenance_preventive,
										 time_performed_history_maintenance_preventive,
										 action_history_maintenance_preventive,
										 observation_history_maintenance_preventive) 
							VALUES(?,?,?,?,?,?,?,?,?)";
			$data = [
				$this->int_id_maintenance_preventive,
				$this->int_id_frequency_maintenance_maintenance_preventive,
				$this->int_id_status_maintenance,
				$this->int_id_type_responsible_maintenance_maintenance_preventive, 
				$this->src_date_scheduled_maintenance_preventive,
				$this->src_date_performed_history_maintenance_preventive,
				$this->src_time_performed_history_maintenance_preventive,
				$this->str_action_history_maintenance_preventivetive,
				$this->str_observation_history_maintenance_preventive
			];
			$request_insert = $this->insert($query_insert, $data);
			if (!$request_insert) {
				return 0;
			}

			$this->int_history_maintenance_preventive = intval($request_insert);


			$sql_search_two = "SELECT id_company FROM maintenances_preventive_company  
								WHERE id_maintenance_preventive = $this->int_id_maintenance_preventive";
			$request_search_two = $this->select($sql_search_two);

			if ($request_search_two) {

				$this->int_id_company = $request_search_two['id_company'];

				$query_insert_two = "INSERT INTO history_maintenances_preventive_company(
											id_history_maintenance_preventive, 
											id_company) 
									VALUES(?,?)";
				$data = [
					$this->int_history_maintenance_preventive,
					$this->int_id_company
				];
				$request_insert_two = $this->insert($query_insert_two, $data);
			}
			
			$performed_date = $this->src_date_performed_history_maintenance_preventive;
			$frequency = $this->int_id_frequency_maintenance_maintenance_preventive;
			$str_date_next_maintenance_preventive = calculate_next_maintenance_date($performed_date, $frequency);
			$this->src_new_date_scheduled_maintenance_preventive = $str_date_next_maintenance_preventive;
			$this->str_new_date_next_maintenance_preventive = calculate_next_maintenance_date($str_date_next_maintenance_preventive, $frequency);;

			$sql_update = "UPDATE maintenances_preventive 
					SET date_scheduled_maintenance_preventive = ?,
						date_next_maintenance_preventive = ?
					WHERE id_maintenance_preventive = $this->int_id_maintenance_preventive";		
			$data = [
				$this->src_new_date_scheduled_maintenance_preventive,
				$this->str_new_date_next_maintenance_preventive
			];
			$request_update = $this->update($sql_update, $data);
			if (!$request_update) {
				return 0;
			}

			return $this->int_history_maintenance_preventive;
		}

		public function show_history_files(int $id_history_maintenance_preventive){
			$sql = "SELECT id_attachment_history_maintenance_preventive,
						   id_history_maintenance_preventive,
						   type_attachment_history_maintenance_preventive,
						   route_attachment_history_maintenance_preventive,
						   name_attachment_history_maintenance_preventive
					FROM attachments_history_maintenance_preventive
					WHERE id_history_maintenance_preventive = ?
					  AND status_deleted_attachment_history_maintenance_preventive = 0
					ORDER BY id_attachment_history_maintenance_preventive ASC";
			return $this->select_all($sql, [$id_history_maintenance_preventive]);
		}

		public function show_history_file_by_id(int $id_attachment_history_maintenance_preventive, int $id_history_maintenance_preventive){
			$sql = "SELECT id_attachment_history_maintenance_preventive,
						   id_history_maintenance_preventive,
						   route_attachment_history_maintenance_preventive,
						   name_attachment_history_maintenance_preventive
					FROM attachments_history_maintenance_preventive
					WHERE id_attachment_history_maintenance_preventive = ?
					  AND id_history_maintenance_preventive = ?
					  AND status_deleted_attachment_history_maintenance_preventive = 0";
			return $this->select($sql, [$id_attachment_history_maintenance_preventive, $id_history_maintenance_preventive]);
		}

		public function delete_attachment_history_maintenance_preventive(int $id_attachment_history_maintenance_preventive){
			$query = "UPDATE attachments_history_maintenance_preventive
					SET status_deleted_attachment_history_maintenance_preventive = 1
					WHERE id_attachment_history_maintenance_preventive = ?";
			return $this->update($query, [$id_attachment_history_maintenance_preventive]);
		}

		public function insert_attachment_history_maintenance_preventive(int $id_history_maintenance_preventive, string $type, string $file_path, string $file_name){
			$query_insert = "INSERT INTO attachments_history_maintenance_preventive(
								id_history_maintenance_preventive,
								type_attachment_history_maintenance_preventive,
								route_attachment_history_maintenance_preventive,
								name_attachment_history_maintenance_preventive
							) VALUES(?,?,?,?)";
			return $this->insert($query_insert, [
				$id_history_maintenance_preventive,
				$type,
				$file_path,
				$file_name
			]);
		}

		public function relate_maintenance_preventive_company(int $id_maintenance_preventive, int $id_company){
			$this->int_id_maintenance_preventive = $id_maintenance_preventive;
			$this->int_id_company = $id_company;

			$sql_search = "SELECT id_maintenance_preventive_company 
							FROM maintenances_preventive_company
							WHERE id_maintenance_preventive = $this->int_id_maintenance_preventive";
			$request_search = $this->select($sql_search);

			if ($request_search) {
				$this->int_id_maintenance_preventive_company = $request_search['id_maintenance_preventive_company'];
				$sql_update = "UPDATE maintenances_preventive_company SET id_maintenance_preventive = ?, id_company = ? WHERE id_maintenance_preventive_company = ?";
				$data = [
					$this->int_id_maintenance_preventive,
					$this->int_id_company,
					$this->int_id_maintenance_preventive_company
				];
				$request = $this->update($sql_update, $data);
			} else {
					$query_insert = "INSERT INTO maintenances_preventive_company(id_maintenance_preventive, id_company) VALUES(?,?)";
					$data = [
						$this->int_id_maintenance_preventive,
						$this->int_id_company
					];
					$request = $this->insert($query_insert, $data);				
			}
	        return $request;
		}

		public function insert_attachment_maintenance_preventive(int $id_maintenance_preventive, string $file_path, string $file_name) {
			$query_insert = "INSERT INTO attachments_maintenance_preventive(id_maintenance_preventive, route_attachment_maintenance_preventive, name_attachment_maintenance_preventive) VALUES(?, ?, ?)";
			$data = [
				$id_maintenance_preventive,
                $file_path,
				$file_name
			];
            $request_insert = $this->insert($query_insert, $data);
            return $request_insert;
        }

		public function delete_attachment_maintenance_preventive(int $id_attachment_maintenance_preventive){
			$this->int_id_attachment_maintenance_preventive = $id_attachment_maintenance_preventive;
			$query  = "DELETE FROM attachments_maintenance_preventive WHERE id_attachment_maintenance_preventive = ?";
			$data = [
				$this->int_id_attachment_maintenance_preventive
			];
	        $request = $this->delete($query, $data);
	        return $request;
		}

		public function delete_movement_asset(int $id_movement_asset){
			$this->int_id_movement_asset = $id_movement_asset;
			$this->int_status_deleted_movement_asset = 1;
			$sql = "UPDATE movements_asset SET status_deleted_movement_asset = ? WHERE id_movement_asset = $this->int_id_movement_asset";		
			$data = [
				$this->int_status_deleted_movement_asset
			];
			$request = $this->update($sql, $data);
			$return = $request;
	        return $return;
		}
	}

