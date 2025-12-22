<?php
namespace Admin\Pages;

use Storage\OptionStore;

// Inclure les onglets
require_once __DIR__ . '/Tabs/IdentityTab.php';
require_once __DIR__ . '/Tabs/ContactTab.php';
require_once __DIR__ . '/Tabs/SavoirEtreTab.php';

class CvPage
{
    protected static string $slug        = 'cv';
    protected static string $title       = 'CV';
    protected static string $optionGroup = 'cv_options_group';
    protected static string $optionKey   = 'cv_options';

    public static function registerSettings(): void
    {
        register_setting(
            self::$optionGroup, // Group
            self::$optionKey,   // Option name
            [
                'type' => 'array',
                'sanitize_callback' => [self::class, 'sanitize'],
            ]
        );
    }

    public static function sanitize(array $data): array
    {
        return [
            'identity'    => IdentityTab::sanitize($data['identity'] ?? []),
            'contact'     => ContactTab::sanitize($data['contact'] ?? []),
            'savoir_etre' => SavoirEtreTab::sanitize(['savoir_etre' => $data['savoir_etre'] ?? '']),
        ];
    }

    public static function render(): void
    {
        $data = get_option(self::$optionKey, []);
        $active_tab = $_GET['tab'] ?? 'identity';
        ?>
        <div class="wrap">
            <h1><?= esc_html(self::$title) ?></h1>

            <form method="post" action="options.php">
                <?php settings_fields(self::$optionGroup); ?>

                <h2 class="nav-tab-wrapper">
                    <a href="?page=<?= self::$slug ?>&tab=identity" class="nav-tab <?= $active_tab === 'identity' ? 'nav-tab-active' : '' ?>">Identité</a>
                    <a href="?page=<?= self::$slug ?>&tab=contact" class="nav-tab <?= $active_tab === 'contact' ? 'nav-tab-active' : '' ?>">Contact</a>
                    <a href="?page=<?= self::$slug ?>&tab=savoir_etre" class="nav-tab <?= $active_tab === 'savoir_etre' ? 'nav-tab-active' : '' ?>">Savoir-être</a>
                </h2>

                <div class="tab-content">
                    <?php
                    switch ($active_tab) {
                        case 'identity':
                            IdentityTab::render($data['identity'] ?? []);
                            break;
                        case 'contact':
                            ContactTab::render($data['contact'] ?? []);
                            break;
                        case 'savoir_etre':
                            SavoirEtreTab::render($data['savoir_etre'] ?? []);
                            break;
                    }
                    ?>
                </div>

                <?php submit_button('Enregistrer'); ?>
            </form>
        </div>

        <style>
        .tab-content { margin-top: 20px; }
        </style>
        <?php
    }
}