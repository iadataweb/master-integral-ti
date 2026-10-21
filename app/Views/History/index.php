<!DOCTYPE html>
<html lang="es">

<?php require('Layouts/head.php'); ?>

<body>
    <?php require('Layouts/sidebar.php'); ?>

    <main class="content">
        <?php require('Layouts/header.php'); ?>

        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4 page-heading-actions">
            <div class="d-block mb-4 mb-md-0">
                <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                    <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                        <li class="breadcrumb-item"><span><i class="bx bx-home-alt"></i></span></li>
                        <li class="breadcrumb-item active" aria-current="page">Historial</li>
                    </ol>
                </nav>
                <h2 class="h4">Historial de Activos</h2>
                <p class="mb-0">Consulta la trazabilidad completa de inventario, movimientos, incidencias y mantenimientos por activo.</p>
            </div>
        </div>

        <section class="row g-3 mb-4 history-summary-row">
            <div class="col-12 col-md-4">
                <article class="history-summary-card">
                    <div>
                        <span class="history-summary-label">Activos</span>
                        <strong id="historyTotalAssets">0</strong>
                        <p>Activos registrados.</p>
                    </div>
                </article>
            </div>
            <div class="col-12 col-md-4">
                <article class="history-summary-card">
                    <div>
                        <span class="history-summary-label">Movimientos</span>
                        <strong id="historyTotalMovements">0</strong>
                        <p>Traslados y asignaciones.</p>
                    </div>
                </article>
            </div>
            <div class="col-12 col-md-4">
                <article class="history-summary-card">
                    <div>
                        <span class="history-summary-label">Eventos</span>
                        <strong id="historyTotalEvents">0</strong>
                        <p>Incidencias y mantenimientos.</p>
                    </div>
                </article>
            </div>
        </section>

        <div class="card card-body border-0 shadow table-wrapper table-responsive">
            <table id="tableHistoryAssets" class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th class="border-gray-200">#</th>
                        <th class="border-gray-200">Activo</th>
                        <th class="border-gray-200">Categoria</th>
                        <th class="border-gray-200">Ubicacion Actual</th>
                        <th class="border-gray-200">Eventos</th>
                        <th class="border-gray-200">Fecha Registro</th>
                        <th class="border-gray-200">Accion</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <?php require('Layouts/footer.php'); ?>
    </main>

    <?php require('Layouts/script.php'); ?>
</body>
</html>
