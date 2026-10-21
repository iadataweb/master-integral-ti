<!DOCTYPE html>
<html lang="es">

<?php require('Layouts/head.php'); ?>

<?php
$context = $data['quick_context'] ?? [];
$asset = $context['asset'] ?? ($data['data_quick'] ?? null);
$isPublicQuickAccess = !empty($data['is_public_quick_access']);

function sq_h($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function sq_text($value, string $fallback = 'No registrado'): string
{
    $value = trim((string)($value ?? ''));
    return $value !== '' ? sq_h($value) : $fallback;
}

function sq_date($value, bool $withTime = false): string
{
    if (empty($value) || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
        return 'No registrado';
    }
    $timestamp = strtotime((string)$value);
    if (!$timestamp) {
        return sq_h($value);
    }
    return date($withTime ? 'd/m/Y H:i' : 'd/m/Y', $timestamp);
}

function sq_file_url(array $file): string
{
    $route = trim((string)($file['route'] ?? ''));
    if ($route === '') {
        return '#';
    }
    if (preg_match('/^https?:\/\//i', $route)) {
        return $route;
    }
    return base_url() . '/' . ltrim($route, '/');
}

function sq_render_files(array $files, string $empty = 'Sin archivos adjuntos.'): void
{
    if (empty($files)) {
        echo '<div class="sq-empty small">' . sq_h($empty) . '</div>';
        return;
    }
    echo '<div class="sq-file-list">';
    foreach ($files as $file) {
        $type = isset($file['attachment_type'])
            ? '<span class="sq-file-type">' . sq_h($file['attachment_type'] === 'before' ? 'Antes' : ($file['attachment_type'] === 'after' ? 'Despues' : $file['attachment_type'])) . '</span>'
            : '';
        echo '<a class="sq-file" href="' . sq_h(sq_file_url($file)) . '" target="_blank" rel="noopener">';
        echo '<span class="sq-file-name">' . sq_text($file['name'] ?? 'Archivo') . $type . '</span>';
        echo '<span class="sq-file-action">Abrir </span>';
        echo '</a>';
    }
    echo '</div>';
}

function sq_status_badge($active, string $activeText = 'Activo', string $inactiveText = 'Inactivo'): string
{
    return ((int)$active === 1)
        ? '<span class="sq-badge success">' . sq_h($activeText) . '</span>'
        : '<span class="sq-badge muted">' . sq_h($inactiveText) . '</span>';
}
?>

<body>

<main class="sq-page">
    <?php if (!$isPublicQuickAccess) { ?>
        <?php require('Layouts/header.php'); ?>
    <?php } ?>

    <link rel="stylesheet" href="<?= assets(); ?>/css/components/scanquick-asset-index.css?v=<?= filemtime(dirname(__DIR__, 3) . '/Assets/css/components/scanquick-asset-index.css'); ?>">

    <?php if ($isPublicQuickAccess) { ?>
        <div class="sq-public-header">
            <div class="sq-public-brand"> Sistema Integral de TI</div>
            <span class="sq-badge info"> Consulta pública</span>
        </div>
    <?php } ?>

    <div class="sq-shell">
        <?php if (!$asset) { ?>
            <section class="sq-panel sq-not-found">
                
                <h1>Activo no encontrado</h1>
                <p class="text-muted mb-3">No se encontró información para el código solicitado.</p>
                <?php if (!$isPublicQuickAccess) { ?>
                    <a href="<?= base_url(); ?>/dashboard" class="btn btn-primary">Volver al inicio</a>
                <?php } ?>
            </section>
        <?php } else { ?>
            <section class="sq-hero">
                <div>
                    <span class="sq-eyebrow">Consulta rápida</span>
                    <h1 class="h4">Ficha del activo</h1>
                    <p>Información del activo, ubicación y mantenimientos.</p>
                </div>
                <div class="sq-hero-actions">
                    <?php if (!$isPublicQuickAccess) { ?>
                        <a href="<?= base_url(); ?>/inventory/assets/manage_assets" class="btn btn-sm btn-outline-gray-600"> Inventario</a>
                    <?php } ?>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="window.print()"> Imprimir</button>
                </div>
            </section>

            <section class="sq-grid">
                <article class="sq-card sq-span-8">
                    <div class="sq-asset-main">
                        
                        <div>
                            <p class="sq-code"><?= sq_text($asset['code_inventory_asset']); ?></p>
                            <p class="sq-subtitle"><?= sq_text($asset['name_type_asset']); ?> · <?= sq_text($asset['name_category_asset']); ?> · <?= sq_text($asset['name_brand']); ?></p>
                            <div class="mt-2">
                                <?= sq_status_badge($asset['status_active_asset'], 'Activo', 'Inactivo'); ?>
                                <span class="sq-badge info"> <?= sq_text($asset['name_condition_asset']); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="sq-kpi-grid">
                        <div class="sq-kpi"><span class="sq-label">Modelo</span><span class="sq-value"><?= sq_text($asset['model_asset']); ?></span></div>
                        <div class="sq-kpi"><span class="sq-label">Número de serie</span><span class="sq-value"><?= sq_text($asset['serial_number_asset']); ?></span></div>
                        <div class="sq-kpi"><span class="sq-label">Fecha de registro</span><span class="sq-value"><?= sq_date($asset['date_created_asset'], true); ?></span></div>
                        <div class="sq-kpi"><span class="sq-label">Fecha de ingreso</span><span class="sq-value"><?= sq_date($asset['entry_date_asset']); ?></span></div>
                        <div class="sq-kpi"><span class="sq-label">Fecha de adquisición</span><span class="sq-value"><?= sq_date($asset['acquisition_date_asset']); ?></span></div>
                        <div class="sq-kpi"><span class="sq-label">Mantenimiento</span><span class="sq-value"><?= ((int)$asset['status_maintenance_asset'] === 1) ? 'Con mantenimiento' : 'Sin mantenimiento activo'; ?></span></div>
                    </div>
                </article>

                <article class="sq-card sq-span-4">
                    <div class="sq-card-title">
                        <div class="sq-section-title"><div><h2>Archivos del activo</h2><p>Documentos e imágenes.</p></div></div>
                    </div>
                    <?php sq_render_files($context['attachments_asset'] ?? []); ?>
                </article>

                <article class="sq-card sq-span-6">
                    <div class="sq-card-title"><div class="sq-section-title"><h2>Descripción y observaciones</h2></div></div>
                    <span class="sq-label">Descripción</span>
                    <div class="sq-description-box mb-3"><?= nl2br(sq_text($asset['descriptions_asset'], 'Sin descripción registrada.')); ?></div>
                    <span class="sq-label">Observación</span>
                    <div class="sq-description-box"><?= nl2br(sq_text($asset['observations_asset'], 'Sin observaciones registradas.')); ?></div>
                </article>

                <article class="sq-card sq-span-6">
                    <div class="sq-card-title">
                        <div class="sq-section-title"><div><h2>Movimientos y ubicación</h2><p>Ubicación actual y movimiento anterior.</p></div></div>
                    </div>
                    <div class="sq-movement-stack">
                        <?php
                        $movements = [
                            ['title' => 'Movimiento actual', 'item' => $context['current_movement'] ?? null, 'class' => 'current'],
                            ['title' => 'Movimiento anterior', 'item' => $context['previous_movement'] ?? null, 'class' => 'previous'],
                        ];
                        foreach ($movements as $movement) {
                            $item = $movement['item'];
                            if (!$item) {
                                echo '<div class="sq-empty small">' . sq_h($movement['title']) . ': sin información registrada.</div>';
                                continue;
                            }
                        ?>
                            <div class="sq-movement <?= $movement['class']; ?>">
                                <div class="sq-movement-head">
                                    <div>
                                        <p class="sq-movement-title"><?= sq_h($movement['title']); ?></p>
                                    </div>
                                    <span class="sq-badge <?= $movement['class'] === 'current' ? 'success' : 'muted'; ?>"><?= sq_text($item['name_type_movement']); ?></span>
                                </div>
                                <dl class="sq-movement-details">
                                    <div><dt>Ambiente</dt><dd><?= sq_text($item['name_environment']); ?></dd></div>
                                    <div><dt>Piso</dt><dd><?= sq_text($item['floor_environment']); ?></dd></div>
                                    <div><dt>Sede</dt><dd><?= sq_text($item['name_campus']); ?></dd></div>
                                    <div><dt><?= !empty($item['date_event_movement_asset']) ? 'Fecha del movimiento' : 'Fecha de registro'; ?></dt><dd><?= sq_date($item['date_event_movement_asset'] ?: $item['date_created_movement_asset']); ?></dd></div>
                                    <div class="sq-movement-notes"><dt>Observaciones</dt><dd><?= nl2br(sq_text($item['observations_movement_asset'], 'Sin observaciones.')); ?></dd></div>
                                </dl>
                            </div>
                        <?php } ?>
                    </div>
                </article>

                <article class="sq-card sq-span-12">
                    <div class="sq-card-title">
                        <div class="sq-section-title"><div><h2>Últimas incidencias</h2><p>Reportes recientes vinculados directamente al activo.</p></div></div>
                    </div>
                    <div class="table-responsive">
                        <table class="table sq-table">
                            <thead><tr><th>#</th><th>Tipo</th><th>Título</th><th>Prioridad</th><th>Estado</th><th>Fecha</th><th>Acción</th></tr></thead>
                            <tbody>
                                <?php if (empty($context['recent_incidents'])) { ?>
                                    <tr><td colspan="7"><div class="sq-empty">Sin incidencias relacionadas.</div></td></tr>
                                <?php } else { foreach ($context['recent_incidents'] as $incident) { ?>
                                    <tr>
                                        <td><?= (int)$incident['id_incident']; ?></td>
                                        <td><?= sq_text($incident['name_type_incident']); ?></td>
                                        <td><?= sq_text($incident['title_incident']); ?></td>
                                        <td><span class="sq-badge warning"><?= sq_text($incident['name_priority']); ?></span></td>
                                        <td><?= ((int)$incident['status_open_incident'] === 1) ? '<span class="sq-badge danger">Abierta</span>' : '<span class="sq-badge success">Cerrada</span>'; ?></td>
                                        <td><?= sq_date($incident['date_incident_incident'] ?: $incident['date_created_incident'], true); ?></td>
                                        <td><a class="btn btn-sm btn-secondary" href="<?= base_url(); ?>/help_desk/incidents/incident_details/<?= (int)$incident['id_incident']; ?>" target="_blank" rel="noopener"> Ver</a></td>
                                    </tr>
                                <?php }} ?>
                            </tbody>
                        </table>
                    </div>
                </article>

                <article class="sq-card sq-span-5">
                    <div class="sq-card-title">
                        <div class="sq-section-title"><div><h2>Próximo mantenimiento preventivo</h2><p>Programación vigente del activo.</p></div></div>
                    </div>
                    <?php $schedule = $context['preventive_schedule'] ?? null; ?>
                    <?php if (!$schedule) { ?>
                        <div class="sq-empty">Sin mantenimiento preventivo programado.</div>
                    <?php } else { ?>
                        <div class="sq-kpi-grid sq-schedule-grid">
                            <div class="sq-kpi"><span class="sq-label">Fecha programada</span><span class="sq-value"><?= sq_date($schedule['date_scheduled_maintenance_preventive']); ?></span></div>
                            <div class="sq-kpi"><span class="sq-label">Próxima fecha</span><span class="sq-value"><?= sq_date($schedule['date_next_maintenance_preventive']); ?></span></div>
                            <div class="sq-kpi"><span class="sq-label">Frecuencia</span><span class="sq-value"><?= sq_text($schedule['name_frequency_maintenance']); ?></span></div>
                            <div class="sq-kpi"><span class="sq-label">Responsable</span><span class="sq-value"><?= sq_text($schedule['name_type_responsible_maintenance']); ?></span></div>
                        </div>
                        <span class="sq-label mt-3">Detalle</span>
                        <div class="sq-description-box mb-3"><?= nl2br(sq_text($schedule['description_maintenance_preventive'], 'Sin descripción.')); ?></div>
                        <span class="sq-label">Archivos del cronograma</span>
                        <?php sq_render_files($context['preventive_schedule_attachments'] ?? [], 'Sin archivos del mantenimiento programado.'); ?>
                    <?php } ?>
                </article>

                <article class="sq-card sq-span-7">
                    <div class="sq-card-title">
                        <div class="sq-section-title"><div><h2>Últimos mantenimientos preventivos</h2><p>Ejecuciones recientes y sus evidencias.</p></div></div>
                    </div>
                    <div class="sq-timeline">
                        <?php if (empty($context['preventive_history'])) { ?>
                            <div class="sq-empty">Sin historial preventivo realizado.</div>
                        <?php } else { foreach ($context['preventive_history'] as $history) { ?>
                            <div class="sq-timeline-item">
                                <div class="sq-timeline-head">
                                    <div>
                                        <p class="sq-timeline-title"><?= sq_text($history['name_status_maintenance']); ?> · <?= sq_date($history['date_performed_history_maintenance_preventive']); ?></p>
                                        <div class="sq-meta"><span><?= sq_text($history['name_frequency_maintenance']); ?></span><span><?= sq_text($history['name_type_responsible_maintenance']); ?></span></div>
                                    </div>
                                    <span class="sq-badge info">Preventivo</span>
                                </div>
                                <div class="small mb-2"><strong>Acciones:</strong> <?= nl2br(sq_text($history['action_history_maintenance_preventive'], 'Sin acciones registradas.')); ?></div>
                                <details><summary class="small fw-bold">Ver archivos adjuntos</summary><div class="mt-2"><?php sq_render_files($history['attachments'] ?? [], 'Sin evidencias adjuntas.'); ?></div></details>
                            </div>
                        <?php }} ?>
                    </div>
                </article>

                <article class="sq-card sq-span-12">
                    <div class="sq-card-title">
                        <div class="sq-section-title"><div><h2>Últimos mantenimientos correctivos</h2><p>Historial reciente de reparaciones, acciones realizadas y repuestos.</p></div></div>
                    </div>
                    <div class="sq-timeline">
                        <?php if (empty($context['corrective_history'])) { ?>
                            <div class="sq-empty">Sin mantenimientos correctivos relacionados.</div>
                        <?php } else { foreach ($context['corrective_history'] as $corrective) { ?>
                            <div class="sq-timeline-item">
                                <div class="sq-timeline-head">
                                    <div>
                                        <p class="sq-timeline-title"><?= sq_text($corrective['name_maintenance_corrective_origin']); ?></p>
                                        <div class="sq-meta">
                                            <span> <?= sq_date($corrective['date_repair_maintenance_corrective'] ?: $corrective['date_created_maintenance_corrective']); ?></span>
                                            <span> <?= sq_text($corrective['time_repair_maintenance_corrective']); ?></span>
                                            <span> <?= sq_text($corrective['name_type_responsible_maintenance']); ?></span>
                                            <?php if (!empty($corrective['id_incident_maintenance_corrective'])) { ?><span> Incidencia #<?= (int)$corrective['id_incident_maintenance_corrective']; ?></span><?php } ?>
                                        </div>
                                    </div>
                                    <?= ((int)$corrective['status_active_maintenance_corrective'] === 1) ? '<span class="sq-badge warning">Pendiente</span>' : '<span class="sq-badge success">Finalizado</span>'; ?>
                                </div>
                                <div class="small mb-2"><strong>Acción realizada:</strong> <?= nl2br(sq_text($corrective['action_maintenance_corrective'], 'Sin acción registrada.')); ?></div>
                                <?php if (!empty($corrective['spare_parts'])) { ?>
                                    <div class="sq-meta mb-2"><strong>Repuestos:</strong>
                                        <?php foreach ($corrective['spare_parts'] as $spare) { ?>
                                            <span><?= sq_text($spare['name_spare_part']); ?> (<?= sq_text($spare['quantity_maintenance_corrective_spare_part']); ?> <?= sq_text($spare['unit_spare_part']); ?>)</span>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                                <details><summary class="small fw-bold">Ver archivos adjuntos</summary><div class="mt-2"><?php sq_render_files($corrective['attachments'] ?? [], 'Sin archivos adjuntos.'); ?></div></details>
                            </div>
                        <?php }} ?>
                    </div>
                </article>
            </section>
        <?php } ?>

        <?php require('Layouts/footer.php'); ?>
    </div>
</main>

<?php require('Layouts/script.php'); ?>

</body>
</html>
