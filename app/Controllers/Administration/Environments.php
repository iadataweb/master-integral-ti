<?php

	class Environments extends Controllers{

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
			enforce_parameter_permission(MODULE_ADMINISTRATION, 'Administracion', 'Ambientes', 'Ver');
		}

		public function manage_environments()
		{
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			check_permissions(MODULE_ADMINISTRATION);
			if(empty($_SESSION['module_permissions']['r_read_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Ambientes', 'Ver')){
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
				"functions_manage_environments.js"
			];
			$this->views->getView($this, "Administration", "index", $data);
		}

		public function show_environments()
		{
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			check_permissions(MODULE_ADMINISTRATION);
			if(empty($_SESSION['module_permissions']['r_read_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Ambientes', 'Ver')){
				header("Location: " . base_url() . "/errors/forbidden");
				die();
			}
			$data = $this->model->show_environments();
			for ($i=0; $i < count($data); $i++) {
				$btnView = '';
				$btnEdit = '';
				$btnDelete = '';
				if($data[$i]['status_active_environment'] == 1){
					$data[$i]['status_active_environment'] = '<span class="badge bg-success">Activo</span>';
				}else{
					$data[$i]['status_active_environment'] = '<span class="badge bg-danger">Inactivo</span>';
				}
				if($_SESSION['module_permissions']['r_read_permission'] && parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Ambientes', 'Ver')){
					$btnView = '<button type="button" class="btn icon btn-secondary js-btn-view" data-id="'.$data[$i]['id_environment'].'" title="Ver"><i class="bx bx-eye"></i></button>';
				}
				if($_SESSION['module_permissions']['u_update_permission'] && parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Ambientes', 'Editar')){
					$btnEdit = '<button type="button" class="btn icon btn-primary js-btn-edit" data-id="'.$data[$i]['id_environment'].'" title="Editar"><i class="bx bx-pencil"></i></button>';
				}
				if($_SESSION['module_permissions']['d_delete_permission'] && parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Ambientes', 'Eliminar')){
					$btnDelete = '<button type="button" class="btn icon btn-danger js-btn-delete" data-id="'.$data[$i]['id_environment'].'" title="Eliminar"><i class="bx bx-trash"></i></button>';
				}
				$data[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnEdit.' '.$btnDelete.'</div>';
			}
			echo json_encode($data, JSON_UNESCAPED_UNICODE);
		}

		public function show_environments_available()
		{
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			// This read-only endpoint is also used by the movement form's environment selector.
			// Its access must be based on the specific Ambientes permission, not on the
			// general Administración module permission, which movement-only roles do not have.
			if (!parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Ambientes', 'Ver')) {
				header("Location: " . base_url() . "/errors/forbidden");
				die();
			}
			$data = $this->model->show_environments_available();
			echo json_encode($data, JSON_UNESCAPED_UNICODE);
		}

		public function show_environment()
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			// Keep the selector usable from Movimiento for roles that have only the
			// read permission for Ambientes. This action does not modify environments.
			if (!parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Ambientes', 'Ver')) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para leer el registro.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				$id_environment = $_POST['id_environment'] ?? $_POST['id_space'] ?? null;
				if (empty($id_environment)) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrio un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_environment = trim($id_environment);
				$request_environment = $this->model->show_environment($int_id_environment);
				if ($request_environment) {
					$response = array("status" => true, "result" => $request_environment);
				} else {
					$response = array("status" => false, "type" => "error", "message" => "No se pudo leer el registro.");
				}
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se enviaron datos.");
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}

		public function insert_environment()
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_ADMINISTRATION);
			if (!$_SESSION['module_permissions']['w_write_permission'] || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Ambientes', 'Crear')) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para registrar nuevo ambiente.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				$response = $this->validate_environment_payload();
				if ($response !== true) {
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$request_environment = $this->model->insert_environment(
					trim($_POST['id_campus_environment']),
					trim($_POST['name_environment']),
					trim($_POST['floor_environment']),
					trim($_POST['use_environment'] ?? '') ?: 'Ninguna.',
					trim($_POST['status_active_environment'])
				);
				if ($request_environment) {
					$response = array("status" => true, "type" => "success", "message" => "Registrado correctamente.");
				} else {
					$response = array("status" => false, "type" => "error", "message" => "No se pudo registrar nuevo ambiente.");
				}
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se enviaron datos.");
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}

		public function update_environment()
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_ADMINISTRATION);
			if (!$_SESSION['module_permissions']['u_update_permission'] || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Ambientes', 'Editar')) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para actualizar el registro.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_environment'])) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrio un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$response = $this->validate_environment_payload();
				if ($response !== true) {
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$request_environment = $this->model->update_environment(
					trim($_POST['id_environment']),
					trim($_POST['id_campus_environment']),
					trim($_POST['name_environment']),
					trim($_POST['floor_environment']),
					trim($_POST['use_environment'] ?? '') ?: 'Ninguna.',
					trim($_POST['status_active_environment'])
				);
				if ($request_environment) {
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

		public function delete_environment()
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_ADMINISTRATION);
			if (!$_SESSION['module_permissions']['d_delete_permission'] || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Ambientes', 'Eliminar')) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para eliminar el registro.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_environment'])) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrio un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$request_environment = $this->model->delete_environment(trim($_POST['id_environment']));
				if ($request_environment) {
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

		public function combo()
		{
			$data = $this->model->combo_environments();
			$html = "<option selected disabled value=''>Selecciona una opcion</option>";
			if(is_array($data) && count($data)>0){
				foreach($data as $row)
				{
					$html .= "<option value='".$row['id_environment']."'>".'Piso '.$row['floor_environment'].' - '.$row['name_environment']."</option>";
				}
			}
			echo $html;
		}

		private function validate_environment_payload()
		{
			if (!isset($_POST['id_campus_environment'])) {
				return array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opcion de establecimiento.");
			}
			if (empty($_POST['name_environment'])) {
				return array("status" => false, "type" => "error", "message" => "Por favor, ingrese el nombre del ambiente.");
			}
			if (empty($_POST['floor_environment'])) {
				return array("status" => false, "type" => "error", "message" => "Por favor, ingrese el piso.");
			}
			if (!filter_var($_POST['floor_environment'], FILTER_VALIDATE_INT)) {
				return array("status" => false, "type" => "error", "message" => "El valor ingresado no es valido.");
			}
			if (!isset($_POST['status_active_environment'])) {
				return array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opcion de estado.");
			}
			return true;
		}
	}
?>
