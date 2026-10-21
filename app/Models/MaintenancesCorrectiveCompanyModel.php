<?php 
	class MaintenancesCorrectiveCompanyModel extends Mysql
	{
		public function __construct()
		{
			parent::__construct();
		}

        public function show_maintenance_corrective_company(int $id_maintenance_corrective)
		{
			$this->int_id_maintenance_corrective = $id_maintenance_corrective;
			$sql = "SELECT c.name_company, c.ruc_company, c.cell_phone_company, c.telephone_company, c.email_company, c.description_company, c.observation_company 
                    FROM maintenances_corrective_company mpc 
                    INNER JOIN companies c ON mpc.id_company = c.id_company
                    WHERE mpc.id_maintenance_corrective = $this->int_id_maintenance_corrective AND mpc.status_deleted_maintenance_corrective_company = 0";
			$request = $this->select($sql);
			return $request;
		}
	}
?>
