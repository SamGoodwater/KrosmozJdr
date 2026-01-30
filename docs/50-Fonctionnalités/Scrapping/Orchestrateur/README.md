# Orchestrateur de Scrapping

## 🎯 Objectif

L'**Orchestrateur de Scrapping** est le composant central qui coordonne l'ensemble du processus de récupération, conversion et intégration des données depuis des sites externes (comme DofusDB) vers KrosmozJDR. Il agit comme un chef d'orchestre qui appelle les services dans le bon ordre et gère le flux de données global.

## 🔄 Rôle dans l'architecture

### **Positionnement**
```
┌─────────────────────────────────────────────────────────────┐
│                    RESTE DU PROJET                         │
│  (Contrôleurs, Commandes, Services métier)                │
└─────────────────────┬───────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────┐
│                ORCHESTRATEUR                               │
│              (Chef d'orchestre)                            │
└─────────────────────┬───────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────┐
│  DATACOLLECT  │  DATACONVERSION  │  DATAINTEGRATION        │
│  (Récupération)│  (Conversion)      │  (Traduction)        │
└─────────────────────────────────────────────────────────────┘
```

### **Responsabilités principales**
- **Coordination** : Orchestrer l'appel des services dans le bon ordre
- **Interface unifiée** : Fournir une API simple pour le reste du projet
- **Gestion des erreurs** : Coordonner la gestion des erreurs entre services
- **Monitoring** : Suivre l'état global du processus de scrapping
- **Logging centralisé** : Centraliser les logs de tous les services

## 🔧 Fonctionnalités principales

### **Import d'entités individuelles**
- **Import d'une classe** : Récupération complète d'une classe depuis DofusDB
- **Import d'un monstre** : Récupération d'un monstre avec ses caractéristiques
- **Import d'un objet** : Récupération d'un objet selon son type
- **Import d'un sort** : Récupération d'un sort avec ses niveaux

### **Import en lot**
- **Import de plusieurs entités** : Traitement en parallèle de plusieurs entités
- **Import par catégorie** : Import de toutes les classes, tous les monstres, etc.
- **Import complet** : Import de l'ensemble des données DofusDB

### **Gestion des processus**
- **Suivi de progression** : Monitoring en temps réel de l'avancement
- **Gestion des erreurs** : Retry automatique et fallback en cas d'échec
- **Rollback** : Annulation des opérations en cas de problème
- **Reprise après erreur** : Reprise automatique des processus interrompus

## 🏗️ Architecture

### **Composants principaux**

```
Orchestrateur de Scrapping
├── ScrappingOrchestrator      # Service principal d'orchestration
├── ProcessManager            # Gestion des processus d'import
├── ErrorHandler              # Gestion centralisée des erreurs
├── ProgressTracker           # Suivi de la progression
├── BatchProcessor            # Traitement des imports en lot
└── ResultAggregator          # Agrégation des résultats
```

### **Flux de données orchestré**

```
1. Demande d'import (ex: classe ID 123)
    ↓
2. Orchestrateur → DataIntegration
    ↓
3. DataIntegration → Traduction KrosmozJDR → DofusDB
    ↓
4. DataIntegration → DataCollect
    ↓
5. DataCollect → DofusDB
    ↓
6. DofusDB → DataCollect (données brutes)
    ↓
7. DataCollect → DataIntegration
    ↓
8. DataIntegration → Restructuration KrosmozJDR
    ↓
9. DataIntegration → DataConversion (pour chaque valeur)
    ↓
10. DataConversion → Valeurs converties
    ↓
11. DataIntegration → Sauvegarde en base KrosmozJDR
    ↓
12. Orchestrateur → Résultat final
```

## 🔌 Interface API

### **Endpoints principaux**

#### **Import d'entités individuelles**
```http
POST /api/scrapping/import/class/{dofusdb_id}
POST /api/scrapping/import/monster/{dofusdb_id}
POST /api/scrapping/import/item/{dofusdb_id}
POST /api/scrapping/import/spell/{dofusdb_id}
```

#### **Import en lot**
```http
POST /api/scrapping/import/batch
POST /api/scrapping/import/classes
POST /api/scrapping/import/monsters
POST /api/scrapping/import/items
POST /api/scrapping/import/spells
```

#### **Gestion des processus**
```http
GET /api/scrapping/status/{process_id}
GET /api/scrapping/progress/{process_id}
POST /api/scrapping/cancel/{process_id}
GET /api/scrapping/history
```

### **Paramètres d'import**

#### **Import individuel**
```json
{
  "dofusdb_id": 123,
  "options": {
    "validate_before_save": true,
    "create_relations": true,
    "notify_on_completion": true,
    "priority": "high",
    "timeout": 1800
  }
}
```

#### **Import en lot**
```json
{
  "entities": [
    {"type": "class", "id": 1},
    {"type": "class", "id": 2},
    {"type": "monster", "id": 100},
    {"type": "item", "id": 500}
  ],
  "options": {
    "parallel_processing": true,
    "max_concurrent": 5,
    "stop_on_error": false
  }
}
```

## 💻 Utilisation

### **Via Contrôleur HTTP**

```php
use App\Services\Scrapping\Orchestrator\ScrappingOrchestrator;

class ScrappingController extends Controller
{
    public function importClass(Request $request)
    {
        $dofusdbId = $request->input('dofusdb_id');
        
        $result = $this->scrappingOrchestrator->importClass($dofusdbId);
        
        return response()->json($result);
    }
    
    public function importBatch(Request $request)
    {
        $entities = $request->input('entities');
        
        $result = $this->scrappingOrchestrator->importBatch($entities);
        
        return response()->json($result);
    }
}
```

### **Via Commande Artisan**

```bash
# Import d'une classe spécifique
php artisan scrapping --import=class --id=123

# Import batch (fichier JSON)
php artisan scrapping --batch=/path/to/batch.json

# Import de plusieurs IDs
php artisan scrapping --import=monster --ids=100,101,102
```

### **Via Service métier**

```php
use App\Services\Scrapping\Orchestrator\ScrappingOrchestrator;

class GameDataService
{
    public function refreshClassData(int $dofusdbId): array
    {
        return $this->scrappingOrchestrator->importClass($dofusdbId);
    }
    
    public function refreshAllClasses(): array
    {
        return $this->scrappingOrchestrator->importClasses();
    }
}
```

## 📊 Monitoring et suivi

### **Suivi de progression**
- **Barre de progression** : Pourcentage d'avancement des imports
- **Temps estimé** : Estimation du temps restant
- **Entités traitées** : Nombre d'entités importées avec succès
- **Erreurs** : Détail des erreurs rencontrées

### **Métriques de performance**
- **Temps d'import** : Durée totale des processus d'import
- **Débit** : Nombre d'entités importées par minute
- **Utilisation des ressources** : CPU, mémoire, réseau
- **Taux de réussite** : Pourcentage d'imports réussis

### **Logs centralisés**
- **Niveau** : `storage/logs/scrapping-orchestrator.log`
- **Format** : JSON structuré avec métadonnées
- **Rotation** : Automatique avec compression
- **Corrélation** : ID de processus pour tracer les opérations

## 🔧 Configuration

### **Paramètres principaux**

```php
// Configuration de l'orchestrateur
'scrapping_orchestrator' => [
    'max_concurrent_processes' => env('SCRAPPING_MAX_CONCURRENT', 3),
    'process_timeout' => env('SCRAPPING_PROCESS_TIMEOUT', 3600),
    'retry_attempts' => env('SCRAPPING_RETRY_ATTEMPTS', 3),
    'retry_delay' => env('SCRAPPING_RETRY_DELAY', 60),
    'enable_parallel_processing' => env('SCRAPPING_PARALLEL', true),
    'max_memory_usage' => env('SCRAPPING_MAX_MEMORY', 1024),
],
```

### **Variables d'environnement**

```bash
# Configuration de l'orchestrateur
SCRAPPING_MAX_CONCURRENT=3
SCRAPPING_PROCESS_TIMEOUT=3600
SCRAPPING_RETRY_ATTEMPTS=3
SCRAPPING_RETRY_DELAY=60
SCRAPPING_PARALLEL=true
SCRAPPING_MAX_MEMORY=1024
```

## 🔗 Intégration avec les autres services

### **Dépendances**

- **Service DataCollect** : Récupération des données brutes
- **Service DataConversion** : Conversion des valeurs selon les caractéristiques
- **Service DataIntegration** : Traduction de structure et intégration
- **Base de données KrosmozJDR** : Destination finale des données

### **Coordination des services**

L'orchestrateur coordonne les services selon ce schéma :

1. **Validation de la demande** : Vérification des paramètres d'entrée
2. **Appel de DataIntegration** : Traduction de la demande
3. **Coordination DataCollect** : Récupération des données
4. **Coordination DataConversion** : Conversion des valeurs
5. **Finalisation DataIntegration** : Sauvegarde en base
6. **Retour du résultat** : Résumé de l'opération

## 🚀 Développement

### **Ajout de nouveaux types d'entités**

1. **Configuration** : Ajouter le mapping dans `DataIntegration/config.php`
2. **Méthode d'import** : Créer la méthode dans `ScrappingOrchestrator`
3. **Tests** : Ajouter les tests unitaires et d'intégration
4. **Documentation** : Mettre à jour la documentation API

### **Tests**

```bash
# Tests unitaires
php artisan test --filter=ScrappingOrchestratorTest

# Tests d'intégration
php artisan test --filter=ScrappingOrchestratorIntegrationTest

# Tests de performance
php artisan test --filter=ScrappingOrchestratorPerformanceTest
```

## 📝 Notes importantes

### **Responsabilités de l'orchestrateur**

- **Coordination** : Orchestrer l'appel des services dans le bon ordre
- **Interface unifiée** : Fournir une API simple pour le reste du projet
- **Gestion des erreurs** : Coordonner la gestion des erreurs entre services
- **Monitoring** : Suivre l'état global du processus de scrapping

### **Avantages de cette architecture**

- **Séparation des responsabilités** : Chaque service a un rôle bien défini
- **Réutilisabilité** : Les services peuvent être utilisés indépendamment
- **Maintenabilité** : Architecture claire et modulaire
- **Testabilité** : Chaque composant peut être testé séparément
- **Évolutivité** : Facile d'ajouter de nouveaux types d'entités

### **Limitations**

- **Complexité** : L'orchestrateur ajoute une couche de complexité
- **Dépendances** : Nécessite que tous les services soient opérationnels
- **Performance** : Overhead de coordination entre services

---

*Orchestrateur développé pour le projet KrosmozJDR - Coordination centralisée du processus de scrapping*
