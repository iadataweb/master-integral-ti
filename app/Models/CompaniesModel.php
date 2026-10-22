<?php 
	class CompaniesModel extends Mysql {
		private $str_cell_phone_company;
		private $str_description_company;
		private $str_email_company;
		private $str_observation_company;
		private $str_ruc_company;
		private $str_telephone_company;
		private $int_id_company;
		private $str_name_company;
		private $int_status_active_company;
		private $int_status_deleted_company;

		public function __construct()
		{
			parent::__construct();
		}

		public function show_companies()
		{
			$sql = "SELECT id_company, name_company, ruc_company, cell_phone_company, telephone_company, email_company, status_active_company, date_created_company FROM companies WHERE status_deleted_company = 0";
			$request = $this->select_all($sql);
			return $request;
		}

		public function show_company(int $id_company)
		{
			$this->int_id_company = $id_company;
			$sql = "SELECT name_company, ruc_company, cell_phone_company, telephone_company, email_company, description_company, observation_company, status_active_company FROM companies WHERE id_company = $this->int_id_company AND status_deleted_company = 0";
			$request = $this->select($sql);
			return $request;
		}

		public function show_companies_existing()
		{
			$sql = "SELECT  id_company, name_company, ruc_company, cell_phone_company, 
							telephone_company, email_company, date_created_company 
					FROM companies WHERE status_active_company = 1 AND status_deleted_company = 0";       
			$request = $this->select_all($sql);
			return $request;
		}

		public function insert_company(
			string $name_company, 
			string $ruc_company,
			string $cell_phone_company,
			string $telephone_company,
			string $email_company,
			string $description_company,
			string $observation_company,
			int $status_active_company
			){	
			$this->str_name_company = $name_company;
			$this->str_ruc_company = $ruc_company;
			$this->str_cell_phone_company = $cell_phone_company;
			$this->str_telephone_company = $telephone_company;
			$this->str_email_company = $email_company;
			$this->str_description_company = $description_company;
			$this->str_observation_company = $observation_company;
			$this->int_status_active_company = $status_active_company;
			$query_insert = "INSERT INTO companies(name_company, ruc_company, cell_phone_company, telephone_company, email_company, description_company, observation_company, status_active_company) VALUES(?,?,?,?,?,?,?,?)";
			$data = array(
				$this->str_name_company,
				$this->str_ruc_company,
				$this->str_cell_phone_company,
				$this->str_telephone_company,
				$this->str_email_company,
				$this->str_description_company,
				$this->str_observation_company,
				$this->int_status_active_company
			);
			$request_insert = $this->insert($query_insert, $data);
	        $return = $request_insert;
	        return $return;
		}

		public function update_company(
			int $id_company, 
			string $name_company, 
			string $ruc_company,
			string $cell_phone_company,
			string $telephone_company,
			string $email_company,
			string $description_company,
			string $observation_company,
			int $status_active_company
			){
			$this->int_id_company = $id_company;
			$this->str_name_company = $name_company;
			$this->str_ruc_company = $ruc_company;
			$this->str_cell_phone_company = $cell_phone_company;
			$this->str_telephone_company = $telephone_company;
			$this->str_email_company = $email_company;
			$this->str_description_company = $description_company;
			$this->str_observation_company = $observation_company;
			$this->int_status_active_company = $status_active_company;
			$sql = "UPDATE companies 
						SET name_company = ?,
							ruc_company = ?, 
							cell_phone_company = ?, 
							telephone_company = ?, 
							email_company = ?, 
							description_company = ?, 
							observation_company = ?, 
							status_active_company = ? 
						WHERE id_company = $this->int_id_company";
			$data = array(
				$this->str_name_company,
				$this->str_ruc_company,
				$this->str_cell_phone_company,
				$this->str_telephone_company,
				$this->str_email_company,
				$this->str_description_company,
				$this->str_observation_company,
				$this->int_status_active_company
			);
			$request = $this->update($sql, $data);
			$return = $request;
	        return $return;
		}

		public function delete_company(int $id_company){
			$this->int_id_company = $id_company;
			$this->int_status_deleted_company = 1;
			$sql = "UPDATE companies SET status_deleted_company = ? WHERE id_company = $this->int_id_company";
			$data = array(
				$this->int_status_deleted_company
			);
			$request = $this->update($sql, $data);
			$return = $request;
	        return $return;
		}
	}
?>