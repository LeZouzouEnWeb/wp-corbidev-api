<?php
namespace Admin\Pages;

use Storage\OptionStore;

class IdentityTab
{
    public static function sanitize(array $data): array
    {
        return [
            'job_title'  => sanitize_text_field($data['job_title'] ?? ''),
            'first_name' => sanitize_text_field($data['first_name'] ?? ''),
            'last_name'  => sanitize_text_field($data['last_name'] ?? ''),
            'summary'    => sanitize_textarea_field($data['summary'] ?? ''),
        ];
    }

    public static function render(array $data): void
    {
        ?>
        <table class="form-table">
            <tr>
                <th>Poste</th>
                <td><input type="text" name="cv_options[identity][job_title]" value="<?= esc_attr($data['job_title'] ?? '') ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th>Prénom</th>
                <td><input type="text" name="cv_options[identity][first_name]" value="<?= esc_attr($data['first_name'] ?? '') ?>"></td>
            </tr>
            <tr>
                <th>Nom</th>
                <td><input type="text" name="cv_options[identity][last_name]" value="<?= esc_attr($data['last_name'] ?? '') ?>"></td>
            </tr>
            <tr>
                <th>Résumé</th>
                <td><textarea name="cv_options[identity][summary]" rows="5" class="large-text"><?= esc_textarea($data['summary'] ?? '') ?></textarea></td>
            </tr>
        </table>
        <?php
    }
}
