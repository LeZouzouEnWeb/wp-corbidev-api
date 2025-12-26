<?php
namespace CvHeadlessApi\Test;

class TestOpenApi
{
    // =========================
    // Méthodes principales pour OpenApi global
    // =========================
    public static function getOpenApiVersion(): string
    {
        return '3.0.3';
    }

    public static function getInfo(): array
    {
        return [
            'title'       => 'API Test',
            'version'     => '1.0.0',
            'description' => 'API REST de test pour démonstration OpenAPI'
        ];
    }

    public static function getServers(): array
    {
        return [
            [
                'url' => home_url('/wp-json/test/v1'),
                'description' => 'Production'
            ]
        ];
    }

    public static function getPaths(): array
    {
        return [
            '/hello' => [
                'get' => [
                    'summary' => 'Message de test',
                    'responses' => [
                        '200' => [
                            'description' => 'Réponse hello',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/HelloResponse'
                                    ],
                                    'examples' => [
                                        'exemple' => [
                                            'value' => self::exampleHello()
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public static function getComponentsSchemas(): array
    {
        return [
            'HelloResponse' => self::schemaHello()
        ];
    }

    // =========================
    // EXEMPLE
    // =========================
    public static function exampleHello(): array
    {
        return [
            'message' => 'Hello depuis l’API de test !',
            'timestamp' => 1703610000
        ];
    }

    // =========================
    // SCHÉMA
    // =========================
    public static function schemaHello(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'message' => ['type' => 'string'],
                'timestamp' => ['type' => 'integer']
            ]
        ];
    }
}