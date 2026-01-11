# Checklist de refactoring des descriptors

**Date de création** : 2026-01-XX  
**Contexte** : Vérification et refactoring des fichiers selon les nouvelles règles strictes des descriptors

---

## 🎯 Objectif

Refactorer tous les fichiers de descriptors pour respecter les 4 règles absolues :
1. ❌ Aucune logique métier
2. ❌ Ne décrit pas une vue
3. ✅ Déterministe
4. ✅ Parle le langage du moteur

---

## 📋 Fichiers à vérifier/modifier

### 1. Descriptors (`*-descriptors.js`)

#### ❌ Problèmes identifiés

**A. Fonctions `build` dans `bulk` (logique de transformation)**
- **Fichiers concernés** : Tous les `*-descriptors.js`
- **Problème** : Les fonctions `build` contiennent de la logique de transformation
  ```javascript
  // ❌ MAUVAIS
  bulk: { 
    enabled: true, 
    nullable: true, 
    build: (v) => (v === "" ? null : String(v))  // Logique de transformation
  }
  ```
- **Solution** : Déplacer vers le mapper ou supprimer (le mapper gère déjà la normalisation)
- **Exemple** : `resource-descriptors.js` lignes 102, 148, 182, 223, 255, 278, 301, 324, 354, 378, 403, 428, 451

**B. Fonctions `options` au lieu de constantes**
- **Fichiers concernés** : `resource-descriptors.js` (ligne 222)
- **Problème** : Les options sont des fonctions au lieu de constantes
  ```javascript
  // ❌ MAUVAIS
  options: () => [{ value: "", label: "—" }, ...resourceTypes.map(...)]
  
  // ✅ BON
  options: RarityFormatter.OPTIONS  // Référence à une constante
  ```
- **Solution** : Utiliser des constantes ou référencer des formatters

**C. Fonctions `visibleIf` avec dépendances au contexte**
- **Fichiers concernés** : Tous les `*-descriptors.js`
- **Problème** : Les fonctions `visibleIf` utilisent des variables du contexte
  ```javascript
  // ❌ MAUVAIS (si canUpdateAny est calculé dans le descriptor)
  visibleIf: () => canUpdateAny
  
  // ✅ BON (si canUpdateAny vient du contexte)
  visibleIf: (ctx) => ctx.capabilities?.updateAny ?? false
  ```
- **Solution** : S'assurer que `visibleIf` reçoit le contexte et est pure

**D. Structure actuelle vs structure recommandée**
- **Problème** : Les descriptors sont des objets JSON au lieu d'utiliser des builders
- **Solution** : Garder la structure actuelle (elle est déclarative) mais supprimer toute logique

---

### 2. TableConfig (`*TableConfig.js`)

#### ✅ Points positifs
- Utilise des builders (`TableConfig`, `TableColumnConfig`)
- Structure déclarative

#### ⚠️ Points à améliorer
- **Dépendance aux descriptors** : Lit les descriptors pour obtenir labels/icônes
  - **Solution** : Soit intégrer directement dans TableConfig, soit garder la dépendance (acceptable si les descriptors sont purs)

---

### 3. FormConfig (`*FormConfig.js`)

#### ❌ Problèmes identifiés

**A. Utilisation des fonctions `build` des descriptors**
- **Fichiers concernés** : Tous les `*FormConfig.js`
- **Problème** : Utilise les fonctions `build` des descriptors (ligne 68, 80, 99, etc. dans `ResourceFormConfig.js`)
- **Solution** : Supprimer l'utilisation de `build` dans FormConfig, la transformation doit être dans le mapper

**B. Utilisation des fonctions `options`**
- **Fichiers concernés** : `ResourceFormConfig.js` (ligne 95)
- **Problème** : Utilise des fonctions `options` au lieu de constantes
- **Solution** : Utiliser des constantes ou référencer des formatters

---

### 4. BulkConfig (`*BulkConfig.js`)

#### ❌ Problèmes identifiés

**A. Utilisation des fonctions `build` des descriptors**
- **Fichiers concernés** : Tous les `*BulkConfig.js`
- **Problème** : Utilise les fonctions `build` des descriptors
- **Solution** : Supprimer l'utilisation de `build`, la transformation doit être dans le mapper

---

### 5. EntityDescriptor.js

#### ⚠️ Points à vérifier
- Contient des fonctions de formatage (`formatRarity`, `formatVisibility`, etc.)
- **Solution** : Ces fonctions sont dépréciées, utiliser les formatters à la place

---

## 🔧 Plan d'action

### Phase 1 : Nettoyage des descriptors

1. **Supprimer toutes les fonctions `build` des descriptors**
   - Les transformations doivent être dans les mappers
   - Les descriptors ne doivent contenir que des booléens (`enabled`, `nullable`)

2. **Remplacer les fonctions `options` par des constantes**
   - Utiliser `RarityFormatter.OPTIONS` au lieu de fonctions
   - Pour les options dynamiques (ex: `resourceTypes`), passer par le contexte

3. **Vérifier que `visibleIf` est pure et déterministe**
   - Doit recevoir le contexte en paramètre
   - Ne doit pas dépendre de variables externes

### Phase 2 : Nettoyage des Configs

1. **Supprimer l'utilisation de `build` dans FormConfig et BulkConfig**
   - La transformation doit être dans le mapper

2. **Simplifier les Configs**
   - Réduire la dépendance aux descriptors si possible
   - Utiliser directement les constantes des formatters

### Phase 3 : Migration vers les mappers

1. **Créer les mappers pour toutes les entités**
   - Déplacer la logique de transformation vers les mappers

2. **Mettre à jour les adapters**
   - Utiliser les mappers au lieu de créer directement les modèles

---

## 📝 Exemple de refactoring

### Avant (❌ Violation des règles)

```javascript
// resource-descriptors.js
rarity: {
  key: "rarity",
  label: "Rareté",
  edit: {
    form: {
      type: "select",
      options: [
        { value: 0, label: "Commun" },
        { value: 1, label: "Peu commun" },
        // ...
      ],
      bulk: { 
        enabled: true, 
        nullable: true, 
        build: (v) => (v === "" || v === null ? null : Number(v))  // ❌ Logique
      }
    }
  }
}
```

### Après (✅ Conforme)

```javascript
// resource-descriptors.js
rarity: {
  key: "rarity",
  label: "Rareté",
  edit: {
    form: {
      type: "select",
      options: RarityFormatter.OPTIONS,  // ✅ Constante
      bulk: { 
        enabled: true, 
        nullable: true
        // ✅ Pas de build, le mapper gère la transformation
      }
    }
  }
}
```

```javascript
// ResourceMapper.js
static fromForm(formData) {
  return new Resource({
    rarity: formData.rarity !== undefined && formData.rarity !== '' 
      ? Number(formData.rarity) 
      : null,  // ✅ Transformation dans le mapper
    // ...
  });
}
```

---

## ✅ Checklist de validation

Pour chaque descriptor, vérifier :

- [ ] Aucune fonction `build` dans `bulk`
- [ ] Les `options` sont des constantes ou référencent des formatters
- [ ] Les `visibleIf` sont pures et reçoivent le contexte
- [ ] Aucune logique métier (if, calculs, formatage)
- [ ] Aucune description de vue (Large/Compact/Minimal/Text)
- [ ] Déterministe (même contexte = même résultat)
- [ ] Parle le langage du moteur (`sortable`, `filterable`, `editable`)

---

## 📊 Fichiers à modifier

### Descriptors (16 fichiers)
- [ ] `resource/resource-descriptors.js` ⚠️ **PRIORITÉ** (exemple de référence)
- [ ] `resource-type/resource-type-descriptors.js`
- [ ] `item/item-descriptors.js`
- [ ] `consumable/consumable-descriptors.js`
- [ ] `spell/spell-descriptors.js`
- [ ] `monster/monster-descriptors.js`
- [ ] `creature/creature-descriptors.js`
- [ ] `npc/npc-descriptors.js`
- [ ] `classe/classe-descriptors.js`
- [ ] `campaign/campaign-descriptors.js`
- [ ] `scenario/scenario-descriptors.js`
- [ ] `attribute/attribute-descriptors.js`
- [ ] `panoply/panoply-descriptors.js`
- [ ] `capability/capability-descriptors.js`
- [ ] `specialization/specialization-descriptors.js`
- [ ] `shop/shop-descriptors.js`

### FormConfig (16 fichiers)
- [ ] `resource/ResourceFormConfig.js` ⚠️ **PRIORITÉ**
- [ ] Tous les autres `*FormConfig.js`

### BulkConfig (16 fichiers)
- [ ] `resource/ResourceBulkConfig.js` ⚠️ **PRIORITÉ**
- [ ] Tous les autres `*BulkConfig.js`

### Mappers (à créer)
- [ ] `Mappers/Entity/ResourceMapper.js` ✅ **DÉJÀ CRÉÉ**
- [ ] `Mappers/Entity/ItemMapper.js`
- [ ] `Mappers/Entity/ConsumableMapper.js`
- [ ] ... (toutes les autres entités)

---

## 🎯 Ordre de refactoring recommandé

1. **Commencer par Resource** (exemple de référence)
   - Nettoyer `resource-descriptors.js`
   - Nettoyer `ResourceFormConfig.js`
   - Nettoyer `ResourceBulkConfig.js`
   - Vérifier que `ResourceMapper.js` gère toutes les transformations

2. **Appliquer le même pattern aux autres entités**
   - Créer les mappers manquants
   - Nettoyer les descriptors
   - Nettoyer les Configs

3. **Vérification finale**
   - Tous les descriptors respectent les 4 règles
   - Tous les mappers gèrent les transformations
   - Tous les Configs sont déclaratifs

---

## 📚 Références

- [DESCRIPTORS_PATTERN.md](./DESCRIPTORS_PATTERN.md) — Règles strictes des descriptors
- [MAPPERS_PATTERN.md](./MAPPERS_PATTERN.md) — Pattern des mappers
- [ARCHITECTURE_ENTITY_SYSTEM.md](./ARCHITECTURE_ENTITY_SYSTEM.md) — Vue d'ensemble
