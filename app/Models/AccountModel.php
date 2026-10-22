<?php 
class AccountModel extends Mysql
{
    private $int_id_user;
    private $str_first_names_user;
    private $str_last_names_user;
    private $str_email_user;
    private $int_cell_phone_user;
    private $str_current_password;
    private $str_hashed_password;

    public function __construct()
    {
        parent::__construct();
    }

    public function validate_password(int $id_user, string $current_password)
    {
        if ($id_user <= 0 || empty($current_password)) {
            return false;
        }

        $sql = "SELECT id_user, password_user FROM users WHERE id_user = ?";
        $data = [$id_user]; // Pasamos los parámetros correctamente

        $request = $this->select($sql, $data); // Ejecutar con parámetros

        if (!$request) {
            return false; // Usuario no encontrado
        }

        return password_verify($current_password, $request['password_user']) ? $request : false;
    }

    public function update_password(int $id_user, string $hashed_password)
    {
        if ($id_user <= 0 || empty($hashed_password)) {
            return false;
        }

        $sql = "UPDATE users SET password_user = ? WHERE id_user = ?";
        $data = [$hashed_password, $id_user];

        return $this->update($sql, $data);
    }

    public function update_theme_preference(int $id_user, string $theme)
    {
        if ($id_user <= 0 || !in_array($theme, ['light', 'dark'], true)) {
            return false;
        }
        $sql = "UPDATE users SET theme_preference_user = ? WHERE id_user = ?";
        return $this->update($sql, [$theme, $id_user]);
    }

    public function update_profile_photo(int $id_user, ?string $profile_photo_path)
    {
        if ($id_user <= 0) {
            return false;
        }
        $sql = "UPDATE users SET profile_photo_user = ? WHERE id_user = ?";
        return $this->update($sql, [$profile_photo_path, $id_user]);
    }

    public function get_profile_photo_by_user(int $id_user)
    {
        $sql = "SELECT profile_photo_user FROM users WHERE id_user = ?";
        return $this->select($sql, [$id_user]);
    }

    public function get_profile_data(int $id_user)
    {
        $sql = "SELECT  u.id_user,
                        u.name_user,
                        u.profile_photo_user,
                        u.theme_preference_user,
                        r.name_role,
                        CONCAT(
                            pt.abbreviation_professional_title, ' ',
                            CASE e.preferred_name_employee
                                WHEN 1 THEN e.first_name_employee
                                WHEN 2 THEN e.middle_name_employee
                            END, ' ',
                            e.paternal_last_name_employee, ' ',
                            e.maternal_last_name_employee
                        ) AS display_name,
                        e.email_employee
                FROM users u
                INNER JOIN employees e ON u.id_employee_user = e.id_employee
                INNER JOIN professional_titles pt ON e.id_professional_title_employee = pt.id_professional_title
                INNER JOIN roles r ON u.id_role_user = r.id_role
                WHERE u.id_user = ?";
        return $this->select($sql, [$id_user]);
    }

    public function touch_browser_session(int $id_user, string $session_id, string $user_agent, string $ip_address)
    {
        $sql = "SELECT id_user_browser_session,
                       status_active_user_browser_session,
                       status_closed_user_browser_session
                FROM user_browser_sessions
                WHERE id_user = ? AND session_id = ? LIMIT 1";
        $request = $this->select($sql, [$id_user, $session_id]);

        if ($request) {
            if ((int)$request['status_active_user_browser_session'] !== 1 || (int)$request['status_closed_user_browser_session'] !== 0) {
                return false;
            }
            $sql_update = "UPDATE user_browser_sessions
                            SET user_agent = ?,
                                ip_address = ?,
                                date_last_activity_user_browser_session = NOW()
                            WHERE id_user = ?
                              AND session_id = ?
                              AND status_active_user_browser_session = 1
                              AND status_closed_user_browser_session = 0";
            return $this->update($sql_update, [$user_agent, $ip_address, $id_user, $session_id]);
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

    public function get_browser_session(int $id_user, string $session_id)
    {
        $sql = "SELECT id_user_browser_session,
                       status_active_user_browser_session,
                       status_closed_user_browser_session
                FROM user_browser_sessions
                WHERE id_user = ?
                  AND session_id = ?
                LIMIT 1";
        return $this->select($sql, [$id_user, $session_id]);
    }

    public function is_browser_session_active(int $id_user, string $session_id)
    {
        $sql = "SELECT id_user_browser_session
                FROM user_browser_sessions
                WHERE id_user = ?
                AND session_id = ?
                AND status_active_user_browser_session = 1
                AND status_closed_user_browser_session = 0
                LIMIT 1";
        return $this->select($sql, [$id_user, $session_id]);
    }

    public function close_browser_session(string $session_id)
    {
        if (empty($session_id)) {
            return false;
        }
        $sql = "UPDATE user_browser_sessions
                SET status_active_user_browser_session = 0,
                    status_closed_user_browser_session = 1
                WHERE session_id = ?";
        return $this->update($sql, [$session_id]);
    }

    public function close_browser_session_by_user(int $id_user, string $session_id)
    {
        $sql = "UPDATE user_browser_sessions
                SET status_active_user_browser_session = 0,
                    status_closed_user_browser_session = 1
                WHERE id_user = ? AND session_id = ?";
        return $this->update($sql, [$id_user, $session_id]);
    }

    public function close_other_browser_sessions(int $id_user, string $current_session_id)
    {
        $sql = "UPDATE user_browser_sessions
                SET status_active_user_browser_session = 0,
                    status_closed_user_browser_session = 1
                WHERE id_user = ? AND session_id != ?";
        return $this->update($sql, [$id_user, $current_session_id]);
    }

    public function list_browser_sessions(int $id_user)
    {
        $sql = "SELECT id_user_browser_session,
                       session_id,
                       user_agent,
                       ip_address,
                       status_active_user_browser_session,
                       status_closed_user_browser_session,
                       date_last_activity_user_browser_session,
                       date_created_user_browser_session
                FROM user_browser_sessions
                WHERE id_user = ?
                ORDER BY date_last_activity_user_browser_session DESC";
        return $this->select_all($sql, [$id_user]);
    }
}
?>
