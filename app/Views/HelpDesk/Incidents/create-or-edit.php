<div class="modal fade text-left" id="ModalCRUD" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel4"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="formIncident" enctype="multipart/form-data">
                    <h5 class="mb-3">Identificación</h5>
                    
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-title" class="form-label">Título</label>
                                <input type="text" id="input-title" class="form-control" placeholder="Título" name="title">
                            </div>
                        </div>                        
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="select-type-incident" class="form-label">Tipo de Incidencia</label>
                                <select class="form-select" id="select-type-incident" name="id_type_incident">
                                    <option selected disabled value="">Selecciona una opción</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="select-priority" class="form-label">Prioridad</label>
                                <select class="form-select" id="select-priority" name="id_priority">
                                    <option selected disabled value="">Selecciona una opción</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-3">Contexto</h5>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-group mandatory">
                                <label for="select-space" class="form-label">Ubicación</label>
                                <select class="form-select" id="select-space" name="id_space">
                                    <option selected disabled value="">Selecciona una opción</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group">
                                <label for="input-date-incident" class="form-label">Fecha de Incidencia (Opcional)</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
                                    </span>
                                    <input data-datepicker="" data-date-format="dd/mm/yyyy" class="form-control" id="input-date-incident" type="text" placeholder="dd/mm/yyyy" name="date_incident">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group">
                                <label for="input-time-incident" class="form-label">Hora de Incidencia (Opcional)</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-11a.75.75 0 00-1.5 0v3.25c0 .414.336.75.75.75h2.5a.75.75 0 000-1.5h-1.75V7z" clip-rule="evenodd"></path></svg>
                                    </span>
                                    <input class="form-control" id="input-time-incident" type="time" name="time_incident">
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-3">Descripción y evidencias</h5>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-group mandatory">
                                <label for="textarea-description" class="form-label">Descripción</label>
                                <textarea class="form-control" id="textarea-description" rows="6"
                                    placeholder="Ingrese Descripción" name="description"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-group">
                                <label for="company-column" class="form-label">Documentos
                                    Adicionales</label>
                                <div id="custom-btn-upload-table"></div>
                                <div class="table-responsive">
                                    <table id="table-handler" class="table mb-0 custom-table-handler">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>#</th>
                                                <th>Archivo</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>



                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Cerrar</span>
                </button>
                <button type="submit" form="formIncident" class="btn btn-secondary ms-1">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span id="btnSubmit" class="d-none d-sm-block"></span>
                </button>
            </div>
            
        </div>
    </div>
</div>
