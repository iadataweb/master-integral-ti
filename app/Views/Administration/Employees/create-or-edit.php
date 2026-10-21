<div class="modal fade text-left" id="ModalCRUD" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel4"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="formEmployee">
                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-first-name-employee" class="form-label">Primer Nombre</label>
                                <input type="text" id="input-first-name-employee" class="form-control"
                                    placeholder="Primer Nombre" name="first_name_employee">
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group">
                                <label for="input-middle-name-employee" class="form-label">Segundo Nombre <small class="text-muted">(opcional)</small></label>
                                <input type="text" id="input-middle-name-employee" class="form-control"
                                    placeholder="Segundo Nombre" name="middle_name_employee">
                            </div>                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-paternal-last-name-employee" class="form-label">Apellido Paterno</label>
                                <input type="text" id="input-paternal-last-name-employee" class="form-control"
                                    placeholder="Apellido Paterno" name="paternal_last_name_employee">
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-maternal-last-name-employee" class="form-label">Apellido Materno</label>
                                <input type="text" id="input-maternal-last-name-employee" class="form-control"
                                    placeholder="Apellido Materno" name="maternal_last_name_employee">
                            </div>                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="select-preferred-name-employee"
                                    class="form-label">Nombre Preferido</label>
                                <select class="form-select" id="select-preferred-name-employee" name="preferred_name_employee">
                                    <option selected disabled value="">Selecciona una opción</option>
                                    <option value="1">Primer Nombre</option>
                                    <option value="2">Segundo Nombre</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-dni-employee" class="form-label">DNI</label>
                                <input type="text" id="input-dni-employee" class="form-control"
                                    placeholder="DNI" name="dni_employee">
                            </div>                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-cell-phone-employee" class="form-label">Número de Celular</label>
                                <input type="text" id="input-cell-phone-employee" class="form-control"
                                    placeholder="Número de Celular" name="cell_phone_employee">
                            </div>                            
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group">
                                <label for="input-email-employee" class="form-label">Correo Electrónico <small class="text-muted">(opcional)</small></label>
                                <input type="email" id="input-email-employee" class="form-control"
                                    placeholder="Correo Electrónico" name="email_employee">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="select-area"
                                    class="form-label">Área</label>
                                <select class="form-select" id="select-area" name="id_area">
                                    <option selected disabled value="">Selecciona una opción</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="select-position"
                                    class="form-label">Cargo</label>
                                <select class="form-select" id="select-position" name="id_position">
                                    <option selected disabled value="">Selecciona una opción</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="select-professional-title"
                                    class="form-label">Título Profesional</label>
                                <select class="form-select" id="select-professional-title" name="id_professional_title">
                                    <option selected disabled value="">Selecciona una opción</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="select-status-active-employee"
                                    class="form-label">Estado</label>
                                <select class="form-select" id="select-status-active-employee" name="status_active_employee">
                                    <option selected disabled value="">Selecciona una opción</option>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
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
                <button type="submit" form="formEmployee" class="btn btn-secondary ms-1">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span id="btnSubmit" class="d-none d-sm-block"></span>
                </button>
            </div>
            
        </div>
    </div>
</div>