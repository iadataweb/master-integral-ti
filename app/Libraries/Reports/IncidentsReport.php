<?php
require_once __DIR__.'/StandardReport.php';

/** One portrait record sheet per incident; long content continues without truncation. */
class IncidentsReport extends StandardReport
{
    public function __construct()
    {
        parent::__construct('Incidencias');
        $this->setPageOrientation('P',true,16);
        $this->SetTitle('Reporte de incidencias - Sistema Integral de TI');
    }
    public function Header()
    {
        $this->SetTextColor(15,23,42);$this->SetFont('dejavusans','B',10);
        $this->SetXY(12,8);$this->Cell(125,6,'Sistema Integral de TI',0,0);
        $this->SetFont('dejavusans','',8);$this->Cell(61,6,'Incidencias',0,1,'R');
        $this->SetDrawColor(8,145,178);$this->Line(12,17,198,17);
    }
    public function Footer()
    {
        $this->SetY(-12);$this->SetFont('dejavusans','',7);$this->SetTextColor(100,116,139);
        $this->Cell(125,5,'Sistema Integral de TI | Incidencias',0,0);
        $this->Cell(61,5,'Página '.$this->getAliasNumPage().' de '.$this->getAliasNbPages(),0,0,'R');
    }
    private function escapeValue($value,string $empty='Sin registrar'): string
    {
        $value=trim((string)$value);
        return nl2br(htmlspecialchars($value!==''?$value:$empty,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8'));
    }
    private function dateParts($date,$time): string
    {
        $parsed=$date?DateTimeImmutable::createFromFormat('!Y-m-d',substr((string)$date,0,10)):false;
        $label=$parsed && $parsed->format('Y-m-d')===substr((string)$date,0,10)?$parsed->format('d/m/Y'):'Sin fecha registrada';
        return $label.' | '.($time?substr((string)$time,0,5):'Sin hora registrada');
    }
    private function attachmentPreview(array $file): string
    {
        $name=$this->escapeValue($file['name_attachment_incident']);
        $root=realpath(__DIR__.'/../../Assets/uploads');
        $path=realpath(__DIR__.'/../../'.ltrim((string)($file['route_attachment_incident']??''),'/'));
        if(!$root || !$path || !is_file($path) || !str_starts_with($path,$root.DIRECTORY_SEPARATOR))return $name;
        $size=@getimagesize($path);
        if(!$size || !in_array($size[2],[IMAGETYPE_JPEG,IMAGETYPE_PNG,IMAGETYPE_GIF],true))return $name;
        $scale=min(96/$size[0],115/$size[1],1);
        $width=max(1,(int)round($size[0]*$scale));$height=max(1,(int)round($size[1]*$scale));
        return '<img src="'.$this->escapeValue($path).'" width="'.$width.'" height="'.$height.'" />';
    }
    private function overview(array $report,string $user): void
    {
        $this->AddPage();$this->SetFont('dejavusans','',8.5);$this->SetTextColor(23,33,47);
        $html='<h1 style="color:#0f172a;font-size:16pt;">Reporte de incidencias</h1><p>Elaborado por: '.$this->escapeValue($user).'<br />Emitido: '.(new DateTimeImmutable('now',new DateTimeZone('America/Lima')))->format('d/m/Y H:i').' (America/Lima)</p>';
        $filters=[];foreach($report['filters_applied'] as $label=>$value)$filters[]=$this->escapeValue($label).': '.$this->escapeValue($value);
        $html.='<p style="color:#475569;font-size:8pt;">'.implode(' | ',$filters).'</p><h2 style="font-size:12pt;">Resumen ejecutivo</h2>';
        $summary=$report['summary'];
        $html.='<p>Total coincidente: '.(int)$summary['total'].' | Abiertas: '.(int)$summary['open'].' | Cerradas: '.(int)$summary['closed'].'</p>';
        $html.='<table border="1" cellpadding="5" cellspacing="0" width="100%"><thead><tr style="background-color:#e8f0f9;font-weight:bold;"><th width="50%">Incidencias por tipo</th><th width="50%">Incidencias por prioridad</th></tr></thead><tbody>';
        $types=$report['by_type'];$priorities=$report['by_priority'];
        for($i=0;$i<max(count($types),count($priorities),1);$i++){
            $html.='<tr>';
            foreach([$types,$priorities] as $group){
                $html.='<td width="50%">'.(isset($group[$i])?$this->escapeValue($group[$i]['label']).' · '.(int)$group[$i]['total']:($i===0?'Sin registros':'')).'</td>';
            }
            $html.='</tr>';
        }
        $html.='</tbody></table><h2 style="font-size:12pt;">Listado resumido</h2><p style="font-size:8pt;color:#475569;">Se incluyen '.count($report['incidents']).' de '.(int)$summary['total'].' incidencias según el límite seleccionado. El resumen comprende todos los resultados de los filtros.</p>';
        $widths=[7,19,27,12,12,23];$headings=['#','Tipo','Título','Prioridad','Estado','Fecha de incidencia'];
        $html.='<table border="1" cellpadding="4" cellspacing="0" width="100%"><thead><tr style="background-color:#e8f0f9;font-weight:bold;">';
        foreach($headings as $i=>$heading)$html.='<th width="'.$widths[$i].'%">'.$heading.'</th>';
        $html.='</tr></thead><tbody>';
        foreach($report['incidents'] as $incident){
            $title=(string)$incident['title_incident'];if(mb_strlen($title)>100)$title=mb_substr($title,0,97).'...';
            $values=[(int)$incident['id_incident'],$incident['name_type_incident'],$title,$incident['name_priority'],(int)$incident['status_open_incident']===1?'Abierta':'Cerrada',$this->dateParts($incident['date_incident_date'],$incident['time_incident_time'])];
            $html.='<tr>';foreach($values as $i=>$value)$html.='<td width="'.$widths[$i].'%">'.$this->escapeValue($value).'</td>';$html.='</tr>';
        }
        if(!$report['incidents'])$html.='<tr><td colspan="6">Sin incidencias para los filtros seleccionados.</td></tr>';
        $html.='</tbody></table>';
        $this->writeHTML($html,true,false,true,false,'');
    }
    public function render(array $report,string $user): void
    {
        $this->overview($report,$user);
        if($report['incidents']){
            $this->AddPage();$this->SetFont('dejavusans','B',22);$this->SetTextColor(15,23,42);
            $height=18;
            $this->SetXY(12,($this->getPageHeight()-$height)/2);
            $this->Cell($this->getPageWidth()-24,$height,'Detalle de incidencias',0,1,'C',false,'',0,false,'T','M');
        }
        foreach($report['incidents'] as $incident){
            $this->AddPage();$this->SetFont('dejavusans','',8.5);$this->SetTextColor(23,33,47);$this->SetDrawColor(219,229,241);$this->SetLineWidth(0.15);
            $rows=[
                'Título'=>$incident['title_incident'],
                'Tipo de incidencia'=>$incident['name_type_incident'],
                'Prioridad'=>$incident['name_priority'],
                'Estado'=>(int)$incident['status_open_incident']===1?'Abierta':'Cerrada',
                'Ubicación'=>$incident['display_location'],
                'Descripción'=>$incident['description_incident'],
                'Fecha y hora de incidencia'=>$this->dateParts($incident['date_incident_date'],$incident['time_incident_time']),
                'Fecha y hora de cierre'=>$this->dateParts($incident['date_closed_incident'],$incident['time_closed_incident']),
                'Código de inventario'=>$incident['affected_code_inventory_asset']?:'No vinculado',
                'Tipo de activo'=>$incident['affected_name_type_asset']?:'No definido',
                'Categoría de activo'=>$incident['affected_name_category_asset']?:'No definido',
                'Marca'=>$incident['affected_name_brand']?:'No definido',
                'Modelo'=>$incident['affected_model_asset']?:'No definido',
                'Diagnóstico'=>$incident['action_diagnostic'],
                'Solución'=>$incident['action_solution']
            ];
            $html='<table border="1" bordercolor="#dbe5f1" cellpadding="5" cellspacing="0" width="100%"><thead><tr><th colspan="2" style="background-color:#e8f0f9;color:#0f172a;font-size:11pt;font-weight:bold;">Información de la incidencia Nro. '.(int)$incident['id_incident'].'</th></tr></thead><tbody>';
            foreach($rows as $label=>$value){
                $html.='<tr><td width="32%" style="background-color:#f8fafc;font-weight:bold;">'.$this->escapeValue($label).'</td><td width="68%">'.$this->escapeValue($value).'</td></tr>';
            }
            $files=$incident['report_attachments']??[];$attachments=[];
            foreach(array_chunk($files,3) as $group){
                $cells='<tr>';
                for($i=0;$i<3;$i++){
                    $cells.='<td width="33.33%" align="center" valign="top" style="font-size:7pt;">'.(isset($group[$i])?$this->attachmentPreview($group[$i]):'').'</td>';
                }
                $attachments[]=$cells.'</tr>';
            }
            $html.='<tr><td width="32%" style="background-color:#f8fafc;font-weight:bold;">Archivos adjuntos</td><td width="68%">'.($attachments?'<table cellpadding="3" cellspacing="0" width="100%">'.implode('',$attachments).'</table>':'Sin archivos adjuntos registrados.').'</td></tr></tbody></table>';
            $this->writeHTML($html,true,false,true,false,'');
        }
    }
}
