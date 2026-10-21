<?php
require_once __DIR__.'/../vendor/tecnickcom/tcpdf/tcpdf.php';

/** Native PDF layout: explicit column widths and repeated table headers. */
class StandardReport extends TCPDF
{
    private string $issuedAt;
    private string $reportContext;
    public function __construct(string $context)
    {
        $this->reportContext=$context;
        parent::__construct('L','mm','A4',true,'UTF-8',false);
        $this->issuedAt=(new DateTimeImmutable('now',new DateTimeZone('America/Lima')))->format('d/m/Y H:i');
        $this->SetCreator('Sistema Integral de TI');
        $this->SetAuthor('Sistema Integral de TI');
        $this->SetTitle($context.' - Sistema Integral de TI');
        $this->SetMargins(12,23,12); $this->SetAutoPageBreak(true,16);
        $this->setCellPaddings(2,1.5,2,1.5);
    }
    public function Header()
    {
        $this->SetTextColor(15,23,42); $this->SetFont('dejavusans','B',10);
        $this->SetXY(12,8);$this->Cell(190,6,'Sistema Integral de TI',0,0);
        $this->SetFont('dejavusans','',8);$this->Cell(83,6,$this->reportContext,0,1,'R');
        $this->SetDrawColor(8,145,178);$this->Line(12,17,285,17);
    }
    public function Footer()
    {
        $this->SetY(-12);$this->SetFont('dejavusans','',7);$this->SetTextColor(100,116,139);
        $this->Cell(190,5,'Emitido: '.$this->issuedAt.' (America/Lima)',0,0);
        $this->Cell(83,5,'Página '.$this->getAliasNumPage().' de '.$this->getAliasNbPages(),0,0,'R');
    }
    protected function space(float $height): bool
    {
        if($this->GetY()+$height>194){$this->AddPage();return true;}return false;
    }
    protected function note(string $text): void
    {
        $this->SetFont('dejavusans','',8);$this->SetTextColor(71,85,105);
        $h=$this->getStringHeight(273,$text);$this->space(min($h+3,20));
        $this->MultiCell(273,0,$text,0,'L');$this->Ln(2);
    }
    protected function section(string $title): void
    {
        $this->space(27);$this->Ln(3);$this->SetFont('dejavusans','B',11);
        $this->SetTextColor(15,23,42);$this->Cell(273,8,$title,0,1);$this->Ln(1);
    }
    protected function cells(array $values,array $widths,bool $header=false): void
    {
        $this->SetFont('dejavusans',$header?'B':'',8.5);
        $height=8;
        foreach($values as $i=>$value)$height=max($height,$this->getStringHeight($widths[$i],(string)$value));
        $x=12;$y=$this->GetY();$this->SetDrawColor(219,229,241);
        $this->SetFillColor(...($header?[232,240,249]:[255,255,255]));$this->SetTextColor(23,33,47);
        foreach($values as $i=>$value){
            $this->Rect($x,$y,$widths[$i],$height,'DF');$this->SetXY($x,$y);
            $this->MultiCell($widths[$i],$height,(string)$value,0,(is_numeric($value) || preg_match('/^[0-9.,]+%$/',(string)$value))?'R':'L',false,0);
            $x+=$widths[$i];
        }
        $this->SetXY(12,$y+$height);
    }
    protected function table(string $title,array $headers,array $rows,array $ratios,string $empty='Sin registros para el alcance seleccionado.'): void
    {
        $this->space(26);$this->SetFont('dejavusans','B',9);$this->SetTextColor(15,23,42);
        $this->Cell(273,7,$title,0,1);
        if(!$rows){$this->note($empty);return;}
        $sum=array_sum($ratios);$widths=array_map(fn($r)=>273*$r/$sum,$ratios);
        $this->cells($headers,$widths,true);
        foreach($rows as $row){
            $this->SetFont('dejavusans','',8.5);$height=8;
            foreach($row as $i=>$value)$height=max($height,$this->getStringHeight($widths[$i],(string)$value));
            if($this->space($height)){$this->SetFont('dejavusans','B',9);$this->Cell(273,7,$title.' (continuación)',0,1);$this->cells($headers,$widths,true);}
            $this->cells($row,$widths);
        }
        $this->Ln(3);
    }
    protected function distribution(string $title,array $chart): void
    {
        $values=$chart['values']??[];$total=array_sum($values);$rows=[];
        foreach($chart['labels']??[] as $i=>$label){$v=(int)($values[$i]??0);$rows[]=[$label,$v,$total?number_format(100*$v/$total,1,',','.').'%':'No aplica'];}
        $this->table($title,['Concepto','Cantidad','% del conjunto'], $rows,[65,15,20]);
    }
    protected function mapped(array $rows,array $keys): array
    {
        return array_map(fn($r)=>array_map(fn($key)=>$r[$key]??'—',$keys),$rows);
    }
}
