# Exemple de manifest JSON pour un plugin enfant type

Ce manifest décrit la structure d'un plugin enfant pour l'API builder versionné.

```json
{
  "name": "API Enfant Exemple",
  "description": "Exemple de manifest pour un plugin enfant du socle wp-corbidev-api.",
  "version": "1.0.0",
  "modules": [
    {
      "name": "exemple",
      "tabs": [
        {
          "name": "Informations",
          "fields": [
            {
              "key": "titre",
              "label": "Titre",
              "type": "input",
              "validation": { "required": true }
            },
            {
              "key": "description",
              "label": "Description",
              "type": "textarea"
            },
            {
              "key": "categorie",
              "label": "Catégorie",
              "type": "select",
              "options": ["A", "B", "C"]
            },
            { "key": "actif", "label": "Actif", "type": "checkbox" },
            {
              "key": "tags",
              "label": "Tags",
              "type": "list",
              "mode": "toggle",
              "modes": ["dynamic", "textarea"],
              "placeholder": "Un tag par ligne"
            }
          ]
        }
      ]
    }
  ],
  "permissions": { "crud": ["create", "read", "update", "delete"] },
  "meta": {
    "created_at": "2025-12-28T12:00:00Z",
    "updated_at": "2025-12-28T12:00:00Z",
    "deprecated": false,
    "expires_at": null
  }
}
```

## Champs obligatoires

- `name`, `description`, `version`, `modules`
- Pour chaque module : `name`, `tabs`
- Pour chaque tab : `name`, `fields`
- Pour chaque field : `key`, `label`, `type`
- `permissions.crud`

## Champs optionnels

- `options`, `validation`, `meta`, `deprecated`, `expires_at`, `mode`, `modes`, `placeholder`

## Types de champs supportés

- `input` : champ texte simple
- `textarea` : zone de texte multi-ligne
- `select` : liste déroulante (avec `options`)
- `checkbox` : case à cocher
- `list` : liste dynamique ou textarea (mode toggle)
- `media` : sélection de média WordPress
- `object_list` : liste d'objets (ex : modules avec titre + contenu)

---

> Ce manifest sert de base pour la conversion de tout plugin enfant en manifest JSON versionné.
