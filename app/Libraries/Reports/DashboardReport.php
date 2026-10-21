<?php
require_once __DIR__.'/StandardReport.php';
class DashboardReport extends StandardReport
{
    public function __construct() { parent::__construct('Panel de Control'); $this->SetTitle('Reporte de gestión de TI - Panel de Control'); }
    public function render(array $data,array $filterLabels,string $user): void
    {
        $this->AddPage();$s=$data['summary'];$t=$data['tables'];$c=$data['charts'];$stored=$t['stored_assets_status'];
        $this->SetFont('dejavusans','B',18);$this->SetTextColor(15,23,42);$this->Cell(273,12,'Reporte de gestión de TI',0,1);
        $this->note('Panel de Control | Elaborado por: '.$user);
        $this->note(implode('  |  ',$filterLabels));
        $this->section('1. Resumen ejecutivo');
        $this->table('Indicadores principales',['Indicador','Cantidad','Interpretación'],[
            ['Activos registrados',$s['assets'],'Inventario según los filtros aplicables'],
            ['Activos sin ubicación vigente',$s['assets_without_movement'],'Pendientes de regularizar su movimiento'],
            ['Disponibles en almacén',$stored['available_storage']??0,'Equipos operativos disponibles'],
            ['Movimientos registrados',$s['movements'],'Eventos logísticos del período'],
            ['Incidencias abiertas',$s['open_incidents'],'Casos que requieren seguimiento'],
            ['Incidencias cerradas',$s['closed_incidents'],'Casos resueltos'],
            ['Repuestos con stock bajo',$s['low_stock'],'Alertas de reposición actuales']
        ],[40,12,48]);
        $this->note('Alcance: las fechas se aplican al registro de activos y a las fechas de actividad de cada módulo; la ubicación y la condición reflejan el estado vigente, no una reconstrucción histórica. El stock corresponde al catálogo actual. Los porcentajes utilizan el total de cada conjunto mostrado; cuando ese total es cero, no aplica un porcentaje.');
        $this->section('2. Inventario y ubicación');
        $this->distribution('Activos por categoría',$c['assets_by_category']);
        $this->table('Activos por piso',['Piso','Total de activos'],array_map(fn($r)=>[$r['floor_environment']===null?'Sin piso definido':'Piso '.$r['floor_environment'],(int)$r['total_assets']],$t['assets_by_floor']),[75,25]);
        $this->table('Activos por ambiente',['#','Ambiente','Piso','Total'], $this->mapped($t['assets_by_environment'],['id_environment','name_environment','floor_environment','total_assets']),[8,60,16,16]);
        $this->table('Inventario por tipo y situación vigente',['Tipo','Total','Almacén','Asignados','Transferidos','Prestados','Mant./otros'], $this->mapped($t['assets_by_type_flow'],['type_asset','total_assets','available_storage','assigned_use','transferred_use','loaned_use','maintenance_other']),[28,10,12,12,14,12,12]);
        $this->table('Condición técnica por tipo',['Tipo','Total','Operativo','Inoperativo','Mantenimiento','Baja','Otros'],$this->mapped($t['assets_by_type_condition'],['type_asset','total_assets','operative_assets','inoperative_assets','maintenance_assets','retired_assets','other_assets']),[28,10,12,13,17,10,10]);
        $this->section('3. Disponibilidad');
        $this->table('Activos almacenados',['Total en almacén','Disponibles','Operativos','Inoperativos','Mantenimiento','Baja','Otros'],[array_map(fn($k)=>(int)($stored[$k]??0),['total_storage','available_storage','operative_storage','inoperative_storage','maintenance_storage','retired_storage','other_storage'])],[18,14,14,14,16,10,14]);
        $this->section('4. Operación de TI');
        $this->distribution('Movimientos por tipo',$c['movements_by_type']);
        $this->distribution('Estado de incidencias',['labels'=>['Abiertas','Cerradas'],'values'=>[$s['open_incidents'],$s['closed_incidents']]]);
        $this->distribution('Incidencias por tipo',$c['incidents_by_type']);
        $maintenance=[];
        foreach($c['maintenance_overview']['labels'] as $i=>$label)$maintenance[]=[$label,(int)($c['maintenance_overview']['values'][$i]??0)];
        $this->space(24 + count($maintenance)*8);
        $this->note('Los registros de mantenimiento programado y realizado son indicadores independientes; no se suman como un único total.');
        $this->table('Mantenimiento',['Concepto','Cantidad'],$maintenance,[75,25]);
        $this->section('5. Repuestos y alertas');
        $this->note('Repuestos en catálogo: '.$s['spares'].'. Alertas actuales de stock bajo: '.$s['low_stock'].'.');
        $this->table('Repuestos que requieren reposición (hasta 10 registros)',['Código','Repuesto','Stock','Mínimo','Unidad'],$this->mapped($t['low_stock'],['code_spare_part','name_spare_part','stock_current_spare_part','stock_minimum_spare_part','unit_spare_part']),[20,40,12,12,16],'Sin repuestos por debajo del mínimo configurado.');
        if($s['low_stock']>count($t['low_stock']))$this->note('Se muestran '.count($t['low_stock']).' de '.$s['low_stock'].' alertas. Consulta Repuestos para el listado completo.');
        $this->section('6. Detalle complementario');
        $this->table('Activos sin ubicación definida',['#','Código','Tipo','Categoría','Condición'],$this->mapped($t['assets_without_movement'],['id_asset','code_inventory_asset','name_type_asset','name_category_asset','name_condition_asset']),[8,28,18,28,18],'No hay activos pendientes de registrar su ubicación.');
        $recent=array_map(fn($r)=>[$r['id_incident'],$r['name_type_incident'],$r['title_incident'],(int)$r['status_open_incident']===1?'Abierta':'Cerrada',date('d/m/Y',strtotime($r['date_created_incident']))],$t['recent_incidents']);
        $this->table('Incidencias recientes (hasta 10 registros)',['#','Tipo','Título','Estado','Registro'],$recent,[8,20,44,14,14],'Sin incidencias para el alcance seleccionado.');
        if($s['incidents']>count($recent))$this->note('Se muestran '.count($recent).' de '.$s['incidents'].' incidencias. Consulta Incidencias para el listado completo.');
    }
}
