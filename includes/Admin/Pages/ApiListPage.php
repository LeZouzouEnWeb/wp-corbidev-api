<?php

namespace CorbiDev\ApiBuilder\Admin\Pages;

use CorbiDev\ApiBuilder\Database\ManifestRepository;

class ApiListPage
{
    public static function render(): void
    {
        $models        = ManifestRepository::models_with_latest_versions();
        $statuses_meta = self::statuses_meta();
        $created       = isset($_GET['created']) ? (int) $_GET['created'] : 0;
        $error         = isset($_GET['error']) ? sanitize_text_field(wp_unslash($_GET['error'])) : '';

        echo '<div id="corbidev-api-builder-admin" class="p-8">';
        echo '<h1 class="text-3xl font-bold mb-6">API Builder – Modèles/API</h1>';

        if ($created) {
            echo '<div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded mb-4">' . esc_html__('Nouvelle API créée avec succès.', 'wp-corbidev-api-new') . '</div>';
        }

        if ($error) {
            echo '<div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded mb-4">' . esc_html($error) . '</div>';
        }

        echo '<style>
            .btn-disabled{opacity:0.5;pointer-events:none;cursor:not-allowed;}
            .cv-api-title-actions{display:none;}
            .cv-api-title:hover .cv-api-title-actions{display:flex;}
        </style>';

        echo '<div class="mb-3 flex flex-wrap items-center justify-between gap-4">';
        echo '<div class="text-sm text-gray-700 flex flex-wrap items-center gap-2">';
        echo '<span class="text-blue-700 font-semibold">' . esc_html__('Tous', 'wp-corbidev-api-new') . ' <span class="text-gray-500">(' . count($models) . ')</span></span>';
        echo '</div>';
        echo '<form class="flex items-stretch gap-0 min-w-[240px]" role="search" method="get">';
        echo '<input type="hidden" name="page" value="corbidev-api-builder">';
        echo '<input type="search" name="s" class="border rounded-l px-3 py-1 text-sm min-w-0" placeholder="' . esc_attr__('Rechercher...', 'wp-corbidev-api-new') . '" value="' . esc_attr(sanitize_text_field($_GET['s'] ?? '')) . '">';
        echo '<button class="px-4 py-1 border border-l-0 rounded-r bg-blue-600 text-white hover:bg-blue-700 text-sm" type="submit">' . esc_html__('Rechercher', 'wp-corbidev-api-new') . '</button>';
        echo '</form>';
        echo '</div>';

        $add_url = esc_url(admin_url('admin.php?page=corbidev-api-builder-add'));
        echo '<div class="mb-3 flex justify-end gap-2">';
        echo '<a href="' . $add_url . '" class="flex items-center gap-1 px-3 py-1 border rounded bg-blue-600 text-white text-sm hover:bg-blue-700">';
        echo '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>';
        echo esc_html__('Ajouter', 'wp-corbidev-api-new');
        echo '</a>';
        echo '<button class="flex items-center gap-1 px-3 py-1 border rounded bg-white text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-800" disabled>';
        echo '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v10m0 0l3-3m-3 3l-3-3"/><path d="M5 19h14"/></svg>';
        echo esc_html__('Importer (bientôt)', 'wp-corbidev-api-new');
        echo '</button>';
        echo '</div>';

        echo '<div class="bg-white rounded shadow p-0 overflow-x-auto">
            <table class="min-w-full text-sm border border-gray-200">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-200">
                        <th class="px-4 py-2 text-left w-8"><input type="checkbox" /></th>
                        <th class="px-4 py-2 text-left">' . esc_html__('Titre', 'wp-corbidev-api-new') . '</th>
                        <th class="px-4 py-2 text-left">Slug</th>
                        <th class="px-4 py-2 text-left">' . esc_html__('Version', 'wp-corbidev-api-new') . '</th>
                        <th class="px-4 py-2 text-left">' . esc_html__('API active', 'wp-corbidev-api-new') . '</th>
                        <th class="px-4 py-2 text-left">' . esc_html__('Actions', 'wp-corbidev-api-new') . '</th>
                    </tr>
                </thead>
                <tbody>';

        if (empty($models)) {
            echo '<tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">' . esc_html__('Aucun modèle pour le moment.', 'wp-corbidev-api-new') . '</td></tr>';
        } else {
            foreach ($models as $model) {
                $version       = $model['version_info'] ?? null;
                $status        = $version['status'] ?? 'edition';
                $version_label = $version['version'] ?? '—';
                $status_meta   = $statuses_meta[$status] ?? $statuses_meta['edition'];

                echo '<tr class="border-b border-gray-100 hover:bg-gray-50">';
                echo '<td class="px-4 py-2"><input type="checkbox" /></td>';
                echo '<td class="px-4 py-2 align-top">';
                echo '<div class="cv-api-title">';
                echo '<div class="font-medium text-blue-700 cursor-pointer">' . esc_html($model['name']) . '</div>';
                echo '<div class="mt-1 text-xs text-gray-500 cv-api-title-actions gap-3">';
                echo '<button type="button" class="text-blue-600 hover:text-blue-800">' . esc_html__('Modification rapide', 'wp-corbidev-api-new') . '</button>';
                echo '<span class="text-gray-300">|</span>';
                echo '<button type="button" class="text-blue-600 hover:text-blue-800">' . esc_html__('Modifier', 'wp-corbidev-api-new') . '</button>';
                echo '</div>';
                echo '</div>';
                echo '</td>';
                echo '<td class="px-4 py-2">' . esc_html($model['slug']) . '</td>';
                echo '<td class="px-4 py-2">' . esc_html($version_label) . '</td>';
                echo '<td class="px-4 py-2">';
                echo '<div class="flex items-center gap-2">';
                echo '<span class="inline-flex h-5 w-5 rounded-full" style="background:' . esc_attr($status_meta['color']) . '"></span>';
                echo '<span class="text-xs font-semibold" style="color:' . esc_attr($status_meta['text']) . '">' . esc_html($status_meta['label']) . '</span>';
                echo '</div>';
                echo '</td>';
                echo '<td class="px-4 py-2 flex gap-2">';
                echo '<button class="text-blue-600 hover:text-white hover:bg-blue-600 hover:shadow-lg hover:scale-110 transition-all duration-150 rounded p-1 cursor-pointer" title="' . esc_attr__('Modifier', 'wp-corbidev-api-new') . '"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 3.487a2.25 2.25 0 113.182 3.182L7.75 18.963a2 2 0 01-.878.513l-4 1a.5.5 0 01-.606-.606l1-4a2 2 0 01.513-.878L16.862 3.487z" /></svg></button>';
                echo '<button class="text-green-600 hover:text-white hover:bg-green-600 hover:shadow-lg hover:scale-110 transition-all duration-150 rounded p-1 cursor-pointer" title="' . esc_attr__('Cloner', 'wp-corbidev-api-new') . '"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><rect x="9" y="9" width="10" height="10" rx="2" stroke-width="2"/><rect x="5" y="5" width="10" height="10" rx="2" stroke-width="2"/></svg></button>';
                echo '<button class="text-yellow-600 hover:text-white hover:bg-yellow-500 hover:shadow-lg hover:scale-110 transition-all duration-150 rounded p-1 cursor-pointer" title="' . esc_attr__('Exporter', 'wp-corbidev-api-new') . '"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16V4m0 0l-4 4m4-4l4 4" /><rect x="4" y="16" width="16" height="4" rx="1" stroke-width="2"/></svg></button>';
                $delete_disabled = !in_array($status, ['perime', 'edition'], true) ? ' btn-disabled" disabled' : '"';
                echo '<button class="text-red-600 hover:text-white hover:bg-red-600 hover:shadow-lg hover:scale-110 transition-all duration-150 rounded p-1' . $delete_disabled . ' title="' . esc_attr__('Supprimer', 'wp-corbidev-api-new') . '"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>';
                echo '</td>';
                echo '</tr>';
            }
        }

        echo '</tbody></table></div>';
        echo '</div>';
    }

    private static function statuses_meta(): array
    {
        return [
            'edition'  => ['label' => __('En édition', 'wp-corbidev-api-new'), 'color' => '#3B82F6', 'text' => '#1D4ED8'],
            'active'   => ['label' => __('Actif', 'wp-corbidev-api-new'), 'color' => '#22C55E', 'text' => '#166534'],
            'perime'   => ['label' => __('Périmé', 'wp-corbidev-api-new'), 'color' => '#EF4444', 'text' => '#B91C1C'],
            'obsolete' => ['label' => __('Obsolète', 'wp-corbidev-api-new'), 'color' => '#F97316', 'text' => '#C2410C'],
        ];
    }
}