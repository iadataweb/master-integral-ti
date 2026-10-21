<!DOCTYPE html>
<html lang="es">

<?php require('Layouts/head.php'); ?>

<body>

    <?php require('Layouts/sidebar.php'); ?>

    <main class="content">

        <?php require('Layouts/header.php'); ?>

        <link rel="stylesheet" href="<?= assets(); ?>/css/components/dashboard-index.css?v=<?= filemtime(dirname(__DIR__, 2) . '/Assets/css/components/dashboard-index.css'); ?>">

        <div class="dashboard-page">
        <section class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4 page-heading-actions">
            <div class="d-block mb-4 mb-md-0">
                <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                    <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                        <li class="breadcrumb-item"><span><i class="bx bx-home-alt"></i></span></li>
                        <li class="breadcrumb-item active" aria-current="page">Inicio</li>
                    </ol>
                </nav>
                <h1 class="h4">Panel de control</h1>
                <p class="mb-0">Consulta el estado de los activos y el trabajo de TI.</p>
            </div>
            <div class="btn-toolbar mb-2 mb-md-0 page-action-toolbar">
                <button type="button" class="btn btn-tertiary" id="btnPrintDashboardReport"><i class="bx bx-printer me-1"></i> Generar PDF</button>
            </div>
        </section>

        <section class="dashboard-filter-card">
            <div class="dashboard-filter-layout">
                <div class="dashboard-date-field">
                    <label for="filterDateStartDashboard" class="form-label">Fecha inicio</label>
                    <input type="date" class="form-control" id="filterDateStartDashboard" name="date_start">
                </div>
                <div class="dashboard-date-field">
                    <label for="filterDateEndDashboard" class="form-label">Fecha fin</label>
                    <input type="date" class="form-control" id="filterDateEndDashboard" name="date_end">
                </div>
                <div class="dashboard-actions dashboard-filter-actions">
                <button type="button" class="btn btn-outline-secondary" id="btnResetDashboardFilters"><i class="bx bx-refresh me-1"></i> Restablecer</button>
                <button type="button" class="btn btn-primary" id="btnApplyDashboardFilters"><i class="bx bx-filter-alt me-1"></i> Aplicar filtros</button>
                </div>
            </div>
        </section>

        <section class="metric-grid" aria-label="Resumen del sistema">
            <article class="metric-card">
                <div class="metric-kicker">Activos</div>
                <div class="metric-value" id="metricAssets">0</div>
                <span class="metric-hint">Activos en inventario.</span>
            </article>
            <article class="metric-card">
                <div class="metric-kicker">Movimientos</div>
                <div class="metric-value" id="metricMovements">0</div>
                <span class="metric-hint">Movimientos registrados.</span>
            </article>
            <article class="metric-card">
                <div class="metric-kicker">Incidencias</div>
                <div class="metric-value" id="metricIncidents">0</div>
                <span class="metric-hint"><span id="metricOpenIncidents">0</span> abiertas.</span>
            </article>
            <article class="metric-card">
                <div class="metric-kicker">Preventivos</div>
                <div class="metric-value" id="metricPreventive">0</div>
                <span class="metric-hint">Mantenimientos preventivos.</span>
            </article>
            <article class="metric-card">
                <div class="metric-kicker">Correctivos</div>
                <div class="metric-value" id="metricCorrective">0</div>
                <span class="metric-hint">Mantenimientos correctivos.</span>
            </article>
            <article class="metric-card">
                <div class="metric-kicker">Repuestos</div>
                <div class="metric-value" id="metricSpares">0</div>
                <span class="metric-hint">Repuestos registrados.</span>
            </article>
            <article class="metric-card">
                <div class="metric-kicker">Stock bajo</div>
                <div class="metric-value" id="metricLowStock">0</div>
                <span class="metric-hint">Por debajo del mínimo.</span>
            </article>
            <article class="metric-card">
                <div class="metric-kicker">Incidencias cerradas</div>
                <div class="metric-value" id="metricClosedIncidents">0</div>
                <span class="metric-hint">Casos resueltos.</span>
            </article>
        </section>

        <section class="row g-4 mb-4" aria-label="Activos en almacén">
            <div class="col-12">
                <article class="dashboard-card">
                    <div class="dashboard-card-header">
                        <div>
                            <h2 class="h5 dashboard-card-title">Activos en almacén</h2>
                            <p class="dashboard-card-subtitle">Cantidad por tipo, condición y disponibilidad.</p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table dashboard-table">
                            <thead>
                                <tr>
                                    <th>#</th><th>Tipo de activo</th>
                                    <th>Total almacen</th>
                                    <th>Disponibles</th>
                                    <th>Operativo</th>
                                    <th>Inoper.</th>
                                    <th>Mant.</th>
                                    <th>Baja</th>
                                    <th>Otros</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyStoredAssetsStatusDashboard">
                                <tr><td colspan="9" class="empty-dashboard-row">Cargando...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>
        </section>

        <section class="row g-4 mb-4" aria-label="Inventario por tipo y ambiente">
            <div class="col-12 col-xl-7">
                <article class="dashboard-card">
                    <div class="dashboard-card-header">
                        <div>
                            <h2 class="h5 dashboard-card-title">Estado del inventario</h2>
                            <p class="dashboard-card-subtitle">Cantidad de activos por tipo y situación actual.</p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table dashboard-table">
                            <thead>
                                <tr>
                                    <th>#</th><th>Tipo de activo</th>
                                    <th>Total</th>
                                    <th>Almacen</th>
                                    <th>Asignados</th>
                                    <th>Transferidos</th>
                                    <th>Prestados</th>
                                    <th>Mant./otros</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyAssetTypeFlowDashboard">
                                <tr><td colspan="8" class="empty-dashboard-row">Cargando...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>
            <div class="col-12 col-xl-5">
                <article class="dashboard-card">
                    <div class="dashboard-card-header">
                        <div>
                            <h2 class="h5 dashboard-card-title">Activos por ambiente</h2>
                            <p class="dashboard-card-subtitle">Ambientes con más activos.</p>
                        </div>
                    </div>
                    <div id="chartAssetsEnvironment" class="dashboard-chart"></div>
                </article>
            </div>
            <div class="col-12">
                <article class="dashboard-card">
                    <div class="dashboard-card-header">
                        <div>
                            <h2 class="h5 dashboard-card-title">Distribución por ambiente</h2>
                            <p class="dashboard-card-subtitle">Cantidad de activos en cada ambiente.</p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table dashboard-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Ambiente</th>
                                    <th>Piso</th>
                                    <th>Total de equipos</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyAssetsEnvironmentDashboard">
                                <tr><td colspan="4" class="empty-dashboard-row">Cargando...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>
            <div class="col-12">
                <article class="dashboard-card">
                    <div class="dashboard-card-header"><div>
                        <h2 class="h5 dashboard-card-title">Distribución por piso</h2>
                        <p class="dashboard-card-subtitle">Cantidad de activos en cada piso. Sin ubicación: «Sin piso definido».</p>
                    </div></div>
                    <div class="table-responsive"><table class="table dashboard-table">
                        <thead><tr><th>#</th><th>Piso</th><th>Total de activos</th></tr></thead>
                        <tbody id="tbodyAssetsFloorDashboard"><tr><td colspan="3" class="empty-dashboard-row">Cargando...</td></tr></tbody>
                    </table></div>
                </article>
            </div>
            <div class="col-12 col-xl-7">
                <article class="dashboard-card">
                    <div class="dashboard-card-header">
                        <div>
                            <h2 class="h5 dashboard-card-title">Condición de los activos</h2>
                            <p class="dashboard-card-subtitle">Cantidad por tipo y condición técnica.</p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table dashboard-table">
                            <thead>
                                <tr>
                                    <th>#</th><th>Tipo de activo</th>
                                    <th>Total</th>
                                    <th>Operativo</th>
                                    <th>Inoper.</th>
                                    <th>Mant.</th>
                                    <th>Baja</th>
                                    <th>Otros</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyAssetTypeConditionDashboard">
                                <tr><td colspan="8" class="empty-dashboard-row">Cargando...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>
            <div class="col-12 col-xl-5">
                <article class="dashboard-card">
                    <div class="dashboard-card-header">
                        <div>
                            <h2 class="h5 dashboard-card-title">Activos sin ubicación</h2>
                            <p class="dashboard-card-subtitle">Activos pendientes de registrar su ubicación.</p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table dashboard-table">
                            <thead>
                                <tr>
                                    <th>#</th><th>Codigo</th>
                                    <th>Tipo</th>
                                    <th>Condicion</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyAssetsWithoutMovementDashboard">
                                <tr><td colspan="4" class="empty-dashboard-row">Cargando...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>
        </section>

        <section class="row g-4">
            <div class="col-12 col-xl-8">
                <article class="dashboard-card">
                    <div class="dashboard-card-header">
                        <div>
                            <h2 class="h5 dashboard-card-title">Actividad mensual</h2>
                            <p class="dashboard-card-subtitle">Incidencias, movimientos y reparaciones por mes.</p>
                        </div>
                    </div>
                    <div id="chartMonthlyActivity" class="dashboard-chart"></div>
                </article>
            </div>
            <div class="col-12 col-xl-4">
                <article class="dashboard-card">
                    <div class="dashboard-card-header">
                        <div>
                            <h2 class="h5 dashboard-card-title">Incidencias por estado</h2>
                            <p class="dashboard-card-subtitle">Cantidad de incidencias en cada estado.</p>
                        </div>
                    </div>
                    <div id="chartIncidentsStatus" class="dashboard-chart"></div>
                </article>
            </div>
            <div class="col-12 col-lg-6">
                <article class="dashboard-card">
                    <div class="dashboard-card-header">
                        <div>
                            <h2 class="h5 dashboard-card-title">Activos por categoría</h2>
                            <p class="dashboard-card-subtitle">Cantidad de activos en cada categoría.</p>
                        </div>
                    </div>
                    <div id="chartAssetsCategory" class="dashboard-chart"></div>
                </article>
            </div>
            <div class="col-12 col-lg-6">
                <article class="dashboard-card">
                    <div class="dashboard-card-header">
                        <div>
                            <h2 class="h5 dashboard-card-title">Condición técnica</h2>
                            <p class="dashboard-card-subtitle">Cantidad de activos en cada condición.</p>
                        </div>
                    </div>
                    <div id="chartAssetConditions" class="dashboard-chart"></div>
                </article>
            </div>
            <div class="col-12 col-lg-6">
                <article class="dashboard-card">
                    <div class="dashboard-card-header">
                        <div>
                            <h2 class="h5 dashboard-card-title">Movimientos por tipo</h2>
                            <p class="dashboard-card-subtitle">Cantidad de movimientos de cada tipo.</p>
                        </div>
                    </div>
                    <div id="chartMovementsType" class="dashboard-chart"></div>
                </article>
            </div>
            <div class="col-12 col-lg-6">
                <article class="dashboard-card">
                    <div class="dashboard-card-header">
                        <div>
                            <h2 class="h5 dashboard-card-title">Incidencias por tipo</h2>
                            <p class="dashboard-card-subtitle">Cantidad de incidencias de cada tipo.</p>
                        </div>
                    </div>
                    <div id="chartIncidentsType" class="dashboard-chart"></div>
                </article>
            </div>
            <div class="col-12 col-lg-6">
                <article class="dashboard-card">
                    <div class="dashboard-card-header">
                        <div>
                            <h2 class="h5 dashboard-card-title">Mantenimientos por tipo</h2>
                            <p class="dashboard-card-subtitle">Cantidad de preventivos y correctivos.</p>
                        </div>
                    </div>
                    <div id="chartMaintenanceOverview" class="dashboard-chart"></div>
                </article>
            </div>
            <div class="col-12 col-lg-6">
                <article class="dashboard-card">
                    <div class="dashboard-card-header">
                        <div>
                            <h2 class="h5 dashboard-card-title">Stock de repuestos</h2>
                            <p class="dashboard-card-subtitle">Repuestos con mayor stock.</p>
                        </div>
                    </div>
                    <div id="chartSpareStock" class="dashboard-chart"></div>
                </article>
            </div>
            <div class="col-12 col-xl-5">
                <article class="dashboard-card">
                    <div class="dashboard-card-header">
                        <div>
                            <h2 class="h5 dashboard-card-title">Repuestos con stock bajo</h2>
                            <p class="dashboard-card-subtitle">Repuestos que alcanzaron el stock mínimo.</p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table dashboard-table">
                            <thead>
                                <tr>
                                    <th>#</th><th>Codigo</th>
                                    <th>Repuesto</th>
                                    <th>Stock</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyLowStockDashboard">
                                <tr><td colspan="4" class="empty-dashboard-row">Cargando...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>
            <div class="col-12 col-xl-7">
                <article class="dashboard-card">
                    <div class="dashboard-card-header">
                        <div>
                            <h2 class="h5 dashboard-card-title">Incidencias recientes</h2>
                            <p class="dashboard-card-subtitle">Últimas incidencias registradas.</p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table dashboard-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tipo</th>
                                    <th>Titulo</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyRecentIncidentsDashboard">
                                <tr><td colspan="5" class="empty-dashboard-row">Cargando...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>
        </section>
        </div>

        <?php require('Layouts/footer.php'); ?>
    </main>

    <?php require('Layouts/script.php'); ?>

</body>

</html>
