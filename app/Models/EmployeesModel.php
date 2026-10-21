<?php 
	class EmployeesModel extends Mysql
	{
		private $int_id_employee;
		private $str_name_employee;
		private $int_status_active_employee;
		private $int_status_deleted_employee;

		public function __construct()
		{
			parent::__construct();
		}

		public function show_employees()
		{
			$sql = "SELECT id_employee, first_name_employee, middle_name_employee, paternal_last_name_employee, maternal_last_name_employee, status_active_employee, date_created_employee FROM employees WHERE status_deleted_employee = 0";
			$request = $this->select_all($sql);
			return $request;
		}

		public function show_employee(int $id_employee)
		{
			$this->int_id_employee = $id_employee;
			$sql = "SELECT e.first_name_employee, e.middle_name_employee, e.paternal_last_name_employee, 
					e.maternal_last_name_employee, e.preferred_name_employee, e.dni_employee, e.cell_phone_employee, 
					e.email_employee, e.status_active_employee, a.id_area, a.name_area, p.id_position, p.name_position, 
					pt.id_professional_title, pt.name_professional_title 
					FROM employees e 
					INNER JOIN areas a ON e.id_area_employee = a.id_area
					INNER JOIN positions p ON e.id_position_employee = p.id_position
					INNER JOIN professional_titles pt ON e.id_professional_title_employee = pt.id_professional_title
					WHERE e.id_employee = ? AND e.status_deleted_employee = 0";
			$data = [
				$this->int_id_employee
			];
			$request = $this->select($sql, $data);
			return $request;
		}

		public function search_employee(string $dni_employee)
		{
			$this->str_dni_employee = $dni_employee;
			$sql = "SELECT CONCAT(
								pt.abbreviation_professional_title, ' ',
								COALESCE(
									CASE e.preferred_name_employee
										WHEN 2 THEN e.middle_name_employee
										ELSE e.first_name_employee
									END,
									e.first_name_employee
								), ' ',
								e.paternal_last_name_employee, ' ',
								e.maternal_last_name_employee)
								AS official_name_employee, e.id_employee, a.name_area, p.name_position
					FROM employees e 
					INNER JOIN areas a ON e.id_area_employee = a.id_area
					INNER JOIN positions p ON e.id_position_employee = p.id_position
					INNER JOIN professional_titles pt ON e.id_professional_title_employee = pt.id_professional_title
					WHERE e.dni_employee = ? AND e.status_active_employee = 1 AND e.status_deleted_employee = 0";
			$data = [
				$this->str_dni_employee
			];
			$request = $this->select($sql, $data);
			return $request;
		}

		public function insert_employee(
			string $first_name_employee, 
			?string $middle_name_employee,
			string $paternal_last_name_employee,
			string $maternal_last_name_employee,
			int $preferred_name_employee,
			string $dni_employee,
			string $cell_phone_employee,
			?string $email_employee,
			int $id_area,
			int $id_position,
			int $id_professional_title,
			int $status_active_employee
		){
			$this->str_first_name_employee = $first_name_employee;
			$this->str_middle_name_employee = $middle_name_employee;
			$this->str_paternal_last_name_employee = $paternal_last_name_employee;
			$this->str_maternal_last_name_employee = $maternal_last_name_employee;
			$this->int_preferred_name_employee = $preferred_name_employee;
			$this->str_dni_employee = $dni_employee;
			$this->str_cell_phone_employee = $cell_phone_employee;
			$this->str_email_employee = $email_employee;
			$this->int_id_area = $id_area;
			$this->int_id_position = $id_position;
			$this->int_id_professional_title = $id_professional_title;
			$this->int_status_active_employee = $status_active_employee;
			$query_insert = "INSERT INTO employees(
							first_name_employee,
							middle_name_employee,
							paternal_last_name_employee,
							maternal_last_name_employee,
							preferred_name_employee,
							dni_employee,
							cell_phone_employee,
							email_employee,
							id_area_employee,
							id_position_employee,
							id_professional_title_employee,
							status_active_employee) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)";
			$data = array(
				$this->str_first_name_employee,
				$this->str_middle_name_employee,
				$this->str_paternal_last_name_employee,
				$this->str_maternal_last_name_employee,
				$this->int_preferred_name_employee,
				$this->str_dni_employee,
				$this->str_cell_phone_employee,
				$this->str_email_employee,
				$this->int_id_area,
				$this->int_id_position,
				$this->int_id_professional_title,
				$this->int_status_active_employee
			);
			$request_insert = $this->insert($query_insert, $data);
	        $return = $request_insert;
	        return $return;
		}

		public function update_employee(
			int $id_employee, 
			string $first_name_employee, 
			?string $middle_name_employee,
			string $paternal_last_name_employee,
			string $maternal_last_name_employee,
			int $preferred_name_employee,
			string $dni_employee,
			string $cell_phone_employee,
			?string $email_employee,
			int $id_area,
			int $id_position,
			int $id_professional_title,
			int $status_active_employee
			){
			$this->int_id_employee = $id_employee;
			$this->str_first_name_employee = $first_name_employee;
			$this->str_middle_name_employee = $middle_name_employee;
			$this->str_paternal_last_name_employee = $paternal_last_name_employee;
			$this->str_maternal_last_name_employee = $maternal_last_name_employee;
			$this->int_preferred_name_employee = $preferred_name_employee;
			$this->str_dni_employee = $dni_employee;
			$this->str_cell_phone_employee = $cell_phone_employee;
			$this->str_email_employee = $email_employee;
			$this->int_id_area = $id_area;
			$this->int_id_position = $id_position;
			$this->int_id_professional_title = $id_professional_title;
			$this->int_status_active_employee = $status_active_employee;
			$sql = "UPDATE employees 
						SET first_name_employee = ?,
							middle_name_employee = ?,
							paternal_last_name_employee = ?,
							maternal_last_name_employee = ?,
							preferred_name_employee = ?,
							dni_employee = ?,
							cell_phone_employee = ?,
							email_employee = ?,
							id_area_employee = ?,
							id_position_employee = ?,
							id_professional_title_employee = ?,
							status_active_employee = ? 
						WHERE id_employee = $this->int_id_employee";
			$data = array(
				$this->str_first_name_employee,
				$this->str_middle_name_employee,
				$this->str_paternal_last_name_employee,
				$this->str_maternal_last_name_employee,
				$this->int_preferred_name_employee,
				$this->str_dni_employee,
				$this->str_cell_phone_employee,
				$this->str_email_employee,
				$this->int_id_area,
				$this->int_id_position,
				$this->int_id_professional_title,
				$this->int_status_active_employee
			);
			$request = $this->update($sql, $data);
			$return = $request;
	        return $return;
		}

		public function delete_employee(int $id_employee){
			$this->int_id_employee = $id_employee;
			$this->int_status_deleted_employee = 1;
			$sql = "UPDATE employees SET status_deleted_employee = ? WHERE id_employee = $this->int_id_employee";
			$data = array(
				$this->int_status_deleted_employee
			);
			$request = $this->update($sql, $data);
			$return = $request;
	        return $return;
		}

        public function combo_employees()
		{
			$sql = "SELECT e.id_employee,
					TRIM(CONCAT_WS(' ',
						NULLIF(pt.abbreviation_professional_title, ''),
						NULLIF(CASE e.preferred_name_employee
							WHEN 2 THEN COALESCE(NULLIF(e.middle_name_employee, ''), e.first_name_employee)
							ELSE e.first_name_employee
						END, ''),
						NULLIF(e.paternal_last_name_employee, ''),
						NULLIF(e.maternal_last_name_employee, '')
					)) AS name_employee
					FROM employees e
					LEFT JOIN professional_titles pt ON e.id_professional_title_employee = pt.id_professional_title
					WHERE e.status_active_employee = 1 AND e.status_deleted_employee = 0
					ORDER BY e.paternal_last_name_employee ASC, e.maternal_last_name_employee ASC, name_employee ASC";
			$request = $this->select_all($sql);
			return $request;
		}
	}
?>
