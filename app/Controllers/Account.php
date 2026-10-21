<?php 

	class Account extends Controllers{
		
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

		public function change_password()
		{
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			$data['page_functions_js'] = [
				"functions_change_password.js"
			];
			$this->views->getView($this, "", "change_password", $data);
		}

		public function profile()
		{
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			$data['profile_data'] = $this->model->get_profile_data((int)$_SESSION['user_data']['id_user']);
			$data['page_functions_js'] = [
				"functions_profile.js"
			];
			$this->views->getView($this, "", "profile", $data);
		}

		public function browser_sessions()
		{
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
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
				"functions_browser_sessions.js"
			];
			$this->views->getView($this, "", "browser_sessions", $data);
		}

		public function update_password()
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if(!empty($_POST)){
				if (empty($_POST['current_password'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese la contraseña actual.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['new_password'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese la nueva contraseña.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['repeat_password'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, repetir la nueva contraseña.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_user = $_SESSION['user_data']['id_user'];
				$str_current_password = $_POST['current_password'];
				$str_new_password = $_POST['new_password'];
				$str_repeat_password = $_POST['repeat_password'];
				$request_validate_password = $this->model->validate_password($int_id_user, $str_current_password);
				if ($request_validate_password) {
					if ($str_new_password === $str_repeat_password) {
						$hashed_password = password_hash($str_new_password, PASSWORD_DEFAULT);
						$request_update_password = $this->model->update_password($int_id_user, $hashed_password);
						if ($request_update_password) {
							$response = array("status" => true, "type" => "success", "message" => "La contraseña se actualizó correctamente.");
						} else {
							$response = array("status" => false, "type" => "error", "message" => "Error al actualizar la contraseña.");
						}
					} else {
						$response = array("status" => false, "type" => "error", "message" => "Las nuevas contraseñas no coinciden.");
					}
				} else {
					$response = array("status" => false, "type" => "error", "message" => "La contraseña actual no es válida.");
				}
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se enviaron datos.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
			}
			die();
		}

		public function update_theme_preference()
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}

			if (empty($_POST['theme_preference'])) {
				$response = array("status" => false, "type" => "error", "message" => "No se envio el tema.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}

			$theme = trim((string)$_POST['theme_preference']);
			if (!in_array($theme, ['light', 'dark'], true)) {
				$response = array("status" => false, "type" => "error", "message" => "Tema no valido.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}

			$request = $this->model->update_theme_preference((int)$_SESSION['user_data']['id_user'], $theme);
			if ($request) {
				session_user((int)$_SESSION['user_data']['id_user']);
				$response = array("status" => true, "type" => "success", "message" => "Tema actualizado.");
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se pudo actualizar el tema.");
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}

		public function update_profile_photo()
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (empty($_FILES['profile_photo'])) {
				$response = array("status" => false, "type" => "error", "message" => "No se envio una foto.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}

			$file = $_FILES['profile_photo'];
			if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
				$response = array("status" => false, "type" => "error", "message" => "Archivo no valido.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}

			if (!empty($file['size']) && (int)$file['size'] > (5 * 1024 * 1024)) {
				$response = array("status" => false, "type" => "error", "message" => "La foto excede el tamano maximo de 5 MB.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}

			$image_info = @getimagesize($file['tmp_name']);
			if ($image_info === false || empty($image_info['mime'])) {
				$response = array("status" => false, "type" => "error", "message" => "Solo se permiten imagenes.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}

			$allowed_mimes = [
				'image/jpeg' => 'jpg',
				'image/png' => 'png',
				'image/webp' => 'webp'
			];
			if (!isset($allowed_mimes[$image_info['mime']])) {
				$response = array("status" => false, "type" => "error", "message" => "Formato no permitido. Use JPG, PNG o WEBP.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}

			$id_user = (int)$_SESSION['user_data']['id_user'];
			$upload_dir = 'Assets/uploads/profile_user/' . $id_user . '/';
			if (!is_dir($upload_dir)) {
				mkdir($upload_dir, 0777, true);
			}

			$request_current = $this->model->get_profile_photo_by_user($id_user);
			$current_photo = $request_current['profile_photo_user'] ?? null;

			$file_extension = $allowed_mimes[$image_info['mime']];
			$new_file_name = 'PROFILE_' . uniqid((string)$id_user . '_', true) . '.' . $file_extension;
			$target_path = $upload_dir . $new_file_name;

			if (!move_uploaded_file($file['tmp_name'], $target_path)) {
				$response = array("status" => false, "type" => "error", "message" => "No se pudo guardar la foto.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}

			$request_update = $this->model->update_profile_photo($id_user, $target_path);
			if ($request_update) {
				if (!empty($current_photo) && file_exists($current_photo)) {
					@unlink($current_photo);
				}
				session_user($id_user);
				$response = array(
					"status" => true,
					"type" => "success",
					"message" => "Foto de perfil actualizada.",
					"photo_url" => profile_photo_url($target_path)
				);
			} else {
				@unlink($target_path);
				$response = array("status" => false, "type" => "error", "message" => "No se pudo actualizar la foto en la base de datos.");
			}

			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}

		public function delete_profile_photo()
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}

			$id_user = (int)$_SESSION['user_data']['id_user'];
			$request_current = $this->model->get_profile_photo_by_user($id_user);
			$current_photo = $request_current['profile_photo_user'] ?? null;

			$request_update = $this->model->update_profile_photo($id_user, null);
			if ($request_update) {
				if (!empty($current_photo) && file_exists($current_photo)) {
					@unlink($current_photo);
				}
				session_user($id_user);
				$response = array(
					"status" => true,
					"type" => "success",
					"message" => "Foto de perfil eliminada.",
					"photo_url" => profile_photo_url(null)
				);
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se pudo eliminar la foto de perfil.");
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}

		public function list_browser_sessions()
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}

			$id_user = (int)$_SESSION['user_data']['id_user'];
			$current_session_id = session_id();
			$data = $this->model->list_browser_sessions($id_user);

			foreach ($data as &$row) {
				$row['is_current'] = $row['session_id'] === $current_session_id;
				$row['status_label'] = ((int)$row['status_active_user_browser_session'] === 1 && (int)$row['status_closed_user_browser_session'] === 0)
					? 'Activa'
					: 'Cerrada';
			}
			unset($row);

			$response = array("status" => true, "result" => $data);
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}

		public function close_browser_session()
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}

			$id_user = (int)$_SESSION['user_data']['id_user'];
			$session_id = trim((string)($_POST['session_id'] ?? ''));
			$current_password = (string)($_POST['current_password'] ?? '');
			if ($session_id === '') {
				$response = array("status" => false, "type" => "error", "message" => "No se envio la sesion.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if ($current_password === '') {
				$response = array("status" => false, "type" => "error", "message" => "Ingrese su contrasena para confirmar el cierre de sesion.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!$this->model->validate_password($id_user, $current_password)) {
				$response = array("status" => false, "type" => "error", "message" => "La contrasena ingresada no es valida.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if ($session_id === session_id()) {
				$response = array("status" => false, "type" => "error", "message" => "No puede cerrar la sesion actual desde esta opcion.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}

			$request = $this->model->close_browser_session_by_user($id_user, $session_id);
			if ($request) {
				$response = array("status" => true, "type" => "success", "message" => "Sesion remota cerrada.");
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se pudo cerrar la sesion.");
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}

		public function close_other_browser_sessions()
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			$id_user = (int)$_SESSION['user_data']['id_user'];
			$current_password = (string)($_POST['current_password'] ?? '');
			if ($current_password === '') {
				$response = array("status" => false, "type" => "error", "message" => "Ingrese su contrasena para confirmar el cierre de sesiones.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!$this->model->validate_password($id_user, $current_password)) {
				$response = array("status" => false, "type" => "error", "message" => "La contrasena ingresada no es valida.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			$request = $this->model->close_other_browser_sessions($id_user, session_id());
			if ($request) {
				$response = array("status" => true, "type" => "success", "message" => "Se cerraron las otras sesiones activas.");
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se pudieron cerrar las otras sesiones.");
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}

		public function trash_list()
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}

			$trash_key = trim((string)($_POST['trash_key'] ?? ''));
			if ($trash_key === '') {
				$response = array("status" => false, "type" => "error", "message" => "No se definio la papelera.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}

			$config = $this->resolve_trash_config($trash_key);
			if (empty($config)) {
				$response = array("status" => false, "type" => "error", "message" => "Papelera no habilitada para esta interfaz.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}

			require_once("Models/TrashModel.php");
			$trash_model = new TrashModel();
			$request = $trash_model->list_deleted_records($config);
			$response = array(
				"status" => true,
				"title" => $config['title'],
				"trash_key" => $trash_key,
				"allow_hard_delete" => !empty($config['allow_hard_delete']),
				"result" => $request
			);
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}

		public function trash_restore()
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			$trash_key = trim((string)($_POST['trash_key'] ?? ''));
			$id_record = (int)($_POST['id_record'] ?? 0);
			if ($trash_key === '' || $id_record <= 0) {
				$response = array("status" => false, "type" => "error", "message" => "Datos incompletos para restaurar.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}

			$config = $this->resolve_trash_config($trash_key);
			if (empty($config)) {
				$response = array("status" => false, "type" => "error", "message" => "Papelera no habilitada para esta interfaz.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}

			require_once("Models/TrashModel.php");
			$trash_model = new TrashModel();
			$request = $trash_model->restore_record($config, $id_record);

			if ($request) {
				$response = array("status" => true, "type" => "success", "message" => "Registro restaurado correctamente.");
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se pudo restaurar el registro.");
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}

		public function trash_delete_permanent()
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			$trash_key = trim((string)($_POST['trash_key'] ?? ''));
			$id_record = (int)($_POST['id_record'] ?? 0);
			if ($trash_key === '' || $id_record <= 0) {
				$response = array("status" => false, "type" => "error", "message" => "Datos incompletos para eliminar.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}

			$config = $this->resolve_trash_config($trash_key);
			if (empty($config)) {
				$response = array("status" => false, "type" => "error", "message" => "Papelera no habilitada para esta interfaz.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (empty($config['allow_hard_delete'])) {
				$response = array("status" => false, "type" => "error", "message" => "La eliminacion definitiva no esta habilitada para este modulo por integridad de datos.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}

			require_once("Models/TrashModel.php");
			$trash_model = new TrashModel();
			$request = $trash_model->hard_delete_record($config, $id_record);
			if ($request['status']) {
				$response = array("status" => true, "type" => "success", "message" => "Registro eliminado definitivamente.");
			} else {
				$response = array("status" => false, "type" => "error", "message" => $request['message']);
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}

		public function logout()
		{
			$this->model->close_browser_session(session_id());
			session_unset();
			session_destroy();
            setcookie(session_name(), '', ['expires'=>time()-3600,'path'=>'/','secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off','httponly'=>true,'samesite'=>'Lax']);
			header('location: '.base_url().'/login');
			die();
		}

		private function resolve_trash_config(string $trash_key): array
		{
			$configs = [
				"administration/areas" => [
					"title" => "Papelera de Areas",
					"table" => "areas",
					"id_column" => "id_area",
					"label_column" => "name_area",
					"status_deleted_column" => "status_deleted_area",
					"created_column" => "date_created_area",
					"allow_hard_delete" => true
				],
				"administration/campuses" => [
					"title" => "Papelera de Sedes",
					"table" => "campuses",
					"id_column" => "id_campus",
					"label_column" => "name_campus",
					"status_deleted_column" => "status_deleted_campus",
					"created_column" => "date_created_campus",
					"allow_hard_delete" => true
				],
				"administration/companies" => [
					"title" => "Papelera de Empresas",
					"table" => "companies",
					"id_column" => "id_company",
					"label_column" => "name_company",
					"status_deleted_column" => "status_deleted_company",
					"created_column" => "date_created_company",
					"allow_hard_delete" => true
				],
				"administration/positions" => [
					"title" => "Papelera de Puestos",
					"table" => "positions",
					"id_column" => "id_position",
					"label_column" => "name_position",
					"status_deleted_column" => "status_deleted_position",
					"created_column" => "date_created_position",
					"allow_hard_delete" => true
				],
				"administration/professional_titles" => [
					"title" => "Papelera de Titulos Profesionales",
					"table" => "professional_titles",
					"id_column" => "id_professional_title",
					"label_column" => "name_professional_title",
					"status_deleted_column" => "status_deleted_professional_title",
					"created_column" => "date_created_professional_title",
					"allow_hard_delete" => true
				],
				"administration/roles" => [
					"title" => "Papelera de Roles",
					"table" => "roles",
					"id_column" => "id_role",
					"label_column" => "name_role",
					"status_deleted_column" => "status_deleted_role",
					"created_column" => "date_created_role",
					"allow_hard_delete" => false
				],
				"administration/environments" => [
					"title" => "Papelera de Ambientes",
					"table" => "environments",
					"id_column" => "id_environment",
					"label_column" => "name_environment",
					"status_deleted_column" => "status_deleted_environment",
					"created_column" => "date_created_environment",
					"allow_hard_delete" => false
				],
				"administration/users" => [
					"title" => "Papelera de Usuarios",
					"table" => "users",
					"id_column" => "id_user",
					"label_column" => "name_user",
					"status_deleted_column" => "status_deleted_user",
					"created_column" => "date_created_user",
					"allow_hard_delete" => false
				],
				"inventory/architectures" => [
					"title" => "Papelera de Arquitecturas",
					"table" => "architectures",
					"id_column" => "id_architecture",
					"label_column" => "name_architecture",
					"status_deleted_column" => "status_deleted_architecture",
					"created_column" => "date_created_architecture",
					"allow_hard_delete" => true
				],
				"inventory/brands" => [
					"title" => "Papelera de Marcas",
					"table" => "brands",
					"id_column" => "id_brand",
					"label_column" => "name_brand",
					"status_deleted_column" => "status_deleted_brand",
					"created_column" => "date_created_brand",
					"allow_hard_delete" => true
				],
				"inventory/capacity_units" => [
					"title" => "Papelera de Unidades de Capacidad",
					"table" => "capacity_units",
					"id_column" => "id_capacity_unit",
					"label_column" => "name_capacity_unit",
					"status_deleted_column" => "status_deleted_capacity_unit",
					"created_column" => "date_created_capacity_unit",
					"allow_hard_delete" => true
				],
				"inventory/categories_asset" => [
					"title" => "Papelera de Categorias de Activo",
					"table" => "categories_asset",
					"id_column" => "id_category_asset",
					"label_column" => "name_category_asset",
					"status_deleted_column" => "status_deleted_category_asset",
					"created_column" => "date_created_category_asset",
					"allow_hard_delete" => true
				],
				"inventory/conditions_asset" => [
					"title" => "Papelera de Condiciones de Activo",
					"table" => "conditions_asset",
					"id_column" => "id_condition_asset",
					"label_column" => "name_condition_asset",
					"status_deleted_column" => "status_deleted_condition_asset",
					"created_column" => "date_created_condition_asset",
					"allow_hard_delete" => true
				],
				"inventory/types_asset" => [
					"title" => "Papelera de Tipos de Activo",
					"table" => "types_asset",
					"id_column" => "id_type_asset",
					"label_column" => "name_type_asset",
					"status_deleted_column" => "status_deleted_type_asset",
					"created_column" => "date_created_type_asset",
					"allow_hard_delete" => true
				],
				"inventory/types_firmware" => [
					"title" => "Papelera de Tipos de Firmware",
					"table" => "types_firmware",
					"id_column" => "id_type_firmware",
					"label_column" => "name_type_firmware",
					"status_deleted_column" => "status_deleted_type_firmware",
					"created_column" => "date_created_type_firmware",
					"allow_hard_delete" => true
				],
				"inventory/types_license" => [
					"title" => "Papelera de Tipos de Licencia",
					"table" => "types_license",
					"id_column" => "id_type_license",
					"label_column" => "name_type_license",
					"status_deleted_column" => "status_deleted_type_license",
					"created_column" => "date_created_type_license",
					"allow_hard_delete" => true
				],
				"inventory/types_network_interface" => [
					"title" => "Papelera de Tipos de Interfaz de Red",
					"table" => "types_network_interface",
					"id_column" => "id_type_network_interface",
					"label_column" => "name_type_network_interface",
					"status_deleted_column" => "status_deleted_type_network_interface",
					"created_column" => "date_created_type_network_interface",
					"allow_hard_delete" => true
				],
				"inventory/types_software" => [
					"title" => "Papelera de Tipos de Software",
					"table" => "types_software",
					"id_column" => "id_type_software",
					"label_column" => "name_type_software",
					"status_deleted_column" => "status_deleted_type_software",
					"created_column" => "date_created_type_software",
					"allow_hard_delete" => true
				],
				"inventory/types_storage" => [
					"title" => "Papelera de Tipos de Almacenamiento",
					"table" => "types_storage",
					"id_column" => "id_type_storage",
					"label_column" => "name_type_storage",
					"status_deleted_column" => "status_deleted_type_storage",
					"created_column" => "date_created_type_storage",
					"allow_hard_delete" => true
				],
				"inventory/versions_ip" => [
					"title" => "Papelera de Versiones IP",
					"table" => "versions_ip",
					"id_column" => "id_version_ip",
					"label_column" => "name_version_ip",
					"status_deleted_column" => "status_deleted_version_ip",
					"created_column" => "date_created_version_ip",
					"allow_hard_delete" => true
				],
				"movement/movements_asset" => [
					"title" => "Papelera de Movimientos de Activo",
					"table" => "movements_asset",
					"id_column" => "id_movement_asset",
					"label_column" => "id_movement_asset",
					"status_deleted_column" => "status_deleted_movement_asset",
					"created_column" => "date_created_movement_asset",
					"allow_hard_delete" => false
				],
				"maintenance/actions_maintenance_preventive" => [
					"title" => "Papelera de Acciones Preventivas",
					"table" => "parameters_maintenance_preventive_actions",
					"id_column" => "id_parameter_maintenance_preventive_action",
					"label_column" => "name_parameter_maintenance_preventive_action",
					"status_deleted_column" => "status_deleted_parameter_maintenance_preventive_action",
					"created_column" => "date_created_parameter_maintenance_preventive_action",
					"allow_hard_delete" => true
				],
				"maintenance/maintenances_preventive" => [
					"title" => "Papelera de Mantenimientos Preventivos",
					"table" => "maintenances_preventive",
					"id_column" => "id_maintenance_preventive",
					"label_column" => "id_maintenance_preventive",
					"status_deleted_column" => "status_deleted_maintenance_preventive",
					"created_column" => "date_created_maintenance_preventive",
					"allow_hard_delete" => false
				],
				"maintenance/maintenances_corrective" => [
					"title" => "Papelera de Mantenimientos Correctivos",
					"table" => "maintenances_corrective",
					"id_column" => "id_maintenance_corrective",
					"label_column" => "id_maintenance_corrective",
					"status_deleted_column" => "status_deleted_maintenance_corrective",
					"created_column" => "date_created_maintenance_corrective",
					"allow_hard_delete" => false
				],
				"help_desk/categories" => [
					"title" => "Papelera de Categorias de Incidencia",
					"table" => "categories",
					"id_column" => "id_category",
					"label_column" => "name_category",
					"status_deleted_column" => "status_deleted_category",
					"created_column" => "date_created_category",
					"allow_hard_delete" => false
				],
				"help_desk/priorities" => [
					"title" => "Papelera de Prioridades",
					"table" => "priorities",
					"id_column" => "id_priority",
					"label_column" => "name_priority",
					"status_deleted_column" => "status_deleted_priority",
					"created_column" => "date_created_priority",
					"allow_hard_delete" => false
				],
				"help_desk/subcategories" => [
					"title" => "Papelera de Subcategorias de Incidencia",
					"table" => "subcategories",
					"id_column" => "id_subcategory",
					"label_column" => "name_subcategory",
					"status_deleted_column" => "status_deleted_subcategory",
					"created_column" => "date_created_subcategory",
					"allow_hard_delete" => false
				],
				"inventory/assets" => [
					"title" => "Papelera de Activos",
					"table" => "assets",
					"id_column" => "id_asset",
					"label_column" => "code_inventory_asset",
					"status_deleted_column" => "status_deleted_asset",
					"created_column" => "date_created_asset",
					"allow_hard_delete" => false
				],
				"help_desk/incidents" => [
					"title" => "Papelera de Incidencias",
					"table" => "incidents",
					"id_column" => "id_incident",
					"label_column" => "title_incident",
					"status_deleted_column" => "status_deleted_incident",
					"created_column" => "date_created_incident",
					"allow_hard_delete" => true,
					"related_delete" => [
                        ["table" => "incident_events", "foreign_column" => "id_incident"],
                        ["table" => "incident_corrective_derivation_history", "foreign_column" => "id_incident"],
						[
							"table" => "attachments_incident",
							"foreign_column" => "id_incident_attachment_incident"
						]
					]
				]
			];
			return $configs[$trash_key] ?? [];
		}

	}
 ?>
