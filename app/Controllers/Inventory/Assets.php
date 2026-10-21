<?php 

	class Assets extends Controllers{
		
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
		
        public function manage_assets()
		{
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			check_permissions(MODULE_INVENTORY);
			if(empty($_SESSION['module_permissions']['r_read_permission'])){
				header("Location:".base_url().'/errors/forbidden');
				die();
			}
            $_SESSION['asset_label_token'] ??= bin2hex(random_bytes(32));
            $data['css'] = [
				"/extensions/datatables/datatables.min.css"
			];
			$data['script'] = [
				"/js/language_datatable.js",
				"/js/upload_handler.js",
				"/extensions/datatables/datatables.min.js"
			];
			$data['page_functions_js'] = [
				"functions_manage_assets.js",
				"functions_manage_asset_ips.js",
				"functions_manage_asset_confidential_notes.js",
				"functions_manage_asset_windows_credentials.js",
				"functions_manage_asset_email_accounts.js",
				"functions_manage_asset_equipment_user.js",
				"functions_manage_software.js"
			];
			$this->views->getView($this, "Inventory", "index", $data);
		}

		public function show_assets()
		{
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			check_permissions(MODULE_INVENTORY);
			if(empty($_SESSION['module_permissions']['r_read_permission'])){
				header("Location: " . base_url() . "/errors/forbidden");
				die();
			}
			$data = $this->model->show_assets();
			for ($i=0; $i < count($data); $i++) {
				$btnView = '';
				$btnEdit = '';
				$btnDelete = '';
				$btnMenu = '';
				if($data[$i]['status_active_asset'] == 1){
					$data[$i]['status_active_asset'] = '<span class="badge bg-success">Activo</span>';
				}else{
					$data[$i]['status_active_asset'] = '<span class="badge bg-danger">Inactivo</span>';
				}
				if($_SESSION['module_permissions']['r_read_permission']){
					$btnView = '<button type="button" class="btn icon btn-secondary js-btn-view" data-id="'.$data[$i]['id_asset'].'" title="Ver"><i class="bx bx-eye"></i></button>';
				}
				if($_SESSION['module_permissions']['u_update_permission'] && submenu_action_permission_allowed(MODULE_INVENTORY, 'Inventario', 'Activos', 'Editar')){
					$btnEdit = '<button type="button" class="btn icon btn-primary js-btn-edit" data-id="'.$data[$i]['id_asset'].'" title="Editar"><i class="bx bx-pencil"></i></button>';
				}
				if($_SESSION['module_permissions']['d_delete_permission'] && submenu_action_permission_allowed(MODULE_INVENTORY, 'Inventario', 'Activos', 'Eliminar')){
					$btnDelete = '<button type="button" class="btn icon btn-danger js-btn-delete" data-id="'.$data[$i]['id_asset'].'" title="Eliminar"><i class="bx bx-trash"></i></button>';
				}
				if($_SESSION['module_permissions']['u_update_permission'] && submenu_action_permission_allowed(MODULE_INVENTORY, 'Inventario', 'Activos', 'Editar')){
					$btnMenu = '<button type="button" class="btn icon btn-tertiary js-btn-menu" data-id="'.$data[$i]['id_asset'].'" title="Menu"><i class="bx  bx-dots-horizontal"></i></button>';
				}
                $attached = (int)$data[$i]['label_attached_asset'] === 1;
                $labelText = $attached ? 'Colocada' : 'Pendiente';
                $labelClass = $attached ? 'success' : 'warning';
                $data[$i]['label_status'] = '<span class="badge bg-' . $labelClass . '">' . $labelText . '</span>';
                if (!empty($_SESSION['module_permissions']['u_update_permission']) && submenu_action_permission_allowed(MODULE_INVENTORY, 'Inventario', 'Activos', 'Editar')) {
                    $data[$i]['label_status'] = '<button type="button" class="btn btn-sm btn-outline-' . $labelClass . ' js-btn-label-status" data-id="' . (int)$data[$i]['id_asset'] . '" data-attached="' . (int)$attached . '" title="Cambiar estado de etiqueta" aria-label="Etiqueta ' . $labelText . ': cambiar estado">' . $labelText . '</button>';
                }
				$data[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnEdit.' '.$btnDelete.' '.$btnMenu.'</div>';
			}
			echo json_encode($data, JSON_UNESCAPED_UNICODE);
		}

        public function update_label_status()
        {
            check_permissions(MODULE_INVENTORY);
            $validToken = isset($_SESSION['asset_label_token']) && hash_equals($_SESSION['asset_label_token'], (string)($_POST['token'] ?? ''));
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$validToken || (int)$_SESSION['status_active_user'] !== 1 || empty($_SESSION['module_permissions']['u_update_permission']) || !submenu_action_permission_allowed(MODULE_INVENTORY, 'Inventario', 'Activos', 'Editar')) {
                http_response_code(403);
                echo json_encode(['status' => false, 'message' => 'No tienes permiso para cambiar el estado de la etiqueta.']);
                return;
            }
            $id = filter_var($_POST['id_asset'] ?? '', FILTER_VALIDATE_INT);
            $attached = $_POST['attached'] ?? '';
            if (!$id || $id < 1 || !in_array($attached, ['0', '1'], true) || !$this->model->code_inventory_asset_specific($id)) {
                http_response_code(400);
                echo json_encode(['status' => false, 'message' => 'Activo o estado no válido.']);
                return;
            }
            $saved = $this->model->set_label_attached($id, (int)$attached);
            echo json_encode(['status' => $saved, 'message' => $saved ? 'Estado de etiqueta actualizado.' : 'No se pudo actualizar la etiqueta.'], JSON_UNESCAPED_UNICODE);
        }

        public function generate_selected_labels()
        {
            check_permissions(MODULE_INVENTORY);
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || (int)$_SESSION['status_active_user'] !== 1 || empty($_SESSION['module_permissions']['r_read_permission']) || !submenu_action_permission_allowed(MODULE_INVENTORY, 'Inventario', 'Activos', 'Ver')) {
                http_response_code(403);
                echo 'No tienes permiso para generar etiquetas.';
                return;
            }
            $input = $_POST['asset_ids'] ?? [];
            if (!is_array($input) || !$input || count($input) > 500) {
                http_response_code(400);
                echo 'Selecciona entre 1 y 500 activos por PDF.';
                return;
            }
            $ids = [];
            foreach ($input as $value) {
                $id = filter_var($value, FILTER_VALIDATE_INT);
                if (!$id || $id < 1) {
                    http_response_code(400);
                    echo 'Selección de activos no válida.';
                    return;
                }
                $ids[] = $id;
            }
            $ids = array_values(array_unique($ids));
            $labels = $this->model->selected_labels($ids, ($_POST['pending_only'] ?? '') === '1');
            if (!$labels) {
                echo 'No hay etiquetas pendientes para los activos seleccionados.';
                return;
            }
            $payload = $this->build_label_pdf_payload($labels);
            if ($payload === null) return;
            $this->views->getView($this, 'Inventory', 'code-inventory', $payload);
        }

		public function show_assets_existing()
		{
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			check_permissions(MODULE_INVENTORY);
			if(empty($_SESSION['module_permissions']['r_read_permission'])){
				header("Location: " . base_url() . "/errors/forbidden");
				die();
			}
			$int_id_asset = intval($_POST['id_asset'] ?? 0);
			$data = $this->model->show_assets_existing($int_id_asset);
			for ($i=0; $i < count($data); $i++) {
				$btnView = '';				
				if($_SESSION['module_permissions']['r_read_permission']){
					$btnView = '<button type="button" class="btn icon btn-secondary js-btn-view" data-id="'.$data[$i]['id_asset'].'" title="Ver"><i class="bx bx-eye"></i></button>';
				}
				$data[$i]['options'] = '<div class="text-center">'.$btnView.'</div>';
			}
			echo json_encode($data, JSON_UNESCAPED_UNICODE);
		}

		public function show_assets_without_movements()
		{
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			check_permissions(MODULE_INVENTORY);
			if(empty($_SESSION['module_permissions']['r_read_permission'])){
				header("Location: " . base_url() . "/errors/forbidden");
				die();
			}
			$int_id_asset = intval($_POST['id_asset'] ?? 0);
			$data = $this->model->show_assets_without_movements($int_id_asset);
			for ($i=0; $i < count($data); $i++) {
				$btnView = '';				
				if($_SESSION['module_permissions']['r_read_permission']){
					$btnView = '<button type="button" class="btn icon btn-secondary js-btn-view" data-id="'.$data[$i]['id_asset'].'" title="Ver"><i class="bx bx-eye"></i></button>';
				}
				$data[$i]['options'] = '<div class="text-center">'.$btnView.'</div>';
			}
			echo json_encode($data, JSON_UNESCAPED_UNICODE);
		}

		public function show_assets_without_scheduled_maintenance()
		{
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			check_permissions(MODULE_INVENTORY);
			if(empty($_SESSION['module_permissions']['r_read_permission'])){
				header("Location: " . base_url() . "/errors/forbidden");
				die();
			}
			$int_id_asset = intval($_POST['id_asset'] ?? 0);
			$data = $this->model->show_assets_without_scheduled_maintenance($int_id_asset);			
			echo json_encode($data, JSON_UNESCAPED_UNICODE);
		}

		public function show_asset() 
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_INVENTORY);
			if (!$_SESSION['module_permissions']['r_read_permission']) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para leer el registro.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_asset'])) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrió un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_asset = trim($_POST['id_asset']);
				$request_asset = $this->model->show_asset($int_id_asset);
				if($request_asset['entry_date_asset'] == NULL){
					$request_asset['entry_date_asset'] = 'No definido';
				}
				if($request_asset['acquisition_date_asset'] == NULL){
					$request_asset['acquisition_date_asset'] = 'No definido';
				}
				if ($request_asset) {
					$request_attachments = $this->model->show_files($int_id_asset);
					$attachments = normalized_attachment(
						$request_attachments, 
						'id_attachment_asset', 
						'name_attachment_asset', 
						'route_attachment_asset'
					);
					$response = array("status" => true, "result" => $request_asset, "attachments" => $attachments);
				} else {
					$response = array("status" => false, "type" => "error", "message" => "No se pudo leer el registro.");
				}
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se enviaron datos.");
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}
		
		public function insert_asset() 
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_INVENTORY);
			if (!$_SESSION['module_permissions']['w_write_permission']) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para registrar nueva categoría.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_type_asset'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de tipo.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['id_category_asset'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de categoría.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['id_brand'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de marca.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['id_condition_asset'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de condición.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}										
				if (!isset($_POST['status_active_asset'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de estado.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_type_asset = trim($_POST['id_type_asset']);
				$int_id_category_asset = trim($_POST['id_category_asset']);
				$int_id_brand = trim($_POST['id_brand']);
				$int_id_condition_asset = trim($_POST['id_condition_asset']);
				$str_code_inventory_asset = "INV-" . date("YmdHis") . "-" . strtoupper(substr(uniqid(), -4));
				// $str_code_history_movement_asset = "MOV" . strtoupper(bin2hex(random_bytes(3))) . date("YmdHis");
				$str_model_asset = trim($_POST['model_asset'] ?? '') ?: 'Ninguna.';
				$str_serial_number_asset = trim($_POST['serial_number_asset'] ?? '') ?: 'Ninguna.';
				$str_entry_date_asset = !empty($_POST['entry_date_asset']) ? date("Y-m-d", strtotime(trim($_POST['entry_date_asset']))) : NULL;
				$str_acquisition_date_asset = !empty($_POST['acquisition_date_asset']) ? date("Y-m-d", strtotime(trim($_POST['acquisition_date_asset']))) : NULL;
				$str_descriptions_asset = trim($_POST['descriptions_asset'] ?? '') ?: 'Ninguna.';
				$str_observations_asset = trim($_POST['observations_asset'] ?? '') ?: 'Ninguna.';
				$int_status_active_asset = trim($_POST['status_active_asset']);
				$files = isset($_FILES['document']) && $_FILES['document']['size'] > 0 ? $_FILES['document'] : null;
				// Verificar la cantidad y el tamaño de los archivos
				if ($files) {
					$response = check_files($files, 10);
					if ($response != "succes") {
						echo json_encode($response, JSON_UNESCAPED_UNICODE);
						return;
					}
				}
				$request_asset = $this->model->insert_asset(
					$int_id_type_asset,
					$int_id_category_asset,
					$int_id_brand,
					$int_id_condition_asset,
					$str_code_inventory_asset,
					$str_model_asset,
					$str_serial_number_asset,
					$str_entry_date_asset,
					$str_acquisition_date_asset,
					$str_descriptions_asset,
					$str_observations_asset,
					$int_status_active_asset
				);
				if ($request_asset) {
					// Ahora insertar los archivos asociados al registro
					if ($files) {
						upload_attachments_asset($files, $request_asset);
					}
					$response = array("status" => true, "type" => "success", "message" => "Registrado correctamente.");
				} else {
					$response = array("status" => false, "type" => "error", "message" => "No se pudo registrar nuevo activo.");
				}
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se enviaron datos.");
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}

		public function update_asset() 
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_INVENTORY);
			if (!$_SESSION['module_permissions']['u_update_permission'] || !submenu_action_permission_allowed(MODULE_INVENTORY, 'Inventario', 'Activos', 'Editar')) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para actualizar el registro.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_asset'])) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrió un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['id_type_asset'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de tipo.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['id_category_asset'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de categoría.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['id_brand'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de marca.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['id_condition_asset'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de condición.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['model_asset'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese el nombre de modelo.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['serial_number_asset'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese el número de serie.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}				
				if (empty($_POST['descriptions_asset'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese la descripción.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['observations_asset'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese la observación.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (!isset($_POST['status_active_asset'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de estado.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_asset = trim($_POST['id_asset']);
				$int_id_type_asset = trim($_POST['id_type_asset']);
				$int_id_category_asset = trim($_POST['id_category_asset']);
				$int_id_brand = trim($_POST['id_brand']);
				$int_id_condition_asset = trim($_POST['id_condition_asset']);				
				$str_model_asset = trim($_POST['model_asset']);
				$str_serial_number_asset = trim($_POST['serial_number_asset']);
				$str_entry_date_asset = !empty($_POST['entry_date_asset']) ? date("Y-m-d", strtotime(trim($_POST['entry_date_asset']))) : NULL;
				$str_acquisition_date_asset = !empty($_POST['acquisition_date_asset']) ? date("Y-m-d", strtotime(trim($_POST['acquisition_date_asset']))) : NULL;
				$str_descriptions_asset = trim($_POST['descriptions_asset']);
				$str_observations_asset = trim($_POST['observations_asset']);
				$int_status_active_asset = trim($_POST['status_active_asset']);
				$files = isset($_FILES['document']) && $_FILES['document']['size'] > 0 ? $_FILES['document'] : null;
				// Verificar la cantidad y el tamaño de los archivos
				if ($files) {
					$response = check_files($files, 10);
					if ($response != "succes") {
						echo json_encode($response, JSON_UNESCAPED_UNICODE);
						return;
					}
				}
				$request_asset = $this->model->update_asset(
					$int_id_asset, 
					$int_id_type_asset,
					$int_id_category_asset,
					$int_id_brand,
					$int_id_condition_asset,
					$str_model_asset,
					$str_serial_number_asset,
					$str_entry_date_asset,
					$str_acquisition_date_asset,
					$str_descriptions_asset,
					$str_observations_asset,
					$int_status_active_asset
				);
				if ($request_asset) {
					if (!empty($_POST['delete_attachments'])) {
						$delete_attachments = $_POST['delete_attachments'];
						$request_attachments_asset = $this->model->show_files($int_id_asset);
						$attachments_map = [];
						foreach ($request_attachments_asset as $item) {
							$attachments_map[$item['id_attachment_asset']] = $item['route_attachment_asset'];
						}
						foreach ($delete_attachments as $id) {
							if (isset($attachments_map[$id])) {
								delete_file($attachments_map[$id]);
								$this->model->delete_attachment_asset($id);
							}
						}
					}
					if ($files) {
						upload_attachments_asset($files, $int_id_asset);
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
			//ERROR: CONVERTIR EN VALOR ENTERO
			$int_id_asset = trim($_POST['id_asset']);
			$request_files = $this->model->show_files($int_id_asset);					
			$data['attachments_asset'] = $request_files;
			$html = modal_folder($data);
			die();
		}

		public function delete_asset() 
		{
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_INVENTORY);
			if (!$_SESSION['module_permissions']['d_delete_permission']) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para eliminar el registro.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_asset'])) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrió un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_asset = trim($_POST['id_asset']);
				$request_asset = $this->model->delete_asset($int_id_asset);
				if ($request_asset) {
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

		public function code_inventory_asset_specific() {

			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			check_permissions(MODULE_INVENTORY);
			if(empty($_SESSION['module_permissions']['r_read_permission'])){
				header("Location:".base_url().'/errors/forbidden');
				die();
			}
			if (!empty($_GET)) {
				if (empty($_GET['id_asset'])) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrió un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_asset = trim($_GET['id_asset']);
				$request_code_inventory_asset_specific = $this->model->code_inventory_asset_specific(
					$int_id_asset
				);
				if ($request_code_inventory_asset_specific) {
					$payload = $this->build_label_pdf_payload($request_code_inventory_asset_specific);
                    if ($payload === null) return;
                    $this->views->getView($this, "Inventory", "code-inventory", $payload);
				} else {
					echo "No disponible 1";
				}
			} else {
				echo "No se enviaron datos.";
			}
		}

        public function asset_equipment_user_data()
        {
            if ($_SESSION['status_active_user'] === 0) {
                $response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa.");
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                die();
            }
            check_permissions(MODULE_INVENTORY);
            if (empty($_SESSION['module_permissions']['r_read_permission'])) {
                $response = array("status" => false, "type" => "error", "message" => "No tienes permisos para leer el usuario del equipo.");
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                die();
            }
            $int_id_asset = intval($_POST['id_asset'] ?? 0);
            if ($int_id_asset <= 0) {
                $response = array("status" => false, "type" => "error", "message" => "No se pudo identificar el activo.");
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                die();
            }
            $asset = $this->model->show_asset($int_id_asset);
            if (!$asset) {
                $response = array("status" => false, "type" => "error", "message" => "No se encontro el activo seleccionado.");
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                die();
            }
            $response = array(
                "status" => true,
                "asset" => $asset,
                "equipment_user" => $this->model->asset_equipment_user($int_id_asset),
                "permissions" => [
                    'create' => !empty($_SESSION['module_permissions']['w_write_permission']),
                    'edit' => !empty($_SESSION['module_permissions']['u_update_permission'])
                ]
            );
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            die();
        }

        public function save_asset_equipment_user()
        {
            check_permissions(MODULE_INVENTORY);
            $operation = $_POST['operation'] ?? '';
            $permission = $operation === 'create' ? 'w_write_permission' : 'u_update_permission';
            if ($_SESSION['status_active_user'] === 0 || !in_array($operation, ['create', 'update'], true) || empty($_SESSION['module_permissions'][$permission])) {
                echo json_encode(['status'=>false, 'type'=>'error', 'message'=>'No tienes permisos para realizar esta operación.'], JSON_UNESCAPED_UNICODE);
                return;
            }
            $id = (int)($_POST['id_asset'] ?? 0);
            $input = $_POST['username_asset_equipment_user'] ?? '';
            $username = is_string($input) ? trim($input) : '';
            if ($id <= 0 || !$this->model->show_asset($id) || $username === '' || mb_strlen($username) > 150) {
                echo json_encode(['status'=>false, 'type'=>'error', 'message'=>'Selecciona un activo válido y escribe un usuario de hasta 150 caracteres.'], JSON_UNESCAPED_UNICODE);
                return;
            }
            $existing = $this->model->asset_equipment_user($id);
            if (($operation === 'create' && $existing) || ($operation === 'update' && !$existing)) {
                echo json_encode(['status'=>false, 'type'=>'error', 'message'=>'El registro ha cambiado. Cierra el formulario y vuelve a consultar el activo.'], JSON_UNESCAPED_UNICODE);
                return;
            }
            $saved = $this->model->save_asset_equipment_user($id, $username, (int)$_SESSION['user_data']['id_user']);
            echo json_encode(['status'=>(bool)$saved, 'type'=>$saved ? 'success' : 'error', 'message'=>$saved ? ($operation === 'create' ? 'Usuario del equipo registrado correctamente.' : 'Usuario del equipo actualizado correctamente.') : 'No se pudo guardar el usuario del equipo.'], JSON_UNESCAPED_UNICODE);
        }

        public function asset_confidential_note_data()
        {
            if ($_SESSION['status_active_user'] === 0) {
                $response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa.");
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                die();
            }
            check_permissions(MODULE_INVENTORY);
            if (empty($_SESSION['module_permissions']['r_read_permission'])) {
                $response = array("status" => false, "type" => "error", "message" => "No tienes permisos para leer la nota privada.");
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                die();
            }
            $int_id_asset = intval($_POST['id_asset'] ?? 0);
            if ($int_id_asset <= 0) {
                $response = array("status" => false, "type" => "error", "message" => "No se pudo identificar el activo.");
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                die();
            }
            $asset = $this->model->show_asset($int_id_asset);
            if (!$asset) {
                $response = array("status" => false, "type" => "error", "message" => "No se encontro el activo seleccionado.");
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                die();
            }
            $response = array(
                "status" => true,
                "asset" => $asset,
                "confidential_note" => $this->model->asset_confidential_note($int_id_asset) ?: array(),
                "permissions" => [
                    'create' => !empty($_SESSION['module_permissions']['w_write_permission']),
                    'edit' => !empty($_SESSION['module_permissions']['u_update_permission']),
                    'delete' => !empty($_SESSION['module_permissions']['d_delete_permission'])
                ]
            );
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            die();
        }

        public function save_asset_confidential_note()
        {
            check_permissions(MODULE_INVENTORY);
            $operation = $_POST['operation'] ?? '';
            $permission = $operation === 'create' ? 'w_write_permission' : 'u_update_permission';
            if ($_SESSION['status_active_user'] === 0 || !in_array($operation, ['create', 'update'], true) || empty($_SESSION['module_permissions'][$permission])) {
                echo json_encode(['status' => false, 'type' => 'error', 'message' => 'No tienes permisos para realizar esta operación.'], JSON_UNESCAPED_UNICODE);
                return;
            }
            $id = (int)($_POST['id_asset'] ?? 0);
            $input = $_POST['confidential_note_asset'] ?? '';
            $note = is_string($input) ? trim($input) : '';
            if ($id <= 0 || !$this->model->show_asset($id) || $note === '' || mb_strlen($note) > 5000) {
                echo json_encode(['status' => false, 'type' => 'error', 'message' => 'Selecciona un activo válido y escribe una nota de hasta 5000 caracteres. Para quitarla utiliza Eliminar.'], JSON_UNESCAPED_UNICODE);
                return;
            }
            $existing = $this->model->asset_confidential_note($id);
            $hasNote = trim($existing['confidential_note_asset'] ?? '') !== '';
            if (($operation === 'create' && $hasNote) || ($operation === 'update' && !$hasNote)) {
                echo json_encode(['status' => false, 'type' => 'error', 'message' => 'La nota ha cambiado. Cierra el formulario y vuelve a consultar el activo.'], JSON_UNESCAPED_UNICODE);
                return;
            }
            $saved = $this->model->save_asset_confidential_note($id, $note, (int)$_SESSION['user_data']['id_user']);
            echo json_encode(['status' => (bool)$saved, 'type' => $saved ? 'success' : 'error', 'message' => $saved ? ($operation === 'create' ? 'Nota privada registrada correctamente.' : 'Nota privada actualizada correctamente.') : 'No se pudo guardar la nota privada.'], JSON_UNESCAPED_UNICODE);
        }

        public function delete_asset_confidential_note()
        {
            check_permissions(MODULE_INVENTORY);
            if ($_SESSION['status_active_user'] === 0 || empty($_SESSION['module_permissions']['d_delete_permission'])) {
                echo json_encode(['status' => false, 'type' => 'error', 'message' => 'No tienes permisos para eliminar la nota.'], JSON_UNESCAPED_UNICODE);
                return;
            }
            $id = (int)($_POST['id_asset'] ?? 0);
            $existing = $id > 0 && $this->model->show_asset($id) ? $this->model->asset_confidential_note($id) : false;
            if (!$existing || trim($existing['confidential_note_asset'] ?? '') === '') {
                echo json_encode(['status' => false, 'type' => 'error', 'message' => 'No hay una nota registrada para este activo.'], JSON_UNESCAPED_UNICODE);
                return;
            }
            $deleted = $this->model->save_asset_confidential_note($id, '', (int)$_SESSION['user_data']['id_user']);
            echo json_encode(['status' => (bool)$deleted, 'type' => $deleted ? 'success' : 'error', 'message' => $deleted ? 'Nota privada eliminada correctamente.' : 'No se pudo eliminar la nota.'], JSON_UNESCAPED_UNICODE);
        }


		private function build_label_pdf_payload(array $labels): ?array
		{
			$id_template = (int)($_POST['id_template'] ?? $_GET['id_template'] ?? 0);
			$template = null;
			if ($id_template > 0) {
				$template = $this->model->label_template_for_pdf($id_template);
			}
            if (!$template || !is_file(__DIR__.'/../../'.ltrim((string)$template['design_route_label_template'],'/'))) {
                http_response_code(400);
                echo 'Seleccione una plantilla activa registrada en Administración > Parámetros > Etiquetas.';
                return null;
            }
			return [
				"labels" => $labels,
				"template" => $template
			];
		}
	}
?>