document.addEventListener('DOMContentLoaded', function () {
    const baseUrl = document.querySelector('meta[name="base-url"]')?.getAttribute('content') || '';
    const form = document.getElementById('formProfilePhoto');
    const inputPhoto = document.getElementById('input-profile-photo');
    const preview = document.getElementById('img-profile-preview');
    const btnDelete = document.getElementById('btnDeleteProfilePhoto');

    if (!baseUrl || !form || !inputPhoto || !preview) {
        return;
    }

    inputPhoto.addEventListener('change', function () {
        const file = inputPhoto.files && inputPhoto.files[0] ? inputPhoto.files[0] : null;
        if (!file) {
            return;
        }
        const reader = new FileReader();
        reader.onload = function (event) {
            if (event.target && event.target.result) {
                preview.src = event.target.result;
            }
        };
        reader.readAsDataURL(file);
    });

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        if (!inputPhoto.files || !inputPhoto.files.length) {
            showMessage('error', 'Seleccione una imagen para actualizar.');
            return;
        }

        const formData = new FormData(form);
        fetch(baseUrl + '/account/update_profile_photo', {
            method: 'POST',
            body: formData,
        })
            .then(parseJsonResponse)
            .then(function (response) {
                if (!response.status) {
                    showMessage(response.type || 'error', response.message || 'No se pudo actualizar la foto.');
                    return;
                }

                const photoUrl = (response.photo_url || '').toString();
                if (photoUrl) {
                    updateProfilePhotoInLayout(photoUrl);
                }
                inputPhoto.value = '';
                showMessage(response.type || 'success', response.message || 'Foto de perfil actualizada.');
            })
            .catch(function () {
                showMessage('error', 'Ocurrio un error al actualizar la foto de perfil.');
            });
    });

    if (btnDelete) {
        btnDelete.addEventListener('click', function () {
            Swal.fire({
                title: 'Eliminar foto de perfil?',
                text: 'Se usara la imagen predeterminada del sistema.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-danger m-2',
                    cancelButton: 'btn btn-gray-600 m-2',
                },
            }).then(function (result) {
                if (!result.isConfirmed) {
                    return;
                }

                fetch(baseUrl + '/account/delete_profile_photo', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    },
                    body: '',
                })
                    .then(parseJsonResponse)
                    .then(function (response) {
                        if (!response.status) {
                            showMessage(response.type || 'error', response.message || 'No se pudo eliminar la foto.');
                            return;
                        }

                        const photoUrl = (response.photo_url || '').toString();
                        if (photoUrl) {
                            updateProfilePhotoInLayout(photoUrl);
                        }
                        inputPhoto.value = '';
                        showMessage(response.type || 'success', response.message || 'Foto de perfil eliminada.');
                    })
                    .catch(function () {
                        showMessage('error', 'Ocurrio un error al eliminar la foto de perfil.');
                    });
            });
        });
    }
});

function parseJsonResponse(response) {
    return response
        .text()
        .then(function (text) {
            try {
                return JSON.parse(text);
            } catch (e) {
                return {
                    status: false,
                    type: 'error',
                    message: 'Respuesta invalida del servidor.',
                };
            }
        });
}

function updateProfilePhotoInLayout(photoUrl) {
    const finalUrl = addCacheBuster(photoUrl);
    const selectors = [
        '.navbar .avatar',
        '.user-card .avatar-lg img',
        '#img-profile-preview',
    ];

    selectors.forEach(function (selector) {
        document.querySelectorAll(selector).forEach(function (image) {
            image.src = finalUrl;
        });
    });
}

function addCacheBuster(url) {
    const value = (url || '').toString();
    if (!value) {
        return value;
    }
    const separator = value.includes('?') ? '&' : '?';
    return value + separator + 'v=' + Date.now();
}
