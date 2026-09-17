# Tests unitaires pour les Services

## Structure

```
tests/unit/services/
├── TransformService.test.js      # Tests pour TransformService
├── BaseMapper.test.js             # Tests pour BaseMapper
└── Mappers/
    ├── SectionMapper.test.js      # Tests pour SectionMapper
    └── PageMapper.test.js         # Tests pour PageMapper
```

## TransformService

Tests pour toutes les transformations communes :
- ✅ `generateSlugFromTitle()` - Génération de slug depuis un titre
- ✅ `generateSlug()` - Génération de slug depuis titre ou ID
- ✅ `generateRandomString()` - Génération de chaîne aléatoire
- ✅ `normalizeText()` - Normalisation de texte
- ✅ `toEnum()` / `fromEnum()` - Conversion d'enums
- ✅ `normalizePivot()` - Normalisation des relations pivots
- ✅ `formatDate()` - Formatage de dates

## BaseMapper

Tests pour la classe de base des mappers :
- ✅ `normalize()` - Normalisation des données brutes
- ✅ `normalizePermissions()` - Normalisation des permissions
- ✅ `normalizeRelation()` - Normalisation des relations
- ✅ `normalizePivot()` - Délégation à TransformService
- ✅ `extractValue()` - Extraction de valeurs avec fallback
- ✅ `toEnum()` / `fromEnum()` - Délégation à TransformService
- ✅ `mapToModels()` - Mapping de tableaux

## SectionMapper

Tests pour le mapper de sections :
- ✅ `mapToModel()` - Mapping des données brutes en modèle Section
- ✅ `mapToFormData()` - Conversion pour formulaires
- ✅ `mapFromFormData()` - Nettoyage des valeurs vides
- ✅ `normalizeSectionData()` - Normalisation complète des données

## PageMapper

Tests pour le mapper de pages :
- ✅ `mapToModel()` - Mapping des données brutes en modèle Page
- ✅ `mapToFormData()` - Conversion pour formulaires
- ✅ `mapFromFormData()` - Nettoyage des valeurs vides

## Exécution

```bash
# Lancer tous les tests
pnpm test

# Lancer uniquement les tests des services
pnpm test tests/unit/services

# Avec coverage
pnpm test:coverage
```

## Couverture

Les tests couvrent :
- ✅ Toutes les méthodes publiques
- ✅ Les cas limites (null, undefined, valeurs vides)
- ✅ Les conversions de types
- ✅ Les normalisations de données
- ✅ Les relations et pivots

