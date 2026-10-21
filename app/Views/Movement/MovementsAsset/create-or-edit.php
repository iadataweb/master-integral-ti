<div class="modal fade text-left" id="ModalCRUD" tabindex="-1" role="dialog" aria-labelledby="movementCrudTitle"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <link rel="stylesheet" href="<?= assets(); ?>/css/components/movement-movementsasset-create-or-edit.css?v=<?= filemtime(dirname(__DIR__, 3) . '/Assets/css/components/movement-movementsasset-create-or-edit.css'); ?>">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id="movementCrudTitle"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <form id="formMovementAsset">

                    <ol class="movement-steps" aria-label="Pasos del movimiento"><li data-step="0">Activo</li><li data-step="1">Operación</li><li data-step="2">Destino</li><li data-step="3">Detalles</li><li data-step="4">Confirmar</li></ol>
                    <input type="hidden" name="movement_token" value="<?= htmlspecialchars($_SESSION['movement_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
<section class="movement-step" data-step="0">                    <div class="custom-header-modal bg-secondary">
                        <div class="custom-header-title">
                            <h4 class="m-0">Activo</h4>
                        </div>
                        <div class="custom-header-btn">
                            <button type="button" class="btn btn-info js-btn-search-asset">Buscar</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group">
                                <label for="input-code-inventory-asset" class="form-label">Codigo de Inventario</label>
                                <input type="text" id="input-code-inventory-asset" class="form-control" name="code_inventory_asset" disabled readonly>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group">
                                <label for="input-name-condition-asset" class="form-label">Condicion de Activo</label>
                                <input type="text" id="input-name-condition-asset" class="form-control" name="name_condition_asset" disabled readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group">
                                <label for="input-name-type-asset" class="form-label">Tipo de Activo</label>
                                <input type="text" id="input-name-type-asset" class="form-control" name="name_type_asset" disabled readonly>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group">
                                <label for="input-name-category-asset" class="form-label">Categoria de Activo</label>
                                <input type="text" id="input-name-category-asset" class="form-control" name="name_category_asset" disabled readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group">
                                <label for="input-name-brand" class="form-label">Marca de Activo</label>
                                <input type="text" id="input-name-brand" class="form-control" name="name_brand" disabled readonly>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group">
                                <label for="textarea-descriptions-asset" class="form-label">Descripcion del Activo</label>
                                <textarea class="form-control" id="textarea-descriptions-asset" rows="3"
                                    placeholder="Ingrese Descripciones" name="descriptions_asset" disabled readonly></textarea>
                            </div>
                        </div>
                    </div>

<div class="movement-origin-summary" id="movement-origin-summary" role="status">Selecciona un activo para consultar su situación actual.</div></section>
<section class="movement-step d-none" data-step="1">                    <div class="custom-header-modal bg-secondary">
                        <div class="custom-header-title">
                            <h4 class="m-0">Movimiento</h4>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-group mandatory">
                                <label for="select-type-movement" class="form-label">Tipo de Movimiento</label>
                                <select class="form-select" id="select-type-movement" name="id_type_movement">
                                    <option selected disabled value="">Selecciona una opcion</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="alert alert-info mb-0" id="movement-type-helper">
                                Seleccione el tipo de movimiento para ver los datos requeridos.
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group">
                                <label for="input-date-event-movement-asset" class="form-label" id="label-date-event-movement-asset">Fecha del evento (opcional)</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
                                    </span>
                                    <input data-datepicker="" data-date-format="dd/mm/yyyy" class="form-control" id="input-date-event-movement-asset" type="text" placeholder="dd/mm/yyyy" name="date_event_movement_asset">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group">
                                <label for="input-time-event-movement-asset" class="form-label">Hora del evento (opcional)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-time"></i></span>
                                    <input class="form-control" id="input-time-event-movement-asset" type="time" name="time_event_movement_asset">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3 movement-field movement-field-return-date">
                            <div class="form-group">
                                <label for="input-expected-return-date-movement-asset" class="form-label">Fecha Esperada de Devolucion</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
                                    </span>
                                    <input data-datepicker="" class="form-control" id="input-expected-return-date-movement-asset" type="text" placeholder="dd/mm/yyyy" name="expected_return_date_movement_asset">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3 movement-field movement-field-manual-responsible">
                            <div class="form-group">
                                <label for="input-manual-responsible-movement-asset" class="form-label">Responsable que recibe</label>
                                <input type="text" id="input-manual-responsible-movement-asset" class="form-control" name="manual_responsible_movement_asset" maxlength="160" placeholder="Ingrese nombre de quien recibe el equipo">
                                <small class="text-muted">Opcional. Indica el nombre de la persona que recibe el equipo.</small>
                            </div>
                        </div>
                        <input type="hidden" id="input-status-process-movement-asset" name="status_process_movement_asset" value="Registrado">
                    </div>

                    <div class="movement-loan-section d-none">
                        <div class="custom-header-modal bg-secondary">
                            <div class="custom-header-title">
                                <h4 class="m-0">Datos del Prestamo</h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-lg-6 mb-3">
                                <div class="form-group mandatory">
                                    <label for="select-loan-recipient-type" class="form-label">Tipo de Prestamo</label>
                                    <select class="form-select" id="select-loan-recipient-type" name="loan_recipient_type_movement_asset">
                                        <option selected disabled value="">Selecciona una opcion</option>
                                        <option value="internal">Personal interno</option>
                                        <option value="external_person">Persona externa</option>
                                        <option value="external_company">Empresa o institucion externa</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6 mb-3">
                                <div class="form-group">
                                    <label for="input-loan-recipient-name" class="form-label">Persona, Empresa o Institucion</label>
                                    <input type="text" id="input-loan-recipient-name" class="form-control" name="loan_recipient_name_movement_asset" placeholder="Ingrese nombre o razon social">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-lg-6 mb-3">
                                <div class="form-group">
                                    <label for="input-loan-recipient-document" class="form-label">Documento o Referencia</label>
                                    <input type="text" id="input-loan-recipient-document" class="form-control" name="loan_recipient_document_movement_asset" placeholder="DNI, RUC, acta, correo u otra referencia">
                                </div>
                            </div>
                            <div class="col-12 col-lg-6 mb-3">
                                <div class="form-check mt-lg-4 pt-lg-2">
                                    <input class="form-check-input" type="checkbox" value="1" id="check-loan-return-indefinite" name="loan_return_indefinite_movement_asset">
                                    <label class="form-check-label" for="check-loan-return-indefinite">
                                        Devolucion indeterminada
                                    </label>
                                </div>
                                <small class="text-muted">Mantiene el prestamo activo hasta registrar el retorno.</small>
                            </div>
                        </div>
                    </div>

</section>
<section class="movement-step d-none" data-step="2">                    <div class="custom-header-modal bg-secondary">
                        <div class="custom-header-title">
                            <h4 class="m-0" id="space-section-title">Ubicacion / Ambiente</h4>
                        </div>
                        <div class="custom-header-btn">
                            <button type="button" class="btn btn-info js-btn-search-space">Buscar</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="alert alert-info mb-0" id="space-flow-helper">
                                Seleccione la ubicacion interna, ambiente o almacen donde quedara registrado el activo.
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-name-space" class="form-label" id="label-name-space">Ambiente / Almacen</label>
                                <input type="text" id="input-name-space" class="form-control" name="name_space" disabled readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-name-campus" class="form-label" id="label-name-campus">Establecimiento</label>
                                <input type="text" id="input-name-campus" class="form-control" name="name_campus" disabled readonly>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-floor-space" class="form-label">Piso</label>
                                <input type="text" id="input-floor-space" class="form-control" name="floor_space" disabled readonly>
                            </div>
                        </div>
                    </div>

<details class="movement-optional-plan"><summary>Ubicar en plano (opcional)</summary>                    <div class="custom-header-modal bg-secondary">
                        <div class="custom-header-title">
                            <h4 class="m-0">Ubicacion en Plano</h4>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="alert alert-info mb-0">
                                Opcional. Seleccione un plano y haga clic sobre el punto exacto donde se ubica el activo.
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-8 mb-3">
                            <div class="form-group">
                                <label for="select-floor-plan-movement-asset" class="form-label">Plano institucional</label>
                                <select class="form-select" id="select-floor-plan-movement-asset" name="id_floor_plan_movement_asset">
                                    <option value="">Sin plano seleccionado</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4 mb-3 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-secondary w-100" id="btn-clear-plan-location">
                                <i class="bx bx-eraser me-1"></i> Limpiar ubicacion
                            </button>
                        </div>
                    </div>

                    <input type="hidden" id="input-coordinate-x-movement-asset" name="coordinate_x_movement_asset">
                    <input type="hidden" id="input-coordinate-y-movement-asset" name="coordinate_y_movement_asset">

                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="movement-plan-picker" id="movement-plan-picker">
                                <img id="img-movement-floor-plan" class="movement-plan-image" alt="Plano del movimiento">
                                <div class="movement-plan-overlay" id="movement-plan-overlay">
                                    <div class="movement-plan-marker d-none" id="movement-plan-marker">
                                        <i class="bx bx-map-pin"></i>
                                    </div>
                                </div>
                                <div class="movement-plan-empty" id="movement-plan-empty">Seleccione un plano para marcar la ubicacion del activo.</div>
                            </div>
                            <small class="text-muted" id="movement-plan-coordinate-label">Coordenada no definida.</small>
                        </div>
                    </div>

</details></section>
<section class="movement-step d-none" data-step="3">                    <div class="custom-header-modal bg-secondary">
                        <div class="custom-header-title">
                            <h4 class="m-0">Detalles y evidencias</h4>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Fotografias o Evidencias del Movimiento</label>
                            <div id="custom-btn-upload-table"></div>
                            <div class="table-responsive">
                                <table id="table-handler" class="table mb-0 custom-table-handler">
                                    <thead>
                                        <tr>
                                            <th style="width: 80px;">#</th>
                                            <th>Archivo</th>
                                            <th style="width: 140px;">Accion</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-group mandatory">
                                <label for="textarea-observations-movement-asset" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="textarea-observations-movement-asset" rows="4"
                                    placeholder="Ingrese Observaciones" name="observations_movement_asset"></textarea>
                            </div>
                        </div>
                    </div>

<div id="movement-correction-group" class="d-none"><label for="movement-correction-reason" class="form-label">Motivo de la corrección</label><textarea id="movement-correction-reason" name="correction_reason" class="form-control" minlength="5" rows="2"></textarea><p class="small text-muted">Se conservarán los datos anteriores y las evidencias.</p></div></section>
<section class="movement-step d-none" data-step="4"><h5>Revisar movimiento</h5><div id="movement-review" class="movement-review"></div><p class="small text-muted mt-3">Registrar crea un nuevo evento y conserva el historial del activo.</p></section>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Cerrar</span>
                </button>
                <button type="button" class="btn btn-secondary" id="movement-prev">Anterior</button>
                <button type="button" class="btn btn-primary" id="movement-next">Continuar</button>
                <button type="submit" id="movement-save" form="formMovementAsset" class="btn btn-primary ms-1">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span id="btnSubmit" class="d-none d-sm-block"></span>
                </button>
            </div>

        </div>
    </div>
</div>
