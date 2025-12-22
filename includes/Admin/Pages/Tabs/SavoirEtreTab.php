<?php
namespace Admin\Pages;

class SavoirEtreTab
{
    public static function sanitize(array $data): array
    {
        $values = [];

        if (isset($data) && is_string($data)) {
            // Depuis le textarea
            $values = preg_split("/\r\n|\n|\r/", $data);
        } elseif (is_array($data)) {
            // Depuis les inputs dynamiques
            $values = $data;
        }

        $values = array_map('sanitize_text_field', $values);
        return array_values(array_filter($values));
    }

    public static function render(array $data): void
    {
        $textarea_value = implode("\n", $data);
        ?>
        <p>
            <label>
                <input type="checkbox" id="savoir-etre-mode" />
                Utiliser le textarea
            </label>
        </p>

        <div id="savoir-etre-dynamic">
            <div id="savoir-etre-list">
                <?php foreach ($data as $value): ?>
                    <div class="savoir-etre-item">
                        <input type="text" name="cv_options[savoir_etre][]" value="<?= esc_attr($value) ?>" />
                        <button type="button" class="button remove">✕</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button" id="add-savoir-etre">+ Ajouter</button>
        </div>

        <div id="savoir-etre-textarea-container" style="display:none;">
            <textarea id="savoir-etre-textarea" rows="5" style="width:100%;"><?= esc_textarea($textarea_value) ?></textarea>
        </div>

        <script>
        (function(){
            const listContainer = document.getElementById('savoir-etre-dynamic');
            const textareaContainer = document.getElementById('savoir-etre-textarea-container');
            const checkbox = document.getElementById('savoir-etre-mode');
            const list = document.getElementById('savoir-etre-list');
            const addBtn = document.getElementById('add-savoir-etre');
            const textarea = document.getElementById('savoir-etre-textarea');

            // Mode par défaut = liste dynamique
            listContainer.style.display = '';
            textareaContainer.style.display = 'none';
            checkbox.checked = false;

            checkbox.addEventListener('change', () => {
                if(checkbox.checked){
                    listContainer.style.display = 'none';
                    textareaContainer.style.display = '';
                } else {
                    listContainer.style.display = '';
                    textareaContainer.style.display = 'none';
                }
            });

            addBtn.addEventListener('click', () => {
                const div = document.createElement('div');
                div.className = 'savoir-etre-item';
                div.innerHTML = `<input type="text" name="cv_options[savoir_etre][]" value="" />
                                 <button type="button" class="button remove">✕</button>`;
                list.appendChild(div);
                syncTextarea();
            });

            list.addEventListener('click', (e) => {
                if(e.target.classList.contains('remove')){
                    e.target.parentElement.remove();
                    syncTextarea();
                }
            });

            textarea.addEventListener('input', () => {
                const lines = textarea.value.split(/\r\n|\n|\r/).filter(l => l.trim() !== '');
                list.innerHTML = '';
                lines.forEach(l => {
                    const div = document.createElement('div');
                    div.className = 'savoir-etre-item';
                    div.innerHTML = `<input type="text" name="cv_options[savoir_etre][]" value="${l.replace(/"/g,'&quot;')}" />
                                     <button type="button" class="button remove">✕</button>`;
                    list.appendChild(div);
                });
            });

            const form = list.closest('form');
            form.addEventListener('submit', () => {
                const inputs = list.querySelectorAll('input[type=text]');
                const values = Array.from(inputs).map(i => i.value).filter(v => v.trim() !== '');
                textarea.value = values.join("\n");
            });

            function syncTextarea(){
                const inputs = list.querySelectorAll('input[type=text]');
                const values = Array.from(inputs).map(i => i.value).filter(v => v.trim() !== '');
                textarea.value = values.join("\n");
            }
        })();
        </script>

        <style>
        .savoir-etre-item { margin-bottom: 5px; display: flex; gap: 5px; align-items: center; }
        .savoir-etre-item input { flex: 1; }
        </style>
        <?php
    }
}