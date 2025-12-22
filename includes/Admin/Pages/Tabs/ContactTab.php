<?php

namespace Admin\Pages;

class ContactTab
{
    public static function sanitize(array $data): array
    {
        // Sanitize websites array
        $websites = [];
        if (isset($data['websites']) && is_array($data['websites'])) {
            foreach ($data['websites'] as $website) {
                $url = esc_url_raw(wp_unslash($website));
                if (!empty($url)) {
                    $websites[] = $url;
                }
            }
        }

        return [
            'street'      => sanitize_text_field(wp_unslash($data['street'] ?? '')),
            'complement'  => sanitize_text_field(wp_unslash($data['complement'] ?? '')),
            'postal_code' => sanitize_text_field(wp_unslash($data['postal_code'] ?? '')),
            'city'        => sanitize_text_field(wp_unslash($data['city'] ?? '')),
            'phone'       => sanitize_text_field(wp_unslash($data['phone'] ?? '')),
            'phone2'      => sanitize_text_field(wp_unslash($data['phone2'] ?? '')),
            'email'       => sanitize_email(wp_unslash($data['email'] ?? '')),
            'websites'    => $websites,
        ];
    }

    public static function render(array $data): void
    {
?>
        <div class="space-y-6">
            <!-- Coordonnées -->
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Coordonnées
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Rue
                        </label>
                        <input type="text" name="cv_options[contact][street]" value="<?= esc_attr($data['street'] ?? '') ?>"
                            placeholder="Ex: 123 Rue de la République"
                            pattern="[a-zA-ZÀ-ÿ0-9\s\-_']+"
                            title="Seuls les lettres, chiffres, espaces, tirets, underscores et apostrophes sont autorisés"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 cv-text-field-street">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Adresse complémentaire</label>
                        <input type="text" name="cv_options[contact][complement]"
                            value="<?= esc_attr($data['complement'] ?? '') ?>"
                            placeholder="Appartement, étage, bâtiment... (optionnel)"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Code postal</label>
                            <input type="text" name="cv_options[contact][postal_code]"
                                value="<?= esc_attr($data['postal_code'] ?? '') ?>" placeholder="75001" maxlength="5"
                                pattern="[0-9]{5}" title="Le code postal doit contenir exactement 5 chiffres"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ville</label>
                            <input type="text" name="cv_options[contact][city]" value="<?= esc_attr($data['city'] ?? '') ?>"
                                placeholder="Paris"
                                pattern="[a-zA-ZÀ-ÿ\s\-_']+"
                                title="Seuls les lettres, espaces, tirets, underscores et apostrophes sont autorisés"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 cv-text-field">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            Téléphone
                        </label>
                        <input type="tel" name="cv_options[contact][phone]" value="<?= esc_attr($data['phone'] ?? '') ?>"
                            placeholder="+33 6 12 34 56 78" pattern="^(\+33|0)[1-9]([-. ]?[0-9]{2}){4}$"
                            title="Format attendu : +33 6 12 34 56 78 ou 06 12 34 56 78"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            Téléphone 2 <span class="text-gray-500 text-xs">(optionnel)</span>
                        </label>
                        <input type="tel" name="cv_options[contact][phone2]" value="<?= esc_attr($data['phone2'] ?? '') ?>"
                            placeholder="+33 1 23 45 67 89" pattern="^(\+33|0)[1-9]([-. ]?[0-9]{2}){4}$"
                            title="Format attendu : +33 1 23 45 67 89 ou 01 23 45 67 89"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                            Email
                        </label>
                        <input type="email" name="cv_options[contact][email]" value="<?= esc_attr($data['email'] ?? '') ?>"
                            placeholder="votre.email@exemple.fr" title="Veuillez saisir une adresse email valide"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Sites web -->
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                    </svg>
                    Sites web et réseaux sociaux
                </h3>

                <div id="websites-container" class="space-y-3 mb-4">
                    <?php
                    $websites = $data['websites'] ?? [''];
                    if (empty($websites)) {
                        $websites = [''];
                    }
                    foreach ($websites as $index => $website):
                    ?>
                        <div class="website-row flex gap-2">
                            <input type="url" name="cv_options[contact][websites][]" value="<?= esc_attr($website) ?>"
                                placeholder="https://www.exemple.com ou https://linkedin.com/in/votre-profil" pattern="https?://.+"
                                title="L'URL doit commencer par http:// ou https://"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <button type="button"
                                class="remove-website px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button type="button" id="add-website"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Ajouter un site web
                </button>
                <p class="mt-3 text-xs text-gray-500">Portfolio, LinkedIn, GitHub, réseaux sociaux professionnels...</p>
            </div>
        </div>

        <script>
            jQuery(document).ready(function($) {
                // Add website
                $('#add-website').on('click', function() {
                    const container = $('#websites-container');
                    const newRow = `
                        <div class="website-row flex gap-2">
                            <input type="url"
                                   name="cv_options[contact][websites][]"
                                   value=""
                                   placeholder="https://www.exemple.com ou https://linkedin.com/in/votre-profil"
                                   pattern="https?://.+"
                                   title="L'URL doit commencer par http:// ou https://"
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <button type="button" class="remove-website px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    `;
                    container.append(newRow);
                });

                // Remove website
                $(document).on('click', '.remove-website', function() {
                    const rows = $('.website-row');
                    if (rows.length > 1) {
                        const row = $(this).closest('.website-row');
                        row.css('opacity', '0');
                        row.css('transform', 'translateX(20px)');
                        row.css('transition', 'all 0.3s ease');
                        setTimeout(() => row.remove(), 300);
                    } else {
                        // Keep at least one row, just clear it
                        $(this).siblings('input').val('');
                    }
                });

                // Validation en temps réel pour les champs téléphone
                $('input[type="tel"]').on('input', function() {
                    let value = $(this).val();
                    // Autoriser uniquement les chiffres, +, espaces, points, tirets
                    let filtered = value.replace(/[^0-9+\s.\-]/g, '');

                    if (value !== filtered) {
                        $(this).val(filtered);
                    }

                    // Validation visuelle
                    const pattern = /^(\+33|0)[1-9]([-. ]?[0-9]{2}){4}$/;
                    if (filtered === '' || pattern.test(filtered)) {
                        $(this).removeClass('border-red-500').addClass('border-gray-300');
                    } else if (filtered.length > 0) {
                        $(this).removeClass('border-gray-300').addClass('border-red-500');
                    }
                });

                // Validation en temps réel pour le code postal
                $('input[name="cv_options[contact][postal_code]"]').on('input', function() {
                    let value = $(this).val();
                    // Autoriser uniquement les chiffres
                    let filtered = value.replace(/[^0-9]/g, '').substring(0, 5);

                    if (value !== filtered) {
                        $(this).val(filtered);
                    }

                    // Validation visuelle
                    if (filtered === '' || filtered.length === 5) {
                        $(this).removeClass('border-red-500').addClass('border-gray-300');
                    } else if (filtered.length > 0) {
                        $(this).removeClass('border-gray-300').addClass('border-red-500');
                    }
                });

                // Validation en temps réel pour les champs texte (ville)
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

                // Validation en temps réel pour la rue (avec nombres)
                $('.cv-text-field-street').on('input', function() {
                    let value = $(this).val();
                    // Autoriser lettres, chiffres, espaces, tirets, underscores, apostrophes
                    let filtered = value.replace(/[^a-zA-ZÀ-ÿ0-9\s\-_']/g, '');

                    if (value !== filtered) {
                        $(this).val(filtered);
                    }

                    // Validation visuelle
                    const pattern = /^[a-zA-ZÀ-ÿ0-9\s\-_']+$/;
                    if (filtered === '' || pattern.test(filtered)) {
                        $(this).removeClass('border-red-500').addClass('border-gray-300');
                    } else if (filtered.length > 0) {
                        $(this).removeClass('border-gray-300').addClass('border-red-500');
                    }
                });

                // Validation en temps réel pour les URLs
                $(document).on('input', 'input[type="url"]', function() {
                    const value = $(this).val();
                    const pattern = /^https?:\/\/.+/;

                    if (value === '' || pattern.test(value)) {
                        $(this).removeClass('border-red-500').addClass('border-gray-300');
                    } else if (value.length > 0) {
                        $(this).removeClass('border-gray-300').addClass('border-red-500');
                    }
                });

                // Bloquer la soumission si des champs sont invalides
                $('form').on('submit', function(e) {
                    let hasErrors = false;
                    let errorMessages = [];

                    // Vérifier les téléphones
                    $('input[type="tel"]').each(function() {
                        const value = $(this).val().trim();
                        const pattern = /^(\+33|0)[1-9]([-. ]?[0-9]{2}){4}$/;

                        if (value !== '' && !pattern.test(value)) {
                            hasErrors = true;
                            const label = $(this).closest('div').find('label').text().trim();
                            errorMessages.push(label + ' : format invalide');
                            $(this).addClass('border-red-500');
                        }
                    });

                    // Vérifier le code postal
                    const postalCode = $('input[name="cv_options[contact][postal_code]"]').val().trim();
                    if (postalCode !== '' && postalCode.length !== 5) {
                        hasErrors = true;
                        errorMessages.push('Code postal : doit contenir 5 chiffres');
                        $('input[name="cv_options[contact][postal_code]"]').addClass('border-red-500');
                    }

                    // Vérifier les champs texte (nom, prénom, titre, ville)
                    $('.cv-text-field').each(function() {
                        const value = $(this).val().trim();
                        const pattern = /^[a-zA-ZÀ-ÿ\s\-_']+$/;

                        if (value !== '' && !pattern.test(value)) {
                            hasErrors = true;
                            const label = $(this).closest('div').find('label').text().trim();
                            errorMessages.push(label + ' : caractères invalides (seuls lettres, espaces, tirets, underscores et apostrophes sont autorisés)');
                            $(this).addClass('border-red-500');
                        }
                    });

                    // Vérifier le champ rue (avec nombres)
                    $('.cv-text-field-street').each(function() {
                        const value = $(this).val().trim();
                        const pattern = /^[a-zA-ZÀ-ÿ0-9\s\-_']+$/;

                        if (value !== '' && !pattern.test(value)) {
                            hasErrors = true;
                            const label = $(this).closest('div').find('label').text().trim();
                            errorMessages.push(label + ' : caractères invalides (seuls lettres, chiffres, espaces, tirets, underscores et apostrophes sont autorisés)');
                            $(this).addClass('border-red-500');
                        }
                    });

                    // Vérifier l'email
                    const email = $('input[type="email"]').val().trim();
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (email !== '' && !emailPattern.test(email)) {
                        hasErrors = true;
                        errorMessages.push('Email : adresse email invalide');
                        $('input[type="email"]').addClass('border-red-500');
                    }

                    // Vérifier les URLs
                    $('input[type="url"]').each(function() {
                        const value = $(this).val().trim();
                        const pattern = /^https?:\/\/.+/;

                        if (value !== '' && !pattern.test(value)) {
                            hasErrors = true;
                            errorMessages.push('Site web : doit commencer par http:// ou https://');
                            $(this).addClass('border-red-500');
                        }
                    });

                    // Bloquer si erreurs
                    if (hasErrors) {
                        e.preventDefault();

                        // Afficher les erreurs dans la zone dédiée au-dessus des onglets
                        let errorHtml = `
                            <div class="bg-red-50 border border-red-400 text-red-800 px-4 py-3 rounded-lg mb-6 shadow-sm">
                                <div class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-red-900 mb-2">⚠️ Veuillez corriger les erreurs suivantes :</h3>
                                        <ul class="list-disc list-inside space-y-1 text-sm">
                                            ${errorMessages.map(msg => `<li>${msg}</li>`).join('')}
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        `;

                        // Insérer dans la zone dédiée
                        $('#cv-validation-errors').html(errorHtml);

                        // Scroller vers les erreurs
                        $('html, body').animate({
                            scrollTop: $('#cv-validation-errors').offset().top - 100
                        }, 300);
                    } else {
                        // Supprimer les notifications d'erreur si tout est OK
                        $('#cv-validation-errors').empty();
                    }
                });

                // Validation en temps réel pour l'email
                $('input[type="email"]').on('input', function() {
                    const value = $(this).val().trim();
                    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                    if (value === '' || pattern.test(value)) {
                        $(this).removeClass('border-red-500').addClass('border-gray-300');
                    } else if (value.length > 0) {
                        $(this).removeClass('border-gray-300').addClass('border-red-500');
                    }
                });
            });
        </script>
<?php
    }
}
