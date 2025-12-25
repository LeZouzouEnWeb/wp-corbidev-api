<?php
namespace Core;

class Plugin
{
    public static function init(): void
    {
        self::load_files();

        add_action('admin_menu', ['Admin\\Menu', 'register']);
        add_action('rest_api_init', ['Api\\Routes', 'register']);
    }

    private static function load_files(): void
    {
        $base = plugin_dir_path(__FILE__) . '..';

        require_once $base . '/Admin/Menu.php';
        require_once $base . '/Admin/Pages/CvPage.php';
        require_once $base . '/Api/Routes.php';
        require_once $base . '/Api/OpenApi.php';
        require_once $base . '/Api/CvController.php';
        require_once $base . '/Storage/OptionStore.php';
    }
}
