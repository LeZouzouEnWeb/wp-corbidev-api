# Pages_<slug> conventions

Chaque API doit être placée dans un dossier `Pages_<slug>` au niveau du plugin.

Fichiers attendus (au moins un):
- `Pages.php` : retourne un tableau PHP (manifest) avec au minimum `slug`.
- `manifest.json` : alternative JSON contenant `slug`, `display_name`, `modules`, `admin_class`, `api_contract`.

Exemple `manifest.json`:

```
{
  "slug": "cv",
  "display_name": "CV",
  "modules": ["identity", "experience"],
  "admin_class": "Admin\\Pages\\CvPage",
  "api_contract": "openapi.json"
}
```
