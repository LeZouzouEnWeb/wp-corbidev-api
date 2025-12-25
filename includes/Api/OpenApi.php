<?php

namespace Api;

use WP_REST_Request;
use WP_REST_Response;

/**
 * Générateur OpenAPI 3.0.3 pour l'API CV
 */
class OpenApi
{
    public static function generate(WP_REST_Request $request): WP_REST_Response
    {
        // Préflight CORS
        if ($request->get_method() === 'OPTIONS') {
            return new WP_REST_Response(null, 200, self::corsHeaders());
        }

        $spec = [
            'openapi' => '3.0.3',
            'info' => [
                'title'       => 'API CV',
                'version'     => '1.0.0',
                'description' => 'API REST exposant les contenus du CV depuis WordPress'
            ],
            'servers' => [
                [
                    'url' => home_url('/wp-json/cv/v1'),
                    'description' => 'Production'
                ]
            ],

            /* =========================
             * PATHS
             * ========================= */
            'paths' => [

                '/' => [
                    'get' => [
                        'summary' => 'Endpoint racine',
                        'responses' => [
                            '200' => [
                                'description' => 'API accessible',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/ApiStatus'
                                        ],
                                        'examples' => [
                                            'ok' => [
                                                'value' => ['status' => 'ok']
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],

                '/contenus' => [
                    'get' => [
                        'summary' => 'Récupérer tous les contenus du CV',
                        'responses' => [
                            '200' => [
                                'description' => 'Tous les contenus',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/Contenus'
                                        ],
                                        'examples' => [
                                            'exemple' => [
                                                'value' => self::exampleContenus()
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],

                '/contenus/meta' => [
                    'get' => [
                        'summary' => 'Métadonnées des contenus',
                        'responses' => [
                            '200' => [
                                'description' => 'Liste des modules disponibles',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/ContenusMeta'
                                        ],
                                        'examples' => [
                                            'exemple' => [
                                                'value' => [
                                                    'modules' => [
                                                        'identity',
                                                        'contact',
                                                        'savoir_etre',
                                                        'autres_informations'
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],

                '/contenus/{module}' => [
                    'get' => [
                        'summary' => 'Récupérer un module spécifique',
                        'parameters' => [
                            [
                                'name' => 'module',
                                'in' => 'path',
                                'required' => true,
                                'schema' => ['type' => 'string'],
                                'example' => 'identity'
                            ]
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Contenu du module',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/Module'
                                        ],
                                        'examples' => [
                                            'identity' => [
                                                'value' => self::exampleIdentity()
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            '404' => [
                                'description' => 'Module inexistant'
                            ]
                        ]
                    ]
                ],

                /* ===== Legacy ===== */

                '/all' => [
                    'get' => [
                        'summary' => 'Ancien endpoint global (legacy)',
                        'deprecated' => true,
                        'responses' => [
                            '200' => [
                                'description' => 'Succès',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/Contenus'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],

                '/module/{module}' => [
                    'get' => [
                        'summary' => 'Ancien endpoint module (legacy)',
                        'deprecated' => true,
                        'parameters' => [
                            [
                                'name' => 'module',
                                'in' => 'path',
                                'required' => true,
                                'schema' => ['type' => 'string']
                            ]
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Succès',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/Module'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],

            /* =========================
             * COMPONENTS / SCHEMAS
             * ========================= */
            'components' => [
                'schemas' => [

                    'ApiStatus' => [
                        'type' => 'object',
                        'properties' => [
                            'status' => ['type' => 'string']
                        ]
                    ],

                    'Contenus' => [
                        'type' => 'object',
                        'properties' => [
                            'identity' => ['$ref' => '#/components/schemas/Identity'],
                            'contact' => ['$ref' => '#/components/schemas/Contact'],
                            'savoir_etre' => [
                                'type' => 'array',
                                'items' => ['type' => 'string']
                            ],
                            'autres_informations' => [
                                'type' => 'array',
                                'items' => ['type' => 'string']
                            ]
                        ]
                    ],

                    'ContenusMeta' => [
                        'type' => 'object',
                        'properties' => [
                            'modules' => [
                                'type' => 'array',
                                'items' => ['type' => 'string']
                            ]
                        ]
                    ],

                    'Module' => [
                        'type' => 'object',
                        'additionalProperties' => true,
                        'description' => 'Structure variable selon le module'
                    ],

                    'Identity' => [
                        'type' => 'object',
                        'properties' => [
                            'first_name' => ['type' => 'string'],
                            'last_name' => ['type' => 'string'],
                            'job_title' => ['type' => 'string'],
                            'summary' => ['type' => 'string'],
                            'photo_url' => [
                                'type' => 'string',
                                'format' => 'uri'
                            ]
                        ]
                    ],

                    'Contact' => [
                        'type' => 'object',
                        'properties' => [
                            'email' => [
                                'type' => 'string',
                                'format' => 'email'
                            ],
                            'phone' => ['type' => 'string'],
                            'website' => [
                                'type' => 'string',
                                'format' => 'uri'
                            ],
                            'linkedin' => [
                                'type' => 'string',
                                'format' => 'uri'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return new WP_REST_Response(
            $spec,
            200,
            array_merge(
                ['Content-Type' => 'application/json; charset=utf-8'],
                self::corsHeaders()
            )
        );
    }

    /* =========================
     * EXEMPLES
     * ========================= */

    private static function exampleIdentity(): array
    {
        return [
            'first_name' => 'Eric',
            'last_name'  => 'Corbisier',
            'job_title'  => 'Développeur Web',
            'summary'    => 'Développeur PHP / WordPress orienté API',
            'photo_url'  => 'https://api-cv.corbisier.fr/wp-content/uploads/photo.jpg'
        ];
    }

    private static function exampleContenus(): array
    {
        return [
            'identity' => self::exampleIdentity(),
            'contact' => [
                'email' => 'contact@corbisier.fr',
                'phone' => '+33 6 00 00 00 00',
                'website' => 'https://corbisier.fr',
                'linkedin' => 'https://linkedin.com/in/ericcorbisier'
            ],
            'savoir_etre' => [
                'Autonome',
                'Rigoureux',
                'Esprit d’analyse'
            ],
            'autres_informations' => [
                'Permis B',
                'Télétravail possible'
            ]
        ];
    }

    private static function corsHeaders(): array
    {
        return [
            'Access-Control-Allow-Origin'  => '*',
            'Access-Control-Allow-Methods' => 'GET, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization'
        ];
    }
}