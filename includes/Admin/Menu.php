<?php

namespace Admin;

class Menu
{
    public static function register(): void
    {
        // Try to get discovered manifests from the core loader
        $manifests = $GLOBALS['cv_headless_discovered_apis'] ?? [];

        // If no manifests discovered, show a generic placeholder admin page
        if (empty($manifests)) {
            add_menu_page(
                'APIs',
                'APIs',
                'manage_options',
                'api',
                function () {
                    echo '<div class="wrap"><h1>APIs</h1><p>Aucune API configurée.</p></div>';
                },
                'dashicons-networking',
                30
            );

            return;
        }

        // Main parent menu: overview linking to each API admin page
        add_menu_page(
            'APIs',
            'APIs',
            'manage_options',
            'api',
            function () use ($manifests) {
                echo '<script src="https://cdn.tailwindcss.com"></script>';
                echo '<script>tailwind.config = {theme: {extend: {colors: {"brand-primary": "#a78bfa"}}}}</script>';
                echo '<style>.via-brand-primary { --tw-gradient-via-position: 50%!important; --tw-gradient-stops: var(--tw-gradient-from), #2026ed var(--tw-gradient-via-position), var(--tw-gradient-to)!important; z-index: 999; }</style>';
                echo '<div class="wrap"><div class="max-w-7xl mx-auto py-8 px-6">';

                // Header like Pages_cv: gradient background, white title and description with icon
                echo '<div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-lg shadow-lg p-6 mb-6">';
                echo '<div class="flex items-center gap-4">';
                // Inline SVG icon (network nodes) — more API-like
                echo '<svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" viewBox="0 0 24 24" fill="none">';
                echo '<circle cx="12" cy="6" r="2" fill="white" />';
                echo '<circle cx="5" cy="17" r="2" fill="white" />';
                echo '<circle cx="19" cy="17" r="2" fill="white" />';
                echo '<path d="M12 8v3" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>';
                echo '<path d="M6.7 15.3L11 11" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>';
                echo '<path d="M17.3 15.3L13 11" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>';
                echo '</svg>';
                echo '<div>';
                echo '<h1 class="text-3xl font-bold text-white">APIs</h1>';
                echo '<p class="text-blue-100 mt-2">Liste et gestion des APIs disponibles dans le plugin.</p>';
                echo '</div>';
                echo '</div>';
                echo '</div>';

                echo '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">';

                foreach ($manifests as $slug => $manifest) {
                    $menu_slug = 'api-' . $slug;
                    $name = $manifest['name'] ?? ($manifest['display_name'] ?? $slug);
                    $description = $manifest['description'] ?? '';
                    $url = admin_url('admin.php?page=' . $menu_slug);

                    // Card with gradient header and white body (like Pages_cv style)
                    // Add a relative wrapper so we can place a luminous halo above the card
                    echo '<div class="relative">';

                    // Halo lumineux diffus (glow) au-dessus de la carte
                    echo '<div class="absolute inset-x-0 top-0 h-[3px] bg-gradient-to-r from-transparent via-brand-primary to-transparent"></div>';

                    // Anchor/card
                    echo '<a href="' . esc_url($url) . '" class="block rounded-lg overflow-hidden shadow hover:shadow-lg transition relative z-10">';
                    // Header (reduced padding) -> solid pastel (slightly darker) and sober title
                    echo '<div class="p-2" style="background-color: #82abf5;">';
                    echo '<h3 class="text-lg font-semibold text-white">' . esc_html($name) . '</h3>';
                    echo '</div>';

                    // Body (reduced padding)
                    echo '<div class="bg-white p-3">';
                    if (!empty($description)) {
                        echo '<p class="text-gray-700 mb-4">' . esc_html($description) . '</p>';
                    }
                    echo '<div class="text-right">';
                    echo '<span class="inline-block px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Gérer</span>';
                    echo '</div>';
                    echo '</div>';
                    echo '</a>';

                    // (no external halo; luminous band above is used)

                    echo '</div>'; // end relative wrapper
                }

                echo '</div></div></div>';
            },
            'dashicons-networking',
            30
        );

        // Add a submenu page for each manifest discovered
        foreach ($manifests as $slug => $manifest) {
            $menu_slug = 'api-' . $slug;
            $display = $manifest['display_name'] ?? $slug;

            $callback = null;
            if (!empty($manifest['admin_class'])) {
                if (!class_exists($manifest['admin_class']) && !empty($manifest['__path'])) {
                    // attempt to require all PHP files from the manifest path so class becomes available
                    foreach (glob($manifest['__path'] . '/*.php') as $phpFile) {
                        if (basename($phpFile) === 'Pages.php') {
                            continue;
                        }

                        require_once $phpFile;
                    }
                }

                if (class_exists($manifest['admin_class'])) {
                    $instance = new $manifest['admin_class']();
                    $callback = [$instance, 'render'];
                }
            }

            if (!$callback) {
                // fallback to a simple renderer using manifest
                $callback = function () use ($manifest) {
                    $name = esc_html($manifest['display_name'] ?? $manifest['slug']);
                    echo '<div class="wrap"><h1>' . $name . '</h1><p>Administration générique pour l\'API "' . $name . '".</p></div>';
                };
            }

            add_submenu_page('api', $display, $display, 'manage_options', $menu_slug, $callback);
        }
    }
}