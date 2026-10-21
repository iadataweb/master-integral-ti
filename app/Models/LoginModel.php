<?php 

	class LoginModel extends Mysql
	{
		private $int_id_user;
		private $str_name_user;
		private $str_password;

		public function __construct()
		{
			parent::__construct();
		}	

        public function consume_login_attempt(string $ip, string $username): bool
        {
            foreach ([['ip:'.$ip,100],['user:'.$ip.':'.mb_strtolower($username),10]] as [$identity,$limit]) {
                $key=hash('sha256',$identity);
                $this->update("INSERT INTO login_attempt_limits(attempt_key,attempts,window_started) VALUES(?,1,NOW())
                    ON DUPLICATE KEY UPDATE attempts=IF(window_started < NOW()-INTERVAL 15 MINUTE,1,LEAST(attempts+1,65535)),
                    window_started=IF(window_started < NOW()-INTERVAL 15 MINUTE,NOW(),window_started)",[$key]);
                $row=$this->select('SELECT attempts FROM login_attempt_limits WHERE attempt_key=?',[$key]);
                if((int)$row['attempts']>$limit)return false;
            }
            $this->delete('DELETE FROM login_attempt_limits WHERE window_started < NOW()-INTERVAL 1 DAY LIMIT 100');
            return true;
        }

        public function clear_login_attempts(string $ip,string $username): void
        {
            $this->delete('DELETE FROM login_attempt_limits WHERE attempt_key=?',[hash('sha256','user:'.$ip.':'.mb_strtolower($username))]);
        }

		public function login_user(string $name_user, string $password)
		{
			$this->str_name_user = $name_user;
			$this->str_password = $password;
			$sql = "SELECT id_user, password_user, status_active_user FROM users WHERE 
					name_user = ? AND status_deleted_user = 0 AND EXISTS (SELECT 1 FROM roles r WHERE r.id_role = users.id_role_user AND r.status_active_role = 1 AND r.status_deleted_role = 0)";
			$data = [
				$this->str_name_user
			];
			$request = $this->select($sql, $data);
			if ($request) {
				if (!password_verify($password, $request['password_user'])) {
					$request = false;
				}
			} else {
				$request = false;
			}
			return $request;
		}

		
		public function session_login(int $id_user)
		{
			$this->int_id_user = $id_user;
			$sql = "SELECT  u.id_user,
							u.status_active_user,
							u.theme_preference_user,
							u.profile_photo_user,
							r.id_role,
							r.name_role,
							CONCAT(
								pt.abbreviation_professional_title, ' ',
								CASE e.preferred_name_employee
									WHEN 1 THEN e.first_name_employee
									WHEN 2 THEN e.middle_name_employee
								END, ' ',
								e.paternal_last_name_employee, ' ',
								e.maternal_last_name_employee)
								AS display_name
					FROM users u
					INNER JOIN roles r ON u.id_role_user = r.id_role
					INNER JOIN employees e ON u.id_employee_user = e.id_employee
					INNER JOIN professional_titles pt ON e.id_professional_title_employee = pt.id_professional_title
					WHERE u.id_user = $this->int_id_user";
			$request = $this->select($sql);
			return $request;
		}

		public function register_browser_session(int $id_user, string $session_id, string $user_agent, string $ip_address)
		{
			$sql_exists = "SELECT id_user_browser_session FROM user_browser_sessions WHERE session_id = ?";
			$request_exists = $this->select($sql_exists, [$session_id]);

			if ($request_exists) {
				$sql_update = "UPDATE user_browser_sessions
							  SET id_user = ?,
								  user_agent = ?,
								  ip_address = ?,
								  status_active_user_browser_session = 1,
								  status_closed_user_browser_session = 0,
								  date_last_activity_user_browser_session = NOW()
							  WHERE session_id = ?";
				return $this->update($sql_update, [$id_user, $user_agent, $ip_address, $session_id]);
			}

			$sql_insert = "INSERT INTO user_browser_sessions(
								id_user,
								session_id,
								user_agent,
								ip_address,
								status_active_user_browser_session,
								status_closed_user_browser_session,
								date_last_activity_user_browser_session
						   ) VALUES(?, ?, ?, ?, 1, 0, NOW())";
			$request_insert = $this->insert($sql_insert, [$id_user, $session_id, $user_agent, $ip_address]);
			return $request_insert > 0;
		}
	}
?>
