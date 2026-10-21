<?php 

	class Companies extends Controllers{
		
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
			enforce_parameter_permission(MODULE_ADMINISTRATION, 'Administracion', 'Empresas', 'Ver');
		}
		
        public function manage_companies()
		{
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			check_permissions(MODULE_INVENTORY);
			if(empty($_SESSION['module_permissions']['r_read_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Empresas', 'Ver')){
				header("Location:".base_url().'/errors/forbidden');
				die();
			}
            $data['css'] = [
				"/extensions/datatables/datatables.min.css"
			];
			$data['script'] = [
				"/js/language_datatable.js",
				"/extensions/datatables/datatables.min.js"
			];
			$data['page_functions_js'] = [
				"functions_manage_companies.js"
			];
			$this->views->getView($this, "Administration", "index", $data);
		}

		public function show_companies()
		{
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			check_permissions(MODULE_INVENTORY);
			if(empty($_SESSION['module_permissions']['r_read_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Empresas', 'Ver')){
				header("Location: " . base_url() . "/errors/forbidden");
				die();
			}
			$data = $this->model->show_companies();
			for ($i=0; $i < count($data); $i++) {
				$btnView = '';
				$btnEdit = '';
				$btnDelete = '';
				if($data[$i]['status_active_company'] == 1){
					$data[$i]['status_active_company'] = '<span class="badge bg-success">Activo</span>';
				}else{
					$data[$i]['status_active_company'] = '<span class="badge bg-danger">Inactivo</span>';
				}
				if($_SESSION['module_permissions']['r_read_permission'] && parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Empresas', 'Ver')){
					$btnView = '<button type="button" class="btn icon btn-secondary js-btn-view" data-id="'.$data[$i]['id_company'].'" title="Ver"><i class="bx bx-eye"></i></button>';
				}
				if($_SESSION['module_permissions']['u_update_permission'] && parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Empresas', 'Editar')){
					$btnEdit = '<button type="button" class="btn icon btn-primary js-btn-edit" data-id="'.$data[$i]['id_company'].'" title="Editar"><i class="bx bx-pencil"></i></button>';
				}
				if($_SESSION['module_permissions']['d_delete_permission'] && parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Empresas', 'Eliminar')){	
					$btnDelete = '<button type="button" class="btn icon btn-danger js-btn-delete" data-id="'.$data[$i]['id_company'].'" title="Eliminar"><i class="bx bx-trash"></i></button>';
				}
				$data[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnEdit.' '.$btnDelete.'</div>';
			}
			echo json_encode($data, JSON_UNESCAPED_UNICODE);
		}

		public function show_company() 
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_INVENTORY);
			if (!$_SESSION['module_permissions']['r_read_permission'] || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Empresas', 'Ver')) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para leer el registro.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_company'])) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrió un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_company = trim($_POST['id_company']);
				$request_company = $this->model->show_company($int_id_company);
				if ($request_company) {
					$response = array("status" => true, "result" => $request_company);
				} else {
					$response = array("status" => false, "type" => "error", "message" => "No se pudo leer el registro.");
				}
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se enviaron datos.");
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}

		public function show_companies_existing()
		{
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			check_permissions(MODULE_INVENTORY);
			if(empty($_SESSION['module_permissions']['r_read_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Empresas', 'Ver')){
				header("Location: " . base_url() . "/errors/forbidden");
				die();
			}
			$data = $this->model->show_companies_existing();
			for ($i=0; $i < count($data); $i++) {
				$btnView = '';				
				if($_SESSION['module_permissions']['r_read_permission'] && parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Empresas', 'Ver')){
					$btnView = '<button type="button" class="btn icon btn-secondary js-btn-view" data-id="'.$data[$i]['id_company'].'" title="Ver"><i class="bx bx-eye"></i></button>';
				}
				$data[$i]['options'] = '<div class="text-center">'.$btnView.'</div>';
			}
			echo json_encode($data, JSON_UNESCAPED_UNICODE);
		}
		
		public function insert_company() 
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_INVENTORY);
			if (!$_SESSION['module_permissions']['w_write_permission'] || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Empresas', 'Crear')) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para registrar nueva empresa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['name_company'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese el nombre de la empresa.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['ruc_company'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese el RUC de la empresa.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (!empty($_POST['cell_phone_company'])) {
					if (!filter_var($_POST['cell_phone_company'], FILTER_VALIDATE_INT)) {
					$response = array("status" => false, "type" => "error", "message" => "El número de celular no es válido.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
					}
					if (strlen($_POST['cell_phone_company']) !== 9) {
						$response = array("status" => false, "type" => "error", "message" => "El número de celular debe tener exactamente 9 dígitos.");
						echo json_encode($response, JSON_UNESCAPED_UNICODE);
						return;
					}
				}	
				if (!empty($_POST['telephone_company'])) {
					if (empty($_POST['telephone_company'])) {
						$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese el teléfono fijo de la empresa.");
						echo json_encode($response, JSON_UNESCAPED_UNICODE);
						return;
					}
				}
				if (!empty($_POST['email_company'])) {
					if (!filter_var($_POST['email_company'], FILTER_VALIDATE_EMAIL)) {
						$response = array("status" => false, "type" => "error", "message" => "El email ingresado no es válido.");
						echo json_encode($response, JSON_UNESCAPED_UNICODE);
						return;
					}
				}
				if (!isset($_POST['status_active_company'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de estado.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$str_name_company = trim($_POST['name_company']);
				$str_ruc_company = trim($_POST['ruc_company']);
				$str_cell_phone_company = trim($_POST['cell_phone_company']);
				$str_telephone_company = trim($_POST['telephone_company']);
				$str_email_company = trim($_POST['email_company']);
				$str_description_company = trim($_POST['description_company'] ?? '') ?: 'Ninguna.';
				$str_observation_company = trim($_POST['observation_company'] ?? '') ?: 'Ninguna.';
				$int_status_active_company = trim($_POST['status_active_company']);
				$request_company = $this->model->insert_company(
					$str_name_company,
					$str_ruc_company,
					$str_cell_phone_company,
					$str_telephone_company,
					$str_email_company,
					$str_description_company,
					$str_observation_company,
					$int_status_active_company
				);
				if ($request_company) {
					$response = array("status" => true, "type" => "success", "message" => "Registrado correctamente.");
				} else {
					$response = array("status" => false, "type" => "error", "message" => "No se pudo registrar nueva empresa.");
				}
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se enviaron datos.");
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}

		public function update_company() 
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_INVENTORY);
			if (!$_SESSION['module_permissions']['u_update_permission'] || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Empresas', 'Editar')) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para actualizar el registro.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_company'])) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrió un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['name_company'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese el nombre de la empresa.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['ruc_company'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese el RUC de la empresa.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (!empty($_POST['cell_phone_company'])) {
					if (!filter_var($_POST['cell_phone_company'], FILTER_VALIDATE_INT)) {
					$response = array("status" => false, "type" => "error", "message" => "El número de celular no es válido.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
					}
					if (strlen($_POST['cell_phone_company']) !== 9) {
						$response = array("status" => false, "type" => "error", "message" => "El número de celular debe tener exactamente 9 dígitos.");
						echo json_encode($response, JSON_UNESCAPED_UNICODE);
						return;
					}
				}	
				if (!empty($_POST['telephone_company'])) {
					if (empty($_POST['telephone_company'])) {
						$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese el teléfono fijo de la empresa.");
						echo json_encode($response, JSON_UNESCAPED_UNICODE);
						return;
					}
				}
				if (!empty($_POST['email_company'])) {
					if (!filter_var($_POST['email_company'], FILTER_VALIDATE_EMAIL)) {
						$response = array("status" => false, "type" => "error", "message" => "El email ingresado no es válido.");
						echo json_encode($response, JSON_UNESCAPED_UNICODE);
						return;
					}
				}
				if (!isset($_POST['status_active_company'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de estado.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_company = trim($_POST['id_company']);
				$str_name_company = trim($_POST['name_company']);
				$str_ruc_company = trim($_POST['ruc_company']);
				$str_cell_phone_company = trim($_POST['cell_phone_company']);
				$str_telephone_company = trim($_POST['telephone_company']);
				$str_email_company = trim($_POST['email_company']);
				$str_description_company = trim($_POST['description_company'] ?? '') ?: 'Ninguna.';
				$str_observation_company = trim($_POST['observation_company'] ?? '') ?: 'Ninguna.';
				$int_status_active_company = trim($_POST['status_active_company']);
				$request_company = $this->model->update_company(
					$int_id_company,
					$str_name_company,
					$str_ruc_company,
					$str_cell_phone_company,
					$str_telephone_company,
					$str_email_company,
					$str_description_company,
					$str_observation_company,
					$int_status_active_company
				);
				if ($request_company) {
					$response = array("status" => true, "type" => "success", "message" => "Actualizado correctamente.");
				} else {
					$response = array("status" => false, "type" => "error", "message" => "No se pudo actualizar el registro.");
				}
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se enviaron datos.");
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}

		public function delete_company() 
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_INVENTORY);
			if (!$_SESSION['module_permissions']['d_delete_permission'] || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Empresas', 'Eliminar')) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para eliminar el registro.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_company'])) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrió un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_company = trim($_POST['id_company']);
				$request_company = $this->model->delete_company($int_id_company);
				if ($request_company) {
					$response = array("status" => true, "type" => "success", "message" => "Eliminado correctamente.");
				} else {
					$response = array("status" => false, "type" => "error", "message" => "No se pudo eliminar el registro.");
				}
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se enviaron datos.");
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}
	}
?>