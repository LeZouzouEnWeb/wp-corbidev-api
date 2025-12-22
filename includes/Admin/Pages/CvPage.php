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

        <div class="wrap">
            <h1><?= esc_html(self::$title) ?></h1>

            <form method="post">

                <h2 class="nav-tab-wrapper">
                    <a href="?page=<?= self::$slug ?>&tab=identity"
                        class="nav-tab <?= $active_tab === 'identity' ? 'nav-tab-active' : '' ?>">Identité</a>
                    <a href="?page=<?= self::$slug ?>&tab=contact"
                        class="nav-tab <?= $active_tab === 'contact' ? 'nav-tab-active' : '' ?>">Contact</a>
                    <a href="?page=<?= self::$slug ?>&tab=savoir_etre"
                        class="nav-tab <?= $active_tab === 'savoir_etre' ? 'nav-tab-active' : '' ?>">Savoir-être</a>

                    <a href="?page=<?= self::$slug ?>&tab=autres_informations"
                        class="nav-tab <?= $active_tab === 'autres_informations' ? 'nav-tab-active' : '' ?>">Autres Info</a>
                </h2>

                <div class="tab-panel" style="<?= $active_tab === 'identity' ? '' : 'display:none;' ?>">
                    <?php IdentityTab::render($data['identity'] ?? []); ?>
                </div>

                <div class="tab-panel" style="<?= $active_tab === 'contact' ? '' : 'display:none;' ?>">
                    <?php ContactTab::render($data['contact'] ?? []); ?>
                </div>

                <div class="tab-panel" style="<?= $active_tab === 'savoir_etre' ? '' : 'display:none;' ?>">
                    <?php SavoirEtreTab::render($data['savoir_etre'] ?? []); ?>
                </div>

                <div class="tab-panel" style="<?= $active_tab === 'autres_informations' ? '' : 'display:none;' ?>">
                    <?php
                    AutresInformationsTab::render($data['autres_informations'] ?? []);
                    ?>
                </div>


                <?php submit_button('Enregistrer', 'primary', 'cv_save'); ?>

            </form>
        </div>

        <style>
            .tab-panel {
                margin-top: 20px;
            }
        </style>

        <script>
            // JS simple pour basculer les onglets
            document.querySelectorAll('.nav-tab').forEach(tab => {
                tab.addEventListener('click', e => {
                    e.preventDefault();
                    const target = new URL(tab.href).searchParams.get('tab');

                    document.querySelectorAll('.tab-panel').forEach(p => p.style.display = 'none');
                    document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('nav-tab-active'));

                    document.querySelector(`.nav-tab[href$="tab=${target}"]`).classList.add('nav-tab-active');
                    document.querySelectorAll('.tab-panel')[['identity', 'contact', 'savoir_etre',
                            'autres_informations'
                        ].indexOf(target)]
                        .style.display = 'block';
                });
            });
        </script>
<?php
    }
}
