document.addEventListener('DOMContentLoaded', function () {
    const base_url = document.querySelector('meta[name="base-url"]').getAttribute('content');
    const form = $('#formLabelTemplate');
    const preview = document.getElementById('label-template-preview');
    const qrBox = document.getElementById('label-template-qr');
    const codeBox = document.getElementById('label-template-code');
    let submitUrl = base_url + '/administration/label_templates/insert_label_template';
    let currentPreviewUrl = '';
    let draggingTarget = null;

    const table = $('#tableManageLabelTemplates').DataTable({
        pagingType: 'simple_numbers',
        aProcessing: true,
        aServerSide: true,
        ajax: { url: base_url + '/administration/label_templates/show_label_templates', dataSrc: '' },
        language: languageDatatable(),
        columns: [
            { data: 'id_label_template' },
            { data: 'name_label_template' },
            { data: 'size_label_template' },
            { data: 'qr_position_label_template' },
            { data: 'code_position_label_template' },
            { data: 'status_active_label_template' },
            { data: 'options' }
        ],
        responsive: true,
        dom: 'Bfrtilp',
        buttons: [
            { extend: 'excelHtml5', text: '<i class="icon-file-excel"></i>', titleAttr: 'Exportar a Excel', className: 'btn btn-success', filename: 'Plantillas de Etiquetas' },
            { extend: 'pdfHtml5', text: '<i class="icon-file-pdf"></i>', titleAttr: 'Exportar a PDF', className: 'btn btn-danger', filename: 'Plantillas de Etiquetas' },
            { extend: 'print', text: '<i class="icon-print"></i>', titleAttr: 'Imprimir', className: 'btn btn-info', filename: 'Plantillas de Etiquetas' }
        ],
        destroy: true,
        order: [[0, 'desc']]
    });

    $('#btnNew').on('click', function () {
        submitUrl = base_url + '/administration/label_templates/insert_label_template';
        resetForm();
        $('#labelLabelTemplate').text('Nuevo Registro');
        $('#btnSubmitLabelTemplate').text('Guardar');
        $('.js-file-help').hide();
        $('#ModalCRUD').modal('show');
    });

    $(document).on('click', '.js-btn-edit', function () {
        submitUrl = base_url + '/administration/label_templates/update_label_template';
        readTemplate($(this).data('id'), 'edit');
    });

    $(document).on('click', '.js-btn-view', function () {
        readTemplate($(this).data('id'), 'view');
    });

    $('#input-design-label-template').on('change', function () {
        const file = this.files && this.files[0] ? this.files[0] : null;
        if (!file) return;
        if (currentPreviewUrl && currentPreviewUrl.startsWith('blob:')) URL.revokeObjectURL(currentPreviewUrl);
        currentPreviewUrl = URL.createObjectURL(file);
        renderPreview(currentPreviewUrl);
    });

    $('.js-preview-field').on('input', updatePreviewBoxes);
    attachDrag(qrBox, 'qr');
    attachDrag(codeBox, 'code');

    form.on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(form[0]);
        $.ajax({
            url: submitUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                showMessage(response.type, response.message);
                if (response.status) {
                    $('#ModalCRUD').modal('hide');
                    table.ajax.reload(null, false);
                }
            },
            error: function () {
                showMessage('error', 'Ocurrio un problema al procesar la solicitud.');
            }
        });
    });

    $(document).on('click', '.js-btn-delete', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Eliminar plantilla',
            text: 'La plantilla dejara de estar disponible para nuevas etiquetas.',
            icon: 'warning',
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar',
            customClass: { confirmButton: 'btn btn-danger m-2', cancelButton: 'btn btn-secondary m-2' },
            allowOutsideClick: false
        }).then((result) => {
            if (!result.value) return;
            $.ajax({
                url: base_url + '/administration/label_templates/delete_label_template',
                type: 'POST',
                dataType: 'json',
                data: { id_label_template: id },
                success: function (response) {
                    showMessage(response.type, response.message);
                    if (response.status) table.ajax.reload(null, false);
                }
            });
        });
    });

    function readTemplate(id, mode) {
        $.ajax({
            url: base_url + '/administration/label_templates/show_label_template',
            type: 'POST',
            dataType: 'json',
            data: { id_label_template: id },
            success: function (response) {
                if (!response.status) {
                    showMessage(response.type, response.message);
                    return;
                }
                const item = response.result;
                if (mode === 'edit') {
                    resetForm();
                    $('#input-id-label-template').val(item.id_label_template);
                    $('#input-name-label-template').val(item.name_label_template);
                    $('#input-width-cm-label-template').val(item.width_cm_label_template);
                    $('#input-height-cm-label-template').val(item.height_cm_label_template);
                    $('#textarea-description-label-template').val(item.description_label_template);
                    $('#select-status-active-label-template').val(item.status_active_label_template);
                    $('#input-qr-x-label-template').val(item.qr_x_percent_label_template);
                    $('#input-qr-y-label-template').val(item.qr_y_percent_label_template);
                    $('#input-qr-width-label-template').val(item.qr_width_percent_label_template);
                    $('#input-qr-height-label-template').val(item.qr_height_percent_label_template);
                    $('#input-code-x-label-template').val(item.code_x_percent_label_template || 5);
                    $('#input-code-y-label-template').val(item.code_y_percent_label_template || 75);
                    $('#input-code-width-label-template').val(item.code_width_percent_label_template || 45);
                    $('#input-code-height-label-template').val(item.code_height_percent_label_template || 12);
                    $('#input-code-font-size-label-template').val(item.code_font_size_label_template || 8);
                    currentPreviewUrl = buildUrl(item.design_route_label_template);
                    renderPreview(currentPreviewUrl);
                    $('#labelLabelTemplate').text('Actualizar Registro');
                    $('#btnSubmitLabelTemplate').text('Actualizar');
                    $('.js-file-help').show();
                    $('#ModalCRUD').modal('show');
                    return;
                }
                $('#see-name-label-template').text(item.name_label_template || 'No definido');
                $('#see-width-label-template').text((item.width_cm_label_template || '0') + ' cm');
                $('#see-height-label-template').text((item.height_cm_label_template || '0') + ' cm');
                $('#see-description-label-template').text(item.description_label_template || 'Ninguna.');
                $('#see-status-label-template').html(item.status_active_label_template == 1 ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>');
                $('#see-label-template-preview').html(buildPreviewHtml(item));
                $('#ModalSee').modal('show');
            }
        });
    }

    function resetForm() {
        form.trigger('reset');
        $('#input-id-label-template').val('');
        $('#input-width-cm-label-template').val('8.50');
        $('#input-height-cm-label-template').val('5.50');
        $('#input-qr-x-label-template').val('60');
        $('#input-qr-y-label-template').val('20');
        $('#input-qr-width-label-template').val('30');
        $('#input-qr-height-label-template').val('45');
        $('#input-code-x-label-template').val('5');
        $('#input-code-y-label-template').val('75');
        $('#input-code-width-label-template').val('45');
        $('#input-code-height-label-template').val('12');
        $('#input-code-font-size-label-template').val('8');
        currentPreviewUrl = '';
        preview.innerHTML = '<div class="label-template-empty">Cargue un PNG para visualizar la etiqueta y ajustar el QR y el codigo.</div>';
        preview.appendChild(qrBox);
        preview.appendChild(codeBox);
        qrBox.style.display = 'none';
        codeBox.style.display = 'none';
    }

    function renderPreview(url) {
        preview.innerHTML = `<img src="${url}" alt="Vista previa de plantilla">`;
        preview.appendChild(qrBox);
        preview.appendChild(codeBox);
        qrBox.style.display = 'grid';
        codeBox.style.display = 'grid';
        updatePreviewBoxes();
    }

    function updatePreviewBoxes() {
        if (!currentPreviewUrl) return;
        updateBox(qrBox, 'qr');
        updateBox(codeBox, 'code');
    }

    function updateBox(box, type) {
        const prefix = type === 'qr' ? 'qr' : 'code';
        const width = clamp(parseFloat($(`#input-${prefix}-width-label-template`).val()) || 1, 1, 100);
        const height = clamp(parseFloat($(`#input-${prefix}-height-label-template`).val()) || 1, 1, 100);
        const x = clamp(parseFloat($(`#input-${prefix}-x-label-template`).val()) || 0, 0, 100 - width);
        const y = clamp(parseFloat($(`#input-${prefix}-y-label-template`).val()) || 0, 0, 100 - height);
        box.style.left = x + '%';
        box.style.top = y + '%';
        box.style.width = width + '%';
        box.style.height = height + '%';
        if (type === 'code') {
            const fontSize = clamp(parseFloat($('#input-code-font-size-label-template').val()) || 8, 4, 24);
            box.style.fontSize = fontSize + 'px';
        }
    }

    function attachDrag(box, type) {
        box.addEventListener('pointerdown', function (event) {
            draggingTarget = type;
            box.setPointerCapture(event.pointerId);
        });
        box.addEventListener('pointermove', function (event) {
            if (draggingTarget !== type) return;
            const rect = preview.getBoundingClientRect();
            const widthPercent = parseFloat($(`#input-${type}-width-label-template`).val()) || 1;
            const heightPercent = parseFloat($(`#input-${type}-height-label-template`).val()) || 1;
            const x = ((event.clientX - rect.left) / rect.width) * 100 - (widthPercent / 2);
            const y = ((event.clientY - rect.top) / rect.height) * 100 - (heightPercent / 2);
            $(`#input-${type}-x-label-template`).val(clamp(x, 0, 100 - widthPercent).toFixed(2));
            $(`#input-${type}-y-label-template`).val(clamp(y, 0, 100 - heightPercent).toFixed(2));
            updatePreviewBoxes();
        });
        box.addEventListener('pointerup', function () { draggingTarget = null; });
        box.addEventListener('pointercancel', function () { draggingTarget = null; });
    }

    function buildPreviewHtml(item) {
        const codeFontSize = item.code_font_size_label_template || 8;
        return `<img src="${buildUrl(item.design_route_label_template)}" alt="Plantilla">
            <div class="label-template-qr" style="display:grid;left:${item.qr_x_percent_label_template}%;top:${item.qr_y_percent_label_template}%;width:${item.qr_width_percent_label_template}%;height:${item.qr_height_percent_label_template}%;"></div>
            <div class="label-template-code" style="display:grid;left:${item.code_x_percent_label_template || 5}%;top:${item.code_y_percent_label_template || 75}%;width:${item.code_width_percent_label_template || 45}%;height:${item.code_height_percent_label_template || 12}%;font-size:${codeFontSize}px;">INV-20260511170229-5A4F</div>`;
    }

    function buildUrl(route) {
        const clean = (route || '').toString().replace(/^\/+/, '');
        return clean.startsWith('http') ? clean : base_url + '/' + clean;
    }

    function clamp(value, min, max) {
        return Math.max(min, Math.min(max, value));
    }
});
