<?php
// OpenAPI générique minimaliste pour toutes les APIs découvertes

class OpenApiTable
{
    public static function generate(): WP_REST_Response
    {
        // Découverte dynamique des classes OpenApi de chaque Pages_**
        $modules = self::discoverModules();
        $openapi_docs = [];

        foreach ($modules as $slug => $class) {
            if (!class_exists($class)) continue;
            $openapi_version = method_exists($class, 'getOpenApiVersion') ? $class::getOpenApiVersion() : '3.0.3';
            $info = method_exists($class, 'getInfo') ? $class::getInfo() : ['title' => $slug, 'version' => '1.0.0'];
            $servers = method_exists($class, 'getServers') ? $class::getServers() : [];
            $paths = method_exists($class, 'getPaths') ? $class::getPaths() : [];
            $schemas = method_exists($class, 'getComponentsSchemas') ? $class::getComponentsSchemas() : [];

            $openapi_docs[] = [
                'openapi' => $openapi_version,
                'info' => $info,
                'servers' => $servers,
                'paths' => $paths,
                'components' => [
                    'schemas' => $schemas
                ]
            ];
        }

        return new WP_REST_Response($openapi_docs, 200);
    }

    /**
     * Découvre dynamiquement les classes OpenApi de chaque Pages_**
     * @return array slug => className
     */
    private static function discoverModules(): array
    {
        $base = dirname(__DIR__) . '/Admin/Pages/';
        $modules = [];
        foreach (glob($base . 'Pages_*', GLOB_ONLYDIR) as $dir) {
            $manifestFile = $dir . '/manifest.json';
            if (!file_exists($manifestFile)) continue;
            $manifest = json_decode(file_get_contents($manifestFile), true);
            if (empty($manifest['slug'])) continue;
            $slug = $manifest['slug'];
            $namespace = 'CvHeadlessApi\\' . ucfirst($slug);
            $className = ucfirst($slug) . 'OpenApi';
            $openapiFile = $dir . '/OpenApi.php';
            if (file_exists($openapiFile)) {
                if (!class_exists($namespace . '\\' . $className)) {
                    require_once $openapiFile;
                }
                $fqcn = $namespace . '\\' . $className;
                if (class_exists($fqcn)) {
                    $modules[$slug] = $fqcn;
                }
            }
        }
        return $modules;
    }
}