$(document).ready(function () {
    const sidebar = $('#sidebarMenu');
    const currentPath = normalizePath(window.location.pathname);

    if (!sidebar.length) {
        return;
    }

    const activeLink = resolveActiveLink(currentPath);
    resetSidebarState();

    if (activeLink && activeLink.length) {
        activateLink(activeLink);
    }

    sidebar.on('click', 'a.nav-link', function () {
        const href = ($(this).attr('href') || '').trim();
        if (!href || href === '#') {
            return;
        }

        resetSidebarState();
        activateLink($(this));
    });

    function resolveActiveLink(pathname) {
        const routeAliases = [
            { currentPrefix: '/movement/movements_asset/asset_locations', menuHref: '/movement/movements_asset/asset_locations' },
            { currentPrefix: '/help_desk/incidents/reports', menuHref: '/help_desk/incidents/reports' },
            {
                currentPrefix: '/administration/areas',
                menuHref: '/administration/parameters',
            },
            {
                currentPrefix: '/administration/campuses',
                menuHref: '/administration/parameters',
            },
            {
                currentPrefix: '/administration/companies',
                menuHref: '/administration/parameters',
            },
            {
                currentPrefix: '/administration/positions',
                menuHref: '/administration/parameters',
            },
            {
                currentPrefix: '/administration/professional_titles',
                menuHref: '/administration/parameters',
            },
            {
                currentPrefix: '/administration/environments',
                menuHref: '/administration/parameters',
            },
            {
                currentPrefix: '/inventory/architectures',
                menuHref: '/inventory/parameters',
            },
            {
                currentPrefix: '/inventory/brands',
                menuHref: '/inventory/parameters',
            },
            {
                currentPrefix: '/inventory/capacity_units',
                menuHref: '/inventory/parameters',
            },
            {
                currentPrefix: '/inventory/categories_asset',
                menuHref: '/inventory/parameters',
            },
            {
                currentPrefix: '/inventory/conditions_asset',
                menuHref: '/inventory/parameters',
            },
            {
                currentPrefix: '/inventory/types_asset',
                menuHref: '/inventory/parameters',
            },
            {
                currentPrefix: '/inventory/types_firmware',
                menuHref: '/inventory/parameters',
            },
            {
                currentPrefix: '/inventory/types_license',
                menuHref: '/inventory/parameters',
            },
            {
                currentPrefix: '/inventory/types_network_interface',
                menuHref: '/inventory/parameters',
            },
            {
                currentPrefix: '/inventory/types_software',
                menuHref: '/inventory/parameters',
            },
            {
                currentPrefix: '/inventory/types_storage',
                menuHref: '/inventory/parameters',
            },
            {
                currentPrefix: '/inventory/versions_ip',
                menuHref: '/inventory/parameters',
            },
            {
                currentPrefix: '/maintenance/actions_maintenance_preventive',
                menuHref: '/maintenance/parameters',
            },
            {
                currentPrefix: '/help_desk/categories',
                menuHref: '/help_desk/parameters',
            },
            {
                currentPrefix: '/help_desk/subcategories',
                menuHref: '/help_desk/parameters',
            },
            {
                currentPrefix: '/help_desk/priorities',
                menuHref: '/help_desk/parameters',
            },
            {
                currentPrefix: '/help_desk/types_incident',
                menuHref: '/help_desk/parameters',
            },
        ];

        const alias = routeAliases.find(function (item) {
            return pathname === item.currentPrefix || pathname.startsWith(item.currentPrefix + '/');
        });

        if (alias) {
            const aliasLink = findLinkByPath(alias.menuHref);
            if (aliasLink.length) {
                return aliasLink;
            }
        }

        const links = sidebar.find('a.nav-link[href]').filter(function () {
            const hrefPath = normalizeHrefPath($(this).attr('href'));
            return hrefPath && hrefPath !== '/';
        });

        let bestLink = $();
        let bestScore = -1;

        links.each(function () {
            const link = $(this);
            const hrefPath = normalizeHrefPath(link.attr('href'));
            const score = calculateMatchScore(pathname, hrefPath);

            if (score > bestScore) {
                bestScore = score;
                bestLink = link;
            }
        });

        if (bestScore >= 0) {
            return bestLink;
        }

        if (pathname === '/' || pathname === '/dashboard') {
            return findLinkByPath('/dashboard');
        }

        return $();
    }

    function calculateMatchScore(pathname, hrefPath) {
        if (pathname === hrefPath) {
            return 10000 + hrefPath.length;
        }

        if (hrefPath === '/dashboard') {
            return -1;
        }

        const pathSegments = pathname.split('/').filter(Boolean);
        const hrefSegments = hrefPath.split('/').filter(Boolean);

        if (pathSegments.length >= 2 && hrefSegments.length >= 2) {
            const sameController = pathSegments[0] === hrefSegments[0] && pathSegments[1] === hrefSegments[1];
            if (sameController) {
                return 5000 + hrefPath.length;
            }
        }

        if (pathname.startsWith(hrefPath + '/')) {
            return 1000 + hrefPath.length;
        }

        return -1;
    }

    function findLinkByPath(path) {
        return sidebar.find('a.nav-link[href]').filter(function () {
            return normalizeHrefPath($(this).attr('href')) === path;
        }).first();
    }

    function activateLink(link) {
        link.addClass('active').attr('aria-current', 'page');
        link.closest('.nav-item').addClass('active');

        const parentCollapse = link.closest('.collapse');
        if (!parentCollapse.length) {
            return;
        }

        parentCollapse.addClass('show').attr('aria-expanded', 'true');
        const toggle = parentCollapse.prev('[data-bs-toggle="collapse"]');
        toggle.addClass('active').removeClass('collapsed').attr('aria-expanded', 'true');
        toggle.closest('.nav-item').addClass('active');
    }

    function resetSidebarState() {
        sidebar.find('.nav-item').removeClass('active');
        sidebar.find('.nav-link').removeClass('active').removeAttr('aria-current');
        sidebar.find('.collapse').removeClass('show').attr('aria-expanded', 'false');
        sidebar.find('[data-bs-toggle="collapse"]').addClass('collapsed').attr('aria-expanded', 'false');
    }

    function normalizeHrefPath(href) {
        const value = (href || '').trim();
        if (!value || value === '#') {
            return '';
        }

        try {
            return normalizePath(new URL(value, window.location.origin).pathname);
        } catch (error) {
            return '';
        }
    }

    function normalizePath(path) {
        const normalized = ('/' + (path || '').replace(/^\/+/, '')).replace(/\/+$/, '');
        return normalized === '' ? '/' : normalized;
    }
});
