<?php
namespace Admin\Pages;

use Storage\OptionStore;

require_once __DIR__ . '/Tabs/IdentityTab.php';
require_once __DIR__ . '/Tabs/ContactTab.php';
require_once __DIR__ . '/Tabs/SavoirEtreTab.php';

class CvPage
{
    protected static string $slug  = 'cv';
    protected static string $title = 'CV';

    public function render(): void
    {
        // Récupération des données
        $identity   = OptionStore::get('identity');
        $contact    = OptionStore::get('contact');
        $savoirEtre = OptionStore::get('savoir_etre', '');

        // Sauvegarde
        if (isset($_POST['cv_save'])) {
            OptionStore::set('identity', IdentityTab::sanitize($_POST['identity'] ?? []));
            OptionStore::set('contact', ContactTab::sanitize($_POST['contact'] ?? []));
            OptionStore::set('savoir_etre', SavoirEtreTab::sanitize($_POST['savoir_etre'] ?? []));
            
            echo '<div class="updated notice"><p>Toutes les données ont été enregistrées !</p></div>';

            // recharger pour affichage
            $identity   = OptionStore::get('identity');
            $contact    = OptionStore::get('contact');
            $savoirEtre = OptionStore::get('savoir_etre', '');
        }

        // Onglet actif
        $active_tab = $_GET['tab'] ?? 'identity';
        ?>

        <div class="wrap">
            <h1><?= esc_html(self::$title) ?></h1>

            <form method="post">

                <h2 class="nav-tab-wrapper">
                    <a href="#identity" class="nav-tab <?= $active_tab === 'identity' ? 'nav-tab-active' : '' ?>" data-tab="identity">Identité</a>
                    <a href="#contact" class="nav-tab <?= $active_tab === 'contact' ? 'nav-tab-active' : '' ?>" data-tab="contact">Contact</a>
                    <a href="#savoir_etre" class="nav-tab <?= $active_tab === 'savoir_etre' ? 'nav-tab-active' : '' ?>" data-tab="savoir_etre">Savoir-être</a>
                </h2>

                <div class="tab-panel" id="identity" style="<?= $active_tab === 'identity' ? '' : 'display:none;' ?>">
                    <?php IdentityTab::render($identity); ?>
                </div>

                <div class="tab-panel" id="contact" style="<?= $active_tab === 'contact' ? '' : 'display:none;' ?>">
                    <?php ContactTab::render($contact); ?>
                </div>

                <div class="tab-panel" id="savoir_etre" style="<?= $active_tab === 'savoir_etre' ? '' : 'display:none;' ?>">
                    <?php SavoirEtreTab::render($savoirEtre); ?>
                </div>

                <?php submit_button('Enregistrer', 'primary', 'cv_save'); ?>

            </form>
        </div>

        <script>
        // JS pour basculer entre les onglets sans recharger la page
        document.querySelectorAll('.nav-tab').forEach(tab => {
            tab.addEventListener('click', e => {
                e.preventDefault();
                const target = tab.dataset.tab;

                // masquer tous les panels
                document.querySelectorAll('.tab-panel').forEach(p => p.style.display = 'none');
                // retirer active
                document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('nav-tab-active'));

                // afficher panel sélectionné
                document.getElementById(target).style.display = 'block';
                tab.classList.add('nav-tab-active');
            });
        });
        </script>
        <?php
    }
}