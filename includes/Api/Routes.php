<?php

namespace Api;

class Routes
{
    public static function register(): void
    {
        /*
         * =====================================================
         *  API REST NORMALISÉE (recommandée)
         * =====================================================
         */

        // GET /cv/v1/contenus
        register_rest_route('cv/v1', '/contenus', [
            'methods'  => 'GET',
            'callback' => [CvController::class, 'getAll'],
            'permission_callback' => '__return_true',
        ]);

        // GET /cv/v1/contenus/{module}
        register_rest_route('cv/v1', '/contenus/(?P<module>[a-z_]+)', [
            'methods'  => 'GET',
            'callback' => [CvController::class, 'getModule'],
            'permission_callback' => '__return_true',
        ]);

        // GET /cv/v1/contenus/meta
        register_rest_route('cv/v1', '/contenus/meta', [
            'methods'  => 'GET',
            'callback' => [CvController::class, 'getMeta'],
            'permission_callback' => '__return_true',
        ]);

        /*
         * =====================================================
         *  OPENAPI / SWAGGER
         * =====================================================
         */

        // GET /cv/v1/openapi
        register_rest_route('cv/v1', '/openapi', [
            'methods'  => 'GET',
            'callback' => [OpenApi::class, 'generate'],
            'permission_callback' => '__return_true',
        ]);

        /*
         * =====================================================
         *  ALIAS (ANCIENNES ROUTES — optionnel)
         * =====================================================
         *  À garder temporairement pour compatibilité
         */

        // Ancien : GET /cv/v1/all
        register_rest_route('cv/v1', '/all', [
            'methods'  => 'GET',
            'callback' => [CvController::class, 'getAll'],
            'permission_callback' => '__return_true',
        ]);

        // Ancien : GET /cv/v1/module/{module}
        register_rest_route('cv/v1', '/module/(?P<module>[a-z_]+)', [
            'methods'  => 'GET',
            'callback' => [CvController::class, 'getModule'],
            'permission_callback' => '__return_true',
        ]);
    }
}
