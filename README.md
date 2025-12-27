# WP Corbidev API

Plugin WordPress pour la gestion modulaire d'un CV avec API REST JSON.

## 📋 Description

WP Corbidev API est un plugin WordPress permettant de gérer les informations d'un CV de manière structurée via une interface d'administration et de les exposer via une API REST JSON. Idéal pour créer des CV dynamiques dans des applications headless.

## ✨ Fonctionnalités

- **Interface d'administration intuitive** avec système d'onglets
- **API REST complète** pour l'accès aux données du CV
- **Structure modulaire** pour une maintenance facilitée
- **Stockage sécurisé** des données via WordPress Options API
- **Validation et sanitisation** des données

## 📂 Structure du Plugin

```
wp-corbidev-api/
├── wp-corbidev-api.php          # Fichier principal du plugin
├── includes/
│   ├── Core/
│   │   └── Plugin.php           # Initialisation du plugin
│   ├── Api/
│   │   ├── Routes.php           # Déclaration des routes REST
│   │   └── CvController.php     # Contrôleur API
│   ├── Admin/
│   │   ├── Menu.php             # Menus d'administration
│   │   └── Pages/
│   │       ├── CvPage.php       # Page principale
│   │       └── Tabs/            # Onglets de formulaires
│   │           ├── IdentityTab.php
│   │           ├── ContactTab.php
│   │           ├── SavoirEtreTab.php
│   │           └── AutresInformationsTab.php
│   └── Storage/
│       └── OptionStore.php      # Gestion du stockage
└── README.md
```

## 🚀 Installation

1. Téléchargez le plugin
2. Placez le dossier `wp-corbidev-api` dans `/wp-content/plugins/`
3. Activez le plugin depuis l'administration WordPress

## ⚙️ Configuration

Une fois activé, accédez au menu **CV** dans l'administration WordPress pour configurer :

- **Identité** : Informations personnelles
- **Contact** : Coordonnées
- **Savoir-être** : Compétences comportementales
- **Autres informations** : Informations complémentaires

## 🔌 API REST

### Endpoints disponibles

#### Récupérer toutes les données

```http
GET /wp-json/cv/v1/all
```

**Réponse :**
```json
{
  "identity": {
    // Données d'identité
  }
}
```

#### Récupérer un module spécifique

```http
GET /wp-json/cv/v1/module/{module_name}
```

**Paramètres :**
- `module_name` : Nom du module (`identity`, `contact`, `savoir_etre`, `autres_informations`)

**Exemple :**
```http
GET /wp-json/cv/v1/module/identity
```

**Réponse :**
```json
{
  // Données du module demandé
}
```

### Permissions

Les endpoints sont actuellement publics (`permission_callback: '__return_true'`). Modifiez selon vos besoins de sécurité.

## 🛠️ Développement

### Architecture

Le plugin utilise une architecture MVC simplifiée :

- **Core** : Initialisation et chargement des composants
- **API** : Routes et contrôleurs REST
- **Admin** : Interface d'administration
- **Storage** : Couche d'abstraction pour le stockage

### Classes principales

#### Plugin (Core\Plugin)
Point d'entrée du plugin, gère l'initialisation et le chargement des fichiers.

#### Routes (Api\Routes)
Déclaration des routes REST API.

#### CvController (Api\CvController)
Gestion des requêtes API et récupération des données.

#### OptionStore (Storage\OptionStore)
Abstraction pour le stockage/récupération des données via WordPress Options API.

## 📝 Utilisation avec un frontend

### Exemple avec JavaScript

```javascript
// Récupérer toutes les données
fetch('https://votre-site.com/wp-json/cv/v1/all')
  .then(response => response.json())
  .then(data => console.log(data));

// Récupérer un module spécifique
fetch('https://votre-site.com/wp-json/cv/v1/module/identity')
  .then(response => response.json())
  .then(data => console.log(data));
```

### Exemple avec React

```jsx
import { useEffect, useState } from 'react';

function CV() {
  const [cvData, setCvData] = useState(null);

  useEffect(() => {
    fetch('https://votre-site.com/wp-json/cv/v1/all')
      .then(res => res.json())
      .then(data => setCvData(data));
  }, []);

  if (!cvData) return <div>Chargement...</div>;

  return (
    <div>
      <h1>{cvData.identity?.nom}</h1>
      {/* Affichage des données */}
    </div>
  );
}
```

## 📄 Licence

Ce plugin est développé par **Éric Corbisier**.

## 🔄 Versions

### 1.0.1 (Actuelle)
- Gestion modulaire des données CV
- API REST complète
- Interface d'administration avec onglets

## 🤝 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à ouvrir une issue ou une pull request.

## 📧 Support

Pour toute question ou problème, veuillez ouvrir une issue sur le dépôt.
