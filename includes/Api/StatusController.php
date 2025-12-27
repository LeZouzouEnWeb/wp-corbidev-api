<?php
// Contrôleur générique pour endpoint de status (health check)


class StatusController
{
    /**
     * GET /api/v1/status
     * Retourne un status HTTP 200 et un message simple
     */
    public static function getStatus(): WP_REST_Response
    {
        return new WP_REST_Response([
            'status' => 'ok',
            'message' => 'API disponible',
            'timestamp' => time(),
        ], 200);
    }
}