# RAG – Manifest Admin & API déclarative (WordPress)

## 🎯 Objectif

Créer un `manifest.json` qui décrit :

- la page admin (slug, titre)
- les onglets
- les champs (input, textarea, media, etc.)
- les règles de sauvegarde
- les clés d’options (OptionStore)
- (optionnel) la correspondance avec l’API REST / OpenAPI

➡️ **PHP devient un moteur de rendu, pas une source de vérité.**

---

## 📁 État actuel (analyse de l’existant)

D’après l’architecture actuelle du plugin :

- `CvPage.php` → structure de la page admin + gestion des onglets
- `IdentityTab.php`
- `ContactTab.php`
- `SavoirEtreTab.php`
- `AutresInformationsTab.php`
- Stockage via `OptionStore::get('contenus')`
- API REST déjà décrite via OpenAPI

👉 **L’ensemble est parfaitement convertible en manifest déclaratif.**  
Les fichiers `*Tab.php` deviennent des **descripteurs**, puis obsolètes à terme.

---

## ✅ Manifest.json proposé (structure cible)

```json
{
  "plugin": {
    "slug": "cv",
    "title": "CV",
    "option_key": "contenus",
    "capability": "manage_options"
  },

  "page": {
    "menu_icon": "dashicons-id",
    "position": 30,
    "submit_label": "Enregistrer le CV"
  },

  "tabs": [
    {
      "id": "identity",
      "label": "Identité",
      "description": "Informations principales",
      "fields": [
        {
          "name": "job_title",
          "label": "Poste",
          "type": "text",
          "placeholder": "Développeur web",
          "sanitize": "text"
        },
        {
          "name": "first_name",
          "label": "Prénom",
          "type": "text",
          "sanitize": "text"
        },
        {
          "name": "last_name",
          "label": "Nom",
          "type": "text",
          "sanitize": "text"
        },
        {
          "name": "summary",
          "label": "Résumé",
          "type": "textarea",
          "rows": 5,
          "sanitize": "textarea"
        },
        {
          "name": "photo",
          "label": "Photo d'identité",
          "type": "media",
          "mime": ["image/jpeg", "image/png"]
        }
      ]
    },

    {
      "id": "contact",
      "label": "Contact",
      "fields": [
        {
          "name": "email",
          "label": "Email",
          "type": "email",
          "sanitize": "email"
        },
        {
          "name": "phone",
          "label": "Téléphone",
          "type": "text",
          "sanitize": "text"
        },
        {
          "name": "website",
          "label": "Site web",
          "type": "url",
          "sanitize": "url"
        }
      ]
    },

    {
      "id": "savoir_etre",
      "label": "Savoir-être",
      "description": "Qualités humaines",
      "fields": [
        {
          "name": "values",
          "label": "Liste",
          "type": "textarea_list",
          "placeholder": "Une qualité par ligne",
          "sanitize": "string_array"
        }
      ]
    },

    {
      "id": "autres_informations",
      "label": "Autres informations",
      "fields": [
        {
          "name": "hobbies",
          "label": "Centres d'intérêt",
          "type": "textarea",
          "sanitize": "textarea"
        }
      ]
    }
  ]
}
```

---

## 🧠 Correspondance avec le code existant

| PHP actuel | Manifest |
|-----------|----------|
| `CvPage::$slug` | `plugin.slug` |
| `OptionStore::get('contenus')` | `plugin.option_key` |
| `render()` | moteur basé sur `tabs[]` |
| `sanitize()` par tab | `field.sanitize` |
| HTML input | `field.type` |
| Onglets | `tabs[]` |

👉 Les fichiers `*Tab.php` deviennent **obsolètes**  
(peuvent subsister comme fallback legacy).

---

## ⚙️ Moteur PHP minimal (exemple)

### Chargement du manifest

```php
$manifest = json_decode(
    file_get_contents(__DIR__ . '/manifest.json'),
    true
);
```

### Rendu des onglets

```php
foreach ($manifest['tabs'] as $tab) {
    echo '<a class="nav-tab" href="?tab=' . esc_attr($tab['id']) . '">';
    echo esc_html($tab['label']);
    echo '</a>';
}
```

### Rendu des champs

```php
foreach ($tab['fields'] as $field) {
    FieldRenderer::render(
        $field,
        $data[$tab['id']][$field['name']] ?? null
    );
}
```

---

## 🧩 FieldRenderer (clé du système)

```php
class FieldRenderer
{
    public static function render(array $field, $value): void
    {
        switch ($field['type']) {
            case 'text':
            case 'email':
            case 'url':
                printf(
                    '<input type="%s" name="contenus[%s][%s]" value="%s" class="regular-text">',
                    esc_attr($field['type']),
                    esc_attr($GLOBALS['current_tab']),
                    esc_attr($field['name']),
                    esc_attr($value)
                );
                break;

            case 'textarea':
                printf(
                    '<textarea name="contenus[%s][%s]" rows="%d">%s</textarea>',
                    esc_attr($GLOBALS['current_tab']),
                    esc_attr($field['name']),
                    $field['rows'] ?? 4,
                    esc_textarea($value)
                );
                break;
        }
    }
}
```

---

## 🚀 Bonus stratégique

Le manifest peut également servir à :

- Générer automatiquement OpenAPI (schemas)
- Générer un front React / Vue
- Exposer `/cv/v1/meta`
- Valider les données côté API
- Générer la documentation admin

---

> **Conclusion**  
> Le manifest devient le contrat.  
> PHP devient l’exécuteur.  
> L’API devient gouvernée, versionnée et stable.
