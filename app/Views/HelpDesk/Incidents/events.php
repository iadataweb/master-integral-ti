<section class="card mb-4" aria-labelledby="incident-tracking">
 <div class="card-header"><h4 class="card-title m-0" id="incident-tracking">Seguimiento</h4></div>
 <div class="card-body">
 <div class="table-responsive"><table class="table table-hover align-middle mb-0 ui-record-history-table">
  <colgroup><col style="width:8%"><col style="width:16%"><col style="width:20%"><col style="width:24%"><col style="width:32%"></colgroup>
  <thead><tr><th>#</th><th>Evento</th><th>Fecha y hora</th><th>Usuario</th><th>Detalle</th></tr></thead>
  <tbody>
  <?php
   $eventLabels=['created'=>'Creada','closed'=>'Cerrada','reopened'=>'Reabierta','derived'=>'Derivada a correctivo','cancelled'=>'Derivación cancelada','accepted'=>'Derivación aceptada','rejected'=>'Derivación rechazada'];
   foreach ($data['incident_events'] as $event) {
    $escapeEvent = static fn($value) => htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
  ?>
   <tr><td><?= $escapeEvent($event['event_id']) ?></td><td><?= $escapeEvent($eventLabels[$event['event_type']] ?? $event['event_type']) ?></td>
    <td><?= $escapeEvent($event['date_created']) ?></td><td><?= $escapeEvent($event['actor_name']) ?></td>
    <td><span class="text-break"><?= nl2br($escapeEvent($event['solution_snapshot'] ?? '')) ?></span>
     <?php if ($event['resolution_date'] || $event['resolution_time']) { ?><div class="text-muted small">Resolución: <?= $escapeEvent(($event['resolution_date'] ?? 'Sin fecha').' '.($event['resolution_time'] ?? 'Sin hora')) ?></div><?php } ?>
    </td></tr>
  <?php } ?>
  <?php if (empty($data['incident_events'])) { ?><tr><td colspan="5" class="text-center text-muted">Sin registros.</td></tr><?php } ?>
  </tbody>
 </table></div>
 </div>
</section>
