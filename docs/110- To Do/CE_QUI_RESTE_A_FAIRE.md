# Ce qui reste à faire

**Date de création** : 2026-01-XX  
**Contexte** : Après la refonte de Resource et la simplification des configs

---

## ✅ Ce qui a été fait

1. **Réorganisation des fichiers** ✅
   - Migration `Entities/entity/` → `Utils/Entity/`
   - Suppression des fichiers obsolètes
   - Mise à jour de tous les imports (59 fichiers)

2. **Simplification des configs** ✅
   - Suppression de la redondance bulk (`FormFieldConfig.bulk` supprimé)
   - Fusion des helpers dans les classes (`TableConfig.fromDescriptors()`, `BulkConfig.fromDescriptors()`)
   - Réduction de 7 à 5 fichiers

3. **Refonte de Resource** ✅
   - `ResourceTableConfig.js` simplifié (utilise `_tableConfig` + descriptors)
   - `ResourceBulkConfig.js` simplifié (utilise `BulkConfig.fromDescriptors()`)
   - `ResourceFormConfig.js` nettoyé
   - `resource-descriptors.js` enrichi (`_tableConfig`, `_quickeditConfig`)

---

## 📋 Ce qui reste à faire

### 1. Appliquer la refonte aux autres entités (15 entités restantes)

**Entités concernées :**
- ResourceType
- Item
- Consumable
- Spell
- Monster
- Creature
- Npc
- Classe
- Campaign
- Scenario
- Attribute
- Capability
- Specialization
- Shop
- Panoply

**Actions pour chaque entité :**

#### 1.1 Enrichir les descriptors avec `_tableConfig` et `_quickeditConfig`

**Exemple pour ResourceType :**
```javascript
// resource-type-descriptors.js
export function getResourceTypeFieldDescriptors(ctx = {}) {
  return {
    // ... descriptors des champs ...
    
    _tableConfig: {
      id: "resource-types.index",
      entityType: "resource-type",
      quickEdit: { enabled: true, permission: "updateAny" },
      actions: { enabled: true, permission: "view", available: [...], defaultVisible: {...} },
      features: { search: {...}, filters: {...}, pagination: {...}, ... },
      ui: { skeletonRows: 10 },
    },
    _quickeditConfig: {
      fields: RESOURCE_TYPE_QUICK_EDIT_FIELDS,
    },
    _quickEditFields: RESOURCE_TYPE_QUICK_EDIT_FIELDS,
  };
}
```

**Fichiers à modifier :** 15 fichiers `*-descriptors.js`

---

#### 1.2 Simplifier les `*TableConfig.js`

**Avant :**
```javascript
// ResourceTypeTableConfig.js - 300+ lignes
const tableConfig = new TableConfig({ id: "...", entityType: "..." })
  .withQuickEdit({ ... })
  .withActions({ ... })
  .addColumn(new TableColumnConfig({ key: "name", label: "Nom", ... }))
  .addColumn(...)
  // ... 20+ colonnes manuelles
```

**Après :**
```javascript
// ResourceTypeTableConfig.js - ~100 lignes
const descriptors = getResourceTypeFieldDescriptors(ctx);
const tableConfigData = descriptors._tableConfig || {};
const tableConfig = new TableConfig({ ...tableConfigData });
// Applique les configs depuis _tableConfig
if (tableConfigData.quickEdit) tableConfig.withQuickEdit(...);
// Utilise les labels/icônes des descriptors
tableConfig.addColumn(
  new TableColumnConfig({
    key: "name",
    label: descriptors.name?.label || "Nom",  // ✅ Depuis descriptor
    icon: descriptors.name?.icon || "...",     // ✅ Depuis descriptor
    ...
  })
);
// Colonnes avec configs spéciales seulement
```

**Fichiers à modifier :** 15 fichiers `*TableConfig.js`

**Réduction estimée :** ~200 lignes par fichier → ~100 lignes = **-1500 lignes au total**

---

#### 1.3 Simplifier les `*BulkConfig.js`

**Avant :**
```javascript
// ResourceTypeBulkConfig.js - 49 lignes
const bulkConfig = new BulkConfig({ entityType: "resource-type" });
for (const [key, descriptor] of Object.entries(descriptors)) {
  if (descriptor.edit?.form?.bulk?.enabled) {
    bulkConfig.addField(key, { ... });
  }
}
bulkConfig.withQuickEditFields(RESOURCE_TYPE_QUICK_EDIT_FIELDS);
```

**Après :**
```javascript
// ResourceTypeBulkConfig.js - 25 lignes
const descriptors = getResourceTypeFieldDescriptors(ctx);
const bulkConfig = BulkConfig.fromDescriptors(descriptors, ctx);
return bulkConfig.build();
```

**Fichiers à modifier :** 15 fichiers `*BulkConfig.js`

**Réduction estimée :** 49 lignes → 25 lignes = **-24 lignes par fichier = -360 lignes au total**

---

#### 1.4 Nettoyer les `*FormConfig.js`

**Actions :**
- Supprimer tous les appels `.withBulk()` et `.withoutBulk()` (déjà fait pour Resource)
- Vérifier qu'il n'y a pas d'erreurs de syntaxe

**Fichiers à modifier :** 15 fichiers `*FormConfig.js`

---

### 2. Créer `FormConfig.fromDescriptors()` (amélioration future)

**Objectif :** Automatiser aussi la génération des formulaires, similaire à `BulkConfig.fromDescriptors()`

**Avantages :**
- Réduction drastique du code dans les `*FormConfig.js`
- Configuration centralisée dans les descriptors
- Code plus DRY

**Complexité :** Moyenne (gestion des groupes, options dynamiques, etc.)

**Priorité :** ⚠️ Faible (les formulaires fonctionnent déjà bien manuellement)

---

### 3. Tests et vérifications

**Actions :**
1. ✅ Vérifier que les tests existants passent toujours
2. ⚠️ Créer des tests pour `TableConfig.fromDescriptors()` et `BulkConfig.fromDescriptors()`
3. ⚠️ Tester que l'application fonctionne correctement avec les nouvelles configs
4. ⚠️ Vérifier que les tableaux s'affichent correctement
5. ⚠️ Vérifier que le bulk edit fonctionne

---

### 4. Documentation

**Actions :**
1. ✅ Documenter la refonte de Resource
2. ⚠️ Créer un guide pour appliquer la refonte aux autres entités
3. ⚠️ Mettre à jour la documentation générale du projet

---

## 📊 Estimation

### Réduction de code totale (si on applique à toutes les entités)

| Type de fichier | Lignes avant | Lignes après | Réduction par fichier | Total (15 entités) |
|----------------|--------------|--------------|----------------------|-------------------|
| `*TableConfig.js` | ~300 | ~100 | -200 | **-3000 lignes** |
| `*BulkConfig.js` | ~49 | ~25 | -24 | **-360 lignes** |
| `*FormConfig.js` | ~200 | ~200 | 0 | 0 (nettoyé) |

**Total estimé :** **-3360 lignes de code** 🎉

---

## 🎯 Priorités

### Priorité 1 : Appliquer la refonte aux autres entités ⚠️

**Impact :** Très élevé (réduction massive de code, cohérence)

**Ordre suggéré :**
1. ResourceType (similaire à Resource)
2. Item, Consumable, Spell (entités simples)
3. Monster, Creature, Npc (entités avec caractéristiques)
4. Classe, Campaign, Scenario (entités métier)
5. Attribute, Capability, Specialization (entités de configuration)
6. Shop, Panoply (entités complexes)

**Durée estimée :** 2-3 jours (si automatisé avec des scripts)

---

### Priorité 2 : Tests et vérifications ⚠️

**Impact :** Élevé (s'assurer que tout fonctionne)

**Actions :**
- Exécuter tous les tests
- Tester manuellement les fonctionnalités critiques
- Vérifier que les tableaux s'affichent correctement
- Vérifier que le bulk edit fonctionne

**Durée estimée :** 1 jour

---

### Priorité 3 : FormConfig.fromDescriptors() (optionnel)

**Impact :** Moyen (amélioration future)

**Complexité :** Moyenne

**Durée estimée :** 1-2 jours

---

## 📝 Checklist

### Phase 1 : Refonte des autres entités

- [ ] ResourceType
  - [ ] Ajouter `_tableConfig` dans `resource-type-descriptors.js`
  - [ ] Simplifier `ResourceTypeTableConfig.js`
  - [ ] Simplifier `ResourceTypeBulkConfig.js`
  - [ ] Nettoyer `ResourceTypeFormConfig.js`
- [ ] Item
- [ ] Consumable
- [ ] Spell
- [ ] Monster
- [ ] Creature
- [ ] Npc
- [ ] Classe
- [ ] Campaign
- [ ] Scenario
- [ ] Attribute
- [ ] Capability
- [ ] Specialization
- [ ] Shop
- [ ] Panoply

### Phase 2 : Tests et vérifications

- [ ] Exécuter tous les tests unitaires
- [ ] Tester les tableaux (affichage, tri, filtres, recherche)
- [ ] Tester le bulk edit
- [ ] Tester les formulaires
- [ ] Vérifier les permissions

### Phase 3 : Documentation

- [ ] Guide pour appliquer la refonte aux autres entités
- [ ] Mettre à jour la documentation générale
- [ ] Documenter les nouvelles méthodes statiques

---

## 🚀 Prochaines étapes recommandées

1. **Créer un script/template** pour automatiser la refonte des autres entités
2. **Commencer par ResourceType** (le plus similaire à Resource)
3. **Tester au fur et à mesure** pour s'assurer que tout fonctionne
4. **Documenter les changements** pour chaque entité

---

## 📚 Références

- [REFONTE_RESOURCE_TERMINEE.md](./REFONTE_RESOURCE_TERMINEE.md) — Exemple de refonte complète
- [SIMPLIFICATION_CONFIGS_TERMINEE.md](./SIMPLIFICATION_CONFIGS_TERMINEE.md) — Simplification des configs
- [VERIFICATION_ENTITIES_SYSTEM.md](./VERIFICATION_ENTITIES_SYSTEM.md) — État de migration
