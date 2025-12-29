# RAG DE CONNAISSANCE — API BUILDER WORDPRESS DÉCLARATIF & VERSIONNÉ

## 1. Objectif du projet

Créer un **moteur d'API déclaratif dans WordPress**, administrable via une interface admin, permettant de :

- Définir dynamiquement des modèles d'API (modules, champs, CRUD)
- Générer automatiquement :
  - Routes REST WordPress
  - OpenAPI / Swagger
  - UI Admin dynamique
- Gérer la sécurité via JWT (optionnelle)
- Versionner les modèles (anti-régression)
- Informer les consommateurs lors de l'obsolescence
- Exporter / importer des modèles

Le tout **piloté par des manifests JSON stockés en base de données**.

---

## 2. Concepts clés

### Model

- Représente une API fonctionnelle (ex: CV)
- Possède plusieurs versions

### Version

- Snapshot immuable d'un modèle
- États :
  - draft
  - active
  - obsolete
  - expired

### Module

- Ressource métier (identity, contact, savoir_etre…)

### Field

- Champ typé d'un module

### Runtime

- Exécution en lecture seule d'une version active

---

## 3. Structure de base de données

### wp_api_models

- id
- slug
- name
- description
- active_version_id
- created_at

### wp_api_model_versions

- id
- model_id
- version
- status
- expires_at
- manifest_json
- created_at

### wp_api_runtime_data

- id
- model_id
- version_id
- module
- data_json
- created_at
- updated_at

### wp_api_keys (JWT)

- id
- key_hash
- permissions_json
- active

---

## 4. Manifest JSON (contrat central)

### Exemple simplifié

```json
{
  "meta": {
    "model": "cv",
    "version": "1.0.0",
    "status": "active",
    "jwt_required": true
  },
  "modules": {
    "identity": {
      "label": "Identité",
      "crud": ["read", "update"],
      "fields": {
        "first_name": { "type": "text" },
        "summary": { "type": "textarea" },
        "photo": { "type": "media" }
      }
    }
  }
}
```

---

## 5. Types de champs supportés

- text
- textarea
- media
- select
- checkbox
- number
- json
- list
  - textarea (1 élément par ligne)
  - inputs (1 input par élément)
  - mode sélectionnable par checkbox

---

## 6. Sécurité JWT & CRUD

### Règle

- Si `jwt_required = true`
  - Clé absente → 401
  - CRUD non autorisé → 403
- Sinon → accès libre

### Permissions par clé

```json
{
  "identity": ["read"],
  "savoir_etre": ["read", "update"]
}
```

---

## 7. Réponse API standardisée

```json
{
  "status": 200,
  "title": "Succès",
  "message": "Requête traitée",
  "data": {},
  "version": "1.0.0",
  "deprecated": false,
  "expires_at": null
}
```

---

## 8. Versionning & anti-régression

Règles :

- Une version active est immuable
- Toute modification → nouvelle version
- Une version obsolete reste accessible
- Une version expired :
  - n'est plus accessible
  - peut être supprimée

Message d'avertissement automatique pour les consommateurs.

---

## 9. Workflow Admin

1. Créer un modèle
2. Ajouter modules
3. Ajouter champs
4. Configurer CRUD
5. Activer JWT (optionnel)
6. Valider → version 1.0.0
7. Activer
8. Modifier → popup confirmation
9. Nouvelle version (1.1.0)

---

## 10. Export / Import

### Export

- JSON du manifest
- Métadonnées version

### Import

- Validation
- Preview
- Création d'une nouvelle version

---

## 11. Génération automatique

Depuis le manifest :

- Routes REST WP
- OpenAPI
- UI Admin
- Validation
- Sécurité
- Runtime API

---

# PROMPTS COPILOT (À UTILISER TELS QUELS)

## Prompt 1 — Générateur de routes

"À partir de ce manifest JSON, génère dynamiquement les routes WordPress REST API avec vérification JWT et CRUD."

## Prompt 2 — Runtime API

"Crée un moteur PHP Runtime qui lit une version active du manifest JSON et sert les données avec réponse HTTP normalisée."

## Prompt 3 — OpenAPI

"Génère un fichier OpenAPI 3.1 complet à partir de ce manifest JSON (schemas, routes, réponses)."

## Prompt 4 — UI Admin

"Crée une interface admin WordPress dynamique basée sur un manifest JSON pour gérer modules, champs et CRUD."

## Prompt 5 — Versionning

"Implémente un système de versionning immuable avec statuts active / obsolete / expired et alertes API."

## Prompt 6 — JWT

"Ajoute un middleware JWT WordPress avec permissions CRUD par module et gestion 401 / 403."

---

## 12. Positionnement architectural

Ce système se rapproche de :

- Strapi (Content Types Builder)
- Directus
- PostgREST

Mais reste :

- 100% WordPress
- JSON contract-first
- Headless
- Versionné

---

FIN DU DOCUMENT
