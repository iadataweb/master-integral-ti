<?php 
    require 'Libraries/vendor/autoload.php';

	function base_url()
	{
		return BASE_URL;
	}

    function profile_photo_url(?string $profile_photo_path = null): string
    {
        $default_photo = assets() . "/img/team/avatar.jpg";
        if (empty($profile_photo_path)) {
            return $default_photo;
        }
        $clean_path = ltrim(trim($profile_photo_path), "/");
        if ($clean_path === "") {
            return $default_photo;
        }
        return base_url() . "/" . $clean_path;
    }

    function assets()
    {
        return BASE_URL."/Assets";
    }
    function modal_permissions($data)
    {
        $view_modal = "Views/Administration/Roles/permissions.php";
        require_once $view_modal;        
    }
    function modal_additional_permissions($data)
    {
        $view_modal = "Views/Administration/Roles/additional-permissions.php";
        require_once $view_modal;        
    }
    function modal_folder($data)
    {
        $view_modal = "Views/Maintenance/MaintenancesPreventive/folder.php";
        require_once $view_modal;        
    }
    function modal_folder_maintenance_corrective($data)
    {
        $view_modal = "Views/Maintenance/MaintenancesCorrective/folder.php";
        require_once $view_modal;
    }
    function generate_html(string $name_folder, string $name_archive, $data)
    {
        $view_content = "Views/{$name_folder}/{$name_archive}.php";
        require_once $view_content;        
    }

    function upload_attachments_ticket($files, $id_ticket) {
        return upload_attachments_incident($files, $id_ticket);
    }
    require_once __DIR__.'/AccessPermissions.php';

    function session_user(int $id_user){
        require_once ("Models/LoginModel.php");
        $obj_login = new LoginModel();
        $request = $obj_login->session_login($id_user);
        $_SESSION['user_data'] = $request;
        return $request;
    }
    
    function check_user_status(int $id_user){
        require_once ("Models/UsersModel.php");
        $object = new UsersModel();
        $request = $object->get_user_status($id_user);
        $_SESSION['status_active_user'] = $request['status_active_user'] ?? 0;
        if ($request && isset($request['id_role_user'])) {
            if ((int)($_SESSION['user_data']['id_role'] ?? 0) !== (int)$request['id_role_user']) {
                unset($_SESSION['permissions'], $_SESSION['module_permissions'], $_SESSION['additional_permissions_module']);
            }
            $_SESSION['user_data']['id_role'] = (int)$request['id_role_user'];
            $_SESSION['user_data']['name_role'] = $request['name_role'];
        }

        if (!empty($_SESSION['login']) && !empty($_SESSION['user_data']['id_user'])) {
            sync_browser_session((int)$_SESSION['user_data']['id_user']);
        }
        return;
    }

    function sync_browser_session(int $id_user)
    {
        require_once ("Models/AccountModel.php");
        $object = new AccountModel();

        $session_id = session_id();
        if (empty($session_id)) {
            return;
        }

        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $user_agent = substr(trim((string)$user_agent), 0, 255);

        $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $ip_address = trim((string)$ip_address);
        if (strpos($ip_address, ',') !== false) {
            $ip_address = trim(explode(',', $ip_address)[0]);
        }
        $ip_address = substr($ip_address, 0, 45);

        $request_session = $object->get_browser_session($id_user, $session_id);
        if (!empty($request_session)) {
            $is_closed_remotely = (int)$request_session['status_active_user_browser_session'] !== 1
                || (int)$request_session['status_closed_user_browser_session'] !== 0;
            if ($is_closed_remotely) {
                session_unset();
                session_destroy();
                header('Location: ' . base_url() . '/login?session_closed=1');
                die();
            }
        }

        $request_touch = $object->touch_browser_session($id_user, $session_id, $user_agent, $ip_address);
        if (empty($request_touch)) {
            session_unset();
            session_destroy();
            header('Location: ' . base_url() . '/login?session_closed=1');
            die();
        }
    }

    function check_files($files, $amount){
        $file_count = count($files['name']);
        $max_file_size = 15 * 1024 * 1024;
        $response = "succes";

        if ($file_count > $amount) {
            $response = array("status" => false, "type" => "error", "message" => "Solo se permiten un máximo de ".$amount." archivos.");
            return $response;
        }
        
        foreach ($files['size'] as $key => $size) {
            if ($size > $max_file_size) {
                $response = array(
                    "status" => false,
                    "type" => "error",
                    "message" => "El archivo '{$files['name'][$key]}' excede el tamaño máximo permitido (15 MB)."
                );
                return $response;
            }
        }
        
        return $response;

    }

    function normalized_attachment($data, $id, $name, $route){
        return array_map(function($item) use ($id, $name, $route) {
            return [
                'id_attachment' => $item[$id] ?? null,
                'name_attachment' => $item[$name] ?? null,
                'route_attachment' => $item[$route] ?? null,
            ];
        }, $data);
    }

    function upload_attachments_maintenance_preventive($files, $id_maintenance_preventive){
        require_once ("Models/MaintenancesPreventiveModel.php");
        $object = new MaintenancesPreventiveModel();

        // Crear una carpeta con el id de la incidencia
        $upload_dir = 'Assets/uploads/attachments_maintenance_preventive/' . $id_maintenance_preventive . '/';

        // Verificar si la carpeta existe, si no, crearla
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        foreach ($files['name'] as $key => $file_name) {
            
            // Limpiar el nombre del archivo
            $file_name = preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $file_name);

            $file_tmp_name = $files['tmp_name'][$key];
            $file_type = $files['type'][$key];
            $file_size = $files['size'][$key];

            // Generar un código único para renombrar el archivo
            $prefix = "MP_";
            $unique_code = uniqid($prefix, true);
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION); // Obtener la extensión del archivo
            $new_file_name = $unique_code . '.' . $file_extension; // Renombrar el archivo con el código único y la extensión original

            // Definir la ruta de destino
            $uploaded_file_path = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp_name, $uploaded_file_path)) {
                // Insertar el archivo en la base de datos asociándolo a la incidencia
                $object->insert_attachment_maintenance_preventive($id_maintenance_preventive, $uploaded_file_path, $file_name);
            }
        }
    }

    function upload_attachments_maintenance_corrective($files, $id_maintenance_corrective){
        require_once ("Models/MaintenancesCorrectiveModel.php");
        $object = new MaintenancesCorrectiveModel();

        $upload_dir = 'Assets/uploads/attachments_maintenance_corrective/' . $id_maintenance_corrective . '/';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        foreach ($files['name'] as $key => $file_name) {
            $file_name = preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $file_name);
            $file_tmp_name = $files['tmp_name'][$key];
            $prefix = "MC_";
            $unique_code = uniqid($prefix, true);
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
            $new_file_name = $unique_code . '.' . $file_extension;
            $uploaded_file_path = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp_name, $uploaded_file_path)) {
                $object->insert_attachment_maintenance_corrective($id_maintenance_corrective, $uploaded_file_path, $file_name);
            }
        }
    }

    function upload_attachments_history_maintenance_preventive($files, $id_history_maintenance_preventive, $type){
        require_once ("Models/MaintenancesPreventiveModel.php");
        $object = new MaintenancesPreventiveModel();

        $safe_type = in_array($type, ['before', 'after', 'additional', 'external'], true) ? $type : 'before';
        $upload_dir = 'Assets/uploads/attachments_history_maintenance_preventive/' . $id_history_maintenance_preventive . '/' . $safe_type . '/';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        foreach ($files['name'] as $key => $file_name) {
            $file_name = preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $file_name);
            $file_tmp_name = $files['tmp_name'][$key];
            $prefix = ($safe_type === 'before') ? "MHP_B_" : "MHP_A_";
            $unique_code = uniqid($prefix, true);
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
            $new_file_name = $unique_code . '.' . $file_extension;
            $uploaded_file_path = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp_name, $uploaded_file_path)) {
                $object->insert_attachment_history_maintenance_preventive($id_history_maintenance_preventive, $safe_type, $uploaded_file_path, $file_name);
            }
        }
    }

    function upload_attachments_asset($files, $id_asset){
        require_once ("Models/AssetsModel.php");
        $object = new AssetsModel();

        // Crear una carpeta con el id de la incidencia
        $upload_dir = 'Assets/uploads/attachments_asset/' . $id_asset . '/';

        // Verificar si la carpeta existe, si no, crearla
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        foreach ($files['name'] as $key => $file_name) {
            
            // Limpiar el nombre del archivo
            $file_name = preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $file_name);

            $file_tmp_name = $files['tmp_name'][$key];
            $file_type = $files['type'][$key];
            $file_size = $files['size'][$key];

            // Generar un código único para renombrar el archivo
            $prefix = "AS_";
            $unique_code = uniqid($prefix, true);
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION); // Obtener la extensión del archivo
            $new_file_name = $unique_code . '.' . $file_extension; // Renombrar el archivo con el código único y la extensión original

            // Definir la ruta de destino
            $uploaded_file_path = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp_name, $uploaded_file_path)) {
                // Insertar el archivo en la base de datos asociándolo a la incidencia
                $object->insert_attachment_asset($id_asset, $uploaded_file_path, $file_name);
            }
        }
    }

    function upload_attachments_movement_asset($files, $id_movement_asset){
        require_once ("Models/MovementsAssetModel.php");
        $object = new MovementsAssetModel();

        $upload_dir = 'Assets/uploads/attachments_movement_asset/' . $id_movement_asset . '/';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        foreach ($files['name'] as $key => $file_name) {
            $file_name = preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $file_name);

            $file_tmp_name = $files['tmp_name'][$key];
            $prefix = "MA_";
            $unique_code = uniqid($prefix, true);
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
            $new_file_name = $unique_code . '.' . $file_extension;
            $uploaded_file_path = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp_name, $uploaded_file_path)) {
                $object->insert_attachment_movement_asset($id_movement_asset, $uploaded_file_path, $file_name);
            }
        }
    }
    
    function upload_attachments_incident($files, $id_incident){
        require_once ("Models/IncidentsModel.php");
        $object = new IncidentsModel();

        // Crear una carpeta con el id de la incidencia
        $upload_dir = 'Assets/uploads/attachments_incident/' . $id_incident . '/';

        // Verificar si la carpeta existe, si no, crearla
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        foreach ($files['name'] as $key => $file_name) {
            
            // Limpiar el nombre del archivo
            $file_name = preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $file_name);

            $file_tmp_name = $files['tmp_name'][$key];
            $file_type = $files['type'][$key];
            $file_size = $files['size'][$key];

            // Generar un código único para renombrar el archivo
            $prefix = "IN_";
            $unique_code = uniqid($prefix, true);
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION); // Obtener la extensión del archivo
            $new_file_name = $unique_code . '.' . $file_extension; // Renombrar el archivo con el código único y la extensión original

            // Definir la ruta de destino
            $uploaded_file_path = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp_name, $uploaded_file_path)) {
                // Insertar el archivo en la base de datos asociándolo a la incidencia
                $object->insert_attachment_incident($id_incident, $uploaded_file_path, $file_name);
            }
        }
    }

    function delete_file(string $route_complete){
        unlink($route_complete);
    }

    function calculate_next_maintenance_date(string $date_scheduled_maintenance_preventive, int $id_frequency_maintenance){
        require_once ("Models/FrequenciesMaintenanceModel.php");
        $object = new FrequenciesMaintenanceModel();
        $request = $object->obtain_interval_frequency($id_frequency_maintenance);
        $interval_days = (int)$request['interval_frequency_maintenance'];
        
        $date = new DateTime($date_scheduled_maintenance_preventive);
        $date->modify("+{$interval_days} days");
        return $date->format('Y-m-d');
    }
