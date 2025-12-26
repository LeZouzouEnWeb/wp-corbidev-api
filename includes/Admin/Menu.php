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
                echo '<div class="wrap"><h1>APIs</h1><ul>';
                foreach ($manifests as $slug => $manifest) {
                    $menu_slug = 'api-' . $slug;
                    $name = isset($manifest['display_name']) ? esc_html($manifest['display_name']) : esc_html($slug);
                    echo '<li><a href="' . esc_attr(admin_url('admin.php?page=' . $menu_slug)) . '">' . $name . '</a></li>';
                }
                echo '</ul></div>';
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