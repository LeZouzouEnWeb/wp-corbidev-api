<?php

namespace Admin\Pages;

class AutresInformationsTab
{
    /**
     * Sanitize des données
     */
    public static function sanitize(array $data): array
    {
        $modules = [];

        if (!isset($data['modules']) || !is_array($data['modules'])) {
            return $modules;
        }

        foreach ($data['modules'] as $module) {
            $title = sanitize_text_field(wp_unslash($module['title'] ?? ''));

            if (empty($module['content'])) {
                continue;
            }

            // Contenu depuis textarea (une ligne = une entrée)
            $content = is_string($module['content'])
                ? preg_split("/\r\n|\n|\r/", wp_unslash($module['content']))
                : array_map('wp_unslash', $module['content']);

            $content = array_map('sanitize_text_field', $content);
            $content = array_filter($content);

            if ($title === '' && empty($content)) {
                continue;
            }

            $modules[] = [
                'title'   => $title,
                'content' => $content,
            ];
        }

        return $modules;
    }

    /**
     * Rendu HTML
     */
    public static function render(array $modules = []): void
    {
?>
        <div class="mb-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
            <h3 class="text-sm font-semibold text-gray-800 mb-1 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Modules d'informations personnalisés
            </h3>
            <p class="text-xs text-gray-600">Créez des sections personnalisées (ex: Loisirs, Références, etc.)</p>
        </div>

        <div id="autres-informations-wrapper" class="space-y-4 mb-6">
            <?php if (empty($modules)): ?>
                <div class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                    <svg class="mx-auto w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-gray-600 text-sm">Aucun module ajouté</p>
                    <p class="text-gray-500 text-xs mt-1">Cliquez sur "Ajouter un module" ci-dessous</p>
                </div>
            <?php else: ?>
                <?php foreach ($modules as $index => $module) : ?>
                    <?php self::renderModule($index, $module); ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <button type="button" id="add-autres-module" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Ajouter un module
        </button>

        <script>
            (function() {
                let index = <?php echo count($modules); ?>;

                document.getElementById('add-autres-module').addEventListener('click', function() {
                    const wrapper = document.getElementById('autres-informations-wrapper');

                    wrapper.insertAdjacentHTML('beforeend', `
                    <div class="autres-module bg-white border border-gray-200 rounded-lg p-5 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-4">
                            <h4 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                                </svg>
                                Module ${index + 1}
                            </h4>
                            <button type="button" class="remove-module px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition-colors flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Supprimer
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Titre du module</label>
                                <input type="text"
                                    name="cv_options[autres_informations][modules][${index}][title]"
                                    pattern="[a-zA-ZÀ-ÿ0-9\\s\\-_']+"
                                    title="Seuls les lettres, chiffres, espaces, tirets, underscores et apostrophes sont autorisés"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 cv-autres-title-field"
                                    placeholder="Ex: Loisirs, Références, Projets personnels...">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Contenu</label>
                                <textarea
                                    name="cv_options[autres_informations][modules][${index}][content]"
                                    rows="5"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-mono text-sm"
                                    placeholder="Une information par ligne...\nEx:\n• Photographie\n• Voyages\n• Développement open source"></textarea>
                                <p class="mt-1 text-xs text-gray-500">Une information par ligne</p>
                            </div>
                        </div>
                    </div>
                `);

                    // Attacher la validation au nouveau champ
                    attachAutresTitleValidation();
                    index++;
                });

                document.addEventListener('click', function(e) {
                    if (e.target.classList.contains('remove-module') || e.target.closest('.remove-module')) {
                        const module = e.target.closest('.autres-module');
                        if (module) {
                            module.style.opacity = '0';
                            module.style.transform = 'translateX(20px)';
                            module.style.transition = 'all 0.3s ease';
                            setTimeout(() => module.remove(), 300);
                        }
                    }
                });

                function attachAutresTitleValidation() {
                    document.querySelectorAll('.cv-autres-title-field').forEach(field => {
                        field.removeEventListener('input', handleAutresTitleInput);
                        field.addEventListener('input', handleAutresTitleInput);
                    });
                }

                function handleAutresTitleInput(e) {
                    let value = e.target.value;
                    // Autoriser lettres, chiffres, espaces, tirets, underscores, apostrophes
                    let filtered = value.replace(/[^a-zA-ZÀ-ÿ0-9\s\-_']/g, '');

                    if (value !== filtered) {
                        e.target.value = filtered;
                    }

                    const pattern = /^[a-zA-ZÀ-ÿ0-9\s\-_']+$/;
                    if (filtered === '' || pattern.test(filtered)) {
                        e.target.classList.remove('border-red-500');
                        e.target.classList.add('border-gray-300');
                    } else if (filtered.length > 0) {
                        e.target.classList.remove('border-gray-300');
                        e.target.classList.add('border-red-500');
                    }
                }

                // Attacher la validation initiale
                attachAutresTitleValidation();
            })();
        </script>
    <?php
    }

    /**
     * Rendu d’un module existant
     */
    private static function renderModule(int $index, array $module): void
    {
    ?>
        <div class="autres-module bg-white border border-gray-200 rounded-lg p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-4">
                <h4 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                    </svg>
                    Module <?= $index + 1 ?>
                </h4>
                <button type="button" class="remove-module px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition-colors flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Supprimer
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Titre du module</label>
                    <input type="text"
                        name="cv_options[autres_informations][modules][<?= $index ?>][title]"
                        value="<?= esc_attr($module['title'] ?? '') ?>"
                        pattern="[a-zA-ZÀ-ÿ0-9\s\-_']+"
                        title="Seuls les lettres, chiffres, espaces, tirets, underscores et apostrophes sont autorisés"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 cv-autres-title-field"
                        placeholder="Ex: Loisirs, Références, Projets personnels...">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contenu</label>
                    <textarea
                        name="cv_options[autres_informations][modules][<?= $index ?>][content]"
                        rows="5"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-mono text-sm"
                        placeholder="Une information par ligne...&#10;Ex:&#10;• Photographie&#10;• Voyages&#10;• Développement open source"><?= esc_textarea(
                                                                                                                                                implode("\n", $module['content'] ?? [])
                                                                                                                                            ) ?></textarea>
                    <p class="mt-1 text-xs text-gray-500">Une information par ligne</p>
                </div>
            </div>
        </div>
<?php
    }
}
