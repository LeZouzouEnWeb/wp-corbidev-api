<?php

namespace CorbiDev\ApiBuilder\Database;

use wpdb;

class ManifestRepository
{
    public static function ensure_tables(): void
    {
        global $wpdb;
        $models_table   = self::get_models_table();
        $versions_table = self::get_versions_table();
        $charset        = $wpdb->get_charset_collate();

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $sql_models = "CREATE TABLE {$models_table} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            slug varchar(191) NOT NULL,
            name varchar(255) NOT NULL,
            description text NULL,
            active_version_id bigint(20) unsigned NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY slug (slug)
        ) {$charset};";

        $sql_versions = "CREATE TABLE {$versions_table} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            model_id bigint(20) unsigned NOT NULL,
            version varchar(50) NOT NULL,
            status varchar(50) NOT NULL DEFAULT 'edition',
            manifest_json longtext NOT NULL,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            expires_at datetime NULL,
            PRIMARY KEY  (id),
            KEY model_id (model_id)
        ) {$charset};";

        dbDelta($sql_models);
        dbDelta($sql_versions);
    }

    public static function insert_model_with_version(array $model_data, array $manifest): array
    {
        global $wpdb;
        self::ensure_tables();

        $models_table   = self::get_models_table();
        $versions_table = self::get_versions_table();
        $now            = current_time('mysql');

        $wpdb->insert(
            $models_table,
            [
                'slug'        => $model_data['slug'],
                'name'        => $model_data['name'],
                'description' => $model_data['description'],
                'created_at'  => $now,
            ],
            ['%s', '%s', '%s', '%s']
        );

        $model_id = (int) $wpdb->insert_id;

        if (!$model_id) {
            throw new \RuntimeException(__('Impossible de créer le modèle.', 'wp-corbidev-api-new'));
        }

        $wpdb->insert(
            $versions_table,
            [
                'model_id'      => $model_id,
                'version'       => $model_data['version'],
                'status'        => $model_data['status'],
                'manifest_json' => wp_json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                'created_at'    => $now,
                'updated_at'    => $now,
                'expires_at'    => null,
            ],
            ['%d', '%s', '%s', '%s', '%s', '%s', '%s']
        );

        $version_id = (int) $wpdb->insert_id;

        if (!$version_id) {
            throw new \RuntimeException(__('Impossible de créer la version.', 'wp-corbidev-api-new'));
        }

        return [$model_id, $version_id];
    }

    public static function models_with_latest_versions(): array
    {
        global $wpdb;
        self::ensure_tables();

        $models_table   = self::get_models_table();
        $versions_table = self::get_versions_table();

        $models = $wpdb->get_results("SELECT * FROM {$models_table} ORDER BY created_at DESC", ARRAY_A);

        foreach ($models as &$model) {
            $version = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM {$versions_table} WHERE model_id = %d ORDER BY created_at DESC LIMIT 1",
                    $model['id']
                ),
                ARRAY_A
            );

            $model['version_info'] = $version ?: null;
        }

        return $models;
    }

    public static function get_model_with_latest_version(int $model_id): ?array
    {
        global $wpdb;
        self::ensure_tables();

        $models_table   = self::get_models_table();
        $versions_table = self::get_versions_table();

        $model = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$models_table} WHERE id = %d", $model_id),
            ARRAY_A
        );

        if (!$model) {
            return null;
        }

        $version = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$versions_table} WHERE model_id = %d ORDER BY created_at DESC LIMIT 1",
                $model_id
            ),
            ARRAY_A
        );

        $model['version_info'] = $version ?: null;

        return $model;
    }

    public static function slug_exists(string $slug): bool
    {
        global $wpdb;
        self::ensure_tables();
        $models_table = self::get_models_table();
        return (bool) $wpdb->get_var($wpdb->prepare("SELECT COUNT(1) FROM {$models_table} WHERE slug = %s", $slug));
    }

    public static function get_models_table(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'api_models';
    }

    public static function get_versions_table(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'api_model_versions';
    }
}