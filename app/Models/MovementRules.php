<?php
final class MovementRules
{
    public static function allowedTypes(?int $currentType): array
    {
        return [1,2,3,4,5];
    }
    public static function situation(?int $type): string
    {
        return [1=>'Ingresado',2=>'Asignado',3=>'Transferido',4=>'Devuelto',5=>'Prestado'][$type] ?? 'Sin movimiento';
    }
}
