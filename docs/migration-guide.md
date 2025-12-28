# Migration guide

Étapes pour migrer l'ancienne structure vers la nouvelle:

1. Créer un dossier `Pages_cv` et déplacer les fichiers de `includes/Admin/Pages/CvPage.php` vers `Pages_cv/`.
2. Ajouter `manifest.json` ou `Pages.php` avec le champ `slug`.
3. Mettre à jour `OptionStore` pour utiliser une clé namespaced (ex: `cv_contenus` → `cv_contenus_v2`), ou laisser la compatibilité.
4. Vérifier les routes enregistrées et adapter le préfixe si nécessaire.
