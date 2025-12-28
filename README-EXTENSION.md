# Créer un plugin enfant pour wp-corbidev-api

Ce guide explique comment étendre le plugin socle `wp-corbidev-api` en créant un plugin enfant qui ajoute ses propres pages API.

## 1. Structure du plugin enfant

```plaintext
wp-corbidev-api-mon-module/
├── wp-corbidev-api-mon-module.php
└── includes/
    └── Admin/
        └── Pages/
            └── MonModule/
                ├── manifest.json
                ├── Pages.php
                └── ... (vos fichiers PHP)
```

## 2. Exemple de fichier principal

```php
<?php
/*
Plugin Name: wp-corbidev-api-mon-module
Description: Plugin enfant pour ajouter une API personnalisée au socle générique wp-corbidev-api
Version: 1.0.0
Author: Votre Nom
*/

add_filter('corbidev_api_pages_dirs', function($dirs) {
    $dirs[] = __DIR__ . '/includes/Admin/Pages/MonModule';
    return $dirs;
});
```

## 3. Exemple de manifest.json

```json
{
  "slug": "mon-module",
  "title": "API MonModule",
  "description": "API personnalisée pour le module MonModule.",
  "version": "1.0.0"
}
```

## 4. Exemple de Pages.php

```php
<?php
return [
    'slug' => 'mon-module',
    'title' => 'API MonModule',
    'description' => 'API personnalisée pour le module MonModule.',
    'version' => '1.0.0',
];
```

## 5. Ajouter vos fichiers PHP

Ajoutez vos contrôleurs, routes, helpers, etc. dans le dossier `includes/Admin/Pages/MonModule`.

## 6. Convention de nommage

- Le dossier du plugin : `wp-corbidev-api-<slug>`
- Le dossier de pages : `includes/Admin/Pages/<Slug>`
- Le slug doit être unique et cohérent avec le nom du plugin.

## 7. Activation

- Activez d'abord le plugin socle `wp-corbidev-api`.
- Activez ensuite votre plugin enfant.

## 8. Résultat

Votre API sera automatiquement découverte et exposée par le socle générique.

---

Pour toute extension, inspirez-vous du plugin exemple `wp-corbidev-api-modele` fourni dans le dépôt.
