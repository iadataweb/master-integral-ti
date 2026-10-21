<?php 

	class TypesResponsibleMaintenance extends Controllers{
		
		public function __construct()
		{
			parent::__construct();
			if (session_status() !== PHP_SESSION_ACTIVE) session_start();
			if(empty($_SESSION['login']))
			{
				header('Location: '.base_url().'/login');
				die();
			}
			check_user_status($_SESSION['user_data']['id_user']);
		}

		public function combo(){
			$data = $this->model->combo_types_responsible_maintenance();
			$html="";
			$html.="<option selected disabled value=''>Selecciona una opción</option>";
			if(is_array($data)==true and count($data)>0){
				foreach($data as $row)
				{
					$html.= "<option value='".$row['id_type_responsible_maintenance']."'>".$row['name_type_responsible_maintenance']."</option>";
				}
				echo $html;
			}
		}
	}
?>