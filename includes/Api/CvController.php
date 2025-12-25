<?php

namespace Api;

use Storage\OptionStore;
use WP_REST_Request;
use WP_REST_Response;

class CvController
{
    /**
     * =====================================================
     * GET /cv/v1/contenus
     * =====================================================
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
     * =====================================================
     * GET /cv/v1/contenus/{module}
     * =====================================================
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
     * =====================================================
     * GET /cv/v1/contenus/meta
     * =====================================================
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
     * =====================================================
     * Enrichissement automatique des champs *_id
     * =====================================================
     */
    private static function enrichWithMedia(array $data): array
    {
        return self::walkAndResolveMedia($data);
    }

    /**
     * Parcours récursif du tableau
     */
    private static function walkAndResolveMedia(array $data): array
    {
        foreach ($data as $key => $value) {

            // Cas récursif
            if (is_array($value)) {
                $data[$key] = self::walkAndResolveMedia($value);
                continue;
            }

            // Détection *_id
            if (is_string($key) && substr($key, -3) === '_id' && is_numeric($value)) {
                $media = self::resolveMedia((int) $value);

                if ($media !== null) {
                    $baseKey = substr($key, 0, -3); // photo_id → photo

                    unset($data[$key]);

                    $data[$baseKey] = [
                        'id' => (int) $value,
                        'media' => $media,
                    ];
                }
            }
        }

        return $data;
    }

    /**
     * Résolution d’un média WordPress à partir de son ID
     */
    private static function resolveMedia(int $media_id): ?array
    {
        $post = get_post($media_id);

        if (!$post || $post->post_type !== 'attachment') {
            return null;
        }

        $url = wp_get_attachment_url($media_id);
        $alt = get_post_meta($media_id, '_wp_attachment_image_alt', true);
        $meta = wp_get_attachment_metadata($media_id);

        $sizes = [];

        if (!empty($meta['sizes'])) {
            foreach ($meta['sizes'] as $size => $_) {
                $sizeUrl = wp_get_attachment_image_url($media_id, $size);
                if ($sizeUrl) {
                    $sizes[$size] = $sizeUrl;
                }
            }
        }

        return [
            'url' => $url,
            'alt' => $alt ?: '',
            'sizes' => $sizes,
        ];
    }
}
