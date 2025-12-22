<?php

namespace Admin\Pages;

use Storage\OptionStore;

require_once __DIR__ . '/Tabs/IdentityTab.php';
require_once __DIR__ . '/Tabs/ContactTab.php';
require_once __DIR__ . '/Tabs/SavoirEtreTab.php';
require_once __DIR__ . '/Tabs/AutresInformationsTab.php';

class CvPage
{
    protected static string $slug  = 'cv';
    protected static string $title = 'CV';

    public function render(): void
    {
        // Récupération des données existantes
        $data = OptionStore::get('cv_options', [
            'identity'    => [],
            'contact'     => [],
            'savoir_etre' => [],
            'autres_informations' => [],
        ]);

        // Sauvegarde
        if (isset($_POST['cv_save'])) {
            $cv_post = $_POST['cv_options'] ?? [];

            OptionStore::set('cv_options', [
                'identity'    => IdentityTab::sanitize($cv_post['identity'] ?? []),
                'contact'     => ContactTab::sanitize($cv_post['contact'] ?? []),
                'savoir_etre' => SavoirEtreTab::sanitize($cv_post['savoir_etre'] ?? []),

                'autres_informations' => AutresInformationsTab::sanitize($cv_post['autres_informations'] ?? []),
            ]);


            echo '<div class="updated notice"><p>Toutes les données ont été enregistrées !</p></div>';

            // Recharger les données pour affichage
            $data = OptionStore::get('cv_options');
        }

        // Onglet actif
        $active_tab = $_GET['tab'] ?? 'identity';
?>

        <!-- Tailwind CSS CDN -->
        <script src="https://cdn.tailwindcss.com"></script>

        <div class="wrap">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-lg shadow-lg p-6 mb-8">
                    <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Gestion du CV
                    </h1>
                    <p class="text-blue-100 mt-2">Configurez toutes les informations de votre curriculum vitae</p>
                </div>

                <!-- Zone de notification d'erreur -->
                <div id="cv-validation-errors"></div>

                <form method="post" class="space-y-6">

                    <!-- Navigation Tabs -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="border-b border-gray-200">
                            <nav class="flex -mb-px overflow-x-auto">
                                <a href="?page=<?= self::$slug ?>&tab=identity"
                                    class="cv-tab whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors <?= $active_tab === 'identity' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Identité
                                    </span>
                                </a>
                                <a href="?page=<?= self::$slug ?>&tab=contact"
                                    class="cv-tab whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors <?= $active_tab === 'contact' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        Contact
                                    </span>
                                </a>
                                <a href="?page=<?= self::$slug ?>&tab=savoir_etre"
                                    class="cv-tab whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors <?= $active_tab === 'savoir_etre' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Savoir-être
                                    </span>
                                </a>
                                <a href="?page=<?= self::$slug ?>&tab=autres_informations"
                                    class="cv-tab whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors <?= $active_tab === 'autres_informations' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Autres Info
                                    </span>
                                </a>
                            </nav>
                        </div>

                        <!-- Tab Content -->
                        <div class="p-6 bg-gray-50">
                            <div
                                class="tab-panel bg-white rounded-lg shadow-sm p-6 <?= $active_tab === 'identity' ? '' : 'hidden' ?>">
                                <?php IdentityTab::render($data['identity'] ?? []); ?>
                            </div>

                            <div
                                class="tab-panel bg-white rounded-lg shadow-sm p-6 <?= $active_tab === 'contact' ? '' : 'hidden' ?>">
                                <?php ContactTab::render($data['contact'] ?? []); ?>
                            </div>

                            <div
                                class="tab-panel bg-white rounded-lg shadow-sm p-6 <?= $active_tab === 'savoir_etre' ? '' : 'hidden' ?>">
                                <?php SavoirEtreTab::render($data['savoir_etre'] ?? []); ?>
                            </div>

                            <div
                                class="tab-panel bg-white rounded-lg shadow-sm p-6 <?= $active_tab === 'autres_informations' ? '' : 'hidden' ?>">
                                <?php
                                AutresInformationsTab::render($data['autres_informations'] ?? []);
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end gap-4 bg-white p-6 rounded-lg shadow-md">
                        <button type="submit" name="cv_save"
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Enregistrer les modifications
                        </button>
                    </div>

                </form>
            </div>
        </div>

        <style>
            /* Override WordPress default styles */
            .wrap {
                margin: 0 !important;
                background: #f3f4f6;
                min-height: 100vh;
            }

            .form-table th {
                @apply font-semibold text-gray-700;
            }

            .form-table td {
                @apply text-gray-600;
            }

            .regular-text,
            .large-text {
                @apply border-gray-300 rounded-md shadow-sm focus: ring-indigo-500 focus:border-indigo-500;
            }

            textarea.large-text {
                @apply w-full;
            }
        </style>

        <script>
            // Navigation entre les onglets
            document.querySelectorAll('.cv-tab').forEach(tab => {
                tab.addEventListener('click', e => {
                    e.preventDefault();
                    const target = new URL(tab.href).searchParams.get('tab');

                    // Masquer tous les panels
                    document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));

                    // Retirer la classe active de tous les onglets
                    document.querySelectorAll('.cv-tab').forEach(t => {
                        t.classList.remove('border-indigo-500', 'text-indigo-600');
                        t.classList.add('border-transparent', 'text-gray-500');
                    });

                    // Activer l'onglet cliqué
                    tab.classList.remove('border-transparent', 'text-gray-500');
                    tab.classList.add('border-indigo-500', 'text-indigo-600');

                    // Afficher le panel correspondant
                    const tabs = ['identity', 'contact', 'savoir_etre', 'autres_informations'];
                    const panels = document.querySelectorAll('.tab-panel');
                    const index = tabs.indexOf(target);
                    if (index !== -1 && panels[index]) {
                        panels[index].classList.remove('hidden');
                    }
                });
            });
        </script>
<?php
    }
}
