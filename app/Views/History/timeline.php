<!DOCTYPE html>
<html lang="es">

<?php require('Layouts/head.php'); ?>

<?php
$asset = $data['asset'] ?? [];
$events = $data['events'] ?? [];

function tl_h($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function tl_text($value, string $fallback = 'No registrado'): string
{
    $value = trim((string)($value ?? ''));
    return $value !== '' ? tl_h($value) : $fallback;
}

function tl_date($value, bool $withTime = true): string
{
    if (empty($value)) {
        return 'No registrado';
    }
    $time = strtotime((string)$value);
    return $time ? date($withTime ? 'd/m/Y H:i' : 'd/m/Y', $time) : tl_h($value);
}

function tl_file_url(array $file): string
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

function tl_icon(string $type): string
{
    $icons = [
        'inventory' => '<path d="m12 3 9 5v8l-9 5-9-5V8l9-5Z M3 8l9 5 9-5 M12 13v8 M7 5.8l9 5"/>',
        'movement' => '<path d="M3 7h17l-4-4 M21 17H4l4 4 M20 7l-4 4 M4 17l4-4"/>',
        'incident' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v6 M12 16h.01"/>',
        'preventive' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4 M8 3v4 M3 11h18 m-13 5 3 3 5-5"/>',
        'preventive_done' => '<path d="m12 3 8 3v6c0 5-8 9-8 9s-8-4-8-9V6l8-3Z m-4 9 3 3 5-6"/>',
        'corrective' => '<path d="M14 6a5 5 0 0 0-6 6L3 17a3 3 0 0 0 4 4l5-5a5 5 0 0 0 6-6l-3 3-4-4 3-3Z"/>',
        'corrective_done' => '<path d="m3 12 5 5L18 7 M13 17l8-8"/>',
    ];
    $shape = $icons[$type] ?? '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>';
    return '<svg aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">'.$shape.'</svg>';
}

function tl_label(string $type): string
{
    $labels = [
        'inventory' => 'Inventario',
        'movement' => 'Movimiento',
        'incident' => 'Incidencia',
        'preventive' => 'Preventivo',
        'preventive_done' => 'Preventivo realizado',
        'corrective' => 'Correctivo',
        'corrective_done' => 'Correctivo realizado',
    ];
    return $labels[$type] ?? 'Evento';
}

function tl_render_files(array $files): void
{
    if (empty($files)) {
        echo '<div class="tl-empty compact">Sin archivos adjuntos.</div>';
        return;
    }
    echo '<div class="tl-files">';
    foreach ($files as $file) {
        $type = isset($file['type']) ? '<span>' . tl_h($file['type'] === 'before' ? 'Antes' : ($file['type'] === 'after' ? 'Despues' : $file['type'])) . '</span>' : '';
        echo '<a href="' . tl_h(tl_file_url($file)) . '" target="_blank" rel="noopener" class="tl-file"><i class="bx bx-paperclip"></i><strong>' . tl_text($file['name'] ?? 'Archivo') . '</strong>' . $type . '<em>Abrir</em></a>';
    }
    echo '</div>';
}
?>

<body>
    <?php require('Layouts/sidebar.php'); ?>

    <main class="content">
        <?php require('Layouts/header.php'); ?>

        <link rel="stylesheet" href="<?= assets(); ?>/css/components/history-timeline.css?v=<?= filemtime(dirname(__DIR__, 2) . '/Assets/css/components/history-timeline.css'); ?>">

        <section class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4 page-heading-actions">
            <div class="d-block mb-4 mb-md-0">
                <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                    <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                        <li class="breadcrumb-item"><span><i class="bx bx-home-alt"></i></span></li>
                        <li class="breadcrumb-item"><span>Historial</span></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= tl_h($asset['code_inventory_asset']); ?></li>
                    </ol>
                </nav>
                <h1 class="h4"><?= tl_h($asset['code_inventory_asset']); ?></h1>
                <p class="mb-0"><?= tl_h($asset['name_type_asset']); ?> - <?= tl_h($asset['name_category_asset']); ?> - <?= tl_h($asset['name_brand']); ?></p>
            </div>
            <div class="btn-toolbar mb-2 mb-md-0 page-action-toolbar">
                <a href="<?= base_url(); ?>/history" class="btn btn-sm btn-outline-secondary"><i class="bx bx-arrow-back me-1"></i> Volver</a>
            </div>
        </section>

        <section class="tl-summary" aria-label="Resumen del activo">
            <div class="tl-summary-card"><span class="tl-label">Modelo</span><div class="tl-value"><?= tl_text($asset['model_asset']); ?></div></div>
            <div class="tl-summary-card"><span class="tl-label">Serie</span><div class="tl-value"><?= tl_text($asset['serial_number_asset']); ?></div></div>
            <div class="tl-summary-card"><span class="tl-label">Condición</span><div class="tl-value"><?= tl_text($asset['name_condition_asset']); ?></div></div>
            <div class="tl-summary-card"><span class="tl-label">Eventos</span><div class="tl-value"><?= count($events); ?> registros</div></div>
        </section>

        <?php if (empty($events)) { ?>
            <div class="tl-empty">No hay eventos registrados para este activo.</div>
        <?php } else { ?>
            <section class="tl-board" aria-label="Línea de tiempo del activo">
                <?php foreach ($events as $event) { ?>
                    <article class="tl-event">
                        <div class="tl-event-head">
                            <div class="tl-icon"><?= tl_icon($event['type']); ?></div>
                            <div>
                                <span class="tl-pill"><?= tl_label($event['type']); ?></span>
                                <h2 class="h5 tl-title"><?= tl_h($event['title']); ?></h2>
                                <p class="tl-subtitle"><?= tl_text($event['subtitle']); ?></p>
                            </div>
                            <div class="tl-date"><?= tl_date($event['date']); ?></div>
                        </div>

                        <?php if (!empty($event['description'])) { ?>
                            <div class="tl-description"><?= nl2br(tl_text($event['description'])); ?></div>
                        <?php } ?>

                        <details class="tl-details">
                            <summary>Ver información detallada</summary>
                            <div class="tl-detail-grid">
                                <?php foreach (($event['details'] ?? []) as $label => $value) { ?>
                                    <div class="tl-detail">
                                        <strong><?= tl_h($label); ?></strong>
                                        <div><?= nl2br(tl_text($value)); ?></div>
                                    </div>
                                <?php } ?>
                            </div>
                            <?php if (!empty($event['url'])) { ?>
                                <div class="mt-3"><a class="btn btn-sm btn-secondary" href="<?= tl_h($event['url']); ?>" target="_blank" rel="noopener"><i class="bx bx-link-external me-1"></i> Abrir registro</a></div>
                            <?php } ?>
                            <div class="mt-3">
                                <span class="tl-label">Archivos adjuntos</span>
                                <?php tl_render_files($event['attachments'] ?? []); ?>
                            </div>
                        </details>
                    </article>
                <?php } ?>
            </section>
        <?php } ?>

        <?php require('Layouts/footer.php'); ?>
    </main>

    <?php require('Layouts/script.php'); ?>
</body>
</html>

