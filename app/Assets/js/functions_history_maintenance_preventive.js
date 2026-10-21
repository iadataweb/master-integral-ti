document.addEventListener('DOMContentLoaded', function () {
    let base_url = document.querySelector('meta[name="base-url"]').getAttribute('content');
    let tablaDataTable;
    let url;
    let url_read;
    let id_history_maintenance_preventive;
    let modal;
    let statusOptionsHtml = '<option selected disabled value="">Selecciona una opcion</option>';
    let uploadedFilesEditBefore = [];
    let uploadedFilesEditAfter = [];
    let uploadedFilesEditAdditional = [];
    let existingFilesEditBefore = [];
    let existingFilesEditAfter = [];
    let existingFilesEditAdditional = [];
    let selectedDeleteAttachmentsEdit = [];
    let id_object = window.location.href.match(/\/(\d+)$/)[1];

    url = base_url + "/maintenance/maintenances_preventive/show_history_maintenances_preventive";

    tablaDataTable = $('#tableHistoryMaintenancePreventive').DataTable({
        "pagingType": "simple_numbers",
        "aProcessing": true,
        "aServerSide": true,
        "ajax": {
            "url": url,
            "dataSrc": "",
            "method": "POST",
            "data": {
                "id_maintenance_preventive": id_object
            }
        },
        "language": languageDatatable(),
        "columns": [
            {"data": "id_history_maintenance_preventive"},
            {"data": "code_inventory_asset"},
            {"data": "name_type_asset"},
            {"data": "name_category_asset"},
            {"data": "name_type_responsible_maintenance"},
            {"data": "name_frequency_maintenance"},
            {"data": "name_status_maintenance"},
            {"data": "date_scheduled_history_maintenance_preventive"},
            {"data": "date_performed_history_maintenance_preventive"},
            {
                "data": "time_performed_history_maintenance_preventive",
                "render": function (data) {
                    return format_optional_time(data);
                }
            },
            {"data": "options"}
        ],
        "responsive": "true",
        "destroy": true,
        "idisplaylength": 10,
        "order": [[0, "desc"]]
    });

    $.post(base_url + "/maintenance/states_maintenance/combo", function (data) {
        statusOptionsHtml = data;
        $('#select-edit-history-status').html(statusOptionsHtml);
    });

    initialize_edit_history_uploads();

    $('#ModalEditHistory').on('hidden.bs.modal', function () {
        clear_edit_history_uploads(true);
    });

    $(document).on("click", ".js-btn-view-history", function () {
        id_history_maintenance_preventive = $(this).data('id');
        modal = "view";
        show_data(id_history_maintenance_preventive);
    });

    $(document).on("click", ".js-btn-edit-history", function () {
        id_history_maintenance_preventive = $(this).data('id');
        modal = "edit";
        show_data(id_history_maintenance_preventive);
    });

    $('#formEditHistoryMaintenancePreventive').on('submit', function (event) {
        event.preventDefault();
        update_edit_actions_value($("#textarea-edit-history-action-current").val());
        const formData = new FormData(this);
        uploadedFilesEditBefore.forEach((file, index) => {
            formData.append('document_before[' + index + ']', file);
        });
        uploadedFilesEditAfter.forEach((file, index) => {
            formData.append('document_after[' + index + ']', file);
        });
        uploadedFilesEditAdditional.forEach((file, index) => {
            formData.append('document_additional[' + index + ']', file);
        });
        selectedDeleteAttachmentsEdit.forEach((id, index) => {
            formData.append('delete_history_attachments[' + index + ']', id);
        });
        const $button = $('#btn-submit-edit-history');
        const originalHtml = $button.html();
        $button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Guardando...');

        $.ajax({
            url: base_url + '/maintenance/maintenances_preventive/update_history_maintenance_preventive',
            type: 'POST',
            dataType: 'json',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (response) {
                showMessage(response.type || (response.status ? 'success' : 'error'), response.message || 'Proceso finalizado.');
                if (response.status) {
                    clear_edit_history_uploads(true);
                    $('#ModalEditHistory').modal('hide');
                    tablaDataTable.ajax.reload(null, false);
                }
            },
            error: function (error) {
                console.error('Error:', error);
                showMessage('error', 'Ocurrio un problema al corregir el registro.');
            },
            complete: function () {
                $button.prop('disabled', false).html(originalHtml);
            }
        });
    });

    function initialize_edit_history_uploads() {
        setup_edit_history_upload(
            "#custom-btn-upload-table-edit-before",
            "#table-handler-edit-before",
            "input-edit-document-before",
            uploadedFilesEditBefore,
            existingFilesEditBefore
        );
        setup_edit_history_upload(
            "#custom-btn-upload-table-edit-after",
            "#table-handler-edit-after",
            "input-edit-document-after",
            uploadedFilesEditAfter,
            existingFilesEditAfter
        );
        setup_edit_history_upload(
            "#custom-btn-upload-table-edit-additional",
            "#table-handler-edit-additional",
            "input-edit-document-additional",
            uploadedFilesEditAdditional,
            existingFilesEditAdditional
        );
    }

    function setup_edit_history_upload(containerSelector, tableSelector, inputId, targetArray, existingArray) {
        const container = document.querySelector(containerSelector);
        if (!container) {
            return;
        }
        container.innerHTML = `
            <span class="btn btn-gray-200 w-100 my-3" data-for="${inputId}">Seleccionar archivo</span>
            <input type="file" id="${inputId}" multiple style="display:none;">
        `;

        const trigger = container.querySelector(`span[data-for="${inputId}"]`);
        const input = container.querySelector(`#${inputId}`);

        trigger.addEventListener("click", function () {
            input.click();
        });

        input.addEventListener("change", function () {
            const files = Array.from(input.files || []);
            if (targetArray.length + existingArray.length + files.length > 10) {
                showMessage("warning", "Solo se permiten un maximo de 10 archivos.");
                input.value = "";
                return;
            }
            files.forEach((file) => {
                if (file.size > 15 * 1024 * 1024) {
                    showMessage("warning", `El archivo ${file.name} excede el tamano maximo de 15 MB.`);
                    return;
                }
                targetArray.push(file);
            });
            input.value = "";
            refresh_edit_history_upload_table(tableSelector, targetArray, existingArray);
        });

        refresh_edit_history_upload_table(tableSelector, targetArray, existingArray);
    }

    function refresh_edit_history_upload_table(tableSelector, targetArray, existingArray) {
        const table = document.querySelector(tableSelector);
        const tbody = table ? table.querySelector("tbody") : null;
        if (!tbody) {
            return;
        }
        tbody.innerHTML = "";
        let rowNumber = 0;

        existingArray.forEach((attachment) => {
            rowNumber++;
            const row = document.createElement("tr");
            const attachmentId = parseInt(attachment.id_attachment, 10);
            const attachmentName = escape_html(attachment.name_attachment || "Archivo adjunto");
            const attachmentUrl = build_attachment_url(attachment.route_attachment || "");
            const disabledClass = attachmentUrl === "#" ? " disabled" : "";
            row.innerHTML = `
                <td>${rowNumber}</td>
                <td>${attachmentName}</td>
                <td>
                    <a href="${attachmentUrl}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary${disabledClass}">Abrir</a>
                    <button type="button" class="btn btn-danger btn-sm js-remove-existing-edit ms-1" data-id="${attachmentId}">Eliminar</button>
                </td>
            `;
            tbody.appendChild(row);
        });

        targetArray.forEach((file, index) => {
            rowNumber++;
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${rowNumber}</td>
                <td>${escape_html(file.name)}</td>
                <td><button type="button" class="btn btn-danger btn-sm js-remove-upload-edit" data-index="${index}">Eliminar</button></td>
            `;
            tbody.appendChild(row);
        });

        if (rowNumber === 0) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">Ningun archivo seleccionado.</td></tr>';
            return;
        }

        tbody.querySelectorAll(".js-remove-upload-edit").forEach((button) => {
            button.addEventListener("click", function () {
                const idx = parseInt(this.getAttribute("data-index"), 10);
                if (!isNaN(idx)) {
                    targetArray.splice(idx, 1);
                    refresh_edit_history_upload_table(tableSelector, targetArray, existingArray);
                }
            });
        });

        tbody.querySelectorAll(".js-remove-existing-edit").forEach((button) => {
            button.addEventListener("click", function () {
                const idAttachment = parseInt(this.getAttribute("data-id"), 10);
                if (!isNaN(idAttachment)) {
                    selectedDeleteAttachmentsEdit.push(idAttachment);
                    const filtered = existingArray.filter((attachment) => parseInt(attachment.id_attachment, 10) !== idAttachment);
                    existingArray.length = 0;
                    filtered.forEach((attachment) => existingArray.push(attachment));
                    refresh_edit_history_upload_table(tableSelector, targetArray, existingArray);
                }
            });
        });
    }

    function clear_edit_history_uploads(includeExisting) {
        uploadedFilesEditBefore.length = 0;
        uploadedFilesEditAfter.length = 0;
        uploadedFilesEditAdditional.length = 0;
        selectedDeleteAttachmentsEdit.length = 0;
        if (includeExisting) {
            existingFilesEditBefore.length = 0;
            existingFilesEditAfter.length = 0;
            existingFilesEditAdditional.length = 0;
        }
        refresh_edit_history_upload_table("#table-handler-edit-before", uploadedFilesEditBefore, existingFilesEditBefore);
        refresh_edit_history_upload_table("#table-handler-edit-after", uploadedFilesEditAfter, existingFilesEditAfter);
        refresh_edit_history_upload_table("#table-handler-edit-additional", uploadedFilesEditAdditional, existingFilesEditAdditional);
    }

    function load_edit_history_existing_attachments(beforeAttachments, afterAttachments, additionalAttachments) {
        existingFilesEditBefore.length = 0;
        existingFilesEditAfter.length = 0;
        existingFilesEditAdditional.length = 0;
        (beforeAttachments || []).forEach((item) => existingFilesEditBefore.push(item));
        (afterAttachments || []).forEach((item) => existingFilesEditAfter.push(item));
        (additionalAttachments || []).forEach((item) => existingFilesEditAdditional.push(item));
        refresh_edit_history_upload_table("#table-handler-edit-before", uploadedFilesEditBefore, existingFilesEditBefore);
        refresh_edit_history_upload_table("#table-handler-edit-after", uploadedFilesEditAfter, existingFilesEditAfter);
        refresh_edit_history_upload_table("#table-handler-edit-additional", uploadedFilesEditAdditional, existingFilesEditAdditional);
    }

    function show_data(id_history_maintenance_preventive) {
        url_read = base_url + '/maintenance/maintenances_preventive/show_history_maintenance_preventive_one';
        $.ajax({
            url: url_read,
            type: "POST",
            dataType: "json",
            data: {id_history_maintenance_preventive: id_history_maintenance_preventive},
            success: function (response) {
                if (response.status === false) {
                    showMessage(response.type, response.message);
                    return;
                }

                if (modal === "view") {
                    render_view_modal(response);
                    return;
                }

                if (modal === "edit") {
                    render_edit_modal(response);
                }
            },
            error: function (error) {
                console.error("Error:", error);
                showMessage("error", "Ocurrio un problema al procesar la solicitud.");
            }
        });
    }

    function render_view_modal(response) {
        $("#cell-code-inventory-asset").text(response.result.code_inventory_asset);
        $("#cell-name-type-asset").text(response.result.name_type_asset);
        $("#cell-name-category-asset").text(response.result.name_category_asset);
        $("#cell-name-type-responsible-maintenance").text(response.result.name_type_responsible_maintenance);
        $("#cell-name-frequency-maintenance").text(response.result.name_frequency_maintenance);
        $("#cell-date-scheduled-maintenance-preventive").text(response.result.date_scheduled_history_maintenance_preventive);
        $("#cell-date-performed-history-maintenance-preventive").text(response.result.date_performed_history_maintenance_preventive);
        $("#cell-time-performed-history-maintenance-preventive").text(format_optional_time(response.result.time_performed_history_maintenance_preventive));
        render_action_list("#cell-action-history-maintenance-preventive", response.result.action_history_maintenance_preventive);
        $("#cell-observation-history-maintenance-preventive").text(response.result.observation_history_maintenance_preventive);
        render_history_attachments("#tbody-attachments-before-history", response.attachments_before ?? []);
        render_history_attachments("#tbody-attachments-after-history", response.attachments_after ?? []);
        render_history_attachments("#tbody-attachments-additional-history", response.attachments_additional ?? []);
        $("#panel-additional-documents-history").toggleClass("d-none", !Array.isArray(response.attachments_additional) || response.attachments_additional.length === 0);
        $("#ModalSeeHistory .modal-title").text("Datos del Registro");
        $("#ModalSeeHistory").modal("show");
    }

    function render_edit_modal(response) {
        const result = response.result || {};
        $('#formEditHistoryMaintenancePreventive')[0].reset();
        clear_edit_history_uploads(true);
        $('#select-edit-history-status').html(statusOptionsHtml);
        $('#input-edit-history-id').val(result.id_history_maintenance_preventive || id_history_maintenance_preventive);
        $('#select-edit-history-status').val(result.id_status_maintenance || '');
        $('#input-edit-history-date-performed').val((result.date_performed_history_maintenance_preventive || '').substring(0, 10));
        $('#input-edit-history-time-performed').val((result.time_performed_history_maintenance_preventive || '').substring(0, 5));
        const currentActions = result.action_history_maintenance_preventive || '';
        const responsibleType = (result.name_type_responsible_maintenance || '').toString().trim().toLowerCase();
        const isExternalResponsible = responsibleType === 'externo';
        $('#textarea-edit-history-action-current').val(currentActions || 'Sin acciones registradas.');
        $('#input-edit-history-action').val(currentActions);
        $('#section-edit-history-internal-actions').toggleClass('d-none', isExternalResponsible);
        if (isExternalResponsible) {
            $('#content-edit-actions-maintenance-preventive-checks').html('');
        } else {
            load_edit_actions_maintenance_preventive_checks(currentActions);
        }
        $('#textarea-edit-history-observation').val(result.observation_history_maintenance_preventive || '');
        load_edit_history_existing_attachments(response.attachments_before ?? [], response.attachments_after ?? [], response.attachments_additional ?? []);
        $('#ModalEditHistory').modal('show');
    }

    function load_edit_actions_maintenance_preventive_checks(currentActions) {
        const $container = $('#content-edit-actions-maintenance-preventive-checks');
        $container.html('<div class="text-muted small">Cargando acciones...</div>');
        $.post(base_url + "/maintenance/actions_maintenance_preventive/combo_checks", function (data) {
            const html = (data || '').toString().replaceAll('check-action-preventive-', 'check-edit-action-preventive-');
            $container.html(html);
            preselect_edit_actions(currentActions);
            update_edit_actions_value(currentActions);
        }).fail(function () {
            $container.html('<div class="text-danger small">No se pudieron cargar las acciones.</div>');
            update_edit_actions_value(currentActions);
        });
    }

    $(document).on('change', '#content-edit-actions-maintenance-preventive-checks .js-action-check', function () {
        update_edit_actions_value($('#textarea-edit-history-action-current').val());
    });

    function preselect_edit_actions(currentActions) {
        const currentItems = get_action_items(currentActions).map(normalize_action_text);
        if (currentItems.length === 0) {
            return;
        }

        $('#content-edit-actions-maintenance-preventive-checks .js-action-check').each(function () {
            const label = $(this).closest('.form-check').find('label').text().trim();
            if (currentItems.includes(normalize_action_text(label))) {
                $(this).prop('checked', true);
            }
        });
    }

    function update_edit_actions_value(fallbackActions) {
        const selectedActions = build_edit_actions_text_from_checks();
        $('#input-edit-history-action').val(selectedActions || (fallbackActions || '').toString().trim());
    }

    function build_edit_actions_text_from_checks() {
        const labels = [];
        $('#content-edit-actions-maintenance-preventive-checks .js-action-check:checked').each(function () {
            const label = $(this).closest('.form-check').find('label').text().trim();
            if (label !== '') {
                labels.push(label);
            }
        });
        return labels.join(', ');
    }

    function get_action_items(actionText) {
        return (actionText || '').toString()
            .split(/,|\r?\n|;/)
            .map(item => item.trim())
            .filter(item => item.length > 0);
    }

    function normalize_action_text(value) {
        return (value || '').toString().trim().toLowerCase();
    }

    function format_optional_time(value) {
        const raw = (value || '').toString().trim();
        if (raw === '') {
            return 'No definido';
        }
        return raw.substring(0, 5);
    }

    function render_history_attachments(tbodySelector, attachments) {
        const $tbody = $(tbodySelector);
        $tbody.empty();

        if (!Array.isArray(attachments) || attachments.length === 0) {
            $tbody.append('<tr><td colspan="3" class="text-muted text-center">Sin registros.</td></tr>');
            return;
        }

        attachments.forEach((item, index) => {
            const safeName = escape_html((item.name_attachment || '').toString());
            const safeUrl = (item.route_attachment || '').toString();
            const normalizedUrl = build_attachment_url(safeUrl);
            const disabledClass = normalizedUrl === "#" ? " disabled" : "";
            const row = `
                <tr>
                    <td>${formatRecordId(item, 'id_attachment')}</td>
                    <td>${safeName}</td>
                    <td>
                        <a href="${normalizedUrl}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary${disabledClass}">Abrir</a>
                    </td>
                </tr>
            `;
            $tbody.append(row);
        });
    }

    function build_attachment_url(routeAttachment) {
        const cleanRoute = (routeAttachment || "").toString().trim();
        if (cleanRoute === "") {
            return "#";
        }
        if (cleanRoute.startsWith("http://") || cleanRoute.startsWith("https://")) {
            return cleanRoute;
        }
        if (cleanRoute.startsWith("/")) {
            return base_url + cleanRoute;
        }
        return base_url + "/" + cleanRoute.replace(/^\/+/, "");
    }

    function render_action_list(containerSelector, actionText) {
        const $container = $(containerSelector);
        const raw = (actionText || "").toString().trim();

        if (raw === "") {
            $container.text("Sin acciones registradas.");
            return;
        }

        const items = raw
            .split(/,|\r?\n|;/)
            .map(item => item.trim())
            .filter(item => item.length > 0);

        if (items.length <= 1) {
            $container.text(raw);
            return;
        }

        const listHtml = items
            .map(item => `<li>${escape_html(item)}</li>`)
            .join("");

        $container.html(`<ul class="mb-0 ps-3">${listHtml}</ul>`);
    }

    function escape_html(value) {
        return (value || "").toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/\"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
});
