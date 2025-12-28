<?php

namespace CorbiDev\ApiBuilder\Admin;

/**
 * Composant générique d'affichage de messages (alertes / infos).
 *
 * Fonctionne comme un bandeau sous le titre, avec possibilité de
 * fermeture en fondu. Inspiré de ConfirmationModal, mais pour les
 * messages non bloquants.
 */
class MessageModal
{
    public const TYPE_SUCCES  = 'succes';
    public const TYPE_INFO    = 'info';
    public const TYPE_AVERTISSEMENT = 'avertissement';
    public const TYPE_ERREUR  = 'erreur';

    /**
     * Rend un bandeau de message.
     *
     * @param string $id      Identifiant DOM unique.
     * @param string $message Message à afficher (texte simple).
     * @param string $type    Type de message (succes, info, avertissement, erreur).
     * @param array  $options Options : title, dismissible.
     */
    public static function render(string $id, string $message, string $type = self::TYPE_INFO, array $options = []): void
    {
        if ($message === '') {
            return;
        }

        $title       = $options['title'] ?? '';
        $dismissible = array_key_exists('dismissible', $options) ? (bool) $options['dismissible'] : true;

        $styles = self::getStylesForType($type);

        echo '<div id="' . esc_attr($id) . '"'
            . ' class="cv-message-banner max-w-4xl mx-auto mb-4 px-4 py-3 rounded flex items-start justify-between gap-3 ' . esc_attr($styles['bg']) . ' ' . esc_attr($styles['text']) . ' ' . esc_attr($styles['border']) . '"'
            . '>'; // style commun géré par CSS global

        echo '<div class="flex-1 text-sm">';
        if ($title !== '') {
            echo '<div class="font-semibold mb-0.5">' . esc_html($title) . '</div>';
        }
        echo '<div>' . esc_html($message) . '</div>';
        echo '</div>';

        if ($dismissible) {
            echo '<button type="button" class="ml-2 text-sm leading-none ' . esc_attr($styles['close']) . '"'
                . ' data-message-dismiss="' . esc_attr($id) . '"'
                . ' aria-label="' . esc_attr__('Fermer le message', 'wp-corbidev-api-new') . '">';
            echo '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>';
            echo '</button>';
        }

        echo '</div>';
    }

    /**
     * Styles de couleurs selon le type de message.
     */
    private static function getStylesForType(string $type): array
    {
        switch ($type) {
            case self::TYPE_SUCCES:
                return [
                    'bg'    => 'bg-emerald-50',
                    'text'  => 'text-emerald-800',
                    'border'=> 'border border-emerald-400',
                    'close' => 'text-emerald-500 hover:text-emerald-700',
                ];

            case self::TYPE_AVERTISSEMENT:
                return [
                    'bg'    => 'bg-amber-50',
                    'text'  => 'text-amber-800',
                    'border'=> 'border border-amber-400',
                    'close' => 'text-amber-500 hover:text-amber-700',
                ];

            case self::TYPE_ERREUR:
                return [
                    'bg'    => 'bg-red-100',
                    'text'  => 'text-red-800',
                    'border'=> 'border border-red-500',
                    'close' => 'text-red-500 hover:text-red-700',
                ];

            case self::TYPE_INFO:
            default:
                return [
                    'bg'    => 'bg-blue-50',
                    'text'  => 'text-blue-800',
                    'border'=> 'border border-blue-400',
                    'close' => 'text-blue-500 hover:text-blue-700',
                ];
        }
    }

    /**
     * Imprime les styles/JS globaux pour l'animation et la fermeture.
     * À appeler une seule fois.
     */
    public static function print_global_assets(): void
    {
        static $printed = false;
        if ($printed) {
            return;
        }
        $printed = true;

        // Styles simples pour la transition en fondu / léger slide.
        echo '<style>.cv-message-banner{opacity:1;transform:translateY(0);transition:opacity .3s ease-out,transform .3s ease-out;}'
            . '.cv-message-banner.cv-message-hide{opacity:0;transform:translateY(-4px);}</style>';

        // JS pour gérer la fermeture douce (y compris clic sur l'icône SVG interne).
        echo '<script>document.addEventListener("DOMContentLoaded",function(){'
            . 'document.body.addEventListener("click",function(e){var t=e.target;if(!t){return;}'
                . 'var btn=t.closest?t.closest("[data-message-dismiss]"):null;if(!btn){return;}'
                . 'var id=btn.getAttribute("data-message-dismiss");if(!id){return;}'
                . 'var b=document.getElementById(id);if(!b){return;}'
                . 'b.classList.add("cv-message-hide");setTimeout(function(){if(b&&b.parentNode){b.parentNode.removeChild(b);}},300);'
            . '});'
            . '});</script>';
    }
}
