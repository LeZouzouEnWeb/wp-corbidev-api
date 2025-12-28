<?php
namespace CorbiDev\ApiBuilder\Core;

class Loader
{
    public static function init()
    {
        // Initialisation de l'admin
        if (is_admin()) {
            \CorbiDev\ApiBuilder\Admin\MenuApiBuilder::register();
        }
        // (À compléter avec l'initialisation des services, API, etc.)
    }
}