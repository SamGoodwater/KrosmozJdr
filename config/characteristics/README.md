# Configuration des caractéristiques KrosmozJDR

Ce dossier contient les définitions génériques des caractéristiques utilisées par l'ensemble du projet KrosmozJDR.

## 📁 Structure

```
config/characteristics/
├── README.md                    # Ce fichier
├── characteristics.json         # Définitions des caractéristiques
├── formulas.json               # Formules de calcul
├── validation_rules.json       # Règles de validation
└── entity_mappings.json        # Mappings des entités
```

## 🎯 Objectif

Ces fichiers définissent de manière générique :
- **Les caractéristiques** du système KrosmozJDR
- **Les contraintes** par type d'entité (joueur, PNJ, créature)
- **Les formules de calcul** pour les caractéristiques dérivées
- **Les règles de validation** universelles
- **Les mappings d'entités** pour la persistance

## 🔧 Utilisation

### Accès depuis le code Laravel

```php
// Accès aux définitions des caractéristiques
$characteristics = config('characteristics.definitions');

// Accès aux formules
$formulas = config('characteristics.formulas');

// Accès aux règles de validation
$validationRules = config('characteristics.validation_rules');

// Accès aux mappings d'entités
$mappings = config('characteristics.entity_mappings');
```

### Utilisation par différents services

#### Service de dataconversion
```php
// Configuration spécifique au service
$conversionConfig = config('data-conversion');

// Utilisation des définitions génériques
$characteristics = config('characteristics.definitions');
```

#### Service de validation
```php
// Validation universelle de toute caractéristique
$validator = new CharacteristicValidator(config('characteristics.validation_rules'));
$isValid = $validator->validate($characteristic, $value, $entityType);
```

#### Service de génération de formulaires
```php
// Génération automatique de formulaires
$formGenerator = new FormGenerator(config('characteristics.definitions'));
$form = $formGenerator->generateForm($entityType);
```

## 📊 Contenu des fichiers

### `characteristics.json`
- **Définitions** de toutes les caractéristiques KrosmozJDR
- **Contraintes** par type d'entité (min/max, formules)
- **Règles de validation** associées
- **Types de données** (integer, string, boolean, etc.)

### `formulas.json`
- **Formules de calcul** pour les caractéristiques dérivées
- **Fonctions** réutilisables
- **Paramètres** et contextes
- **Types de retour**

### `validation_rules.json`
- **Règles de validation** universelles
- **Niveaux de sévérité** (error, warning, info)
- **Actions** associées (reject, correct, continue)
- **Groupes de règles** prédéfinis

### `entity_mappings.json`
- **Mappings** des entités vers les modèles Laravel
- **Fonctions de traitement** (extraction, transformation)
- **Champs requis** et optionnels
- **Types de données** cibles

## 🔄 Extensibilité

### Ajouter une nouvelle caractéristique

1. **Définir** dans `characteristics.json`
2. **Ajouter** les contraintes par type d'entité
3. **Définir** les règles de validation
4. **Mettre à jour** les mappings si nécessaire

### Ajouter une nouvelle formule

1. **Définir** dans `formulas.json`
2. **Implémenter** la fonction correspondante
3. **Tester** avec différents contextes

### Ajouter une nouvelle entité

1. **Définir** le mapping dans `entity_mappings.json`
2. **Spécifier** les champs requis et optionnels
3. **Configurer** les fonctions de traitement

## 🛠️ Maintenance

### Mise à jour des contraintes
- Modifier directement les fichiers JSON
- Les changements sont automatiquement pris en compte
- Pas de redéploiement nécessaire (sauf si cache activé)

### Versioning
- Chaque fichier contient des métadonnées de version
- Suivi des modifications dans Git
- Possibilité de rollback en cas de problème

### Tests
- Validation automatique de la structure JSON
- Tests de cohérence entre les fichiers
- Tests d'intégration avec les services

## 🔒 Sécurité

- **Validation stricte** des entrées
- **Sanitisation** des données
- **Protection** contre les injections
- **Contrôle d'accès** aux modifications

## 📈 Performance

- **Cache automatique** par Laravel
- **Chargement différé** des configurations
- **Optimisation** des requêtes de validation
- **Gestion mémoire** efficace

## 📁 Structure des services

```
app/Services/
├── data-conversion/                  # Service de data-conversion
│   └── config.php              # Configuration spécifique
├── data-collect/                  # Service de data-collect
│   └── config.php              # Configuration spécifique
├── data-integration/                  # Service de data-integration
│   └── config.php              # Configuration spécifique
├── ImageService.php
├── FileService.php
└── NotificationService.php

config/
├── characteristics.php          # Configuration générique Laravel
└── characteristics/             # Définitions génériques
    ├── README.md
    ├── characteristics.json
    ├── formulas.json
    ├── validation_rules.json
    └── entity_mappings.json
```

---

**Note** : Ces fichiers sont conçus pour être génériques et réutilisables par l'ensemble du projet. Pour des configurations spécifiques à un service, créer un fichier de configuration dédié dans le dossier du service.
