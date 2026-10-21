<?php 

	class ProfessionalTitles extends Controllers{
		
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
			enforce_parameter_permission(MODULE_ADMINISTRATION, 'Administracion', 'Titulos Profesionales', 'Ver');
		}
		
        public function manage_professional_titles()
		{
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			check_permissions(MODULE_ADMINISTRATION);
			if(empty($_SESSION['module_permissions']['r_read_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Titulos Profesionales', 'Ver')){
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
				"functions_manage_professional_titles.js"
			];
			$this->views->getView($this, "Administration", "index", $data);
		}

		public function show_professional_titles()
		{
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			check_permissions(MODULE_ADMINISTRATION);
			if(empty($_SESSION['module_permissions']['r_read_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Titulos Profesionales', 'Ver')){
				header("Location: " . base_url() . "/errors/forbidden");
				die();
			}
			$data = $this->model->show_professional_titles();
			for ($i=0; $i < count($data); $i++) {
				$btnView = '';
				$btnEdit = '';
				$btnDelete = '';
				if($data[$i]['status_active_professional_title'] == 1){
					$data[$i]['status_active_professional_title'] = '<span class="badge bg-success">Activo</span>';
				}else{
					$data[$i]['status_active_professional_title'] = '<span class="badge bg-danger">Inactivo</span>';
				}
				if($_SESSION['module_permissions']['r_read_permission'] && parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Titulos Profesionales', 'Ver')){
					$btnView = '<button type="button" class="btn icon btn-secondary js-btn-view" data-id="'.$data[$i]['id_professional_title'].'" title="Ver"><i class="bx bx-eye"></i></button>';
				}
				if($_SESSION['module_permissions']['u_update_permission'] && parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Titulos Profesionales', 'Editar')){
					$btnEdit = '<button type="button" class="btn icon btn-primary js-btn-edit" data-id="'.$data[$i]['id_professional_title'].'" title="Editar"><i class="bx bx-pencil"></i></button>';
				}
				if($_SESSION['module_permissions']['d_delete_permission'] && parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Titulos Profesionales', 'Eliminar')){	
					$btnDelete = '<button type="button" class="btn icon btn-danger js-btn-delete" data-id="'.$data[$i]['id_professional_title'].'" title="Eliminar"><i class="bx bx-trash"></i></button>';
				}
				$data[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnEdit.' '.$btnDelete.'</div>';
			}
			echo json_encode($data, JSON_UNESCAPED_UNICODE);
		}

		public function show_professional_title() 
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_ADMINISTRATION);
			if (!$_SESSION['module_permissions']['r_read_permission'] || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Titulos Profesionales', 'Ver')) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para leer el registro.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_professional_title'])) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrió un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_professional_title = trim($_POST['id_professional_title']);
				$request_professional_title = $this->model->show_professional_title($int_id_professional_title);
				if ($request_professional_title) {
					$response = array("status" => true, "result" => $request_professional_title);
				} else {
					$response = array("status" => false, "type" => "error", "message" => "No se pudo leer el registro.");
				}
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se enviaron datos.");
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}
		
		public function insert_professional_title() 
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_ADMINISTRATION);
			if (!$_SESSION['module_permissions']['w_write_permission'] || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Titulos Profesionales', 'Crear')) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para registrar nueva categoría.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['name_professional_title'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese el nombre de categoría.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['abbreviation_professional_title'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese la abreviatura del título profesional.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (!isset($_POST['status_active_professional_title'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de estado.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$str_name_professional_title = trim($_POST['name_professional_title']);
				$str_abbreviation_professional_title = trim($_POST['abbreviation_professional_title']);
				$int_status_active_professional_title = trim($_POST['status_active_professional_title']);
				$request_professional_title = $this->model->insert_professional_title(
					$str_name_professional_title,
					$str_abbreviation_professional_title,
					$int_status_active_professional_title
				);
				if ($request_professional_title) {
					$response = array("status" => true, "type" => "success", "message" => "Registrado correctamente.");
				} else {
					$response = array("status" => false, "type" => "error", "message" => "No se pudo registrar nueva categoría.");
				}
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se enviaron datos.");
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}

		public function update_professional_title() 
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_ADMINISTRATION);
			if (!$_SESSION['module_permissions']['u_update_permission'] || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Titulos Profesionales', 'Editar')) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para actualizar el registro.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_professional_title'])) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrió un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['name_professional_title'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese el nombre de categoría.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['abbreviation_professional_title'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese la abreviatura del título profesional.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (!isset($_POST['status_active_professional_title'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de estado.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_professional_title = trim($_POST['id_professional_title']);
				$str_name_professional_title = trim($_POST['name_professional_title']);
				$str_abbreviation_professional_title = trim($_POST['abbreviation_professional_title']);
				$int_status_active_professional_title = trim($_POST['status_active_professional_title']);
				$request_professional_title = $this->model->update_professional_title(
					$int_id_professional_title, 
					$str_name_professional_title,
					$str_abbreviation_professional_title,
					$int_status_active_professional_title
				);
				if ($request_professional_title) {
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

		public function delete_professional_title() 
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_ADMINISTRATION);
			if (!$_SESSION['module_permissions']['d_delete_permission'] || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Titulos Profesionales', 'Eliminar')) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para eliminar el registro.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_professional_title'])) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrió un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_professional_title = trim($_POST['id_professional_title']);
				$request_professional_title = $this->model->delete_professional_title($int_id_professional_title);
				if ($request_professional_title) {
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

		public function combo(){
			$data = $this->model->combo_professional_titles();
			$html="";
			$html.="<option selected disabled value=''>Selecciona una opción</option>";
			if(is_array($data)==true and count($data)>0){
				foreach($data as $row)
				{
					$html.= "<option value='".$row['id_professional_title']."'>".$row['name_professional_title']."</option>";
				}
				echo $html;
			}
		}
	}
?>