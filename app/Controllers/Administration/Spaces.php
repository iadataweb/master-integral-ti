<?php

	class Spaces extends Controllers{

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

		public function manage_spaces()
		{
			header("Location: " . base_url() . "/administration/environments/manage_environments");
			die();
		}

		public function show_spaces()
		{
			$data = $this->model->show_spaces();
			for ($i=0; $i < count($data); $i++) {
				$data[$i]['status_active_space'] = $data[$i]['status_active_space'] == 1
					? '<span class="badge bg-success">Activo</span>'
					: '<span class="badge bg-danger">Inactivo</span>';
				$data[$i]['options'] = '';
			}
			echo json_encode($data, JSON_UNESCAPED_UNICODE);
		}

		public function show_spaces_available()
		{
			echo json_encode($this->model->show_spaces_available(), JSON_UNESCAPED_UNICODE);
		}

		public function show_space()
		{
			$id_space = $_POST['id_space'] ?? $_POST['id_environment'] ?? null;
			if (empty($id_space)) {
				echo json_encode(array("status" => false, "type" => "error", "message" => "No se envio el ambiente."), JSON_UNESCAPED_UNICODE);
				return;
			}
			$request_space = $this->model->show_space((int) $id_space);
			echo json_encode(
				$request_space
					? array("status" => true, "result" => $request_space)
					: array("status" => false, "type" => "error", "message" => "No se pudo leer el ambiente."),
				JSON_UNESCAPED_UNICODE
			);
			die();
		}

		public function combo()
		{
			$data = $this->model->combo_spaces();
			$html = "<option selected disabled value=''>Selecciona una opcion</option>";
			if(is_array($data) && count($data)>0){
				foreach($data as $row)
				{
					$html .= "<option value='".$row['id_space']."'>".'Piso '.$row['floor_space'].' - '.$row['name_space']."</option>";
				}
			}
			echo $html;
		}
	}
?>
