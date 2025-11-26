# Service Data-collect

## 🎯 Objectif

Le service **Data-collect** a pour mission de récupérer les données brutes depuis des sites externes (comme DofusDB) et de les rendre accessibles via une interface unifiée. Ce service ne fait **aucune conversion** ni **intégration** - il se contente de récupérer et d'exposer les données dans leur format d'origine.

## 📋 Fonctionnalités principales

### 🔍 **Récupération de données**
- **Breeds** : Classes jouables (Féca, Iop, Eniripsa, etc.)
- **Monstres** : Créatures, boss, invocations, etc.
- **Items** : Objets multi-types (équipements, ressources, consommables) via filtrage
- **Sorts** : Magies, sorts de classe avec niveaux d'évolution
- **Effets** : Descriptions des effets pour items, sorts, etc.
- **Panoplies** : Ensembles d'items avec bonus (détection automatique)

### 🔧 **Interface d'accès**
- **API REST** : Endpoints pour chaque type de données
- **Recherche avancée** : Filtres par niveau, catégorie, caractéristiques
- **Pagination** : Gestion des gros volumes de données
- **Cache** : Mise en cache des données pour optimiser les performances
- **Rate limiting** : Respect des limites des sites externes

### 📊 **Gestion des données**
- **Stockage temporaire** : Cache local des données récupérées
- **Métadonnées** : Informations sur la source, la date de récupération
- **Validation** : Vérification de l'intégrité des données reçues
- **Logs** : Traçabilité des opérations de récupération

## 🏗️ Architecture

### **Composants principaux**

```
Service Data-collect
├── DataRetrievalService     # Service principal de récupération
├── ExternalSiteScraper      # Scraping des sites externes
├── DataCacheService        # Gestion du cache local
├── SearchService           # Service de recherche et filtrage
├── RateLimitService        # Gestion des limites de requêtes
└── ValidationService       # Validation des données reçues
```

### **Flux de données**

```
Site Externe (ex: DofusDB)
    ↓ (Scraping/API)
DataRetrievalService
    ↓ (Validation)
ValidationService
    ↓ (Cache)
DataCacheService
    ↓ (Interface)
API Endpoints
    ↓
Client Applications
```

## 🔌 Interface API

### **Endpoints principaux**

#### **Breeds (Classes)**
```
GET /api/data-collect/breeds
GET /api/data-collect/breeds/{id}
GET /api/data-collect/breeds/{id}/spells
```

#### **Monstres**
```
GET /api/data-collect/monsters
GET /api/data-collect/monsters/{id}
GET /api/data-collect/monsters/search?level=100&race=bouftou
```

#### **Items (Multi-types)**
```
GET /api/data-collect/items
GET /api/data-collect/items/{id}
GET /api/data-collect/items/by-type/{type}
GET /api/data-collect/items/by-category/{category}
GET /api/data-collect/items/search?level=50&category=weapon
```

#### **Sorts**
```
GET /api/data-collect/spells
GET /api/data-collect/spells/{id}
GET /api/data-collect/spells/{id}/levels
GET /api/data-collect/spells/search?class=feca&level=50
```

#### **Effets**
```
GET /api/data-collect/effects
GET /api/data-collect/effects/{id}
GET /api/data-collect/effects/by-entity/{entity_type}/{entity_id}
```

### **Paramètres de recherche**

- **Niveau** : `level=50` ou `level_min=40&level_max=60`
- **Catégorie** : `category=weapon`, `category=armor`
- **Rareté** : `rarity=legendary`
- **Caractéristiques** : `stats=strength&stats_min=10`
- **Pagination** : `page=1&per_page=20`

## ⚙️ Configuration

### **Configuration du service**

```php
// Configuration du service Data-collect
'data-collect' => [
    'external_sites' => [
        'dofusdb' => [
            'url' => env('DATA_COLLECT_DOFUSDB_URL', 'https://dofusdb.fr'),
            'rate_limit' => env('DATA_COLLECT_RATE_LIMIT', 60),
            'timeout' => env('DATA_COLLECT_TIMEOUT', 30),
            'cache_ttl' => env('DATA_COLLECT_CACHE_TTL', 3600),
            'retry_attempts' => env('DATA_COLLECT_RETRY_ATTEMPTS', 3),
            'retry_delay' => env('DATA_COLLECT_RETRY_DELAY', 5),
        ],
        // Autres sites externes peuvent être ajoutés ici
    ],
    'default_timeout' => env('DATA_COLLECT_DEFAULT_TIMEOUT', 30),
    'default_cache_ttl' => env('DATA_COLLECT_DEFAULT_CACHE_TTL', 3600),
],
```

### **Variables d'environnement**

```bash
# Configuration Data-collect
DATA_COLLECT_DOFUSDB_URL=https://dofusdb.fr
DATA_COLLECT_RATE_LIMIT=60
DATA_COLLECT_TIMEOUT=30
DATA_COLLECT_CACHE_TTL=3600
DATA_COLLECT_RETRY_ATTEMPTS=3
DATA_COLLECT_RETRY_DELAY=5
```

## 💻 Utilisation

### **Exemple d'utilisation basique**

```php
use App\Services\DataCollectService;

class ExampleController extends Controller
{
    public function example(DataCollectService $dataCollectService)
    {
        // Récupération d'un objet spécifique
        $item = $dataCollectService->getItem(12345);

// Recherche d'objets avec filtres
        $weapons = $dataCollectService->searchItems([
    'category' => 'weapon',
    'level_min' => 50,
    'level_max' => 100,
]);

// Récupération des sorts d'une classe
        $spells = $dataCollectService->getBreedSpells('feca');
        
        // Récupération d'items par type
        $weapons = $dataCollectService->getItemsByType('weapon');
        $resources = $dataCollectService->getItemsByType('resource');
        $consumables = $dataCollectService->getItemsByType('consumable');
        
        return response()->json([
            'item' => $item,
            'weapons' => $weapons,
            'resources' => $resources,
            'consumables' => $consumables,
            'spells' => $spells,
        ]);
    }
}
```

## 🔗 Intégration avec les autres services

### **Flux de données**

```
Service Data-collect
    ↓ (Données brutes)
Service Data-conversion
    ↓ (Données converties)
Service Data-integration
    ↓ (Données intégrées)
Base de données KrosmozJDR
```

### **Utilisation par les autres services**

- **Service de conversion** : Alimentation du service de conversion
- **Service d'intégration** : Fourniture des données brutes
- **Applications externes** : Accès direct aux données collectées

## 📝 Notes importantes

### **Responsabilités du service**

- **Récupération** : Collecte des données depuis les sites externes
- **Cache** : Stockage temporaire des données
- **API** : Exposition des données via endpoints REST
- **Pas de conversion** : Les données restent au format d'origine
- **Pas d'intégration** : Pas de sauvegarde en base KrosmozJDR

### **Limitations**

- **Données brutes** : Aucune transformation des données
- **Cache temporaire** : Les données ne sont pas persistantes
- **Dépendance externe** : Nécessite que les sites externes soient accessibles

## 🚀 Développement

### **Ajout d'un nouveau site externe**

1. **Configuration** : Ajouter la configuration dans `config/data-collect.php`
2. **Scraper** : Créer un nouveau scraper dans `ExternalSiteScraper`
3. **Tests** : Ajouter les tests unitaires et d'intégration
4. **Documentation** : Mettre à jour la documentation API

### **Tests**

```bash
# Tests unitaires
php artisan test --filter=DataCollectServiceTest

# Tests d'intégration
php artisan test --filter=DataCollectIntegrationTest
```

---

*Service développé pour le projet KrosmozJDR - Collecte automatique de données externes*
