<?php
require_once __DIR__ . "/../Core/SuperAdministratorPolicy.php"; 

	class AdditionalPermissionsModel extends Mysql
	{
		public $int_id_role;
		public $int_id_module;
		public $int_id_control;
		public $int_allow;

		public function __construct()
		{
			parent::__construct();
		}

		public function show_additional_permissions(int $id_role)
		{
			$this->int_id_role = $id_role;
			$sql = "SELECT ap.id_additional_permission, ap.allow_additional_permission, c.id_control, c.name_control, m.id_module, m.name_module
					FROM additional_permissions ap
					INNER JOIN controls c ON ap.id_control_additional_permission = c.id_control
					INNER JOIN modules m ON c.id_module_control = m.id_module
					WHERE ap.id_role_additional_permission = $this->int_id_role
					AND c.status_active_control = 1
					AND c.status_deleted_control = 0";
			$request = $this->select_all($sql);
			return $request;
		}

		public function select_controls()
		{
			$sql = "SELECT c.id_control, c.name_control, m.id_module, m.name_module
					FROM controls c
					INNER JOIN modules m ON c.id_module_control = m.id_module WHERE c.status_active_control = 1 AND c.status_deleted_control = 0";
			$request = $this->select_all($sql);
			return $request;
		}

		public function check_additional_permissions(int $id_role)
		{
			$this->int_id_role = $id_role;
			$sql = "SELECT COUNT(*) AS total FROM additional_permissions WHERE id_role_additional_permission = $this->int_id_role";
			$request = $this->select_all($sql);
			return $request;
		}

		public function insert_additional_permission(int $id_role, int $id_control, int $allow){
            if ($id_role === ROLE_SUPER_ADMINISTRATOR) { $allow = SuperAdministratorPolicy::controlValue($id_control); }
			$this->int_id_role = $id_role;
			$this->int_id_control = $id_control;
			$this->int_allow = $allow;

			$query_insert = "INSERT INTO additional_permissions(id_role_additional_permission, id_control_additional_permission, allow_additional_permission) VALUES(?,?,?)";
			$data = array(
				$this->int_id_role,
				$this->int_id_control,
				$this->int_allow
			);
			$request_insert = $this->insert($query_insert, $data);
	        $return = $request_insert;
	        return $return;
		}

		public function update_additional_permission(int $id_role, int $id_control, int $allow){
            if ($id_role === ROLE_SUPER_ADMINISTRATOR) { $allow = SuperAdministratorPolicy::controlValue($id_control); }
			$this->int_id_role = $id_role;
			$this->int_id_control = $id_control;
			$this->int_allow = $allow;
			$sql = "UPDATE additional_permissions SET allow_additional_permission = ? WHERE id_role_additional_permission = $this->int_id_role AND id_control_additional_permission = $this->int_id_control";
			$data = array(
				$this->int_allow
			);
			$request = $this->update($sql, $data);
			$return = $request;
	        return $return;
		}

		public function show_additional_permissions_(int $id_role)
		{
			$this->int_id_role = $id_role;
			$sql = "SELECT 	ap.id_additional_permission, 
							ap.allow_additional_permission, 
							c.id_control, 
							c.name_control, 
							m.id_module, 
							m.name_module
					FROM additional_permissions ap
					INNER JOIN controls c ON ap.id_control_additional_permission = c.id_control
					INNER JOIN modules m ON c.id_module_control = m.id_module
					WHERE ap.id_role_additional_permission = $this->int_id_role";
			$request = $this->select_all($sql);
			return $request;
		}

		public function extract_additional_permissions(int $id_role, int $id_module){
			$this->int_id_role = $id_role;
			$this->int_id_module = $id_module;
			$sql = "SELECT ap.id_control_additional_permission,
						   MAX(ap.allow_additional_permission) AS allow_additional_permission,
						   c.id_module_control
					FROM additional_permissions ap
					INNER JOIN controls c ON ap.id_control_additional_permission = c.id_control
					WHERE ap.id_role_additional_permission = $this->int_id_role
					AND c.id_module_control = $this->int_id_module
                    AND c.status_active_control = 1 AND c.status_deleted_control = 0
					GROUP BY ap.id_control_additional_permission, c.id_module_control";
			$request = $this->select_all($sql);
			$data_additional_permissions = array();
			for ($i=0; $i < count($request); $i++) { 
				if ($id_role === ROLE_SUPER_ADMINISTRATOR) $request[$i]['allow_additional_permission'] = SuperAdministratorPolicy::controlValue((int)$request[$i]['id_control_additional_permission']);
				$data_additional_permissions[$request[$i]['id_control_additional_permission']] = $request[$i];
			}
			return $data_additional_permissions;
		}
		

        public function control_allowed_by_name(int $id_role, int $id_module, string $name_control): bool
        {
            $sql = "SELECT c.id_control, COALESCE(MAX(ap.allow_additional_permission), 0) AS allow_additional_permission
                    FROM controls c
                    LEFT JOIN additional_permissions ap ON ap.id_control_additional_permission = c.id_control
                        AND ap.id_role_additional_permission = ?
                    WHERE c.id_module_control = ?
                    AND c.name_control = ?
                    AND c.status_active_control = 1
                    AND c.status_deleted_control = 0 GROUP BY c.id_control";
            $row = $this->select($sql, [$id_role, $id_module, $name_control]);
            if ($id_role === ROLE_SUPER_ADMINISTRATOR) return SuperAdministratorPolicy::controlValue((int)($row['id_control'] ?? 0)) === 1;
            return !empty($row) && (int)($row['allow_additional_permission'] ?? 0) === 1;
        }

        public function allowed_controls_map(int $id_role, int $id_module): array
        {
            $sql = "SELECT c.id_control, c.name_control, COALESCE(MAX(ap.allow_additional_permission), 0) AS allow_additional_permission
                    FROM controls c
                    LEFT JOIN additional_permissions ap ON ap.id_control_additional_permission = c.id_control
                        AND ap.id_role_additional_permission = ?
                    WHERE c.id_module_control = ?
                    AND c.status_active_control = 1
                    AND c.status_deleted_control = 0
                    GROUP BY c.id_control, c.name_control";
            $rows = $this->select_all($sql, [$id_role, $id_module]);
            $map = [];
            foreach ($rows as $row) {
                $map[$row['name_control']] = $id_role === ROLE_SUPER_ADMINISTRATOR ? SuperAdministratorPolicy::controlValue((int)$row['id_control']) : (int)$row['allow_additional_permission'];
            }
            return $map;
        }

	}
?>