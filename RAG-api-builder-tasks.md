# 📝 Suivi des tâches – RAG API-Builder & Manifest Admin

## Objectif global

Créer un système d'API builder versionné, dynamique, administrable, avec conversion des plugins enfants en manifestes stockés en base, gestion CRUD, sécurité JWT, versionning, et interface d'admin avancée.

---

## Tâches principales

1. [ ] **Conversion des plugins enfants en manifest**

    - [ ] Lister les plugins enfants à convertir
        - [ ] Recenser tous les plugins enfants existants
        - [ ] Documenter pour chaque plugin : nom, fonction, structure principale
    - [ ] Définir le format cible du manifest (JSON)
        - [ ] Analyser la structure de chaque plugin (onglets, champs, règles, etc.)
        - [ ] Définir la structure JSON cible (ex : modules, onglets, champs, types, validations)
        - [ ] Lister les champs obligatoires et optionnels du manifest
    - [ ] Développer l'outil d'export/import manifest ↔ base de données
        - [ ] Écrire un script/procédure pour extraire la config du plugin et générer le manifest JSON
        - [ ] Permettre l'import d'un manifest JSON en base (création ou mise à jour)
        - [ ] Prévoir la gestion des versions lors de l'import/export
    - [ ] Stocker chaque manifest en base, versionné
        - [ ] Sauvegarder le manifest généré en base de données, avec version
        - [ ] Vérifier la conformité du manifest généré (tests de validation)
        - [ ] Documenter le process de conversion et de stockage

2. [ ] **Interface d'Admin dynamique**

    - [ ] Créer une UI pour lister, ajouter, modifier, supprimer des modèles/API
    - [ ] Permettre l'ajout/édition de modules, onglets, champs (input, textarea, media, listes)
    - [ ] Gérer les types de listes (input select, textarea multi-ligne, choix du mode via case à cocher)

3. [ ] **Gestion CRUD & sécurité**

    - [ ] Définir les droits CRUD par module (granularité module dans un 1er temps)
    - [ ] Intégrer la vérification JWT (clé, permissions CRU') si corbidev-jwt actif
    - [ ] Adapter la réponse API selon la présence/validité de la clé JWT

4. [ ] **Versionning & validation**'
'''
    - [ ] Implémenter le workflow d' validation/activation d'un modèle
        - [ ] Une seule étape de validation avant activation
        - [ ] Aff'cher une popup de confirmation avant activation
        - [ ] Garder un historique/log des validations/activations (envoi à l'API logs dans un second temps)
        - [ ] Contrôler l'accès à l'activation : seul l'admin peut valider
        - [ ] Ajouter'un contrôle d'accès aux API (version utilisable) selon les utilisateurs
    - [ ] Générer une nouvelle version à chaque modification validée (anti-régression)
    - [ ] Gérer l'obsolescence, la notification de dépréciation, et la suppression conditionnelle

5. [ ] **Export/Import & OpenAPI**

    - [ ] Permettre l'export/import de modèles (format : JSON)
    - [ ] Générer dynamiquement OpenAPI et routes à partir du manifest validé
    - [ ] Prévoir une interface de mapping manifest ↔ OpenAPI personnalisable

6. [ ] **Réponses API**
    - [ ] Standardiser les réponses : statut HTTP, titre, message, data, version, deprecated, expires_at

---

## Décisions / Points validés

- Interface de mapping manifest ↔ OpenAPI personnalisable : **OUI**
- Gestion fine des permissions CRUD : **par modules** (dans un 1er temps)
- Format d'export/import : **JSON**
- Workflow d'activation/validation :
  - Une seule étape de validation avant activation
  - Popup de confirmation obligatoire
  - Historique/logs des validations/activations (API logs à terme)
  - Seul l'admin peut activer/valider
  - Contrôle d'accès aux API (version utilisable) selon les utilisateurs
'
---

## Questions / Points à clarifier

- [ ] Détail du workflow d'activation/validation attendu (étapes, confirmations, logs, etc.)

---

## Historique des modifications

- 28/12/2025 : Création du fichier initial.
