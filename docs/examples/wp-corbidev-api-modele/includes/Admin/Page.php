<?php

namespace Admin\Page;

use Storage\OptionStore;

require_once __DIR__ . '/Tabs/ExampleTab.php';

class ModelePage
{
    protected static string $slug  = 'modele';
    protected static string $title = 'Modèle';

    public function render(): void
    {
        $active_tab = $_POST['active_tab'] ?? $_GET['tab'] ?? 'example';
        $data = OptionStore::get('contenus', [ 'example' => [] ]);
        $toast = null;

        if (isset($_POST['modele_save'])) {
            $modele_post = $_POST['contenus'] ?? [];
            OptionStore::set('contenus', [
                'example' => ExampleTab::sanitize($modele_post['example'] ?? []),
            ]);
            $toast = [
                'message'  => 'Données enregistrées avec succès',
                'type'     => 'success',
                'duration' => 5000,
            ];
            $data = OptionStore::get('contenus');
        }
?>
<script src="https://cdn.tailwindcss.com"></script>
<div class="wrap bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto py-8 px-6">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-lg shadow-lg p-6 mb-8">
            <h1 class="text-3xl font-bold text-white">Gestion du Modèle</h1>
            <p class="text-blue-100 mt-2">Administration des informations du modèle</p>
        </div>
        <form method="post" class="space-y-6">
            <input type="hidden" name="active_tab" value="<?= esc_attr($active_tab) ?>">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <nav class="flex border-b border-gray-200">
                    <?php
                        $tabs = [ 'example' => 'Exemple' ];
                        foreach ($tabs as $key => $label) :
                            $active = $active_tab === $key;
                    ?>
                    <a href="?page=<?= self::$slug ?>&tab=<?= $key ?>" data-tab="<?= $key ?>"
                        class="cv-tab px-6 py-4 text-sm font-medium border-b-2 <?= $active ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
                        <?= esc_html($label) ?>
                    </a>
                    <?php endforeach; ?>
                </nav>
                <div class="p-6 bg-gray-50 space-y-6">
                    <div class="<?= $active_tab === 'example' ? '' : 'hidden' ?> tab-panel">
                        <?php ExampleTab::render(isset($data['example']) && is_array($data['example']) ? $data['example'] : []); ?>
                    </div>
                </div>
            </div>
            <div class="flex justify-end bg-white p-6 rounded-lg shadow-md">
                <button type="submit" name="modele_save"
                    class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
<div id="toast-container" class="fixed bottom-6 right-6 z-50 space-y-3"></div>
<script>
document.querySelectorAll('.cv-tab').forEach(tab => {
    tab.addEventListener('click', e => {
        e.preventDefault();
        const target = tab.dataset.tab;
        document.querySelector('input[name="active_tab"]').value = target;
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
        document.querySelectorAll('.cv-tab').forEach(t => {
            t.classList.remove('border-indigo-500', 'text-indigo-600');
            t.classList.add('border-transparent', 'text-gray-500');
        });
        tab.classList.add('border-indigo-500', 'text-indigo-600');
        tab.classList.remove('border-transparent', 'text-gray-500');
        document.querySelectorAll('.tab-panel')[[...document.querySelectorAll('.cv-tab')].indexOf(tab)]
            .classList.remove('hidden');
    });
});

function showToast(message, type = 'info', duration = 5000) {
    const container = document.getElementById('toast-container');
    const colors = {
        success: 'bg-green-600',
        error: 'bg-red-600',
        warning: 'bg-yellow-500 text-black',
        info: 'bg-blue-600'
    };
    const toast = document.createElement('div');
    toast.className = `
                ${colors[type] || colors.info}
                text-white px-6 py-4 rounded-lg shadow-xl
                flex items-center gap-3
                opacity-0 translate-y-4
                transition-all duration-500
            `;
    toast.innerHTML = `
                <span class=\"font-medium\">${message}</span>
                <button class=\"ml-4 opacity-70 hover:opacity-100\">&times;</button>
            `;
    container.appendChild(toast);
    requestAnimationFrame(() => {
        toast.classList.remove('opacity-0', 'translate-y-4');
        toast.classList.add('opacity-100', 'translate-y-0');
    });
    toast.querySelector('button').onclick = () => removeToast(toast);
    setTimeout(() => removeToast(toast), duration);
}

function removeToast(toast) {
    toast.classList.add('opacity-0', 'translate-y-4');
    toast.classList.remove('opacity-100', 'translate-y-0');
    setTimeout(() => toast.remove(), 500);
}
<?php if ($toast): ?>
document.addEventListener('DOMContentLoaded', () => {
    showToast(
        <?= json_encode($toast['message']) ?>,
        <?= json_encode($toast['type']) ?>,
        <?= (int) $toast['duration'] ?>
    );
});
<?php endif; ?>
</script>
<?php
    }
}