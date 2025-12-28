<?php
namespace CorbiDev\ApiBuilder\Admin;

use CorbiDev\ApiBuilder\Database\ManifestRepository;
use CorbiDev\ApiBuilder\Admin\Pages\ApiListPage;
use CorbiDev\ApiBuilder\Admin\Pages\ApiAddPage;

class MenuApiBuilder
{
    private const MENU_SLUG    = 'corbidev-api-builder';
    private const ADD_SLUG     = 'corbidev-api-builder-add';
    private const ACTION_CREATE = 'corbidev_api_builder_create';
    private const ACTION_DELETE = 'corbidev_api_builder_delete';

    public static function register(): void
    {
        add_action('admin_menu', [self::class, 'add_menu']);
        add_action('admin_head', [self::class, 'print_tailwind']);
        add_action('admin_post_' . self::ACTION_CREATE, [ApiAddPage::class, 'handle_create']);
        add_action('admin_post_' . self::ACTION_DELETE, [ApiListPage::class, 'handle_delete']);
        add_action('admin_init', [ManifestRepository::class, 'ensure_tables']);
    }

    public static function add_menu(): void
    {
        add_menu_page(
            'API Builder',
            'API Builder',
            'manage_options',
            self::MENU_SLUG,
            [ApiListPage::class, 'render'],
            'dashicons-rest-api',
            30
        );

        add_submenu_page(
            self::MENU_SLUG,
            __('Ajouter une API', 'wp-corbidev-api-new'),
            __('Ajouter une API', 'wp-corbidev-api-new'),
            'manage_options',
            self::ADD_SLUG,
            [ApiAddPage::class, 'render']
        );
    }

    public static function print_tailwind(): void
    {
        $page = $_GET['page'] ?? '';
        if (!in_array($page, [self::MENU_SLUG, self::ADD_SLUG], true)) {
            return;
        }

        echo '<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>';
        echo '<style type="text/tailwindcss">@theme { --color-clifford: #da373d; }</style>';
    }

}