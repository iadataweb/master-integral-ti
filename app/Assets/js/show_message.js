/*--------------------------------------------------------------
# FUNCIÓN PARA MOSTRAR MENSAJE
--------------------------------------------------------------*/
function showMessage(tipo, mensaje) {
    
    if (tipo == "success") {
        Swal.fire({
            icon: tipo,
            heightAuto: false,
            title: mensaje,
            showConfirmButton: false,
            timer: 5000,
        });
    }

    if (tipo == "error") {
        Swal.fire({
            icon: tipo,
            heightAuto: false,
            title: mensaje,
            buttonsStyling: false,
            showConfirmButton: false,
            showCancelButton: true,
            cancelButtonText: "Cerrar",
            customClass: {
                cancelButton: "btn btn-danger",
            },
            allowOutsideClick: false
        });
    }

    if (tipo == "warning") {
        Swal.fire({
            icon: tipo,
            heightAuto: false,
            title: mensaje,
            buttonsStyling: false,
            showConfirmButton: false,
            showCancelButton: true,
            cancelButtonText: "Cerrar",
            customClass: {
                cancelButton: "btn btn-danger",
            },
            allowOutsideClick: false
        });
    }
}