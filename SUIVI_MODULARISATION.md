# Suivi migration wp-corbidev-api vers un plugin générique

## Objectif

Transformer `wp-corbidev-api` en socle générique, permettant la création et l'extension de pages API via des plugins enfants. Les APIs existantes seront transférées dans des plugins enfants dédiés, avec une nomenclature harmonisée.

---

## Étapes principales

- [x] **1. Extraire la logique de découverte des modules Pages\_\***
  - Rendre la découverte des modules accessible via un hook/filtre pour les plugins enfants (filtre `corbidev_api_pages_dirs` ajouté dans Loader.php).

- [x] **2. Permettre l'enregistrement de dossiers de pages API externes**
  - Filtre `corbidev_api_pages_dirs` opérationnel : les plugins enfants peuvent déclarer leurs dossiers (voir exemple dans wp-corbidev-api-cv).

- [x] **3. Adapter le chargement dynamique**
  - Le loader inclut désormais les dossiers déclarés par d'autres plugins via le filtre (voir Loader.php).

- [x] **4. Créer un modèle de plugin enfant**
  - Exemple : `wp-corbidev-api-modele` créé avec la structure et un manifest (voir includes/Admin/Pages/Modele).
  - Structure :
    - `includes/Admin/Pages/<slug>`
    - Respecter la convention de nommage : `wp-corbidev-api-<slug>`

- [x] **5. Transférer les APIs existantes dans des plugins enfants**
  - Plugins enfants créés pour chaque module existant (cv, test). Les dossiers et fichiers ont été migrés.
  - Supprimer les modules du plugin principal après validation du bon fonctionnement.

- [ ] **6. Documenter l'API d'extension**

  - Fournir un README pour expliquer comment créer un plugin enfant.

- [ ] **7. Tester l'ajout/suppression d'un plugin enfant**

  - Vérifier que les routes/pages API sont bien ajoutées/retirées dynamiquement.

- [ ] **8. Vérifier la compatibilité ascendante**

  - S'assurer que les modules internes existants fonctionnent une fois transférés.

- [ ] **9. Mettre à jour le README du plugin principal**
  - Expliquer la nouvelle architecture modulaire.

---

## Points de vigilance

- [ ] Isoler le code de découverte pour éviter les conflits de namespace.
- [ ] Harmoniser la convention de nommage des plugins enfants : `wp-corbidev-api-<slug>`.
- [ ] Gérer la désactivation/suppression d'un plugin enfant (suppression des routes/pages API associées).
- [ ] Documenter la migration pour les utilisateurs existants.

---

## Exemple de structure finale

```
wp-content/plugins/
├── wp-corbidev-api/                # Plugin socle générique
├── wp-corbidev-api-cv/             # Plugin enfant pour l'API CV
├── wp-corbidev-api-test/           # Plugin enfant pour l'API Test
└── ...
```

---

N'hésitez pas à cocher chaque étape au fur et à mesure de l'avancement.
