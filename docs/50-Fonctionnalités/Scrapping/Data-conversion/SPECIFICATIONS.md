# Cahier des Charges - Service Data-conversion Dofus vers KrosmozJDR

## 📋 Présentation

Le service Data-conversion Dofus vers KrosmozJDR est un composant central de l'architecture KrosmozJDR qui transforme les données brutes de Dofus en données exploitables par le système KrosmozJDR. Il assure la cohérence et la validité des données converties selon les règles définies dans la configuration des caractéristiques.

## 🎯 Objectifs

### **Objectifs principaux**
- **Responsabilité** : Conversion, validation, cadrage des valeurs, gestion des erreurs
- **Indépendance** : Service autonome utilisable dans d'autres contextes
- **Performance** : Traitement efficace des gros volumes de données
- **Fiabilité** : Gestion robuste des erreurs et cas limites

## 🔧 Fonctionnalités détaillées

### 1. Conversion de données

#### 1.1 Conversion automatique
- **Application des formules** : Appliquer automatiquement les formules de conversion définies dans la configuration
- **Mappings automatiques** : Transformation des champs selon les règles prédéfinies
- **Traitement conditionnel** : Appliquer des conversions différentes selon le type d'entité (joueur, PNJ, créature)

#### 1.2 Conversion contextuelle
- **Niveau de l'entité** : Adaptation des valeurs selon le niveau
- **Type de créature** : Règles spécifiques selon la race/monstre
- **Contexte de jeu** : Différenciation PvE/PvP, solo/groupe
- **Équipement** : Bonus et malus selon l'équipement porté

#### 1.3 Validation des données
- **Vérification des limites** : Respect des seuils min/max définis
- **Cohérence des relations** : Validation des liens entre entités
- **Intégrité des données** : Vérification de la structure et du contenu
- **Caractéristiques convertibles** : Validation avec conversion et contraintes spécifiques

### 2. Gestion des erreurs

#### 2.1 Détection des erreurs
- **Erreurs de structure** : Données malformées ou incomplètes
- **Erreurs de validation** : Valeurs hors limites ou incohérentes
- **Erreurs de conversion** : Détecter les échecs de conversion
- **Erreurs de contexte** : Conflits entre règles de conversion

#### 2.2 Traitement des erreurs
- **Mode strict** : Rejeter les données avec erreurs
- **Mode permissif** : Utiliser des valeurs par défaut
- **Correction automatique** : Tentative de correction des erreurs mineures
- **Logging détaillé** : Enregistrement de toutes les erreurs pour analyse

### 3. Architecture technique

#### 3.1 Service de conversion principal
```php
class DataConversionService
{
    public function convert(array $sourceData, ConversionContext $context): ConversionResult;
    public function convertBatch(array $entities, array $options): BatchConversionResult;
    public function validate(array $data, ValidationContext $context): ValidationResult;
    public function getConversionRules(string $entityType): array;
}
```

#### 3.2 Gestionnaire de configuration
```php
class ConversionConfigManager
{
    public function getCharacteristicRules(string $characteristic): array;
    public function getConversionFormulas(string $entityType): array;
    public function getValidationRules(string $entityType): array;
    public function getDefaultValues(string $entityType): array;
}
```

#### 3.3 Contexte de conversion
```php
class ConversionContext
{
    public function __construct(
        public readonly string $entityType,
        public readonly int $level,
        public readonly string $context,
        public readonly array $options = []
    ) {}
}
```

### 4. Interface API

#### 4.1 Endpoints de conversion
```http
POST /api/data-conversion/convert
POST /api/data-conversion/convert/batch
POST /api/data-conversion/validate
GET /api/data-conversion/rules/{entityType}
```

#### 4.2 Interface de conversion
```php
public function convert(array $sourceData, ConversionContext $context): ConversionResult;
public function convertBatch(array $entities, array $options): BatchConversionResult;
public function validate(array $data, ValidationContext $context): ValidationResult;
```

### 5. Configuration et règles

#### 5.1 Règles de conversion
- **Mappings de champs** : Correspondance entre champs source et destination
- **Formules de calcul** : Transformations mathématiques des valeurs
- **Règles conditionnelles** : Application selon le contexte
- **Valeurs par défaut** : Fallbacks en cas d'erreur

#### 5.2 Formules de conversion
```php
// Exemple de formule pour les points de vie
'health' => [
    'formula' => 'value / 100',
    'min' => 1,
    'max' => 100,
    'round' => 'ceil'
],

// Exemple de formule pour la force
'strength' => [
    'formula' => 'value * 0.5',
    'min' => 0,
    'max' => 50,
    'round' => 'round'
]
```

### 6. Performance et optimisation

#### 6.1 Traitement en lot
- **Conversion multiple** : Traiter plusieurs entités simultanément
- **Gestion mémoire** : Contrôle de l'utilisation des ressources
- **Parallélisation** : Utilisation de workers pour les gros volumes
- **Statistiques** : Générer des statistiques de conversion

#### 6.2 Cache et optimisation
- **Cache des règles** : Mise en cache des règles de conversion
- **Cache des résultats** : Stockage des conversions fréquentes
- **Optimisation des requêtes** : Réduction des accès à la base
- **Lazy loading** : Chargement à la demande des configurations

### 7. Extensibilité

#### 7.1 Plugins de conversion
- **Système de plugins** : Ajout de nouvelles règles de conversion
- **Hooks personnalisés** : Points d'extension pour la logique métier
- **Règles dynamiques** : Chargement de règles depuis la base de données
- **Validation personnalisée** : Règles de validation spécifiques

#### 7.2 Monitoring et métriques
- **Métriques de performance** : Temps de conversion, utilisation mémoire
- **Taux de réussite** : Suivre le pourcentage de conversions réussies
- **Taux d'erreur** : Suivre les erreurs de conversion et validation
- **Alertes** : Notification en cas de problèmes

## 📊 Métriques et KPIs

### **Métriques de performance**
- **Temps de conversion** : Mesurer le temps de traitement par entité
- **Taux de réussite** : Suivre le pourcentage de conversions réussies
- **Taux d'erreur** : Suivre les erreurs de conversion et validation
- **Utilisation mémoire** : Contrôler la consommation des ressources

### **Métriques de qualité**
- **Précision des conversions** : Validation des résultats
- **Cohérence des données** : Vérification des relations
- **Complétude** : Couverture des entités et champs
- **Traçabilité** : Suivi des transformations effectuées

## 🧪 Tests et validation

### **Tests unitaires**
- **Couverture** : Couvrir toutes les fonctions de conversion
- **Cas limites** : Tester les valeurs aux bornes
- **Gestion d'erreurs** : Vérifier le traitement des erreurs
- **Performance** : Tests de charge et de stress

### **Tests d'intégration**
- **End-to-end** : Tester le flux complet de conversion
- **Base de données** : Validation des données sauvegardées
- **API** : Tests des endpoints de conversion
- **Régression** : Vérifier la non-régression des fonctionnalités

## 🔒 Sécurité et robustesse

### **Validation des entrées**
- **Sanitisation** : Nettoyage des données d'entrée
- **Validation stricte** : Vérification du format et du contenu
- **Protection contre les injections** : Sécurisation des formules
- **Limitation des ressources** : Contrôle de l'utilisation mémoire/CPU

### **Gestion des erreurs**
- **Logging sécurisé** : Ne pas exposer d'informations sensibles
- **Fallbacks sécurisés** : Valeurs par défaut sûres
- **Isolation** : Limiter l'impact des erreurs
- **Récupération** : Stratégies de récupération en cas d'échec

## 📚 Documentation

### **Documentation technique**
- **API Reference** : Documentation complète des endpoints
- **Architecture** : Schémas et diagrammes techniques
- **Configuration** : Guide de configuration détaillé
- **Déploiement** : Instructions de déploiement et maintenance

### **Documentation utilisateur**
- **Guide d'utilisation** : Tutoriels et exemples pratiques
- **FAQ** : Questions fréquentes et solutions
- **Troubleshooting** : Guide de résolution des problèmes
- **Changelog** : Historique des modifications

---

*Cahier des charges du service Data-conversion - Projet KrosmozJDR*
