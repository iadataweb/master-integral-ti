<div class="modal fade text-left" id="ModalCRUD" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel4"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="formUser">

                    <div class="custom-header-modal bg-secondary">
                        <div class="custom-header-title">
                            <h4 class="m-0">Empleado</h4>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="d-flex align-items-end gap-2">
                                <div class="form-group">
                                    <label for="input-dni-employee" class="form-label">DNI</label>
                                    <input type="text" id="input-dni-employee" class="form-control" placeholder="DNI" name="dni_employee">                                
                                </div>
                                <div>
                                    <button class="btn btn-info js-btn-search-employee" type="button">Buscar</button>
                                </div>     
                            </div>                                                                           
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-group">
                                <label for="input-official-name-employee" class="form-label">Nombre Oficial</label>
                                <input type="text" id="input-official-name-employee" class="form-control" name="official_name_employee" disabled readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group">
                                <label for="input-name-position" class="form-label">Cargo</label>
                                <input type="text" id="input-name-position" class="form-control" name="name_position" disabled readonly>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group">
                                <label for="input-name-area" class="form-label">Área</label>
                                <input type="text" id="input-name-area" class="form-control" name="name_area" disabled readonly>
                            </div>                            
                        </div>
                    </div>

                    <div class="custom-header-modal bg-secondary">
                        <div class="custom-header-title">
                            <h4 class="m-0">Usuario</h4>
                        </div>
                    </div>

                    <div class="row">                                                
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="input-name-user" class="form-label">Nombre de Usuario</label>
                                <input type="text" id="input-name-user" class="form-control"
                                    placeholder="Nombre de Usuario" name="name_user">
                            </div>                            
                        </div>

                        <div class="col-12 col-lg-6 mb-3">
                            <div id="groupPassword" class="form-group mandatory">
                                <label for="input-password-user" class="form-label">Contraseña</label>
                                <div class="input-group">
                                    <input type="password" id="input-password-user" class="form-control"
                                        placeholder="Contraseña" name="password_user" autocomplete="new-password" spellcheck="false">
                                    <button type="button" class="btn btn-outline-gray-600" id="btn-toggle-user-password" aria-label="Mostrar contraseña" aria-pressed="false" aria-controls="input-password-user">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/><path class="password-eye-slash" d="m3 3 18 18" hidden/></svg>
                                    </button>
                                    <button type="button" class="btn btn-outline-gray-600" id="btn-generate-user-password" aria-label="Generar contraseña segura" title="Generar contraseña segura">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m4 20 12-12 4 4L8 24M14 10l4 4" transform="translate(0 -3)"/><path d="M5 3v4M3 5h4M19 2v4M17 4h4M19 17v4M17 19h4"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="select-role-user"
                                    class="form-label">Rol</label>
                                <select class="form-select" id="select-role-user" name="id_role_user">
                                    <option selected disabled value="">Selecciona una opción</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-group mandatory">
                                <label for="select-status-active-user"
                                    class="form-label">Estado</label>
                                <select class="form-select" id="select-status-active-user" name="status_active_user">
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
                <button type="submit" form="formUser" class="btn btn-secondary ms-1">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span id="btnSubmit" class="d-none d-sm-block"></span>
                </button>
            </div>
            
        </div>
    </div>
</div>