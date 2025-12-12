# Plan d'optimisation - Système Pages et Sections

## 📊 État actuel

### Fichiers identifiés
- **29+ fichiers** dans `resources/js/Pages/Organismes/section/`
- **8 composables** différents
- **1 adapter** UI
- **1 mapper obsolète** (ancien `sectionMapper.js`)
- **1 dossier mappers/** qui devrait être supprimé

### Problèmes identifiés

#### 1. Fichiers obsolètes
- ❌ `resources/js/Pages/Organismes/section/mappers/sectionMapper.js` - **OBSOLÈTE**
  - Remplacé par `@/Utils/Services/Mappers/SectionMapper`
  - Plus utilisé nulle part (vérifié)
  - **Action** : Supprimer le fichier et le dossier `mappers/`

#### 2. Composables redondants/simples
- ⚠️ `useSectionDefaults.js` - Simple wrapper
  - Fait juste `getTemplateDefaults()` depuis `templates/index.js`
  - **Action** : Supprimer, utiliser directement `getTemplateDefaults()`
  
- ⚠️ `useSectionStyles.js` - Logique utilitaire
  - Génère des classes CSS depuis les settings
  - **Action** : Déplacer vers un service statique `SectionStyleService` dans `@/Utils/Services/`
  
- ⚠️ `useSectionParameters.js` - Logique utilitaire
  - Génère des champs de formulaire
  - **Action** : Déplacer vers un service statique `SectionParameterService` dans `@/Utils/Services/`

#### 3. Architecture à simplifier
- ⚠️ Trop de couches : `Mapper → Model → Adapter → Composable`
  - **Action** : Simplifier en fusionnant adapter dans `useSectionUI` ou créer un service unique

#### 4. Duplication de logique
- ⚠️ `sectionUIAdapter` et `useSectionUI` font des choses similaires
  - **Action** : Fusionner ou clarifier les responsabilités

---

## 🎯 Plan d'optimisation

### Phase 1 : Nettoyage (Fichiers obsolètes)

#### 1.1 Supprimer l'ancien mapper
```bash
# Fichier à supprimer
resources/js/Pages/Organismes/section/mappers/sectionMapper.js
```

**Impact** : Aucun (plus utilisé)

#### 1.2 Supprimer le dossier mappers/
```bash
# Dossier à supprimer
resources/js/Pages/Organismes/section/mappers/
```

**Impact** : Aucun (vide après suppression du fichier)

---

### Phase 2 : Refactorisation des composables

#### 2.1 Supprimer `useSectionDefaults`
**Fichier** : `resources/js/Pages/Organismes/section/composables/useSectionDefaults.js`

**Remplacement** : Utiliser directement `getTemplateDefaults()` depuis `templates/index.js`

**Fichiers à modifier** :
- `CreateSectionModal.vue` : Remplacer `useSectionDefaults()` par `getTemplateDefaults()`

**Avantage** : -1 fichier, moins de couches

#### 2.2 Transformer `useSectionStyles` en service statique
**Fichier actuel** : `resources/js/Pages/Organismes/section/composables/useSectionStyles.js`

**Nouveau fichier** : `resources/js/Utils/Services/SectionStyleService.js`

**Changements** :
- Convertir en classe statique
- Méthodes statiques au lieu de computed
- Utiliser `TransformService` pour les transformations si nécessaire

**Fichiers à modifier** :
- Tous les templates Read qui utilisent `useSectionStyles()`
- Remplacer par `SectionStyleService.getClasses(settings)`

**Avantage** : Réutilisable partout, pas besoin de Vue reactivity

#### 2.3 Transformer `useSectionParameters` en service statique
**Fichier actuel** : `resources/js/Pages/Organismes/section/composables/useSectionParameters.js`

**Nouveau fichier** : `resources/js/Utils/Services/SectionParameterService.js`

**Changements** :
- Convertir en classe statique
- Méthodes statiques
- Centraliser les options (Visibility, PageState)

**Fichiers à modifier** :
- `SectionParamsModal.vue` : Remplacer `useSectionParameters()` par `SectionParameterService`

**Avantage** : Réutilisable, testable, pas de dépendance Vue

---

### Phase 3 : Simplification de l'architecture

#### 3.1 Fusionner adapter dans useSectionUI
**Option A** : Intégrer `sectionUIAdapter` directement dans `useSectionUI`
- Supprimer `sectionUIAdapter.js`
- Déplacer les fonctions dans `useSectionUI.js`
- **Avantage** : -1 fichier, moins de couches

**Option B** : Créer un service `SectionUIService`
- Déplacer la logique UI dans un service statique
- `useSectionUI` devient un simple wrapper réactif
- **Avantage** : Logique réutilisable, testable

**Recommandation** : Option A (plus simple, moins de fichiers)

#### 3.2 Simplifier le flux de données
**Avant** :
```
Raw Data → Mapper → Model → Adapter → Composable → Component
```

**Après** :
```
Raw Data → Mapper → Model → Composable (avec logique UI intégrée) → Component
```

**Avantage** : -1 couche (adapter), plus direct

---

### Phase 4 : Optimisation des composables restants

#### 4.1 `useSectionUI` - Garder (essentiel)
- Interface unifiée pour les composants
- Combine mapper + adapter (fusionné)
- **Action** : Intégrer l'adapter dedans

#### 4.2 `useSectionMode` - Garder (essentiel)
- Gère les modes lecture/écriture
- État global réactif
- **Action** : Aucune

#### 4.3 `useSectionSave` - Garder (essentiel)
- Auto-save avec debounce
- **Action** : Aucune

#### 4.4 `useSectionAPI` - Garder (essentiel)
- Appels API centralisés
- **Action** : Aucune

#### 4.5 `useSectionTemplates` - Garder (essentiel)
- Chargement dynamique des templates
- **Action** : Aucune

---

## 📁 Structure optimisée

### Avant (29+ fichiers)
```
section/
├── mappers/                    ❌ OBSOLÈTE
│   └── sectionMapper.js        ❌ OBSOLÈTE
├── adapters/                   ⚠️ À FUSIONNER
│   └── sectionUIAdapter.js     ⚠️ À FUSIONNER
├── composables/
│   ├── useSectionUI.js         ✅ GARDER (fusionner adapter)
│   ├── useSectionMode.js       ✅ GARDER
│   ├── useSectionSave.js       ✅ GARDER
│   ├── useSectionAPI.js        ✅ GARDER
│   ├── useSectionTemplates.js  ✅ GARDER
│   ├── useSectionDefaults.js   ❌ SUPPRIMER
│   ├── useSectionStyles.js     ⚠️ → Service
│   └── useSectionParameters.js ⚠️ → Service
├── modals/                     ✅ GARDER
├── templates/                   ✅ GARDER
└── ...
```

### Après (24 fichiers, -5 fichiers)
```
section/
├── composables/
│   ├── useSectionUI.js         ✅ (avec adapter intégré)
│   ├── useSectionMode.js       ✅
│   ├── useSectionSave.js       ✅
│   ├── useSectionAPI.js        ✅
│   └── useSectionTemplates.js ✅
├── modals/                     ✅
├── templates/                  ✅
└── ...

Utils/Services/
├── SectionStyleService.js      ✅ NOUVEAU (depuis useSectionStyles)
├── SectionParameterService.js  ✅ NOUVEAU (depuis useSectionParameters)
├── TransformService.js         ✅ EXISTANT
├── BaseMapper.js               ✅ EXISTANT
└── Mappers/
    ├── SectionMapper.js        ✅ EXISTANT
    └── PageMapper.js           ✅ EXISTANT
```

---

## 📝 Résumé des actions

### Fichiers à supprimer (3)
1. ❌ `section/mappers/sectionMapper.js`
2. ❌ `section/composables/useSectionDefaults.js`
3. ❌ `section/adapters/sectionUIAdapter.js` (après fusion)

### Fichiers à créer (2)
1. ✅ `Utils/Services/SectionStyleService.js`
2. ✅ `Utils/Services/SectionParameterService.js`

### Fichiers à modifier (8+)
1. ⚠️ `section/composables/useSectionUI.js` - Fusionner adapter
2. ⚠️ `section/modals/CreateSectionModal.vue` - Utiliser `getTemplateDefaults()`
3. ⚠️ `section/modals/SectionParamsModal.vue` - Utiliser `SectionParameterService`
4. ⚠️ Tous les templates Read - Utiliser `SectionStyleService`
5. ⚠️ Documentation à mettre à jour

---

## ✅ Avantages de l'optimisation

1. **-5 fichiers** : Structure plus simple
2. **Services réutilisables** : `SectionStyleService` et `SectionParameterService` utilisables partout
3. **Moins de couches** : Flux plus direct
4. **Meilleure testabilité** : Services statiques plus faciles à tester
5. **Cohérence** : Tous les services dans `Utils/Services/`
6. **Performance** : Moins de computed/watchers inutiles

---

## 🚀 Ordre d'exécution

1. ✅ Créer `SectionStyleService` et migrer `useSectionStyles`
2. ✅ Créer `SectionParameterService` et migrer `useSectionParameters`
3. ✅ Fusionner `sectionUIAdapter` dans `useSectionUI`
4. ✅ Supprimer `useSectionDefaults` et utiliser directement `getTemplateDefaults()`
5. ✅ Supprimer l'ancien `sectionMapper.js` et le dossier `mappers/`
6. ✅ Mettre à jour tous les imports
7. ✅ Tester et vérifier

---

## 📊 Métriques

- **Fichiers avant** : 29+
- **Fichiers après** : 24 (-5 fichiers, -17%)
- **Composables avant** : 8
- **Composables après** : 5 (-3 composables, -37%)
- **Services avant** : 2 (TransformService, BaseMapper)
- **Services après** : 4 (+2 services, +100%)

