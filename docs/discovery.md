# Discovery technique

La méthode `Core\\Loader::discoverApis($pluginBase)` doit:

1. Scanner `{$pluginBase}` pour `Pages_*` (glob).
2. Pour chaque dossier, tenter de charger `Pages.php` (retourne tableau) ou `manifest.json`.
3. Valider que le manifest contient `slug` et `display_name`.
4. Retourner une liste associative `slug => manifest` avec `__path` ajouté.

Le loader peut optionnellement inclure un fichier `includes.php` dans chaque dossier pour charger les classes locales.
