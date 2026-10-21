<?php 

	class Incidents extends Controllers {
		
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

		public function manage_incidents()
		{
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			check_permissions(MODULE_HELP_DESK);
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
				"functions_manage_incidents.js"
			];
			$data['can_create'] = !empty($_SESSION['module_permissions']['w_write_permission']);
            $this->views->getView($this, "HelpDesk", "manage", $data);
		}

        public function new_incident()
        {
            header('Location: '.base_url().'/help_desk/incidents/manage_incidents');
            exit;
        }

        public function list_of_incidents()
        {
            header('Location: '.base_url().'/help_desk/incidents/manage_incidents');
            exit;
        }

		public function reports()
		{
			$this->guard_reports_access();
			$data['filters'] = $this->model->report_filter_options();
			$this->views->getView($this, "HelpDesk", "reports", $data);
		}

		public function print_report()
		{
			$this->guard_reports_access();
			$filters = $this->read_report_filters();
			$report = $this->model->incident_report_data($filters);

            require_once __DIR__.'/../../Libraries/Reports/IncidentsReport.php';
            $pdf = new IncidentsReport();
            $pdf->render($report,(string)($_SESSION['user_data']['display_name'] ?? 'Usuario del sistema'));
            $pdf->Output('reporte-incidencias.pdf', 'I');
			die();
		}

		public function incident_details($id_incident)
        {
            $data = $this->incident_detail_data((int)$id_incident);
            $this->views->getView($this, "HelpDesk", "details", $data);
        }

        private function incident_detail_data(int $id_incident): array
        {
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			$request_specific_incident = $this->model->show_specific_incident($id_incident);
            check_permissions(MODULE_HELP_DESK);
            if (empty($_SESSION['module_permissions']['r_read_permission'])) {
                header('Location: '.base_url().'/errors/forbidden'); exit;
            }
            if (!$request_specific_incident) {
                header('Location: '.base_url().'/errors/not_found'); exit;
            }
            check_additional_permissions(MODULE_HELP_DESK);
            $canManageLifecycle = true;
            $data['incident_lifecycle'] = [
                'is_open' => (int) $request_specific_incident['status_open_incident'] === 1,
                'can_close' => $canManageLifecycle && additional_control_allowed(MODULE_HELP_DESK, 'Permitir cerrar tickets.'),
                'can_reopen' => $canManageLifecycle && additional_control_allowed(MODULE_HELP_DESK, 'Permitir reabrir tickets.')
            ];

			if($request_specific_incident['status_open_incident'] == 1) {
				$request_specific_incident['status_open_incident'] = '<span id="incident-status" class="badge bg-success">Abierta</span>';
			} else {
				$request_specific_incident['status_open_incident'] = '<span id="incident-status" class="badge bg-danger">Cerrada</span>';
			}

			$request_specific_incident['date_created_incident'] = date("d/m/Y h:i A", strtotime($request_specific_incident['date_created_incident']));
			if (!empty($request_specific_incident['date_incident_date'])) {
				$request_specific_incident['date_incident'] = date("d/m/Y", strtotime($request_specific_incident['date_incident_date']));
			} else {
				$request_specific_incident['date_incident'] = 'Sin Registrar';
			}

			if (!empty($request_specific_incident['time_incident_time'])) {
				$request_specific_incident['time_incident'] = date("H:i", strtotime($request_specific_incident['time_incident_time']));
			} else {
				$request_specific_incident['time_incident'] = 'Sin Registrar';
			}

			$incident_action_permissions = [
				'can_register' => false,
				'can_delete' => false
			];
			if ($data['incident_lifecycle']['is_open']) {
				$module_permissions = $_SESSION['module_permissions'] ?? [];
				$incident_action_permissions['can_register'] = !empty($module_permissions['w_write_permission']);
				$incident_action_permissions['can_delete'] = !empty($module_permissions['d_delete_permission']);
			}

			$data['specific_incident'] = $request_specific_incident;
			$data['incident_action_permissions'] = $incident_action_permissions;
			$data['incident_events'] = $this->model->incident_events((int)$id_incident);
            $data['attachments_incident'] = $this->model->show_attachments_incident($id_incident);
			$data['css'] = [
				"/extensions/datatables/datatables.min.css"
			];
			$data['script'] = [
				"/js/language_datatable.js",
				"/extensions/datatables/datatables.min.js"
			];
			$data['page_functions_js'] = [
				"incident_asset_picker.js",
                "functions_incident_details.js"
			];
			return $data;
		}

		public function show_incident() {
            if (!$this->authorize_incident_action('r_read_permission')) { return; }

			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_HELP_DESK);
			if (empty($_SESSION['module_permissions']['r_read_permission'])) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para leer el registro.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_incident'])) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrió un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_incident = trim($_POST['id_incident']);
				$request_incident = $this->model->show_specific_incident($int_id_incident);
				if ($request_incident) {
					$request_attachments = $this->model->show_attachments_incident($int_id_incident);
					$attachments = normalized_attachment(
						$request_attachments, 
						'id_attachment_incident', 
						'name_attachment_incident', 
						'route_attachment_incident'
					);
					$response = array("status" => true, "result" => $request_incident, "attachments" => $attachments);
				} else {
					$response = array("status" => false, "type" => "error", "message" => "No se pudo leer el registro.");
				}
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se enviaron datos.");
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}

		public function insert_incident() {
            if (!$this->authorize_incident_action('w_write_permission')) { return; }

			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			
				check_permissions(MODULE_HELP_DESK);
				if(empty($_SESSION['module_permissions']['w_write_permission'])){
					$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para generar nueva incidencia.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
			
			if (!empty($_POST)) {
				if (empty($_POST['title'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese el título.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['id_type_incident'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de tipo de incidencia.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['id_priority'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de prioridad.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['id_space'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de piso y área");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_user = (int) $_SESSION['user_data']['id_user'];
				if (empty($_POST['description'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese la descripción.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$str_title = trim($_POST['title']);
				$int_id_type_incident = trim($_POST['id_type_incident']);
				$int_id_priority = trim($_POST['id_priority']);
				$int_id_space = trim($_POST['id_space']);
				$request_parse_date_incident = $this->parse_incident_datetime($_POST['date_incident'] ?? '', $_POST['time_incident'] ?? '');
				if ($request_parse_date_incident['status'] === false) {
					$response = array("status" => false, "type" => "error", "message" => $request_parse_date_incident['message']);
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$str_date_incident = $request_parse_date_incident['value'];
				$str_description = trim($_POST['description']);

				$files = isset($_FILES['document']) && $_FILES['document']['size'] > 0 ? $_FILES['document'] : null;
				// Verificar la cantidad y el tamaño de los archivos
				if ($files) {
					$response = check_files($files, 5);
					if ($response != "succes") {
						echo json_encode($response, JSON_UNESCAPED_UNICODE);
						return;
					}
				}
				// Llamar al modelo para insertar la incidencia y manejar los archivos
				$request_new_incident = $this->model->insert_new_incident(
					$int_id_user, 
					$str_title, 
					$int_id_type_incident, 
					$int_id_priority, 
					$int_id_space, 
					$str_date_incident,
					$str_description
				);
				$id_incident = $request_new_incident;

				if ($id_incident) {
					// Ahora insertar los archivos asociados a la incidencia
					if ($files) {
						upload_attachments_incident($files, $id_incident);
					}
					$response = array("status" => true, "type" => "success", "message" => "Incidencia registrada correctamente: Nro - $id_incident.");
				} else {
					$response = array("status" => false, "type" => "error", "message" => "No se pudo registrar la incidencia.");
				}
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se enviaron datos.");
			}

			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}

        private function authorize_incident_action(string $permission, ?string $control = null): bool
        {
            check_permissions(MODULE_HELP_DESK);
            $allowed = !empty($_SESSION['status_active_user'])
                && !empty($_SESSION['module_permissions'][$permission]);
            if ($allowed && $control !== null) {
                check_additional_permissions(MODULE_HELP_DESK);
                $allowed = additional_control_allowed(MODULE_HELP_DESK, $control);
            }
            if (!$allowed) {
                echo json_encode(['status'=>false, 'type'=>'warning', 'message'=>'No tienes permiso para realizar esta operación.'], JSON_UNESCAPED_UNICODE);
            }
            return $allowed;
        }

        private function read_incident_asset(): ?int
        {
            $id = (int)($_POST['id_asset'] ?? 0);
            return $id > 0 ? $id : null;
        }

        private function require_open_incident(): bool
        {
            $incident = $this->model->show_specific_incident((int) ($_POST['id_incident'] ?? 0));
            if (!$incident || (int) $incident['status_open_incident'] !== 1) {
                echo json_encode([
                    'status' => false,
                    'type' => 'warning',
                    'message' => $incident
                        ? 'La incidencia está cerrada. Debes reabrirla para modificar sus detalles.'
                        : 'No se encontró la incidencia.'
                ], JSON_UNESCAPED_UNICODE);
                return false;
            }
            return true;
        }

		public function relate_incident_asset() {
            if (!$this->authorize_incident_action(empty($_POST['id_asset']) ? 'd_delete_permission' : 'w_write_permission')) { return; }

			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
            if (!$this->require_open_incident()) { return; }

			if (!empty($_POST)) {
				if (empty($_POST['id_incident'])) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrió un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}

				$int_id_incident = trim($_POST['id_incident']);
				$int_id_asset = $this->read_incident_asset();
				$request_relate_incident_asset = $this->model->relate_incident_asset(
					$int_id_incident,
					$int_id_asset				
				);
				if ($request_relate_incident_asset) {
					$response = array("status" => true, "type" => "success", "message" => $int_id_asset ? "Activo relacionado correctamente." : "Relación del activo retirada correctamente.", "html" => $this->render_incident_detail_content((int)$int_id_incident));
				} else {
					$response = array("status" => false, "type" => "error", "message" => "No se pudo relacionar el activo a la incidencia.");
				}								
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se enviaron datos.");
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}

        public function clear_incident_field()
        {
            if (!$this->authorize_incident_action('d_delete_permission') || !$this->require_open_incident()) { return; }
            $result = $this->model->clear_incident_field((int)($_POST['id_incident'] ?? 0), (string)($_POST['field'] ?? ''));
            echo json_encode(['status'=>(bool)$result, 'type'=>$result ? 'success' : 'warning',
                'message'=>$result ? 'Contenido eliminado correctamente.' : 'No se pudo limpiar el campo.'], JSON_UNESCAPED_UNICODE);
        }

		public function save_incident_diagnostic() {
            if (!$this->authorize_incident_action('w_write_permission')) { return; }

			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
            if (!$this->require_open_incident()) { return; }

			check_permissions(MODULE_HELP_DESK);
			if(empty($_SESSION['module_permissions']['w_write_permission'])){
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para registrar en incidencias.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_incident'])) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrió un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['diagnostic_incident'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese el diagnóstico.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_incident = trim($_POST['id_incident']);
				$str_diagnostic_incident = trim($_POST['diagnostic_incident']);
				$request_save_incident_diagnostic = $this->model->save_incident_diagnostic(
					$int_id_incident,
					$str_diagnostic_incident				
				);
				if ($request_save_incident_diagnostic) {
					$response = array("status" => true, "type" => "success", "message" => "Registrado correctamente.");
				} else {
					$response = array("status" => false, "type" => "error", "message" => "No se pudo registrar el diagnóstico dla incidencia.");
				}								
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se enviaron datos.");
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}

		public function save_incident_solution() {
            if (!$this->authorize_incident_action('w_write_permission')) { return; }

			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
            if (!$this->require_open_incident()) { return; }

			check_permissions(MODULE_HELP_DESK);
			if(empty($_SESSION['module_permissions']['w_write_permission'])){
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para registrar en incidencias.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_incident'])) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrió un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['solution_incident'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese la solución.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_incident = trim($_POST['id_incident']);
				$str_solution_incident = trim($_POST['solution_incident']);
				$request_save_incident_solution = $this->model->save_incident_solution(
					$int_id_incident,
					$str_solution_incident				
				);
				if ($request_save_incident_solution) {
					$response = array("status" => true, "type" => "success", "message" => "Registrado correctamente.");
				} else {
					$response = array("status" => false, "type" => "error", "message" => "No se pudo registrar la solución dla incidencia.");
				}								
			} else {
				$response = array("status" => false, "type" => "error", "message" => "No se enviaron datos.");
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}

		public function update_incident() {
            if (!$this->authorize_incident_action('u_update_permission')) { return; }

			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
            if (!$this->require_open_incident()) { return; }

			check_permissions(MODULE_HELP_DESK);
			if(empty($_SESSION['module_permissions']['u_update_permission'])){
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para actualizar el registro.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_incident'])) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrió un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['title'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese el título.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['id_type_incident'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de tipo de incidencia.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}				
				if (empty($_POST['id_priority'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de prioridad.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['id_space'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, seleccione una opción de piso y área");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				if (empty($_POST['description'])) {
					$response = array("status" => false, "type" => "error", "message" => "Por favor, ingrese la descripción.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_incident = trim($_POST['id_incident']);
				$int_id_space = trim($_POST['id_space']);
				$str_title = trim($_POST['title']);
				$int_id_type_incident = trim($_POST['id_type_incident']);
				$int_id_priority = trim($_POST['id_priority']);
				$str_description = trim($_POST['description']);
				$request_parse_date_incident = $this->parse_incident_datetime($_POST['date_incident'] ?? '', $_POST['time_incident'] ?? '');
				if ($request_parse_date_incident['status'] === false) {
					$response = array("status" => false, "type" => "error", "message" => $request_parse_date_incident['message']);
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$str_date_incident = $request_parse_date_incident['value'];

				$files = isset($_FILES['document']) && $_FILES['document']['size'] > 0 ? $_FILES['document'] : null;
				// Verificar la cantidad y el tamaño de los archivos
				if ($files) {
					$response = check_files($files, 5);
					if ($response != "succes") {
						echo json_encode($response, JSON_UNESCAPED_UNICODE);
						return;
					}
				}
				$request_incident = $this->model->update_incident(
					$int_id_incident, 
					$str_title, 
					$int_id_type_incident, 
					$int_id_priority, 
					$int_id_space, 
					$str_date_incident,
					$str_description
				);
				if ($request_incident) {
					if (!empty($_POST['delete_attachments'])) {
						$delete_attachments = $_POST['delete_attachments'];
						$request_attachments_incident = $this->model->show_attachments_incident($int_id_incident);
						$attachments_map = [];
						foreach ($request_attachments_incident as $item) {
							$attachments_map[$item['id_attachment_incident']] = $item['route_attachment_incident'];
						}
						foreach ($delete_attachments as $id) {
							if (isset($attachments_map[$id])) {
								delete_file($attachments_map[$id]);
								$this->model->delete_attachment_incident($id);
							}
						}
					}
					if ($files) {
						upload_attachments_incident($files, $int_id_incident);
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
		
		public function show_incidents()
		{
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
            if (!$this->authorize_incident_action('r_read_permission')) { return; }
            $data = $this->model->select_all_incidents();

			for ($i=0; $i < count($data); $i++) {
				$isOpen = (int)$data[$i]['status_open_incident'] === 1;
                $btnView = '';
				$btnEdit = '';

				if($data[$i]['status_open_incident'] == 1) {
					$data[$i]['status_open_incident'] = '<span class="badge bg-success">Abierta</span>';
				} else {
					$data[$i]['status_open_incident'] = '<span class="badge bg-danger">Cerrada</span>';
				}
				if($data[$i]['date_incident'] == null) {
					$data[$i]['date_incident'] = '<span class="badge bg-secondary">Sin Registrar</span>';
				}


				
					if($isOpen && $_SESSION['module_permissions']['u_update_permission']){
						$btnEdit = '<button type="button" class="btn icon btn-primary js-btn-edit" data-id="'.$data[$i]['id_incident'].'" title="Editar"><i class="bx bx-pencil"></i></button>';
					}

				
				$btnDetails = '<button type="button" data-url-details="'.$data[$i]['id_incident'].'" class="btn icon btn-open-details js-btn-open-incident-details" title="Ver" aria-label="Ver"><i class="bx bx-eye"></i></button>';
				$data[$i]['can_delete'] = !empty($_SESSION['module_permissions']['d_delete_permission']);
                $btnMore = '<button type="button" class="btn btn-secondary js-btn-more" data-id="'.(int)$data[$i]['id_incident'].'" title="Más opciones" aria-label="Más opciones"><i class="bx bx-dots-horizontal-rounded"></i></button>';
                $data[$i]['options'] = '<div class="text-center">'.$btnDetails.' '.$btnEdit.' '.$btnMore.'</div>';
			}
			echo json_encode($data, JSON_UNESCAPED_UNICODE);
			die();
		}

		public function derive_corrective()
		{
            if (!$this->authorize_incident_action('w_write_permission')) { return; }

			if ($_SESSION['status_active_user'] === 0) {
				echo json_encode(["status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa."], JSON_UNESCAPED_UNICODE);
				return;
			}
            if (!$this->require_open_incident()) { return; }

			
			check_permissions(MODULE_HELP_DESK);
			if (empty($_SESSION['module_permissions']['w_write_permission'])) {
				echo json_encode(["status" => false, "type" => "error", "message" => "No tienes permisos para derivar incidencias."], JSON_UNESCAPED_UNICODE);
				return;
			}
			$id_incident = intval($_POST['id_incident'] ?? 0);
			if ($id_incident <= 0) {
				echo json_encode(["status" => false, "type" => "error", "message" => "No se recibio la incidencia."], JSON_UNESCAPED_UNICODE);
				return;
			}

			$request = $this->model->derive_corrective($id_incident, intval($_SESSION['user_data']['id_user']));
			if ($request) {
				echo json_encode(["status" => true, "type" => "success", "message" => "Incidencia derivada a mantenimiento correctivo."], JSON_UNESCAPED_UNICODE);
			} else {
				echo json_encode(["status" => false, "type" => "error", "message" => "No se pudo derivar. Verifique que la incidencia este abierta, tenga activo relacionado y no este pendiente o aceptada."], JSON_UNESCAPED_UNICODE);
			}
			die();
		}

		public function cancel_corrective_derivation()
		{
            if (!$this->authorize_incident_action('u_update_permission')) { return; }

			if ($_SESSION['status_active_user'] === 0) {
				echo json_encode(["status" => false, "type" => "error", "message" => "Tu cuenta de usuario no esta activa."], JSON_UNESCAPED_UNICODE);
				return;
			}
            if (!$this->require_open_incident()) { return; }

			
			check_permissions(MODULE_HELP_DESK);
			if (empty($_SESSION['module_permissions']['u_update_permission'])) {
				echo json_encode(["status" => false, "type" => "error", "message" => "No tienes permisos para cancelar derivaciones."], JSON_UNESCAPED_UNICODE);
				return;
			}
			$id_incident = intval($_POST['id_incident'] ?? 0);
			if ($id_incident <= 0) {
				echo json_encode(["status" => false, "type" => "error", "message" => "No se recibio la incidencia."], JSON_UNESCAPED_UNICODE);
				return;
			}

			$request = $this->model->cancel_corrective_derivation($id_incident, intval($_SESSION['user_data']['id_user']));
			if ($request) {
				echo json_encode(["status" => true, "type" => "success", "message" => "Derivacion cancelada correctamente."], JSON_UNESCAPED_UNICODE);
			} else {
				echo json_encode(["status" => false, "type" => "error", "message" => "Solo se puede cancelar una derivacion pendiente."], JSON_UNESCAPED_UNICODE);
			}
			die();
		}

        private function render_incident_detail_content(int $id): string
        {
            $data = $this->incident_detail_data($id);
            ob_start();
            try {
                require __DIR__.'/../../Views/HelpDesk/Incidents/detail-content.php';
                return ob_get_contents();
            } finally { ob_end_clean(); }
        }

        public function close_incident()
        {
            if (!$this->authorize_incident_action('r_read_permission', 'Permitir cerrar tickets.')) { return; }
            $result = $this->model->close_incident((int)($_POST['id_incident'] ?? 0));
            echo json_encode(['status'=>(bool)$result, 'type'=>$result ? 'success' : 'warning',
                'html'=>$result ? $this->render_incident_detail_content((int)$_POST['id_incident']) : null,
                'message'=>$result ? 'Incidencia cerrada correctamente.' : 'Registra una solución o justificación antes de cerrar la incidencia.'], JSON_UNESCAPED_UNICODE);
        }

        public function reopen_incident()
        {
            if (!$this->authorize_incident_action('r_read_permission', 'Permitir reabrir tickets.')) { return; }
            $result = $this->model->reopen_incident((int)($_POST['id_incident'] ?? 0));
            echo json_encode(['status'=>(bool)$result, 'type'=>$result ? 'success' : 'warning',
                'html'=>$result ? $this->render_incident_detail_content((int)$_POST['id_incident']) : null,
                'message'=>$result ? 'Incidencia reabierta correctamente.' : 'No se pudo reabrir la incidencia.'], JSON_UNESCAPED_UNICODE);
        }

		public function update_manual_closed_incident() {
            if (!$this->authorize_incident_action('w_write_permission')) { return; }

			if ($_SESSION['status_active_user'] === 0) {
				echo json_encode(["status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa."], JSON_UNESCAPED_UNICODE);
				return;
			}
            if (!$this->require_open_incident()) { return; }

			
			check_permissions(MODULE_HELP_DESK);
			if (empty($_SESSION['module_permissions']['w_write_permission'])) {
				echo json_encode(["status" => false, "type" => "error", "message" => "No tienes permisos para registrar en incidencias."], JSON_UNESCAPED_UNICODE);
				return;
			}
			if (empty($_POST['id_incident'])) {
				echo json_encode(["status" => false, "type" => "error", "message" => "Ocurrió un error, por favor, reinicia el navegador."], JSON_UNESCAPED_UNICODE);
				return;
			}

			$date_closed_incident = trim((string)($_POST['date_closed_incident'] ?? ''));
			$time_closed_incident = trim((string)($_POST['time_closed_incident'] ?? ''));

			if ($date_closed_incident !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_closed_incident)) {
				echo json_encode(["status" => false, "type" => "error", "message" => "La fecha de cierre manual no tiene un formato válido."], JSON_UNESCAPED_UNICODE);
				return;
			}
			if ($time_closed_incident !== '' && !preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $time_closed_incident)) {
				echo json_encode(["status" => false, "type" => "error", "message" => "La hora de cierre manual no tiene un formato válido."], JSON_UNESCAPED_UNICODE);
				return;
			}

			$request = $this->model->update_manual_closed_incident(
				intval($_POST['id_incident']),
				$date_closed_incident !== '' ? $date_closed_incident : null,
				$time_closed_incident !== '' ? $time_closed_incident : null
			);

			if ($request) {
				$response = ["status" => true, "type" => "success", "message" => "Cierre manual registrado correctamente."];
			} else {
				$response = ["status" => false, "type" => "error", "message" => "No se pudo registrar el cierre manual."];
			}
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
			die();
		}

		private function parse_incident_datetime($date_incident_raw, $time_incident_raw)
		{
			$date_incident = trim((string)$date_incident_raw);
			$time_incident = trim((string)$time_incident_raw);

			if ($date_incident === '' && $time_incident === '') {
				return ["status" => true, "value" => null];
			}

			if ($date_incident === '' && $time_incident !== '') {
				return ["status" => false, "message" => "Si registra hora de incidencia, tambien debe registrar la fecha."];
			}

			$date_object = DateTime::createFromFormat('d/m/Y', $date_incident);
			if (!$date_object || $date_object->format('d/m/Y') !== $date_incident) {
				$date_object = DateTime::createFromFormat('Y-m-d', $date_incident);
			}

			if (!$date_object) {
				return ["status" => false, "message" => "La fecha de incidencia no tiene un formato valido."];
			}

			$date_part = $date_object->format('Y-m-d');

			if ($time_incident === '') {
				return ["status" => true, "value" => $date_part];
			}

			if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $time_incident)) {
				return ["status" => false, "message" => "La hora de incidencia no tiene un formato valido."];
			}

			return ["status" => true, "value" => $date_part . ' ' . $time_incident . ':00'];
		}

		private function guard_reports_access(): void
		{
			if ($_SESSION['status_active_user'] === 0) {
				header("Location: " . base_url() . "/account/logout");
				die();
			}
			
			check_permissions(MODULE_HELP_DESK);
			if (empty($_SESSION['module_permissions']['r_read_permission'])) {
				header("Location:".base_url().'/errors/forbidden');
				die();
			}
		}

		private function read_report_filters(): array
		{
			return [
				'date_start' => $this->normalize_report_date($_GET['date_start'] ?? $_POST['date_start'] ?? ''),
				'date_end' => $this->normalize_report_date($_GET['date_end'] ?? $_POST['date_end'] ?? ''),
				'status' => trim((string)($_GET['status'] ?? $_POST['status'] ?? 'all')),
				'id_type_incident' => (int)($_GET['id_type_incident'] ?? $_POST['id_type_incident'] ?? 0),
				'id_priority' => (int)($_GET['id_priority'] ?? $_POST['id_priority'] ?? 0),
				'asset_code' => trim((string)($_GET['asset_code'] ?? $_POST['asset_code'] ?? '')),
				'limit' => max(1, min(150, (int)($_GET['limit'] ?? $_POST['limit'] ?? 50))),
			];
		}

		private function normalize_report_date($value): string
		{
			$value = trim((string)$value);
			if ($value === '') {
				return '';
			}
			$date = DateTime::createFromFormat('Y-m-d', $value);
			if (!$date) {
				$date = DateTime::createFromFormat('d/m/Y', $value);
			}
			return $date ? $date->format('Y-m-d') : '';
		}

		public function delete_incident() 
		{
            if (!$this->authorize_incident_action('d_delete_permission')) { return; }

			if ($_SESSION['status_active_user'] === 0) {
				$response = array("status" => false, "type" => "error", "message" => "Tu cuenta de usuario no está activa.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			check_permissions(MODULE_HELP_DESK);
			if (!$_SESSION['module_permissions']['d_delete_permission']) {
				$response = array("status" => false, "type" => "error", "message" => "No tienes permisos para eliminar el registro.");
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
				return;
			}
			if (!empty($_POST)) {
				if (empty($_POST['id_incident'])) {
					$response = array("status" => false, "type" => "error", "message" => "Ocurrió un error, por favor, reinicia el navegador.");
					echo json_encode($response, JSON_UNESCAPED_UNICODE);
					return;
				}
				$int_id_incident = trim($_POST['id_incident']);
				$request_incident = $this->model->delete_incident($int_id_incident);
				if ($request_incident) {
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
