# Optimisations identifiées — Système d'entités

**Date** : 2026-01-XX  
**Statut** : 🔍 Analyse

---

## 🎯 Vue d'ensemble

Après analyse du code basée sur la documentation, voici les optimisations identifiées pour améliorer la cohérence, réduire la duplication et simplifier la maintenance.

---

## 🔴 Priorité 1 : Redondances majeures

### 1.1 Fichiers `*TableConfig.js` redondants

**Problème** :
- Tous les fichiers `ResourceTableConfig.js`, `ItemTableConfig.js`, `SpellTableConfig.js`, etc. créent manuellement les colonnes
- `TableConfig.fromDescriptors()` existe déjà et peut générer automatiquement les colonnes depuis les descriptors
- **~15 fichiers** avec du code dupliqué (~300 lignes chacun)

**Solution** :
- Supprimer tous les fichiers `*TableConfig.js`
- Utiliser directement `TableConfig.fromDescriptors(descriptors, ctx)` dans les pages Index
- Si des configurations spéciales sont nécessaires (filtres complexes, etc.), les ajouter après la génération automatique

**Impact** :
- ✅ Réduction de ~4500 lignes de code
- ✅ Source de vérité unique : les descriptors
- ✅ Maintenance simplifiée

**Fichiers concernés** :
```
Entities/resource/ResourceTableConfig.js
Entities/item/ItemTableConfig.js
Entities/spell/SpellTableConfig.js
Entities/monster/MonsterTableConfig.js
... (15 fichiers au total)
```

**Exemple de migration** :
```javascript
// AVANT (ResourceTableConfig.js)
export function createResourceTableConfig(ctx = {}) {
  const descriptors = getResourceFieldDescriptors(ctx);
  const tableConfig = new TableConfig({...});
  // 300+ lignes de création manuelle de colonnes
  return tableConfig;
}

// APRÈS (dans Index.vue)
const tableConfig = computed(() => {
  const descriptors = getResourceFieldDescriptors(ctx);
  const config = TableConfig.fromDescriptors(descriptors, ctx);
  // Si besoin, ajouter des configurations spéciales :
  // config.getColumn('level').withFilter({...});
  return config.build(ctx);
});
```

---

### 1.2 Fonction `buildCell` redondante dans `entity-registry.js`

**Problème** :
- Chaque entité a une fonction `buildCell` identique dans `entity-registry.js` :
  ```javascript
  buildCell: (fieldKey, entityData, ctx = {}, opts = {}) => {
    const resource = entityData instanceof Resource ? entityData : new Resource(entityData);
    return resource.toCell(fieldKey, { size: opts.size || 'md', ...opts });
  }
  ```
- **~15 fonctions identiques** (une par entité)
- `BaseModel.toCell()` fait déjà exactement ça

**Solution** :
- Supprimer toutes les fonctions `buildCell` de `entity-registry.js`
- Utiliser directement `entity.toCell()` dans `TanStackTable.vue`
- Les entités sont déjà des instances de modèles dans `rowParams.entity`

**Impact** :
- ✅ Réduction de ~300 lignes de code
- ✅ Suppression d'une couche d'indirection inutile

**Fichiers concernés** :
- `Entities/entity-registry.js` (15 fonctions `buildCell`)
- `Pages/Organismes/table/TanStackTable.vue` (fallback `entityConfig.buildCell`)

---

### 1.3 `ResourceQuickEdit.vue` spécifique redondant

**Problème** :
- `ResourceQuickEdit.vue` existe alors qu'on a `EntityQuickEdit.vue` générique
- Le code est presque identique (même logique, mêmes composables)
- Seule différence : quelques champs spécifiques qui peuvent être gérés via descriptors

**Solution** :
- Supprimer `ResourceQuickEdit.vue`
- Utiliser `EntityQuickEdit.vue` pour toutes les entités
- Si des besoins spécifiques existent, les gérer via les descriptors ou créer un composant atom/molecule réutilisable

**Impact** :
- ✅ Réduction de ~400 lignes de code
- ✅ Cohérence : toutes les entités utilisent le même composant

**Fichiers concernés** :
- `Pages/Molecules/entity/resource/ResourceQuickEdit.vue`

---

## 🟡 Priorité 2 : Redondances mineures

### 2.1 Constantes `_quickEditFields` redondantes

**Problème** :
- Les descriptors contiennent à la fois `_quickeditConfig.fields` et des constantes `RESOURCE_QUICK_EDIT_FIELDS`
- Redondance : les deux contiennent la même liste de champs

**Solution** :
- Supprimer les constantes `*_QUICK_EDIT_FIELDS`
- Utiliser uniquement `_quickeditConfig.fields` dans les descriptors
- Mettre à jour `entity-registry.js` pour utiliser `descriptors._quickeditConfig.fields`

**Impact** :
- ✅ Source de vérité unique
- ✅ Réduction de ~15 constantes

**Fichiers concernés** :
- Tous les `*-descriptors.js` (15 fichiers)

---

### 2.2 `viewFields` dans `entity-registry.js` redondant

**Problème** :
- `entity-registry.js` contient `viewFields: { quickEdit: [...], compact: [...], extended: [...] }`
- Ces informations sont déjà dans les descriptors (`_quickeditConfig.fields`, `display.*`)

**Solution** :
- Supprimer `viewFields` de `entity-registry.js`
- Utiliser directement les descriptors pour déterminer les champs à afficher

**Impact** :
- ✅ Source de vérité unique : les descriptors
- ✅ Réduction de ~200 lignes

---

## 🟢 Priorité 3 : Améliorations de cohérence

### 3.1 Uniformiser l'utilisation de `TableConfig.fromDescriptors()`

**Problème** :
- Certaines pages utilisent `createResourceTableConfig()`, d'autres pourraient utiliser `TableConfig.fromDescriptors()`
- Incohérence dans l'approche

**Solution** :
- Migrer toutes les pages Index pour utiliser `TableConfig.fromDescriptors()`
- Supprimer les fonctions `create*TableConfig()`

**Impact** :
- ✅ Cohérence dans tout le codebase
- ✅ Maintenance simplifiée

---

### 3.2 Simplifier `entity-registry.js`

**Problème** :
- `entity-registry.js` contient beaucoup d'informations redondantes avec les descriptors
- `buildCell`, `viewFields`, etc. peuvent être déduits automatiquement

**Solution** :
- Réduire `entity-registry.js` à l'essentiel :
  - `getDescriptors` : fonction pour obtenir les descriptors
  - `responseAdapter` : adapter pour les réponses API
  - `ModelClass` : classe du modèle (optionnel, peut être déduit)
- Supprimer `buildCell`, `viewFields`, `defaults` (déduits depuis descriptors)

**Impact** :
- ✅ Code plus simple et maintenable
- ✅ Moins de duplication

---

## 📊 Résumé des gains

| Optimisation | Lignes supprimées | Fichiers supprimés | Complexité réduite |
|--------------|------------------|-------------------|-------------------|
| `*TableConfig.js` | ~4500 | 15 | ⭐⭐⭐ |
| `buildCell` dans registry | ~300 | 0 | ⭐⭐ |
| `ResourceQuickEdit.vue` | ~400 | 1 | ⭐⭐ |
| `_quickEditFields` constants | ~150 | 0 | ⭐ |
| `viewFields` dans registry | ~200 | 0 | ⭐ |
| **TOTAL** | **~5550** | **16** | **⭐⭐⭐** |

---

## 🎯 Plan d'action recommandé

### Phase 1 : Suppression des `*TableConfig.js` (Impact majeur)
1. Migrer une entité test (ex: `resource-type`)
2. Vérifier que tout fonctionne
3. Migrer les autres entités une par une
4. Supprimer les fichiers `*TableConfig.js`

### Phase 2 : Simplification de `entity-registry.js`
1. Supprimer `buildCell` (utiliser `entity.toCell()` directement)
2. Supprimer `viewFields` (utiliser descriptors)
3. Nettoyer le code

### Phase 3 : Suppression de `ResourceQuickEdit.vue`
1. Vérifier que `EntityQuickEdit.vue` couvre tous les cas
2. Supprimer `ResourceQuickEdit.vue`
3. Mettre à jour les imports

### Phase 4 : Nettoyage des constantes
1. Supprimer `*_QUICK_EDIT_FIELDS` des descriptors
2. Utiliser uniquement `_quickeditConfig.fields`

---

## ⚠️ Points d'attention

1. **Filtres complexes** : Certains `*TableConfig.js` peuvent avoir des filtres très spécifiques. Vérifier que `TableConfig.fromDescriptors()` les gère correctement.

2. **Tests** : S'assurer que tous les tests passent après chaque phase.

3. **Rétrocompatibilité** : Si d'autres parties du code utilisent `createResourceTableConfig()`, les migrer en même temps.

---

## 🔗 Liens utiles

- [ARCHITECTURE.md](./ARCHITECTURE.md) — Architecture du système
- [CONFIGS.md](./CONFIGS.md) — Guide des configurations
- [DESCRIPTORS.md](./DESCRIPTORS.md) — Guide des descriptors
