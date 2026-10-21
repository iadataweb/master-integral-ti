document.addEventListener('DOMContentLoaded', function () {

    let form = $("#formUser");
    let base_url = document.querySelector('meta[name="base-url"]').getAttribute('content');
    let tablaDataTable;
    let url;
    let url_submit;
    let url_read;
    let url_search;
    let url_delete;
    let data_array = [];
    var id_user;
    var dni_employee;
    var modal;

    const passwordInput = document.getElementById('input-password-user');
    const togglePassword = document.getElementById('btn-toggle-user-password');
    const generatePassword = document.getElementById('btn-generate-user-password');
    function setPasswordVisible(visible) {
        passwordInput.type = visible ? 'text' : 'password';
        togglePassword.setAttribute('aria-label', visible ? 'Ocultar contraseña' : 'Mostrar contraseña');
        togglePassword.setAttribute('aria-pressed', String(visible));
        togglePassword.querySelector('.password-eye-slash').style.display = visible ? 'block' : 'none';
        togglePassword.querySelector('.password-eye-slash').removeAttribute('hidden');
    }
    togglePassword.addEventListener('click', function () {
        setPasswordVisible(passwordInput.type === 'password');
    });
    // Muestreo sin sesgo y barajado con el generador criptográfico del navegador.
    function secureIndex(limit) {
        const values = new Uint32Array(1);
        const ceiling = Math.floor(4294967296 / limit) * limit;
        do { window.crypto.getRandomValues(values); } while (values[0] >= ceiling);
        return values[0] % limit;
    }
    generatePassword.addEventListener('click', function () {
        if (!window.crypto || !window.crypto.getRandomValues) {
            showMessage('error', 'El navegador no permite generar una contraseña segura.');
            return;
        }
        const groups = ['ABCDEFGHJKLMNPQRSTUVWXYZ', 'abcdefghijkmnopqrstuvwxyz', '23456789', '!@#$%&*+-=?'];
        const alphabet = groups.join('');
        const chars = groups.map(group => group[secureIndex(group.length)]);
        while (chars.length < 20) chars.push(alphabet[secureIndex(alphabet.length)]);
        for (let index = chars.length - 1; index > 0; index--) {
            const swap = secureIndex(index + 1);
            [chars[index], chars[swap]] = [chars[swap], chars[index]];
        }
        passwordInput.value = chars.join('');
        passwordInput.dispatchEvent(new Event('input', {bubbles: true}));
    });
    form.on('reset', function () { setPasswordVisible(false); });
    $('#ModalCRUD').on('hidden.bs.modal', function () {
        passwordInput.value = '';
        setPasswordVisible(false);
    });
    setPasswordVisible(false);

    url = base_url + "/administration/users/show_users";

    tablaDataTable = $('#tableManageUsers').DataTable({
        "aProcessing":true,
        "aServerSide":true,
        "ajax": {
            "url": url,
            "dataSrc":""
        },
        "language": languageDatatable(),
        "columns": [
            {"data":"id_user"},
            {"data":"display_name"},
            {"data":"name_user"},
            {"data":"name_role"},
            {"data":"status_active_user"},
            {"data":"date_created_user"},
            {"data":"options"}
        ],
        "responsive":"true",
        "destroy": true,
        "idisplaylength": 10,
        "order":[[0,"desc"]] 
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA LLENAR SELECT ROLE CON EL RESULTADO
    --------------------------------------------------------------*/
    $.post(base_url + "/administration/roles/combo_roles", function(data) {
        $('#select-role-user').html(data);
        $('#select-role-user option[value="1"]').prop('disabled', true);
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA MOSTRAR MODAL DE AGREGAR NUEVO REGISTRO
    --------------------------------------------------------------*/
    $("#btnNew").click(function () {
        url_submit = base_url + '/administration/users/insert_user';
        data_array = [];
        form.trigger("reset");
        $('#select-role-user option').prop('disabled', false);
        $('#select-role-user option[value="1"]').prop('disabled', true);
        $('#select-status-active-user option').prop('disabled', false);
        $("#groupPassword").addClass("mandatory");
        $(".modal-title").text("Nuevo Registro");
        $("#btnSubmit").text("Guardar");
        $("#ModalCRUD").modal("show");
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA MOSTRAR MODAL DE REGISTRO
    --------------------------------------------------------------*/
    $(document).on("click", ".js-btn-view", function () {
        id_user = $(this).data('id');
        modal = "view";
        show_data(id_user, modal);
        $(".modal-title").text("Datos del Registro");
        $("#ModalSee").modal("show");
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA BUSCAR EMPLEADO
    --------------------------------------------------------------*/
    $(document).on("click", ".js-btn-search-employee", function () {
        dni_employee = $("#input-dni-employee").val();
        url_search = base_url + '/administration/employees/search_employee';
        $.ajax({
            url: url_search,
            type: "POST",
            dataType: "json",
            data: {dni_employee:dni_employee},
            success: function (response) {
                if (response.status === false) {
                    showMessage(response.type, response.message);
                } else {
                    data_array.id_employee = response.result.id_employee;
                    $("#input-official-name-employee").val(response.result.official_name_employee);
                    $("#input-name-position").val(response.result.name_position);
                    $("#input-name-area").val(response.result.name_area);           
                }
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);
                showMessage("error", "Ocurrió un problema al procesar la solicitud.");
            }
        });
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA MOSTRAR MODAL DE EDITAR
    --------------------------------------------------------------*/
    $(document).on("click", ".js-btn-edit", function () {
        url_submit = base_url + '/administration/users/update_user';
        data_array = [];
        form.trigger("reset");
        id_user = $(this).data('id');
        modal = "edit";
        show_data(id_user, modal);
        $("#groupPassword").removeClass("mandatory");
        $(".modal-title").text("Actualizar Registro");
        $("#btnSubmit").text("Actualizar");
        $("#ModalCRUD").modal("show");
    });

    /*--------------------------------------------------------------
    # FUNCIÓN PARA MOSTRAR LOS DATOS EN EL MODAL
    --------------------------------------------------------------*/
    function show_data(id_user) {
        url_read = base_url + '/administration/users/show_user';
        $.ajax({
            url: url_read,
            type: "POST",
            dataType: "json",
            data: {id_user:id_user},
            success: function (response) {
                if (response.status === false) {
                    showMessage(response.type, response.message);
                } else {
                    if (modal == "edit") {
                        data_array.id_employee = response.result.id_employee;
                        $("#input-official-name-employee").val(response.result.official_name_employee);
                        $("#input-name-position").val(response.result.name_position);
                        $("#input-name-area").val(response.result.name_area); 
                        $("#input-name-user").val(response.result.name_user); 
                        $('#select-role-user option').each(function () {
                            this.disabled = Number(response.result.id_role) === 1 ? this.value !== '1' : this.value === '1';
                        });
                        $('#select-status-active-user option').each(function () {
                            this.disabled = Number(response.result.id_role) === 1 && this.value !== '1';
                        });
                        $("#select-role-user").val(response.result.id_role);
                        $("#select-status-active-user").val(response.result.status_active_user);
                    }
                    if (modal == "view") {
                        $("#cell-official-name-employee").text(response.result.official_name_employee);
                        $("#cell-name-position").text(response.result.name_position);
                        $("#cell-name-area").text(response.result.name_area);
                        $("#cell-name-user").text(response.result.name_user);
                        $("#cell-name-role").text(response.result.name_role);
                        if (response.result.status_active_user == 1) {
                            $("#cell-status-user").text("Activo");
                        } else {
                            $("#cell-status-user").text("Inactivo");
                        }
                    }
                }
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);
                showMessage("error", "Ocurrió un problema al procesar la solicitud.");
            }
        });
    }

    /*--------------------------------------------------------------
    # FUNCIÓN PARA REGISTRAR O ACTUALIZAR REGISTRO
    --------------------------------------------------------------*/
    form.submit(function(e){                         
        e.preventDefault();
        var form_data = new FormData(form[0]);
        form_data.append('id_user', id_user);
        form_data.append("id_employee", data_array.id_employee ?? '');
        $.ajax({
            url: url_submit,
            type: "POST",
            data: form_data,
            processData: false,
            contentType: false,
            success: function (response) {
                var response = JSON.parse(response);
                if (response.status === false) {
                    showMessage(response.type, response.message);
                } else {
                    showMessage(response.type, response.message);
                    $("#ModalCRUD").modal("hide");
                    tablaDataTable.ajax.reload(null, false);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);
                showMessage("error", "Ocurrió un problema al procesar la solicitud.");
            }
        });											     			
    }); 


    /*--------------------------------------------------------------
    # FUNCIÓN PARA ELIMINAR REGISTRO
    --------------------------------------------------------------*/
        $(document).on("click", ".js-btn-delete", function () {
            id_user = $(this).data('id');
            url_delete = base_url + "/administration/users/delete_user";
            Swal.fire({
                title: "¿Está seguro de eliminar el registro " + id_user + " del sistema?",
                text: "No volveras a usar este registro",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Eliminar",
                cancelButtonText: "Cancelar",
                customClass: {
                    confirmButton: "btn btn-danger m-2",
                    cancelButton: "btn btn-danger m-2",
                },
                allowOutsideClick: false,
            }).then((resul) => {
                if (resul.value) {
                    $.ajax({
                        url: url_delete,
                        method: "POST",
                        dataType: "json",
                        data: {id_user:id_user},
                        success: function (response) {
                            if (response.status === false) {
                                showMessage(response.type, response.message);
                            } else {
                                showMessage(response.type, response.message);
                                tablaDataTable.ajax.reload(null, false);
                            }
                        }
                    });
                }
            });
        });
});
