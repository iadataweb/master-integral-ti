<?php

class Login extends Controllers
{
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (isset($_SESSION['login'])) {
            header('Location: ' . current_user_default_url());
            die();
        }
        parent::__construct();
    }

    public function login()
    {
        $data['page_functions_js'] = ["functions_login.js"];
        $this->views->getView($this, "", "index", $data);
    }

    public function login_user()
    {
        if (!$_POST) {
            die();
        }

        if (empty($_POST['name_user']) || empty($_POST['password'])) {
            echo json_encode(["status" => false, "type" => "error", "message" => "Error de datos."], JSON_UNESCAPED_UNICODE);
            die();
        }

        $str_name_user = trim((string)$_POST['name_user']);
        $str_password = (string)$_POST['password'];
        if (!$this->model->consume_login_attempt($this->resolve_client_ip(),$str_name_user)) {
            http_response_code(429);header('Retry-After: 900');
            echo json_encode(['status'=>false,'type'=>'error','message'=>'Demasiados intentos. Inténtalo de nuevo en 15 minutos.'],JSON_UNESCAPED_UNICODE);die();
        }
        $request_user = $this->model->login_user($str_name_user, $str_password);

        if (empty($request_user)) {
            echo json_encode(["status" => false, "type" => "error", "message" => "El nombre de usuario o la contrasena es incorrecto."], JSON_UNESCAPED_UNICODE);
            die();
        }

        $data = $request_user;
        $return_url = $_SESSION['return_url'] ?? null;
        if ((int)$data['status_active_user'] === 1) {
            $this->model->clear_login_attempts($this->resolve_client_ip(),$str_name_user);
            session_regenerate_id(true);
            $_SESSION['security_login_time']=time();
            $_SESSION['security_last_activity']=time();
            $_SESSION['id_user'] = $data['id_user'];
            $_SESSION['login'] = true;
            $request_session_login = $this->model->session_login((int)$_SESSION['id_user']);
            $_SESSION['user_data'] = $request_session_login;
            $this->model->register_browser_session(
                (int)$data['id_user'],
                session_id(),
                $this->resolve_user_agent(),
                $this->resolve_client_ip()
            );
            echo json_encode(["status" => true, "url_scan" => ($return_url && str_starts_with($return_url, base_url().'/') ? $return_url : current_user_default_url())], JSON_UNESCAPED_UNICODE);
            die();
        }

        echo json_encode(["status" => false, "type" => "error", "message" => "Usuario inactivo."], JSON_UNESCAPED_UNICODE);
        die();
    }



    private function resolve_user_agent(): string
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        return substr(trim((string)$user_agent), 0, 255);
    }

    private function resolve_client_ip(): string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $ip = trim((string)$ip);
        if (strpos($ip, ',') !== false) {
            $ip = trim(explode(',', $ip)[0]);
        }
        return substr($ip, 0, 45);
    }
}
?>