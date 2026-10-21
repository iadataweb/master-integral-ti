<!-- Core -->
<script src="<?= assets(); ?>/vendor/@popperjs/core/dist/umd/popper.min.js"></script>
<script src="<?= assets(); ?>/vendor/bootstrap/dist/js/bootstrap.min.js"></script>

<script src="<?= assets(); ?>/extensions/jquery/jquery.min.js"></script>
<script src="<?= assets(); ?>/js/csrf_protection.js"></script>
<script src="<?= assets(); ?>/js/show_message.js"></script>
<script src="<?= assets(); ?>/js/theme_manager.js?v=<?= filemtime(dirname(__DIR__) . '/Assets/js/theme_manager.js'); ?>"></script>
<script src="<?= assets(); ?>/js/trash_manager.js"></script>
<script src="<?= assets(); ?>/js/ui_enhancements.js?v=<?= filemtime(dirname(__DIR__) . '/Assets/js/ui_enhancements.js'); ?>"></script>

<!-- Vendor JS -->
<script src="<?= assets(); ?>/vendor/onscreen/dist/on-screen.umd.min.js"></script>

<!-- Slider -->
<script src="<?= assets(); ?>/vendor/nouislider/dist/nouislider.min.js"></script>

<!-- Smooth scroll -->
<script src="<?= assets(); ?>/vendor/smooth-scroll/dist/smooth-scroll.polyfills.min.js"></script>

<!-- Charts -->
<script src="<?= assets(); ?>/vendor/chartist/dist/chartist.min.js"></script>
<script src="<?= assets(); ?>/vendor/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js"></script>

<!-- Datepicker -->
<script src="<?= assets(); ?>/vendor/vanillajs-datepicker/dist/js/datepicker.min.js"></script>

<!-- Sweet Alerts 2 -->
<script src="<?= assets(); ?>/vendor/sweetalert2/dist/sweetalert2.all.min.js"></script>

<!-- Moment JS 2.27.0 -->
<script src="<?= assets(); ?>/js/moment.min.js"></script>


<!-- Notyf -->
<script src="<?= assets(); ?>/vendor/notyf/notyf.min.js"></script>

<!-- Simplebar -->
<script src="<?= assets(); ?>/vendor/simplebar/dist/simplebar.min.js"></script>

<!-- Buttons 11 -->
<!-- <script src=".../js/buttons.js"></script> -->

<!-- Volt JS 11 -->
<script src="<?= assets(); ?>/js/volt.js?v=<?= filemtime(__DIR__ . "/../Assets/js/volt.js"); ?>"></script>


<script src="<?= assets(); ?>/js/sidebar-menu.js"></script>


<?php
if (!function_exists('local_asset_version')) {
    function local_asset_version($relative_path)
    {
        $relative_path = ltrim((string)$relative_path, '/');
        $local_path = dirname(__DIR__) . '/Assets/' . $relative_path;
        return is_file($local_path) ? '?v=' . filemtime($local_path) : '';
    }
}

if (isset($data['script']) && !empty($data['script'])) {
    if (is_array($data['script'])) {
        foreach ($data['script'] as $js_file) {
            $asset_relative = ltrim($js_file, '/');
            $script_path = assets() . $js_file . local_asset_version($asset_relative);
            echo "<script src=\"$script_path\"></script>";
        }
    } else {
        $asset_relative = ltrim($data['script'], '/');
        $script_path = assets() . $data['script'] . local_asset_version($asset_relative);
        echo "<script src=\"$script_path\"></script>";
    }
}

echo '<script src="' . assets() . '/js/datatables_ui.js' . local_asset_version('js/datatables_ui.js') . '"></script>';

if (isset($data['page_functions_js']) && !empty($data['page_functions_js'])) {
    if (is_array($data['page_functions_js'])) {
        foreach ($data['page_functions_js'] as $functions_file) {
            $asset_relative = "js/" . $functions_file;
            $functions_path = assets() . "/js/" . $functions_file . local_asset_version($asset_relative);
            echo "<script src=\"$functions_path\"></script>";
        }
    } else {
        $asset_relative = ltrim($data['page_functions_js'], '/');
        $functions_path = assets() . $data['page_functions_js'] . local_asset_version($asset_relative);
        echo "<script src=\"$functions_path\"></script>";
    }
}
?>
