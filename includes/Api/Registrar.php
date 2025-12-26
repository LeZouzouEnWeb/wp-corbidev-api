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
        if (empty($manifest['routes']) || !is_array($manifest['routes'])) {
            return;
        }

        foreach ($manifest['routes'] as $route) {
            if (empty($route['namespace']) || empty($route['route']) || empty($route['methods']) || empty($route['callback'])) {
                continue;
            }

            $callback = $route['callback'];
            // Support "Class@method" or "Class::method" notation
            if (is_string($callback) && (strpos($callback, '@') !== false || strpos($callback, '::') !== false)) {
                $parts = preg_split('/[@:]{1,2}/', $callback);
                if (count($parts) === 2) {
                    $callback = [
                        class_exists($parts[0]) ? $parts[0] : (isset($manifest['namespace']) ? $manifest['namespace'] . '\\' . $parts[0] : $parts[0]),
                        $parts[1]
                    ];
                }
            }

            $args = [
                'methods' => $route['methods'],
                'callback' => $callback,
                'permission_callback' => $route['permission_callback'] ?? '__return_true',
            ];
            if (!empty($route['args'])) {
                $args['args'] = $route['args'];
            }

            register_rest_route($route['namespace'], $route['route'], $args);
        }
    }
}
