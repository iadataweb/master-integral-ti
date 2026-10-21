<?php 

	class Employees extends Controllers {
		
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
		
        public function manage_employees()
		{
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			check_permissions(MODULE_ADMINISTRATION);
			if(empty($_SESSION['module_permissions']['r_read_permission'])){
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
				"functions_manage_employees.js"
			];
			$this->views->getView($this, "Administration", "index", $data);
		}

		public function show_employees()
		{
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			check_permissions(MODULE_ADMINISTRATION);
			if(empty($_SESSION['module_permissions']['r_read_permission'])){
				header("Location: " . base_url() . "/errors/forbidden");
				die();
			}
			$data = $this->model->show_employees();
			for ($i=0; $i < count($data); $i++) {
				$btnView = '';
				$btnEdit = '';
				$btnDelete = '';
				if($data[$i]['status_active_employee'] == 1){
					$data[$i]['status_active_employee'] = '<span class="badge bg-success">Activo</span>';
				}else{
					$data[$i]['status_active_employee'] = '<span class="badge bg-danger">Inactivo</span>';
				}
				if($_SESSION['module_permissions']['r_read_permission']){
					$btnView = '<button type="button" class="btn icon btn-secondary js-btn-view" data-id="'.$data[$i]['id_employee'].'" title="Ver"><i class="bx bx-eye"></i></button>';
				}
				if($_SESSION['module_permissions']['u_update_permission']){
					$btnEdit = '<button type="button" class="btn icon btn-primary js-btn-edit" data-id="'.$data[$i]['id_employee'].'" title="Editar"><i class="bx bx-pencil"></i></button>';
				}
				if($_SESSION['module_permissions']['d_delete_permission']){	
					$btnDelete = '<button type="button" class="btn icon btn-danger js-btn-delete" data-id="'.$data[$i]['id_employee'].'" title="Eliminar"><i class="bx bx-trash"></i></button>';
				}
				$data[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnEdit.' '.$btnDelete.'</div>';
			}
			echo json_encode($data, JSON_UNESCAPED_UNICODE);
		}

		public function show_employee() 
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_ADMINISTRATION);
			if (!$_SESSION['module_permissions']['r_read_permission']) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para leer el registro.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_employee'])) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrió un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_employee = trim($_POST['id_employee']);
				$request_employee = $this->model->show_employee($int_id_employee);
				if ($request_employee) {
					$response = array("status" => true, "result" => $request_employee);
				} else {
					$response = array("status" => false, "type" => "error", "message" => "No se pudo leer el registro.");
				}
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se enviaron datos.");
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}

		public function search_employee() 
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_ADMINISTRATION);
			if (!$_SESSION['module_permissions']['r_read_permission']) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para leer el registro.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {				
				if (empty($_POST['dni_employee'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese el DNI.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$str_dni_employee = trim($_POST['dni_employee']);
				$request_search_employee = $this->model->search_employee($str_dni_employee);
				if ($request_search_employee) {
					$response = array("status" => true, "result" => $request_search_employee);
				} else {
					$response = array("status" => false, "type" => "error", "message" => "No se pudo leer el registro.");
				}
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se enviaron datos.");
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}
		
		public function insert_employee() 
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_ADMINISTRATION);
			if (!$_SESSION['module_permissions']['w_write_permission']) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para registrar nueva categoría.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['first_name_employee'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese el primer nombre.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['paternal_last_name_employee'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese el apellido paterno.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['maternal_last_name_employee'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese el apellido materno.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['preferred_name_employee'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de nombre preferido.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['dni_employee'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese el DNI.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (!empty($_POST['cell_phone_employee'])) {
					if (!filter_var($_POST['cell_phone_employee'], FILTER_VALIDATE_INT)) {
					$response = array("status" => false, "type" => "error", "message" => "El número de celular no es válido.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
					}
					if (strlen($_POST['cell_phone_employee']) !== 9) {
						$response = array("status" => false, "type" => "error", "message" => "El número de celular debe tener exactamente 9 dígitos.");
						echo json_encode($response, JSON_UNESCAPED_UNICODE);
						return;
					}
				}				
				if (!empty($_POST['email_employee'])) {
					if (!filter_var($_POST['email_employee'], FILTER_VALIDATE_EMAIL)) {
						$response = array("status" => false, "type" => "error", "message" => "El email ingresado no es válido.");
						echo json_encode($response, JSON_UNESCAPED_UNICODE);
						return;
					}
				}				
				if (empty($_POST['id_area'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de área.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['id_position'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de cargo.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['id_professional_title'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de título profesional.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (!isset($_POST['status_active_employee'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de estado.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$str_first_name_employee = trim($_POST['first_name_employee']);
				$str_middle_name_employee = trim($_POST['middle_name_employee'] ?? '');
				$str_middle_name_employee = $str_middle_name_employee === '' ? null : $str_middle_name_employee;
				$str_paternal_last_name_employee = trim($_POST['paternal_last_name_employee']);
				$str_maternal_last_name_employee = trim($_POST['maternal_last_name_employee']);
				$int_preferred_name_employee = trim($_POST['preferred_name_employee']);
				if ($int_preferred_name_employee == 2 && $str_middle_name_employee === null) {
					$response = array("status" => false, "type" => "error", "message" => "Para usar el segundo nombre como nombre preferido, primero debe ingresarlo.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$str_dni_employee = trim($_POST['dni_employee']);
				$str_cell_phone_employee = trim($_POST['cell_phone_employee'] ?? '');
				$str_email_employee = trim($_POST['email_employee'] ?? '');
				$str_email_employee = $str_email_employee === '' ? null : $str_email_employee;
				$int_id_area = trim($_POST['id_area']);
				$int_id_position = trim($_POST['id_position']);
				$int_id_professional_title = trim($_POST['id_professional_title']);
				$int_status_active_employee = trim($_POST['status_active_employee']);
				$request_employee = $this->model->insert_employee(
					$str_first_name_employee,
					$str_middle_name_employee,
					$str_paternal_last_name_employee,
					$str_maternal_last_name_employee,
					$int_preferred_name_employee,
					$str_dni_employee,
					$str_cell_phone_employee,
					$str_email_employee,
					$int_id_area,
					$int_id_position,
					$int_id_professional_title,
					$int_status_active_employee
				);
				if ($request_employee) {
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

		public function update_employee() 
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_ADMINISTRATION);
			if (!$_SESSION['module_permissions']['u_update_permission']) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para actualizar el registro.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_employee'])) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrió un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['first_name_employee'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese el primer nombre.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['paternal_last_name_employee'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese el apellido paterno.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['maternal_last_name_employee'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese el apellido materno.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['preferred_name_employee'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de nombre preferido.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['dni_employee'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese el DNI.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (!empty($_POST['cell_phone_employee'])) {
					if (!filter_var($_POST['cell_phone_employee'], FILTER_VALIDATE_INT)) {
					$response = array("status" => false, "type" => "error", "message" => "El número de celular no es válido.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
					}
					if (strlen($_POST['cell_phone_employee']) !== 9) {
						$response = array("status" => false, "type" => "error", "message" => "El número de celular debe tener exactamente 9 dígitos.");
						echo json_encode($response, JSON_UNESCAPED_UNICODE);
						return;
					}
				}				
				if (!empty($_POST['email_employee'])) {
					if (!filter_var($_POST['email_employee'], FILTER_VALIDATE_EMAIL)) {
						$response = array("status" => false, "type" => "error", "message" => "El email ingresado no es válido.");
						echo json_encode($response, JSON_UNESCAPED_UNICODE);
						return;
					}
				}						
				if (empty($_POST['id_area'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de área.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['id_position'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de cargo.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['id_professional_title'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de título profesional.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (!isset($_POST['status_active_employee'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de estado.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_employee = trim($_POST['id_employee']);
				$str_first_name_employee = trim($_POST['first_name_employee']);
				$str_middle_name_employee = trim($_POST['middle_name_employee'] ?? '');
				$str_middle_name_employee = $str_middle_name_employee === '' ? null : $str_middle_name_employee;
				$str_paternal_last_name_employee = trim($_POST['paternal_last_name_employee']);
				$str_maternal_last_name_employee = trim($_POST['maternal_last_name_employee']);
				$int_preferred_name_employee = trim($_POST['preferred_name_employee']);
				if ($int_preferred_name_employee == 2 && $str_middle_name_employee === null) {
					$response = array("status" => false, "type" => "error", "message" => "Para usar el segundo nombre como nombre preferido, primero debe ingresarlo.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$str_dni_employee = trim($_POST['dni_employee']);
				$str_cell_phone_employee = trim($_POST['cell_phone_employee'] ?? '');
				$str_email_employee = trim($_POST['email_employee'] ?? '');
				$str_email_employee = $str_email_employee === '' ? null : $str_email_employee;
				$int_id_area = trim($_POST['id_area']);
				$int_id_position = trim($_POST['id_position']);
				$int_id_professional_title = trim($_POST['id_professional_title']);
				$int_status_active_employee = trim($_POST['status_active_employee']);
				$request_employee = $this->model->update_employee(
					$int_id_employee, 
					$str_first_name_employee,
					$str_middle_name_employee,
					$str_paternal_last_name_employee,
					$str_maternal_last_name_employee,
					$int_preferred_name_employee,
					$str_dni_employee,
					$str_cell_phone_employee,
					$str_email_employee,
					$int_id_area,
					$int_id_position,
					$int_id_professional_title,
					$int_status_active_employee
				);
				if ($request_employee) {
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

		public function delete_employee() 
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_ADMINISTRATION);
			if (!$_SESSION['module_permissions']['d_delete_permission']) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para eliminar el registro.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_employee'])) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrió un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_employee = trim($_POST['id_employee']);
				$request_employee = $this->model->delete_employee($int_id_employee);
				if ($request_employee) {
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
			$data = $this->model->combo_employees();
			$html="";
			$html.="<option selected disabled value=''>Selecciona una opción</option>";
			if(is_array($data)==true and count($data)>0){
				foreach($data as $row)
				{
					$html.= "<option value='".$row['id_employee']."'>".$row['name_employee']."</option>";
				}
				echo $html;
			}
		}
	}
?>