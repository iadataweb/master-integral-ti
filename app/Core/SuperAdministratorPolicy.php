<?php
final class SuperAdministratorPolicy
{
    public static function controlValue(int $controlId): int
    {
        return $controlId > 0 ? 1 : 0;
    }
}
