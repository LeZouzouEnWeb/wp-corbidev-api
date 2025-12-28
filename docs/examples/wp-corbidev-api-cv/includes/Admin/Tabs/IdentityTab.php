<?php

namespace Admin\Page;

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
            'job_title'  => sanitize_text_field(wp_unslash($data['job_title'] ?? '')),
            'first_name' => sanitize_text_field(wp_unslash($data['first_name'] ?? '')),
            'last_name'  => sanitize_text_field(wp_unslash($data['last_name'] ?? '')),
            'birth_date' => sanitize_text_field(wp_unslash($data['birth_date'] ?? '')),
            'has_driving_license' => isset($data['has_driving_license']) ? 1 : 0,
            'driving_licenses' => $driving_licenses,
            'summary'    => sanitize_textarea_field(wp_unslash($data['summary'] ?? '')),

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
<div class="space-y-6">
    <!-- Informations personnelles -->
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Informations personnelles
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Poste</label>
                <input type="text" name="contenus[identity][job_title]"
                    value="<?= esc_attr($data['job_title'] ?? '') ?>" placeholder="Ex: Développeur Full Stack"
                    pattern="[a-zA-ZÀ-ÿ\s\-_']+"
                    title="Seuls les lettres, espaces, tirets, underscores et apostrophes sont autorisés"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 cv-text-field">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date de naissance</label>
                <input type="date" name="contenus[identity][birth_date]"
                    value="<?= esc_attr($data['birth_date'] ?? '') ?>"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Prénom</label>
                <input type="text" name="contenus[identity][first_name]"
                    value="<?= esc_attr($data['first_name'] ?? '') ?>" placeholder="Votre prénom"
                    pattern="[a-zA-ZÀ-ÿ\s\-_']+"
                    title="Seuls les lettres, espaces, tirets, underscores et apostrophes sont autorisés"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 cv-text-field">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                <input type="text" name="contenus[identity][last_name]"
                    value="<?= esc_attr($data['last_name'] ?? '') ?>" placeholder="Votre nom"
                    pattern="[a-zA-ZÀ-ÿ\s\-_']+"
                    title="Seuls les lettres, espaces, tirets, underscores et apostrophes sont autorisés"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 cv-text-field">
            </div>
        </div>
    </div>

    <!-- Permis de conduire -->
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            </svg>
            Permis de conduire
        </h3>

        <div class="mb-4">
            <label
                class="flex items-center gap-2 cursor-pointer p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <input type="checkbox" name="contenus[identity][has_driving_license]" value="1" id="has_driving_license"
                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                    <?= !empty($data['has_driving_license']) ? 'checked' : '' ?>>
                <span class="text-sm font-medium text-gray-700">J'ai le permis de conduire</span>
            </label>
        </div>

        <div id="driving_licenses_container"
            class="<?= empty($data['has_driving_license']) ? 'hidden' : '' ?> p-4 bg-blue-50 rounded-lg border border-blue-200">
            <label class="block text-sm font-semibold text-gray-700 mb-3">Types de permis :</label>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                <?php
                        $licenses = ['AM', 'A1', 'A2', 'A', 'B', 'BE', 'C', 'CE', 'D', 'DE'];
                        $selected = $data['driving_licenses'] ?? [];
                        foreach ($licenses as $license):
                        ?>
                <label
                    class="flex items-center gap-2 p-2 bg-white rounded border border-gray-200 cursor-pointer hover:bg-indigo-50 hover:border-indigo-300 transition-colors">
                    <input type="checkbox" name="contenus[identity][driving_licenses][]"
                        value="<?= esc_attr($license) ?>"
                        class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                        <?= in_array($license, $selected) ? 'checked' : '' ?>>
                    <span class="text-sm font-medium text-gray-700"><?= esc_html($license) ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Résumé -->
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
            </svg>
            Résumé / Présentation
        </h3>
        <textarea name="contenus[identity][summary]" rows="5"
            placeholder="Présentez-vous brièvement : votre parcours, vos compétences clés, vos objectifs professionnels..."
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"><?= esc_textarea($data['summary'] ?? '') ?></textarea>
        <p class="mt-2 text-xs text-gray-500">Ce texte apparaîtra en introduction de votre CV</p>
    </div>

    <!-- Médias -->
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Images et logos
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Photo d'identité</label>
                <?php self::mediaField('photo_id', $photo_id); ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Logo</label>
                <?php self::mediaField('logo_id', $logo_id); ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Icône</label>
                <?php self::mediaField('icon_id', $icon_id); ?>
            </div>
        </div>
    </div>
</div>
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
    <input type="hidden" name="contenus[identity][<?= esc_attr($key) ?>]" value="<?= esc_attr($attachment_id) ?>">

    <div class="flex flex-col gap-3">
        <div class="cv-media-preview <?= $image_url ? 'p-2 bg-gray-50 rounded-lg border border-gray-200' : '' ?>">
            <?php if ($image_url): ?>
            <img src="<?= esc_url($image_url) ?>" class="w-24 h-24 object-cover rounded-lg shadow-sm">
            <?php else: ?>
            <div
                class="w-24 h-24 bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <?php endif; ?>
        </div>

        <div class="flex gap-2">
            <button type="button"
                class="cv-media-select inline-flex items-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Choisir
            </button>

            <button type="button"
                class="cv-media-remove inline-flex items-center px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition-colors <?= $attachment_id ? '' : 'hidden' ?>">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Supprimer
            </button>
        </div>
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
            $('#driving_licenses_container').removeClass('hidden').hide().slideDown();
        } else {
            $('#driving_licenses_container').slideUp(function() {
                $(this).addClass('hidden');
            });
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
                '" class="w-24 h-24 object-cover rounded-lg shadow-sm">');
            preview.addClass('p-2 bg-gray-50 rounded-lg border border-gray-200');
            removeBtn.removeClass('hidden');
        });

        frame.open();
    });

    $('.cv-media-remove').on('click', function() {
        const container = $(this).closest('.cv-media-field');
        const preview = container.find('.cv-media-preview');
        container.find('input[type="hidden"]').val('');
        preview.html(`
                        <div class="w-24 h-24 bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    `);
        preview.removeClass('p-2 bg-gray-50 rounded-lg border border-gray-200');
        $(this).addClass('hidden');
    });

    // Validation en temps réel pour les champs texte (titre, nom, prénom)
    $('.cv-text-field').on('input', function() {
        let value = $(this).val();
        // Autoriser uniquement lettres, espaces, tirets, underscores, apostrophes
        let filtered = value.replace(/[^a-zA-ZÀ-ÿ\s\-_']/g, '');

        if (value !== filtered) {
            $(this).val(filtered);
        }

        // Validation visuelle
        const pattern = /^[a-zA-ZÀ-ÿ\s\-_']+$/;
        if (filtered === '' || pattern.test(filtered)) {
            $(this).removeClass('border-red-500').addClass('border-gray-300');
        } else if (filtered.length > 0) {
            $(this).removeClass('border-gray-300').addClass('border-red-500');
        }
    });

});
</script>
<?php
    }
}