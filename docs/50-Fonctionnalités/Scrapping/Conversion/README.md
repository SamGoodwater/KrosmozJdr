# Service de Data-conversion Dofus vers KrosmozJDR

## 📋 Présentation

Ce service permet de convertir automatiquement les données du jeu Dofus en données compatibles et exploitables par le projet KrosmozJDR. Il assure une transformation précise des caractéristiques, statistiques et propriétés des entités de Dofus vers le format attendu par KrosmozJDR.

## 🎯 Objectifs

- **Conversion automatique** : Transformer les données de Dofus en données KrosmozJDR sans intervention manuelle
- **Préservation de l'intégrité** : Maintenir la cohérence des données lors de la conversion
- **Adaptation des échelles** : Ajuster les valeurs numériques selon les spécificités de KrosmozJDR
- **Gestion des relations** : Préserver les liens entre entités (objets, sorts, créatures, etc.)
- **Traçabilité** : Conserver les références vers les données sources (DofusDB, ID officiels)

## 🔄 Fonctionnalités principales

### Conversion des entités
- **Classes** : Féca, Iop, Eniripsa, etc. avec leurs spécificités
- **Objets/Équipements** : Armes, armures, anneaux, etc.
- **Sorts** : Magies et capacités spéciales
- **Monstres** : Créatures et adversaires
- **Ressources** : Matériaux de base
- **Consommables** : Potions, nourritures, etc.

### Adaptations spécifiques
- **Points de vie** : Réduction d'un facteur de 100 (ex: 1000 PV Dofus → 10 PV KrosmozJDR)
- **Caractéristiques** : Adaptation des échelles de force, intelligence, agilité, etc.
- **Éléments** : Conversion des résistances et dégâts élémentaires
- **Niveaux** : Ajustement des paliers de progression

### Exclusion des données non pertinentes
- Caractéristiques spécifiques à Dofus (prospection, initiative, etc.)
- Mécaniques de jeu non utilisées dans KrosmozJDR
- Données techniques ou d'interface

## 🏗️ Architecture

### Services impliqués
- **DofusDBOrchestrator** : Orchestration du processus de conversion
- **DataConverterService** : Conversion des données selon les mappings
- **DataIntegrationService** : Intégration dans la base de données KrosmozJDR
- **DofusDBRetrievalService** : Récupération des données depuis l'API DofusDB

### Configuration
- **Mapping des champs** : `app/Services/Scrapping/config/mapping.json`
- **Configuration des entités** : `app/Services/Scrapping/data-conversion/config.php`
- **Règles de conversion** : `docs/50-Fonctionnalités/Scrapping/Conversion/DEFINITIONS.md`

## 📊 Données sources

### API DofusDB
- **Base URL** : `https://api.dofusdb.fr`
- **Entités disponibles** : breeds, items, spells, monsters, effects, etc.
- **Format** : JSON avec pagination
- **Langues** : Français, anglais, espagnol

### Documents d'équilibrage KrosmozJDR
- **Caractéristiques** : Valeurs cibles pour les attributs
- **Système de soin** : Mécaniques de régénération
- **Généralités Classes** : Spécificités des classes
- **Équipements et forgemagie** : Système d'équipement
- **Création de sorts** : Mécaniques magiques

## 🚀 Utilisation

### Commandes Artisan
```bash
# Conversion complète de toutes les entités
php artisan scrapping:convert-all

# Conversion d'une entité spécifique
php artisan scrapping:convert breeds
php artisan scrapping:convert items
php artisan scrapping:convert spells

# Conversion avec options
php artisan scrapping:convert breeds --limit=50 --dry-run
```

### Configuration
```bash
# Variables d'environnement
DOFUSDB_API_BASE_URL=https://api.dofusdb.fr
DOFUSDB_TIMEOUT=30
DOFUSDB_BATCH_SIZE=100
DOFUSDB_DEBUG_MODE=false
DOFUSDB_DRY_RUN_ENABLED=false
```

## 📈 Monitoring et logs

### Logs de conversion
- **Niveau** : `storage/logs/scrapping-conversion.log`
- **Informations** : Entités traitées, erreurs, statistiques
- **Format** : JSON structuré pour analyse

### Métriques
- Nombre d'entités converties
- Taux de succès par entité
- Temps de traitement
- Erreurs et avertissements

## 🔧 Maintenance

### Mise à jour des mappings
- Modification du fichier `mapping.json`
- Ajout de nouvelles règles de conversion
- Adaptation aux changements de l'API DofusDB

### Validation des données
- Vérification de l'intégrité des données converties
- Tests de cohérence entre entités
- Validation des relations

## 📚 Documentation associée

- [Définitions des conversions](./DEFINITIONS.md) : Règles détaillées de conversion
- [API du service](./API.md) : Interface technique du service
- [Architecture des services](../EXTERNALIZED_ARCHITECTURE.md) : Vue d'ensemble technique
- [Structure des données](../DATA_STRUCTURE.md) : Analyse des données DofusDB

## 🤝 Contribution

Pour contribuer au service de conversion :
1. Consulter les définitions de conversion
2. Tester les modifications en mode dry-run
3. Valider l'intégrité des données
4. Documenter les changements

---

*Service développé pour le projet KrosmozJDR - Conversion automatique des données Dofus*
