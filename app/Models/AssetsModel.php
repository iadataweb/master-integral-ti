<?php 
	class AssetsModel extends Mysql
	{
		private $int_id_asset;
		private $str_name_asset;
		private $int_status_active_asset;
		private $int_status_deleted_asset;

		public function __construct()
		{
			parent::__construct();
		}

		public function show_assets()
		{
            $sql = "SELECT a.id_asset, a.code_inventory_asset, a.date_created_asset, a.status_active_asset, a.label_attached_asset, a.model_asset, a.serial_number_asset, 
                    ta.name_type_asset, ca.name_category_asset, b.name_brand, coa.name_condition_asset
					FROM assets a 
					INNER JOIN types_asset ta ON a.id_type_asset_asset = ta.id_type_asset
					INNER JOIN categories_asset ca ON a.id_category_asset_asset = ca.id_category_asset
					INNER JOIN brands b ON a.id_brand_asset = b.id_brand
					INNER JOIN conditions_asset coa ON a.id_condition_asset_asset = coa.id_condition_asset
					WHERE a.status_deleted_asset = 0";
			$request = $this->select_all($sql);
			return $request;
		}

        public function show_assets_existing(int $id_asset)
		{
            $this->int_id_asset = $id_asset;
            $sql = "SELECT a.id_asset, a.code_inventory_asset, a.date_created_asset, ta.name_type_asset, 
                    ca.name_category_asset, b.name_brand, coa.name_condition_asset
					FROM assets a 
					INNER JOIN types_asset ta ON a.id_type_asset_asset = ta.id_type_asset
					INNER JOIN categories_asset ca ON a.id_category_asset_asset = ca.id_category_asset
					INNER JOIN brands b ON a.id_brand_asset = b.id_brand
					LEFT JOIN conditions_asset coa ON a.id_condition_asset_asset = coa.id_condition_asset
					WHERE a.status_active_asset = 1 AND a.status_deleted_asset = 0";
			$request = $this->select_all($sql);
			return $request;
		}

        public function show_assets_without_movements(int $id_asset)
		{
            $this->int_id_asset = $id_asset;
            $sql = "SELECT a.id_asset, a.code_inventory_asset, a.date_created_asset, a.status_active_asset, 
                    ta.name_type_asset, ca.name_category_asset, b.name_brand, coa.name_condition_asset
					FROM assets a 
					INNER JOIN types_asset ta ON a.id_type_asset_asset = ta.id_type_asset
					INNER JOIN categories_asset ca ON a.id_category_asset_asset = ca.id_category_asset
					INNER JOIN brands b ON a.id_brand_asset = b.id_brand
					INNER JOIN conditions_asset coa ON a.id_condition_asset_asset = coa.id_condition_asset
					WHERE a.status_movement_asset = 0 AND a.status_active_asset = 1 AND a.status_deleted_asset = 0";            
			$request = $this->select_all($sql);
			return $request;
		}

        public function show_assets_without_scheduled_maintenance(int $id_asset)
		{
            $this->int_id_asset = $id_asset;
            $sql = "SELECT a.id_asset, a.code_inventory_asset, a.date_created_asset, a.status_active_asset, 
                    ta.name_type_asset, ca.name_category_asset, b.name_brand, coa.name_condition_asset
					FROM assets a 
					INNER JOIN types_asset ta ON a.id_type_asset_asset = ta.id_type_asset
					INNER JOIN categories_asset ca ON a.id_category_asset_asset = ca.id_category_asset
					INNER JOIN brands b ON a.id_brand_asset = b.id_brand
					INNER JOIN conditions_asset coa ON a.id_condition_asset_asset = coa.id_condition_asset
					WHERE a.status_maintenance_asset = 0 AND a.status_active_asset = 1 AND a.status_deleted_asset = 0";            
			$request = $this->select_all($sql);
			return $request;
		}

        public function show_assets_available(int $id_asset)
		{
            $this->int_id_asset = $id_asset;
            $sql = "SELECT a.id_asset, a.code_inventory_asset, a.date_created_asset, a.status_active_asset, 
                    ta.name_type_asset, ca.name_category_asset, b.name_brand, coa.name_condition_asset
					FROM assets a 
					INNER JOIN types_asset ta ON a.id_type_asset_asset = ta.id_type_asset
					INNER JOIN categories_asset ca ON a.id_category_asset_asset = ca.id_category_asset
					INNER JOIN brands b ON a.id_brand_asset = b.id_brand
					INNER JOIN conditions_asset coa ON a.id_condition_asset_asset = coa.id_condition_asset
					WHERE a.status_available_asset = 1 AND a.status_active_asset = 1 AND a.status_deleted_asset = 0";
            if (!empty($id_asset)) {
                $sql .= "
                    UNION ALL
                    SELECT a.id_asset, a.code_inventory_asset, a.date_created_asset, a.status_active_asset, 
                    ta.name_type_asset, ca.name_category_asset, b.name_brand, coa.name_condition_asset
					FROM assets a 
					INNER JOIN types_asset ta ON a.id_type_asset_asset = ta.id_type_asset
					INNER JOIN categories_asset ca ON a.id_category_asset_asset = ca.id_category_asset
					INNER JOIN brands b ON a.id_brand_asset = b.id_brand
					INNER JOIN conditions_asset coa ON a.id_condition_asset_asset = coa.id_condition_asset
					WHERE a.id_asset = $this->int_id_asset                    
                ";
            }
			$request = $this->select_all($sql);
			return $request;
		}

		public function show_asset(int $id_asset)
		{
			$this->int_id_asset = $id_asset;
            $sql = "SELECT a.id_asset, a.code_inventory_asset, a.model_asset, a.serial_number_asset, a.entry_date_asset, 
                    a.acquisition_date_asset, a.date_created_asset, a.status_active_asset, a.descriptions_asset, a.observations_asset,
					ta.id_type_asset, ta.name_type_asset, ca.id_category_asset, ca.name_category_asset, b.id_brand, b.name_brand, coa.id_condition_asset, coa.name_condition_asset
					FROM assets a 
					INNER JOIN types_asset ta ON a.id_type_asset_asset = ta.id_type_asset
					INNER JOIN categories_asset ca ON a.id_category_asset_asset = ca.id_category_asset
					INNER JOIN brands b ON a.id_brand_asset = b.id_brand
					INNER JOIN conditions_asset coa ON a.id_condition_asset_asset = coa.id_condition_asset
					WHERE a.id_asset = $this->int_id_asset AND a.status_deleted_asset = 0";
			$request = $this->select($sql);
			return $request;
		}

        public function show_files(int $id_asset){
            $this->int_id_asset = $id_asset;
			$sql = "SELECT 	ta.id_attachment_asset, 
							ta.id_asset,
							ta.route_attachment_asset,
							ta.name_attachment_asset
					FROM attachments_asset ta 
					INNER JOIN assets a ON ta.id_asset = a.id_asset
					WHERE ta.id_asset = ?";
			$data = [
				$this->int_id_asset
			];
			$request = $this->select_all($sql, $data);
			return $request;
		}

		public function insert_asset(int $id_type_asset, int $id_category_asset, int $id_brand, int $id_condition_asset, string $code_inventory_asset, string $model_asset, string $serial_number_asset, ?string $entry_date_asset, ?string $acquisition_date_asset, string $descriptions_asset, string $observations_asset, int $status_active_asset){
            $this->int_id_type_asset = $id_type_asset;
            $this->int_id_category_asset = $id_category_asset;
            $this->int_id_brand = $id_brand;
            $this->int_id_condition_asset = $id_condition_asset;
            $this->str_code_inventory_asset = $code_inventory_asset;
            $this->str_model_asset = $model_asset;
            $this->str_serial_number_asset = $serial_number_asset;
            $this->str_entry_date_asset = $entry_date_asset;
            $this->str_acquisition_date_asset = $acquisition_date_asset;
            $this->str_descriptions_asset = $descriptions_asset;
            $this->str_observations_asset = $observations_asset;
			$this->int_status_active_asset = $status_active_asset;
            $query_insert = "INSERT INTO assets(
                                id_type_asset_asset,
                                id_category_asset_asset,
                                id_brand_asset,
                                id_condition_asset_asset,
                                code_inventory_asset,
                                model_asset,
                                serial_number_asset,
                                entry_date_asset,
                                acquisition_date_asset,
                                descriptions_asset,
                                observations_asset,
                                status_active_asset
                            ) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)";
            $data = array(
                $this->int_id_type_asset,
                $this->int_id_category_asset,
                $this->int_id_brand,
                $this->int_id_condition_asset,
                $this->str_code_inventory_asset,
                $this->str_model_asset,
                $this->str_serial_number_asset,
                $this->str_entry_date_asset,
                $this->str_acquisition_date_asset,
                $this->str_descriptions_asset,
                $this->str_observations_asset,
                $this->int_status_active_asset
            );
			$request_insert = $this->insert($query_insert, $data);
	        $return = $request_insert;
	        return $return;
		}

		public function update_asset(int $id_asset, int $id_type_asset, int $id_category_asset, int $id_brand, int $id_condition_asset, string $model_asset, string $serial_number_asset, ?string $entry_date_asset, ?string $acquisition_date_asset, string $descriptions_asset, string $observations_asset, int $status_active_asset){
			$this->int_id_asset = $id_asset;
            $this->int_id_type_asset = $id_type_asset;
            $this->int_id_category_asset = $id_category_asset;
            $this->int_id_brand = $id_brand;
            $this->int_id_condition_asset = $id_condition_asset;
            $this->str_model_asset = $model_asset;
            $this->str_serial_number_asset = $serial_number_asset;
            $this->str_entry_date_asset = $entry_date_asset;
            $this->str_acquisition_date_asset = $acquisition_date_asset;
            $this->str_descriptions_asset = $descriptions_asset;
            $this->str_observations_asset = $observations_asset;
			$this->int_status_active_asset = $status_active_asset;
            $sql = "UPDATE assets SET 
                        id_type_asset_asset = ?,
                        id_category_asset_asset = ?,
                        id_brand_asset = ?,
                        id_condition_asset_asset = ?,
                        model_asset = ?,
                        serial_number_asset = ?,
                        entry_date_asset = ?,
                        acquisition_date_asset = ?,
                        descriptions_asset = ?,
                        observations_asset = ?,
                        status_active_asset = ?
                    WHERE id_asset = ?";
            $data = array(
                $this->int_id_type_asset,
                $this->int_id_category_asset,
                $this->int_id_brand,
                $this->int_id_condition_asset,
                $this->str_model_asset,
                $this->str_serial_number_asset,
                $this->str_entry_date_asset,
                $this->str_acquisition_date_asset,
                $this->str_descriptions_asset,
                $this->str_observations_asset,
                $this->int_status_active_asset,
                $this->int_id_asset
            );
			$request = $this->update($sql, $data);
			$return = $request;
	        return $return;
		}

        public function code_inventory_asset_specific(int $id_asset)
		{
            $this->int_id_asset = $id_asset;
            $sql = "SELECT id_asset, code_inventory_asset FROM assets WHERE id_asset = $this->int_id_asset AND status_deleted_asset = 0";
            $request = $this->select_all($sql);
			return $request;
		}

        public function set_label_attached(int $id, int $attached): bool
        {
            return $this->update("UPDATE assets SET label_attached_asset = ? WHERE id_asset = ? AND status_deleted_asset = 0", [$attached, $id]);
        }

        public function selected_labels(array $ids, bool $pendingOnly): array
        {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $sql = "SELECT id_asset, code_inventory_asset FROM assets WHERE status_deleted_asset = 0 AND id_asset IN ($placeholders)";
            if ($pendingOnly) {
                $sql .= " AND label_attached_asset = 0";
            }
            return $this->select_all($sql . " ORDER BY id_asset", $ids);
        }

        public function label_template_for_pdf(int $id_label_template)
        {
            $sql = "SELECT * FROM label_templates WHERE id_label_template = ? AND status_active_label_template = 1 AND status_deleted_label_template = 0";
            return $this->select($sql, [$id_label_template]);
        }
        public function insert_attachment_asset(int $id_asset, string $file_path, string $file_name) {
			$query_insert = "INSERT INTO attachments_asset(id_asset, route_attachment_asset, name_attachment_asset) VALUES(?, ?, ?)";
			$data = [
				$id_asset,
                $file_path,
				$file_name
			];
            $request_insert = $this->insert($query_insert, $data);
            return $request_insert;
        }

        public function delete_attachment_asset(int $id_attachment_asset){
			$this->int_id_attachment_asset = $id_attachment_asset;
			$query  = "DELETE FROM attachments_asset WHERE id_attachment_asset = ?";
			$data = [
				$this->int_id_attachment_asset
			];
	        $request = $this->delete($query, $data);
	        return $request;
		}

        public function asset_equipment_user(int $id_asset)
        {
            $sql = "SELECT
                        aeu.*,
                        CONCAT(
                            COALESCE(pt_created.abbreviation_professional_title, ''), ' ',
                            COALESCE(e_created.first_name_employee, ''), ' ',
                            COALESCE(e_created.paternal_last_name_employee, '')
                        ) AS created_by,
                        CONCAT(
                            COALESCE(pt_updated.abbreviation_professional_title, ''), ' ',
                            COALESCE(e_updated.first_name_employee, ''), ' ',
                            COALESCE(e_updated.paternal_last_name_employee, '')
                        ) AS updated_by
                    FROM asset_equipment_users aeu
                    LEFT JOIN users u_created ON u_created.id_user = aeu.id_user_created_asset_equipment_user
                    LEFT JOIN employees e_created ON e_created.id_employee = u_created.id_employee_user
                    LEFT JOIN professional_titles pt_created ON pt_created.id_professional_title = e_created.id_professional_title_employee
                    LEFT JOIN users u_updated ON u_updated.id_user = aeu.id_user_updated_asset_equipment_user
                    LEFT JOIN employees e_updated ON e_updated.id_employee = u_updated.id_employee_user
                    LEFT JOIN professional_titles pt_updated ON pt_updated.id_professional_title = e_updated.id_professional_title_employee
                    WHERE aeu.id_asset_asset_equipment_user = ?";
            return $this->select($sql, [$id_asset]);
        }

        public function save_asset_equipment_user(int $id_asset, string $username, int $id_user)
        {
            $existing = $this->asset_equipment_user($id_asset);
            if ($existing) {
                $sql = "UPDATE asset_equipment_users
                        SET username_asset_equipment_user = ?,
                            id_user_updated_asset_equipment_user = ?
                        WHERE id_asset_asset_equipment_user = ?";
                return $this->update($sql, [$username, $id_user, $id_asset]);
            }
            $sql = "INSERT INTO asset_equipment_users(
                        id_asset_asset_equipment_user,
                        username_asset_equipment_user,
                        id_user_created_asset_equipment_user,
                        id_user_updated_asset_equipment_user
                    ) VALUES (?, ?, ?, ?)";
            return (int)$this->insert($sql, [$id_asset, $username, $id_user, $id_user]) > 0;
        }

        public function asset_confidential_note(int $id_asset)
        {
            $this->ensure_asset_confidential_notes_table();
            $sql = "SELECT * FROM asset_confidential_notes WHERE id_asset_asset_confidential_note = ?";
            return $this->select($sql, [$id_asset]);
        }

        public function save_asset_confidential_note(int $id_asset, string $confidential_note, int $id_user)
        {
            $this->ensure_asset_confidential_notes_table();
            $existing = $this->asset_confidential_note($id_asset);
            if ($existing) {
                $sql = "UPDATE asset_confidential_notes
                        SET confidential_note_asset = ?,
                            id_user_updated_asset_confidential_note = ?
                        WHERE id_asset_asset_confidential_note = ?";
                return $this->update($sql, [$confidential_note, $id_user, $id_asset]);
            }
            $sql = "INSERT INTO asset_confidential_notes(
                        id_asset_asset_confidential_note,
                        confidential_note_asset,
                        id_user_created_asset_confidential_note,
                        id_user_updated_asset_confidential_note
                    ) VALUES (?, ?, ?, ?)";
            return (int)$this->insert($sql, [$id_asset, $confidential_note, $id_user, $id_user]) > 0;
        }

        private function ensure_asset_confidential_notes_table()
        {
            $sql = "CREATE TABLE IF NOT EXISTS asset_confidential_notes (
                        id_asset_confidential_note INT(11) NOT NULL AUTO_INCREMENT,
                        id_asset_asset_confidential_note INT(11) NOT NULL,
                        confidential_note_asset TEXT NULL,
                        id_user_created_asset_confidential_note INT(11) NULL,
                        id_user_updated_asset_confidential_note INT(11) NULL,
                        date_created_asset_confidential_note TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        date_updated_asset_confidential_note TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        PRIMARY KEY (id_asset_confidential_note),
                        UNIQUE KEY uq_asset_confidential_note_asset (id_asset_asset_confidential_note)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
            return $this->update($sql);
        }

		public function delete_asset(int $id_asset){
			$this->int_id_asset = $id_asset;
			$this->int_status_deleted_asset = 1;
			$sql = "UPDATE assets SET status_deleted_asset = ? WHERE id_asset = $this->int_id_asset";
			$data = array(
				$this->int_status_deleted_asset
			);
			$request = $this->update($sql, $data);
			$return = $request;
	        return $return;
		}
	}
?>
