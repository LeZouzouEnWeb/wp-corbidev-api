<?php
namespace Admin\Pages;

//use Storage\OptionStore;

class ContactTab
{
    public static function sanitize(array $data): array
    {
        return [
            'address' => sanitize_textarea_field($data['address'] ?? ''),
            'phone'   => sanitize_text_field($data['phone'] ?? ''),
            'email'   => sanitize_email($data['email'] ?? ''),
            'website' => esc_url_raw($data['website'] ?? ''),
        ];
    }

    public static function render(array $data): void
    {
        ?>
<table class="form-table">
    <tr>
        <th>Adresse</th>
        <td><textarea name="cv_options[contact][address]" rows="3"
                class="large-text"><?= esc_textarea($data['address'] ?? '') ?></textarea></td>
    </tr>
    <tr>
        <th>Téléphone</th>
        <td><input type="text" name="cv_options[contact][phone]" value="<?= esc_attr($data['phone'] ?? '') ?>"
                class="regular-text"></td>
    </tr>
    <tr>
        <th>Email</th>
        <td><input type="email" name="cv_options[contact][email]" value="<?= esc_attr($data['email'] ?? '') ?>"
                class="regular-text"></td>
    </tr>
    <tr>
        <th>Site web</th>
        <td><input type="url" name="cv_options[contact][website]" value="<?= esc_attr($data['website'] ?? '') ?>"
                class="regular-text"></td>
    </tr>
</table>
<?php
    }
}