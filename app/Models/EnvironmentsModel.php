<?php
	class EnvironmentsModel extends Mysql
	{
		private $int_id_environment;
		private $int_status_deleted_environment;

		public function __construct()
		{
			parent::__construct();
		}

		public function show_environments()
		{
			$sql = "SELECT e.id_environment,  e.name_environment, e.floor_environment,
						e.status_active_environment, c.name_campus
					FROM environments e
					INNER JOIN campuses c ON e.id_campus_environment = c.id_campus
					WHERE e.status_deleted_environment = 0";
			return $this->select_all($sql);
		}

		public function show_environments_available()
		{
			$sql = "SELECT e.id_environment AS id_space, e.id_environment, 
						 e.name_environment AS name_space, e.name_environment,
						e.floor_environment AS floor_space, e.floor_environment,
						e.status_active_environment AS status_active_space, e.status_active_environment,
						c.name_campus
					FROM environments e
					INNER JOIN campuses c ON e.id_campus_environment = c.id_campus
					WHERE e.status_active_environment = 1 AND e.status_deleted_environment = 0";
			return $this->select_all($sql);
		}

		public function show_environment(int $id_environment)
		{
			$this->int_id_environment = $id_environment;
			$sql = "SELECT e.id_environment, e.id_campus_environment,  e.name_environment,
						e.floor_environment, e.use_environment, e.status_active_environment, c.name_campus
					FROM environments e
					INNER JOIN campuses c ON e.id_campus_environment = c.id_campus
					WHERE e.id_environment = $this->int_id_environment AND e.status_deleted_environment = 0";
			$request = $this->select($sql);
			if ($request) {
				$request['id_space'] = $request['id_environment'];
				$request['id_campus_space'] = $request['id_campus_environment'];
				$request['name_space'] = $request['name_environment'];
				$request['floor_space'] = $request['floor_environment'];
				$request['use_space'] = $request['use_environment'];
				$request['status_active_space'] = $request['status_active_environment'];
			}
			return $request;
		}

		public function insert_environment(int $id_campus_environment, string $name_environment, int $floor_environment, string $use_environment, int $status_active_environment)
		{
			$query_insert = "INSERT INTO environments(id_campus_environment, name_environment, floor_environment, use_environment, status_active_environment) VALUES(?,?,?,?,?)";
			$data = array(
				$id_campus_environment,
				$name_environment,
				$floor_environment,
				$use_environment,
				$status_active_environment
			);
			return $this->insert($query_insert, $data);
		}

		public function update_environment(int $id_environment, int $id_campus_environment, string $name_environment, int $floor_environment, string $use_environment, int $status_active_environment)
		{
			$this->int_id_environment = $id_environment;
			$sql = "UPDATE environments
						SET id_campus_environment = ?,
							name_environment = ?,
							floor_environment = ?,
							use_environment = ?,
							status_active_environment = ?
						WHERE id_environment = $this->int_id_environment";
			$data = array(
				$id_campus_environment,
				$name_environment,
				$floor_environment,
				$use_environment,
				$status_active_environment
			);
			return $this->update($sql, $data);
		}

		public function delete_environment(int $id_environment)
		{
			$this->int_id_environment = $id_environment;
			$this->int_status_deleted_environment = 1;
			$sql = "UPDATE environments SET status_deleted_environment = ? WHERE id_environment = $this->int_id_environment";
			$data = array($this->int_status_deleted_environment);
			return $this->update($sql, $data);
		}

		public function combo_environments()
		{
			$sql = "SELECT id_environment, floor_environment, name_environment
					FROM environments
					WHERE status_active_environment = 1 AND status_deleted_environment = 0
					ORDER BY floor_environment ASC";
			return $this->select_all($sql);
		}

		/* Backward-compatible methods used by modules that still name the field as space/location. */
		public function show_spaces_available()
		{
			return $this->show_environments_available();
		}

		public function show_space(int $id_space)
		{
			$environment = $this->show_environment($id_space);
			if ($environment) {
				$environment['id_space'] = $environment['id_environment'];
				$environment['id_campus_space'] = $environment['id_campus_environment'];
				$environment['name_space'] = $environment['name_environment'];
				$environment['floor_space'] = $environment['floor_environment'];
				$environment['use_space'] = $environment['use_environment'];
				$environment['status_active_space'] = $environment['status_active_environment'];
			}
			return $environment;
		}

		public function combo_spaces()
		{
			$sql = "SELECT id_environment AS id_space, floor_environment AS floor_space, name_environment AS name_space
					FROM environments
					WHERE status_active_environment = 1 AND status_deleted_environment = 0
					ORDER BY floor_environment ASC";
			return $this->select_all($sql);
		}
	}
