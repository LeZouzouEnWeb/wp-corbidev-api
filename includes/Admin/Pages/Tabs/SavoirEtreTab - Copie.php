<?php
namespace Admin\Pages;

use Storage\OptionStore;

class SavoirEtreTab
{
    /**
     * Sanitize : transforme le texte en tableau, une ligne = un élément
     */
    public static function sanitize(array $data): array
    {
        if (!isset($data['savoir_etre']) || !is_string($data['savoir_etre'])) {
            return [];
        }

        $lines = preg_split("/\r\n|\n|\r/", $data['savoir_etre']);
        $lines = array_map('sanitize_text_field', $lines);
        return array_values(array_filter($lines));
    }

    /**
     * Render : convertir le tableau en textarea avec un savoir-être par ligne
     */
    public static function render(array $data): void
    {
        $value = implode("\n", $data);
        ?>
        <textarea name="cv_options[savoir_etre]" rows="10" style="width:100%;"><?= esc_textarea($value) ?></textarea>
        <p class="description">Un savoir-être par ligne.</p>
        <?php
    }
}