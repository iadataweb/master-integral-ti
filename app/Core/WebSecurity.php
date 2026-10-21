<?php
final class WebSecurity
{
    public static function bootstrap(): void
    {
        if (PHP_SAPI === 'cli') return;
        ini_set('display_errors','0');ini_set('log_errors','1');
        ini_set('session.use_strict_mode','1');ini_set('session.use_only_cookies','1');
        session_set_cookie_params(['httponly'=>true,'secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off','samesite'=>'Lax','path'=>'/']);
        if(session_status()!==PHP_SESSION_ACTIVE)session_start();
        $now=time();
        if (!empty($_SESSION['login']) && ($now-(int)($_SESSION['security_last_activity']??$now)>1800 || $now-(int)($_SESSION['security_login_time']??$now)>43200)) {
            $_SESSION=[];session_regenerate_id(true);
            if (($_SERVER['REQUEST_METHOD']??'GET')!=='GET') self::reject(401,'La sesión ha expirado. Inicia sesión nuevamente.');
            header('Location: '.BASE_URL.'/login');exit;
        }
        $_SESSION['security_last_activity']=$now;
        $_SESSION['security_login_time']??=$now;
        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
        header('X-Content-Type-Options: nosniff');header('X-Frame-Options: SAMEORIGIN');
        header('Referrer-Policy: same-origin');header('Cache-Control: no-store');
        $method=strtoupper($_SERVER['REQUEST_METHOD']??'GET');
        if(!in_array($method,['GET','HEAD','OPTIONS'],true)){
            $token=$_SERVER['HTTP_X_CSRF_TOKEN']??($_POST['_csrf']??'');
            if(!self::validToken($_SESSION['csrf_token'],$token))self::reject(403,'La sesión del formulario no es válida. Recarga la página e inténtalo de nuevo.');
            foreach($_FILES as $file)if(!self::validUpload($file))self::reject(400,'El archivo no está permitido, supera 15 MB o no coincide con su formato.');
        }
    }
    public static function validToken($expected,$received): bool
    {
        return is_string($expected) && strlen($expected)===64 && is_string($received) && hash_equals($expected,$received);
    }
    public static function validUpload(array $file): bool
    {
        if(is_array($file['error']??null)){
            foreach($file['error'] as $key=>$error){
                $child=[];foreach(['name','tmp_name','error','size','type'] as $field)$child[$field]=$file[$field][$key]??null;
                if(!self::validUpload($child))return false;
            }
            return true;
        }
        if(($file['error']??UPLOAD_ERR_NO_FILE)===UPLOAD_ERR_NO_FILE)return true;
        if(($file['error']??null)!==UPLOAD_ERR_OK || (int)($file['size']??0)>15*1024*1024)return false;
        $allowed=[
            'jpg'=>['image/jpeg'],'jpeg'=>['image/jpeg'],'png'=>['image/png'],'gif'=>['image/gif'],'webp'=>['image/webp'],
            'pdf'=>['application/pdf'],'txt'=>['text/plain'],'csv'=>['text/plain','text/csv','application/csv'],
            'zip'=>['application/zip','application/x-zip-compressed'],
            'doc'=>['application/msword','application/x-ole-storage','application/CDFV2'],
            'xls'=>['application/vnd.ms-excel','application/x-ole-storage','application/CDFV2'],
            'ppt'=>['application/vnd.ms-powerpoint','application/x-ole-storage','application/CDFV2'],
            'docx'=>['application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/zip'],
            'xlsx'=>['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/zip'],
            'pptx'=>['application/vnd.openxmlformats-officedocument.presentationml.presentation','application/zip']
        ];
        $ext=strtolower(pathinfo((string)($file['name']??''),PATHINFO_EXTENSION));
        $path=(string)($file['tmp_name']??'');
        return isset($allowed[$ext]) && is_file($path) && in_array((new finfo(FILEINFO_MIME_TYPE))->file($path),$allowed[$ext],true);
    }
    public static function callableAction(object $controller,string $action): bool
    {
        if(!preg_match('/^[a-z][a-z0-9_]*$/iD',$action) || str_starts_with($action,'__') || !method_exists($controller,$action))return false;
        $method=new ReflectionMethod($controller,$action);
        if (PHP_SAPI !== 'cli' && preg_match('/^(insert|update|delete|save|close|reopen|relate|derive|cancel|restore|clone|execute|set_|revoke|terminate)/i', $action) && ($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') return false;
        return $method->isPublic() && !$method->isStatic() && $method->getDeclaringClass()->getName()!=='Controllers';
    }
    private static function reject(int $code,string $message): void
    {
        http_response_code($code);header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status'=>false,'type'=>'error','message'=>$message],JSON_UNESCAPED_UNICODE);exit;
    }
}
