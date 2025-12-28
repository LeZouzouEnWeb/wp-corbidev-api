<?php

namespace CorbiDev\ApiBuilder\Admin\Pages;

use CorbiDev\ApiBuilder\Database\ManifestRepository;

class ApiAddPage
{
    private const ACTION_CREATE = 'corbidev_api_builder_create';

    public static function render(): void
    {
        echo '<div class="p-8">';

        // Header modern avec fond dégradé
        echo '<div class="max-w-4xl mx-auto mb-6">';
        echo '<div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl shadow-lg p-6 flex items-center justify-between gap-4">';
        echo '<div>';
        echo '<h1 class="text-2xl font-bold text-white">' . esc_html__('Créer une nouvelle API', 'wp-corbidev-api-new') . '</h1>';
        echo '<p class="text-blue-100 mt-1 text-sm">' . esc_html__('Définissez les informations principales de votre modèle d\'API. Le mode sera créé en édition.', 'wp-corbidev-api-new') . '</p>';
        echo '</div>';
        echo '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/10 text-blue-100 border border-white/20">';
        echo esc_html__('Mode : En édition', 'wp-corbidev-api-new');
        echo '</span>';
        echo '</div>';
        echo '</div>';

        // Carte formulaire
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" class="max-w-4xl mx-auto space-y-6 bg-white p-6 rounded-xl shadow">';
        wp_nonce_field(self::ACTION_CREATE);
        echo '<input type="hidden" name="action" value="' . esc_attr(self::ACTION_CREATE) . '">';
        echo '<input type="hidden" name="mode" value="edition">';

        self::render_input('name', __('Nom de l\'API', 'wp-corbidev-api-new'), 'text', 'API Modèle');
        self::render_input('slug', __('Slug', 'wp-corbidev-api-new'), 'text', 'api-modele');
        self::render_textarea('description', __('Description', 'wp-corbidev-api-new'), __('Modèle de plugin enfant...', 'wp-corbidev-api-new'));
        self::render_input('version', __('Version', 'wp-corbidev-api-new'), 'text', '1.0.0');

        echo '<div><label class="block text-sm font-medium mb-1">' . esc_html__('Permissions CRUD', 'wp-corbidev-api-new') . '</label>';
        $crud = ['create', 'read', 'update', 'delete'];
        echo '<div class="flex gap-4">';
        foreach ($crud as $perm) {
            echo '<label class="inline-flex items-center gap-1"><input type="checkbox" name="permissions_crud[]" value="' . esc_attr($perm) . '" checked>' . esc_html(ucfirst($perm)) . '</label>';
        }
        echo '</div></div>';

        echo '<div><label class="block text-sm font-medium mb-1">' . esc_html__('Utilisateurs autorisés (emails, séparés par des virgules)', 'wp-corbidev-api-new') . '</label>';
        echo '<input type="text" name="permissions_users" class="w-full border rounded px-3 py-2" placeholder="">';
        echo '<p class="text-xs text-gray-500 mt-1">' . esc_html__('Laissez vide pour autoriser selon les capacités WordPress.', 'wp-corbidev-api-new') . '</p>';
        echo '</div>';

        echo '<div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">';
        echo '<a href="' . esc_url(admin_url('admin.php?page=corbidev-api-builder')) . '" class="px-4 py-2 text-sm rounded border border-gray-300 text-gray-700 hover:bg-gray-50">' . esc_html__('Annuler', 'wp-corbidev-api-new') . '</a>';
        echo '<button type="submit" class="px-5 py-2.5 text-sm font-semibold bg-blue-600 text-white rounded shadow hover:bg-blue-700 hover:shadow-md transition">' . esc_html__('Créer l\'API', 'wp-corbidev-api-new') . '</button>';
        echo '</div>';
        echo '</form>';
        echo '</div>';
    }

    public static function handle_create(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('Accès refusé.', 'wp-corbidev-api-new'));
        }

        check_admin_referer(self::ACTION_CREATE);

        $name        = sanitize_text_field(wp_unslash($_POST['name'] ?? ''));
        $slug        = sanitize_title(wp_unslash($_POST['slug'] ?? ''));
        $description = sanitize_textarea_field(wp_unslash($_POST['description'] ?? ''));
        $version     = sanitize_text_field(wp_unslash($_POST['version'] ?? '1.0.0'));
        $mode        = 'edition';
        $permissions_crud  = array_map('sanitize_text_field', $_POST['permissions_crud'] ?? []);
        $permissions_users = array_filter(array_map('trim', explode(',', sanitize_text_field(wp_unslash($_POST['permissions_users'] ?? '')))));

        if (!$name || !$slug) {
            self::redirect_with_error(__('Le nom et le slug sont obligatoires.', 'wp-corbidev-api-new'));
        }

        if (ManifestRepository::slug_exists($slug)) {
            self::redirect_with_error(__('Ce slug existe déjà.', 'wp-corbidev-api-new'));
        }

        $modules = json_decode(self::default_modules_json(), true);

        $now_iso = current_time('Y-m-d\TH:i:s\Z', true);

        $manifest = [
            'name'        => $name,
            'description' => $description,
            'version'     => $version,
            'mode'        => $mode,
            'modules'     => $modules,
            'permissions' => [
                'crud'  => $permissions_crud ?: ['create', 'read', 'update', 'delete'],
                'users' => $permissions_users,
            ],
            'meta' => [
                'created_at' => $now_iso,
                'updated_at' => $now_iso,
                'deprecated' => false,
                'expires_at' => null,
            ],
        ];

        try {
            ManifestRepository::insert_model_with_version(
                [
                    'slug'        => $slug,
                    'name'        => $name,
                    'description' => $description,
                    'version'     => $version,
                    'status'      => $mode,
                ],
                $manifest
            );
        } catch (\Throwable $e) {
            self::redirect_with_error($e->getMessage());
        }

        wp_safe_redirect(add_query_arg(['page' => 'corbidev-api-builder', 'created' => 1], admin_url('admin.php')));
        exit;
    }

    private static function render_input(string $name, string $label, string $type, string $value = ''): void
    {
        echo '<div><label class="block text-sm font-medium mb-1" for="' . esc_attr($name) . '">' . esc_html($label) . '</label>';
        echo '<input type="' . esc_attr($type) . '" id="' . esc_attr($name) . '" name="' . esc_attr($name) . '" class="w-full border rounded px-3 py-2" value="' . esc_attr($value) . '">';
        echo '</div>';
    }

    private static function render_textarea(string $name, string $label, string $value = ''): void
    {
        echo '<div><label class="block text-sm font-medium mb-1" for="' . esc_attr($name) . '">' . esc_html($label) . '</label>';
        echo '<textarea id="' . esc_attr($name) . '" name="' . esc_attr($name) . '" rows="3" class="w-full border rounded px-3 py-2">' . esc_textarea($value) . '</textarea>';
        echo '</div>';
    }

    private static function default_modules_json(): string
    {
        $template = [
            [
                'name' => 'modele',
                'tabs' => [
                    [
                        'name'   => 'Exemple',
                        'fields' => [
                            [
                                'key'        => 'example_field',
                                'label'      => 'Champ exemple',
                                'type'       => 'input',
                                'validation' => ['required' => false],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return wp_json_encode($template, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    private static function redirect_with_error(string $message): void
    {
        wp_safe_redirect(add_query_arg(
            [
                'page'  => 'corbidev-api-builder',
                'error' => rawurlencode($message),
            ],
            admin_url('admin.php')
        ));
        exit;
    }
}
