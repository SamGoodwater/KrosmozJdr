# Service Data-integration

## 🎯 Objectif

Le service **Data-integration** est responsable de l'intégration des données converties par le service Data-conversion dans la base de données KrosmozJDR. Il assure la cohérence des données, gère les relations entre entités et maintient l'intégrité de la base de données.

## 🔧 Fonctionnalités principales

### **Intégration des données**
- **Insertion en base** : Sauvegarde des données converties dans la base KrosmozJDR
- **Gestion des relations** : Création et maintenance des liens entre entités
- **Gestion des conflits** : Résolution des doublons et des données en conflit
- **Validation d'intégrité** : Vérification de la cohérence des données

### **Gestion des entités**
- **Breeds** : Intégration des classes jouables avec leurs spécificités
- **Monstres** : Intégration des créatures et monstres avec races et tailles
- **Items** : Intégration multi-types (équipements, ressources, consommables)
- **Sorts** : Intégration des sorts avec gestion des niveaux d'évolution
- **Effets** : Intégration des descriptions d'effets pour items et sorts
- **Panoplies** : Création automatique des ensembles d'items
- **Relations** : Maintien des liens entre toutes les entités

### **Gestion des conflits**
- **Stratégies configurables** : Ignorer, mettre à jour, remplacer ou fusionner
- **Gestion des doublons** : Détection et traitement des entrées en double
- **Rollback automatique** : Annulation des opérations en cas d'erreur
- **Sauvegarde préventive** : Backup des données avant modification

## 🏗️ Architecture

### **Composants principaux**

```
Service Data-integration
├── DataIntegrationService    # Service principal d'intégration
├── EntityManager            # Gestion des entités et relations
├── ConflictResolver         # Résolution des conflits et doublons
├── ValidationService        # Validation des données avant intégration
├── TransactionManager       # Gestion des transactions de base
└── BackupService           # Sauvegarde et restauration
```

### **Configuration**

- **Configuration du service** : `app/Services/data-integration/config.php`
- **Configuration des caractéristiques** : `config/characteristics.php`
- **Mappings d'entités** : Définis dans la configuration des caractéristiques

## 🔌 Interface API

### **Endpoints principaux**

#### **Intégration d'entités**
```
POST /api/data-integration/integrate
POST /api/data-integration/integrate/batch
POST /api/data-integration/validate
```

#### **Gestion des conflits**
```
GET /api/data-integration/conflicts
POST /api/data-integration/conflicts/resolve
DELETE /api/data-integration/conflicts/{id}
```

#### **Gestion des sauvegardes**
```
GET /api/data-integration/backups
POST /api/data-integration/backups/create
POST /api/data-integration/backups/restore/{id}
```

### **Paramètres d'intégration**

- **Type d'entité** : `entity_type=creature`, `entity_type=item`
- **Stratégie de conflit** : `conflict_strategy=update`, `conflict_strategy=ignore`
- **Mode transaction** : `use_transactions=true`
- **Validation** : `validate_before_insert=true`

## 💻 Utilisation

### **Exemple d'utilisation basique**

```php
use App\Services\DataIntegrationService;

class ExampleController extends Controller
{
    public function example(DataIntegrationService $dataIntegrationService)
    {
        // Intégration d'une entité
        $result = $dataIntegrationService->integrate([
            'entity_type' => 'breed',
            'data' => $convertedData,
            'options' => [
                'conflict_strategy' => 'update',
                'validate_before_insert' => true,
                'use_transactions' => true
            ]
        ]);
        
        // Intégration d'items par type
        $equipmentResult = $dataIntegrationService->integrateItemsByType('equipment', $equipmentData);
        $resourceResult = $dataIntegrationService->integrateItemsByType('resource', $resourceData);
        $consumableResult = $dataIntegrationService->integrateItemsByType('consumable', $consumableData);
        
        return response()->json($result);
    }
}
```

### **Intégration en lot**

```php
// Intégration complète de toutes les entités
$results = $dataIntegrationService->integrateBatch([
    'entities' => $allConvertedEntities,
    'options' => [
        'conflict_strategy' => 'update',
        'batch_size' => 100,
        'use_transactions' => true,
        'validate_before_insert' => true
    ]
]);

// Intégration avec gestion des conflits
$result = $dataIntegrationService->integrateWithConflictResolution($entity, [
    'strategy' => 'merge',
    'backup_before' => true,
    'notify_users' => true
]);
```

## 📊 Monitoring et logs

### **Logs d'intégration**
- **Niveau** : `storage/logs/data-integration.log`
- **Format** : JSON structuré avec métadonnées
- **Rotation** : Automatique avec compression

### **Métriques de performance**
- **Temps d'intégration** : Par entité et par lot
- **Taux de réussite** : Pourcentage d'intégrations réussies
- **Gestion des conflits** : Nombre et types de conflits résolus
- **Utilisation base de données** : Requêtes et transactions

## 🔧 Configuration

### **Paramètres principaux**

```php
// Configuration du service Data-integration
'data-integration' => [
    'strict_mode' => env('DATA_INTEGRATION_STRICT_MODE', false),
    'auto_validation' => env('DATA_INTEGRATION_AUTO_VALIDATION', true),
    'auto_correction' => env('DATA_INTEGRATION_AUTO_CORRECTION', true),
    'batch_size' => env('DATA_INTEGRATION_BATCH_SIZE', 100),
    'use_transactions' => env('DATA_INTEGRATION_USE_TRANSACTIONS', true),
    'conflict_strategy' => env('DATA_INTEGRATION_CONFLICT_STRATEGY', 'update'),
],
```

### **Variables d'environnement**

```bash
# Configuration Data-integration
DATA_INTEGRATION_STRICT_MODE=false
DATA_INTEGRATION_AUTO_VALIDATION=true
DATA_INTEGRATION_AUTO_CORRECTION=true
DATA_INTEGRATION_BATCH_SIZE=100
DATA_INTEGRATION_USE_TRANSACTIONS=true
DATA_INTEGRATION_CONFLICT_STRATEGY=update
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

### **Dépendances**

- **Service Data-conversion** : Fournit les données converties à intégrer
- **Configuration des caractéristiques** : Définit les règles de validation
- **Base de données KrosmozJDR** : Destination des données intégrées

## 🚀 Développement

### **Ajout de nouvelles stratégies d'intégration**

1. **Définition** : Ajouter la stratégie dans la configuration
2. **Implémentation** : Créer les classes de résolution correspondantes
3. **Tests** : Ajouter les tests unitaires et d'intégration
4. **Documentation** : Mettre à jour la documentation API

### **Tests**

```bash
# Tests unitaires
php artisan test --filter=DataIntegrationServiceTest

# Tests d'intégration
php artisan test --filter=DataIntegrationIntegrationTest
```

## 📝 Notes importantes

### **Responsabilités du service**

- **Intégration** : Sauvegarde des données en base de données
- **Gestion des conflits** : Résolution des doublons et conflits
- **Validation** : Vérification de la cohérence avant intégration
- **Sauvegarde** : Protection des données existantes

### **Limitations**

- **Dépendance aux données converties** : Nécessite des données valides du service Data-conversion
- **Gestion des conflits** : Peut être complexe selon la stratégie choisie
- **Performance** : Les intégrations en lot peuvent être coûteuses en ressources

---

*Service développé pour le projet KrosmozJDR - Intégration automatique des données converties*
