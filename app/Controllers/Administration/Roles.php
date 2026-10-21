<?php 

	class Roles extends Controllers{
		
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
			enforce_parameter_permission(MODULE_ADMINISTRATION, 'Administracion', 'Roles', 'Ver');
		}

        public function manage_roles()
		{
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			check_permissions(MODULE_ADMINISTRATION);
			if(empty($_SESSION['module_permissions']['r_read_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Roles', 'Ver')){
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
				"functions_manage_roles.js"
			];
			$this->views->getView($this, "Administration", "manage", $data);
		}

		public function show_roles()
		{
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			check_permissions(MODULE_ADMINISTRATION);
			if(empty($_SESSION['module_permissions']['r_read_permission']) || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Roles', 'Ver')){
				header("Location:".base_url().'/errors/forbidden');
				die();
			}
			$data = $this->model->show_roles();
			for ($i=0; $i < count($data); $i++) {
				$btnView = '';
				$btnAccess = '';
				$btnEdit = '';
				$btnDelete = '';
				$isProtected = !empty($data[$i]['protected_role']);
				if($data[$i]['status_active_role'] == 1){
					$data[$i]['status_active_role'] = '<span class="badge bg-success">Activo</span>';
				}else{
					$data[$i]['status_active_role'] = '<span class="badge bg-danger">Inactivo</span>';
				}
				$data[$i]['protected_role'] = $isProtected
					? '<span class="badge bg-info">Protegido</span>'
					: '<span class="badge bg-secondary">Personalizado</span>';
				if($_SESSION['module_permissions']['r_read_permission'] && parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Roles', 'Ver')){
					$btnView = '<button type="button" class="btn icon btn-secondary js-btn-view" data-id="'.$data[$i]['id_role'].'" title="Ver"><i class="bx bx-eye"></i></button>';
				}
				if($_SESSION['module_permissions']['u_update_permission'] && parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Roles', 'Editar')){
					$btnAccess = ($isProtected && !$this->currentUserIsSuperAuthority())
						? '<button type="button" class="btn icon btn-warning" disabled title="Super Administrador protegido"><i class="bx bxs-lock-open-alt"></i></button>'
						: '<a class="btn icon btn-warning" href="'.base_url().'/administration/roles/access_manager/'.$data[$i]['id_role'].'" title="Gestionar Permisos"><i class="bx bxs-lock-open-alt"></i></a>';
				}
				if($_SESSION['module_permissions']['u_update_permission'] && parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Roles', 'Editar')){
					$btnEdit = $isProtected
						? '<button type="button" class="btn icon btn-primary" disabled title="Rol protegido"><i class="bx bx-pencil"></i></button>'
						: '<button type="button" class="btn icon btn-primary js-btn-edit" data-id="'.$data[$i]['id_role'].'" title="Editar"><i class="bx bx-pencil"></i></button>';
				}
				if($_SESSION['module_permissions']['d_delete_permission'] && parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Roles', 'Eliminar')){	
					$btnDelete = $isProtected
						? '<button type="button" class="btn icon btn-danger" disabled title="Rol protegido"><i class="bx bx-trash"></i></button>'
						: '<button type="button" class="btn icon btn-danger js-btn-delete" data-id="'.$data[$i]['id_role'].'" title="Eliminar"><i class="bx bx-trash"></i></button>';
				}
				$data[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnAccess.' '.$btnEdit.' '.$btnDelete.'</div>';
			}
			echo json_encode($data, JSON_UNESCAPED_UNICODE);
		}

		public function show_role() {
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_ADMINISTRATION);
			if (!$_SESSION['module_permissions']['r_read_permission'] || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Roles', 'Ver')) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para leer el registro.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_role'])) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrió un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_role = trim($_POST['id_role']);
				$request_role = $this->model->show_role($int_id_role);
				if ($request_role) {
					$response = array("status" => true, "result" => $request_role);
				} else {
					$response = array("status" => false, "type" => "error", "message" => "No se pudo leer el registro.");
				}
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se enviaron datos.");
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}
		
		public function insert_role() {
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_ADMINISTRATION);
			if (!$_SESSION['module_permissions']['w_write_permission'] || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Roles', 'Crear')) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para registrar nuevo rol.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['name_role'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese el nombre de rol.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['description_role'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese la descripción de rol.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (!isset($_POST['status_active_role'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de estado.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$str_name_role = trim($_POST['name_role']);
				$str_description_role = trim($_POST['description_role']);
				$int_status_active_role = trim($_POST['status_active_role']);
				$request_role = $this->model->insert_role($str_name_role, $str_description_role, $int_status_active_role);
				if ($request_role) {
					$response = array("status" => true, "type" => "success", "message" => "Registrado correctamente.");
				} else {
					$response = array("status" => false, "type" => "error", "message" => "No se pudo registrar nuevo rol.");
				}
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se enviaron datos.");
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}

		public function update_role() {
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_ADMINISTRATION);
			if (!$_SESSION['module_permissions']['u_update_permission'] || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Roles', 'Editar')) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para actualizar el registro.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_role'])) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrió un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_role = trim($_POST['id_role']);
				if ($this->model->is_protected_role((int)$int_id_role)) {
					$response = array("status" => false, "type" => "error", "message" => "El rol Super Administrador es un rol protegido del sistema. Solo se permite gestionar sus permisos desde la pantalla de accesos.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['name_role'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese el nombre de rol.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['description_role'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese la descripción de rol.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (!isset($_POST['status_active_role'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de estado.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$str_name_role = trim($_POST['name_role']);
				$str_description_role = trim($_POST['description_role']);
				$int_status_active_role = trim($_POST['status_active_role']);

				if ((int)$int_id_role === (int)$_SESSION['user_data']['id_role'] && (int)$int_status_active_role === 0) {
					$response = array("status" => false, "type" => "error", "message" => "No puedes desactivar el rol que estas usando actualmente.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}

				$request_role = $this->model->update_role($int_id_role, $str_name_role, $str_description_role, $int_status_active_role);
				if ($request_role) {
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

		public function delete_role() {
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_ADMINISTRATION);
			if (!$_SESSION['module_permissions']['d_delete_permission'] || !parameter_permission_allowed(MODULE_ADMINISTRATION, 'Administracion', 'Roles', 'Eliminar')) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para eliminar el registro.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_role'])) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrió un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_role = trim($_POST['id_role']);
				if ($this->model->is_protected_role((int)$int_id_role)) {
					$response = array("status" => false, "type" => "error", "message" => "El rol Super Administrador es un rol protegido y esencial para el funcionamiento del sistema.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if ((int)$int_id_role === (int)$_SESSION['user_data']['id_role']) {
					$response = array("status" => false, "type" => "error", "message" => "No puedes eliminar el rol que estas usando actualmente.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$request_role = $this->model->delete_role($int_id_role);
				if ($request_role) {
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

		public function combo_roles(){
			$data = $this->model->combo_roles();
			$html="";
			$html.="<option selected disabled value=''>Selecciona una opción</option>";
			if(is_array($data) == true and count($data) > 0){
				foreach($data as $row)
				{
					$html.= "<option value='".$row['id_role']."'>".$row['name_role']."</option>";
				}
				echo $html;
			}
		}

		public function access_manager($id_role = null)
		{
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			check_permissions(MODULE_ADMINISTRATION);
			if(empty($_SESSION['module_permissions']['r_read_permission']) || empty($_SESSION['module_permissions']['u_update_permission'])){
				header("Location:".base_url().'/errors/forbidden');
				die();
			}
			$int_id_role = (int)$id_role;
			$role = $this->model->show_role($int_id_role);
			if (!$role) {
				header("Location:".base_url().'/administration/roles/manage_roles');
				die();
			}
			if ($this->model->is_protected_role($int_id_role) && !$this->currentUserIsSuperAuthority()) {
				header("Location:".base_url().'/errors/forbidden');
				die();
			}
			$data['css'] = [
				"/extensions/datatables/datatables.min.css"
			];
			$data['script'] = [
				"/extensions/datatables/datatables.min.js"
			];
			$data['page_functions_js'] = [
				"functions_access_manager_roles.js"
			];
			$data['role'] = $role;
			$data['matrix'] = $this->model->access_matrix($int_id_role);
			$data['is_protected'] = $this->model->is_protected_role($int_id_role);
			$this->views->getView($this, "Administration", "access-manager", $data);
		}

		public function save_access()
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_ADMINISTRATION);
			if(empty($_SESSION['module_permissions']['u_update_permission'])){
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para gestionar accesos.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (empty($_POST['id_role'])) {
				$response = array("status" => false, "type" => "error", "message" => "No se recibio el rol a actualizar.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			$int_id_role = (int)$_POST['id_role'];
			$role = $this->model->show_role($int_id_role);
			if (!$role) {
				$response = array("status" => false, "type" => "error", "message" => "El rol seleccionado no existe.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if ($this->model->is_protected_role($int_id_role) && !$this->currentUserIsSuperAuthority()) {
				$response = array("status" => false, "type" => "error", "message" => "Solo el Super Administrador puede gestionar los permisos del rol Super Administrador.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			$modules = $_POST['modules'] ?? [];
			$controls = $_POST['controls'] ?? [];
			if ($int_id_role === (int)$_SESSION['user_data']['id_role']) {
				$admin = $modules[MODULE_ADMINISTRATION] ?? [];
				if (empty($admin['r']) || empty($admin['u'])) {
					$response = array("status" => false, "type" => "error", "message" => "No puedes retirar tus propios permisos de lectura y actualizacion de Administracion.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
			}
			$request = $this->model->save_role_access($int_id_role, $modules, $controls);
			if ($request) {
				$response = array("status" => true, "type" => "success", "message" => "Permisos actualizados correctamente.");
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se pudieron actualizar los permisos.");
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}

		private function currentUserIsSuperAuthority(): bool
		{
			return (int)($_SESSION['user_data']['id_role'] ?? 0) === ROLE_SUPER_ADMINISTRATOR;
		}







	}
?>
