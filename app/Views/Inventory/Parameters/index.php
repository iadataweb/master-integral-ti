<!DOCTYPE html>
<html lang="en">

<?php require('Layouts/head.php'); ?>

<body>

  <?php require('Layouts/sidebar.php'); ?>

  <main class="content">

    <?php require('Layouts/header.php'); ?>

    <?php $parameter_cards = $data['parameter_cards'] ?? []; ?>

    <?php
      if (!function_exists('parameter_card_svg_icon')) {
        function parameter_card_svg_icon(string $icon): string
        {
          $icons = [
            'building' => '<svg class="icon icon-md" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path fill-rule="evenodd" d="M4 2a2 2 0 0 0-2 2v14h16V4a2 2 0 0 0-2-2H4Zm1.5 4a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 0 1.5h-1.5A.75.75 0 0 1 5.5 6Zm0 3a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 0 1.5h-1.5A.75.75 0 0 1 5.5 9Zm.75 2.25a.75.75 0 0 0 0 1.5h1.5a.75.75 0 0 0 0-1.5h-1.5ZM11.5 6a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 0 1.5h-1.5A.75.75 0 0 1 11.5 6Zm.75 2.25a.75.75 0 0 0 0 1.5h1.5a.75.75 0 0 0 0-1.5h-1.5Zm-.75 3.75a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 0 1.5h-1.5A.75.75 0 0 1 11.5 12Z" clip-rule="evenodd"/></svg>',
            'grid' => '<svg class="icon icon-md" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path clip-rule="evenodd" fill-rule="evenodd" d="M4.25 2A2.25 2.25 0 0 0 2 4.25v2.5A2.25 2.25 0 0 0 4.25 9h2.5A2.25 2.25 0 0 0 9 6.75v-2.5A2.25 2.25 0 0 0 6.75 2h-2.5Zm0 9A2.25 2.25 0 0 0 2 13.25v2.5A2.25 2.25 0 0 0 4.25 18h2.5A2.25 2.25 0 0 0 9 15.75v-2.5A2.25 2.25 0 0 0 6.75 11h-2.5Zm9-9A2.25 2.25 0 0 0 11 4.25v2.5A2.25 2.25 0 0 0 13.25 9h2.5A2.25 2.25 0 0 0 18 6.75v-2.5A2.25 2.25 0 0 0 15.75 2h-2.5Zm0 9A2.25 2.25 0 0 0 11 13.25v2.5A2.25 2.25 0 0 0 13.25 18h2.5A2.25 2.25 0 0 0 18 15.75v-2.5A2.25 2.25 0 0 0 15.75 11h-2.5Z"></path></svg>',
            'briefcase' => '<svg class="icon icon-md" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path fill-rule="evenodd" d="M6 4.5A2.5 2.5 0 0 1 8.5 2h3A2.5 2.5 0 0 1 14 4.5V5h2.25A1.75 1.75 0 0 1 18 6.75v7.5A1.75 1.75 0 0 1 16.25 16H3.75A1.75 1.75 0 0 1 2 14.25v-7.5A1.75 1.75 0 0 1 3.75 5H6v-.5Zm1.5.5h5v-.5a1 1 0 0 0-1-1h-3a1 1 0 0 0-1 1V5Z" clip-rule="evenodd"/></svg>',
            'shield' => '<svg class="icon icon-md" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path fill-rule="evenodd" d="M10 1.944 3.5 4.25v4.83c0 4.05 2.58 7.63 6.5 8.976 3.92-1.346 6.5-4.926 6.5-8.976V4.25L10 1.944Zm3.03 6.586a.75.75 0 0 0-1.06-1.06L9 10.44 8.03 9.47a.75.75 0 1 0-1.06 1.06l1.5 1.5c.293.293.767.293 1.06 0l3.5-3.5Z" clip-rule="evenodd"/></svg>',
            'mail' => '<svg class="icon icon-md" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M3 4a2 2 0 0 0-2 2v.217l9 5.4 9-5.4V6a2 2 0 0 0-2-2H3Z"></path><path d="m19 8.383-8.486 5.092a1 1 0 0 1-1.028 0L1 8.383V14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8.383Z"></path></svg>',
            'tag' => '<svg class="icon icon-md" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path clip-rule="evenodd" fill-rule="evenodd" d="M4.5 2A2.5 2.5 0 0 0 2 4.5v3.879a2.5 2.5 0 0 0 .732 1.767l7.5 7.5a2.5 2.5 0 0 0 3.536 0l3.878-3.878a2.5 2.5 0 0 0 0-3.536l-7.5-7.5A2.5 2.5 0 0 0 8.38 2H4.5ZM5 6a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z"></path></svg>',
            'archive' => '<svg class="icon icon-md" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M2 3a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H2Z"></path><path clip-rule="evenodd" fill-rule="evenodd" d="M2 7.5h16l-.811 7.71a2 2 0 0 1-1.99 1.79H4.802a2 2 0 0 1-1.99-1.79L2 7.5ZM7 11a1 1 0 0 1 1-1h4a1 1 0 1 1 0 2H8a1 1 0 0 1-1-1Z"></path></svg>',
          ];
          $key = 'grid';
          if (str_contains($icon, 'building') || str_contains($icon, 'map')) { $key = 'building'; }
          if (str_contains($icon, 'briefcase')) { $key = 'briefcase'; }
          if (str_contains($icon, 'shield') || str_contains($icon, 'lock') || str_contains($icon, 'cog')) { $key = 'shield'; }
          if (str_contains($icon, 'mail') || str_contains($icon, 'envelope')) { $key = 'mail'; }
          if (str_contains($icon, 'tag')) { $key = 'tag'; }
          if (str_contains($icon, 'package') || str_contains($icon, 'archive')) { $key = 'archive'; }
          return $icons[$key];
        }
      }
    ?>


    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4">
      <div class="d-block mb-4 mb-md-0">
        <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
          <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
            <li class="breadcrumb-item"><span><i class="bx bx-home"></i></span></li>
            <li class="breadcrumb-item">Inventario</li>
            <li class="breadcrumb-item active" aria-current="page">Parametros</li>
          </ol>
        </nav>
        <h2 class="h4">Parametros</h2>
        <p class="mb-0">Configura las categorías, tipos, marcas y características utilizadas en el inventario.</p>
      </div>
    </div>

    <?php if (!empty($parameter_cards)): ?>
      <div class="row">
        <?php foreach ($parameter_cards as $card): ?>
          <div class="col-12 col-sm-6 col-xl-4 mb-4">
            <a href="<?= base_url() . $card['url']; ?>" class="custom-btn-parameter">
              <div class="card text-center shadow-sm h-100 border-0">
                <div class="card-body">
                  <?= parameter_card_svg_icon((string)($card['icon'] ?? '')); ?>
                  <h6 class="mt-3"><?= htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8'); ?></h6>
                </div>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="card text-center shadow-sm border-0">
        <div class="card-body py-5">
          <i class="bx bx-lock-alt d-block mb-3" style="font-size: 2.5rem;"></i>
          <h5 class="mb-2">Sin funcionalidades asignadas</h5>
          <p class="mb-0 text-muted">Este rol tiene acceso al modulo Inventario, pero aun no cuenta con permisos para funcionalidades especificas de Parametros.</p>
        </div>
      </div>
    <?php endif; ?>

    <?php require('Layouts/footer.php'); ?>

  </main>

  <?php require('Layouts/script.php'); ?>

</body>

</html>
