<?php

namespace Admin\Page;

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
            $title = sanitize_text_field(
                wp_unslash($module['title'] ?? '')
            );

            if (!isset($module['content'])) {
                continue;
            }

            // Contenu depuis textarea (1 ligne = 1 entrée)
            $content = is_string($module['content'])
                ? preg_split("/\r\n|\n|\r/", wp_unslash($module['content']))
                : array_map('wp_unslash', (array) $module['content']);

            $content = array_map('sanitize_text_field', $content);
            $content = array_values(array_filter($content));

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
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Modules d’informations personnalisés
    </h3>
    <p class="text-xs text-gray-600">
        Créez des sections libres (Loisirs, Références, Projets…)
    </p>
</div>

<div id="autres-informations-wrapper" class="space-y-4 mb-6">

    <?php if (empty($modules)) : ?>
    <div class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
        <p class="text-gray-600 text-sm">Aucun module ajouté</p>
        <p class="text-gray-500 text-xs mt-1">
            Cliquez sur “Ajouter un module”
        </p>
    </div>
    <?php else : ?>
    <?php foreach ($modules as $index => $module) : ?>
    <?php self::renderModule($index, $module); ?>
    <?php endforeach; ?>
    <?php endif; ?>

</div>

<button type="button" id="add-autres-module"
    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors shadow-sm">
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
                <div class="autres-module bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                    <div class="flex items-start justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-800">
                            Module ${index + 1}
                        </h4>
                        <button type="button"
                            class="remove-module px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg">
                            Supprimer
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Titre</label>
                            <input type="text"
                                name="contenus[autres_informations][modules][${index}][title]"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Contenu</label>
                            <textarea
                                name="contenus[autres_informations][modules][${index}][content]"
                                rows="5"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg font-mono text-sm"
                                placeholder="Une information par ligne"></textarea>
                        </div>
                    </div>
                </div>
                `);

        index++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-module')) {
            e.target.closest('.autres-module')?.remove();
        }
    });
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
<div class="autres-module bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
    <div class="flex items-start justify-between mb-4">
        <h4 class="text-lg font-semibold text-gray-800">
            Module <?= $index + 1 ?>
        </h4>
        <button type="button"
            class="remove-module px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg">
            Supprimer
        </button>
    </div>

    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Titre</label>
            <input type="text" name="contenus[autres_informations][modules][<?= $index ?>][title]"
                value="<?= esc_attr($module['title'] ?? '') ?>"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Contenu</label>
            <textarea name="contenus[autres_informations][modules][<?= $index ?>][content]" rows="5"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg font-mono text-sm"><?= esc_textarea(
                            implode("\n", $module['content'] ?? [])
                        ) ?></textarea>
            <p class="mt-1 text-xs text-gray-500">Une information par ligne</p>
        </div>
    </div>
</div>
<?php
    }
}