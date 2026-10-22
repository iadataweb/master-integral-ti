<?php
require_once __DIR__ . "/../Core/SuperAdministratorPolicy.php"; 
	class RolesModel extends Mysql
	{
    	private $int_status_deleted_role;
		private $int_id_role;
		private $str_name_role;
		private $str_description_role;
		private $int_status_active_role;

		public function __construct()
		{
			parent::__construct();
		}

		public function show_roles()
		{
			$sql = "SELECT id_role, name_role, description_role, status_active_role,
						   CASE WHEN id_role = " . ROLE_SUPER_ADMINISTRATOR . " THEN 1 ELSE 0 END AS protected_role,
						   CASE WHEN id_role = " . ROLE_SUPER_ADMINISTRATOR . " THEN 1 ELSE 0 END AS administrative_role
					FROM roles
					WHERE status_deleted_role = 0 AND id_role != 4
					ORDER BY id_role ASC";
			$request = $this->select_all($sql);
			return $request;
		}

		public function show_role(int $id_role)
		{
			$this->int_id_role = $id_role;
			$sql = "SELECT id_role, name_role, description_role, status_active_role,
						   CASE WHEN id_role = " . ROLE_SUPER_ADMINISTRATOR . " THEN 1 ELSE 0 END AS protected_role,
						   CASE WHEN id_role = " . ROLE_SUPER_ADMINISTRATOR . " THEN 1 ELSE 0 END AS administrative_role
					FROM roles
					WHERE id_role = ? AND status_deleted_role = 0";
			$request = $this->select($sql, [$this->int_id_role]);
			return $request;
		}

		public function insert_role(string $name_role, string $description_role, int $status_active_role){
			$this->str_name_role = $name_role;
			$this->str_description_role = $description_role;
			$this->int_status_active_role = $status_active_role;
			$query_insert = "INSERT INTO roles(name_role, description_role, status_active_role) VALUES(?,?,?)";
			$data = array(
				$this->str_name_role,
				$this->str_description_role,
				$this->int_status_active_role
			);
			$request_insert = $this->insert($query_insert, $data);
	        $return = $request_insert;
	        return $return;
		}

		public function update_role(int $id_role, string $name_role, string $description_role, int $status_active_role){
			$this->int_id_role = $id_role;
			$this->str_name_role = $name_role;
			$this->str_description_role = $description_role;
			$this->int_status_active_role = $status_active_role;
			$sql = "UPDATE roles SET name_role = ?, description_role = ?, status_active_role = ? WHERE id_role = ?";
			$data = array(
				$this->str_name_role,
				$this->str_description_role,
				$this->int_status_active_role,
				$this->int_id_role
			);
			$request = $this->update($sql, $data);
			$return = $request;
	        return $return;
		}

		public function delete_role(int $id_role){
			$this->int_id_role = $id_role;
			$this->int_status_deleted_role = 1;
			$sql = "UPDATE roles SET status_deleted_role = ? WHERE id_role = ?";
			$data = array(
				$this->int_status_deleted_role,
				$this->int_id_role
			);
			$request = $this->update($sql, $data);
			$return = $request;
	        return $return;
		}

		public function combo_roles()
		{
			$sql = "SELECT id_role, name_role FROM roles WHERE status_active_role = 1 AND status_deleted_role = 0 ORDER BY name_role ASC";
			$request = $this->select_all($sql);
			return $request;
		}

		public function is_protected_role(int $id_role): bool
		{
			$role = $this->show_role($id_role);
			return !empty($role) && (int)$role['protected_role'] === 1;
		}

		public function is_super_role(int $id_role): bool
		{
			return $id_role === ROLE_SUPER_ADMINISTRATOR;
		}

		public function select_modules(): array
		{
			return $this->select_all("SELECT id_module, name_module, description_module FROM modules WHERE status_active_module = 1 AND status_deleted_module = 0 ORDER BY id_module ASC");
		}

		public function select_controls(): array
		{
			return $this->select_all("SELECT c.id_control, c.id_module_control, c.name_control, c.description_control, m.name_module
					FROM controls c
					INNER JOIN modules m ON c.id_module_control = m.id_module
					WHERE c.status_active_control = 1 AND c.status_deleted_control = 0
					ORDER BY c.id_module_control ASC, c.id_control ASC");
		}

		public function role_permissions_map(int $id_role): array
		{
			$rows = $this->select_all("SELECT id_module_permission, r_read_permission, w_write_permission, u_update_permission, d_delete_permission
					FROM permissions WHERE id_role_permission = ?", [$id_role]);
			$map = [];
			foreach ($rows as $row) {
				$map[(int)$row['id_module_permission']] = $row;
			}
			return $map;
		}

		public function role_controls_map(int $id_role): array
		{
			$rows = $this->select_all("SELECT id_control_additional_permission, MAX(allow_additional_permission) AS allow_additional_permission
					FROM additional_permissions WHERE id_role_additional_permission = ? GROUP BY id_control_additional_permission", [$id_role]);
			$map = [];
			foreach ($rows as $row) {
				$map[(int)$row['id_control_additional_permission']] = (int)$row['allow_additional_permission'];
			}
			return $map;
		}

		public function access_matrix(int $id_role): array
		{
			$permissions = $this->role_permissions_map($id_role);
			$controls = $this->role_controls_map($id_role);
			$modules = $this->select_modules();
			$all_controls = $this->select_controls();
			$controls_by_module = [];
			foreach ($all_controls as $control) {
				$control['allow_additional_permission'] = $id_role === ROLE_SUPER_ADMINISTRATOR ? SuperAdministratorPolicy::controlValue((int)$control['id_control']) : ($controls[(int)$control['id_control']] ?? 0);
				$controls_by_module[(int)$control['id_module_control']][] = $control;
			}
			foreach ($modules as &$module) {
				$module_id = (int)$module['id_module'];
				$current = $permissions[$module_id] ?? [];
				$module['r_read_permission'] = $id_role === ROLE_SUPER_ADMINISTRATOR ? 1 : (int)($current['r_read_permission'] ?? 0);
				$module['w_write_permission'] = $id_role === ROLE_SUPER_ADMINISTRATOR ? 1 : (int)($current['w_write_permission'] ?? 0);
				$module['u_update_permission'] = $id_role === ROLE_SUPER_ADMINISTRATOR ? 1 : (int)($current['u_update_permission'] ?? 0);
				$module['d_delete_permission'] = $id_role === ROLE_SUPER_ADMINISTRATOR ? 1 : (int)($current['d_delete_permission'] ?? 0);
				$module['controls'] = $controls_by_module[$module_id] ?? [];
			}
			return $modules;
		}

        public function save_role_access(int $id_role, array $modules, array $controls): bool
        {
            if (!$this->show_role($id_role)) return false;
            $pdo = $this->connection();
            $nested = $pdo->inTransaction();
            if ($nested) $pdo->exec('SAVEPOINT role_access_save'); else $pdo->beginTransaction();
            try {
                $result = $this->persist_role_access($id_role, $modules, $controls);
                if ($nested) $pdo->exec('RELEASE SAVEPOINT role_access_save'); else $pdo->commit();
                if (class_exists('RoleAccess', false)) RoleAccess::reset();
                return $result;
            } catch (Throwable $error) {
                if ($nested) $pdo->exec('ROLLBACK TO SAVEPOINT role_access_save');
                elseif ($pdo->inTransaction()) $pdo->rollBack();
                if (class_exists('RoleAccess', false)) RoleAccess::reset();
                throw $error;
            }
        }

        private function persist_role_access(int $id_role, array $modules, array $controls): bool
		{
			$all_controls = $this->select_controls();
			$module_action_controls = [];
			foreach ($all_controls as $control) {
				$parts = explode(': ', (string)$control['name_control']);
				$action = count($parts) === 4 ? $parts[3] : '';
				if (($parts[0] ?? '') !== 'Submenu' || !in_array($action, ['Crear', 'Editar', 'Eliminar'], true)) {
					continue;
				}
				$module_action_controls[(int)$control['id_module_control']][$action][] = (int)$control['id_control'];
			}

			foreach ($this->select_modules() as $module) {
				$id_module = (int)$module['id_module'];
				$permission = $modules[$id_module] ?? [];
				$r = $id_role === ROLE_SUPER_ADMINISTRATOR ? 1 : (!empty($permission['r']) ? 1 : 0);
				$w = $id_role === ROLE_SUPER_ADMINISTRATOR ? 1 : (!empty($permission['w']) ? 1 : 0);
				$u = $id_role === ROLE_SUPER_ADMINISTRATOR ? 1 : (!empty($permission['u']) ? 1 : 0);
				$d = $id_role === ROLE_SUPER_ADMINISTRATOR ? 1 : (!empty($permission['d']) ? 1 : 0);
				if ($id_role !== ROLE_SUPER_ADMINISTRATOR && !empty($module_action_controls[$id_module])) {
					$w = $this->has_selected_action_control($module_action_controls[$id_module]['Crear'] ?? [], $controls) ? 1 : 0;
					$u = $this->has_selected_action_control($module_action_controls[$id_module]['Editar'] ?? [], $controls) ? 1 : 0;
					$d = $this->has_selected_action_control($module_action_controls[$id_module]['Eliminar'] ?? [], $controls) ? 1 : 0;
				}
				$exists = $this->select("SELECT id_permission FROM permissions WHERE id_role_permission = ? AND id_module_permission = ?", [$id_role, $id_module]);
				if ($exists) {
					$this->update("UPDATE permissions SET r_read_permission = ?, w_write_permission = ?, u_update_permission = ?, d_delete_permission = ? WHERE id_role_permission = ? AND id_module_permission = ?", [$r, $w, $u, $d, $id_role, $id_module]);
				} else {
					$this->insert("INSERT INTO permissions(id_role_permission, id_module_permission, r_read_permission, w_write_permission, u_update_permission, d_delete_permission) VALUES(?,?,?,?,?,?)", [$id_role, $id_module, $r, $w, $u, $d]);
				}
			}

			if ($id_role !== ROLE_SUPER_ADMINISTRATOR) {
				$normalize_control_label = function ($value) {
					$value = strtolower(trim((string)$value));
					$transliterated = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
					if ($transliterated !== false && $transliterated !== '') {
						$value = strtolower($transliterated);
					}
					return preg_replace('/[^a-z0-9]+/', '', $value);
				};
				$parameter_scopes_with_access = [];
				$parameter_submenu_controls = [];
				foreach ($all_controls as $control) {
					$id_control = (int)$control['id_control'];
					$parts = explode(': ', (string)$control['name_control']);
					if (count($parts) === 4 && $parts[0] === 'Parametro' && !empty($controls[$id_control])) {
						$parameter_scopes_with_access[$normalize_control_label($parts[1])] = true;
						continue;
					}
					if (($parts[0] ?? '') !== 'Submenu') {
						continue;
					}
					$scope = $parts[1] ?? '';
					$submenu = $parts[2] ?? '';
					$action = count($parts) === 4 ? ($parts[3] ?? '') : 'Ver';
					$normalized_submenu = $normalize_control_label($submenu);
					if (($normalized_submenu === 'parametros' || $normalized_submenu === 'parameters' || strpos($normalized_submenu, 'parametro') !== false) && $action === 'Ver') {
						$parameter_submenu_controls[$normalize_control_label($scope)][] = $id_control;
					}
				}
				foreach ($parameter_submenu_controls as $scope => $control_ids) {
					foreach ($control_ids as $id_control) {
						if (!empty($parameter_scopes_with_access[$scope])) {
							$controls[$id_control] = 1;
						} else {
							unset($controls[$id_control]);
						}
					}
				}
			}

			foreach ($all_controls as $control) {
				$id_control = (int)$control['id_control'];
				$allow = $id_role === ROLE_SUPER_ADMINISTRATOR ? SuperAdministratorPolicy::controlValue($id_control) : (!empty($controls[$id_control]) ? 1 : 0);
					$exists = $this->select("SELECT MIN(id_additional_permission) AS id_additional_permission FROM additional_permissions WHERE id_role_additional_permission = ? AND id_control_additional_permission = ?", [$id_role, $id_control]);
				if (!empty($exists['id_additional_permission'])) {
					$this->update("UPDATE additional_permissions SET allow_additional_permission = ? WHERE id_role_additional_permission = ? AND id_control_additional_permission = ?", [$allow, $id_role, $id_control]);
					$this->delete_duplicate_additional_permissions($id_role, $id_control, (int)$exists['id_additional_permission']);
				} else {
					$this->insert("INSERT INTO additional_permissions(id_role_additional_permission, id_control_additional_permission, allow_additional_permission) VALUES(?,?,?)", [$id_role, $id_control, $allow]);
				}
			}
			return true;
		}

		private function has_selected_action_control(array $control_ids, array $controls): bool
		{
			foreach ($control_ids as $id_control) {
				if (!empty($controls[$id_control])) {
					return true;
				}
			}
			return false;
		}

		private function delete_duplicate_additional_permissions(int $id_role, int $id_control, int $keep_id): void
		{
			$this->delete("DELETE FROM additional_permissions WHERE id_role_additional_permission = ? AND id_control_additional_permission = ? AND id_additional_permission != ?", [$id_role, $id_control, $keep_id]);
		}

	}
?>
