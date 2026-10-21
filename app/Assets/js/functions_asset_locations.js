document.addEventListener('DOMContentLoaded', function () {
    const baseUrl = document.querySelector('meta[name="base-url"]')?.getAttribute('content') || '';
    let plans = [];
    let currentLocations = [];

    if (!baseUrl || typeof $ === 'undefined') {
        return;
    }

    const locationTable=$('#tableAssetLocations').DataTable({
        data:[], language:languageDatatable(),pagingType:'simple_numbers',responsive:true,
        dom:'Bfrtilp',
        buttons:[
            {extend:'excelHtml5',text:'<i class="icon-file-excel"></i>',titleAttr:'Exportar a Excel',className:'btn btn-primary',filename:'Ubicaciones de activos',exportOptions:{columns:[0,1,2,3,4,5]}},
            {extend:'pdfHtml5',text:'<i class="icon-file-pdf"></i>',titleAttr:'Exportar a PDF',className:'btn btn-primary',orientation:'landscape',exportOptions:{columns:[0,1,2,3,4,5]}},
            {extend:'print',text:'<i class="icon-printer"></i>',titleAttr:'Imprimir',className:'btn btn-primary',exportOptions:{columns:[0,1,2,3,4,5]}}
        ],
        columns:[
            {data:'id_asset',responsivePriority:1},
            {data:'code_inventory_asset',render:function(value,type,row){return type==='display'?'<strong>'+escapeHtml(value)+'</strong><small class="d-block text-muted">'+escapeHtml(row.name_type_asset)+'</small>':value+' '+row.name_type_asset;}},
            {data:'situation',render:$.fn.dataTable.render.text()},
            {data:'name_space',defaultContent:'Sin ubicación',render:function(value,type,row){const text=value?[row.name_campus,value,'Piso '+row.floor_space].join(' · '):'Sin ubicación';return type==='display'?escapeHtml(text):text;}},
            {data:'responsible_receiver',render:$.fn.dataTable.render.text()},
            {data:'name_floor_plan',defaultContent:'Sin plano',render:function(value,type,row){const text=value?(value+(row.coordinate_x_movement_asset===null?' · Sin coordenadas':'')):'Sin plano';return type==='display'?escapeHtml(text):text;}},
            {data:'id_asset',orderable:false,searchable:false,render:id=>'<button type="button" class="btn btn-sm btn-secondary js-location-detail" data-id="'+Number(id)+'">Ver</button>'}
        ],order:[[1,'asc']]
    });
    $(document).on('click','.js-location-detail',function(){const item=currentLocations.find(row=>Number(row.id_asset)===Number(this.dataset.id));if(item)showLocationDetail(item);});
    $('#location-list-view,#location-plan-view').on('click',function(){const isList=this.id==='location-list-view';$('#asset-location-list').toggleClass('d-none',!isList);$('#asset-location-plan').toggleClass('d-none',isList);$('#location-list-view').toggleClass('btn-primary',isList).toggleClass('btn-outline-primary',!isList).attr('aria-pressed',String(isList));$('#location-plan-view').toggleClass('btn-primary',!isList).toggleClass('btn-outline-primary',isList).attr('aria-pressed',String(!isList));if(isList)locationTable.columns.adjust();else renderMarkers();});
    $('#filter-campus,#filter-floor,#filter-type-asset,#filter-category-asset,#filter-space,#filter-status').on('change',renderMarkers);
    loadPlans();

    $('#filter-floor-plan').on('change', function () {
        const plan = plans.find(item => String(item.id_floor_plan) === String(this.value));
        setPlan(plan || null);
        loadLocations();
    });
    $('#btnApplyAssetLocationFilters').on('click', loadLocations);
    $('#btnClearAssetLocationFilters').on('click', function () {
        $('#filter-campus,#filter-floor,#filter-floor-plan,#filter-type-asset, #filter-category-asset, #filter-space, #filter-status').val('');
        setPlan(null);locationTable.search('');
        loadLocations();
    });

    function loadPlans() {
        $.post(baseUrl + '/movement/movements_asset/location_lookups', function (response) {
            const data = parseJson(response);
            if (!data.status || !Array.isArray(data.result)) {
                showMessage(data.type || 'error', data.message || 'No se pudieron cargar los planos.');
                return;
            }
            plans = data.result;
            const $select = $('#filter-floor-plan');
            $select.html('<option value="">Todos los planos</option>');
            plans.forEach(function (plan) {
                const label = plan.name_floor_plan + ' - ' + plan.name_campus + ' / Piso ' + plan.floor_floor_plan;
                $select.append('<option value="' + plan.id_floor_plan + '">' + escapeHtml(label) + '</option>');
            });
            loadLocations();
        });
    }

    function loadLocations() {
        const idPlan = $('#filter-floor-plan').val();
        $.post(baseUrl + '/movement/movements_asset/show_asset_locations', {
            id_floor_plan: idPlan
        }, function (response) {
            const data = parseJson(response);
            if (!data.status) {
                showMessage(data.type || 'error', data.message || 'No se pudieron cargar las ubicaciones.');
                return;
            }
            currentLocations = data.result || [];
            buildFilterOptions();
            renderMarkers();
        });
    }

    function buildFilterOptions() {
        fillSelect('#filter-campus',uniqueBy(currentLocations,'name_campus'),'name_campus');
        fillSelect('#filter-floor',uniqueBy(currentLocations.map(row=>({...row,floor_space:row.floor_space===null?'':String(row.floor_space)})),'floor_space'),'floor_space');
        fillSelect('#filter-type-asset', uniqueBy(currentLocations, 'name_type_asset'), 'name_type_asset');
        fillSelect('#filter-category-asset', uniqueBy(currentLocations, 'name_category_asset'), 'name_category_asset');
        fillSelect('#filter-space', uniqueBy(currentLocations, 'name_space'), 'name_space');
        fillSelect('#filter-status', uniqueBy(currentLocations, 'name_condition_asset'), 'name_condition_asset');
    }

    function fillSelect(selector, rows, key) {
        const current = $(selector).val();
        const $select = $(selector);
        const defaultText = selector === '#filter-category-asset' ? 'Todas' : 'Todos';
        $select.html('<option value="">' + defaultText + '</option>');
        rows.forEach(function (row) {
            const value = row[key] || '';
            if (value) {
                $select.append('<option value="' + escapeHtml(value) + '">' + escapeHtml(value) + '</option>');
            }
        });
        if (current) {
            $select.val(current);
        }
    }

    function renderMarkers() {
        const $container = $('#asset-location-markers');
        $container.empty();
        const listRows = currentLocations.filter(function (item) {
            return matches('#filter-campus',item.name_campus) && matches('#filter-floor',item.floor_space===null?'':String(item.floor_space)) && matches('#filter-type-asset', item.name_type_asset)
                && matches('#filter-category-asset', item.name_category_asset)
                && matches('#filter-space', item.name_space)
                && matches('#filter-status', item.name_condition_asset);
        });
        locationTable.clear().rows.add(listRows).draw();
        const filtered=listRows.filter(row=>$('#filter-floor-plan').val() && String(row.id_floor_plan)===String($('#filter-floor-plan').val()) && row.coordinate_x_movement_asset!==null && row.coordinate_y_movement_asset!==null);
        $('#asset-location-empty').toggleClass('d-none', filtered.length > 0 && $('#filter-floor-plan').val());
        $('#asset-location-summary').text($('#filter-floor-plan').val() ? filtered.length + ' activo(s) ubicados en el plano seleccionado.' : 'Selecciona un plano para visualizar los activos con coordenadas.');
        const labelPositions = calculateLabelPositions(filtered);
        filtered.forEach(function (item, index) {
            const marker = $('<button type="button" class="asset-location-marker" aria-label="Ver detalle de ubicacion del activo"><span class="asset-location-marker-line"></span><span class="asset-location-marker-point"><i class="bx bx-map-pin"></i></span><span class="asset-location-marker-label"></span></button>');
            const labelPosition = labelPositions[index] || getLabelMetrics({ x: 0, y: 42 });
            marker.css({
                left: parseFloat(item.coordinate_x_movement_asset) + '%',
                top: parseFloat(item.coordinate_y_movement_asset) + '%',
                '--label-x': labelPosition.x + 'px',
                '--label-y': labelPosition.y + 'px',
                '--line-length': labelPosition.lineLength + 'px',
                '--line-angle': labelPosition.angle + 'deg'
            });
            marker.find('.asset-location-marker-label').text(item.name_type_asset || 'Activo');
            marker.on('click', function () {
                showLocationDetail(item);
            });
            $container.append(marker);
        });
    }

    function calculateLabelPositions(rows) {
        const mapWidth = Math.max(1, $('#asset-location-map').innerWidth() || 1);
        const mapHeight = Math.max(1, $('#asset-location-map').innerHeight() || 1);
        const candidates = [
            { x: -92, y: -44 },
            { x: 92, y: -44 },
            { x: -92, y: 44 },
            { x: 92, y: 44 },
            { x: 0, y: -62 },
            { x: 0, y: 62 },
            { x: -132, y: 0 },
            { x: 132, y: 0 },
            { x: -142, y: -72 },
            { x: 142, y: -72 },
            { x: -142, y: 72 },
            { x: 142, y: 72 }
        ];
        const boxes = [];
        const assigned = [];
        rows.forEach(function (item, index) {
            const x = parseFloat(item.coordinate_x_movement_asset);
            const y = parseFloat(item.coordinate_y_movement_asset);
            const pointX = Number.isNaN(x) ? 0 : (x / 100) * mapWidth;
            const pointY = Number.isNaN(y) ? 0 : (y / 100) * mapHeight;
            const labelText = item.name_type_asset || 'Activo';
            const labelWidth = Math.min(180, Math.max(72, (labelText.length * 7) + 28));
            const labelHeight = labelText.length > 22 ? 42 : 30;
            let selected = null;
            let selectedBox = null;

            for (let i = 0; i < candidates.length; i++) {
                const candidate = candidates[(index + i) % candidates.length];
                const centerX = pointX + candidate.x;
                const centerY = pointY + candidate.y;
                const box = {
                    left: centerX - (labelWidth / 2),
                    right: centerX + (labelWidth / 2),
                    top: centerY - (labelHeight / 2),
                    bottom: centerY + (labelHeight / 2)
                };
                if (box.left < 4 || box.right > mapWidth - 4 || box.top < 4 || box.bottom > mapHeight - 4) {
                    continue;
                }
                if (!boxes.some(existing => boxesOverlap(existing, box))) {
                    selected = candidate;
                    selectedBox = box;
                    break;
                }
            }

            if (!selected) {
                selected = candidates[index % candidates.length];
                const centerX = pointX + selected.x;
                const centerY = pointY + selected.y;
                selectedBox = {
                    left: centerX - (labelWidth / 2),
                    right: centerX + (labelWidth / 2),
                    top: centerY - (labelHeight / 2),
                    bottom: centerY + (labelHeight / 2)
                };
            }

            boxes.push(selectedBox);
            assigned[index] = getLabelMetrics(selected);
        });
        return assigned;
    }

    function getLabelMetrics(candidate) {
        const linePadding = 13;
        const distance = Math.max(1, Math.sqrt((candidate.x * candidate.x) + (candidate.y * candidate.y)) - linePadding);
        const angle = Math.atan2(candidate.y, candidate.x) * (180 / Math.PI);
        return {
            x: candidate.x,
            y: candidate.y,
            lineLength: Math.round(distance),
            angle: Math.round(angle * 100) / 100
        };
    }

    function boxesOverlap(a, b) {
        const gap = 8;
        return !(a.right + gap < b.left || a.left - gap > b.right || a.bottom + gap < b.top || a.top - gap > b.bottom);
    }

    function setPlan(plan) {
        if (!plan) {
            $('#img-asset-location-plan').attr('src', '').addClass('d-none');
            $('#asset-location-plan-title').text('Plano no seleccionado');
            $('#asset-location-summary').text('Seleccione un plano para visualizar activos ubicados.');
            $('#asset-location-empty').removeClass('d-none').text('Seleccione un plano para visualizar los marcadores.');
            return;
        }
        $('#img-asset-location-plan').attr('src', buildUrl(plan.current_image_floor_plan || plan.current_file_floor_plan)).removeClass('d-none');
        $('#asset-location-plan-title').text(plan.name_floor_plan + ' - ' + plan.name_campus + ' / Piso ' + plan.floor_floor_plan);
        $('#asset-location-empty').text('No hay activos ubicados en este plano con los filtros actuales.');
    }

    function showLocationDetail(item) {
        $('#asset-location-detail-code').text(item.code_inventory_asset || '-');
        $('#asset-location-detail-type').text(item.name_type_asset || '-');
        $('#asset-location-detail-category').text(item.name_category_asset || '-');
        $('#asset-location-detail-status').text(item.situation || '-');
        $('#asset-location-history').attr('href',baseUrl+'/movement/movements_asset/history_movement_asset/'+Number(item.id_asset));
        $('#asset-location-move').attr('href',baseUrl+'/movement/movements_asset/manage_movements_asset?asset='+Number(item.id_asset));
        $('#asset-location-detail-space').text(item.name_space || '-');
        $('#asset-location-detail-date').text(item.date_event_movement_asset || item.date_created_movement_asset || '-');
        $('#asset-location-detail-coordinate').text(formatCoordinate(item.coordinate_x_movement_asset, item.coordinate_y_movement_asset));
        const modal = new bootstrap.Modal(document.getElementById('ModalAssetLocationDetail'));
        modal.show();
    }

    function formatCoordinate(x, y) {
        const safeX = parseFloat(x);
        const safeY = parseFloat(y);
        if (Number.isNaN(safeX) || Number.isNaN(safeY)) {
            return 'No definida';
        }
        return 'X ' + safeX.toFixed(2) + '% / Y ' + safeY.toFixed(2) + '%';
    }

    function matches(selector, value) {
        const selected = $(selector).val();
        return !selected || String(value ?? '') === String(selected);
    }

    function uniqueBy(rows, key) {
        const seen = {};
        return rows.filter(function (row) {
            const value = row[key] || '';
            if (!value || seen[value]) {
                return false;
            }
            seen[value] = true;
            return true;
        });
    }

    function buildUrl(route) {
        const clean = (route || '').toString().trim();
        if (!clean) {
            return 'about:blank';
        }
        if (clean.startsWith('http://') || clean.startsWith('https://')) {
            return clean;
        }
        return baseUrl + '/' + clean.replace(/^\/+/, '');
    }

    function parseJson(response) {
        if (typeof response === 'object') {
            return response;
        }
        try {
            return JSON.parse(response);
        } catch (error) {
            return { status: false, type: 'error', message: 'Respuesta invalida del servidor.' };
        }
    }
});

function escapeHtml(value) {
    return (value || '').toString()
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
