<?php 
	class TypesResponsibleMaintenanceModel extends Mysql
	{
		public function __construct()
		{
			parent::__construct();
		}

        public function combo_types_responsible_maintenance()
		{
			$sql = "SELECT id_type_responsible_maintenance, name_type_responsible_maintenance FROM types_responsible_maintenance WHERE status_active_type_responsible_maintenance = 1 AND status_deleted_type_responsible_maintenance = 0";
			$request = $this->select_all($sql);
			return $request;
		}
	}
?>