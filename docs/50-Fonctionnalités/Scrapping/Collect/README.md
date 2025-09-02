# Service Collect DofusDB

## 🎯 Objectif

Le service **Collect** a pour mission de récupérer les données brutes depuis le site [DofusDB](https://dofusdb.fr/fr/database/objects) et de les rendre accessibles via une interface unifiée. Ce service ne fait **aucune conversion** ni **intégration** - il se contente de récupérer et d'exposer les données dans leur format d'origine.

## 📋 Fonctionnalités principales

### 🔍 **Récupération de données**
- **Objets/Équipements** : Armes, armures, anneaux, ceintures, bottes, etc.
- **Monstres** : Créatures, boss, invocations, etc.
- **Sorts** : Magies, sorts de classe, sorts universels, etc.
- **Classes (Breeds)** : Féca, Iop, Eniripsa, etc.
- **Panoplies** : Ensembles d'objets avec bonus
- **Quêtes** : Données des quêtes et objectifs
- **Donjons** : Informations sur les donjons
- **Ressources** : Matériaux, ingrédients, etc.

### 🔧 **Interface d'accès**
- **API REST** : Endpoints pour chaque type de données
- **Recherche avancée** : Filtres par niveau, catégorie, caractéristiques
- **Pagination** : Gestion des gros volumes de données
- **Cache** : Mise en cache des données pour optimiser les performances
- **Rate limiting** : Respect des limites de DofusDB

### 📊 **Gestion des données**
- **Stockage temporaire** : Cache local des données récupérées
- **Métadonnées** : Informations sur la source, la date de récupération
- **Validation** : Vérification de l'intégrité des données reçues
- **Logs** : Traçabilité des opérations de récupération

## 🏗️ Architecture

### **Composants principaux**

```
Service Collect
├── DataRetrievalService     # Service principal de récupération
├── DofusDBScraper          # Scraping des pages DofusDB
├── DataCacheService        # Gestion du cache local
├── SearchService           # Service de recherche et filtrage
├── RateLimitService        # Gestion des limites de requêtes
└── ValidationService       # Validation des données reçues
```

### **Flux de données**

```
DofusDB Website
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

#### **Objets**
```
GET /api/collect/items
GET /api/collect/items/{id}
GET /api/collect/items/search?level=50&category=weapon
```

#### **Monstres**
```
GET /api/collect/monsters
GET /api/collect/monsters/{id}
GET /api/collect/monsters/search?level=100&race=bouftou
```

#### **Sorts**
```
GET /api/collect/spells
GET /api/collect/spells/{id}
GET /api/collect/spells/search?class=feca&level=50
```

#### **Classes**
```
GET /api/collect/breeds
GET /api/collect/breeds/{id}
GET /api/collect/breeds/{id}/spells
```

### **Paramètres de recherche**

- **Niveau** : `level=50` ou `level_min=40&level_max=60`
- **Catégorie** : `category=weapon`, `category=armor`
- **Rareté** : `rarity=legendary`
- **Caractéristiques** : `stats=strength&stats_min=10`
- **Pagination** : `page=1&per_page=20`

## 📈 Gestion des performances

### **Cache**
- **Cache en mémoire** : Données fréquemment consultées
- **Cache fichier** : Données volumineuses
- **TTL configurable** : Durée de vie du cache
- **Invalidation intelligente** : Mise à jour automatique

### **Rate Limiting**
- **Limites par minute** : Respect des contraintes DofusDB
- **Queue de requêtes** : Gestion des pics de charge
- **Retry automatique** : En cas d'échec temporaire
- **Backoff exponentiel** : Stratégie de retry intelligente

### **Optimisations**
- **Requêtes en lot** : Récupération groupée de données
- **Compression** : Réduction de la bande passante
- **Lazy loading** : Chargement à la demande
- **Indexation** : Recherche rapide dans le cache

## 🔒 Sécurité et robustesse

### **Gestion d'erreurs**
- **Timeouts** : Limitation des temps de réponse
- **Fallbacks** : Données de secours en cas d'indisponibilité
- **Monitoring** : Surveillance des performances
- **Alertes** : Notification en cas de problème

### **Validation**
- **Structure des données** : Vérification du format
- **Contenu** : Validation des valeurs
- **Cohérence** : Vérification des relations
- **Sanitisation** : Nettoyage des données

### **Logs et monitoring**
- **Logs détaillés** : Traçabilité complète
- **Métriques** : Performance et utilisation
- **Alertes** : Notification des anomalies
- **Dashboard** : Interface de monitoring

## 🛠️ Configuration

### **Paramètres principaux**

```php
// Configuration du service Data-collect
'data-collect' => [
    'dofusdb_url' => 'https://dofusdb.fr',
    'rate_limit' => 60, // requêtes par minute
    'timeout' => 30, // secondes
    'cache_ttl' => 3600, // secondes
    'retry_attempts' => 3,
    'retry_delay' => 5, // secondes
],
```

### **Variables d'environnement**

```env
# Configuration Data-collect
DATA-COLLECT_DOFUSDB_URL=https://dofusdb.fr
DATA-COLLECT_RATE_LIMIT=60
DATA-COLLECT_TIMEOUT=30
DATA-COLLECT_CACHE_TTL=3600
DATA-COLLECT_RETRY_ATTEMPTS=3
DATA-COLLECT_RETRY_DELAY=5
```

## 📚 Utilisation

### **Exemple d'utilisation**

```php
// Récupération d'un objet par ID
$item = $data-collectService->getItem(12345);

// Recherche d'objets avec filtres
$weapons = $data-collectService->searchItems([
    'category' => 'weapon',
    'level_min' => 50,
    'level_max' => 100,
    'rarity' => 'legendary'
]);

// Récupération des sorts d'une classe
$spells = $data-collectService->getBreedSpells('feca');
```

### **Interface CLI**

```bash
# Récupération complète des données
php artisan scrapping:fetch --type=items

# Recherche d'objets
php artisan scrapping:search --type=items --level=50 --category=weapon

# Mise à jour du cache
php artisan scrapping:cache:clear
php artisan scrapping:cache:warm
```

## 🔄 Évolution

### **Fonctionnalités futures**
- **Webhooks** : Notification des mises à jour
- **Streaming** : Flux de données en temps réel
- **Synchronisation** : Mise à jour automatique
- **API GraphQL** : Interface de requête avancée
- **Export** : Export des données en différents formats

### **Intégrations**
- **Service de conversion** : Alimentation du service de conversion
- **Service d'intégration** : Données pour l'intégration en base
- **Interface utilisateur** : Affichage des données brutes
- **Outils de développement** : Debug et analyse

## 📝 Notes importantes

### **Limitations**
- **Pas de conversion** : Les données restent au format DofusDB
- **Dépendance externe** : Service dépendant de DofusDB
- **Rate limiting** : Contraintes de vitesse de récupération
- **Format variable** : Structure des données peut évoluer

### **Responsabilités**
- **Récupération** : Seule responsabilité du service
- **Cache** : Optimisation des performances
- **Interface** : Exposition des données
- **Robustesse** : Gestion des erreurs et indisponibilités

---

**Note** : Ce service est conçu pour être **indépendant** et **réutilisable**. Il ne fait aucune transformation des données et peut être utilisé par d'autres services du projet.
