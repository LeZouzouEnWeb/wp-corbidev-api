<?php
/*
Plugin Name: CorbiDev API Builder (New)
Description: Système d'API builder dynamique, versionné, administrable, basé sur manifest.json, pour WordPress.
Version: 0.1.0
Author: Eric Corbisier
Text Domain: wp-corbidev-api-new
*/

if (!defined('ABSPATH')) {
    exit;
}

// Chargement automatique des classes (PSR-4 ou simple)
require_once __DIR__ . '/includes/autoload.php';

// Initialisation du plugin
add_action('plugins_loaded', function () {
    if (class_exists('CorbiDev\ApiBuilder\Core\Loader')) {
        CorbiDev\ApiBuilder\Core\Loader::init();
    }
});
