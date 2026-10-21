document.addEventListener('DOMContentLoaded', function () {
    const baseUrlMeta = document.querySelector('meta[name="base-url"]');
    const themeMeta = document.querySelector('meta[name="theme-preference"]');
    const baseUrl = baseUrlMeta ? baseUrlMeta.getAttribute('content') : '';
    const toggleButton = document.getElementById('btn-theme-toggle');
    const toggleIcon = document.getElementById('icon-theme-toggle');

    function normalizeTheme(value) {
        return value === 'dark' ? 'dark' : 'light';
    }

    function getCurrentTheme() {
        const attr = document.documentElement.getAttribute('data-theme');
        return normalizeTheme(attr || (themeMeta ? themeMeta.getAttribute('content') : 'light'));
    }

    function applyTheme(theme) {
        const normalizedTheme = normalizeTheme(theme);
        document.documentElement.setAttribute('data-theme', normalizedTheme);
        document.documentElement.classList.toggle('theme-dark', normalizedTheme === 'dark');
        const metaThemeColor = document.querySelector('meta[name="theme-color"]');
        if (metaThemeColor) {
            metaThemeColor.setAttribute('content', normalizedTheme === 'dark' ? '#0d1422' : '#f4f6fb');
        }
        if (toggleIcon) {
            toggleIcon.innerHTML = normalizedTheme === 'dark'
                ? '<path d="M20.8 13.2A9 9 0 0 1 10.8 3.2a9 9 0 1 0 10 10Z"/>'
                : '<circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M2 12h2m16 0h2M5 5l1.5 1.5m11 11L19 19M5 19l1.5-1.5m11-11L19 5"/>';
        }
        if (toggleButton) {
            const label = normalizedTheme === 'dark' ? 'Cambiar a tema claro' : 'Cambiar a tema oscuro';
            toggleButton.setAttribute('aria-label', label);
            toggleButton.setAttribute('title', label);
        }
    }

    function persistTheme(theme) {
        if (!baseUrl) {
            return;
        }
        $.post(baseUrl + '/account/update_theme_preference', { theme_preference: theme })
            .fail(function () {
                showMessage('warning', 'No se pudo guardar la preferencia del tema en este momento.');
            });
    }

    applyTheme(getCurrentTheme());

    if (toggleButton) {
        toggleButton.addEventListener('click', function () {
            const nextTheme = getCurrentTheme() === 'dark' ? 'light' : 'dark';
            applyTheme(nextTheme);
            persistTheme(nextTheme);
        });
    }
});
