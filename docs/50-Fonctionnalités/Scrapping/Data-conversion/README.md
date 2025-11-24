# Service Data-conversion Dofus vers KrosmozJDR

## 🎯 Objectif

Le service **Data-conversion** est responsable de la transformation des données issues du jeu Dofus (récupérées via le service Data-collect) en données exploitables par le système KrosmozJDR. Il assure la cohérence et la validité des données converties selon les règles définies dans la configuration des caractéristiques.

## 🔧 Fonctionnalités principales

### **Conversion des données**
- **Conversion automatique** : Transformer les données de Dofus en données KrosmozJDR sans intervention manuelle
- **Préservation de l'intégrité** : Maintenir la cohérence des données lors de la conversion
- **Validation automatique** : Vérifier que les données converties respectent les contraintes KrosmozJDR

### **Conversion des entités**
- **Breeds** : Conversion des classes jouables (Féca, Iop, Eniripsa, etc.)
- **Monstres** : Conversion des créatures et monstres avec races et tailles
- **Items** : Conversion multi-types (équipements, ressources, consommables) via filtrage
- **Sorts** : Conversion des sorts avec fusion des niveaux d'évolution
- **Effets** : Conversion des descriptions d'effets pour items et sorts
- **Panoplies** : Détection et création automatique des ensembles d'items

### **Gestion des caractéristiques**
- **Validation des limites** : Respect des seuils min/max définis
- **Application des formules** : Calcul automatique selon le contexte
- **Gestion des erreurs** : Traitement des cas de conversion impossible

## 🏗️ Architecture

### **Composants principaux**

```
Service Data-conversion
├── DofusDBOrchestrator     # Orchestration du processus de conversion
├── DataConverterService    # Conversion des données selon les mappings
├── DataIntegrationService  # Intégration dans la base de données KrosmozJDR
├── ValidationService       # Validation des données converties
└── ErrorHandler           # Gestion des erreurs de conversion
```

### **Configuration**

- **Configuration des entités** : `app/Services/data-conversion/config.php`
- **Règles de conversion** : `docs/50-Fonctionnalités/Scrapping/Data-conversion/DEFINITIONS.md`
- **Configuration des caractéristiques** : `config/characteristics.php`

## 🔌 Interface API

### **Endpoints principaux**

#### **Conversion d'entités**
```
POST /api/data-conversion/convert
POST /api/data-conversion/convert/batch
POST /api/data-conversion/validate
```

#### **Gestion des caractéristiques**
```
GET /api/data-conversion/characteristics
GET /api/data-conversion/characteristics/{id}
POST /api/data-conversion/characteristics/validate
```

### **Paramètres de conversion**

- **Type d'entité** : `entity_type=creature`, `entity_type=item`
- **Niveau** : `level=50`
- **Contexte** : `context=player`, `context=npc`, `context=monster`
- **Mode strict** : `strict_mode=true` (rejeter les erreurs vs. utiliser des valeurs par défaut)

## 💻 Utilisation

### **Exemple d'utilisation basique**

```php
use App\Services\DataConversionService;

class ExampleController extends Controller
{
    public function example(DataConversionService $dataConversionService)
    {
        // Conversion d'une entité
        $result = $dataConversionService->convert([
            'entity_type' => 'breed',
            'level' => 50,
            'context' => 'player',
            'data' => $rawData
        ]);
        
        // Conversion d'items par type
        $equipmentResult = $dataConversionService->convertItemsByType('equipment', $equipmentData);
        $resourceResult = $dataConversionService->convertItemsByType('resource', $resourceData);
        $consumableResult = $dataConversionService->convertItemsByType('consumable', $consumableData);
        
        return response()->json($result);
    }
}
```

### **Conversion en lot**

```php
// Conversion complète de toutes les entités
$results = $dataConversionService->convertBatch([
    'entities' => $allEntities,
    'options' => [
        'strict_mode' => false,
        'auto_correction' => true,
        'generate_reports' => true
    ]
]);

// Conversion d'une entité spécifique
$result = $dataConversionService->convertEntity($entity, [
    'level' => 100,
    'context' => 'monster'
]);

// Conversion avec options
$result = $dataConversionService->convertWithOptions($data, [
    'validation' => true,
    'correction' => true,
    'reporting' => true
]);
```

## 📊 Monitoring et logs

### **Logs de conversion**
- **Niveau** : `storage/logs/scrapping-conversion.log`
- **Format** : JSON structuré avec métadonnées
- **Rotation** : Automatique avec compression

### **Métriques de performance**
- **Temps de conversion** : Par entité et par lot
- **Taux de réussite** : Pourcentage de conversions réussies
- **Erreurs** : Détail des échecs de conversion
- **Utilisation mémoire** : Consommation des ressources

## 🔧 Configuration

### **Paramètres principaux**

```php
// Configuration du service Data-conversion
'data-conversion' => [
    'strict_mode' => env('DATA_CONVERSION_STRICT_MODE', false),
    'auto_validation' => env('DATA_CONVERSION_AUTO_VALIDATION', true),
    'auto_correction' => env('DATA_CONVERSION_AUTO_CORRECTION', true),
    'batch_size' => env('DATA_CONVERSION_BATCH_SIZE', 100),
    'memory_limit' => env('DATA_CONVERSION_MEMORY_LIMIT', 512),
    'timeout' => env('DATA_CONVERSION_TIMEOUT', 300),
],
```

### **Variables d'environnement**

```bash
# Configuration Data-conversion
DATA_CONVERSION_STRICT_MODE=false
DATA_CONVERSION_AUTO_VALIDATION=true
DATA_CONVERSION_AUTO_CORRECTION=true
DATA_CONVERSION_BATCH_SIZE=100
DATA_CONVERSION_MEMORY_LIMIT=512
DATA_CONVERSION_TIMEOUT=300
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

- **Service Data-collect** : Fournit les données brutes à convertir
- **Configuration des caractéristiques** : Définit les règles de conversion
- **Service Data-integration** : Reçoit les données converties

## 🚀 Développement

### **Ajout de nouvelles règles de conversion**

1. **Définition** : Ajouter les règles dans `DEFINITIONS.md`
2. **Implémentation** : Créer les classes de conversion correspondantes
3. **Tests** : Ajouter les tests unitaires et d'intégration
4. **Documentation** : Mettre à jour la documentation API

### **Tests**

```bash
# Tests unitaires
php artisan test --filter=DataConversionServiceTest

# Tests d'intégration
php artisan test --filter=DataConversionIntegrationTest
```

## 📝 Notes importantes

### **Responsabilités du service**

- **Conversion** : Transformation des données selon les règles définies
- **Validation** : Vérification de la cohérence des données converties
- **Gestion d'erreurs** : Traitement des cas de conversion impossible
- **Performance** : Optimisation des conversions en lot

### **Limitations**

- **Dépendance aux règles** : Nécessite une configuration complète des caractéristiques
- **Validation stricte** : Peut rejeter des données si le mode strict est activé
- **Performance** : Les conversions en lot peuvent être coûteuses en ressources

---

*Service développé pour le projet KrosmozJDR - Conversion automatique des données Dofus*
