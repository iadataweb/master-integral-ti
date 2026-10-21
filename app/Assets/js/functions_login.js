document.addEventListener('DOMContentLoaded', function () {
    const passwordField = document.getElementById('password');
    const visibilityButton = document.getElementById('toggle-login-password');
    visibilityButton.addEventListener('click', function () {
        const visible = passwordField.type === 'password';
        passwordField.type = visible ? 'text' : 'password';
        const label = visible ? 'Ocultar contraseña' : 'Mostrar contraseña';
        visibilityButton.setAttribute('aria-label', label);
        visibilityButton.setAttribute('aria-pressed', String(visible));
        visibilityButton.title = label;
        document.getElementById('login-password-slash').style.display = visible ? '' : 'none';
    });
    let form = $("#formLogin");
    let base_url = document.querySelector('meta[name="base-url"]').getAttribute('content');
    let url_submit;
    let name_user;
    let password;
    /*--------------------------------------------------------------
    # FUNCIÓN PARA INICIAR SESIÓN
    --------------------------------------------------------------*/
    form.submit(function(e){                         
        e.preventDefault();
        name_user = $.trim($("#name_user").val());
        password = $("#password").val();
        if(name_user.length == ""){
            showMessage("warning", "Debe ingresar su nombre de usuario.");
            return false; 
        } else if(password.length == ""){
            showMessage("warning", "Debe ingresar la contraseña.");
            return false; 
        } else if(name_user.length == "" && password.length == ""){
            showMessage("warning", "Debe ingresar su nombre de usuario y la contraseña.");
            return false; 
        } else {
            url_submit = base_url + '/login/login_user'; 
            var form_data = new FormData(form[0]);
            $.ajax({
                url: url_submit,
                type: "POST",
                data: form_data,
                processData: false,
                contentType: false,
                error: function (xhr) { showMessage('error', xhr.responseJSON?.message || 'No se pudo iniciar sesión. Recarga la página e inténtalo de nuevo.'); },
                success: function (response) {
                    var response = JSON.parse(response);
                    if (response.status === false) {
                        showMessage(response.type, response.message);
                    } else {
                        if (response.url_scan && response.url_scan !== "") {
                            window.location = response.url_scan;
                        } else {
                            window.location = base_url + '/dashboard';
                        }                       
                    }
                },
                error: function (error) {
                    console.error("Error:", error);
                    showMessage("error", "Ocurrió un problema al procesar la solicitud.");
                }
            });
        }
    });
});
