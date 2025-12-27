<?php

// Ce contrôleur est désormais spécifique au module CV (Pages_cv)
// Il n'utilise pas de namespace global, pour être auto-chargé par le loader du module

use Storage\OptionStore;

class CvController
{
    /**
     * GET /cv/v1/status
     * Endpoint de status simple
     */
    public static function getStatus(): WP_REST_Response
    {
        return new WP_REST_Response([
            'status' => 'ok',
            'message' => 'API CV disponible',
            'timestamp' => time(),
        ], 200);
    }


    /**
     * GET /cv/v1/contenus
     * Retourne l’ensemble du CV enrichi avec les médias
     */
    public static function getAll(): WP_REST_Response
    {
        $data = OptionStore::get('contenus', []);
        return new WP_REST_Response([
            'version' => '1.0',
            'data' => self::enrichWithMedia($data),
        ], 200);
    }

    /**
     * GET /cv/v1/contenus/{module}
     * Retourne un module précis
     */
    public static function getModule(WP_REST_Request $request): WP_REST_Response
    {
        $module = sanitize_key($request->get_param('module'));
        $data = OptionStore::get('contenus', []);
        if (!isset($data[$module])) {
            return new WP_REST_Response([
                'error' => 'module_not_found',
                'message' => 'Module inexistant',
            ], 404);
        }
        return new WP_REST_Response([
            'module' => $module,
            'data' => self::enrichWithMedia($data[$module]),
        ], 200);
    }

    /**
     * GET /cv/v1/contenus/meta
     * Métadonnées de l’API
     */
    public static function getMeta(): WP_REST_Response
    {
        return new WP_REST_Response([
            'storage' => 'cv_contenus',
            'version' => '1.0',
            'modules' => [
                'identity',
                'contact',
                'savoir_etre',
                'autres_informations',
            ],
            'media' => [
                'strategy' => 'resolve_from_ids',
                'supported_suffix' => '_id',
            ],
        ], 200);
    }

    /**
     * Enrichissement automatique des champs *_id
     */
    private static function enrichWithMedia(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = self::enrichWithMedia($value);
            } elseif (is_string($key) && substr($key, -3) === '_id' && is_numeric($value)) {
                // On tente de résoudre l'image via MediaHelper
                if (class_exists('Api\\MediaHelper')) {
                    $media = \Api\MediaHelper::resolve((int)$value);
                    if ($media) {
                        $data[substr($key, 0, -3) . 'media'] = $media;
                    }
                }
            }
        }
        return $data;
    }
}