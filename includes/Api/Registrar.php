<?php
namespace Api;

class Registrar
{
    /**
     * Register REST routes from a manifest array description.
     * This is a stub implementation — adapt to your manifest schema.
     *
     * @param array $manifest
     * @return void
     */
    public static function registerRestFromManifest(array $manifest): void
    {
        if (empty($manifest['slug'])) {
            return;
        }

        $slug = $manifest['slug'];

        if (!function_exists('register_rest_route')) {
            return;
        }

        // Example: register a simple info endpoint for the API
        register_rest_route($slug . '/v1', '/info', [
            'methods' => 'GET',
            'callback' => function () use ($manifest) {
                return [
                    'slug' => $manifest['slug'] ?? null,
                    'name' => $manifest['display_name'] ?? null,
                ];
            },
            'permission_callback' => '__return_true',
        ]);
    }
}
