<?php 
	class StatesMaintenanceModel extends Mysql
	{
		public function __construct()
		{
			parent::__construct();
		}

        public function combo_states_maintenance()
		{
			$sql = "SELECT id_status_maintenance, name_status_maintenance FROM states_maintenance WHERE status_active_status_maintenance = 1 AND status_deleted_status_maintenance = 0";
			$request = $this->select_all($sql);
			return $request;
		}
	}
?>