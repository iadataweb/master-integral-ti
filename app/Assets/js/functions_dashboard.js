const base_url = document.querySelector('meta[name="base-url"]').getAttribute('content');
const dashboardCharts = {};

const dashboardColors = ['#2563eb', '#0f766e', '#e11d48', '#b45309', '#7c3aed', '#0891b2', '#16a34a', '#f97316'];
const dashboardChartFont = 'Manrope, Segoe UI, sans-serif';

document.addEventListener('DOMContentLoaded', async () => {
  bindDashboardActions();
  await loadDashboardData();
});

function bindDashboardActions() {
  const applyButton = document.getElementById('btnApplyDashboardFilters');
  const resetButton = document.getElementById('btnResetDashboardFilters');
  const printButton = document.getElementById('btnPrintDashboardReport');

  applyButton?.addEventListener('click', loadDashboardData);
  resetButton?.addEventListener('click', () => {
    ['filterDateStartDashboard', 'filterDateEndDashboard'].forEach((id) => {
      const input = document.getElementById(id);
      if (input) input.value = '';
    });
    loadDashboardData();
  });
  printButton?.addEventListener('click', () => {
    const params = new URLSearchParams(getDashboardFilters());
    window.open(`${base_url}/dashboard/print_report?${params.toString()}`, '_blank');
  });
}

async function loadDashboardData() {
  setDashboardLoading(true);
  try {
    const response = await fetch(`${base_url}/dashboard/dashboard_data`, {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
      body: new URLSearchParams(getDashboardFilters()).toString(),
    });
    const payload = await response.json();
    if (!payload.status) {
      notifyDashboard(payload.message || 'No se pudo cargar la informacion del dashboard.');
      return;
    }

    renderDashboard(payload.result);
  } catch (error) {
    console.error('Error cargando dashboard.', error);
    notifyDashboard('No se pudo cargar la informacion del dashboard.');
  } finally {
    setDashboardLoading(false);
  }
}

function getDashboardFilters() {
  return {
    date_start: document.getElementById('filterDateStartDashboard')?.value || '',
    date_end: document.getElementById('filterDateEndDashboard')?.value || '',
  };
}

function renderDashboard(data) {
  renderMetrics(data.summary || {});
  renderCharts(data.charts || {});
  renderStoredAssetsStatusTable(data.tables?.stored_assets_by_type_status || []);
  renderAssetTypeFlowTable(data.tables?.assets_by_type_flow || []);
  renderAssetTypeConditionTable(data.tables?.assets_by_type_condition || []);
  renderAssetsEnvironmentTable(data.tables?.assets_by_environment || []);
  renderAssetsFloorTable(data.tables?.assets_by_floor || []);
  renderAssetsWithoutMovementTable(data.tables?.assets_without_movement || []);
  renderLowStockTable(data.tables?.low_stock || []);
  renderRecentIncidentsTable(data.tables?.recent_incidents || []);
}

function renderMetrics(summary) {
  setText('metricAssets', numberFormat(summary.assets));
  setText('metricMovements', numberFormat(summary.movements));
  setText('metricIncidents', numberFormat(summary.incidents));
  setText('metricOpenIncidents', numberFormat(summary.open_incidents));
  setText('metricClosedIncidents', numberFormat(summary.closed_incidents));
  setText('metricPreventive', numberFormat(summary.preventive));
  setText('metricCorrective', numberFormat(summary.corrective));
  setText('metricSpares', numberFormat(summary.spares));
  setText('metricLowStock', numberFormat(summary.low_stock));
}

function renderCharts(charts) {
  const theme = getChartTheme();

  renderApexChart('chartMonthlyActivity', {
    ...getBaseChartOptions(theme, 'area', 330),
    series: charts.monthly_activity?.series || [],
    colors: ['#2563eb', '#0f766e', '#7c3aed'],
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 3 },
    markers: { size: 3, strokeWidth: 0, hover: { size: 5 } },
    fill: { type: 'gradient', gradient: { shadeIntensity: .45, opacityFrom: 0.32, opacityTo: 0.04 } },
    xaxis: {
      categories: charts.monthly_activity?.labels || [],
      labels: { style: { colors: theme.axisLabelColors, fontSize: '12px', fontWeight: 600 } },
      axisBorder: { color: theme.axisBorderColor },
      axisTicks: { color: theme.axisBorderColor },
    },
    yaxis: {
      min: 0,
      forceNiceScale: true,
      labels: { formatter: integerLabel, style: { colors: theme.axisLabelColors, fontSize: '12px', fontWeight: 600 } },
    },
    grid: theme.grid,
    legend: getLegendOptions(theme),
    tooltip: { theme: theme.tooltip },
    noData: { text: 'Sin datos' },
  });

  renderBarChart('chartAssetsEnvironment', charts.assets_by_environment, true, theme, 'Equipos');
  renderAssetsCategoryChart('chartAssetsCategory', charts.assets_by_category, theme);
  renderDonutChart('chartAssetConditions', charts.asset_conditions, theme);
  renderBarChart('chartMovementsType', charts.movements_by_type, true, theme);
  renderDonutChart('chartIncidentsStatus', charts.incidents_by_status, theme);
  renderBarChart('chartIncidentsType', charts.incidents_by_type, false, theme);
  renderBarChart('chartMaintenanceOverview', charts.maintenance_overview, true, theme);
  renderBarChart('chartSpareStock', charts.spare_stock, true, theme, 'Stock');
}

function renderAssetsCategoryChart(elementId, chartData, theme) {
  const prepared = prepareChartData(chartData);
  const chartHeight = Math.max(315, prepared.labels.length * 42 + 82);

  renderApexChart(elementId, {
    ...getBaseChartOptions(theme, 'bar', chartHeight),
    series: [{ name: 'Activos', data: prepared.values }],
    colors: ['#2563eb'],
    plotOptions: {
      bar: {
        horizontal: true,
        borderRadius: 7,
        barHeight: '56%',
        dataLabels: { position: 'right' },
      },
    },
    dataLabels: {
      enabled: prepared.values.some((value) => value > 0),
      formatter: integerLabel,
      offsetX: 8,
      style: { fontWeight: 800, colors: [theme.dataLabelColor] },
    },
    xaxis: {
      categories: prepared.labels,
      min: 0,
      forceNiceScale: true,
      labels: {
        formatter: integerLabel,
        style: { colors: theme.axisLabelColors },
      },
      axisBorder: { color: theme.axisBorderColor },
      axisTicks: { color: theme.axisBorderColor },
    },
    yaxis: {
      labels: {
        minWidth: 165,
        maxWidth: 235,
        style: { colors: theme.axisLabelColors, fontSize: '12px', fontWeight: 700 },
      },
    },
    grid: theme.grid,
    tooltip: {
      theme: theme.tooltip,
      y: { formatter: (value) => `${integerLabel(value)} activos` },
    },
    noData: { text: 'Sin datos' },
  });
}
function renderBarChart(elementId, chartData, horizontal, theme, seriesName = 'Total') {
  const prepared = prepareChartData(chartData);
  renderApexChart(elementId, {
    ...getBaseChartOptions(theme, 'bar', 320),
    series: [{ name: seriesName, data: prepared.values }],
    colors: ['#2563eb'],
    plotOptions: {
      bar: {
        horizontal,
        borderRadius: 7,
        columnWidth: '52%',
        barHeight: '58%',
      },
    },
    dataLabels: {
      enabled: horizontal && prepared.values.length <= 8,
      formatter: integerLabel,
      offsetX: 8,
      style: { fontWeight: 800, colors: [theme.dataLabelColor] },
    },
    xaxis: {
      categories: prepared.labels,
      labels: {
        rotate: horizontal ? 0 : -18,
        trim: true,
        style: { colors: theme.axisLabelColors, fontSize: '12px', fontWeight: 600 },
      },
      axisBorder: { color: theme.axisBorderColor },
      axisTicks: { color: theme.axisBorderColor },
    },
    yaxis: {
      labels: {
        formatter: horizontal ? truncateLabel : integerLabel,
        style: { colors: theme.axisLabelColors, fontSize: '12px', fontWeight: 600 },
      },
    },
    grid: theme.grid,
    tooltip: { theme: theme.tooltip, y: { formatter: integerLabel } },
    noData: { text: 'Sin datos' },
  });
}

function renderDonutChart(elementId, chartData, theme) {
  const prepared = prepareChartData(chartData);
  renderApexChart(elementId, {
    ...getBaseChartOptions(theme, 'donut', 320),
    series: prepared.values,
    labels: prepared.labels,
    colors: dashboardColors,
    dataLabels: {
      enabled: true,
      formatter: (value) => `${value.toFixed(0)}%`,
      style: { fontWeight: 800, colors: ['#ffffff'] },
      dropShadow: { enabled: false },
    },
    legend: getLegendOptions(theme, 'bottom'),
    stroke: { width: 2, colors: [theme.cardColor] },
    plotOptions: {
      pie: {
        donut: {
          size: '70%',
          labels: {
            show: true,
            name: { color: theme.mutedColor, fontSize: '13px', fontWeight: 700, offsetY: -6 },
            value: { color: theme.foreColor, fontSize: '24px', fontWeight: 850, formatter: integerLabel },
            total: { show: true, label: 'Total', color: theme.mutedColor, fontSize: '13px', fontWeight: 700 },
          },
        },
      },
    },
    tooltip: { theme: theme.tooltip, y: { formatter: integerLabel } },
    noData: { text: 'Sin datos' },
  });
}

function getBaseChartOptions(theme, type, height) {
  return {
    chart: {
      type,
      height,
      foreColor: theme.foreColor,
      fontFamily: dashboardChartFont,
      toolbar: { show: false },
      zoom: { enabled: false },
      animations: {
        enabled: true,
        easing: 'easeinout',
        speed: 450,
        animateGradually: { enabled: true, delay: 80 },
      },
    },
  };
}

function getLegendOptions(theme, position = 'top') {
  return {
    position,
    horizontalAlign: position === 'top' ? 'right' : 'center',
    fontSize: '12px',
    fontWeight: 700,
    labels: { colors: theme.foreColor },
    markers: { width: 10, height: 10, radius: 999 },
    itemMargin: { horizontal: 10, vertical: 6 },
  };
}

function renderApexChart(elementId, options) {
  const element = document.getElementById(elementId);
  if (!element || typeof ApexCharts === 'undefined') return;
  if (dashboardCharts[elementId]) dashboardCharts[elementId].destroy();
  dashboardCharts[elementId] = new ApexCharts(element, options);
  dashboardCharts[elementId].render();
}

function prepareChartData(chartData) {
  const labels = Array.isArray(chartData?.labels) ? chartData.labels : [];
  const values = Array.isArray(chartData?.values) ? chartData.values.map((value) => Number(value) || 0) : [];
  if (!labels.length || !values.some((value) => value > 0)) {
    return { labels: ['Sin datos'], values: [0] };
  }
  return { labels, values };
}

function renderAssetTypeFlowTable(rows) {
  const tbody = document.getElementById('tbodyAssetTypeFlowDashboard');
  if (!tbody) return;
  if (!rows.length) {
    tbody.innerHTML = '<tr><td colspan="8" class="empty-dashboard-row">No se encontraron activos.</td></tr>';
    return;
  }
  tbody.innerHTML = rows.map((row) => `
    <tr>
      <td>${formatRecordId(row, 'id_type_asset')}</td>
      <td>${escapeHtml(row.type_asset || 'Sin tipo')}</td>
      <td><span class="dashboard-number">${numberFormat(row.total_assets)}</span></td>
      <td><span class="dashboard-status-pill">${numberFormat(row.available_storage)}</span></td>
      <td><span class="dashboard-status-pill">${numberFormat(row.assigned_use)}</span></td>
      <td><span class="dashboard-status-pill">${numberFormat(row.transferred_use)}</span></td>
      <td><span class="dashboard-status-pill">${numberFormat(row.loaned_use)}</span></td>
      <td><span class="dashboard-status-pill">${numberFormat(row.maintenance_other)}</span></td>
    </tr>
  `).join('');
}

function renderStoredAssetsStatusTable(rows) {
  const tbody = document.getElementById('tbodyStoredAssetsStatusDashboard');
  if (!tbody) return;
  if (!rows.length) {
    tbody.innerHTML = '<tr><td colspan="9" class="empty-dashboard-row">No se encontraron activos en almacén.</td></tr>';
    return;
  }
  tbody.innerHTML = rows.map((row) => `
    <tr>
      <td>${formatRecordId(row, 'id_type_asset')}</td>
      <td>${escapeHtml(row.type_asset || 'Sin tipo')}</td>
      <td><span class="dashboard-number">${numberFormat(row.total_storage)}</span></td>
      <td><span class="dashboard-status-pill">${numberFormat(row.available_storage)}</span></td>
      <td><span class="dashboard-status-pill">${numberFormat(row.operative_storage)}</span></td>
      <td><span class="dashboard-status-pill">${numberFormat(row.inoperative_storage)}</span></td>
      <td><span class="dashboard-status-pill">${numberFormat(row.maintenance_storage)}</span></td>
      <td><span class="dashboard-status-pill">${numberFormat(row.retired_storage)}</span></td>
      <td><span class="dashboard-status-pill">${numberFormat(row.other_storage)}</span></td>
    </tr>
  `).join('');
}

function renderAssetTypeConditionTable(rows) {
  const tbody = document.getElementById('tbodyAssetTypeConditionDashboard');
  if (!tbody) return;
  if (!rows.length) {
    tbody.innerHTML = '<tr><td colspan="8" class="empty-dashboard-row">No se encontraron activos.</td></tr>';
    return;
  }
  tbody.innerHTML = rows.map((row) => `
    <tr>
      <td>${formatRecordId(row, 'id_type_asset')}</td>
      <td>${escapeHtml(row.type_asset || 'Sin tipo')}</td>
      <td><span class="dashboard-number">${numberFormat(row.total_assets)}</span></td>
      <td><span class="dashboard-status-pill">${numberFormat(row.operative_assets)}</span></td>
      <td><span class="dashboard-status-pill">${numberFormat(row.inoperative_assets)}</span></td>
      <td><span class="dashboard-status-pill">${numberFormat(row.maintenance_assets)}</span></td>
      <td><span class="dashboard-status-pill">${numberFormat(row.retired_assets)}</span></td>
      <td><span class="dashboard-status-pill">${numberFormat(row.other_assets)}</span></td>
    </tr>
  `).join('');
}

function renderAssetsEnvironmentTable(rows) {
  const tbody = document.getElementById('tbodyAssetsEnvironmentDashboard');
  if (!tbody) return;
  if (!rows.length) {
    tbody.innerHTML = '<tr><td colspan="4" class="empty-dashboard-row">No se encontraron activos por ambiente.</td></tr>';
    return;
  }
  tbody.innerHTML = rows.map((row) => `
    <tr>
      <td>${escapeHtml(row.id_environment ?? '—')}</td>
      <td>${escapeHtml(row.name_environment || 'Sin ambiente registrado')}</td>
      <td>${row.floor_environment === null || row.floor_environment === undefined ? '-' : escapeHtml(row.floor_environment)}</td>
      <td><span class="dashboard-number">${numberFormat(row.total_assets)}</span></td>
    </tr>
  `).join('');
}

function renderAssetsFloorTable(rows) {
  const tbody = document.getElementById('tbodyAssetsFloorDashboard');
  if (!tbody) return;
  if (!rows.length) {
    tbody.innerHTML = '<tr><td colspan="3" class="empty-dashboard-row">No se encontraron activos por piso.</td></tr>';
    return;
  }
  tbody.innerHTML = rows.map((row, index) => `<tr>
    <td>${index + 1}</td>
    <td>${row.floor_environment === null || row.floor_environment === undefined ? 'Sin piso definido' : 'Piso ' + escapeHtml(row.floor_environment)}</td>
    <td><span class="dashboard-number">${numberFormat(row.total_assets)}</span></td>
  </tr>`).join('');
}

function renderAssetsWithoutMovementTable(rows) {
  const tbody = document.getElementById('tbodyAssetsWithoutMovementDashboard');
  if (!tbody) return;
  if (!rows.length) {
    tbody.innerHTML = '<tr><td colspan="4" class="empty-dashboard-row">No hay activos sin ubicación.</td></tr>';
    return;
  }
  tbody.innerHTML = rows.map((row) => `
    <tr>
      <td>${formatRecordId(row, 'id_asset')}</td>
      <td>${escapeHtml(row.code_inventory_asset || '-')}</td>
      <td>${escapeHtml(row.name_type_asset || 'Sin tipo')}</td>
      <td><span class="badge bg-warning text-dark">${escapeHtml(row.name_condition_asset || 'Sin condicion')}</span></td>
    </tr>
  `).join('');
}

function renderLowStockTable(rows) {
  const tbody = document.getElementById('tbodyLowStockDashboard');
  if (!tbody) return;
  if (!rows.length) {
    tbody.innerHTML = '<tr><td colspan="4" class="empty-dashboard-row">No hay repuestos con stock bajo.</td></tr>';
    return;
  }
  tbody.innerHTML = rows.map((row) => `
    <tr>
      <td>${formatRecordId(row, 'id_spare_part')}</td>
      <td>${escapeHtml(row.code_spare_part || '-')}</td>
      <td>${escapeHtml(row.name_spare_part || '-')}</td>
      <td><span class="badge bg-danger">${escapeHtml(row.stock_current_spare_part ?? 0)} / min. ${escapeHtml(row.stock_minimum_spare_part ?? 0)} ${escapeHtml(row.unit_spare_part || '')}</span></td>
    </tr>
  `).join('');
}

function renderRecentIncidentsTable(rows) {
  const tbody = document.getElementById('tbodyRecentIncidentsDashboard');
  if (!tbody) return;
  if (!rows.length) {
    tbody.innerHTML = '<tr><td colspan="5" class="empty-dashboard-row">No hay incidencias recientes.</td></tr>';
    return;
  }
  tbody.innerHTML = rows.map((row) => {
    const isOpen = Number(row.status_open_incident) === 1;
    const stateClass = isOpen ? 'bg-warning text-dark' : 'bg-success';
    const stateText = isOpen ? 'Abierta' : 'Cerrada';
    return `
      <tr>
        <td>${escapeHtml(row.id_incident || '-')}</td>
        <td>${escapeHtml(row.name_type_incident || 'Sin tipo')}</td>
        <td>${escapeHtml(row.title_incident || '-')}</td>
        <td><span class="badge ${stateClass}">${stateText}</span></td>
        <td>${formatDate(row.date_created_incident)}</td>
      </tr>
    `;
  }).join('');
}

function setDashboardLoading(isLoading) {
  const button = document.getElementById('btnApplyDashboardFilters');
  if (!button) return;
  button.disabled = isLoading;
  button.innerHTML = isLoading ? '<span class="spinner-border spinner-border-sm me-1"></span> Cargando' : '<i class="bx bx-filter-alt me-1"></i> Aplicar filtros';
}

function getChartTheme() {
  const dark = document.documentElement.classList.contains('theme-dark')
    || document.body.classList.contains('theme-dark')
    || document.documentElement.getAttribute('data-theme') === 'dark';
  const axisColor = dark ? '#dbeafe' : '#334155';
  return {
    foreColor: dark ? '#e2e8f0' : '#334155',
    mutedColor: dark ? '#c4cfdd' : '#607086',
    cardColor: dark ? '#171c24' : '#ffffff',
    dataLabelColor: dark ? '#f8fafc' : '#17212f',
    axisLabelColors: Array(20).fill(axisColor),
    axisBorderColor: dark ? 'rgba(226, 232, 240, .55)' : 'rgba(51, 65, 85, .45)',
    tooltip: dark ? 'dark' : 'light',
    grid: { borderColor: dark ? 'rgba(148, 163, 184, .16)' : 'rgba(148, 163, 184, .28)', strokeDashArray: 4 },
  };
}

function notifyDashboard(message) {
  if (typeof Swal !== 'undefined') {
    Swal.fire('Dashboard', message, 'warning');
    return;
  }
  console.warn(message);
}

function setText(id, value) {
  const element = document.getElementById(id);
  if (element) element.textContent = value ?? '0';
}

function numberFormat(value) {
  return new Intl.NumberFormat('es-PE').format(Number(value) || 0);
}

function integerLabel(value) {
  return Number(value || 0).toFixed(0);
}

function truncateLabel(value) {
  const text = String(value || '');
  return text.length > 22 ? `${text.substring(0, 22)}...` : text;
}

function formatDate(value) {
  if (!value) return '-';
  const date = new Date(String(value).replace(' ', 'T'));
  if (Number.isNaN(date.getTime())) return escapeHtml(value);
  return date.toLocaleDateString('es-PE', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

function escapeHtml(value) {
  return String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}
