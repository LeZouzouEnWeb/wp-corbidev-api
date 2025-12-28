<?php

namespace CvHeadlessApi\Cv;

class CvOpenApi
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
            'title'       => 'API CV',
            'version'     => '1.0.0',
            'description' => 'API REST exposant les contenus du CV depuis WordPress'
        ];
    }

    public static function getServers(): array
    {
        return [
            'url' => home_url('/wp-json/cv/v1'),
            'description' => 'Production'
        ];
    }

    public static function getPaths(): array
    {
        return [
            '/status' => [
                'get' => [
                    'summary' => 'Vérifier la disponibilité de l’API CV',
                    'responses' => [
                        '200' => [
                            'description' => 'Statut de l’API',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/ApiStatus'
                                    ],
                                    'examples' => [
                                        'value' => self::exampleStatus()
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
                                            'value' => self::exampleContenusMeta()
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
            ]
        ];
    }

    public static function getComponentsSchemas(): array
    {
        return [
            'ApiStatus' => [
                'type' => 'object',
                'properties' => [
                    'status' => ['type' => 'string']
                ]
            ],
            'Contenus' => self::schemaContenus(),
            'ContenusMeta' => self::schemaContenusMeta(),
            'Module' => self::schemaModule(),
            'Identity' => self::schemaIdentity(),
            'Contact' => self::schemaContact()
        ];
    }

    // =========================
    // EXEMPLES DE MODULES
    // =========================
    public static function exampleIdentity(): array
    {
        return [
            'first_name' => 'Eric',
            'last_name'  => 'Corbisier',
            'job_title'  => 'Développeur Web',
            'summary'    => 'Développeur PHP / WordPress orienté API',
            'photo_url'  => 'https://api-cv.corbisier.fr/wp-content/uploads/photo.jpg'
        ];
    }

    public static function exampleContact(): array
    {
        return [
            'email' => 'contact@domaine.fr',
            'phone' => '+33 6 00 00 00 00',
            'website' => 'https://ton-site.fr',
            'linkedin' => 'https://linkedin.com/in/user_name'
        ];
    }

    public static function exampleSavoirEtre(): array
    {
        return [
            'Autonome',
            'Rigoureux',
            'Esprit d’analyse'
        ];
    }

    public static function exampleAutresInformations(): array
    {
        return [
            'Permis B',
            'Télétravail possible'
        ];
    }

    public static function exampleContenus(): array
    {
        return [
            'identity' => self::exampleIdentity(),
            'contact' => self::exampleContact(),
            'savoir_etre' => self::exampleSavoirEtre(),
            'autres_informations' => self::exampleAutresInformations()
        ];
    }

    public static function exampleContenusMeta(): array
    {
        return [
            'modules' => [
                'identity',
                'contact',
                'savoir_etre',
                'autres_informations'
            ]
        ];
    }

    public static function exampleStatus(): array
    {
        return [

            'status' => 'ok',
            'message' => 'API CV disponible',
            'timestamp' => 1703610000
        ];
    }

    // =========================
    // SCHÉMAS DE MODULES
    // =========================
    public static function schemaIdentity(): array
    {
        return [
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
        ];
    }

    public static function schemaContact(): array
    {
        return [
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
        ];
    }

    public static function schemaSavoirEtre(): array
    {
        return [
            'type' => 'array',
            'items' => ['type' => 'string']
        ];
    }

    public static function schemaAutresInformations(): array
    {
        return [
            'type' => 'array',
            'items' => ['type' => 'string']
        ];
    }

    public static function schemaContenus(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'identity' => self::schemaIdentity(),
                'contact' => self::schemaContact(),
                'savoir_etre' => self::schemaSavoirEtre(),
                'autres_informations' => self::schemaAutresInformations()
            ]
        ];
    }

    public static function schemaContenusMeta(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'modules' => [
                    'type' => 'array',
                    'items' => ['type' => 'string']
                ]
            ]
        ];
    }

    public static function schemaModule(): array
    {
        return [
            'type' => 'object',
            'additionalProperties' => true,
            'description' => 'Structure variable selon le module'
        ];
    }
}