document.addEventListener('DOMContentLoaded', function () {
    const baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content');
    const formPublicAccess = $('#formPublicQuickAccess');

    togglePublicAccessDates();
    $('#public-quick-access-mode').on('change', togglePublicAccessDates);

    formPublicAccess.on('submit', function (event) {
        event.preventDefault();
        $.ajax({
            url: baseUrl + '/administration/system_configuration/save_public_quick_access',
            type: 'POST',
            dataType: 'json',
            data: formPublicAccess.serialize(),
            success: function (response) {
                showMessage(response.type, response.message);
                if (response.status && response.public_access) {
                    $('#public-access-status-badge')
                        .removeClass('bg-success bg-danger bg-secondary bg-info')
                        .addClass(statusClass(response.public_access.status))
                        .text(response.public_access.status);
                }
            },
            error: function () {
                showMessage('error', 'No se pudo guardar la configuracion.');
            }
        });
    });

    function togglePublicAccessDates() {
        const isTimed = $('#public-quick-access-mode').val() === 'timed';
        $('.js-public-access-dates').toggle(isTimed);
        $('#public-quick-access-starts-at, #public-quick-access-expires-at').prop('disabled', !isTimed);
    }

    function statusClass(status) {
        status = (status || '').toLowerCase();
        if (status === 'activo') return 'bg-success';
        if (status === 'programado') return 'bg-info';
        if (status === 'expirado') return 'bg-danger';
        return 'bg-secondary';
    }

});
