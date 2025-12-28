<?php
namespace Core;

class Plugin
{
    public static function init(): void
    {
        self::load_files();

        // Discovery of Pages_<slug> folders so plugin can be extended dynamically
        $manifests = [];
        if (class_exists('\\Core\\Loader')) {
            $manifests = \Core\Loader::discoverApis(plugin_dir_path(__FILE__) . '..');
            // expose for other parts of the plugin that may read discovered APIs
            $GLOBALS['cv_headless_discovered_apis'] = $manifests;
        }

        // Register dynamic REST routes for discovered APIs at rest_api_init
        add_action('rest_api_init', function () use ($manifests) {
            if (empty($manifests) || !class_exists('\\Api\\Registrar')) {
                return;
            }

            foreach ($manifests as $manifest) {
                \Api\Registrar::registerRestFromManifest($manifest);
            }
        });

        add_action('admin_menu', ['Admin\\Menu', 'register']);
        // Route générique pour lister toutes les APIs
        add_action('rest_api_init', function () {
            $base = plugin_dir_path(__FILE__) . '..';
            if (!class_exists('GenericApiRouter')) {
                require_once $base . '/Api/GenericApiRouter.php';
            }
            if (!class_exists('StatusController')) {
                require_once $base . '/Api/StatusController.php';
            }
            if (!class_exists('OpenApi')) {
                require_once $base . '/Api/OpenApi.php';
            }
            register_rest_route('api/v1', '/list', [
                'methods' => 'GET',
                'callback' => ['GenericApiRouter', 'listApis'],
                'permission_callback' => '__return_true',
            ]);
            register_rest_route('api/v1', '/status', [
                'methods' => 'GET',
                'callback' => ['StatusController', 'getStatus'],
                'permission_callback' => '__return_true',
            ]);
        });
    }

    private static function load_files(): void
    {
        $base = plugin_dir_path(__FILE__) . '..';

        require_once $base . '/Admin/Menu.php';
        // Loader for dynamic Pages_<slug> discovery
        require_once $base . '/Core/Loader.php';
        require_once $base . '/Api/MediaHelper.php';
        require_once $base . '/Api/Routes.php';
        require_once $base . '/Api/Registrar.php';
        // require_once $base . '/Api/OpenApi.php';
        // require_once $base . '/Api/CvController.php';
        require_once $base . '/Storage/OptionStore.php';
    }
}