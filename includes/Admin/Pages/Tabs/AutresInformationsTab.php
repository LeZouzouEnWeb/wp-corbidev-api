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
            $title = sanitize_text_field($module['title'] ?? '');

            if (empty($module['content'])) {
                continue;
            }

            // Contenu depuis textarea (une ligne = une entrée)
            $content = is_string($module['content'])
                ? preg_split("/\r\n|\n|\r/", $module['content'])
                : [];

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
        <div id="autres-informations-wrapper">

            <?php foreach ($modules as $index => $module) : ?>
                <?php self::renderModule($index, $module); ?>
            <?php endforeach; ?>

        </div>

        <button type="button" class="button" id="add-autres-module">
            + Ajouter un module
        </button>

        <script>
        (function () {
            let index = <?php echo count($modules); ?>;

            document.getElementById('add-autres-module').addEventListener('click', function () {
                const wrapper = document.getElementById('autres-informations-wrapper');

                wrapper.insertAdjacentHTML('beforeend', `
                    <div class="autres-module">
                        <h4>Module</h4>

                        <input type="text"
                            name="cv_options[autres_informations][modules][${index}][title]"
                            class="regular-text"
                            placeholder="Titre du module">

                        <textarea
                            name="cv_options[autres_informations][modules][${index}][content]"
                            rows="5"
                            class="large-text"
                            placeholder="Une information par ligne"></textarea>

                        <button type="button" class="button remove-module">
                            Supprimer
                        </button>
                        <hr>
                    </div>
                `);

                index++;
            });

            document.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-module')) {
                    e.target.closest('.autres-module').remove();
                }
            });
        })();
        </script>

        <style>
            .autres-module {
                background: #f9f9f9;
                border: 1px solid #ddd;
                padding: 15px;
                margin-bottom: 15px;
            }

            .autres-module h4 {
                margin-top: 0;
            }
        </style>
        <?php
    }

    /**
     * Rendu d’un module existant
     */
    private static function renderModule(int $index, array $module): void
    {
        ?>
        <div class="autres-module">
            <h4>Module</h4>

            <input type="text"
                name="cv_options[autres_informations][modules][<?= $index ?>][title]"
                value="<?= esc_attr($module['title'] ?? '') ?>"
                class="regular-text"
                placeholder="Titre du module">

            <textarea
                name="cv_options[autres_informations][modules][<?= $index ?>][content]"
                rows="5"
                class="large-text"
                placeholder="Une information par ligne"><?= esc_textarea(
                    implode("\n", $module['content'] ?? [])
                ) ?></textarea>

            <button type="button" class="button remove-module">
                Supprimer
            </button>
            <hr>
        </div>
        <?php
    }
}