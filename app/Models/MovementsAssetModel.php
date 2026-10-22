<?php
require_once __DIR__ . '/MovementRules.php';
	class MovementsAssetModel extends Mysql
	{
        private $int_id_asset;
        private $int_id_attachment_movement_asset;
        private $int_status_deleted_attachment_movement_asset;
        private $str_name_attachment_movement_asset;
        private $str_route_attachment_movement_asset;
		private $int_id_movement_asset;
		private $str_name_movement_asset;
		private $int_status_active_movement_asset;
		private $int_status_deleted_movement_asset;

		public function __construct()
		{
			parent::__construct();
			$this->ensure_manual_responsible_column();
			$this->ensure_time_event_column();
		}

		private function ensure_manual_responsible_column(): void
		{
			$column = $this->select("SHOW COLUMNS FROM movements_asset LIKE 'manual_responsible_movement_asset'");
			if (!$column) {
				$this->update("ALTER TABLE movements_asset ADD COLUMN manual_responsible_movement_asset VARCHAR(160) DEFAULT NULL AFTER id_responsible_employee_movement_asset");
			}
		}

		private function ensure_time_event_column(): void
		{
			$column = $this->select("SHOW COLUMNS FROM movements_asset LIKE 'time_event_movement_asset'");
			if (!$column) {
				$this->update("ALTER TABLE movements_asset ADD COLUMN time_event_movement_asset TIME DEFAULT NULL AFTER date_event_movement_asset");
			}
		}

        public function show_movements_asset()
        {
            return $this->asset_states(true);
        }

        public function pending_assets(): array
        {
            return $this->asset_states(false, true);
        }

        public function asset_states(bool $currentOnly = true, bool $pendingOnly = false): array
        {
            $sql = "SELECT r.id_asset_movement_register,a.id_asset,a.id_type_asset_asset,a.id_category_asset_asset,a.id_condition_asset_asset,a.code_inventory_asset,a.model_asset,a.serial_number_asset,a.descriptions_asset,a.status_active_asset,a.date_created_asset,
                ta.name_type_asset,ca.name_category_asset,b.name_brand,co.name_condition_asset,
                ma.id_movement_asset,ma.id_type_movement_asset,ma.id_space_movement_asset,ma.id_responsible_employee_movement_asset,
                ma.date_event_movement_asset,ma.time_event_movement_asset,ma.expected_return_date_movement_asset,ma.loan_return_indefinite_movement_asset,
                ma.loan_recipient_name_movement_asset,ma.manual_responsible_movement_asset,ma.status_process_movement_asset,
                ma.id_floor_plan_movement_asset AS id_floor_plan,ma.coordinate_x_movement_asset,ma.coordinate_y_movement_asset,
                tm.name_type_movement,s.name_environment AS name_space,s.floor_environment AS floor_space,s.id_campus_environment,
                c.name_campus,fp.name_floor_plan,fp.current_file_floor_plan,fp.current_image_floor_plan,
                TRIM(CONCAT_WS(' ',e.first_name_employee,e.paternal_last_name_employee,e.maternal_last_name_employee)) AS responsible_employee
                FROM assets a
                INNER JOIN types_asset ta ON ta.id_type_asset=a.id_type_asset_asset
                INNER JOIN categories_asset ca ON ca.id_category_asset=a.id_category_asset_asset
                INNER JOIN brands b ON b.id_brand=a.id_brand_asset
                INNER JOIN conditions_asset co ON co.id_condition_asset=a.id_condition_asset_asset
                LEFT JOIN asset_movement_registers r ON r.id_asset=a.id_asset
                LEFT JOIN movements_asset ma ON ma.id_asset_movement_asset=a.id_asset AND ma.status_record_current_movement_asset=1 AND ma.status_deleted_movement_asset=0 AND ma.status_annulled_movement_asset=0
                LEFT JOIN types_movement tm ON tm.id_type_movement=ma.id_type_movement_asset
                LEFT JOIN environments s ON s.id_environment=ma.id_space_movement_asset
                LEFT JOIN campuses c ON c.id_campus=s.id_campus_environment
                LEFT JOIN floor_plans fp ON fp.id_floor_plan=ma.id_floor_plan_movement_asset
                LEFT JOIN employees e ON e.id_employee=ma.id_responsible_employee_movement_asset
                WHERE a.status_deleted_asset=0";
            if ($currentOnly) $sql .= ' AND ma.id_movement_asset IS NOT NULL';
            if ($pendingOnly) $sql .= ' AND ma.id_movement_asset IS NULL AND a.status_active_asset=1';
            $rows = $this->select_all($sql . ' ORDER BY a.code_inventory_asset');
            foreach ($rows as &$row) {
                $row['situation'] = MovementRules::situation($row['id_type_movement_asset'] ? (int)$row['id_type_movement_asset'] : null);
                $row['responsible_receiver'] = trim($row['loan_recipient_name_movement_asset'] ?? '') ?: (trim($row['manual_responsible_movement_asset'] ?? '') ?: ($row['responsible_employee'] ?: 'Sin responsable'));
                $row['return_status'] = (int)$row['id_type_movement_asset'] !== 5 ? 'No aplica' : ((int)$row['loan_return_indefinite_movement_asset'] === 1 ? 'Indeterminado' : (($row['expected_return_date_movement_asset'] ?? '') < date('Y-m-d') ? 'Vencido' : 'Pendiente'));
                $row['options'] = '';
            }
            return $rows;
        }
        public function asset_context(int $id): array|false
        {
            require_once __DIR__ . '/AssetsModel.php';
            $asset = (new AssetsModel())->show_asset($id);
            if (!$asset || (int)$asset['status_active_asset'] !== 1) return false;
            $current = $this->get_current_movement_by_asset($id);
            $asset['current'] = $current ? $this->show_movement_asset((int)$current['id_movement_asset']) : null;
            if ($current && (int)$current['id_type_movement_asset'] === 5) {
                $previous = $this->select('SELECT id_movement_asset FROM movements_asset WHERE id_asset_movement_asset=? AND id_movement_asset<? AND status_deleted_movement_asset=0 AND status_annulled_movement_asset=0 ORDER BY id_movement_asset DESC LIMIT 1', [$id,$current['id_movement_asset']]);
                $asset['return_destination'] = $previous ? $this->show_movement_asset((int)$previous['id_movement_asset']) : null;
            }
            $asset['allowed_types'] = MovementRules::allowedTypes($current ? (int)$current['id_type_movement_asset'] : null);
            return $asset;
        }

		public function show_history_movement_asset(int $id_asset)
		{
			$this->int_id_asset = $id_asset;
            $sql = "SELECT ma.id_movement_asset, ma.status_record_current_movement_asset, ma.status_annulled_movement_asset, ma.date_created_movement_asset, ma.date_event_movement_asset, ma.time_event_movement_asset, ma.expected_return_date_movement_asset,
					ma.document_code_movement_asset, ma.status_process_movement_asset, ma.loan_recipient_type_movement_asset,
					ma.loan_recipient_name_movement_asset, ma.loan_recipient_document_movement_asset, ma.loan_return_indefinite_movement_asset,
					ma.manual_responsible_movement_asset, ma.id_floor_plan_movement_asset, ma.coordinate_x_movement_asset, ma.coordinate_y_movement_asset,
					a.id_asset, a.code_inventory_asset, ta.name_type_asset, 
					ca.name_category_asset, tm.name_type_movement, s.name_environment AS name_space, s.floor_environment AS floor_space,
					fp.name_floor_plan,
					TRIM(CONCAT_WS(' ', NULLIF(CASE e.preferred_name_employee WHEN 2 THEN COALESCE(NULLIF(e.middle_name_employee, ''), e.first_name_employee) ELSE e.first_name_employee END, ''), NULLIF(e.paternal_last_name_employee, ''), NULLIF(e.maternal_last_name_employee, ''))) AS responsible_employee
					FROM movements_asset ma 
					INNER JOIN assets a ON ma.id_asset_movement_asset = a.id_asset
					INNER JOIN types_asset ta ON a.id_type_asset_asset = ta.id_type_asset
					INNER JOIN categories_asset ca ON a.id_category_asset_asset = ca.id_category_asset
					INNER JOIN types_movement tm ON ma.id_type_movement_asset = tm.id_type_movement
					INNER JOIN environments s ON ma.id_space_movement_asset = s.id_environment
					LEFT JOIN floor_plans fp ON fp.id_floor_plan = ma.id_floor_plan_movement_asset
					LEFT JOIN employees e ON ma.id_responsible_employee_movement_asset = e.id_employee
					WHERE a.id_asset = $this->int_id_asset AND ma.status_deleted_movement_asset = 0
					ORDER BY COALESCE(ma.date_event_movement_asset, ma.date_created_movement_asset) DESC, ma.id_movement_asset DESC";
			$request = $this->select_all($sql);
			return $request;
		}

		public function show_movement_asset(int $id_movement_asset)
		{
			$this->int_id_movement_asset = $id_movement_asset;
			$sql = "SELECT ma.id_movement_asset, ma.observations_movement_asset, ma.date_created_movement_asset, ma.date_updated_movement_asset, ma.date_event_movement_asset, ma.time_event_movement_asset,
					ma.id_responsible_employee_movement_asset, ma.expected_return_date_movement_asset, ma.document_code_movement_asset, ma.status_process_movement_asset,
					ma.status_record_current_movement_asset, ma.manual_responsible_movement_asset,
					ma.id_floor_plan_movement_asset, ma.coordinate_x_movement_asset, ma.coordinate_y_movement_asset,
					ma.loan_recipient_type_movement_asset, ma.loan_recipient_name_movement_asset, ma.loan_recipient_document_movement_asset, ma.loan_return_indefinite_movement_asset,
					a.id_asset, a.code_inventory_asset, a.model_asset, a.serial_number_asset, a.entry_date_asset, a.acquisition_date_asset,
					a.descriptions_asset, a.observations_asset, a.status_movement_asset, a.status_maintenance_asset, ta.name_type_asset, 
					ca.name_category_asset, b.name_brand, coa.name_condition_asset, tm.id_type_movement, tm.name_type_movement, 
					s.id_environment AS id_space, s.name_environment AS name_space, s.floor_environment AS floor_space, s.use_environment, cp.id_campus, cp.name_campus,
					fp.name_floor_plan, fp.current_file_floor_plan, fp.current_image_floor_plan,
					TRIM(CONCAT_WS(' ', NULLIF(CASE e.preferred_name_employee WHEN 2 THEN COALESCE(NULLIF(e.middle_name_employee, ''), e.first_name_employee) ELSE e.first_name_employee END, ''), NULLIF(e.paternal_last_name_employee, ''), NULLIF(e.maternal_last_name_employee, ''))) AS responsible_employee
					FROM movements_asset ma 
					INNER JOIN assets a ON ma.id_asset_movement_asset = a.id_asset
					INNER JOIN types_asset ta ON a.id_type_asset_asset = ta.id_type_asset
					INNER JOIN brands b ON a.id_brand_asset = b.id_brand
					INNER JOIN conditions_asset coa ON a.id_condition_asset_asset = coa.id_condition_asset
					INNER JOIN categories_asset ca ON a.id_category_asset_asset = ca.id_category_asset
					INNER JOIN types_movement tm ON ma.id_type_movement_asset = tm.id_type_movement
					INNER JOIN environments s ON ma.id_space_movement_asset = s.id_environment
					INNER JOIN campuses cp ON s.id_campus_environment = cp.id_campus
					LEFT JOIN floor_plans fp ON fp.id_floor_plan = ma.id_floor_plan_movement_asset
					LEFT JOIN employees e ON ma.id_responsible_employee_movement_asset = e.id_employee
					WHERE ma.id_movement_asset = $this->int_id_movement_asset AND ma.status_deleted_movement_asset = 0";
			$request = $this->select($sql);
			return $request;
		}

		public function show_files(int $id_movement_asset)
		{
			$this->int_id_movement_asset = $id_movement_asset;
			$sql = "SELECT id_attachment_movement_asset, route_attachment_movement_asset, name_attachment_movement_asset
					FROM attachments_movement_asset
					WHERE id_movement_asset = $this->int_id_movement_asset
					AND status_deleted_attachment_movement_asset = 0";
			$request = $this->select_all($sql);
			return $request;
		}

		public function insert_attachment_movement_asset(int $id_movement_asset, string $route_attachment_movement_asset, string $name_attachment_movement_asset)
		{
			$this->int_id_movement_asset = $id_movement_asset;
			$this->str_route_attachment_movement_asset = $route_attachment_movement_asset;
			$this->str_name_attachment_movement_asset = $name_attachment_movement_asset;
			$query_insert = "INSERT INTO attachments_movement_asset(id_movement_asset, route_attachment_movement_asset, name_attachment_movement_asset) VALUES(?,?,?)";
			$data = [
				$this->int_id_movement_asset,
				$this->str_route_attachment_movement_asset,
				$this->str_name_attachment_movement_asset
			];
			$request = $this->insert($query_insert, $data);
			return $request;
		}

		public function delete_attachment_movement_asset(int $id_attachment_movement_asset)
		{
			$this->int_id_attachment_movement_asset = $id_attachment_movement_asset;
			$this->int_status_deleted_attachment_movement_asset = 1;
			$sql = "UPDATE attachments_movement_asset
					SET status_deleted_attachment_movement_asset = ?
					WHERE id_attachment_movement_asset = $this->int_id_attachment_movement_asset";
			$data = [
				$this->int_status_deleted_attachment_movement_asset
			];
			$request = $this->update($sql, $data);
			return $request;
		}

		public function get_current_movement_by_asset(int $id_asset, bool $lock = false)
		{
			$this->int_id_asset = $id_asset;
			$sql = "SELECT id_movement_asset, id_asset_movement_asset, id_space_movement_asset, id_type_movement_asset,
					id_responsible_employee_movement_asset, date_event_movement_asset, time_event_movement_asset, expected_return_date_movement_asset, status_process_movement_asset,
					id_floor_plan_movement_asset, coordinate_x_movement_asset, coordinate_y_movement_asset,
					loan_recipient_type_movement_asset, loan_recipient_name_movement_asset, loan_recipient_document_movement_asset, loan_return_indefinite_movement_asset,
					manual_responsible_movement_asset
					FROM movements_asset
					WHERE id_asset_movement_asset = $this->int_id_asset
					AND status_deleted_movement_asset = 0
					AND status_record_current_movement_asset = 1 AND status_annulled_movement_asset = 0
					LIMIT 1";
			$request = $this->select($sql . ($lock ? ' FOR UPDATE' : ''));
			return $request;
		}

		public function insert_movement_asset(int $id_asset_movement_asset, int $id_space_movement_asset, ?int $id_floor_plan_movement_asset, ?float $coordinate_x_movement_asset, ?float $coordinate_y_movement_asset, int $id_type_movement_asset, ?int $id_responsible_employee_movement_asset, ?string $date_event_movement_asset, ?string $time_event_movement_asset, ?string $expected_return_date_movement_asset, ?string $document_code_movement_asset, string $status_process_movement_asset, ?string $loan_recipient_type_movement_asset, ?string $loan_recipient_name_movement_asset, ?string $loan_recipient_document_movement_asset, int $loan_return_indefinite_movement_asset, ?string $manual_responsible_movement_asset, string $observations_movement_asset){
            return $this->recordMovement(func_get_args(), 0);
        }

		public function move_movement_asset(int $id_movement_asset, int $id_asset_movement_asset, int $id_space_movement_asset, ?int $id_floor_plan_movement_asset, ?float $coordinate_x_movement_asset, ?float $coordinate_y_movement_asset, int $id_type_movement_asset, ?int $id_responsible_employee_movement_asset, ?string $date_event_movement_asset, ?string $time_event_movement_asset, ?string $expected_return_date_movement_asset, ?string $document_code_movement_asset, string $status_process_movement_asset, ?string $loan_recipient_type_movement_asset, ?string $loan_recipient_name_movement_asset, ?string $loan_recipient_document_movement_asset, int $loan_return_indefinite_movement_asset, ?string $manual_responsible_movement_asset, string $observations_movement_asset){
            $args = func_get_args(); $current = (int)array_shift($args); return $this->recordMovement($args, $current);
        }

		public function update_movement_asset(int $id_movement_asset, int $id_asset_movement_asset, int $id_space_movement_asset, ?int $id_floor_plan_movement_asset, ?float $coordinate_x_movement_asset, ?float $coordinate_y_movement_asset, int $id_type_movement_asset, ?int $id_responsible_employee_movement_asset, ?string $date_event_movement_asset, ?string $time_event_movement_asset, ?string $expected_return_date_movement_asset, ?string $document_code_movement_asset, string $status_process_movement_asset, ?string $loan_recipient_type_movement_asset, ?string $loan_recipient_name_movement_asset, ?string $loan_recipient_document_movement_asset, int $loan_return_indefinite_movement_asset, ?string $manual_responsible_movement_asset, string $observations_movement_asset, string $reason = '', int $actor = 0){
            $args = func_get_args(); $id = (int)array_shift($args); $data = array_slice($args,0,18);
            return $this->atomicMovement(function () use ($id,$data,$reason,$actor) {
                $this->lockAsset((int)$data[0]);
                $before = $this->select('SELECT * FROM movements_asset WHERE id_movement_asset=? AND status_deleted_movement_asset=0 AND status_annulled_movement_asset=0', [$id]);
                $current = $this->get_current_movement_by_asset((int)$data[0], true);
                if (!$before || !$current || (int)$current['id_movement_asset'] !== $id || (int)$before['id_asset_movement_asset'] !== (int)$data[0]) throw new DomainException('Solo se puede corregir el movimiento vigente del mismo activo.');
                $this->validateMovementData($data);
                $set = implode(',',array_map(fn($column) => $column . '=?', $this->movementColumns()));
                $this->update("UPDATE movements_asset SET $set WHERE id_movement_asset=?", [...$data,$id]);
                $this->logMovementChange($id,'corrected',$reason,$actor,$before,array_combine($this->movementColumns(),$data));
                return true;
            });
        }

		public function delete_movement_asset(int $id_movement_asset){
            throw new DomainException('Usa Anular movimiento e indica el motivo; el historial se conserva.');
        }

        private function atomicMovement(callable $operation)
        {
            $pdo = $this->connection();
            $outer = $pdo->inTransaction();
            if ($outer) $pdo->exec('SAVEPOINT movement_write'); else $pdo->beginTransaction();
            try {
                $result = $operation();
                if ($outer) $pdo->exec('RELEASE SAVEPOINT movement_write'); else $pdo->commit();
                return $result;
            } catch (Throwable $e) {
                if ($outer) $pdo->exec('ROLLBACK TO SAVEPOINT movement_write'); elseif ($pdo->inTransaction()) $pdo->rollBack();
                if ($e instanceof DomainException) throw $e;
                throw new DomainException('No se pudo guardar el movimiento. No se aplicaron cambios.', 0, $e);
            }
        }
        private function lockAsset(int $id): array
        {
            $asset = $this->select('SELECT * FROM assets WHERE id_asset=? AND status_deleted_asset=0 FOR UPDATE', [$id]);
            if (!$asset || (int)$asset['status_active_asset'] !== 1) throw new DomainException('El activo no está disponible para registrar movimientos.');
            return $asset;
        }
        private function movementColumns(): array
        {
            return ['id_asset_movement_asset','id_space_movement_asset','id_floor_plan_movement_asset','coordinate_x_movement_asset','coordinate_y_movement_asset','id_type_movement_asset','id_responsible_employee_movement_asset','date_event_movement_asset','time_event_movement_asset','expected_return_date_movement_asset','document_code_movement_asset','status_process_movement_asset','loan_recipient_type_movement_asset','loan_recipient_name_movement_asset','loan_recipient_document_movement_asset','loan_return_indefinite_movement_asset','manual_responsible_movement_asset','observations_movement_asset'];
        }
        private function validateMovementData(array $data): void
        {
            if (!in_array((int)$data[5], [1,2,3,4,5], true)) throw new DomainException('Tipo de movimiento no válido.');
            $space = $this->select('SELECT * FROM environments WHERE id_environment=? AND status_active_environment=1 AND status_deleted_environment=0', [$data[1]]);
            if (!$space) throw new DomainException('Selecciona un ambiente activo para el destino.');
            if ($data[7] !== null) {
                $date = DateTime::createFromFormat('!Y-m-d', (string)$data[7]);
                if (!$date || $date->format('Y-m-d') !== $data[7]) throw new DomainException('Indica una fecha válida o déjala sin definir.');
            }
            if ($data[8] !== null && !preg_match('/^([01]\d|2[0-3]):[0-5]\d:[0-5]\d$/', (string)$data[8])) throw new DomainException('Indica una hora válida o déjala sin definir.');
            if ($data[6] && !$this->select('SELECT id_employee FROM employees WHERE id_employee=? AND status_active_employee=1 AND status_deleted_employee=0', [$data[6]])) throw new DomainException('Selecciona un responsable activo.');
            if ($data[2]) {
                $plan = $this->select('SELECT * FROM floor_plans WHERE id_floor_plan=? AND status_active_floor_plan=1 AND status_deleted_floor_plan=0', [$data[2]]);
                if (!$plan || (int)$plan['id_campus_floor_plan'] !== (int)$space['id_campus_environment'] || (int)$plan['floor_floor_plan'] !== (int)$space['floor_environment']) throw new DomainException('El plano debe corresponder al establecimiento y piso del destino.');
            }
            if (($data[3] === null) !== ($data[4] === null) || ($data[3] !== null && (!$data[2] || $data[3] < 0 || $data[3] > 100 || $data[4] < 0 || $data[4] > 100))) throw new DomainException('La ubicación en el plano no es válida.');
            if ((int)$data[5] === 5) {
                if (!in_array($data[12], ['internal','external_person','external_company'], true) || (!$data[6] && !trim((string)$data[13]))) throw new DomainException('Indica quién recibe el préstamo.');
                if (!(int)$data[15] && (!$data[9] || ($data[7] !== null && $data[9] < $data[7]))) throw new DomainException('Indica una fecha de retorno válida o devolución indeterminada.');
            }
        }
        private function recordMovement(array $data, int $expectedCurrent): int
        {
            return $this->atomicMovement(function () use ($data, $expectedCurrent) {
                $this->lockAsset((int)$data[0]);
                $current = $this->get_current_movement_by_asset((int)$data[0], true);
                if ((int)($current['id_movement_asset'] ?? 0) !== $expectedCurrent) throw new DomainException('La situación del activo cambió. Actualiza la tabla antes de continuar.');
                if (!in_array((int)$data[5], MovementRules::allowedTypes($current ? (int)$current['id_type_movement_asset'] : null), true)) throw new DomainException('Esta operación no corresponde a la situación actual del activo.');
                $this->validateMovementData($data);
                if ($current && (int)$data[5] === 3 && (int)$current['id_space_movement_asset'] === (int)$data[1]) throw new DomainException('Una transferencia requiere un destino diferente.');
                if ($current && $data[7] !== null && $current['date_event_movement_asset'] !== null && $data[7] . ' ' . ($data[8] ?? '00:00:00') < ($current['date_event_movement_asset'] ?: '0000-00-00') . ' ' . ($current['time_event_movement_asset'] ?: '00:00:00')) throw new DomainException('La fecha no puede ser anterior al movimiento vigente.');
                if ($current) $this->update('UPDATE movements_asset SET status_record_current_movement_asset=0 WHERE id_movement_asset=?', [$expectedCurrent]);
                $columns = implode(',', $this->movementColumns());
                if (!$this->select('SELECT id_asset_movement_register FROM asset_movement_registers WHERE id_asset=?', [(int)$data[0]])) {
                    $this->insert('INSERT INTO asset_movement_registers(id_asset) VALUES(?)', [(int)$data[0]]);
                }
                $id = (int)$this->insert("INSERT INTO movements_asset($columns) VALUES(" . implode(',', array_fill(0,18,'?')) . ')', $data);
                $this->update('UPDATE assets SET status_movement_asset=1 WHERE id_asset=?', [$data[0]]);
                return $id;
            });
        }
        private function logMovementChange(int $id, string $action, string $reason, int $actor, array $previous, array $next): void
        {
            if (mb_strlen(trim($reason)) < 5 || $actor <= 0) throw new DomainException('Registra un motivo de al menos cinco caracteres.');
            $this->insert('INSERT INTO movement_changes(id_movement_asset,id_user,action_change,reason_change,previous_data,new_data) VALUES(?,?,?,?,?,?)', [$id,$actor,$action,trim($reason),json_encode($previous,JSON_UNESCAPED_UNICODE),json_encode($next,JSON_UNESCAPED_UNICODE)]);
        }
        public function annul_movement(int $id, string $reason, int $actor): bool
        {
            return $this->atomicMovement(function () use ($id,$reason,$actor) {
                $movement = $this->select('SELECT * FROM movements_asset WHERE id_movement_asset=?', [$id]);
                if (!$movement) throw new DomainException('Movimiento no encontrado.');
                $this->lockAsset((int)$movement['id_asset_movement_asset']);
                $current = $this->get_current_movement_by_asset((int)$movement['id_asset_movement_asset'], true);
                if (!$current || (int)$current['id_movement_asset'] !== $id) throw new DomainException('Solo se puede anular el movimiento vigente.');
                $this->update("UPDATE movements_asset SET status_record_current_movement_asset=0,status_annulled_movement_asset=1,status_process_movement_asset='Anulado' WHERE id_movement_asset=?", [$id]);
                $previous = $this->select('SELECT * FROM movements_asset WHERE id_asset_movement_asset=? AND id_movement_asset<>? AND status_deleted_movement_asset=0 AND status_annulled_movement_asset=0 ORDER BY id_movement_asset DESC LIMIT 1', [$movement['id_asset_movement_asset'],$id]);
                if ($previous) $this->update('UPDATE movements_asset SET status_record_current_movement_asset=1 WHERE id_movement_asset=?', [$previous['id_movement_asset']]);
                $this->update('UPDATE assets SET status_movement_asset=? WHERE id_asset=?', [$previous ? 1 : 0,$movement['id_asset_movement_asset']]);
                $this->logMovementChange($id,'annulled',$reason,$actor,$movement,['restored_movement'=>$previous['id_movement_asset'] ?? null]);
                return true;
            });
        }

		public function show_current_asset_locations(?int $id_floor_plan = null, ?int $id_type_asset = null, ?int $id_category_asset = null, ?int $id_space = null, ?int $id_condition_asset = null)
		{
            $rows = $this->asset_states(false);
            return array_values(array_filter($rows, function ($row) use ($id_floor_plan,$id_type_asset,$id_category_asset,$id_space,$id_condition_asset) {
                return (!$id_floor_plan || (int)$row['id_floor_plan'] === $id_floor_plan) && (!$id_type_asset || (int)$row['id_type_asset_asset'] === $id_type_asset) && (!$id_category_asset || (int)$row['id_category_asset_asset'] === $id_category_asset) && (!$id_space || (int)$row['id_space_movement_asset'] === $id_space) && (!$id_condition_asset || (int)$row['id_condition_asset_asset'] === $id_condition_asset);
            }));
        }

		public function show_movement_plan_location(int $id_movement_asset)
		{
			$sql = "SELECT ma.id_movement_asset, ma.coordinate_x_movement_asset, ma.coordinate_y_movement_asset,
						a.code_inventory_asset, ta.name_type_asset, ca.name_category_asset, coa.name_condition_asset,
						s.name_environment AS name_space, fp.name_floor_plan, fp.current_file_floor_plan, fp.current_image_floor_plan, c.name_campus
					FROM movements_asset ma
					INNER JOIN assets a ON a.id_asset = ma.id_asset_movement_asset
					INNER JOIN types_asset ta ON ta.id_type_asset = a.id_type_asset_asset
					INNER JOIN categories_asset ca ON ca.id_category_asset = a.id_category_asset_asset
					INNER JOIN conditions_asset coa ON coa.id_condition_asset = a.id_condition_asset_asset
					INNER JOIN environments s ON s.id_environment = ma.id_space_movement_asset
					LEFT JOIN floor_plans fp ON fp.id_floor_plan = ma.id_floor_plan_movement_asset
					LEFT JOIN campuses c ON c.id_campus = fp.id_campus_floor_plan
					WHERE ma.id_movement_asset = ? AND ma.status_deleted_movement_asset = 0";
			return $this->select($sql, [$id_movement_asset]);
		}
	}
