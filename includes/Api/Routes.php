<?php
namespace Api;

class Routes
{
    public static function register(): void
    {
        register_rest_route('cv/v1', '/all', [
            'methods' => 'GET',
            'callback' => ['Api\\CvController', 'all'],
            'permission_callback' => '__return_true'
        ]);

        register_rest_route('cv/v1', '/module/(?P<module>[a-z_]+)', [
            'methods' => 'GET',
            'callback' => ['Api\\CvController', 'module'],
            'permission_callback' => '__return_true'
        ]);
    }
}
