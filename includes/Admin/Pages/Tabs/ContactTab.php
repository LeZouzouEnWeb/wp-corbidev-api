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
        <div class="space-y-6">
            <!-- Coordonnées -->
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Coordonnées
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Adresse complète
                        </label>
                        <textarea name="cv_options[contact][address]" rows="3"
                                  placeholder="Ex: 123 Rue de la République&#10;75001 Paris, France"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"><?= esc_textarea($data['address'] ?? '') ?></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            Téléphone
                        </label>
                        <input type="tel" name="cv_options[contact][phone]" 
                               value="<?= esc_attr($data['phone'] ?? '') ?>"
                               placeholder="+33 6 12 34 56 78"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                            Email
                        </label>
                        <input type="email" name="cv_options[contact][email]" 
                               value="<?= esc_attr($data['email'] ?? '') ?>"
                               placeholder="votre.email@exemple.fr"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Sites web -->
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
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
                            <input type="url" name="cv_options[contact][websites][]" 
                                   value="<?= esc_attr($website) ?>"
                                   placeholder="https://www.exemple.com ou https://linkedin.com/in/votre-profil"
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <button type="button" class="remove-website px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <button type="button" id="add-website" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
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
            });
        </script>
<?php
    }
}
