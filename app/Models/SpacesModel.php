<?php
	require_once("Models/EnvironmentsModel.php");

	class SpacesModel extends EnvironmentsModel
	{
		public function show_spaces()
		{
			$rows = $this->show_environments();
			foreach ($rows as &$row) {
				$row['id_space'] = $row['id_environment'];
				$row['name_space'] = $row['name_environment'];
				$row['floor_space'] = $row['floor_environment'];
				$row['status_active_space'] = $row['status_active_environment'];
			}
			return $rows;
		}

		public function insert_space(int $id_campus_space, int $unused_classification, string $name_space, int $floor_space, string $use_space, int $status_active_space)
		{
			return $this->insert_environment($id_campus_space, $name_space, $floor_space, $use_space, $status_active_space);
		}

		public function update_space(int $id_space, int $id_campus_space, int $unused_classification, string $name_space, int $floor_space, string $use_space, int $status_active_space)
		{
			return $this->update_environment($id_space, $id_campus_space, $name_space, $floor_space, $use_space, $status_active_space);
		}

		public function delete_space(int $id_space)
		{
			return $this->delete_environment($id_space);
		}
	}
