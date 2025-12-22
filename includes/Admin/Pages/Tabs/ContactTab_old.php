<?php

namespace Admin\Pages;

//use Storage\OptionStore;

class ContactTab
{
    public static function sanitize(array $data): array
    {
        // Sanitize websites array
        $websites = [];
        if (isset($data['websites']) && is_array($data['websites'])) {
            foreach ($data['websites'] as $website) {
                $url = esc_url_raw($website);
                if (!empty($url)) {
                    $websites[] = $url;
                }
            }
        }

        return [
            'address'  => sanitize_textarea_field($data['address'] ?? ''),
            'phone'    => sanitize_text_field($data['phone'] ?? ''),
            'email'    => sanitize_email($data['email'] ?? ''),
            'websites' => $websites,
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
                <th>Sites web</th>
                <td>
                    <div id="websites-container">
                        <?php
                        $websites = $data['websites'] ?? [''];
                        if (empty($websites)) {
                            $websites = [''];
                        }
                        foreach ($websites as $index => $website):
                        ?>
                            <div class="website-row" style="margin-bottom:10px;">
                                <input type="url" name="cv_options[contact][websites][]" value="<?= esc_attr($website) ?>"
                                    placeholder="https://example.com" class="regular-text" style="width:70%;">
                                <button type="button" class="button remove-website" style="margin-left:5px;">
                                    Supprimer
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" id="add-website" class="button button-secondary" style="margin-top:10px;">
                        + Ajouter un site web
                    </button>
                </td>
            </tr>
        </table>

        <script>
            JQuery(document).ready(function($) {
                // Add website
                $('#add-website').on('click', function() {
                    const container = $('#websites-container');
                    const newRow = `
            <div class="website-row" style="margin-bottom:10px;">
                <input type="url"
                       name="cv_options[contact][websites][]"
                       value=""
                       placeholder="https://example.com"
                       class="regular-text"
                       style="width:70%;">
                <button type="button" class="button remove-website" style="margin-left:5px;">
                    Supprimer
                </button>
            </div>
        `;
                    container.append(newRow);
                });

                // Remove website
                $(document).on('click', '.remove-website', function() {
                    const rows = $('.website-row');
                    if (rows.length > 1) {
                        $(this).closest('.website-row').remove();
                    } else {
                        // Keep at least one row, just clear it
                        $(this).siblings('input').val('');
                    }
                });
            });
        </script>
<?php
    }
}
