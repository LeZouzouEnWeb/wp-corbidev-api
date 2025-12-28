<?php

namespace CorbiDev\ApiBuilder\Admin;

/**
 * Composant générique de modale de confirmation.
 *
 * Permet de réutiliser une même structure HTML/CSS/JS pour les confirmations
 * avec plusieurs types visuels : valider, information, alerte.
 */
class ConfirmationModal
{
    public const MODE_VALIDER     = 'valider';
    public const MODE_INFORMATION = 'information';
    public const MODE_ALERTE      = 'alerte';

    /**
     * Rend le HTML complet d'une modale de confirmation.
     *
     * @param string $id        Identifiant DOM unique de la modale.
     * @param string $titre     Titre affiché dans la modale.
     * @param string $message   Message principal (zone d'avertissement / info). Peut contenir du HTML sûr.
     * @param string $mode      L'un des MODE_* (valider, information, alerte).
     * @param array  $options   Options : description, confirm_label, cancel_label.
     */
    public static function render(string $id, string $titre, string $message, string $mode = self::MODE_INFORMATION, array $options = []): void
    {
        $description   = $options['description'] ?? '';
        $confirm_label = $options['confirm_label'] ?? __('OK', 'wp-corbidev-api-new');
        $cancel_label  = $options['cancel_label'] ?? __('Annuler', 'wp-corbidev-api-new');

        $styles = self::getStylesForMode($mode);

        echo '<div id="' . esc_attr($id) . '" class="fixed inset-0 z-50 hidden items-start justify-center bg-black/40 pt-24">';
        echo '<div class="bg-white rounded-xl shadow-2xl max-w-lg w-full mx-4 p-6 border-t-4 ' . esc_attr($styles['border']) . '">';
        echo '<div class="flex items-start gap-3 mb-3">';

        // Icône
        echo '<div class="flex-shrink-0 flex items-center justify-center pt-1">';
        echo self::renderIcon($mode);
        echo '</div>';

        // Titre + description
        echo '<div class="flex-1 flex flex-col items-start">';
        echo '<h2 class="text-lg font-semibold ' . esc_attr($styles['title']) . '">' . esc_html($titre) . '</h2>';

        echo '</div>';
        echo '</div>';
        if ($description !== '') {
            echo '<div class="text-sm text-gray-700 mt-1 mb-4">' . wp_kses_post($description) . '</div>';
        }

        // Bloc message
        echo '<div class="text-xs ' . esc_attr($styles['alert_text']) . ' ' . esc_attr($styles['alert_bg']) . ' border rounded px-3 py-2 mb-4" id="' . esc_attr($id) . '-message">' . wp_kses_post($message) . '</div>';

        // Boutons
        echo '<div class="flex justify-end gap-2 mt-1">';
        echo '<button type="button" class="px-3 py-1.5 text-sm rounded border border-gray-300 text-gray-700 hover:bg-gray-50" data-modal-cancel="' . esc_attr($id) . '">' . esc_html($cancel_label) . '</button>';
        echo '<button type="button" class="px-3 py-1.5 text-sm rounded ' . esc_attr($styles['confirm_bg']) . ' text-white hover:opacity-90" data-modal-confirm="' . esc_attr($id) . '">' . esc_html($confirm_label) . '</button>';
        echo '</div>';

        echo '</div>';
        echo '</div>';
    }

    /**
     * Rend l'icône SVG adaptée au mode.
     */
    private static function renderIcon(string $mode): string
    {
        switch ($mode) {
            case self::MODE_VALIDER:
                // Icône de validation (check)
                return '<span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-emerald-500 shadow-lg border-2 border-emerald-600">'
                    . '<svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>'
                    . '</span>';

            case self::MODE_ALERTE:
                // Icône d'alerte (point d'exclamation)
                return '<span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-amber-400 shadow-lg border-2 border-amber-500">'
                    . '<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="11" fill="#f59e42"/><path d="M12 7v5" stroke="#fff" stroke-width="2.2" stroke-linecap="round"/><circle cx="12" cy="16" r="1.3" fill="#fff"/></svg>'
                    . '</span>';

            case self::MODE_INFORMATION:
            default:
                // Icône d'information (i)
                return '<span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-blue-500 shadow-lg border-2 border-blue-600">'
                    . '<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="11" fill="#3b82f6"/><line x1="12" y1="10" x2="12" y2="16" stroke="#fff" stroke-width="2.2" stroke-linecap="round"/><circle cx="12" cy="7" r="1.3" fill="#fff"/></svg>'
                    . '</span>';
        }
    }

    /**
     * Styles Tailwind-like selon le mode.
     */
    private static function getStylesForMode(string $mode): array
    {
        switch ($mode) {
            case self::MODE_VALIDER:
                return [
                    'border'      => 'border-emerald-500',
                    'title'       => 'text-emerald-700',
                    'alert_bg'    => 'bg-emerald-50 border-emerald-200',
                    'alert_text'  => 'text-emerald-900',
                    'confirm_bg'  => 'bg-emerald-600',
                ];

            case self::MODE_ALERTE:
                return [
                    'border'      => 'border-amber-400',
                    'title'       => 'text-gray-900',
                    'alert_bg'    => 'bg-amber-50 border-amber-200',
                    'alert_text'  => 'text-amber-900',
                    'confirm_bg'  => 'bg-amber-500',
                ];

            case self::MODE_INFORMATION:
            default:
                return [
                    'border'      => 'border-blue-500',
                    'title'       => 'text-gray-900',
                    'alert_bg'    => 'bg-blue-50 border-blue-200',
                    'alert_text'  => 'text-blue-900',
                    'confirm_bg'  => 'bg-blue-600',
                ];
        }
    }

    /**
     * JS générique pour ouvrir/fermer les modales.
     *
     * À appeler une seule fois (par exemple depuis l'admin_head du plugin).
     * Permet d'ouvrir une modale en ajoutant l'attribut data-modal-target="ID" sur un bouton.
     */
    public static function print_global_script(): void
    {
        static $printed = false;
        if ($printed) {
            return;
        }
        $printed = true;

        echo '<script>document.addEventListener("DOMContentLoaded",function(){'
            . 'document.body.addEventListener("click",function(e){var t=e.target;'
                . 'var targetId=t.getAttribute&&t.getAttribute("data-modal-target");'
                . 'if(targetId){e.preventDefault();var m=document.getElementById(targetId);if(m){m.classList.remove("hidden");m.classList.add("flex");return;}}'
                . 'var cancelId=t.getAttribute&&t.getAttribute("data-modal-cancel");'
                . 'if(cancelId){e.preventDefault();var m2=document.getElementById(cancelId);if(m2){m2.classList.add("hidden");m2.classList.remove("flex");return;}}'
            . '});'
        . '});</script>';
    }
}