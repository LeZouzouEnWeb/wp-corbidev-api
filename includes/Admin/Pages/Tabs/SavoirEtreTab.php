<?php

namespace Admin\Pages;

class SavoirEtreTab
{
    public static function sanitize(array $data): array
    {
        $values = [];

        if (isset($data) && is_string($data)) {
            // Depuis le textarea
            $values = preg_split("/\r\n|\n|\r/", wp_unslash($data));
        } elseif (is_array($data)) {
            // Depuis les inputs dynamiques
            $values = array_map('wp_unslash', $data);
        }

        $values = array_map('sanitize_text_field', $values);
        return array_values(array_filter($values));
    }

    public static function render(array $data): void
    {
        $textarea_value = implode("\n", $data);
?>
        <!-- Mode Toggle -->
        <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" id="savoir-etre-mode" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                <span class="text-sm font-medium text-gray-700">
                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    Mode texte multi-lignes
                </span>
            </label>
            <p class="mt-2 text-xs text-gray-600">Cochez pour saisir vos qualités ligne par ligne dans un textarea</p>
        </div>

        <!-- Dynamic List Mode -->
        <div id="savoir-etre-dynamic">
            <div id="savoir-etre-list" class="space-y-3 mb-4">
                <?php if (empty($data)): ?>
                    <div class="savoir-etre-item flex gap-2">
                        <input type="text" name="cv_options[savoir_etre][]" value=""
                            placeholder="Ex: Travail d'équipe, Autonomie, Créativité..."
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                        <button type="button" class="remove px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                <?php else: ?>
                    <?php foreach ($data as $value): ?>
                        <div class="savoir-etre-item flex gap-2">
                            <input type="text" name="cv_options[savoir_etre][]" value="<?= esc_attr($value) ?>"
                                placeholder="Ex: Travail d'équipe, Autonomie, Créativité..."
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                            <button type="button" class="remove px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="button" id="add-savoir-etre" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Ajouter une qualité
            </button>
        </div>

        <!-- Textarea Mode -->
        <div id="savoir-etre-textarea-container" class="hidden">
            <textarea id="savoir-etre-textarea" rows="8"
                placeholder="Entrez une qualité par ligne...&#10;Ex:&#10;Travail d'équipe&#10;Autonomie&#10;Créativité"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-mono text-sm"><?= esc_textarea($textarea_value) ?></textarea>
            <p class="mt-2 text-xs text-gray-600">Une qualité par ligne</p>
        </div>

        <script>
            (function() {
                const listContainer = document.getElementById('savoir-etre-dynamic');
                const textareaContainer = document.getElementById('savoir-etre-textarea-container');
                const checkbox = document.getElementById('savoir-etre-mode');
                const list = document.getElementById('savoir-etre-list');
                const addBtn = document.getElementById('add-savoir-etre');
                const textarea = document.getElementById('savoir-etre-textarea');

                checkbox.addEventListener('change', () => {
                    if (checkbox.checked) {
                        listContainer.classList.add('hidden');
                        textareaContainer.classList.remove('hidden');
                    } else {
                        listContainer.classList.remove('hidden');
                        textareaContainer.classList.add('hidden');
                    }
                });

                addBtn.addEventListener('click', () => {
                    const div = document.createElement('div');
                    div.className = 'savoir-etre-item flex gap-2';
                    div.innerHTML = `
                    <input type="text" name="cv_options[savoir_etre][]" value=""
                           placeholder="Ex: Travail d'équipe, Autonomie, Créativité..."
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                    <button type="button" class="remove px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                `;
                    list.appendChild(div);
                    syncTextarea();
                });

                list.addEventListener('click', (e) => {
                    if (e.target.classList.contains('remove') || e.target.closest('.remove')) {
                        const item = e.target.closest('.savoir-etre-item');
                        if (item) {
                            item.remove();
                            syncTextarea();
                        }
                    }
                });

                textarea.addEventListener('input', () => {
                    const lines = textarea.value.split(/\r\n|\n|\r/).filter(l => l.trim() !== '');
                    list.innerHTML = '';
                    lines.forEach(l => {
                        const div = document.createElement('div');
                        div.className = 'savoir-etre-item flex gap-2';
                        div.innerHTML = `
                        <input type="text" name="cv_options[savoir_etre][]" value="${l.replace(/"/g,'&quot;')}"
                               placeholder="Ex: Travail d'équipe, Autonomie, Créativité..."
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                        <button type="button" class="remove px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    `;
                        list.appendChild(div);
                    });
                });

                const form = list.closest('form');
                form.addEventListener('submit', () => {
                    const inputs = list.querySelectorAll('input[type=text]');
                    const values = Array.from(inputs).map(i => i.value).filter(v => v.trim() !== '');
                    textarea.value = values.join("\n");
                });

                function syncTextarea() {
                    const inputs = list.querySelectorAll('input[type=text]');
                    const values = Array.from(inputs).map(i => i.value).filter(v => v.trim() !== '');
                    textarea.value = values.join("\n");
                }
            })();
        </script>
<?php
    }
}
