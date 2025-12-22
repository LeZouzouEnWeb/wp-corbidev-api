<?php
namespace Admin;

use Admin\Pages\CvPage;

class Menu
{
    public static function register(): void
    {
        $page = new CvPage(); // création de l'instance

        // Le menu principal CV
        add_menu_page(
            'CV',
            'CV',
            'manage_options',
            'cv',
            [$page, 'render'], // appel sur l'instance
            'dashicons-id',
            30
        );

        // Les options ne sont pas nécessaires si tu utilises OptionStore
        //add_action('admin_init', [$page, 'registerSettings']);
    }
}
