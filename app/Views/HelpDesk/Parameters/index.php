<!DOCTYPE html>
<html lang="en">

<?php require('Layouts/head.php'); ?>
<?php
$parameter_cards = $data['parameter_cards'] ?? [];
if (!function_exists('parameter_card_svg_icon')) {
    function parameter_card_svg_icon(string $icon): string
    {
        $icons = [
            'grid' => '<path clip-rule="evenodd" fill-rule="evenodd" d="M4.25 2A2.25 2.25 0 0 0 2 4.25v2.5A2.25 2.25 0 0 0 4.25 9h2.5A2.25 2.25 0 0 0 9 6.75v-2.5A2.25 2.25 0 0 0 6.75 2h-2.5Zm0 9A2.25 2.25 0 0 0 2 13.25v2.5A2.25 2.25 0 0 0 4.25 18h2.5A2.25 2.25 0 0 0 9 15.75v-2.5A2.25 2.25 0 0 0 6.75 11h-2.5Zm9-9A2.25 2.25 0 0 0 11 4.25v2.5A2.25 2.25 0 0 0 13.25 9h2.5A2.25 2.25 0 0 0 18 6.75v-2.5A2.25 2.25 0 0 0 15.75 2h-2.5Zm0 9A2.25 2.25 0 0 0 11 13.25v2.5A2.25 2.25 0 0 0 13.25 18h2.5A2.25 2.25 0 0 0 18 15.75v-2.5A2.25 2.25 0 0 0 15.75 11h-2.5Z"></path>',
            'archive' => '<path d="M2 3a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H2Z"></path><path clip-rule="evenodd" fill-rule="evenodd" d="M2 7.5h16l-.811 7.71a2 2 0 0 1-1.99 1.79H4.802a2 2 0 0 1-1.99-1.79L2 7.5ZM7 11a1 1 0 0 1 1-1h4a1 1 0 1 1 0 2H8a1 1 0 0 1-1-1Z"></path>',
            'tag' => '<path clip-rule="evenodd" fill-rule="evenodd" d="M4.5 2A2.5 2.5 0 0 0 2 4.5v3.879a2.5 2.5 0 0 0 .732 1.767l7.5 7.5a2.5 2.5 0 0 0 3.536 0l3.878-3.878a2.5 2.5 0 0 0 0-3.536l-7.5-7.5A2.5 2.5 0 0 0 8.38 2H4.5ZM5 6a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z"></path>',
            'clipboard' => '<path clip-rule="evenodd" fill-rule="evenodd" d="M6 2a2 2 0 0 0-2 2v1H3a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V4a2 2 0 0 0-2-2H6Zm0 3h8V4H6v1Zm-1 5a1 1 0 0 1 1-1h8a1 1 0 1 1 0 2H6a1 1 0 0 1-1-1Zm1 3a1 1 0 1 0 0 2h5a1 1 0 1 0 0-2H6Z"></path>',
        ];
        return $icons[$icon] ?? $icons['grid'];
    }
}
?>

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
                <svg class="icon icon-xxs" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
              </span>
            </li>
            <li class="breadcrumb-item">Mesa de Ayuda</li>
            <li class="breadcrumb-item active" aria-current="page">Parametros</li>
          </ol>
        </nav>
        <h2 class="h4">Parametros</h2>
        <p class="mb-0">Configura las clasificaciones y prioridades para organizar la atención de incidencias.</p>
      </div>
    </div>

    <div class="row">
      <?php if (empty($parameter_cards)): ?>
        <div class="col-12">
          <div class="alert alert-info mb-4">No tiene parametros disponibles para este modulo.</div>
        </div>
      <?php endif; ?>
      <?php foreach ($parameter_cards as $card): ?>
        <div class="col-12 col-sm-6 col-xl-4 mb-4">
          <a href="<?= base_url() . htmlspecialchars($card['url']); ?>" class="custom-btn-parameter">
            <div class="card text-center shadow-sm h-100 border-0">
              <div class="card-body">
                <svg class="icon icon-md" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                  <?= parameter_card_svg_icon((string)($card['icon'] ?? 'grid')); ?>
                </svg>
                <h6 class="mt-3"><?= htmlspecialchars($card['title']); ?></h6>
              </div>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
    </div>

    <?php require('Layouts/footer.php'); ?>

  </main>

  <?php require('Layouts/script.php'); ?>

</body>

</html>