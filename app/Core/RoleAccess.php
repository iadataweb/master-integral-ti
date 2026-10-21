<?php
final class RoleAccess
{
    private static array $modules = [];
    private static array $controls = [];

    public static function reset(): void
    {
        self::$modules = [];
        self::$controls = [];
    }

    public static function modules(int $role): array
    {
        if ($role <= 0) return [];
        if (!array_key_exists($role, self::$modules)) {
            require_once __DIR__.'/../Models/PermissionsModel.php';
            self::$modules[$role] = (new PermissionsModel())->extract_permissions($role);
        }
        return self::$modules[$role];
    }

    public static function controls(int $role, int $module): array
    {
        if ($role <= 0) return [];
        $key = $role.':'.$module;
        if (!array_key_exists($key, self::$controls)) {
            require_once __DIR__.'/../Models/AdditionalPermissionsModel.php';
            self::$controls[$key] = (new AdditionalPermissionsModel())->allowed_controls_map($role, $module);
        }
        return self::$controls[$key];
    }

    public static function allowed(int $role, int $module, string $control): bool
    {
        return (int)(self::controls($role, $module)[$control] ?? 0) === 1;
    }
}
