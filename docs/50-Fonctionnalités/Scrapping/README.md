# 🚀 Fonctionnalité de Scrapping - KrosmozJDR

## 🎯 Objectif

Cette fonctionnalité permet de récupérer, convertir et intégrer des données depuis des sites externes (comme DofusDB) vers KrosmozJDR. Les données qui sont récupérées depuis des sites externes ne sont pas structurées comme ce dont a besoin KrosmozJDR. De plus, ces sites utilisent les données du jeu Dofus alors que KrosmozJDR se base sur le jeu Dofus mais avec des données simplifiées. Il y a donc besoin de convertir.

## 🏗️ Architecture

Cette fonctionnalité de scrapping est composée de **quatre composants** qui travaillent ensemble :

### **1. DataCollect** 📥
- **Objectif** : Récupérer les données brutes depuis des sites externes (DofusDB, etc.)
- **Responsabilités** :
  - Collecte des données via API REST
  - Gestion du cache et du rate limiting
  - Gestion des erreurs et retry
  - Filtrage des catégories d'objets
- **Entités collectées** : Classes, monstres, objets, sorts, effets, ensembles d'objets

### **2. DataConversion** 🔄
- **Objectif** : Convertir les données selon les caractéristiques et formules KrosmozJDR
- **Responsabilités** :
  - Conversion des valeurs selon les seuils définis
  - Application des formules de calcul
  - Validation des données converties
  - Corrections automatiques si nécessaire
- **Caractéristiques** : Service **agnostique** à la source de données

### **3. DataIntegration** 🔗
- **Objectif** : Établir le lien entre les données converties et la structure KrosmozJDR
- **Responsabilités** :
  - Mapping structurel entre DofusDB et KrosmozJDR
  - Gestion des relations entre entités
  - Sauvegarde en base de données
  - Gestion des conflits et doublons
- **Caractéristiques** : Gère le **mapping structurel** spécifique à DofusDB

### **4. ScrappingOrchestrator** 🎼
- **Objectif** : Coordonner l'ensemble du processus de scrapping
- **Responsabilités** :
  - Orchestration des appels aux services
  - Gestion des processus d'import
  - Monitoring et métriques
  - Interface unifiée pour le reste du projet
- **Caractéristiques** : **Point d'entrée unique** pour toutes les opérations de scrapping

## 🔄 Flux de données

```
Site externe (DofusDB) 
    ↓
DataCollect (récupération des données brutes)
    ↓
DataConversion (conversion selon caractéristiques KrosmozJDR)
    ↓
DataIntegration (mapping et sauvegarde)
    ↓
Base de données KrosmozJDR
```

## 📁 Structure des dossiers

```
app/Services/Scrapping/
├── DataCollect/           # Service de collecte
│   ├── DataCollectService.php
│   └── config.php
├── DataConversion/        # Service de conversion
│   ├── DataConversionService.php
│   └── config.php
├── DataIntegration/       # Service d'intégration
│   ├── DataIntegrationService.php
│   └── config.php
└── Orchestrator/          # Service d'orchestration
    ├── ScrappingOrchestrator.php
    └── config.php

config/
├── scrapping.php          # Configuration globale
└── characteristics.php    # Caractéristiques du jeu

docs/50-Fonctionnalités/Scrapping/
├── README.md              # Ce fichier
├── Data-collect/          # Documentation DataCollect
├── Data-conversion/       # Documentation DataConversion
├── Data-integration/      # Documentation DataIntegration
└── Orchestrator/          # Documentation Orchestrator
```

## 🌐 Sources de données supportées

### **DofusDB (Source principale)**
- **URL** : `https://api.dofusdb.fr`
- **Format** : JSON REST API
- **Langues** : fr, en, de, es, pt
- **Entités** : Classes, monstres, objets, sorts, effets, ensembles
- **Rate limiting** : 60 requêtes/minute

### **Extensibilité**
L'architecture permet d'ajouter facilement d'autres sources de données en créant de nouveaux services de collecte.

## 🔧 Configuration

### **Fichiers de configuration**
- **`config/scrapping.php`** : Configuration globale de tous les services
- **`config/characteristics.php`** : Caractéristiques du jeu et formules
- **`app/Services/Scrapping/*/config.php`** : Configuration spécifique à chaque service

### **Variables d'environnement principales**
```bash
# Activation du scrapping
SCRAPPING_ENABLED=true

# Configuration DofusDB
DOFUSDB_BASE_URL=https://api.dofusdb.fr
DOFUSDB_DEFAULT_LANGUAGE=fr

# Limites et timeouts
SCRAPPING_MAX_CONCURRENT=3
SCRAPPING_PROCESS_TIMEOUT=3600
SCRAPPING_COLLECT_TIMEOUT=30

# Cache et rate limiting
SCRAPPING_CACHE_ENABLED=true
SCRAPPING_RATE_LIMITING_ENABLED=true
SCRAPPING_RATE_LIMIT_REQUESTS=60
```

## 📊 Entités et mapping

### **Mapping DofusDB → KrosmozJDR**
- **`breeds`** → **`classes`** (Classes de personnages)
- **`monsters`** → **`monsters` + `creatures`** (Monstres et créatures)
- **`items`** → **`consumables`**, **`resources`**, **`items`** (selon type/catégorie)
- **`Ensemble d'items`** → **`panoplies`** (Ensembles d'équipements)
- **`spells` + `spell-levels`** → **`spells`** (Sorts avec niveaux)
- **`effects`** → **`effects`** (Effets et bonus)

### **Types d'objets supportés**
- **Armes** (typeId=1) : Épées, haches, bâtons, etc.
- **Armures** (typeId=2) : Plastrons, jambières, etc.
- **Boucliers** (typeId=3) : Boucliers de défense
- **Anneaux** (typeId=9) : Anneaux magiques
- **Amulettes** (typeId=10) : Amulettes de protection
- **Ceintures** (typeId=11) : Ceintures de force
- **Potions** (typeId=12) : Potions de soin, buffs
- **Bottes** (typeId=13) : Bottes de vitesse
- **Chapeaux** (typeId=14) : Chapeaux magiques
- **Ressources** (typeId=15) : Matériaux de craft
- **Équipements** (typeId=16) : Équipements divers
- **Fleurs** (typeId=35) : Fleurs et plantes

## 🚀 Utilisation

### 🖥️ Interface d'administration (Vue 3)

Une interface dédiée est disponible pour les administrateurs (`/scrapping`, route `scrapping.index`). Elle est responsive (mobile → desktop) et propose quatre onglets :

- **Entité** : import unitaire avec formulaires d’options (skip cache, force update, dry-run, validation). Un bouton *Prévisualiser* lance un fetch `GET /api/scrapping/preview/{type}/{id}` afin d’afficher :
  - Les données brutes converties.
  - L’éventuelle version déjà présente en base.
  - Un tableau de diff (champ, valeur actuelle, valeur importée) pour décider de conserver ou d’écraser l’entrée.
- **Plage d’ID** : import d’un intervalle (`start_id`, `end_id`). Le formulaire calcule le nombre d’entités concernées et vérifie la limite autorisée (classes 1‑19, monstres 1‑5000, etc.). Le bouton envoie `POST /api/scrapping/import/range`.
- **Import complet** : exécute `POST /api/scrapping/import/all` pour scrapper tout un type d’un coup (utile après un wipe). Un `Alert` rappelle que l’opération est longue.
- **Résultats** : historique horodaté de toutes les actions (entité, plage, import complet). Chaque entrée conserve la réponse JSON et les erreurs éventuelles pour audit.

Chaque action enregistre son résultat localement (pas besoin de recharger) et bascule automatiquement sur l’onglet *Résultats*. Le panneau de prévisualisation reste disponible tant qu’on ne le ferme pas ou qu’on n’importe pas la nouvelle version.

### **Via l'Orchestrateur (Recommandé)**
```php
use App\Services\Scrapping\Orchestrator\ScrappingOrchestrator;

$orchestrator = app(ScrappingOrchestrator::class);

// Import d'une classe
$result = $orchestrator->importClass(1);

// Import d'un monstre
$result = $orchestrator->importMonster(100);

// Import d'un objet
$result = $orchestrator->importItem(1000);

// Import en lot
$result = $orchestrator->importBatch([
    ['type' => 'class', 'id' => 1],
    ['type' => 'monster', 'id' => 100],
]);
```

### **Via les services individuels**
```php
use App\Services\Scrapping\DataCollect\DataCollectService;
use App\Services\Scrapping\DataConversion\DataConversionService;
use App\Services\Scrapping\DataIntegration\DataIntegrationService;

$collectService = app(DataCollectService::class);
$conversionService = app(DataConversionService::class);
$integrationService = app(DataIntegrationService::class);

// Collecte
$rawData = $collectService->collectClass(1);

// Conversion
$convertedData = $conversionService->convertClass($rawData);

// Intégration
$result = $integrationService->integrateClass($convertedData);
```

### 🖼️ Backfill des images locales (entités déjà importées)

Quand des entités existent déjà en base avec une image distante (ou sans image), vous pouvez **télécharger et stocker localement** les images DofusDB, sans relancer un scrapping complet.

Variables utiles (voir `config/scrapping.php`) :

```bash
# Active/désactive le téléchargement et stockage des images
SCRAPPING_IMAGES_ENABLED=true

# Répertoire et disk (Laravel)
SCRAPPING_IMAGES_DISK=public
SCRAPPING_IMAGES_BASE_DIR="scrapping/images"

# Limite de sécurité
SCRAPPING_IMAGES_MAX_BYTES=5242880
SCRAPPING_IMAGES_TIMEOUT=15
```

Commandes :

```bash
# Prévisualisation (ne télécharge rien, n'écrit rien)
php artisan scrapping:backfill-images --limit=50 --dry-run

# Backfill sur toutes les entités (resources/items/consumables/spells/monsters)
php artisan scrapping:backfill-images --limit=500

# Backfill ciblé
php artisan scrapping:backfill-images resource --limit=200

# Re-télécharge même si l'image locale existe déjà
php artisan scrapping:backfill-images resource --force --limit=200
```

### 📊 Tables “hybrides” (serveur + client) avec TanStack Table

Certaines pages d’administration utilisent une table centralisée (`EntityTable.vue`) capable de fonctionner en **2 modes** :

- **Mode serveur** (par défaut) : la pagination/filtrage/tri passe par Inertia + backend (stable pour très gros volumes).
- **Mode client** : on charge un lot important via API **à partir des filtres serveur courants** (baseline), puis **tri/filtre/recherche/pagination** se font instantanément côté navigateur (TanStack Table), avec **export CSV**. Les filtres UI deviennent alors une **couche additionnelle client** (ils ne peuvent pas élargir au-delà du sous-ensemble chargé).

Endpoints utilisés (chargement “mode client”) :

- `GET /api/entity-table/resources?limit=5000`
- `GET /api/entity-table/resource-types?limit=5000`

Notes :
- Le `limit` est **borné** côté backend (par défaut 5000, max 20000) pour éviter les charges excessives.
- Pour de très gros volumes, gardez le **mode serveur** et utilisez le mode client sur des lots ciblés (ex: après un filtre serveur).

## 📈 Monitoring et métriques

### **Métriques collectées**
- **Taux de succès** des conversions et intégrations
- **Temps de traitement** par type d'entité
- **Taux d'erreurs** et corrections automatiques
- **Utilisation des ressources** (mémoire, CPU)
- **Performance du cache** et des API externes

### **Seuils d'alerte**
- **Taux d'erreur** > 10% → Alerte
- **Temps de traitement** > 5 minutes → Alerte
- **Utilisation mémoire** > 80% → Alerte
- **Taux de succès** < 95% → Alerte

## 🔒 Sécurité

### **Mesures de sécurité**
- **Validation des entrées** utilisateur
- **Sanitisation des données** collectées
- **Rate limiting** pour éviter la surcharge
- **Logs d'audit** pour tracer les actions
- **Gestion des erreurs** sans exposition d'informations sensibles

### **Permissions requises**
- **Lecture** des données externes
- **Écriture** dans la base KrosmozJDR
- **Exécution** des commandes Artisan
- **Accès** aux logs et métriques

## 🧪 Tests

### **Types de tests**
- **Tests unitaires** : Chaque service individuellement
- **Tests d'intégration** : Communication entre services
- **Tests end-to-end** : Workflow complet de scrapping
- **Tests de performance** : Charge et limites

### **Exécution des tests**
```bash
# Tests unitaires
php artisan test --filter=Scrapping

# Tests avec couverture
php artisan test --coverage --filter=Scrapping

# Tests de performance
php artisan test --filter=ScrappingPerformance
```

## 📚 Documentation

### **Structure de la documentation**
Chaque composant possède sa propre documentation dans `docs/50-Fonctionnalités/Scrapping/` :

- **`README.md`** : Vue d'ensemble et utilisation
- **`DEFINITIONS.md`** : Définitions des données et structures
- **`SPECIFICATIONS.md`** : Cahier des charges détaillé
- **`API.md`** : Documentation de l'API et des endpoints

### **Documentation technique**
- **Architecture** : Schémas et flux de données
- **Configuration** : Variables d'environnement et paramètres
- **Dépannage** : Solutions aux problèmes courants
- **FAQ** : Questions fréquemment posées

## 🔄 Maintenance

### **Tâches de maintenance**
- **Nettoyage du cache** : Suppression des données expirées
- **Rotation des logs** : Archivage des anciens logs
- **Mise à jour des métriques** : Nettoyage des métriques obsolètes
- **Vérification de la santé** : Monitoring des services externes

### **Commandes de maintenance**
```bash
# Nettoyage du cache
php artisan scrapping:clear-cache

# Vérification de la santé
php artisan scrapping:health-check

# Nettoyage des logs
php artisan scrapping:cleanup-logs

# Mise à jour des métriques
php artisan scrapping:update-metrics
```

## 🚨 Dépannage

### **Problèmes courants**
1. **API DofusDB inaccessible** : Vérifier la connectivité réseau
2. **Rate limiting dépassé** : Réduire la fréquence des requêtes
3. **Erreurs de conversion** : Vérifier la configuration des caractéristiques
4. **Échecs d'intégration** : Vérifier la structure de la base de données

### **Logs et debugging**
- **Logs détaillés** dans `storage/logs/scrapping.log`
- **Métriques en temps réel** via l'API de monitoring
- **Mode debug** activable via configuration
- **Traçabilité** des processus avec IDs de corrélation

## 🔮 Évolutions futures

### **Fonctionnalités prévues**
- **Support multi-sources** : Intégration d'autres sites de données
- **Synchronisation automatique** : Mise à jour périodique des données
- **Interface web** : Dashboard de monitoring et contrôle
- **Webhooks** : Notifications en temps réel
- **Export des données** : Formats multiples (JSON, CSV, XML)

### **Améliorations techniques**
- **Cache distribué** : Support Redis/Memcached
- **Traitement asynchrone** : Queues et jobs en arrière-plan
- **API GraphQL** : Interface de requête plus flexible
- **Microservices** : Décomposition en services indépendants

---

## 📞 Support

Pour toute question ou problème avec cette fonctionnalité :

1. **Consulter la documentation** de chaque composant
2. **Vérifier les logs** pour identifier les erreurs
3. **Consulter les métriques** pour diagnostiquer les problèmes
4. **Contacter l'équipe de développement** si nécessaire

---

*Dernière mise à jour : Décembre 2024*