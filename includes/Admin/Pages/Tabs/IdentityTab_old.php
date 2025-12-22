<?php

namespace Admin\Pages;

class IdentityTab
{
    public static function sanitize(array $data): array
    {
        // Sanitize driving licenses
        $driving_licenses = [];
        if (isset($data['driving_licenses']) && is_array($data['driving_licenses'])) {
            $valid_licenses = ['AM', 'A1', 'A2', 'A', 'B', 'BE', 'C', 'CE', 'D', 'DE'];
            $driving_licenses = array_intersect($data['driving_licenses'], $valid_licenses);
        }

        return [
            'job_title'  => sanitize_text_field($data['job_title'] ?? ''),
            'first_name' => sanitize_text_field($data['first_name'] ?? ''),
            'last_name'  => sanitize_text_field($data['last_name'] ?? ''),
            'birth_date' => sanitize_text_field($data['birth_date'] ?? ''),
            'has_driving_license' => isset($data['has_driving_license']) ? 1 : 0,
            'driving_licenses' => $driving_licenses,
            'summary'    => sanitize_textarea_field($data['summary'] ?? ''),

            // Médias (on stocke les ID)
            'photo_id' => isset($data['photo_id']) ? absint($data['photo_id']) : 0,
            'logo_id'  => isset($data['logo_id'])  ? absint($data['logo_id'])  : 0,
            'icon_id'  => isset($data['icon_id'])  ? absint($data['icon_id'])  : 0,
        ];
    }

    public static function render(array $data): void
    {
        $photo_id = $data['photo_id'] ?? 0;
        $logo_id  = $data['logo_id'] ?? 0;
        $icon_id  = $data['icon_id'] ?? 0;

?>
        <table class="form-table">

            <tr>
                <th>Poste</th>
                <td>
                    <input type="text" name="cv_options[identity][job_title]" value="<?= esc_attr($data['job_title'] ?? '') ?>"
                        class="regular-text">
                </td>
            </tr>

            <tr>
                <th>Prénom</th>
                <td>
                    <input type="text" name="cv_options[identity][first_name]"
                        value="<?= esc_attr($data['first_name'] ?? '') ?>">
                </td>
            </tr>

            <tr>
                <th>Nom</th>
                <td>
                    <input type="text" name="cv_options[identity][last_name]" value="<?= esc_attr($data['last_name'] ?? '') ?>">
                </td>
            </tr>

            <tr>
                <th>Date de naissance</th>
                <td>
                    <input type="date" name="cv_options[identity][birth_date]"
                        value="<?= esc_attr($data['birth_date'] ?? '') ?>">
                </td>
            </tr>

            <tr>
                <th>Permis de conduire</th>
                <td>
                    <label>
                        <input type="checkbox" name="cv_options[identity][has_driving_license]" value="1"
                            id="has_driving_license" <?= !empty($data['has_driving_license']) ? 'checked' : '' ?>>
                        J'ai le permis de conduire
                    </label>

                    <div id="driving_licenses_container"
                        style="margin-top:10px;<?= empty($data['has_driving_license']) ? 'display:none;' : '' ?>">
                        <label style="display:block;margin-bottom:5px;font-weight:600;">Types de permis :</label>
                        <?php
                        $licenses = ['AM', 'A1', 'A2', 'A', 'B', 'BE', 'C', 'CE', 'D', 'DE'];
                        $selected = $data['driving_licenses'] ?? [];
                        foreach ($licenses as $license):
                        ?>
                            <label style="display:inline-block;margin-right:15px;">
                                <input type="checkbox" name="cv_options[identity][driving_licenses][]"
                                    value="<?= esc_attr($license) ?>" <?= in_array($license, $selected) ? 'checked' : '' ?>>
                                <?= esc_html($license) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </td>
            </tr>

            <tr>
                <th>Résumé</th>
                <td>
                    <textarea name="cv_options[identity][summary]" rows="5"
                        class="large-text"><?= esc_textarea($data['summary'] ?? '') ?></textarea>
                </td>
            </tr>

            <!-- PHOTO -->
            <tr>
                <th>Photo d’identité</th>
                <td>
                    <?php self::mediaField('photo_id', $photo_id); ?>
                </td>
            </tr>

            <!-- LOGO -->
            <tr>
                <th>Logo</th>
                <td>
                    <?php self::mediaField('logo_id', $logo_id); ?>
                </td>
            </tr>

            <!-- ICÔNE -->
            <tr>
                <th>Icône</th>
                <td>
                    <?php self::mediaField('icon_id', $icon_id); ?>
                </td>
            </tr>

        </table>
    <?php

        self::enqueueMediaScript();
    }

    /**
     * Champ média réutilisable
     */
    private static function mediaField(string $key, int $attachment_id): void
    {
        $image_url = $attachment_id ? wp_get_attachment_image_url($attachment_id, 'thumbnail') : '';
    ?>
        <div class="cv-media-field">
            <input type="hidden" name="cv_options[identity][<?= esc_attr($key) ?>]" value="<?= esc_attr($attachment_id) ?>">

            <button type="button" class="button cv-media-select">
                Choisir une image
            </button>

            <button type="button" class="button cv-media-remove" style="<?= $attachment_id ? '' : 'display:none;' ?>">
                Supprimer
            </button>

            <div class="cv-media-preview" style="margin-top:10px;">
                <?php if ($image_url): ?>
                    <img src="<?= esc_url($image_url) ?>" style="max-width:100px;height:auto;">
                <?php endif; ?>
            </div>
        </div>
    <?php
    }

    /**
     * JS pour le media uploader
     */
    private static function enqueueMediaScript(): void
    {
        wp_enqueue_media();
    ?>
        <script>
            jQuery(document).ready(function($) {

                // Toggle driving licenses list
                $('#has_driving_license').on('change', function() {
                    if ($(this).is(':checked')) {
                        $('#driving_licenses_container').slideDown();
                    } else {
                        $('#driving_licenses_container').slideUp();
                    }
                });

                $('.cv-media-select').on('click', function(e) {
                    e.preventDefault();

                    const container = $(this).closest('.cv-media-field');
                    const input = container.find('input[type="hidden"]');
                    const preview = container.find('.cv-media-preview');
                    const removeBtn = container.find('.cv-media-remove');

                    const frame = wp.media({
                        title: 'Sélectionner une image',
                        button: {
                            text: 'Utiliser cette image'
                        },
                        multiple: false
                    });

                    frame.on('select', function() {
                        const attachment = frame.state().get('selection').first().toJSON();
                        input.val(attachment.id);
                        preview.html('<img src="' + attachment.sizes.thumbnail.url +
                            '" style="max-width:100px;">');
                        removeBtn.show();
                    });

                    frame.open();
                });

                $('.cv-media-remove').on('click', function() {
                    const container = $(this).closest('.cv-media-field');
                    container.find('input[type="hidden"]').val('');
                    container.find('.cv-media-preview').empty();
                    $(this).hide();
                });

            });
        </script>
<?php
    }
}
