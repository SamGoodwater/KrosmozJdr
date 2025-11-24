# Cahier des Charges - Orchestrateur de Scrapping

## 📋 Présentation

L'Orchestrateur de Scrapping est un composant central de l'architecture KrosmozJDR qui coordonne l'ensemble du processus de récupération, conversion et intégration des données depuis des sites externes (comme DofusDB) vers la base de données KrosmozJDR. Il agit comme un chef d'orchestre qui assure la coordination entre les services Data-collect, Data-conversion et Data-integration.

## 🎯 Objectifs

### **Objectifs principaux**
- **Coordination centralisée** : Orchestrer l'appel des services dans le bon ordre
- **Interface unifiée** : Fournir une API simple et cohérente pour le reste du projet
- **Gestion des erreurs** : Coordonner la gestion des erreurs entre services
- **Monitoring global** : Suivre l'état global du processus de scrapping
- **Performance optimisée** : Gérer la concurrence et l'utilisation des ressources

### **Objectifs secondaires**
- **Traçabilité complète** : Assurer la traçabilité de tous les processus
- **Reprise après erreur** : Gérer automatiquement la reprise des processus interrompus
- **Scalabilité** : Permettre le traitement parallèle de plusieurs entités
- **Maintenabilité** : Faciliter la maintenance et l'évolution du système

## 🔧 Fonctionnalités détaillées

### 1. Gestion des processus d'import

#### 1.1 Import d'entités individuelles
- **Import d'une classe** : Récupération complète d'une classe depuis DofusDB
- **Import d'un monstre** : Récupération d'un monstre avec ses caractéristiques
- **Import d'un objet** : Récupération d'un objet selon son type (équipement, ressource, consommable)
- **Import d'un sort** : Récupération d'un sort avec fusion de ses niveaux d'évolution
- **Import d'un effet** : Récupération d'une description d'effet

#### 1.2 Import en lot
- **Import de plusieurs entités** : Traitement en parallèle de plusieurs entités
- **Import par catégorie** : Import de toutes les classes, tous les monstres, etc.
- **Import complet** : Import de l'ensemble des données DofusDB
- **Import incrémental** : Import uniquement des données modifiées depuis la dernière importation

#### 1.3 Gestion des processus
- **Suivi de progression** : Monitoring en temps réel de l'avancement
- **Gestion des erreurs** : Retry automatique et fallback en cas d'échec
- **Rollback** : Annulation des opérations en cas de problème
- **Reprise après erreur** : Reprise automatique des processus interrompus
- **Pause/Reprise** : Possibilité de mettre en pause et reprendre un processus

### 2. Coordination des services

#### 2.1 Orchestration du flux de données
- **Validation de la demande** : Vérification des paramètres d'entrée
- **Appel de Data-integration** : Traduction de la demande KrosmozJDR → DofusDB
- **Coordination Data-collect** : Récupération des données depuis DofusDB
- **Coordination Data-conversion** : Conversion des valeurs selon les caractéristiques KrosmozJDR
- **Finalisation Data-integration** : Sauvegarde des données converties en base
- **Retour du résultat** : Résumé de l'opération avec métriques

#### 2.2 Gestion des dépendances
- **Ordre d'exécution** : Respect de l'ordre des étapes (validation → traduction → collection → restructuration → conversion → intégration → sauvegarde)
- **Gestion des erreurs en cascade** : Propagation et gestion des erreurs entre services
- **Rollback automatique** : Annulation des opérations en cas d'échec d'une étape
- **Reprise intelligente** : Reprise à partir de la dernière étape réussie

### 3. Monitoring et observabilité

#### 3.1 Suivi de progression
- **Barre de progression** : Pourcentage d'avancement des imports
- **Temps estimé** : Estimation du temps restant basée sur les performances historiques
- **Entités traitées** : Nombre d'entités importées avec succès
- **Erreurs détaillées** : Détail des erreurs rencontrées avec contexte

#### 3.2 Métriques de performance
- **Temps d'import** : Durée totale des processus d'import
- **Débit** : Nombre d'entités importées par minute
- **Utilisation des ressources** : CPU, mémoire, réseau
- **Taux de réussite** : Pourcentage d'imports réussis
- **Bottlenecks** : Identification des étapes les plus lentes

#### 3.3 Logs centralisés
- **Logs structurés** : Format JSON avec métadonnées
- **Corrélation des logs** : Identifiants de corrélation pour tracer les opérations
- **Rotation automatique** : Gestion automatique de la rotation des logs
- **Niveaux de log** : Différents niveaux selon l'environnement (debug, info, warning, error)

## 🏗️ Architecture technique

### **Composants principaux**

#### 1.1 Service d'orchestration principal
```php
class ScrappingOrchestrator
{
    public function importClass(int $dofusdbId, array $options = []): ImportResult;
    public function importMonster(int $dofusdbId, array $options = []): ImportResult;
    public function importItem(int $dofusdbId, array $options = []): ImportResult;
    public function importSpell(int $dofusdbId, array $options = []): ImportResult;
    public function importBatch(array $entities, array $options = []): BatchImportResult;
    public function importCategory(string $category, array $options = []): CategoryImportResult;
}
```

#### 1.2 Gestionnaire de processus
```php
class ProcessManager
{
    public function createProcess(string $type, array $parameters): Process;
    public function startProcess(Process $process): void;
    public function pauseProcess(Process $process): void;
    public function resumeProcess(Process $process): void;
    public function cancelProcess(Process $process): void;
    public function getProcessStatus(string $processId): ProcessStatus;
}
```

#### 1.3 Gestionnaire d'erreurs
```php
class ErrorHandler
{
    public function handleError(ProcessError $error): ErrorResolution;
    public function shouldRetry(ProcessError $error): bool;
    public function getRetryStrategy(ProcessError $error): RetryStrategy;
    public function applyFallback(ProcessError $error): mixed;
}
```

#### 1.4 Suiveur de progression
```php
class ProgressTracker
{
    public function updateProgress(string $processId, float $progress): void;
    public function updateStep(string $processId, string $step, array $metadata): void;
    public function getProgress(string $processId): Progress;
    public function estimateCompletion(string $processId): DateTime;
}
```

#### 1.5 Traiteur de lots
```php
class BatchProcessor
{
    public function processBatch(array $entities, array $options): BatchResult;
    public function splitIntoBatches(array $entities, int $batchSize): array;
    public function processBatchParallel(array $batches, int $maxConcurrent): array;
    public function aggregateResults(array $batchResults): BatchResult;
}
```

#### 1.6 Agrégateur de résultats
```php
class ResultAggregator
{
    public function aggregateResults(array $results): AggregatedResult;
    public function calculateMetrics(array $results): Metrics;
    public function generateReport(AggregatedResult $result): Report;
    public function storeResults(AggregatedResult $result): void;
}
```

### **Interfaces et contrats**

#### 2.1 Interface d'orchestration
```php
interface ScrappingOrchestratorInterface
{
    public function importEntity(string $entityType, int $dofusdbId, array $options = []): ImportResult;
    public function importBatch(array $entities, array $options = []): BatchImportResult;
    public function getProcessStatus(string $processId): ProcessStatus;
    public function cancelProcess(string $processId): bool;
}
```

#### 2.2 Interface de processus
```php
interface ProcessInterface
{
    public function getId(): string;
    public function getType(): string;
    public function getStatus(): ProcessStatus;
    public function getProgress(): float;
    public function getCurrentStep(): string;
    public function getStartedAt(): DateTime;
    public function getEstimatedCompletion(): DateTime;
}
```

## 🔌 Interface API

### **Endpoints principaux**

#### 3.1 Import d'entités individuelles
```http
POST /api/scrapping/import/class/{dofusdb_id}
POST /api/scrapping/import/monster/{dofusdb_id}
POST /api/scrapping/import/item/{dofusdb_id}
POST /api/scrapping/import/spell/{dofusdb_id}
POST /api/scrapping/import/effect/{dofusdb_id}
```

#### 3.2 Import en lot
```http
POST /api/scrapping/import/batch
POST /api/scrapping/import/classes
POST /api/scrapping/import/monsters
POST /api/scrapping/import/items
POST /api/scrapping/import/spells
POST /api/scrapping/import/effects
```

#### 3.3 Gestion des processus
```http
GET /api/scrapping/status/{process_id}
GET /api/scrapping/progress/{process_id}
POST /api/scrapping/pause/{process_id}
POST /api/scrapping/resume/{process_id}
POST /api/scrapping/cancel/{process_id}
GET /api/scrapping/history
GET /api/scrapping/metrics
```

### **Paramètres d'import**

#### 4.1 Import individuel
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

#### 4.2 Import en lot
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
    "stop_on_error": false,
    "batch_size": 10,
    "priority": "normal"
  }
}
```

#### 4.3 Import par catégorie
```json
{
  "category": "classes",
  "options": {
    "batch_size": 20,
    "max_concurrent": 3,
    "include_relations": true,
    "force_refresh": false,
    "priority": "low"
  }
}
```

## ⚙️ Configuration et paramétrage

### **Fichiers de configuration**

#### 5.1 Configuration principale
```php
// config/scrapping-orchestrator.php
return [
    'max_concurrent_processes' => env('SCRAPPING_MAX_CONCURRENT', 3),
    'process_timeout' => env('SCRAPPING_PROCESS_TIMEOUT', 3600),
    'retry_attempts' => env('SCRAPPING_RETRY_ATTEMPTS', 3),
    'retry_delay' => env('SCRAPPING_RETRY_DELAY', 60),
    'enable_parallel_processing' => env('SCRAPPING_PARALLEL', true),
    'max_memory_usage' => env('SCRAPPING_MAX_MEMORY', 1024),
    'default_priority' => env('SCRAPPING_DEFAULT_PRIORITY', 'normal'),
    'enable_notifications' => env('SCRAPPING_NOTIFICATIONS', true),
];
```

#### 5.2 Variables d'environnement
```bash
# Configuration de l'orchestrateur
SCRAPPING_MAX_CONCURRENT=3
SCRAPPING_PROCESS_TIMEOUT=3600
SCRAPPING_RETRY_ATTEMPTS=3
SCRAPPING_RETRY_DELAY=60
SCRAPPING_PARALLEL=true
SCRAPPING_MAX_MEMORY=1024
SCRAPPING_DEFAULT_PRIORITY=normal
SCRAPPING_NOTIFICATIONS=true

# Configuration des timeouts par type
SCRAPPING_INDIVIDUAL_TIMEOUT=1800
SCRAPPING_BATCH_TIMEOUT=7200
SCRAPPING_CATEGORY_TIMEOUT=14400

# Configuration des ressources
SCRAPPING_MEMORY_PER_PROCESS=512
SCRAPPING_CPU_PER_PROCESS=50
SCRAPPING_MAX_NETWORK_CONNECTIONS=10
```

### **Configuration des processus**

#### 5.3 Configuration des timeouts
```php
'process_timeouts' => [
    'individual_import' => [
        'total_timeout' => env('SCRAPPING_INDIVIDUAL_TIMEOUT', 1800),
        'step_timeout' => 300,
        'collection_timeout' => 600,
        'conversion_timeout' => 300,
        'integration_timeout' => 600
    ],
    'batch_import' => [
        'total_timeout' => env('SCRAPPING_BATCH_TIMEOUT', 7200),
        'entity_timeout' => 600,
        'batch_timeout' => 1800
    ],
    'category_import' => [
        'total_timeout' => env('SCRAPPING_CATEGORY_TIMEOUT', 14400),
        'batch_timeout' => 3600,
        'entity_timeout' => 300
    ]
];
```

#### 5.4 Configuration de la concurrence
```php
'concurrency_settings' => [
    'max_concurrent_processes' => env('SCRAPPING_MAX_CONCURRENT', 3),
    'max_concurrent_entities' => 5,
    'max_concurrent_batches' => 2,
    'resource_limits' => [
        'memory_per_process' => env('SCRAPPING_MEMORY_PER_PROCESS', 512),
        'cpu_per_process' => env('SCRAPPING_CPU_PER_PROCESS', 50),
        'network_connections' => env('SCRAPPING_MAX_NETWORK_CONNECTIONS', 10)
    ]
];
```

## 🔒 Sécurité et robustesse

### **Validation des entrées**

#### 6.1 Validation des paramètres
- **Validation des IDs** : Vérification du format et de la validité des IDs DofusDB
- **Validation des options** : Vérification de la cohérence des options d'import
- **Validation des priorités** : Vérification des niveaux de priorité autorisés
- **Validation des timeouts** : Vérification des limites de timeout

#### 6.2 Protection contre les abus
- **Rate limiting** : Limitation du nombre de processus simultanés par utilisateur
- **Quotas** : Limitation du nombre total de processus par période
- **Validation des permissions** : Vérification des droits d'accès aux fonctionnalités
- **Audit des opérations** : Enregistrement de toutes les opérations d'import

### **Gestion des erreurs**

#### 6.3 Stratégies de récupération
- **Retry automatique** : Tentatives de reconnexion automatiques avec backoff exponentiel
- **Fallbacks intelligents** : Utilisation de valeurs par défaut en cas d'échec
- **Rollback automatique** : Annulation des opérations en cas d'erreur critique
- **Reprise après erreur** : Reprise automatique des processus interrompus

#### 6.4 Gestion des timeouts
- **Timeouts configurables** : Timeouts différents selon le type d'import
- **Détection des blocages** : Détection automatique des processus bloqués
- **Nettoyage automatique** : Nettoyage automatique des processus expirés
- **Notification des timeouts** : Notification automatique en cas de timeout

## 📊 Métriques et monitoring

### **Métriques de performance**

#### 7.1 Métriques temporelles
- **Temps total d'import** : Durée totale des processus d'import
- **Temps par étape** : Durée de chaque étape du processus
- **Temps par type d'entité** : Durée moyenne par type d'entité
- **Temps par priorité** : Durée moyenne selon le niveau de priorité

#### 7.2 Métriques de ressources
- **Utilisation mémoire** : Pic et moyenne d'utilisation mémoire
- **Utilisation CPU** : Pic et moyenne d'utilisation CPU
- **Requêtes réseau** : Nombre et volume des requêtes réseau
- **Connexions base de données** : Nombre de connexions simultanées

#### 7.3 Métriques de qualité
- **Taux de réussite** : Pourcentage d'imports réussis
- **Taux d'erreur** : Pourcentage d'imports échoués
- **Types d'erreurs** : Distribution des erreurs par type
- **Entités traitées** : Nombre total d'entités traitées

### **Monitoring et alertes**

#### 7.4 Surveillance en temps réel
- **Dashboard de monitoring** : Interface de surveillance des performances
- **Alertes automatiques** : Notification en cas de dépassement de seuils
- **Seuils configurables** : Seuils d'alerte personnalisables
- **Rapports automatiques** : Génération automatique de rapports de performance

#### 7.5 Seuils d'alerte
```php
'alert_thresholds' => [
    'process_timeout' => env('SCRAPPING_ALERT_TIMEOUT', 3600),
    'memory_limit' => env('SCRAPPING_ALERT_MEMORY', 1024),
    'cpu_limit' => env('SCRAPPING_ALERT_CPU', 90),
    'error_rate_threshold' => env('SCRAPPING_ALERT_ERROR_RATE', 0.05),
    'success_rate_minimum' => env('SCRAPPING_ALERT_SUCCESS_RATE', 0.95),
    'step_duration_max' => env('SCRAPPING_ALERT_STEP_DURATION', 300),
];
```

## 🧪 Tests et validation

### **Tests unitaires**

#### 8.1 Couverture des tests
- **Toutes les méthodes** : Couvrir toutes les méthodes publiques
- **Cas limites** : Tester les valeurs aux bornes et cas extrêmes
- **Gestion d'erreurs** : Vérifier le traitement des erreurs
- **Performance** : Tests de charge et de stress

#### 8.2 Tests d'intégration
- **End-to-end** : Tester le flux complet d'import
- **Services externes** : Validation de l'intégration avec les services
- **Base de données** : Validation des données sauvegardées
- **API** : Tests des endpoints d'import

### **Tests de performance**

#### 8.3 Tests de charge
- **Volumes de données** : Tester avec différents volumes de données
- **Concurrence** : Tester la gestion de plusieurs processus simultanés
- **Ressources système** : Vérifier l'utilisation des ressources
- **Scalabilité** : Tester la capacité d'adaptation à la charge

#### 8.4 Tests de robustesse
- **Gestion des erreurs** : Tester la récupération après erreur
- **Timeouts** : Tester la gestion des timeouts
- **Ressources limitées** : Tester avec des ressources système limitées
- **Réseau instable** : Tester avec des conditions réseau instables

## 📚 Documentation

### **Documentation technique**

#### 9.1 Référence API
- **Documentation complète** : Tous les endpoints et paramètres
- **Exemples d'utilisation** : Cas d'usage concrets et exemples de code
- **Codes d'erreur** : Documentation des erreurs et solutions
- **Changelog** : Historique des modifications et évolutions

#### 9.2 Architecture et design
- **Schémas techniques** : Diagrammes d'architecture et de flux
- **Guide de configuration** : Instructions de configuration détaillées
- **Guide de déploiement** : Instructions de déploiement et maintenance
- **Troubleshooting** : Guide de résolution des problèmes courants

### **Documentation utilisateur**

#### 9.3 Guides d'utilisation
- **Tutoriels pratiques** : Guides pas à pas pour les cas d'usage courants
- **FAQ** : Questions fréquentes et réponses
- **Bonnes pratiques** : Recommandations d'utilisation
- **Exemples concrets** : Cas d'usage réels et solutions

---

*Cahier des charges de l'orchestrateur de scrapping - Projet KrosmozJDR*
