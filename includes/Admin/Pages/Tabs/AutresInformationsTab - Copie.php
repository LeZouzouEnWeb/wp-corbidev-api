<?php
namespace Admin\Pages;

use Storage\OptionStore;

class AutresInformationsTab
{
    public static function sanitize(array $data): array
    {
        $modules = [];

        if (!isset($data['modules']) || !is_array($data['modules'])) {
            return $modules;
        }

        foreach ($data['modules'] as $module) {
            if (empty($module['title']) && empty($module['content'])) {
                continue;
            }

            $content = [];

            if (is_string($module['content'])) {
                $content = preg_split("/\r\n|\n|\r/", $module['content']);
                $content = array_map('sanitize_text_field', $content);
                $content = array_filter($content);
            }

            $modules[] = [
                'title'   => sanitize_text_field($module['title'] ?? ''),
                'content' => $content,
            ];
        }

        return $modules;
    }

    public static function render(): void
    {
        $modules = OptionStore::get('autres_informations');
        ?>

        <div id="autres-informations-wrapper">
            <?php if (!empty($modules)) : ?>
                <?php foreach ($modules as $index => $module) : ?>
                    <?php self::renderModule($index, $module); ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <button type="button" class="button" id="add-autres-module">
            + Ajouter un module
        </button>

        <script>
        (function () {
            let index = <?php echo count($modules); ?>;

            document.getElementById('add-autres-module').addEventListener('click', function () {
                const wrapper = document.getElementById('autres-informations-wrapper');

                const html = `
                <div class="autres-module">
                    <h4>Module</h4>

                    <input type="text"
                           name="autres_informations[modules][${index}][title]"
                           placeholder="Titre du module"
                           class="regular-text" />

                    <textarea name="autres_informations[modules][${index}][content]"
                              rows="5"
                              class="large-text"
                              placeholder="Une information par ligne"></textarea>

                    <button type="button" class="button remove-module">Supprimer</button>
                    <hr>
                </div>`;

                wrapper.insertAdjacentHTML('beforeend', html);
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
                padding: 15px;
                margin-bottom: 15px;
                border: 1px solid #ddd;
            }
        </style>

        <?php
    }

    private static function renderModule(int $index, array $module): void
    {
        ?>
        <div class="autres-module">
            <h4>Module</h4>

            <input type="text"
                   name="autres_informations[modules][<?php echo $index; ?>][title]"
                   value="<?php echo esc_attr($module['title'] ?? ''); ?>"
                   class="regular-text"
                   placeholder="Titre du module" />

            <textarea name="autres_informations[modules][<?php echo $index; ?>][content]"
                      rows="5"
                      class="large-text"
                      placeholder="Une information par ligne"><?php
                echo esc_textarea(implode("\n", $module['content'] ?? []));
            ?></textarea>

            <button type="button" class="button remove-module">Supprimer</button>
            <hr>
        </div>
        <?php
    }
}