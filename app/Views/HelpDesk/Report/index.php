<!DOCTYPE html>
<html lang="en">

<?php require('Layouts/head.php'); ?>

<body>

  <?php require('Layouts/sidebar.php'); ?>

  <main class="content">

    <?php require('Layouts/header.php'); ?>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4">
      <div class="d-block mb-4 mb-md-0">
        <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
          <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
            <li class="breadcrumb-item">
              <span>
                <svg class="icon icon-xxs" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                  xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                  </path>
                </svg>
              </span>
            </li>
            <li class="breadcrumb-item">Incidencia</li>
            <li class="breadcrumb-item active" aria-current="page">Reporte</li>
          </ol>
        </nav>
        <h2 class="h4">Reporte</h2>
        <p class="mb-0">Consulta la información de las incidencias y prepara reportes para su seguimiento.</p>
      </div>
    </div>

    <div class="row">

        <div class="col-12">
            <form id="formMovementAsset">

                <div class="row">                        
                    <div class="col-12 mb-3">
                        <div class="form-group mandatory">
                            <label for="input-date-event-movement-asset" class="form-label">Fecha Inicial</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
                                </span>
                                <input data-datepicker="" class="form-control" id="input-date-start" type="text" placeholder="dd/mm/yyyy" name="date_event_movement_asset">                                               
                            </div>
                        </div>                            
                    </div>                    
                </div>

                <div class="row">                        
                    <div class="col-12 mb-3">
                        <div class="form-group mandatory">
                            <label for="input-date-event-movement-asset" class="form-label">Fecha Final</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
                                </span>
                                <input data-datepicker="" class="form-control" id="input-date-start" type="text" placeholder="dd/mm/yyyy" name="date_event_movement_asset">                                               
                            </div>
                        </div>                            
                    </div>                    
                </div>

            </form>
        </div>

        <div class="col-12 col-sm-6 col-xl-4 mb-4">
            <a href="<?= base_url(); ?>/help_desk/subcategories/manage_subcategories" class="custom-btn-parameter">
                <div class="card text-center shadow-sm h-100 border-0">
                    <div class="card-body">
                        <svg class="icon icon-md" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M2 3a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H2Z"></path>
                            <path clip-rule="evenodd" fill-rule="evenodd" d="M2 7.5h16l-.811 7.71a2 2 0 0 1-1.99 1.79H4.802a2 2 0 0 1-1.99-1.79L2 7.5ZM7 11a1 1 0 0 1 1-1h4a1 1 0 1 1 0 2H8a1 1 0 0 1-1-1Z"></path>
                        </svg>
                        <h6 class="mt-3">Subcategorías de Incidencia</h6>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-4 mb-4">
            <a href="<?= base_url(); ?>/help_desk/priorities/manage_priorities" class="custom-btn-parameter">
                <div class="card text-center shadow-sm h-100 border-0">
                    <div class="card-body">
                        <svg class="icon icon-md" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path clip-rule="evenodd" fill-rule="evenodd" d="M4.5 2A2.5 2.5 0 0 0 2 4.5v3.879a2.5 2.5 0 0 0 .732 1.767l7.5 7.5a2.5 2.5 0 0 0 3.536 0l3.878-3.878a2.5 2.5 0 0 0 0-3.536l-7.5-7.5A2.5 2.5 0 0 0 8.38 2H4.5ZM5 6a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z"></path>
                        </svg>
                        <h6 class="mt-3">Prioridad de Incidencia</h6>
                    </div>
                </div>
            </a>
        </div>

    </div>

    <?php require('Layouts/footer.php'); ?>

  </main>

  <?php require('Layouts/script.php'); ?>

</body>

</html>