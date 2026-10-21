<?php 
	class MaintenancesPreventiveCompanyModel extends Mysql
	{
		public function __construct()
		{
			parent::__construct();
		}

        public function show_maintenance_preventive_company(int $id_maintenance_preventive)
		{
			$this->int_id_maintenance_preventive = $id_maintenance_preventive;
			$sql = "SELECT c.name_company, c.ruc_company, c.cell_phone_company, c.telephone_company, c.email_company, c.description_company, c.observation_company 
                    FROM maintenances_preventive_company mpc 
                    INNER JOIN companies c ON mpc.id_company = c.id_company
                    WHERE mpc.id_maintenance_preventive = $this->int_id_maintenance_preventive AND mpc.status_deleted_maintenance_preventive_company = 0";
			$request = $this->select($sql);
			return $request;
		}
	}
?>