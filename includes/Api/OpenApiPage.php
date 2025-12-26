<?php
// Contrôleur générique pour exposer l'OpenAPI d'un module selon le slug passé en paramètre
use WP_REST_Request;
use WP_REST_Response;

class OpenApiPage
{
    public static function generate(WP_REST_Request $request): WP_REST_Response
    {
        $slug = $request->get_param('slug');
        if (!$slug) {
            return new WP_REST_Response(['error' => 'Paramètre slug manquant'], 400);
        }
        $namespace = 'CvHeadlessApi\\' . ucfirst($slug);
        $className = ucfirst($slug) . 'OpenApi';
        $fqcn = $namespace . '\\' . $className;

        // Recherche du dossier du module
        $base = dirname(__DIR__) . '/Admin/Pages/' . $slug;
        $openapiFile = $base . '/OpenApi.php';
        if (!file_exists($openapiFile)) {
            return new WP_REST_Response(['error' => 'Module ou OpenApi non trouvé'], 404);
        }
        if (!class_exists($fqcn)) {
            require_once $openapiFile;
        }
        if (!class_exists($fqcn)) {
            return new WP_REST_Response(['error' => 'Classe OpenApi non trouvée'], 404);
        }

        $openapi_version = method_exists($fqcn, 'getOpenApiVersion') ? $fqcn::getOpenApiVersion() : '3.0.3';
        $info = method_exists($fqcn, 'getInfo') ? $fqcn::getInfo() : ['title' => $slug, 'version' => '1.0.0'];
        $servers = method_exists($fqcn, 'getServers') ? $fqcn::getServers() : [];
        $paths = method_exists($fqcn, 'getPaths') ? $fqcn::getPaths() : [];
        $schemas = method_exists($fqcn, 'getComponentsSchemas') ? $fqcn::getComponentsSchemas() : [];

        $spec = [
            'openapi' => $openapi_version,
            'info' => $info,
            'servers' => $servers,
            'paths' => $paths,
            'components' => [
                'schemas' => $schemas
            ]
        ];
        return new WP_REST_Response($spec, 200);
    }
}