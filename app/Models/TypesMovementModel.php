<?php 
	class TypesMovementModel extends Mysql
	{
		public function __construct()
		{
			parent::__construct();
		}

        public function combo_types_movement()
		{
			$sql = "SELECT id_type_movement, name_type_movement FROM types_movement WHERE status_active_type_movement = 1 AND status_deleted_type_movement = 0";
			$request = $this->select_all($sql);
			return $request;
		}
	}
?>