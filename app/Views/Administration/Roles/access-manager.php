<!DOCTYPE html>
<html lang="en">

<?php require('Layouts/head.php'); ?>

<body>

  <?php require('Layouts/sidebar.php'); ?>

  <main class="content">

    <?php require('Layouts/header.php'); ?>

    <?php
      $role = $data['role'];
      $is_protected = !empty($data['is_protected']);
      $matrix = $data['matrix'] ?? [];
      $role_name = htmlspecialchars($role['name_role'] ?? '', ENT_QUOTES, 'UTF-8');
      $parameter_actions = ['Ver', 'Crear', 'Editar', 'Eliminar'];
      $normalize_access_label = function ($value) {
        $value = strtolower(trim((string)$value));
        $transliterated = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        if ($transliterated !== false && $transliterated !== '') {
          $value = strtolower($transliterated);
        }
        return preg_replace('/[^a-z0-9]+/', '', $value);
      };
      $is_parameter_submenu = function ($submenu) use ($normalize_access_label) {
        $normalized = $normalize_access_label($submenu);
        return $normalized === 'parametros' || $normalized === 'parameters' || strpos($normalized, 'parametro') !== false;
      };
      $submenu_applicable_actions = function ($submenu) use ($parameter_actions, $is_parameter_submenu) {
        return $is_parameter_submenu($submenu) ? ['Ver'] : $parameter_actions;
      };
      $access_summary = [
        'modules_total' => count($matrix),
        'modules_visible' => 0,
        'base_active' => 0,
        'submenus_total' => 0,
        'submenus_active' => 0,
        'parameters_total' => 0,
        'parameters_active' => 0,
        'actions_total' => 0,
        'actions_active' => 0,
      ];
      foreach ($matrix as $summary_module) {
        if ((int)($summary_module['r_read_permission'] ?? 0) === 1) { $access_summary['modules_visible']++; }
        if ((int)($summary_module['r_read_permission'] ?? 0) === 1) { $access_summary['base_active']++; }
        foreach (($summary_module['controls'] ?? []) as $summary_control) {
          $summary_name = (string)($summary_control['name_control'] ?? '');
          $is_active = (int)($summary_control['allow_additional_permission'] ?? 0) === 1;
          if (strpos($summary_name, 'Submenu: ') === 0) {
            $access_summary['submenus_total']++;
            if ($is_active) { $access_summary['submenus_active']++; }
          } elseif (strpos($summary_name, 'Parametro: ') === 0) {
            $access_summary['parameters_total']++;
            if ($is_active) { $access_summary['parameters_active']++; }
          } else {
            $access_summary['actions_total']++;
            if ($is_active) { $access_summary['actions_active']++; }
          }
        }
      }
      $global_parameter_groups = [];
      foreach ($matrix as $parameter_module) {
        $module_id_for_parameter = (int)($parameter_module['id_module'] ?? 0);
        $module_name_for_parameter = (string)($parameter_module['name_module'] ?? 'Modulo');
        foreach (($parameter_module['controls'] ?? []) as $parameter_control) {
          $parameter_name = (string)($parameter_control['name_control'] ?? '');
          $parameter_parts = explode(': ', $parameter_name);
          if (count($parameter_parts) !== 4 || $parameter_parts[0] !== 'Parametro') {
            continue;
          }
          $scope = $parameter_parts[1];
          $resource = $parameter_parts[2];
          $action = $parameter_parts[3];
          $group_key = $module_id_for_parameter . '|' . $scope;
          if (!isset($global_parameter_groups[$group_key])) {
            $global_parameter_groups[$group_key] = [
              'module_id' => $module_id_for_parameter,
              'module_name' => $module_name_for_parameter,
              'scope' => $scope,
              'resources' => [],
            ];
          }
          $global_parameter_groups[$group_key]['resources'][$resource][$action] = $parameter_control;
        }
      }
    ?>

    <link rel="stylesheet" href="<?= assets(); ?>/css/components/administration-roles-access-manager.css?v=<?= filemtime(dirname(__DIR__, 3) . '/Assets/css/components/administration-roles-access-manager.css'); ?>">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4">
      <div class="d-block mb-4 mb-md-0">
        <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
          <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
            <li class="breadcrumb-item"><span><i class="bx bx-home"></i></span></li>
            <li class="breadcrumb-item">Administracion</li>
            <li class="breadcrumb-item">Roles</li>
            <li class="breadcrumb-item active" aria-current="page">Permisos</li>
          </ol>
        </nav>
        <h2 class="h4">Gestionar Permisos</h2>

      </div>
      <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url(); ?>/administration/roles/manage_roles" class="btn btn-sm btn-outline-gray-600"><i class="bx bx-arrow-back me-1"></i> Volver</a>
      </div>
    </div>

    <section class="access-hero mb-4">
      <div class="access-role-heading">
        <div class="access-role-title"><?= $role_name; ?></div>

        <div class="access-role-status">
          <?= (int)$role['status_active_role'] === 1 ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>'; ?>
          <?php if ($is_protected): ?><span class="badge bg-info ms-1">Rol protegido</span><?php endif; ?>
        </div>
      </div>
    </section>

        <form id="formRoleAccess" data-role-id="<?= (int)$role['id_role']; ?>">
      <input type="hidden" name="id_role" value="<?= (int)$role['id_role']; ?>">

      <fieldset <?= $is_protected ? 'disabled' : ''; ?>>
      <?php if (!empty($global_parameter_groups)): ?>
        <section class="access-module-card access-parameters-panel mb-4">
          <header class="access-module-header">
            <div>
              <div class="d-flex align-items-center gap-2 flex-wrap">
                <h5 class="mb-1">Permisos de Parametros</h5>
                <span class="badge bg-info">Gestion independiente</span>
              </div>

            </div>
          </header>
          <div class="access-module-body">
            <div class="access-grid access-parameters-grid">
              <?php foreach ($global_parameter_groups as $group): ?>
                <?php
                  $parameter_module_id = (int)$group['module_id'];
                  $parameter_module_name = htmlspecialchars($group['module_name'], ENT_QUOTES, 'UTF-8');
                  $parameter_scope = (string)$group['scope'];
                  $parameter_scope_label = htmlspecialchars($parameter_scope, ENT_QUOTES, 'UTF-8');
                  $parameter_scope_key = preg_replace('/[^A-Za-z0-9]/', '', $parameter_scope);
                  $parameter_modal_id = 'ModalParameterPermissionsGlobal' . $parameter_module_id . $parameter_scope_key;
                  $parameter_group_key = 'global-parameters-' . $parameter_module_id . '-' . preg_replace('/[^A-Za-z0-9_-]/', '-', $parameter_scope);
                  $parameter_total_resources = count($group['resources']);
                  $parameter_total_permissions = 0;
                  $parameter_enabled_permissions = 0;
                  foreach ($group['resources'] as $resource_actions) {
                    foreach ($parameter_actions as $action) {
                      if (!empty($resource_actions[$action])) {
                        $parameter_total_permissions++;
                        if ((int)$resource_actions[$action]['allow_additional_permission'] === 1) {
                          $parameter_enabled_permissions++;
                        }
                      }
                    }
                  }
                ?>
                <section class="access-control-group access-parameter-summary">
                  <div class="access-control-head">
                    <div>
                      <h6 class="access-control-title"><?= $parameter_scope_label; ?></h6>

                    </div>
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#<?= $parameter_modal_id; ?>">
                      <i class="bx bx-table me-1"></i> Gestionar matriz
                    </button>
                  </div>
                  <div class="access-parameter-summary-grid">
                    <div class="access-parameter-summary-card">
                            <span>Funcionalidades</span>
                            <strong><?= $parameter_total_resources; ?></strong>
                    </div>
                    <div class="access-parameter-summary-card">
                            <span>Permisos activos</span>
                            <strong><?= $parameter_enabled_permissions; ?>/<?= $parameter_total_permissions; ?></strong>
                    </div>
                  </div>
                </section>

                <div class="modal fade access-parameter-modal" id="<?= $parameter_modal_id; ?>" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                      <div class="modal-header">
                        <div>
                          <h5 class="modal-title">Permisos de parametros de <?= $parameter_scope_label; ?></h5>

                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                      </div>
                      <div class="modal-body">
                        <section class="access-control-group border-0 m-0 p-0" data-control-group="<?= htmlspecialchars($parameter_group_key, ENT_QUOTES, 'UTF-8'); ?>">
                          <div class="access-control-head">
                            <div>
                              <h6 class="access-control-title">Matriz de permisos</h6>

                            </div>
                            <div class="access-toolbar">
                              <button type="button" class="btn btn-sm btn-outline-secondary js-control-group-check-all">Todos</button>
                              <button type="button" class="btn btn-sm btn-outline-gray-600 js-control-group-uncheck-all">Ninguno</button>
                            </div>
                          </div>
                          <div class="access-parameter-table-wrap">
                            <table class="access-parameter-table">
                              <thead>
                                <tr>
                                  <th>Parametro</th>
                                  <?php foreach ($parameter_actions as $action): ?><th class="text-center"><?= $action; ?></th><?php endforeach; ?>
                                </tr>
                              </thead>
                              <tbody>
                                <?php foreach ($group['resources'] as $resource => $actions): ?>
                                  <tr>
                                    <td><span class="access-parameter-name"><?= htmlspecialchars($resource, ENT_QUOTES, 'UTF-8'); ?></span></td>
                                    <?php foreach ($parameter_actions as $action): ?>
                                      <td class="text-center">
                                        <?php if (!empty($actions[$action])): ?>
                                          <label class="access-parameter-check" title="<?= htmlspecialchars($resource . ' - ' . $action, ENT_QUOTES, 'UTF-8'); ?>">
                                            <input type="checkbox" name="controls[<?= (int)$actions[$action]['id_control']; ?>]" value="1" <?= (int)$actions[$action]['allow_additional_permission'] === 1 ? 'checked' : ''; ?>>
                                          </label>
                                        <?php else: ?>
                                          <span class="text-muted">-</span>
                                        <?php endif; ?>
                                      </td>
                                    <?php endforeach; ?>
                                  </tr>
                                <?php endforeach; ?>
                              </tbody>
                            </table>
                          </div>
                        </section>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-outline-gray-600" data-bs-dismiss="modal">Cerrar</button>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </section>
      <?php endif; ?>
      <div class="access-grid">
        <?php foreach ($matrix as $module): ?>
          <?php
            $module_id = (int)$module['id_module'];
            $module_card_key = (string)$module_id;
            $module_name = htmlspecialchars($module['name_module'], ENT_QUOTES, 'UTF-8');

            $parameter_matrix = [];
            $submenu_matrix = [];
            $other_controls = [];
            foreach (($module['controls'] ?? []) as $control) {
              $raw_name = (string)($control['name_control'] ?? '');
              $parts = explode(': ', $raw_name);
              if ($parts[0] === 'Submenu') {
                if (count($parts) === 4) {
                  $submenu_matrix[$parts[1]][$parts[2]][$parts[3]] = $control;
                } elseif (count($parts) === 3 && empty($submenu_matrix[$parts[1]][$parts[2]]['Ver'])) {
                  $submenu_matrix[$parts[1]][$parts[2]]['Ver'] = $control;
                }
                continue;
              }
              if (count($parts) === 4 && $parts[0] === 'Parametro') {
                $parameter_matrix[$parts[1]][$parts[2]][$parts[3]] = $control;
                continue;
              }
              if (strpos($raw_name, 'Submenu: ') === 0 || strpos($raw_name, 'Parametro: ') === 0) {
                continue;
              }
              $other_controls[] = $control;
            }
            $has_independent_controls = !empty($submenu_matrix) || !empty($other_controls);
            $module_visible = (int)($module['r_read_permission'] ?? 0) === 1;
          ?>
          <article class="access-module-card" data-module-card="<?= htmlspecialchars($module_card_key, ENT_QUOTES, 'UTF-8'); ?>">
            <header class="access-module-header">
              <div>
                <div class="d-flex align-items-center gap-2 flex-wrap"><h5 class="mb-1"><?= $module_name; ?></h5><span class="badge access-module-state <?= $module_visible ? 'bg-success' : 'bg-secondary'; ?>" data-module-state><?= $module_visible ? 'Visible' : 'Oculto'; ?></span></div>

              </div>
              <div class="access-toolbar">
                <button type="button" class="btn btn-sm btn-outline-secondary js-module-check-all">Todos</button>
                <button type="button" class="btn btn-sm btn-outline-gray-600 js-module-uncheck-all">Ninguno</button>
              </div>
            </header>
            <div class="access-module-body">
              <div class="access-permission-list<?= $has_independent_controls ? ' is-container-only' : ''; ?>">
                <label class="access-check"><input type="checkbox" data-permission="read" name="modules[<?= $module_id; ?>][r]" value="1" <?= (int)$module['r_read_permission'] === 1 ? 'checked' : ''; ?>><span><?= $has_independent_controls ? 'Habilitar modulo' : 'Visualizar'; ?></span></label>
                  <?php if (!$has_independent_controls && $module_id !== MODULE_DASHBOARD): ?>
                    <label class="access-check"><input type="checkbox" data-permission="write" name="modules[<?= $module_id; ?>][w]" value="1" <?= (int)$module['w_write_permission'] === 1 ? 'checked' : ''; ?>><span>Crear</span></label>
                    <label class="access-check"><input type="checkbox" data-permission="update" name="modules[<?= $module_id; ?>][u]" value="1" <?= (int)$module['u_update_permission'] === 1 ? 'checked' : ''; ?>><span>Editar</span></label>
                    <label class="access-check"><input type="checkbox" data-permission="delete" name="modules[<?= $module_id; ?>][d]" value="1" <?= (int)$module['d_delete_permission'] === 1 ? 'checked' : ''; ?>><span>Eliminar</span></label>
                  <?php endif; ?>
              </div>

              <?php if ($has_independent_controls): ?>
                <div class="access-controls">
                  <?php if (!empty($submenu_matrix)): ?>
                    <?php foreach ($submenu_matrix as $scope => $submenus): ?>
                      <?php
                        $scope_label = htmlspecialchars($scope, ENT_QUOTES, 'UTF-8');
                        $scope_key = preg_replace('/[^A-Za-z0-9]/', '', (string)$scope);
                        $submenu_modal_id = 'ModalSubmenuPermissions' . $module_id . $scope_key;
                        $scope_group_key = 'submenu-' . $module_id . '-' . preg_replace('/[^A-Za-z0-9_-]/', '-', (string)$scope);
                        $submenu_total = 0;
                        $submenu_enabled = 0;
                        $submenu_items = 0;
                        foreach ($submenus as $submenu_label => $submenu_actions_map) {
                          if ($is_parameter_submenu($submenu_label)) {
                            continue;
                          }
                          $submenu_items++;
                          foreach ($parameter_actions as $action) {
                            if (!empty($submenu_actions_map[$action])) {
                              $submenu_total++;
                              if ((int)$submenu_actions_map[$action]['allow_additional_permission'] === 1) {
                                $submenu_enabled++;
                              }
                            }
                          }
                        }
                        if ($submenu_items === 0) {
                          continue;
                        }
                      ?>
                      <section class="access-control-group access-parameter-summary">
                        <div class="access-control-head">
                          <div>
                            <h6 class="access-control-title">Submenus de <?= $scope_label; ?></h6>

                          </div>
                          <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#<?= $submenu_modal_id; ?>">
                            <i class="bx bx-slider-alt me-1"></i> Gestionar submenus
                          </button>
                        </div>
                        <div class="access-parameter-stats">
                          <div class="access-stat-card">
                            <span>Submenus</span>
                            <strong><?= $submenu_items; ?></strong>
                          </div>
                          <div class="access-stat-card">
                            <span>Permisos activos</span>
                            <strong><?= $submenu_enabled; ?>/<?= $submenu_total; ?></strong>
                          </div>
                        </div>
                      </section>

                      <div class="modal fade access-parameter-modal" id="<?= $submenu_modal_id; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-xl">
                          <div class="modal-content">
                            <div class="modal-header">
                              <div>
                                <h5 class="modal-title">Permisos de submenus de <?= $scope_label; ?></h5>

                              </div>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body">
                              <section class="access-control-group border-0 m-0 p-0" data-control-group="<?= htmlspecialchars($scope_group_key, ENT_QUOTES, 'UTF-8'); ?>">
                                <div class="access-control-head">
                                  <div>
                                    <h6 class="access-control-title"><?= $scope_label; ?></h6>

                                  </div>
                                  <div class="access-control-actions">
                                    <button type="button" class="btn btn-outline-secondary btn-sm js-access-group-toggle" data-action="all" data-target-group="<?= htmlspecialchars($scope_group_key, ENT_QUOTES, 'UTF-8'); ?>">Todos</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm js-access-group-toggle" data-action="none" data-target-group="<?= htmlspecialchars($scope_group_key, ENT_QUOTES, 'UTF-8'); ?>">Ninguno</button>
                                  </div>
                                </div>
                                <div class="access-table-wrap">
                                  <table class="access-table">
                                    <thead>
                                      <tr>
                                        <th>Submenu</th>
                                        <?php foreach ($parameter_actions as $action): ?>
                                          <th><?= $action_labels[$action] ?? $action; ?></th>
                                        <?php endforeach; ?>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <?php foreach ($submenus as $submenu => $actions): ?>
                                        <?php
                                          if ($is_parameter_submenu($submenu)) {
                                            continue;
                                          }
                                          $submenu_label = htmlspecialchars($submenu, ENT_QUOTES, 'UTF-8');
                                        ?>
                                        <tr>
                                          <td>
                                            <span class="access-subitem-name"><?= $submenu_label; ?></span>
                                          </td>
                                          <?php foreach ($parameter_actions as $action): ?>
                                            <?php $permission = $actions[$action] ?? null; ?>
                                            <td>
                                              <?php if ($permission): ?>
                                                <input class="form-check-input access-checkbox" type="checkbox" name="controls[<?= (int)$permission['id_control']; ?>]" value="1" <?= (int)$permission['allow_additional_permission'] === 1 ? 'checked' : ''; ?>>
                                              <?php else: ?>
                                                <span class="access-na">No aplica</span>
                                              <?php endif; ?>
                                            </td>
                                          <?php endforeach; ?>
                                        </tr>
                                      <?php endforeach; ?>
                                    </tbody>
                                  </table>
                                </div>
                              </section>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                          </div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  <?php endif; ?>
                  <?php if (!empty($other_controls)): ?>
                    <?php
                      $additional_modal_id = 'ModalAdditionalPermissions' . $module_id;
                      $additional_total = count($other_controls);
                      $additional_enabled = 0;
                      foreach ($other_controls as $control) {
                        if ((int)$control['allow_additional_permission'] === 1) {
                          $additional_enabled++;
                        }
                      }
                    ?>
                    <section class="access-control-group access-parameter-summary">
                      <div class="access-control-head">
                        <div>
                          <h6 class="access-control-title">Acciones adicionales</h6>

                        </div>
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#<?= $additional_modal_id; ?>">
                          <i class="bx bx-slider-alt me-1"></i> Gestionar acciones
                        </button>
                      </div>
                      <div class="access-parameter-summary-grid">
                        <div class="access-parameter-summary-card">
                            <span>Acciones</span>
                            <strong><?= $additional_total; ?></strong>
                        </div>
                        <div class="access-parameter-summary-card">
                            <span>Permisos activos</span>
                            <strong><?= $additional_enabled; ?>/<?= $additional_total; ?></strong>
                        </div>
                      </div>
                    </section>

                    <div class="modal fade access-parameter-modal" id="<?= $additional_modal_id; ?>" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content">
                          <div class="modal-header">
                            <div>
                              <h5 class="modal-title">Acciones adicionales de <?= $module_name; ?></h5>

                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                          </div>
                          <div class="modal-body">
                            <section class="access-control-group border-0 m-0 p-0" data-control-group="additional-<?= $module_id; ?>">
                              <div class="access-control-head">
                                <div>
                                  <h6 class="access-control-title">Permisos especiales</h6>

                                </div>
                                <div class="access-toolbar">
                                  <button type="button" class="btn btn-sm btn-outline-secondary js-control-group-check-all">Todos</button>
                                  <button type="button" class="btn btn-sm btn-outline-gray-600 js-control-group-uncheck-all">Ninguno</button>
                                </div>
                              </div>
                              <div class="access-control-list">
                                <?php foreach ($other_controls as $control): ?>
                                  <label class="access-control-card">
                                    <input type="checkbox" name="controls[<?= (int)$control['id_control']; ?>]" value="1" <?= (int)$control['allow_additional_permission'] === 1 ? 'checked' : ''; ?>>
                                    <span>
                                      <span class="access-control-label"><?= htmlspecialchars($control['name_control'], ENT_QUOTES, 'UTF-8'); ?></span>

                                    </span>
                                  </label>
                                <?php endforeach; ?>
                              </div>
                            </section>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-outline-gray-600" data-bs-dismiss="modal">Cerrar</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            </div>
          </article>
        <?php endforeach; ?>
      </div>

      </fieldset>
      <?php if (!$is_protected): ?>
      <div class="card card-body border-0 shadow mt-4">
        <div class="d-flex flex-column flex-md-row justify-content-between gap-3 align-items-md-center">
          <div>
            <h5 class="mb-1">Guardar configuracion de accesos</h5>

          </div>
          <button type="submit" class="btn btn-secondary"><i class="bx bx-save me-1"></i> Guardar Permisos</button>
        </div>
      </div>
      <?php endif; ?>
    </form>

    <?php require('Layouts/footer.php'); ?>

  </main>

  <?php require('Layouts/script.php'); ?>

</body>

</html>
