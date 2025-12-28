<?php
// Routeur générique pour lister toutes les APIs découvertes dynamiquement

class GenericApiRouter
{
    public static function listApis() {
        $apis = $GLOBALS['cv_headless_discovered_apis'] ?? [];
        $result = [];
        foreach ($apis as $slug => $manifest) {
            $namespace = $manifest['slug'] ?? $slug;
            $openapi_url = rest_url("$namespace/v1/openapi");
            $result[] = [
                'slug' => $namespace,
                'name' => $manifest['name'] ?? ($manifest['display_name'] ?? $namespace),
                'description' => $manifest['description'] ?? '',
                'version' => $manifest['version'] ?? '1.0.0',
                'openapi_url' => $openapi_url,
            ];
        }
        return rest_ensure_response($result);
    }
}
