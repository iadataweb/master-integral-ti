<div class="modal fade" id="ModalIncidentResolution" tabindex="-1" aria-labelledby="incident-resolution-title" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg"><div class="modal-content">
  <div class="modal-header"><h4 class="modal-title" id="incident-resolution-title">Cierre de incidencia</h4><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
  <div class="modal-body">
   <form id="formManualClosedIncident">
    <input type="hidden" name="id_incident" value="<?= (int)$incident['id_incident'] ?>">
    <div class="row">
     <div class="col-12 col-md-6 mb-3"><label for="input-date-closed-incident" class="form-label">Fecha de resolución (opcional)</label><input type="date" id="input-date-closed-incident" name="date_closed_incident" class="form-control"></div>
     <div class="col-12 col-md-6 mb-3"><label for="input-time-closed-incident" class="form-label">Hora de resolución (opcional)</label><input type="time" id="input-time-closed-incident" name="time_closed_incident" class="form-control"></div>
    </div>
   </form>
  </div>
  <div class="modal-footer"><button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button><button type="submit" form="formManualClosedIncident" class="btn btn-secondary">Guardar</button></div>
 </div></div>
</div>
