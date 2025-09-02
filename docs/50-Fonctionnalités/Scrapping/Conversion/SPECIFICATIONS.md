# Cahier des Charges - Service de Conversion Dofus vers KrosmozJDR

## 📋 Vue d'ensemble

### Objectif
Le service de conversion Dofus vers KrosmozJDR est un composant central de l'architecture KrosmozJDR qui transforme les données brutes de Dofus en données exploitables par le système KrosmozJDR. Il assure la cohérence, la validation et l'adaptation des données selon les règles métier définies.

### Portée
- **Entrée** : Données brutes de Dofus (majoritairement) et autres sources
- **Sortie** : Données validées et converties pour KrosmozJDR
- **Responsabilité** : Conversion, validation, cadrage des valeurs, gestion des erreurs

## 🎯 Fonctionnalités principales

### 1. Conversion de données

#### 1.1 Conversion automatique
- **Application des formules** : Appliquer automatiquement les formules de conversion définies dans la configuration
- **Gestion des types** : Convertir les types de données (entiers, décimaux, chaînes, booléens)
- **Traitement conditionnel** : Appliquer des conversions différentes selon le type d'entité (joueur, PNJ, créature)

#### 1.2 Conversion contextuelle
- **Variables d'environnement** : Utiliser le niveau, la classe, les caractéristiques de base
- **Calculs dérivés** : Générer des valeurs calculées à partir d'autres caractéristiques
- **Adaptations spécifiques** : Gérer les cas particuliers et exceptions

### 2. Cadrage des valeurs

#### 2.1 Limites dynamiques
- **Min/Max par niveau** : Définir des plages de valeurs acceptables selon le niveau
- **Min/Max par type d'entité** : Différencier les limites selon le type (joueur, PNJ, créature)
- **Min/Max par caractéristique** : Appliquer des limites spécifiques à chaque caractéristique
- **Limites universelles** : Définir des limites pour toutes les caractéristiques, convertibles ou non

#### 2.2 Validation des plages
- **Contrôle automatique** : Vérifier que les valeurs converties respectent les limites
- **Correction automatique** : Ajuster automatiquement les valeurs hors limites
- **Logging des corrections** : Enregistrer les ajustements effectués
- **Validation universelle** : Valider toute caractéristique selon sa définition, même si non convertible

### 3. Validation des données

#### 3.1 Validation universelle
- **Validation de toute caractéristique** : Le service peut valider n'importe quelle caractéristique définie dans le fichier de définition
- **Caractéristiques convertibles** : Validation avec conversion et contraintes spécifiques
- **Caractéristiques non-convertibles** : Validation simple (optionnel, ignoré, ou rejeté selon la règle)

#### 3.2 Validation structurelle
- **Cohérence des types** : Vérifier que les types de données sont corrects selon la définition
- **Champs obligatoires** : S'assurer que tous les champs requis sont présents
- **Format des données** : Valider le format des chaînes, dates, etc.
- **Champs optionnels** : Gérer les caractéristiques non-convertibles selon les règles définies

#### 3.3 Validation métier
- **Règles de cohérence** : Vérifier les relations entre caractéristiques
- **Contraintes logiques** : Valider les contraintes métier (ex: PV > 0)
- **Intégrité référentielle** : Vérifier les références vers d'autres entités
- **Validation par type d'entité** : Appliquer les contraintes spécifiques selon le type (joueur, PNJ, créature)

### 4. Gestion des erreurs

#### 4.1 Détection d'erreurs
- **Erreurs de conversion** : Détecter les échecs de conversion
- **Erreurs de validation** : Identifier les données invalides
- **Erreurs de configuration** : Repérer les problèmes de configuration

#### 4.2 Traitement des erreurs
- **Récupération gracieuse** : Continuer le traitement malgré certaines erreurs
- **Valeurs par défaut** : Utiliser des valeurs de secours en cas d'erreur
- **Reporting détaillé** : Fournir des rapports d'erreur complets

## 🏗️ Architecture du service

### 1. Composants principaux

#### 1.1 Service de conversion principal
```php
class DataConversionService
{
    public function convert(array $sourceData, array $mapping, string $context): array
    public function validate(array $convertedData, array $rules): ValidationResult
    public function applyConstraints(array $data, array $constraints): array
}
```

#### 1.2 Gestionnaire de configuration
```php
class ConversionConfigManager
{
    public function getMapping(string $entityType): array
    public function getConstraints(string $entityType, int $level): array
    public function getFormulas(string $characteristic): array
}
```

#### 1.3 Validateur de données
```php
class DataValidator
{
    public function validateStructure(array $data, array $schema): ValidationResult
    public function validateBusinessRules(array $data, array $rules): ValidationResult
    public function validateConstraints(array $data, array $constraints): ValidationResult
    public function validateAnyCharacteristic(string $characteristic, $value, array $context): ValidationResult
    public function validateAllCharacteristics(array $data, array $characteristics): ValidationResult
}
```

### 2. Interfaces et contrats

#### 2.1 Interface de conversion
```php
interface DataConverterInterface
{
    public function convert(array $sourceData, ConversionContext $context): ConversionResult;
    public function validate(array $data, ValidationContext $context): ValidationResult;
    public function applyConstraints(array $data, ConstraintContext $context): array;
}
```

#### 2.2 Interface de configuration
```php
interface ConfigProviderInterface
{
    public function getMapping(string $entityType): array;
    public function getFormulas(string $characteristic): array;
    public function getConstraints(string $entityType, int $level): array;
}
```

## ⚙️ Configuration et paramétrage

### 1. Fichiers de configuration

#### 1.1 Définition complète des caractéristiques
```json
{
  "characteristics": {
    "health_points": {
      "name": "Points de Vie",
      "type": "integer",
      "convertible": true,
      "formula": "convertHealth",
      "constraints": {
        "player": {"min": 5, "max": "level * 3"},
        "npc": {"min": 3, "max": "level * 2.5"},
        "creature": {"min": 2, "max": "level * 2"}
      },
      "validation_rules": ["positive", "within_bounds"]
    },
    "strength": {
      "name": "Force",
      "type": "integer",
      "convertible": true,
      "formula": "convertStatistic",
      "constraints": {
        "player": {"min": 1, "max": "level * 2"},
        "npc": {"min": 1, "max": "level * 1.8"},
        "creature": {"min": 1, "max": "level * 1.5"}
      },
      "validation_rules": ["positive", "within_bounds"]
    },
    "prospection": {
      "name": "Prospection",
      "type": "integer",
      "convertible": false,
      "description": "Caractéristique Dofus non utilisée dans KrosmozJDR",
      "validation_rules": ["optional"]
    },
    "critical_hit_chance": {
      "name": "Chance Critique",
      "type": "integer",
      "convertible": false,
      "description": "Mécanique Dofus non implémentée dans KrosmozJDR",
      "validation_rules": ["optional"]
    }
  }
}
```

#### 1.2 Mapping des entités
```json
{
  "breeds": {
    "target_model": "Classe",
    "mapping": {
      "name": {"source": "name", "type": "string", "required": true},
      "level": {"source": "level", "type": "integer", "formula": "level/10"},
      "health_points": {"source": "stats.health", "type": "integer", "formula": "convertHealth"},
      "strength": {"source": "stats.strength", "type": "integer", "formula": "convertStatistic"},
      "prospection": {"source": "stats.prospection", "type": "integer", "convertible": false}
    }
  }
}
```

#### 1.3 Formules de conversion
```json
{
  "formulas": {
    "convertHealth": {
      "function": "convertStatistic",
      "parameters": ["value", "type", "level", "multiplier"],
      "constraints": {"min": 1, "max": "level * 3"}
    },
    "convertStatistic": {
      "function": "convertStatistic",
      "parameters": ["value", "type", "level", "baseMultiplier"],
      "description": "Formule générique pour les caractéristiques de base"
    }
  }
}
```

#### 1.4 Règles de validation
```json
{
  "validation_rules": {
    "positive": {
      "type": "min_value",
      "value": 0,
      "message": "La valeur doit être positive"
    },
    "within_bounds": {
      "type": "range_check",
      "message": "La valeur doit être dans les limites définies"
    },
    "optional": {
      "type": "optional_field",
      "message": "Ce champ est optionnel et peut être ignoré"
    }
  }
}
```

### 2. Variables d'environnement

#### 2.1 Contexte de conversion
- **Type d'entité** : joueur, PNJ, créature
- **Niveau** : niveau de l'entité (1-20 pour KrosmozJDR)
- **Classe** : classe du personnage (pour les joueurs)
- **Caractéristiques de base** : valeurs de référence

#### 2.2 Paramètres de configuration
- **Mode strict** : Rejeter les données invalides vs. utiliser des valeurs par défaut
- **Logging détaillé** : Niveau de détail des logs
- **Validation automatique** : Activer/désactiver la validation automatique

## 🔧 Fonctionnalités avancées

### 1. Conversion en lot

#### 1.1 Traitement par lots
- **Conversion multiple** : Traiter plusieurs entités simultanément
- **Gestion de la mémoire** : Optimiser l'utilisation mémoire pour les gros volumes
- **Reprise après erreur** : Continuer le traitement malgré les erreurs

#### 1.2 Monitoring et reporting
- **Progression** : Suivre l'avancement du traitement
- **Statistiques** : Générer des statistiques de conversion
- **Rapports d'erreur** : Fournir des rapports détaillés

### 2. Extensibilité

#### 2.1 Plugins de conversion
- **Formules personnalisées** : Permettre l'ajout de formules spécifiques
- **Validateurs personnalisés** : Intégrer des règles de validation métier
- **Adaptateurs de données** : Supporter de nouvelles sources de données

#### 2.2 Configuration dynamique
- **Rechargement à chaud** : Modifier la configuration sans redémarrage
- **Configuration par environnement** : Différentes configurations selon l'environnement
- **Override de configuration** : Permettre des surcharges locales

### 3. Performance et optimisation

#### 3.1 Cache et mise en cache
- **Cache des formules** : Mettre en cache les formules calculées
- **Cache des contraintes** : Mettre en cache les contraintes par niveau
- **Cache des mappings** : Optimiser l'accès aux mappings

#### 3.2 Optimisations algorithmiques
- **Conversion lazy** : Convertir seulement quand nécessaire
- **Validation différée** : Reporter la validation à la fin
- **Traitement parallèle** : Utiliser le parallélisme quand possible

## 📊 Métriques et monitoring

### 1. Métriques de performance
- **Temps de conversion** : Mesurer le temps de traitement par entité
- **Taux de réussite** : Suivre le pourcentage de conversions réussies
- **Utilisation mémoire** : Surveiller l'utilisation des ressources

### 2. Métriques de qualité
- **Taux d'erreur** : Suivre les erreurs de conversion et validation
- **Qualité des données** : Mesurer la qualité des données converties
- **Cohérence** : Vérifier la cohérence des données générées

### 3. Alertes et notifications
- **Seuils d'erreur** : Alerter en cas de dépassement de seuils
- **Anomalies** : Détecter les comportements anormaux
- **Maintenance** : Notifier les besoins de maintenance

## 🔒 Sécurité et robustesse

### 1. Validation des entrées
- **Sanitisation** : Nettoyer les données d'entrée
- **Validation stricte** : Rejeter les données suspectes
- **Protection contre les injections** : Sécuriser les formules dynamiques

### 2. Gestion des erreurs
- **Isolation des erreurs** : Empêcher la propagation des erreurs
- **Rollback automatique** : Annuler les modifications en cas d'erreur
- **Logs sécurisés** : Ne pas exposer d'informations sensibles dans les logs

### 3. Tests et validation
- **Tests unitaires** : Couvrir toutes les fonctions de conversion
- **Tests d'intégration** : Valider l'intégration avec le système
- **Tests de charge** : Vérifier les performances sous charge

## 📝 Documentation et maintenance

### 1. Documentation technique
- **API Reference** : Documentation complète des interfaces
- **Guides d'utilisation** : Tutoriels et exemples d'utilisation
- **Architecture** : Documentation de l'architecture du service

### 2. Maintenance et évolution
- **Versioning** : Gérer les versions du service
- **Migration** : Faciliter les migrations de données
- **Rétrocompatibilité** : Maintenir la compatibilité avec les anciennes versions

### 3. Support et débogage
- **Logs détaillés** : Fournir des logs exploitables pour le débogage
- **Outils de diagnostic** : Outils pour diagnostiquer les problèmes
- **Documentation des erreurs** : Catalogue des erreurs courantes et solutions

---

**Version** : 1.0  
**Date** : 2025-01-27  
**Responsable** : Équipe de développement KrosmozJDR  
**Statut** : En cours de rédaction
