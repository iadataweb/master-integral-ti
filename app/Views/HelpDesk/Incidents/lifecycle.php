<?php $lifecycle = $data['incident_lifecycle']; ?>
<?php if ($lifecycle['can_close'] || $lifecycle['can_reopen']) { ?>
<div id="incident-lifecycle" class="d-flex justify-content-end gap-2 mb-4">
    <?php if ($lifecycle['can_close']) { ?>
    <form id="formIncidentClose" class="<?= $lifecycle['is_open'] ? '' : 'd-none' ?>">
        <input type="hidden" name="id_incident" value="<?= (int) $data['specific_incident']['id_incident'] ?>">
        <button type="button" class="btn btn-secondary js-btn-close">Cerrar incidencia</button>
    </form>
    <?php } ?>
    <?php if ($lifecycle['can_reopen']) { ?>
    <form id="formIncidentReopen" class="<?= $lifecycle['is_open'] ? 'd-none' : '' ?>">
        <input type="hidden" name="id_incident" value="<?= (int) $data['specific_incident']['id_incident'] ?>">
        <button type="submit" class="btn btn-warning">Reabrir incidencia</button>
    </form>
    <?php } ?>
</div>
<?php } ?>
