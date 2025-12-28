<?php
/*
Plugin Name: wp-corbidev-api-modele
Description: Modèle de plugin enfant pour ajouter une API au socle générique wp-corbidev-api
Version: 1.0.0
Author: Équipe Dev
*/

// Déclarer le dossier de pages API à ajouter au socle générique
add_filter('corbidev_api_pages_dirs', function($dirs) {
    $dirs[] = __DIR__ . '/includes/Admin';
    return $dirs;
});