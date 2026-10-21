<?php

use Fpdf\Fpdf;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class PDFEtiqueta extends Fpdf {}

$labels = $data['labels'] ?? $data;
$template = $data['template'] ?? null;

if (!is_array($template) || empty($template['design_route_label_template'])) {
    http_response_code(400);echo 'Seleccione una plantilla registrada.';return;
}
$templatePath = __DIR__.'/../../../'.ltrim($template['design_route_label_template'],'/');
if (!is_file($templatePath)) { http_response_code(400);echo 'El diseño de la plantilla no está disponible.';return; }
$labelWidth = max(1,(float)$template['width_cm_label_template']*10);
$labelHeight = max(1,(float)$template['height_cm_label_template']*10);
$qrXPercent = (float)$template['qr_x_percent_label_template'];
$qrYPercent = (float)$template['qr_y_percent_label_template'];
$qrWPercent = (float)$template['qr_width_percent_label_template'];
$qrHPercent = (float)$template['qr_height_percent_label_template'];
$codeXPercent = (float)($template['code_x_percent_label_template']??5);
$codeYPercent = (float)($template['code_y_percent_label_template']??75);
$codeWPercent = (float)($template['code_width_percent_label_template']??45);
$codeHPercent = (float)($template['code_height_percent_label_template']??12);
$codeFontSize = (float)($template['code_font_size_label_template']??8);
$pdf = new PDFEtiqueta('P', 'mm', 'A4');
$pdf->AddPage();
$pageWidth = 210;
$pageHeight = 297;
$columns = 2;
$marginY = 8;
$gapX = 4;
$gapY = 3;

// A4 vertical: two centered columns while preserving the configured label size when it fits.
$minimumMarginX = 6;
$totalLabelWidth = ($labelWidth * $columns) + ($gapX * ($columns - 1));
$maxContentWidth = $pageWidth - ($minimumMarginX * 2);
if ($totalLabelWidth > $maxContentWidth) {
    $scale = $maxContentWidth / $totalLabelWidth;
    $labelWidth *= $scale;
    $labelHeight *= $scale;
    $totalLabelWidth = ($labelWidth * $columns) + ($gapX * ($columns - 1));
}
$marginX = ($pageWidth - $totalLabelWidth) / 2;
$perRow = $columns;
$counter = 0;

foreach ($labels as $label) {
    $col = $counter % $perRow;
    $row = floor($counter / $perRow);
    $x = $marginX + ($col * ($labelWidth + $gapX));
    $y = $marginY + ($row * ($labelHeight + $gapY));

    if ($y + $labelHeight > $pageHeight - $marginY) {
        $pdf->AddPage();
        $counter = 0;
        $col = 0;
        $row = 0;
        $x = $marginX;
        $y = $marginY;
    }

    $pdf->Image($templatePath, $x, $y, $labelWidth, $labelHeight);

    $url = base_url() . "/scan_quick/asset/data/{$label['code_inventory_asset']}";
    $options = new QROptions([
        'outputType' => QRCode::OUTPUT_IMAGE_PNG,
        'scale' => 6,
        'addQuietzone' => true,
        'quietzoneSize' => 2,
    ]);
    $qrImage = (new QRCode($options))->render($url);
    $tmpFile = sys_get_temp_dir() . "/qr_{$label['id_asset']}_" . uniqid() . ".png";
    file_put_contents($tmpFile, base64_decode(explode(',', $qrImage)[1]));

    $pdf->Image(
        $tmpFile,
        $x + ($labelWidth * ($qrXPercent / 100)),
        $y + ($labelHeight * ($qrYPercent / 100)),
        $labelWidth * ($qrWPercent / 100),
        $labelHeight * ($qrHPercent / 100)
    );

    $codeText = $label['code_inventory_asset'];
    $codeBoxX = $x + ($labelWidth * ($codeXPercent / 100));
    $codeBoxY = $y + ($labelHeight * ($codeYPercent / 100));
    $codeBoxW = max(1, $labelWidth * ($codeWPercent / 100));
    $codeBoxH = max(1, $labelHeight * ($codeHPercent / 100));

    // The editor stores a visual CSS pixel size; PDF uses points and FPDF does not clip overflowing text.
    $pdfFontSize = max(4, min(24, $codeFontSize * 0.75));
    do {
        $pdf->SetFont('Arial', 'B', $pdfFontSize);
        if ($pdf->GetStringWidth($codeText) <= ($codeBoxW - 1) || $pdfFontSize <= 4) {
            break;
        }
        $pdfFontSize -= 0.25;
    } while ($pdfFontSize > 4);

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY($codeBoxX, $codeBoxY);
    $pdf->Cell($codeBoxW, $codeBoxH, $codeText, 0, 1, 'C');

    if (file_exists($tmpFile)) {
        unlink($tmpFile);
    }
    $counter++;
}

$pdf->Output('I', 'Etiquetas_Inventario.pdf');
