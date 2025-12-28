<?php
// Enregistrement des routes génériques du plugin

add_action('rest_api_init', function () {
    // S'assurer que la classe OpenApi est chargée
    if (!class_exists('OpenApi')) {
        require_once __DIR__ . '/OpenApi.php';
    }
    register_rest_route('api/v1', '/openapi', [
        'methods' => 'GET',
        'callback' => ['OpenApi', 'generate'],
        'permission_callback' => '__return_true',
    ]);

    // Route générique pour OpenApiPage (par slug)
    if (!class_exists('OpenApiPage')) {
        require_once __DIR__ . '/OpenApiPage.php';
    }
    register_rest_route('api/v1', '/openapi-page/(?P<slug>[a-zA-Z0-9_-]+)', [
        'methods' => 'GET',
        'callback' => ['OpenApiPage', 'generate'],
        'permission_callback' => '__return_true',
        'args' => [
            'slug' => [
                'required' => true,
                'type' => 'string',
                'description' => 'Slug du module à exposer'
            ]
        ]
    ]);
    // ... autres routes génériques à ajouter ici
});