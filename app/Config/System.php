<?php 
	// Configuración de url base del Sistema Web

    // Configuración de Zona Horaria
	date_default_timezone_set('America/Lima');

    // Docker recibe las variables desde el .env de la raíz del proyecto.
    $environmentFile = dirname(__DIR__, 2).'/.env';
    $databaseEnvironment = is_readable($environmentFile)
        ? @parse_ini_file($environmentFile, false, INI_SCANNER_RAW)
        : [];
    if (!is_array($databaseEnvironment)) {
        throw new RuntimeException('El archivo de configuración .env no es válido.');
    }
    $webPort = getenv('WEB_PORT');
    $webPort = $webPort !== false ? $webPort : ($databaseEnvironment['WEB_PORT'] ?? '8080');
    if (filter_var($webPort, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1, 'max_range'=>65535]]) === false) {
        throw new RuntimeException('WEB_PORT debe ser un puerto entre 1 y 65535.');
    }
    $applicationUrl = getenv('APP_URL');
    $applicationUrl = trim((string)($applicationUrl !== false ? $applicationUrl : ($databaseEnvironment['APP_URL'] ?? '')));
    if ($applicationUrl === '') {
        $applicationUrl = 'http://localhost'.((int)$webPort === 80 ? '' : ':'.$webPort);
    }
    if (!filter_var($applicationUrl, FILTER_VALIDATE_URL) || !in_array(parse_url($applicationUrl, PHP_URL_SCHEME), ['http', 'https'], true)) {
        throw new RuntimeException('APP_URL debe ser una URL válida con http o https.');
    }
    define('BASE_URL', rtrim($applicationUrl, '/'));

    $databaseSetting = static function (string $name) use ($databaseEnvironment): string {
        $value = getenv($name);
        $value = $value !== false ? $value : ($databaseEnvironment[$name] ?? null);
        if (!is_string($value) || $value === '' || ($name !== 'DB_PASSWORD' && trim($value) === '')) {
            throw new RuntimeException('Falta definir '.$name.' en el entorno o en el .env de la raíz del proyecto.');
        }
        return $value;
    };

    // Conexión sin valores predeterminados en el código.
    define('DB_HOST', $databaseSetting('DB_HOST'));
    define('DB_NAME', $databaseSetting('DB_NAME'));
    define('DB_USER', $databaseSetting('DB_USER'));
    define('DB_PASSWORD', $databaseSetting('DB_PASSWORD'));
    define('DB_CHARSET', $databaseSetting('DB_CHARSET'));

    // Módulos del sistema: sus IDs coinciden con la tabla modules.
    const MODULE_DASHBOARD = 1;
    const MODULE_TASKS = 2;
    const MODULE_INVENTORY = 3;
    const MODULE_MOVEMENT = 4;
    const MODULE_HELP_DESK = 5;
    const MODULE_MAINTENANCE = 6;
    const MODULE_NETWORKS = 7;
    const MODULE_REMOTE_ACCESS = 8;
    const MODULE_HISTORY = 9;
    const MODULE_ADMINISTRATION = 10;

    // Roles del Sistema
	const ROLE_SUPER_ADMINISTRATOR = 1;
