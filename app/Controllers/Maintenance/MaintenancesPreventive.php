<?php 
	class MaintenancesPreventive extends Controllers{
		
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
		
        public function manage_maintenances_preventive()
		{
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			check_permissions(MODULE_MAINTENANCE);
			if(empty($_SESSION['module_permissions']['r_read_permission'])){
				header("Location:".base_url().'/errors/forbidden');
				die();
			}
            $data['css'] = [
				"/extensions/datatables/datatables.min.css"
			];
			$data['script'] = [
				"/js/language_datatable.js",
				"/js/upload_handler.js",
				"/extensions/datatables/datatables.min.js"
			];
			$data['page_functions_js'] = [
				"functions_manage_maintenances_preventive.js",
				"functions_manage_company_responsible.js"
			];
			$data['preventive_indicators'] = $this->model->show_preventive_indicators();
			$this->views->getView($this, "Maintenance", "index", $data);
		}

		public function show_maintenances_preventive()
		{
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			check_permissions(MODULE_MAINTENANCE);
			if(empty($_SESSION['module_permissions']['r_read_permission'])){
				header("Location: " . base_url() . "/errors/forbidden");
				die();
			}
			$data = $this->model->show_maintenances_preventive();
			for ($i=0; $i < count($data); $i++) {
				$btnView = '';
				$btnEdit = '';
				$btnExecute = '';
				$btnCompany = '';
				$btnHistory = '';
				if($_SESSION['module_permissions']['r_read_permission']){
					$btnView = '<button type="button" class="btn icon btn-secondary js-btn-view" data-id="'.$data[$i]['id_maintenance_preventive'].'" title="Ver"><i class="bx bx-eye"></i></button>';
				}
				if($_SESSION['module_permissions']['u_update_permission']){
					$btnEdit = '<button type="button" class="btn icon btn-primary js-btn-edit" data-id="'.$data[$i]['id_maintenance_preventive'].'" title="Editar"><i class="bx bx-pencil"></i></button>';
				}
				$isActiveSchedule = intval($data[$i]['status_active_maintenance_preventive'] ?? 1) === 1;
				if($_SESSION['module_permissions']['u_update_permission'] && $isActiveSchedule){
					$btnExecute = '<button type="button" class="btn icon btn-primary js-btn-execute" data-id="'.$data[$i]['id_maintenance_preventive'].'" title="Ejecutar"><i class="bx bxs-brush"></i></button>';
				}
				if($data[$i]['name_type_responsible_maintenance'] == "Externo"){
					if($_SESSION['module_permissions']['u_update_permission']){
						$btnCompany = '<button type="button" class="btn icon btn-primary js-btn-company" data-id="'.$data[$i]['id_maintenance_preventive'].'" title="Empresa"><i class="bx bxs-business"></i></button>';
					}
				}			
				if($_SESSION['module_permissions']['r_read_permission']){	
					$btnHistory = '<button type="button" class="btn icon btn-danger js-btn-history" data-id="'.$data[$i]['id_maintenance_preventive'].'" title="Historial"><i class="bx bx-history"></i></button>';
				}
				$data[$i]['options'] = '<div class="text-center">' .$btnView.' '.$btnEdit.' '.$btnExecute.' '.$btnCompany.' '.$btnHistory. '</div>';
			}
			echo json_encode($data, JSON_UNESCAPED_UNICODE);
		}

		public function history_maintenance_preventive()
		{
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			check_permissions(MODULE_MAINTENANCE);
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
				"functions_history_maintenance_preventive.js"
			];
			$this->views->getView($this, "Maintenance", "history", $data);
		}

		public function show_history_maintenances_preventive()
		{
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			check_permissions(MODULE_MAINTENANCE);
			if(empty($_SESSION['module_permissions']['r_read_permission'])){
				header("Location: " . base_url() . "/errors/forbidden");
				die();
			}
			$int_id_maintenance_preventive = intval($_POST['id_maintenance_preventive'] ?? 0);
			$data = $this->model->show_history_maintenances_preventive($int_id_maintenance_preventive);
			for ($i=0; $i < count($data); $i++) {
				$btnView = '';
				$btnEdit = '';
				if($_SESSION['module_permissions']['r_read_permission']){
					$btnView = '<button type="button" class="btn icon btn-secondary js-btn-view-history" data-id="'.$data[$i]['id_history_maintenance_preventive'].'" title="Ver"><i class="bx bx-eye"></i></button>';
				}
				if($_SESSION['module_permissions']['u_update_permission']){
					$btnEdit = '<button type="button" class="btn icon btn-primary js-btn-edit-history" data-id="'.$data[$i]['id_history_maintenance_preventive'].'" title="Editar"><i class="bx bx-pencil"></i></button>';
				}
				$data[$i]['options'] = '<div class="text-center">'.$btnView.' '. $btnEdit.'</div>';
			}
			echo json_encode($data, JSON_UNESCAPED_UNICODE);
		}

		public function show_history_maintenance_preventive_one() 
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_MAINTENANCE);
			if (!$_SESSION['module_permissions']['r_read_permission']) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para leer el registro.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_history_maintenance_preventive'])) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrio un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_history_maintenance_preventive = trim($_POST['id_history_maintenance_preventive']);
				$request_history_maintenance_preventive_one = $this->model->show_history_maintenance_preventive_one($int_id_history_maintenance_preventive);
				if ($request_history_maintenance_preventive_one) {
					$request_history_files = $this->model->show_history_files(intval($int_id_history_maintenance_preventive));
					$attachments_before = [];
					$attachments_after = [];
					$attachments_additional = [];
					if (is_array($request_history_files) && count($request_history_files) > 0) {
						foreach ($request_history_files as $row) {
							$item = [
								'id_attachment' => intval($row['id_attachment_history_maintenance_preventive']),
								'name_attachment' => $row['name_attachment_history_maintenance_preventive'],
								'route_attachment' => $row['route_attachment_history_maintenance_preventive']
							];
							if (in_array(($row['type_attachment_history_maintenance_preventive'] ?? ''), ['additional', 'external'], true)) {
								$attachments_additional[] = $item;
							} elseif (($row['type_attachment_history_maintenance_preventive'] ?? '') === 'after') {
								$attachments_after[] = $item;
							} else {
								$attachments_before[] = $item;
							}
						}
					}
					$response = array(
						"status" => true,
						"result" => $request_history_maintenance_preventive_one,
						"attachments_before" => $attachments_before,
						"attachments_after" => $attachments_after,
						"attachments_additional" => $attachments_additional
					);
				} else {
					$response = array("status" => false, "type" => "error", "message" => "No se pudo leer el registro.");
				}
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se enviaron datos.");
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}

		public function update_history_maintenance_preventive()
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_MAINTENANCE);
			if (!$_SESSION['module_permissions']['u_update_permission']) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para corregir el registro.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_history_maintenance_preventive'])) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrio un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['id_status_maintenance'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opcion de estado.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}

				$int_id_history_maintenance_preventive = intval($_POST['id_history_maintenance_preventive']);
				$int_id_status_maintenance = intval($_POST['id_status_maintenance']);
				$str_date_performed = trim($_POST['date_performed_history_maintenance_preventive'] ?? '');
				if ($str_date_performed === '') {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese la fecha realizada.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$performed_date = DateTime::createFromFormat('Y-m-d', $str_date_performed);
				$performed_date_errors = DateTime::getLastErrors();
				if (!$performed_date || ($performed_date_errors !== false && ($performed_date_errors['warning_count'] > 0 || $performed_date_errors['error_count'] > 0))) {
					$response = array("status" => false, "type" => "error", "message" => "La fecha realizada no tiene un formato valido.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$str_date_performed = $performed_date->format('Y-m-d');
				$str_time_performed = trim($_POST['time_performed_history_maintenance_preventive'] ?? '');
				if ($str_time_performed !== '') {
					$performed_time = DateTime::createFromFormat('H:i', $str_time_performed);
					$performed_time_errors = DateTime::getLastErrors();
					if (!$performed_time || ($performed_time_errors !== false && ($performed_time_errors['warning_count'] > 0 || $performed_time_errors['error_count'] > 0))) {
						$response = array("status" => false, "type" => "error", "message" => "La hora realizada no tiene un formato valido.");
						echo json_encode($response, JSON_UNESCAPED_UNICODE);
						return;
					}
					$str_time_performed = $performed_time->format('H:i:s');
				} else {
					$str_time_performed = null;
				}
				$str_action = trim($_POST['action_history_maintenance_preventive'] ?? '') ?: 'Sin acciones registradas.';
				$str_observation = trim($_POST['observation_history_maintenance_preventive'] ?? '') ?: 'Ninguna.';

				$files_before = (isset($_FILES['document_before']) && isset($_FILES['document_before']['name']) && is_array($_FILES['document_before']['name']) && count(array_filter($_FILES['document_before']['name'])) > 0) ? $_FILES['document_before'] : null;
				$files_after = (isset($_FILES['document_after']) && isset($_FILES['document_after']['name']) && is_array($_FILES['document_after']['name']) && count(array_filter($_FILES['document_after']['name'])) > 0) ? $_FILES['document_after'] : null;
				$files_additional = (isset($_FILES['document_additional']) && isset($_FILES['document_additional']['name']) && is_array($_FILES['document_additional']['name']) && count(array_filter($_FILES['document_additional']['name'])) > 0) ? $_FILES['document_additional'] : ((isset($_FILES['document_external']) && isset($_FILES['document_external']['name']) && is_array($_FILES['document_external']['name']) && count(array_filter($_FILES['document_external']['name'])) > 0) ? $_FILES['document_external'] : null);

				foreach ([$files_before, $files_after, $files_additional] as $files) {
					if ($files) {
						$response = check_files($files, 10);
						if ($response != "succes") {
							echo json_encode($response, JSON_UNESCAPED_UNICODE);
							return;
						}
					}
				}

				$request_update = $this->model->update_history_maintenance_preventive(
					$int_id_history_maintenance_preventive,
					$int_id_status_maintenance,
					$str_date_performed,
					$str_time_performed,
					$str_action,
					$str_observation
				);

				if ($request_update !== false) {
					if (!empty($_POST['delete_history_attachments']) && is_array($_POST['delete_history_attachments'])) {
						$delete_history_attachments = array_unique(array_map('intval', $_POST['delete_history_attachments']));
						foreach ($delete_history_attachments as $id_attachment_history) {
							if ($id_attachment_history <= 0) {
								continue;
							}
							$attachment = $this->model->show_history_file_by_id($id_attachment_history, $int_id_history_maintenance_preventive);
							if ($attachment) {
								delete_file($attachment['route_attachment_history_maintenance_preventive']);
								$this->model->delete_attachment_history_maintenance_preventive($id_attachment_history);
							}
						}
					}
					if ($files_before) {
						upload_attachments_history_maintenance_preventive($files_before, $int_id_history_maintenance_preventive, 'before');
					}
					if ($files_after) {
						upload_attachments_history_maintenance_preventive($files_after, $int_id_history_maintenance_preventive, 'after');
					}
					if ($files_additional) {
						upload_attachments_history_maintenance_preventive($files_additional, $int_id_history_maintenance_preventive, 'external');
					}
					$response = array("status" => true, "type" => "success", "message" => "Registro corregido correctamente.");
				} else {
					$response = array("status" => false, "type" => "error", "message" => "No se pudo corregir el registro.");
				}
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se enviaron datos.");
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}
		public function show_maintenance_preventive() 
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_MAINTENANCE);
			if (!$_SESSION['module_permissions']['r_read_permission']) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para leer el registro.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_maintenance_preventive'])) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrio un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_maintenance_preventive = trim($_POST['id_maintenance_preventive']);
				$request_maintenance_preventive = $this->model->show_maintenance_preventive($int_id_maintenance_preventive);
				if ($request_maintenance_preventive) {
					$request_attachments = $this->model->show_files($int_id_maintenance_preventive);
					$attachments = normalized_attachment(
						$request_attachments, 
						'id_attachment_maintenance_preventive', 
						'name_attachment_maintenance_preventive', 
						'route_attachment_maintenance_preventive'
					);
					$response = array("status" => true, "result" => $request_maintenance_preventive, "attachments" => $attachments);
				} else {
					$response = array("status" => false, "type" => "error", "message" => "No se pudo leer el registro.");
				}
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se enviaron datos.");
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}

		public function insert_maintenance_preventive() 
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_MAINTENANCE);
			if (!$_SESSION['module_permissions']['w_write_permission']) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para registrar nueva categoria.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_asset'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione un activo.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['date_scheduled_maintenance_preventive'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese la fecha programada.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['id_frequency_maintenance'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opcion de frecuencia.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['id_type_responsible_maintenance'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opcion de responsable de mantenimiento.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_asset = trim($_POST['id_asset']);
				$str_date_scheduled_maintenance_preventive = date("Y-m-d", strtotime(trim($_POST['date_scheduled_maintenance_preventive'])));				
				$int_id_frequency_maintenance = trim($_POST['id_frequency_maintenance']);
				$str_date_next_maintenance_preventive = calculate_next_maintenance_date($str_date_scheduled_maintenance_preventive, $int_id_frequency_maintenance);
				$int_id_type_responsible_maintenance = trim($_POST['id_type_responsible_maintenance']);
				$str_description_maintenance_preventive = trim($_POST['description_maintenance_preventive'] ?? '') ?: 'Ninguna.';
				$str_observation_maintenance_preventive = trim($_POST['observation_maintenance_preventive'] ?? '') ?: 'Ninguna.';				
				$files = isset($_FILES['document']) && $_FILES['document']['size'] > 0 ? $_FILES['document'] : null;
				// Verificar la cantidad y el tamano de los archivos
				if ($files) {
					$response = check_files($files, 10);
					if ($response != "succes") {
						echo json_encode($response, JSON_UNESCAPED_UNICODE);
						return;
					}
				}
				$request_maintenance_preventive = $this->model->insert_maintenance_preventive(
					$int_id_asset,
					$str_date_scheduled_maintenance_preventive,
					$str_date_next_maintenance_preventive,
					$int_id_frequency_maintenance,
					$int_id_type_responsible_maintenance,
					$str_description_maintenance_preventive,
					$str_observation_maintenance_preventive					
				);				
				if ($request_maintenance_preventive) {
					// Ahora insertar los archivos asociados al registro
					if ($files) {
						upload_attachments_maintenance_preventive($files, $request_maintenance_preventive);
					}
					$response = array("status" => true, "type" => "success", "message" => "Registrado correctamente.");
				} else {
					$response = array("status" => false, "type" => "error", "message" => "No se pudo registrar nueva programacion de mantenimiento preventivo.");
				}
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se enviaron datos.");
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}

		public function update_maintenance_preventive() 
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_MAINTENANCE);
			if (!$_SESSION['module_permissions']['u_update_permission']) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para actualizar el registro.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_maintenance_preventive'])) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrio un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['id_asset'])) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrio un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['date_scheduled_maintenance_preventive'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese la fecha programada.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['id_frequency_maintenance'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opcion de frecuencia.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['id_type_responsible_maintenance'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opcion de responsable de mantenimiento.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_maintenance_preventive = trim($_POST['id_maintenance_preventive']);
				$int_id_asset = trim($_POST['id_asset']);
				$str_date_scheduled_maintenance_preventive = date("Y-m-d", strtotime(trim($_POST['date_scheduled_maintenance_preventive'])));				
				$int_id_frequency_maintenance = trim($_POST['id_frequency_maintenance']);
				$str_date_next_maintenance_preventive = calculate_next_maintenance_date($str_date_scheduled_maintenance_preventive, $int_id_frequency_maintenance);
				$int_id_type_responsible_maintenance = trim($_POST['id_type_responsible_maintenance']);
				$str_description_maintenance_preventive = trim($_POST['description_maintenance_preventive'] ?? '') ?: 'Ninguna.';
				$str_observation_maintenance_preventive = trim($_POST['observation_maintenance_preventive'] ?? '') ?: 'Ninguna.';				
				$files = isset($_FILES['document']) && $_FILES['document']['size'] > 0 ? $_FILES['document'] : null;
				// Verificar la cantidad y el tamano de los archivos
				if ($files) {
					$response = check_files($files, 10);
					if ($response != "succes") {
						echo json_encode($response, JSON_UNESCAPED_UNICODE);
						return;
					}
				}
				$request_maintenance_preventive = $this->model->update_maintenance_preventive(
					$int_id_maintenance_preventive,
					$int_id_asset,
					$str_date_scheduled_maintenance_preventive,
					$str_date_next_maintenance_preventive,
					$int_id_frequency_maintenance,
					$int_id_type_responsible_maintenance,
					$str_description_maintenance_preventive,
					$str_observation_maintenance_preventive					
				);
				if ($request_maintenance_preventive) {
					if (!empty($_POST['delete_attachments'])) {
						$delete_attachments = $_POST['delete_attachments'];
						$request_attachments_maintenance_preventive = $this->model->show_files($int_id_maintenance_preventive);
						$attachments_map = [];
						foreach ($request_attachments_maintenance_preventive as $item) {
							$attachments_map[$item['id_attachment_maintenance_preventive']] = $item['route_attachment_maintenance_preventive'];
						}
						foreach ($delete_attachments as $id) {
							if (isset($attachments_map[$id])) {
								delete_file($attachments_map[$id]);
								$this->model->delete_attachment_maintenance_preventive($id);
							}
						}
					}
					if ($files) {
						upload_attachments_maintenance_preventive($files, $int_id_maintenance_preventive);
					}
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

		public function show_files() {
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			$int_id_maintenance_preventive = trim($_POST['id_maintenance_preventive']);
			$request_files = $this->model->show_files($int_id_maintenance_preventive);					
			$data['attachments_maintenance_preventive'] = $request_files;
			$html = modal_folder($data);
			die();
		}
		
		public function execute_maintenance_preventive() 
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_MAINTENANCE);
			if (!$_SESSION['module_permissions']['u_update_permission']) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para registrar nueva tarea realizada.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_maintenance_preventive'])) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrio un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (!isset($_POST['id_status_maintenance'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opcion de estado.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}				
				if (empty($_POST['observation_history_maintenance_preventive'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese la observacion");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['date_performed_history_maintenance_preventive'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese la fecha realizada.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['time_performed_history_maintenance_preventive'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese la hora realizada.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$str_date_performed_input = trim($_POST['date_performed_history_maintenance_preventive']);
				$str_time_performed_input = trim($_POST['time_performed_history_maintenance_preventive']);
				$performed_datetime = DateTime::createFromFormat('Y-m-d H:i', $str_date_performed_input . ' ' . $str_time_performed_input);
				$performed_datetime_errors = DateTime::getLastErrors();
				if (!$performed_datetime || ($performed_datetime_errors !== false && ($performed_datetime_errors['warning_count'] > 0 || $performed_datetime_errors['error_count'] > 0))) {
					$response = array("status" => false, "type" => "error", "message" => "La fecha u hora realizada no tiene un formato valido.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_maintenance_preventive = trim($_POST['id_maintenance_preventive']);
				$int_id_status_maintenance = trim($_POST['id_status_maintenance']);
				$maintenance_context = $this->model->show_maintenance_preventive(intval($int_id_maintenance_preventive));
				$is_external = strtolower($maintenance_context['name_type_responsible_maintenance'] ?? '') === 'externo';
				if (!$is_external && empty($_POST['action_history_maintenance_preventive'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese la accion realizada" );
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if ($is_external && empty($maintenance_context['id_company'])) {
					$response = array("status" => false, "type" => "error", "message" => "El mantenimiento externo requiere una empresa responsable relacionada." );
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$str_action_history_maintenance_preventive = trim($_POST['action_history_maintenance_preventive'] ?? '');
				if ($is_external && $str_action_history_maintenance_preventive === '') {
					$str_action_history_maintenance_preventive = 'Mantenimiento preventivo externo sustentado con documentos adjuntos.';
				}
				$str_observation_history_maintenance_preventive = trim($_POST['observation_history_maintenance_preventive']);
				$str_date_performed_history_maintenance_preventive = $performed_datetime->format('Y-m-d');
				$str_time_performed_history_maintenance_preventive = $performed_datetime->format('H:i:s');

				$files_before = (isset($_FILES['document_before']) && isset($_FILES['document_before']['name']) && is_array($_FILES['document_before']['name']) && count(array_filter($_FILES['document_before']['name'])) > 0) ? $_FILES['document_before'] : null;
				$files_after = (isset($_FILES['document_after']) && isset($_FILES['document_after']['name']) && is_array($_FILES['document_after']['name']) && count(array_filter($_FILES['document_after']['name'])) > 0) ? $_FILES['document_after'] : null;
				$files_additional = (isset($_FILES['document_additional']) && isset($_FILES['document_additional']['name']) && is_array($_FILES['document_additional']['name']) && count(array_filter($_FILES['document_additional']['name'])) > 0) ? $_FILES['document_additional'] : ((isset($_FILES['document_external']) && isset($_FILES['document_external']['name']) && is_array($_FILES['document_external']['name']) && count(array_filter($_FILES['document_external']['name'])) > 0) ? $_FILES['document_external'] : null);

				if ($files_before) {
					$response = check_files($files_before, 10);
					if ($response != "succes") {
						echo json_encode($response, JSON_UNESCAPED_UNICODE);
						return;
					}
				}
				if ($files_after) {
					$response = check_files($files_after, 10);
					if ($response != "succes") {
						echo json_encode($response, JSON_UNESCAPED_UNICODE);
						return;
					}
				}
				if ($files_additional) {
					$response = check_files($files_additional, 10);
					if ($response != "succes") {
						echo json_encode($response, JSON_UNESCAPED_UNICODE);
						return;
					}
				}
				$request_execute_maintenance_preventive = $this->model->execute_maintenance_preventive(
					$int_id_maintenance_preventive, 
					$int_id_status_maintenance,
					$str_action_history_maintenance_preventive,
					$str_observation_history_maintenance_preventive,
					$str_date_performed_history_maintenance_preventive,
					$str_time_performed_history_maintenance_preventive
				);
				if ($request_execute_maintenance_preventive) {
					$int_id_history_maintenance_preventive = intval($request_execute_maintenance_preventive);
					if ($files_before) {
						upload_attachments_history_maintenance_preventive($files_before, $int_id_history_maintenance_preventive, 'before');
					}
					if ($files_after) {
						upload_attachments_history_maintenance_preventive($files_after, $int_id_history_maintenance_preventive, 'after');
					}
					if ($files_additional) {
						upload_attachments_history_maintenance_preventive($files_additional, $int_id_history_maintenance_preventive, 'external');
					}
					$response = array("status" => true, "type" => "success", "message" => "Registrado correctamente.");
				} else {
					$response = array("status" => false, "type" => "error", "message" => "No se pudo registrar nueva tarea realizada");
				}
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se enviaron datos.");
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}

		public function relate_maintenance_preventive_company() {
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_HELP_DESK);
			if(empty($_SESSION['module_permissions']['u_update_permission'])){
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para actualizar el registro.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_maintenance_preventive'])) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrio un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['id_company'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una empresa.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_maintenance_preventive = trim($_POST['id_maintenance_preventive']);
				$int_id_company = trim($_POST['id_company']);
				$request_relate_maintenance_preventive_company = $this->model->relate_maintenance_preventive_company(
					$int_id_maintenance_preventive,
					$int_id_company				
				);
				if ($request_relate_maintenance_preventive_company) {
					$response = array("status" => true, "type" => "success", "message" => "Registrado correctamente.");
				} else {
					$response = array("status" => false, "type" => "error", "message" => "No se pudo relacionar la empresa al mantenimiento preventivo.");
				}								
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se enviaron datos.");
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}

		public function delete_maintenance_preventive() 
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_MAINTENANCE);
			if (!$_SESSION['module_permissions']['d_delete_permission']) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para eliminar el registro.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_movement_asset'])) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrio un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_movement_asset = trim($_POST['id_movement_asset']);
				$request_movement_asset = $this->model->delete_movement_asset($int_id_movement_asset);
				if ($request_movement_asset) {
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
