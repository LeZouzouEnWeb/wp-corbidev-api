<?php

namespace Admin\Pages;

class SavoirEtreTab
{
    /**
     * Sanitize : accepte soit une string (textarea),
     * soit un tableau (inputs dynamiques)
     */
    public static function sanitize(array|string $data): array
    {
        $values = [];

        if (is_string($data)) {
            $values = preg_split("/\r\n|\n|\r/", wp_unslash($data));
        } elseif (is_array($data)) {
            $values = array_map('wp_unslash', $data);
        }

        $values = array_map('sanitize_text_field', $values);
        return array_values(array_filter($values));
    }

    /**
     * Render
     */
    public static function render(array $data): void
    {
        $textarea_value = implode("\n", $data);
        ?>

        <!-- MODE TOGGLE -->
        <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" id="savoir-etre-mode"
                       class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <span class="text-sm font-medium text-gray-700">
                    Mode texte multi-lignes
                </span>
            </label>
            <p class="mt-2 text-xs text-gray-600">
                Cochez pour saisir les qualités ligne par ligne dans un textarea
            </p>
        </div>

        <!-- MODE LISTE DYNAMIQUE -->
        <div id="savoir-etre-dynamic">
            <div id="savoir-etre-list" class="space-y-3 mb-4">
                <?php if (empty($data)): ?>
                    <div class="savoir-etre-item flex gap-2">
                        <input type="text"
                               placeholder="Ex: Travail d'équipe"
                               class="cv-savoir-etre-field flex-1 px-4 py-2 border border-gray-300 rounded-lg">
                        <button type="button" class="remove px-3 py-2 bg-red-500 text-white rounded-lg">✕</button>
                    </div>
                <?php else: ?>
                    <?php foreach ($data as $value): ?>
                        <div class="savoir-etre-item flex gap-2">
                            <input type="text"
                                   value="<?= esc_attr($value) ?>"
                                   class="cv-savoir-etre-field flex-1 px-4 py-2 border border-gray-300 rounded-lg">
                            <button type="button" class="remove px-3 py-2 bg-red-500 text-white rounded-lg">✕</button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <button type="button" id="add-savoir-etre"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg">
                + Ajouter une qualité
            </button>
        </div>

        <!-- MODE TEXTAREA -->
        <div id="savoir-etre-textarea-container" class="hidden">
            <textarea id="savoir-etre-textarea"
                      rows="8"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg font-mono text-sm"><?= esc_textarea($textarea_value) ?></textarea>
            <p class="mt-2 text-xs text-gray-600">Une qualité par ligne</p>
        </div>

        <script>
        (function () {
            const checkbox = document.getElementById('savoir-etre-mode');
            const dynamic = document.getElementById('savoir-etre-dynamic');
            const textareaBox = document.getElementById('savoir-etre-textarea-container');
            const textarea = document.getElementById('savoir-etre-textarea');
            const list = document.getElementById('savoir-etre-list');
            const addBtn = document.getElementById('add-savoir-etre');
            const form = list.closest('form');

            /* Toggle mode */
            checkbox.addEventListener('change', () => {
                dynamic.classList.toggle('hidden', checkbox.checked);
                textareaBox.classList.toggle('hidden', !checkbox.checked);
            });

            /* Add input */
            addBtn.addEventListener('click', () => {
                const div = document.createElement('div');
                div.className = 'savoir-etre-item flex gap-2';
                div.innerHTML = `
                    <input type="text" class="cv-savoir-etre-field flex-1 px-4 py-2 border border-gray-300 rounded-lg">
                    <button type="button" class="remove px-3 py-2 bg-red-500 text-white rounded-lg">✕</button>
                `;
                list.appendChild(div);
                syncTextarea();
            });

            /* Remove input */
            list.addEventListener('click', e => {
                if (e.target.classList.contains('remove')) {
                    e.target.parentElement.remove();
                    syncTextarea();
                }
            });

            /* Textarea -> inputs */
            textarea.addEventListener('input', () => {
                const lines = textarea.value.split(/\r?\n/).filter(v => v.trim());
                list.innerHTML = '';
                lines.forEach(v => {
                    const div = document.createElement('div');
                    div.className = 'savoir-etre-item flex gap-2';
                    div.innerHTML = `
                        <input type="text" value="${v.replace(/"/g, '&quot;')}"
                               class="cv-savoir-etre-field flex-1 px-4 py-2 border border-gray-300 rounded-lg">
                        <button type="button" class="remove px-3 py-2 bg-red-500 text-white rounded-lg">✕</button>
                    `;
                    list.appendChild(div);
                });
            });

            /* Inputs -> textarea */
            function syncTextarea() {
                textarea.value = Array.from(
                    list.querySelectorAll('input')
                ).map(i => i.value).filter(v => v.trim()).join("\n");
            }

            /* FINAL SUBMIT HANDLING */
            form.addEventListener('submit', () => {
                if (checkbox.checked) {
                    textarea.setAttribute('name', 'contenus[savoir_etre]');
                    list.querySelectorAll('input').forEach(i => i.removeAttribute('name'));
                } else {
                    textarea.removeAttribute('name');
                    list.querySelectorAll('input').forEach(i =>
                        i.setAttribute('name', 'contenus[savoir_etre][]')
                    );
                }
            });
        })();
        </script>

        <?php
    }
}
