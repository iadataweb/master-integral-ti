<head>
    <?php
        $current_theme = $_SESSION['user_data']['theme_preference_user'] ?? 'light';
        if (!in_array($current_theme, ['light', 'dark'], true)) {
            $current_theme = 'light';
        }
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sistema Integral de TI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="base-url" content="<?= base_url(); ?>">
    <meta name="theme-preference" content="<?= $current_theme; ?>">
    <meta name="title" content="Sistema Integral de TI">
    <meta name="author" content="Sistema Integral de TI">
    <!-- Favicon -->
    <link rel="icon" href="<?= assets(); ?>/img/logo/logo_img.ico">
    <link rel="manifest" href="<?= assets(); ?>/img/favicon/site.webmanifest">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="<?= $current_theme === 'dark' ? '#0d1422' : '#f4f6fb'; ?>">
    <script>
        (function () {
            var theme = "<?= $current_theme; ?>";
            if (theme !== "light" && theme !== "dark") {
                theme = "light";
            }
            document.documentElement.setAttribute("data-theme", theme);
            document.documentElement.classList.toggle("theme-dark", theme === "dark");
        })();
    </script>

    <!-- Sweet Alert -->
    <link type="text/css" href="<?= assets(); ?>/vendor/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Notyf -->
    <link type="text/css" href="<?= assets(); ?>/vendor/notyf/notyf.min.css" rel="stylesheet">

    <!-- Volt CSS -->
    <link type="text/css" href="<?= assets(); ?>/css/volt.css" rel="stylesheet">

    <!-- Boxicons -->
    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>

    <!-- Fontello -->
    <link type="text/css" href="<?= assets(); ?>/fontello/css/fontello.css" rel='stylesheet'>
    
    <?php
    if (isset($data['css']) && !empty($data['css'])) {
        if (is_array($data['css'])) {
            foreach ($data['css'] as $css_file) {
                $css_path = assets() . $css_file;
                echo "<link rel=\"stylesheet\" href=\"$css_path\">";
            }
        } else {
            $css_path = assets() . $data['css'];
            echo "<link rel=\"stylesheet\" href=\"$css_path\">";
        }
    }
    ?>

    <!-- Personal CSS -->
    <link type="text/css" href="<?= assets(); ?>/css/styles.css?v=<?= filemtime(dirname(__DIR__) . '/Assets/css/styles.css'); ?>" rel="stylesheet">
    
</head>
