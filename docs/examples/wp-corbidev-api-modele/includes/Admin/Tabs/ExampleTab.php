<?php

namespace Admin\Page;

class ExampleTab
{
    public static function sanitize(array $data): array
    {
        return [
            'example_field' => sanitize_text_field($data['example_field'] ?? ''),
        ];
    }

    public static function render(array $data): void
    {
        ?>
<div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Exemple d'onglet</h3>
    <label class="block text-sm font-medium text-gray-700 mb-2">Champ exemple</label>
    <input type="text" name="contenus[example][example_field]" value="<?= esc_attr($data['example_field'] ?? '') ?>"
        class="w-full px-4 py-2 border border-gray-300 rounded-lg">
</div>
<?php
    }
}