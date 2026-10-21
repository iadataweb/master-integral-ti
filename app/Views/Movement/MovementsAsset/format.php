<?php
    $movement = $data['movement'] ?? [];
    $safe = function ($value) {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    };
    $display = function ($value, $fallback = 'No registrado') use ($safe) {
        $text = trim((string)($value ?? ''));
        return $safe($text !== '' ? $text : $fallback);
    };
    $movement_type_id = (int)($movement['id_type_movement'] ?? 0);
    $is_loan = $movement_type_id === 5;
    $movement_date_label = $movement_type_id === 4 ? 'Fecha de retorno' : 'Fecha de movimiento';
    $location_title = 'Ubicacion';
    if ($movement_type_id === 3) {
        $location_title = 'Ubicacion destino';
    } elseif ($movement_type_id === 4) {
        $location_title = 'Ubicacion de retorno / almacen';
    } elseif ($is_loan) {
        $location_title = 'Ubicacion de control del prestamo';
    }
    $responsible_receiver = trim((string)($movement['responsible_receiver'] ?? ''));
    $manual_responsible = trim((string)($movement['manual_responsible_movement_asset'] ?? ''));
    $loan_receiver = trim((string)($movement['loan_recipient_name_movement_asset'] ?? ''));
    if ($loan_receiver === '' && ($movement['loan_recipient_type_movement_asset'] ?? '') === 'internal') {
        $loan_receiver = $responsible_receiver ?: trim((string)($movement['responsible_employee'] ?? ''));
    }
    $loan_return_mode = ((int)($movement['loan_return_indefinite_movement_asset'] ?? 0) === 1) ? 'Devolucion indeterminada' : 'Con fecha definida';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formato de Movimiento de Activo</title>
    <style>
        :root {
            --ink: #17212f;
            --muted: #64748b;
            --line: #d8e1ec;
            --soft: #f5f8fc;
            --brand: #1f5fbf;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 28px;
            color: var(--ink);
            font-family: Arial, sans-serif;
            background: #eef3f9;
        }
        .page {
            max-width: 980px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            overflow: hidden;
        }
        .header {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 24px 28px;
            border-bottom: 1px solid var(--line);
            background: linear-gradient(135deg, #f8fbff 0%, #eef5ff 100%);
        }
        .title h1 {
            margin: 0 0 6px;
            font-size: 24px;
        }
        .title p {
            margin: 0;
            color: var(--muted);
        }
        .code {
            min-width: 210px;
            padding: 12px 14px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #fff;
            text-align: right;
        }
        .code strong {
            display: block;
            color: var(--brand);
            font-size: 18px;
        }
        .toolbar {
            display: flex;
            justify-content: flex-end;
            padding: 14px 28px 0;
        }
        .btn-print {
            border: 0;
            border-radius: 10px;
            background: var(--brand);
            color: #fff;
            cursor: pointer;
            font-weight: 700;
            padding: 10px 18px;
        }
        .content {
            padding: 24px 28px 30px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h2 {
            margin: 0 0 12px;
            font-size: 15px;
            color: var(--brand);
            text-transform: uppercase;
            letter-spacing: .4px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }
        .item {
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px 12px;
            background: var(--soft);
        }
        .item label {
            display: block;
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        .item div {
            font-weight: 700;
            word-break: break-word;
        }
        .box {
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 14px;
            min-height: 70px;
            background: #fff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border-bottom: 1px solid var(--line);
            padding: 10px;
            text-align: left;
        }
        th {
            color: var(--muted);
            font-size: 11px;
            text-transform: uppercase;
            background: var(--soft);
        }
        .signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-top: 48px;
        }
        .signature {
            padding-top: 44px;
            border-top: 1px solid var(--ink);
            text-align: center;
            color: var(--muted);
            font-size: 13px;
        }
        @media (max-width: 720px) {
            body { padding: 12px; }
            .header, .grid, .signatures { grid-template-columns: 1fr; }
            .header { display: grid; }
            .code { text-align: left; }
        }
        @media print {
            body { padding: 0; background: #fff; }
            .page { max-width: none; border: 0; border-radius: 0; }
            .toolbar { display: none; }
            a { color: var(--ink); text-decoration: none; }
        }
    </style>
</head>
<body>
    <main class="page">
        <header class="header">
            <div class="title">
                <h1>Formato de Movimiento de Activo</h1>
                <p>Registro imprimible para conformidad y supervision.</p>
            </div>
            <div class="code">
                <span>Documento</span>
                <strong><?= $safe($movement['document_code_movement_asset'] ?? 'No definido'); ?></strong>
            </div>
        </header>

        <div class="toolbar">
            <button class="btn-print" type="button" onclick="window.print()">Imprimir / Guardar PDF</button>
        </div>

        <section class="content">
            <div class="section">
                <h2>Datos del activo</h2>
                <div class="grid">
                    <div class="item"><label>Codigo de inventario</label><div><?= $display($movement['code_inventory_asset'] ?? ''); ?></div></div>
                    <div class="item"><label>Tipo de activo</label><div><?= $display($movement['name_type_asset'] ?? ''); ?></div></div>
                    <div class="item"><label>Categoria</label><div><?= $display($movement['name_category_asset'] ?? ''); ?></div></div>
                    <div class="item"><label>Marca</label><div><?= $display($movement['name_brand'] ?? ''); ?></div></div>
                    <div class="item"><label>Modelo</label><div><?= $display($movement['model_asset'] ?? ''); ?></div></div>
                    <div class="item"><label>Numero de serie</label><div><?= $display($movement['serial_number_asset'] ?? ''); ?></div></div>
                    <div class="item"><label>Condicion</label><div><?= $display($movement['name_condition_asset'] ?? ''); ?></div></div>
                    <div class="item"><label>Fecha de ingreso</label><div><?= $display($movement['entry_date_asset'] ?? '', 'No definido'); ?></div></div>
                    <div class="item"><label>Fecha de adquisicion</label><div><?= $display($movement['acquisition_date_asset'] ?? '', 'No definido'); ?></div></div>
                </div>
            </div>

            <div class="section">
                <h2>Movimiento</h2>
                <div class="grid">
                    <div class="item"><label>ID de movimiento</label><div><?= $display($movement['id_movement_asset'] ?? ''); ?></div></div>
                    <div class="item"><label>Tipo de movimiento</label><div><?= $display($movement['name_type_movement'] ?? ''); ?></div></div>
                    <div class="item"><label>Estado</label><div><?= $display($movement['status_process_movement_asset'] ?? ''); ?></div></div>
                    <div class="item"><label>Registro vigente</label><div><?= ((int)($movement['status_record_current_movement_asset'] ?? 0) === 1) ? 'Si' : 'No'; ?></div></div>
                    <div class="item"><label>Documento</label><div><?= $display($movement['document_code_movement_asset'] ?? '', 'No definido'); ?></div></div>
                    <?php if (!$is_loan): ?>
                        <div class="item"><label><?= $safe($movement_date_label); ?></label><div><?= $display($movement['date_event_movement_asset'] ?? '', 'No definido'); ?></div></div>
                        <div class="item"><label>Hora de movimiento</label><div><?= $display($movement['time_event_movement_asset'] ?? '', 'No definido'); ?></div></div>
                        <div class="item"><label>Responsable</label><div><?= $display($movement['responsible_employee'] ?? '', 'No asignado'); ?></div></div>
                    <?php endif; ?>
                    <div class="item"><label>Fecha de registro</label><div><?= $display($movement['date_created_movement_asset'] ?? ''); ?></div></div>
                    <div class="item"><label>Ultima actualizacion</label><div><?= $display($movement['date_updated_movement_asset'] ?? ''); ?></div></div>
                </div>
            </div>

            <?php if ($is_loan): ?>
                <div class="section">
                    <h2>Datos del prestamo</h2>
                    <div class="grid">
                        <div class="item"><label>Tipo de prestamo</label><div><?= $display($movement['loan_recipient_type_label'] ?? 'No aplica'); ?></div></div>
                        <div class="item"><label>Persona / empresa que recibe</label><div><?= $display($loan_receiver); ?></div></div>
                        <div class="item"><label>Documento o referencia del receptor</label><div><?= $display($movement['loan_recipient_document_movement_asset'] ?? ''); ?></div></div>
                        <div class="item"><label>Responsable interno / control</label><div><?= $display($movement['responsible_employee'] ?? '', 'No asignado'); ?></div></div>
                        <div class="item"><label>Fecha de entrega</label><div><?= $display($movement['date_event_movement_asset'] ?? '', 'No definido'); ?></div></div>
                        <div class="item"><label>Hora de movimiento</label><div><?= $display($movement['time_event_movement_asset'] ?? '', 'No definido'); ?></div></div>
                        <div class="item"><label>Fecha esperada de devolucion</label><div><?= $display($movement['expected_return_date_movement_asset'] ?? '', 'No aplica'); ?></div></div>
                        <div class="item"><label>Modalidad de devolucion</label><div><?= $safe($loan_return_mode); ?></div></div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($responsible_receiver !== ''): ?>
                <div class="section">
                    <h2>Responsable que recibe</h2>
                    <div class="grid">
                        <div class="item"><label>Nombre</label><div><?= $display($responsible_receiver); ?></div></div>
                        <div class="item"><label>Origen del registro</label><div><?= $manual_responsible !== '' ? 'Registro manual' : 'Colaborador interno'; ?></div></div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="section">
                <h2><?= $safe($location_title); ?></h2>
                <div class="grid">
                    <div class="item"><label>Ambiente</label><div><?= $display($movement['name_space'] ?? ''); ?></div></div>
                    <div class="item"><label>Piso</label><div><?= $display($movement['floor_space'] ?? ''); ?></div></div>
                    <div class="item"><label>Establecimiento</label><div><?= $display($movement['name_campus'] ?? ''); ?></div></div>
                    <div class="item"><label>Uso del ambiente</label><div><?= $display($movement['use_environment'] ?? ''); ?></div></div>
                    <div class="item"><label>Plano institucional</label><div><?= $display($movement['name_floor_plan'] ?? '', 'No definido'); ?></div></div>
                    <div class="item"><label>Coordenada en plano</label><div>
                        <?php if (($movement['coordinate_x_movement_asset'] ?? '') !== '' && ($movement['coordinate_y_movement_asset'] ?? '') !== ''): ?>
                            X <?= number_format((float)$movement['coordinate_x_movement_asset'], 2); ?>% / Y <?= number_format((float)$movement['coordinate_y_movement_asset'], 2); ?>%
                        <?php else: ?>
                            No definida
                        <?php endif; ?>
                    </div></div>
                </div>
            </div>

            <div class="section">
                <h2>Informacion complementaria del activo</h2>
                <div class="grid">
                    <div class="item"><label>Descripcion del activo</label><div><?= nl2br($display($movement['descriptions_asset'] ?? '', 'Ninguna.')); ?></div></div>
                    <div class="item"><label>Observacion del activo</label><div><?= nl2br($display($movement['observations_asset'] ?? '', 'Ninguna.')); ?></div></div>
                </div>
            </div>

            <div class="section">
                <h2>Observaciones</h2>
                <div class="box"><?= nl2br($display($movement['observations_movement_asset'] ?? '', 'Ninguna.')); ?></div>
            </div>


            <div class="signatures">
                <div class="signature">Entrega</div>
                <div class="signature">Recibe / Responsable</div>
                <div class="signature">Autoriza</div>
            </div>
        </section>
    </main>
</body>
</html>
