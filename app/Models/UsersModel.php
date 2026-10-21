<?php 
	class UsersModel extends Mysql
	{
		private $int_id_user;
		private $str_first_names_user;
		private $str_last_names_user;
		private $str_cell_phone_user;
		private $str_email_user;
		private $str_password_user;
		private $int_id_role_user;
		private $int_status_active_user;

		public function __construct()
		{
			parent::__construct();
		}

		public function show_users()
		{
			$sql = "SELECT CONCAT(
								pt.abbreviation_professional_title, ' ',
								CASE e.preferred_name_employee
									WHEN 1 THEN e.first_name_employee
									WHEN 2 THEN e.middle_name_employee
								END, ' ',
								e.paternal_last_name_employee, ' ',
								e.maternal_last_name_employee)
								AS display_name, 
					u.id_user, u.name_user, u.id_role_user, u.status_active_user, u.date_created_user, r.name_role
					FROM users u 
					INNER JOIN employees e ON u.id_employee_user = e.id_employee
					INNER JOIN professional_titles pt ON e.id_professional_title_employee = pt.id_professional_title
					INNER JOIN roles r ON u.id_role_user = r.id_role
					WHERE u.status_deleted_user = 0";
			$request = $this->select_all($sql);
			return $request;
		}

		public function show_user(int $id_user)
		{
			$this->int_id_user = $id_user;
			$sql = "SELECT u.id_user,
							CONCAT(
								pt.abbreviation_professional_title, ' ',
								CASE e.preferred_name_employee
									WHEN 1 THEN e.first_name_employee
									WHEN 2 THEN e.middle_name_employee
								END, ' ',
								e.paternal_last_name_employee, ' ',
								e.maternal_last_name_employee)
								AS official_name_employee, e.id_employee, u.name_user, u.status_active_user, r.id_role, r.name_role, a.name_area, p.name_position
					FROM users u 
					INNER JOIN employees e ON u.id_employee_user = e.id_employee
					INNER JOIN professional_titles pt ON e.id_professional_title_employee = pt.id_professional_title
					INNER JOIN areas a ON e.id_area_employee = a.id_area
					INNER JOIN positions p ON e.id_position_employee = p.id_position
					INNER JOIN roles r ON u.id_role_user = r.id_role
					WHERE u.id_user = ? AND u.status_deleted_user = 0";
			$data = [
				$this->int_id_user
			];
			$request = $this->select($sql, $data);
			return $request;
		}

		public function super_user(): array
		{
			$sql = "SELECT u.id_user, u.name_user, u.id_role_user, u.status_active_user, u.status_deleted_user
					FROM users u
					WHERE u.id_role_user = ?
					AND u.status_deleted_user = 0
					ORDER BY u.id_user ASC
					LIMIT 1";
			$request = $this->select($sql, [ROLE_SUPER_ADMINISTRATOR]);
			return is_array($request) ? $request : [];
		}

		public function active_super_count(?int $exclude_user = null): int
		{
			$sql = "SELECT COUNT(*) AS total
					FROM users
					WHERE id_role_user = ?
					AND status_active_user = 1
					AND status_deleted_user = 0";
			$data = [ROLE_SUPER_ADMINISTRATOR];
			if ($exclude_user !== null) {
				$sql .= " AND id_user != ?";
				$data[] = $exclude_user;
			}
			$request = $this->select($sql, $data);
			return (int)($request['total'] ?? 0);
		}

		public function count_super_users(?int $exclude_user = null): int
		{
			$sql = "SELECT COUNT(*) AS total
					FROM users
					WHERE id_role_user = ?
					AND status_deleted_user = 0";
			$data = [ROLE_SUPER_ADMINISTRATOR];
			if ($exclude_user !== null) {
				$sql .= " AND id_user != ?";
				$data[] = $exclude_user;
			}
			$request = $this->select($sql, $data);
			return (int)($request['total'] ?? 0);
		}

		public function basic_user(int $id_user): array
		{
			$sql = "SELECT id_user, name_user, id_role_user, status_active_user, status_deleted_user
					FROM users
					WHERE id_user = ?";
			$request = $this->select($sql, [$id_user]);
			return is_array($request) ? $request : [];
		}

		public function check_name_user_exists(string $name_user)
		{
			$this->str_name_user = $name_user;
			$sql = "SELECT * FROM users WHERE name_user = ?";
			$data = [
				$this->str_name_user
			];
			$request = $this->select($sql, $data);
			return $request;
		}

		public function get_user_status(int $id_user)
		{
			$this->int_id_user = $id_user;
			$sql = "SELECT u.status_active_user, u.id_role_user, r.name_role 
					FROM users u INNER JOIN roles r ON r.id_role = u.id_role_user 
					WHERE u.id_user = ? 
					AND u.status_deleted_user = 0 AND r.status_active_role = 1 AND r.status_deleted_role = 0";
			$data = [
				$this->int_id_user
			];
			$request = $this->select($sql, $data);
			return $request;
		}

		public function insert_user(
			int $id_employee, 
			string $name_user, 
			string $password_user, 
			int $id_role_user, 
			int $status_active_user
			){
			if ($id_role_user === ROLE_SUPER_ADMINISTRATOR) { return 0; }
			$this->int_id_employee = $id_employee; 
			$this->str_name_user = $name_user;
			$this->str_password_user = $password_user;
			$this->int_id_role_user = $id_role_user;
			$this->int_status_active_user = $status_active_user;
			$query_insert = "INSERT INTO users(id_role_user, id_employee_user, name_user, password_user, status_active_user) VALUES(?,?,?,?,?)";
			$data = [
				$this->int_id_role_user,
				$this->int_id_employee,
				$this->str_name_user,
				$this->str_password_user,
				$this->int_status_active_user
			];
			$request_insert = $this->insert($query_insert, $data);
	        $return = $request_insert;
	        return $return;
		}

		public function update_user(
			int $id_user, 
			int $id_employee,
			string $name_user, 
			string $password_user, 
			int $id_role_user, 
			int $status_active_user
			){
            $target = $this->basic_user($id_user);
            if ((int)($target['id_role_user'] ?? 0) === ROLE_SUPER_ADMINISTRATOR) {
                if ($id_role_user !== ROLE_SUPER_ADMINISTRATOR || $status_active_user !== 1) { return false; }
            } elseif ($id_role_user === ROLE_SUPER_ADMINISTRATOR) { return false; }
			$this->int_id_user = $id_user;
			$this->int_id_employee = $id_employee; 
			$this->str_name_user = $name_user;
			$this->str_password_user = $password_user;
			$this->int_id_role_user = $id_role_user;
			$this->int_status_active_user = $status_active_user;
			if ($this->str_password_user  != "") {
				$sql = "UPDATE users 
							SET id_role_user = ?, 
								id_employee_user = ?, 
								name_user = ?, 
								password_user = ?, 
								status_active_user = ? 
							WHERE id_user = ?";
				$data = [
					$this->int_id_role_user,
					$this->int_id_employee,
					$this->str_name_user,
					$this->str_password_user,
					$this->int_status_active_user,
					$this->int_id_user
				];
			} else {
				$sql = "UPDATE users 
							SET id_role_user = ?, 
								id_employee_user = ?, 
								name_user = ?, 
								status_active_user = ? 
							WHERE id_user = ?";
				$data = [
					$this->int_id_role_user,
					$this->int_id_employee,
					$this->str_name_user,
					$this->int_status_active_user,
					$this->int_id_user
				];
			}
			$request = $this->update($sql, $data);
			$return = $request;
	        return $return;
		}

		public function delete_user(int $id_user){
            if ((int)($this->basic_user($id_user)['id_role_user'] ?? 0) === ROLE_SUPER_ADMINISTRATOR) { return false; }
			$this->int_id_user = $id_user;
			$this->status_deleted_user = 1;
			$sql = "UPDATE users SET status_deleted_user = ? WHERE id_user = ?";
			$data = [
				$this->status_deleted_user,
				$this->int_id_user
			];
			$request = $this->update($sql, $data);
			$return = $request;
	        return $return;
		}

		public function show_end_users()
		{
			$sql = "SELECT 	id_user, 
							CONCAT(
								pt.abbreviation_professional_title, ' ',
								CASE e.preferred_name_employee
									WHEN 1 THEN e.first_name_employee
									WHEN 2 THEN e.middle_name_employee
								END, ' ',
								e.paternal_last_name_employee, ' ',
								e.maternal_last_name_employee)
								AS display_name
					FROM users u
					INNER JOIN employees e ON u.id_employee_user = e.id_employee
					INNER JOIN professional_titles pt ON e.id_professional_title_employee = pt.id_professional_title
					WHERE id_role_user IN (1, 4)
					AND status_active_user = 1 
					AND status_deleted_user = 0";
			$request = $this->select_all($sql);
			return $request;
		}

	}
?>
