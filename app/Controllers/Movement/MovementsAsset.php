<?php 

	class MovementsAsset extends Controllers{
		
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
		
        public function manage_movements_asset()
		{
            if (!$this->movementJsonAccess('Ver')) return;
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			check_permissions(MODULE_MOVEMENT);
			if(empty($_SESSION['module_permissions']['r_read_permission'])){
				header("Location:".base_url().'/errors/forbidden');
				die();
			}
            $_SESSION['movement_token'] ??= bin2hex(random_bytes(32));
            $data['css'] = [
				"/extensions/datatables/datatables.min.css"
			];
			$data['script'] = [
				"/js/language_datatable.js",
				"/js/upload_handler.js",
				"/extensions/datatables/datatables.min.js"
			];
			$data['page_functions_js'] = [
				"functions_manage_movements_asset.js"
			];
			$this->views->getView($this, "Movement", "index", $data);
		}

        public function show_movements_asset()
        {
            if (!$this->movementJsonAccess()) return;
            echo json_encode($this->model->show_movements_asset(),JSON_UNESCAPED_UNICODE);
        }

        private function movementJsonAccess(string $action = 'Ver', string $submenu = 'Activos'): bool
        {
            check_permissions(MODULE_MOVEMENT);
            $key = ['Ver'=>'r_read_permission','Crear'=>'w_write_permission','Editar'=>'u_update_permission','Eliminar'=>'d_delete_permission'][$action];
            if ((int)$_SESSION['status_active_user'] !== 1 || empty($_SESSION['module_permissions'][$key]) || !submenu_action_permission_allowed(MODULE_MOVEMENT,'Movimiento',$submenu,$action)) {
                http_response_code(403);
                echo json_encode(['status'=>false,'type'=>'error','message'=>'No tienes permiso para esta operación.'],JSON_UNESCAPED_UNICODE);
                return false;
            }
            return true;
        }
        public function available_assets()
        {
            if (!$this->movementJsonAccess()) return;
            echo json_encode($this->model->pending_assets(),JSON_UNESCAPED_UNICODE);
        }
        public function asset_context()
        {
            if (!$this->movementJsonAccess()) return;
            $asset = $this->model->asset_context((int)($_POST['id_asset'] ?? 0));
            echo json_encode(['status'=>(bool)$asset,'result'=>$asset,'message'=>$asset ? '' : 'Activo no disponible.'],JSON_UNESCAPED_UNICODE);
        }
        public function movement_lookups()
        {
            if (!$this->movementJsonAccess()) return;
            require_once 'Models/FloorPlansModel.php';
            require_once 'Models/TypesMovementModel.php';
            require_once 'Models/EnvironmentsModel.php';
            echo json_encode(['status'=>true,'timezone'=>date_default_timezone_get(),'types'=>(new TypesMovementModel())->combo_types_movement(),'spaces'=>(new EnvironmentsModel())->show_environments_available(),'plans'=>(new FloorPlansModel())->combo_floor_plans()],JSON_UNESCAPED_UNICODE);
        }
        public function location_lookups()
        {
            if (!$this->movementJsonAccess('Ver','Ubicacion de Activos')) return;
            require_once 'Models/FloorPlansModel.php';
            echo json_encode(['status'=>true,'result'=>(new FloorPlansModel())->combo_floor_plans()],JSON_UNESCAPED_UNICODE);
        }
        public function available_spaces()
        {
            if (!$this->movementJsonAccess()) return;
            require_once 'Models/EnvironmentsModel.php';
            echo json_encode((new EnvironmentsModel())->show_environments_available(),JSON_UNESCAPED_UNICODE);
        }
        public function space_context()
        {
            if (!$this->movementJsonAccess()) return;
            require_once 'Models/EnvironmentsModel.php';
            $space=(new EnvironmentsModel())->show_environment((int)($_POST['id_space'] ?? 0));
            echo json_encode(['status'=>(bool)$space,'result'=>$space],JSON_UNESCAPED_UNICODE);
        }
        public function annul_movement()
        {
            if (!$this->movementJsonAccess('Eliminar')) return;
            if (!$this->validMovementToken()) return;
            try {
                $saved=$this->model->annul_movement((int)($_POST['id_movement_asset'] ?? 0),trim($_POST['reason'] ?? ''),(int)$_SESSION['user_data']['id_user']);
                echo json_encode(['status'=>$saved,'type'=>'success','message'=>'Movimiento anulado. Se restauró la situación anterior.'],JSON_UNESCAPED_UNICODE);
            } catch (DomainException $e) {
                echo json_encode(['status'=>false,'type'=>'error','message'=>$e->getMessage()],JSON_UNESCAPED_UNICODE);
            }
        }
        private function validEventInput(): bool
        {
            foreach (['date_event_movement_asset'=>'normalize_movement_date','time_event_movement_asset'=>'normalize_movement_time'] as $field=>$normalizer) {
                $value=$_POST[$field] ?? '';
                if (!is_string($value) || (trim($value)!=='' && $this->$normalizer($value)===null)) {
                    echo json_encode(['status'=>false,'type'=>'error','message'=>'Indica fecha y hora válidas o déjalas sin definir.'],JSON_UNESCAPED_UNICODE);
                    return false;
                }
            }
            return true;
        }
        private function validMovementToken(): bool
        {
            if (empty($_SESSION['movement_token']) || $_SERVER['REQUEST_METHOD'] !== 'POST' || !is_string($_POST['movement_token'] ?? null) || !hash_equals($_SESSION['movement_token'] ?? '',$_POST['movement_token'])) {
                http_response_code(403);
                echo json_encode(['status'=>false,'type'=>'error','message'=>'Actualiza la página antes de guardar.'],JSON_UNESCAPED_UNICODE);
                return false;
            }
            return true;
        }

		public function history_movement_asset($id_asset = 0)
		{
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			check_permissions(MODULE_MOVEMENT);
			if(empty($_SESSION['module_permissions']['r_read_permission'])){
				header("Location:".base_url().'/errors/forbidden');
				die();
			}
            if (!submenu_action_permission_allowed(MODULE_MOVEMENT, 'Movimiento', 'Activos', 'Ver')) {
                header('Location: '.base_url().'/errors/forbidden'); exit;
            }
            $data['history_asset'] = $this->model->select('SELECT id_asset,code_inventory_asset FROM assets WHERE id_asset=? AND status_deleted_asset=0',[(int)$id_asset]);
            if (!$data['history_asset']) { header('Location: '.base_url().'/errors/not_found'); exit; }
            $data['css'] = [
				"/extensions/datatables/datatables.min.css"
			];
			$data['script'] = [
				"/js/language_datatable.js",
				"/extensions/datatables/datatables.min.js"
			];
			$data['page_functions_js'] = [
				"functions_history_movement_asset.js"
			];
			$this->views->getView($this, "Movement", "history", $data);
		}

		public function show_history_movement_asset()
		{
            if (!$this->movementJsonAccess('Ver')) return;
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			check_permissions(MODULE_MOVEMENT);
			if(empty($_SESSION['module_permissions']['r_read_permission'])){
				header("Location: " . base_url() . "/errors/forbidden");
				die();
			}
			$int_id_asset = intval($_POST['id_asset'] ?? 0);
			$data = $this->model->show_history_movement_asset($int_id_asset);
			for ($i=0; $i < count($data); $i++) {
				$btnView = '';
                $data[$i]['movement_record_state'] = (int)$data[$i]['status_annulled_movement_asset'] === 1 ? 'Anulado' : ((int)$data[$i]['status_record_current_movement_asset'] === 1 ? 'Vigente' : 'Cerrado');
				if($data[$i]['date_event_movement_asset'] == NULL){
					$data[$i]['date_event_movement_asset'] = '<span class="badge bg-danger">No definido</span>';
				}
				if(empty($data[$i]['time_event_movement_asset'])){
					$data[$i]['time_event_movement_asset'] = '<span class="badge bg-secondary">No definido</span>';
				}
				if(empty($data[$i]['expected_return_date_movement_asset'])){
					$data[$i]['expected_return_date_movement_asset'] = '<span class="badge bg-secondary">No aplica</span>';
				}
				$data[$i]['responsible_employee'] = trim($data[$i]['manual_responsible_movement_asset'] ?? '') ?: (trim($data[$i]['loan_recipient_name_movement_asset'] ?? '') ?: trim($data[$i]['responsible_employee'] ?? ''));
				if(empty($data[$i]['responsible_employee'])){
					$data[$i]['responsible_employee'] = '<span class="text-muted">No asignado</span>';
				}
				if($_SESSION['module_permissions']['r_read_permission']){
					$btnView = '<button type="button" class="btn icon btn-secondary js-btn-view" data-id="'.$data[$i]['id_movement_asset'].'" title="Ver"><i class="bx bx-eye"></i></button>';
				}
				$data[$i]['options'] = '<div class="text-center">'.$btnView.'</div>';
			}
			echo json_encode($data, JSON_UNESCAPED_UNICODE);
		}

		public function show_movement_asset() 
		{
            if (!$this->movementJsonAccess('Ver')) return;
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_MOVEMENT);
			if (!$_SESSION['module_permissions']['r_read_permission']) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para leer el registro.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_movement_asset'])) {
					$response = array("status" => false, "type" => "error", "message" => "No se recibió un identificador válido. Actualiza la página.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_movement_asset = trim($_POST['id_movement_asset']);
				$request_movement_asset = $this->model->show_movement_asset($int_id_movement_asset);
				if ($request_movement_asset) {
					if($request_movement_asset['date_event_movement_asset'] == NULL){
						$request_movement_asset['date_event_movement_asset'] = 'No definido';
					}
					if(empty($request_movement_asset['time_event_movement_asset'])){
						$request_movement_asset['time_event_movement_asset'] = 'No definido';
					}
					if(empty($request_movement_asset['expected_return_date_movement_asset'])){
						$request_movement_asset['expected_return_date_movement_asset'] = 'No aplica';
					}
					$request_movement_asset['manual_responsible_movement_asset'] = trim($request_movement_asset['manual_responsible_movement_asset'] ?? '');
					$request_movement_asset['responsible_receiver'] = $request_movement_asset['manual_responsible_movement_asset'] ?: trim($request_movement_asset['responsible_employee'] ?? '');
					if(empty(trim($request_movement_asset['responsible_employee'] ?? ''))){
						$request_movement_asset['responsible_employee'] = 'No asignado';
					}
					if(empty(trim($request_movement_asset['document_code_movement_asset'] ?? ''))){
						$request_movement_asset['document_code_movement_asset'] = 'No definido';
					}
					if(empty(trim($request_movement_asset['status_process_movement_asset'] ?? ''))){
						$request_movement_asset['status_process_movement_asset'] = 'Registrado';
					}
					$request_movement_asset['loan_recipient_type_movement_asset'] = trim($request_movement_asset['loan_recipient_type_movement_asset'] ?? '');
					$request_movement_asset['loan_recipient_name_movement_asset'] = trim($request_movement_asset['loan_recipient_name_movement_asset'] ?? '');
					$request_movement_asset['loan_recipient_document_movement_asset'] = trim($request_movement_asset['loan_recipient_document_movement_asset'] ?? '');
					$request_movement_asset['loan_return_indefinite_movement_asset'] = (int)($request_movement_asset['loan_return_indefinite_movement_asset'] ?? 0);
					$request_movement_asset['loan_recipient_type_label'] = $this->loan_recipient_type_label($request_movement_asset['loan_recipient_type_movement_asset']);
					$request_attachments = $this->model->show_files($int_id_movement_asset);
					$attachments = normalized_attachment($request_attachments, 'id_attachment_movement_asset', 'name_attachment_movement_asset', 'route_attachment_movement_asset');
					$response = array("status" => true, "result" => $request_movement_asset, "attachments" => $attachments);
				} else {
					$response = array("status" => false, "type" => "error", "message" => "No se pudo leer el registro.");
				}
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se enviaron datos.");
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}
		
		public function insert_movement_asset() 
		{
            if (!$this->movementJsonAccess('Crear') || !$this->validMovementToken() || !$this->validEventInput()) return;
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_MOVEMENT);
			if (!$_SESSION['module_permissions']['w_write_permission']) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para registrar movimientos.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_asset'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione un activo.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}		
				if (empty($_POST['id_space'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una ubicacion, ambiente o almacen.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['id_type_movement'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione un tipo de movimiento.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_asset_movement_asset = trim($_POST['id_asset']);
				$int_id_space_movement_asset = trim($_POST['id_space']);
				$int_id_floor_plan_movement_asset = !empty($_POST['id_floor_plan_movement_asset']) ? intval($_POST['id_floor_plan_movement_asset']) : NULL;
				$float_coordinate_x_movement_asset = ($_POST['coordinate_x_movement_asset'] ?? '') !== '' ? floatval($_POST['coordinate_x_movement_asset']) : NULL;
				$float_coordinate_y_movement_asset = ($_POST['coordinate_y_movement_asset'] ?? '') !== '' ? floatval($_POST['coordinate_y_movement_asset']) : NULL;
				$int_id_type_movement_asset = trim($_POST['id_type_movement']);
				$int_id_responsible_employee_movement_asset = NULL;
				$str_date_event_movement_asset = $this->normalize_movement_date($_POST['date_event_movement_asset'] ?? null);
				$str_time_event_movement_asset = $this->normalize_movement_time($_POST['time_event_movement_asset'] ?? null);
				$int_loan_return_indefinite_movement_asset = !empty($_POST['loan_return_indefinite_movement_asset']) ? 1 : 0;
				$str_expected_return_date_movement_asset = (!$int_loan_return_indefinite_movement_asset && !empty($_POST['expected_return_date_movement_asset'])) ? date("Y-m-d", strtotime(trim($_POST['expected_return_date_movement_asset']))) : NULL;
				$str_document_code_movement_asset = $this->generate_movement_document_code();
				$str_status_process_movement_asset = $this->resolve_movement_status($int_id_type_movement_asset);
				$str_loan_recipient_type_movement_asset = $int_id_type_movement_asset == 5 ? trim($_POST['loan_recipient_type_movement_asset'] ?? '') : NULL;
				$str_loan_recipient_name_movement_asset = $int_id_type_movement_asset == 5 ? trim($_POST['loan_recipient_name_movement_asset'] ?? '') : NULL;
				$str_loan_recipient_document_movement_asset = $int_id_type_movement_asset == 5 ? trim($_POST['loan_recipient_document_movement_asset'] ?? '') : NULL;
				$str_manual_responsible_movement_asset = trim($_POST['manual_responsible_movement_asset'] ?? '') ?: NULL;
				$str_observations_movement_asset = trim($_POST['observations_movement_asset'] ?? '') ?: 'Ninguna.';
				$files = isset($_FILES['document']) ? $_FILES['document'] : null;
				$validation = $this->validate_movement_flow(
					$int_id_asset_movement_asset,
					$int_id_space_movement_asset,
					$int_id_type_movement_asset,
					$int_id_responsible_employee_movement_asset,
					$str_date_event_movement_asset,
					$str_expected_return_date_movement_asset,
					$str_loan_recipient_type_movement_asset,
					$str_loan_recipient_name_movement_asset,
					$int_loan_return_indefinite_movement_asset,
					$str_manual_responsible_movement_asset,
					'insert'
				);
				if (!$validation['status']) {
					echo json_encode($validation, JSON_UNESCAPED_UNICODE);
					return;
				}
				try {
                    $request_movement_asset = $this->model->insert_movement_asset(
					$int_id_asset_movement_asset,
					$int_id_space_movement_asset,
					$int_id_floor_plan_movement_asset,
					$float_coordinate_x_movement_asset,
					$float_coordinate_y_movement_asset,
					$int_id_type_movement_asset,
					$int_id_responsible_employee_movement_asset,
					$str_date_event_movement_asset,
					$str_time_event_movement_asset,
					$str_expected_return_date_movement_asset,
					$str_document_code_movement_asset,
					$str_status_process_movement_asset,
					$str_loan_recipient_type_movement_asset,
					$str_loan_recipient_name_movement_asset,
					$str_loan_recipient_document_movement_asset,
					$int_loan_return_indefinite_movement_asset,
					$str_manual_responsible_movement_asset,
					$str_observations_movement_asset					
				);
                } catch (DomainException $e) {
                    echo json_encode(['status'=>false,'type'=>'error','message'=>$e->getMessage()],JSON_UNESCAPED_UNICODE);
                    return;
                }
				if ($request_movement_asset) {
					if ($files && !empty($files['name'][0])) {
						upload_attachments_movement_asset($files, $request_movement_asset);
					}
					$response = array("status" => true, "type" => "success", "message" => "Registrado correctamente.");
				} else {
					$response = array("status" => false, "type" => "error", "message" => "No se pudo registrar el movimiento.");
				}
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se enviaron datos.");
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}

		public function move_movement_asset() 
		{
            if (!$this->movementJsonAccess('Crear') || !$this->validMovementToken() || !$this->validEventInput()) return;
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_MOVEMENT);
			if (!$_SESSION['module_permissions']['w_write_permission']) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para actualizar el registro.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_movement_asset'])) {
					$response = array("status" => false, "type" => "error", "message" => "No se recibió un identificador válido. Actualiza la página.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['id_asset'])) {
					$response = array("status" => false, "type" => "error", "message" => "No se recibió un identificador válido. Actualiza la página.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}		
				if (empty($_POST['id_space'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una ubicacion, ambiente o almacen.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['id_type_movement'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione un tipo de movimiento.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_movement_asset = trim($_POST['id_movement_asset']);
				$int_id_asset_movement_asset = trim($_POST['id_asset']);
				$int_id_space_movement_asset = trim($_POST['id_space']);
				$int_id_floor_plan_movement_asset = !empty($_POST['id_floor_plan_movement_asset']) ? intval($_POST['id_floor_plan_movement_asset']) : NULL;
				$float_coordinate_x_movement_asset = ($_POST['coordinate_x_movement_asset'] ?? '') !== '' ? floatval($_POST['coordinate_x_movement_asset']) : NULL;
				$float_coordinate_y_movement_asset = ($_POST['coordinate_y_movement_asset'] ?? '') !== '' ? floatval($_POST['coordinate_y_movement_asset']) : NULL;
				$int_id_type_movement_asset = trim($_POST['id_type_movement']);
				$int_id_responsible_employee_movement_asset = NULL;
				$str_date_event_movement_asset = $this->normalize_movement_date($_POST['date_event_movement_asset'] ?? null);
				$str_time_event_movement_asset = $this->normalize_movement_time($_POST['time_event_movement_asset'] ?? null);
				$int_loan_return_indefinite_movement_asset = !empty($_POST['loan_return_indefinite_movement_asset']) ? 1 : 0;
				$str_expected_return_date_movement_asset = (!$int_loan_return_indefinite_movement_asset && !empty($_POST['expected_return_date_movement_asset'])) ? date("Y-m-d", strtotime(trim($_POST['expected_return_date_movement_asset']))) : NULL;
				$str_document_code_movement_asset = $this->generate_movement_document_code();
				$str_status_process_movement_asset = $this->resolve_movement_status($int_id_type_movement_asset);
				$str_loan_recipient_type_movement_asset = $int_id_type_movement_asset == 5 ? trim($_POST['loan_recipient_type_movement_asset'] ?? '') : NULL;
				$str_loan_recipient_name_movement_asset = $int_id_type_movement_asset == 5 ? trim($_POST['loan_recipient_name_movement_asset'] ?? '') : NULL;
				$str_loan_recipient_document_movement_asset = $int_id_type_movement_asset == 5 ? trim($_POST['loan_recipient_document_movement_asset'] ?? '') : NULL;
				$str_manual_responsible_movement_asset = trim($_POST['manual_responsible_movement_asset'] ?? '') ?: NULL;
				$str_observations_movement_asset = trim($_POST['observations_movement_asset'] ?? '') ?: 'Ninguna.';
				$files = isset($_FILES['document']) ? $_FILES['document'] : null;
				$validation = $this->validate_movement_flow(
					$int_id_asset_movement_asset,
					$int_id_space_movement_asset,
					$int_id_type_movement_asset,
					$int_id_responsible_employee_movement_asset,
					$str_date_event_movement_asset,
					$str_expected_return_date_movement_asset,
					$str_loan_recipient_type_movement_asset,
					$str_loan_recipient_name_movement_asset,
					$int_loan_return_indefinite_movement_asset,
					$str_manual_responsible_movement_asset,
					'move',
					$int_id_movement_asset
				);
				if (!$validation['status']) {
					echo json_encode($validation, JSON_UNESCAPED_UNICODE);
					return;
				}
				try {
                    $request_movement_asset = $this->model->move_movement_asset(
					$int_id_movement_asset, 
					$int_id_asset_movement_asset,
					$int_id_space_movement_asset,
					$int_id_floor_plan_movement_asset,
					$float_coordinate_x_movement_asset,
					$float_coordinate_y_movement_asset,
					$int_id_type_movement_asset,
					$int_id_responsible_employee_movement_asset,
					$str_date_event_movement_asset,
					$str_time_event_movement_asset,
					$str_expected_return_date_movement_asset,
					$str_document_code_movement_asset,
					$str_status_process_movement_asset,
					$str_loan_recipient_type_movement_asset,
					$str_loan_recipient_name_movement_asset,
					$str_loan_recipient_document_movement_asset,
					$int_loan_return_indefinite_movement_asset,
					$str_manual_responsible_movement_asset,
					$str_observations_movement_asset	
				);
                } catch (DomainException $e) {
                    echo json_encode(['status'=>false,'type'=>'error','message'=>$e->getMessage()],JSON_UNESCAPED_UNICODE);
                    return;
                }
				if ($request_movement_asset) {
					if ($files && !empty($files['name'][0])) {
						upload_attachments_movement_asset($files, $request_movement_asset);
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

		public function update_movement_asset() 
		{
            if (!$this->movementJsonAccess('Editar') || !$this->validMovementToken() || !$this->validEventInput()) return;
			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_MOVEMENT);
			if (!$_SESSION['module_permissions']['u_update_permission']) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para actualizar el registro.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_movement_asset'])) {
					$response = array("status" => false, "type" => "error", "message" => "No se recibió un identificador válido. Actualiza la página.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['id_asset'])) {
					$response = array("status" => false, "type" => "error", "message" => "No se recibió un identificador válido. Actualiza la página.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}		
				if (empty($_POST['id_space'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una ubicacion, ambiente o almacen.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['id_type_movement'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione un tipo de movimiento.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_movement_asset = trim($_POST['id_movement_asset']);
				$int_id_asset_movement_asset = trim($_POST['id_asset']);
				$int_id_space_movement_asset = trim($_POST['id_space']);
				$int_id_floor_plan_movement_asset = !empty($_POST['id_floor_plan_movement_asset']) ? intval($_POST['id_floor_plan_movement_asset']) : NULL;
				$float_coordinate_x_movement_asset = ($_POST['coordinate_x_movement_asset'] ?? '') !== '' ? floatval($_POST['coordinate_x_movement_asset']) : NULL;
				$float_coordinate_y_movement_asset = ($_POST['coordinate_y_movement_asset'] ?? '') !== '' ? floatval($_POST['coordinate_y_movement_asset']) : NULL;
				$int_id_type_movement_asset = trim($_POST['id_type_movement']);
				$existingRecord = $this->model->show_movement_asset((int)$int_id_movement_asset);
                $int_id_responsible_employee_movement_asset = !empty($existingRecord['id_responsible_employee_movement_asset']) ? (int)$existingRecord['id_responsible_employee_movement_asset'] : NULL;
				$str_date_event_movement_asset = $this->normalize_movement_date($_POST['date_event_movement_asset'] ?? null);
				$str_time_event_movement_asset = $this->normalize_movement_time($_POST['time_event_movement_asset'] ?? null);
				$int_loan_return_indefinite_movement_asset = !empty($_POST['loan_return_indefinite_movement_asset']) ? 1 : 0;
				$str_expected_return_date_movement_asset = (!$int_loan_return_indefinite_movement_asset && !empty($_POST['expected_return_date_movement_asset'])) ? date("Y-m-d", strtotime(trim($_POST['expected_return_date_movement_asset']))) : NULL;
				$str_document_code_movement_asset = $existingRecord['document_code_movement_asset'] ?? $this->generate_movement_document_code();
				$str_status_process_movement_asset = $this->resolve_movement_status($int_id_type_movement_asset);
				$str_loan_recipient_type_movement_asset = $int_id_type_movement_asset == 5 ? trim($_POST['loan_recipient_type_movement_asset'] ?? '') : NULL;
				$str_loan_recipient_name_movement_asset = $int_id_type_movement_asset == 5 ? trim($_POST['loan_recipient_name_movement_asset'] ?? '') : NULL;
				$str_loan_recipient_document_movement_asset = $int_id_type_movement_asset == 5 ? trim($_POST['loan_recipient_document_movement_asset'] ?? '') : NULL;
				$str_manual_responsible_movement_asset = trim($_POST['manual_responsible_movement_asset'] ?? '') ?: NULL;
				$str_observations_movement_asset = trim($_POST['observations_movement_asset'] ?? '') ?: 'Ninguna.';
				$files = isset($_FILES['document']) ? $_FILES['document'] : null;
				$attachments_deleted = $_POST['delete_attachments'] ?? [];
				$validation = $this->validate_movement_flow(
					$int_id_asset_movement_asset,
					$int_id_space_movement_asset,
					$int_id_type_movement_asset,
					$int_id_responsible_employee_movement_asset,
					$str_date_event_movement_asset,
					$str_expected_return_date_movement_asset,
					$str_loan_recipient_type_movement_asset,
					$str_loan_recipient_name_movement_asset,
					$int_loan_return_indefinite_movement_asset,
					$str_manual_responsible_movement_asset,
					'update',
					$int_id_movement_asset
				);
				if (!$validation['status']) {
					echo json_encode($validation, JSON_UNESCAPED_UNICODE);
					return;
				}

                if (!empty($attachments_deleted)) {
                    echo json_encode(['status'=>false,'type'=>'error','message'=>'Las evidencias previas se conservan. Adjunta nueva documentación para corregir el movimiento.'],JSON_UNESCAPED_UNICODE);
                    return;
                }

				try {
                    $request_movement_asset = $this->model->update_movement_asset(
					$int_id_movement_asset, 
					$int_id_asset_movement_asset,
					$int_id_space_movement_asset,
					$int_id_floor_plan_movement_asset,
					$float_coordinate_x_movement_asset,
					$float_coordinate_y_movement_asset,
					$int_id_type_movement_asset,
					$int_id_responsible_employee_movement_asset,
					$str_date_event_movement_asset,
					$str_time_event_movement_asset,
					$str_expected_return_date_movement_asset,
					$str_document_code_movement_asset,
					$str_status_process_movement_asset,
					$str_loan_recipient_type_movement_asset,
					$str_loan_recipient_name_movement_asset,
					$str_loan_recipient_document_movement_asset,
					$int_loan_return_indefinite_movement_asset,
					$str_manual_responsible_movement_asset,
					$str_observations_movement_asset, trim($_POST['correction_reason'] ?? ''), (int)$_SESSION['user_data']['id_user']	
				);
                } catch (DomainException $e) {
                    echo json_encode(['status'=>false,'type'=>'error','message'=>$e->getMessage()],JSON_UNESCAPED_UNICODE);
                    return;
                }
				if ($request_movement_asset) {
					if ($files && !empty($files['name'][0])) {
						upload_attachments_movement_asset($files, $int_id_movement_asset);
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

        public function delete_movement_asset()
        {
            $this->annul_movement();
        }

		public function format_movement_asset($id_movement_asset = null)
		{
            if (!$this->movementJsonAccess('Ver')) return;
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			check_permissions(MODULE_MOVEMENT);
			if(empty($_SESSION['module_permissions']['r_read_permission'])){
				header("Location: " . base_url() . "/errors/forbidden");
				die();
			}
			$int_id_movement_asset = intval($id_movement_asset);
			$movement = $this->model->show_movement_asset($int_id_movement_asset);
			if (!$movement) {
				header("Location: " . base_url() . "/movement/movements_asset/manage_movements_asset");
				die();
			}
			$movement['date_event_movement_asset'] = $movement['date_event_movement_asset'] ?: 'No definido';
			$movement['time_event_movement_asset'] = $movement['time_event_movement_asset'] ?: 'No definido';
			$movement['expected_return_date_movement_asset'] = $movement['expected_return_date_movement_asset'] ?: 'No aplica';
			$movement['manual_responsible_movement_asset'] = trim($movement['manual_responsible_movement_asset'] ?? '');
			$movement['responsible_receiver'] = $movement['manual_responsible_movement_asset'] ?: trim($movement['responsible_employee'] ?? '');
			$movement['responsible_employee'] = trim($movement['responsible_employee'] ?? '') ?: 'No asignado';
			$movement['document_code_movement_asset'] = trim($movement['document_code_movement_asset'] ?? '') ?: 'No definido';
			$movement['status_process_movement_asset'] = trim($movement['status_process_movement_asset'] ?? '') ?: 'Registrado';
			$movement['loan_recipient_type_movement_asset'] = trim($movement['loan_recipient_type_movement_asset'] ?? '');
			$movement['loan_recipient_name_movement_asset'] = trim($movement['loan_recipient_name_movement_asset'] ?? '');
			$movement['loan_recipient_document_movement_asset'] = trim($movement['loan_recipient_document_movement_asset'] ?? '');
			$movement['loan_return_indefinite_movement_asset'] = (int)($movement['loan_return_indefinite_movement_asset'] ?? 0);
			$movement['loan_recipient_type_label'] = $this->loan_recipient_type_label($movement['loan_recipient_type_movement_asset']);

			$data['movement'] = $movement;
			$this->views->getView($this, "Movement", "format", $data);
		}

		public function asset_locations()
		{
            if (!$this->movementJsonAccess('Ver','Ubicacion de Activos')) return;
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			check_permissions(MODULE_MOVEMENT);
			if(empty($_SESSION['module_permissions']['r_read_permission'])){
				header("Location:" . base_url() . '/errors/forbidden');
				die();
			}
			$data['css'] = ['/extensions/datatables/datatables.min.css'];
            $data['script'] = ['/js/language_datatable.js','/extensions/datatables/datatables.min.js'];
            $data['page_functions_js'] = [
				"functions_asset_locations.js"
			];
			$this->views->getView($this, "Movement", "asset-locations", $data);
		}

		public function show_asset_locations()
		{
            if (!$this->movementJsonAccess('Ver','Ubicacion de Activos')) return;
			if ($_SESSION['status_active_user'] === 0) {
				echo json_encode(array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa."), JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_MOVEMENT);
			if(empty($_SESSION['module_permissions']['r_read_permission'])){
				echo json_encode(array("status" => false, "type" => "error", "message" => "No tienes permisos para leer."), JSON_UNESCAPED_UNICODE);
				return;
			}
			$data = $this->model->show_current_asset_locations(
				!empty($_POST['id_floor_plan']) ? (int)$_POST['id_floor_plan'] : null,
				!empty($_POST['id_type_asset']) ? (int)$_POST['id_type_asset'] : null,
				!empty($_POST['id_category_asset']) ? (int)$_POST['id_category_asset'] : null,
				!empty($_POST['id_space']) ? (int)$_POST['id_space'] : null,
				!empty($_POST['id_condition_asset']) ? (int)$_POST['id_condition_asset'] : null
			);
			echo json_encode(array("status" => true, "result" => $data), JSON_UNESCAPED_UNICODE);
			die();
		}

		public function show_movement_plan_location()
		{
            if (!$this->movementJsonAccess('Ver')) return;
			if ($_SESSION['status_active_user'] === 0) {
				echo json_encode(array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa."), JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_MOVEMENT);
			if(empty($_SESSION['module_permissions']['r_read_permission'])){
				echo json_encode(array("status" => false, "type" => "error", "message" => "No tienes permisos para leer."), JSON_UNESCAPED_UNICODE);
				return;
			}
			$id = (int)($_POST['id_movement_asset'] ?? 0);
			if ($id <= 0) {
				echo json_encode(array("status" => false, "type" => "error", "message" => "No se recibio el movimiento."), JSON_UNESCAPED_UNICODE);
				return;
			}
			$item = $this->model->show_movement_plan_location($id);
			if (!$item || (empty($item['current_image_floor_plan']) && empty($item['current_file_floor_plan'])) || $item['coordinate_x_movement_asset'] === null || $item['coordinate_y_movement_asset'] === null) {
				echo json_encode(array("status" => false, "type" => "warning", "message" => "No se ha definido una ubicacion en plano para este movimiento."), JSON_UNESCAPED_UNICODE);
				return;
			}
			echo json_encode(array("status" => true, "result" => $item), JSON_UNESCAPED_UNICODE);
			die();
		}

		public function location_movement_asset($id_movement_asset = null)
		{
            if (!$this->movementJsonAccess('Ver')) return;
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			check_permissions(MODULE_MOVEMENT);
			if(empty($_SESSION['module_permissions']['r_read_permission'])){
				header("Location:" . base_url() . '/errors/forbidden');
				die();
			}
			$movement = $this->model->show_movement_plan_location((int)$id_movement_asset);
			if (!$movement || (empty($movement['current_image_floor_plan']) && empty($movement['current_file_floor_plan'])) || $movement['coordinate_x_movement_asset'] === null || $movement['coordinate_y_movement_asset'] === null) {
				header("Location: " . base_url() . "/movement/movements_asset/manage_movements_asset");
				die();
			}
			$data['movement'] = $movement;
			$this->views->getView($this, "Movement", "location", $data);
		}

		private function normalize_movement_time($time): ?string
		{
			$time = trim((string)($time ?? ''));
			if ($time === '') {
				return null;
			}
			if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/', $time)) {
				return null;
			}
			return strlen($time) === 5 ? $time . ':00' : $time;
		}

		private function normalize_movement_date($date): ?string
		{
			$date = trim((string)($date ?? ''));
			if ($date === '') {
				return null;
			}

			$parsed = DateTime::createFromFormat('!d/m/Y', $date);
			$errors = DateTime::getLastErrors();
			if ($parsed && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))) {
				return $parsed->format('Y-m-d');
			}

			return null;
		}

		private function validate_movement_flow(
			int $id_asset,
			int $id_space,
			int $id_type_movement,
			?int $id_responsible_employee,
			?string $date_event,
			?string $expected_return_date,
			?string $loan_recipient_type,
			?string $loan_recipient_name,
			int $loan_return_indefinite,
			?string $manual_responsible,
			string $mode,
			?int $id_current_movement = null
		) {
			if ($id_asset <= 0 || $id_type_movement <= 0) {
				return array("status" => false, "type" => "error", "message" => "Complete activo y tipo de movimiento.");
			}
			if ($id_space <= 0) {
				return array("status" => false, "type" => "error", "message" => $this->movement_location_required_message($id_type_movement));
			}
			if ($id_type_movement === 5) {
				if (empty($loan_recipient_type)) {
					return array("status" => false, "type" => "error", "message" => "Seleccione el tipo de prestamo.");
				}
				if (!in_array($loan_recipient_type, ['internal', 'external_person', 'external_company'], true)) {
					return array("status" => false, "type" => "error", "message" => "El tipo de prestamo no es valido.");
				}
				if ($loan_recipient_type === 'internal' && empty($id_responsible_employee) && empty($loan_recipient_name)) {
					return array("status" => false, "type" => "error", "message" => "Registra el nombre de la persona que recibe el préstamo.");
				}
				if (in_array($loan_recipient_type, ['external_person', 'external_company'], true) && empty($loan_recipient_name)) {
					return array("status" => false, "type" => "error", "message" => "Ingrese el nombre de la persona, empresa o institucion que recibe el prestamo.");
				}
				if (!$loan_return_indefinite && empty($expected_return_date)) {
					return array("status" => false, "type" => "error", "message" => "Ingrese la fecha esperada de devolucion o marque devolucion indeterminada.");
				}
			}
			if (!empty($expected_return_date)) {
				$movement_date = $date_event;
				if ($movement_date && strtotime($expected_return_date) < strtotime($movement_date)) {
					return array("status" => false, "type" => "error", "message" => "La fecha esperada de devolucion no puede ser menor a la fecha del movimiento.");
				}
			}

			$current_movement = $this->model->get_current_movement_by_asset($id_asset);
			if ($mode === 'insert' && $current_movement) {
				return array("status" => false, "type" => "error", "message" => "El activo ya tiene un movimiento vigente. Usa Nuevo o Registrar movimiento desde el activo.");
			}
			if (in_array($mode, ['move', 'update']) && !$current_movement) {
				return array("status" => false, "type" => "error", "message" => "No se encontro movimiento vigente para el activo.");
			}
			if ($mode === 'move') {
				if ((int)$current_movement['id_movement_asset'] !== (int)$id_current_movement) {
					return array("status" => false, "type" => "error", "message" => "El movimiento seleccionado ya no es el vigente. Actualice la tabla.");
				}
				$is_same_movement = (int)$current_movement['id_space_movement_asset'] === $id_space
					&& (int)$current_movement['id_type_movement_asset'] === $id_type_movement
					&& (int)($current_movement['id_responsible_employee_movement_asset'] ?? 0) === (int)($id_responsible_employee ?? 0)
					&& (string)($current_movement['expected_return_date_movement_asset'] ?? '') === (string)($expected_return_date ?? '')
					&& (string)($current_movement['loan_recipient_type_movement_asset'] ?? '') === (string)($loan_recipient_type ?? '')
					&& (string)($current_movement['loan_recipient_name_movement_asset'] ?? '') === (string)($loan_recipient_name ?? '')
					&& (int)($current_movement['loan_return_indefinite_movement_asset'] ?? 0) === (int)$loan_return_indefinite
					&& (string)($current_movement['manual_responsible_movement_asset'] ?? '') === (string)($manual_responsible ?? '');
				if ($is_same_movement) {
					return array("status" => false, "type" => "error", "message" => "No se puede registrar un movimiento identico al vigente.");
				}
				if ($id_type_movement === 3 && (int)$current_movement['id_space_movement_asset'] === $id_space) {
					return array("status" => false, "type" => "error", "message" => "Para una transferencia seleccione un ambiente diferente.");
				}
			}
			return array("status" => true);
		}

		private function movement_location_required_message(int $id_type_movement): string
		{
			$messages = [
				1 => 'Seleccione la ubicacion inicial del activo.',
				2 => 'Seleccione el ambiente o ubicacion de asignacion.',
				3 => 'Seleccione el ambiente destino de la transferencia.',
				4 => 'Seleccione el almacen o ambiente de retorno.',
				5 => 'Seleccione la ubicacion interna de control del prestamo.'
			];
			return $messages[$id_type_movement] ?? 'Seleccione una ubicacion, ambiente o almacen.';
		}

		private function resolve_movement_status(int $id_type_movement): string
		{
			if ($id_type_movement === 5) {
				return 'Prestamo activo';
			}
			if ($id_type_movement === 4) {
				return 'Devuelto / almacenado';
			}
			return 'Vigente';
		}

		private function loan_recipient_type_label(?string $type): string
		{
			$labels = [
				'internal' => 'Personal interno',
				'external_person' => 'Persona externa',
				'external_company' => 'Empresa o institucion externa'
			];
			return $labels[$type] ?? 'No aplica';
		}

		private function generate_movement_document_code(): string
		{
			return 'MOV-' . date('YmdHis') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
		}

		public function combo(){
			$data = $this->model->combo_movements_asset();
			$html="";
			$html.="<option selected disabled value=''>Selecciona una opción</option>";
			if(is_array($data)==true and count($data)>0){
				foreach($data as $row)
				{
					$html.= "<option value='".$row['id_movement_asset']."'>".$row['name_movement_asset']."</option>";
				}
				echo $html;
			}
		}
	}
?>
