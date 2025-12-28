<?php
/*
Plugin Name: wp-corbidev-api-test
Description: Plugin enfant pour l'API Test du socle générique wp-corbidev-api
Version: 1.0.0
Author: Équipe Dev
*/

add_filter('corbidev_api_pages_dirs', function($dirs) {
    $dirs[] = __DIR__ . '/includes/Admin';
    return $dirs;
});