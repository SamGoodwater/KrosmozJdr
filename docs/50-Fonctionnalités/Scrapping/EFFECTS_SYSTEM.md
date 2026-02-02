## Système d’effets (DofusDB → KrosmozJDR)

### Problème
DofusDB modélise les effets selon les mécaniques de Dofus (ciblage, triggers, zones, etc.).  
KrosmozJDR a un modèle plus “design system” (capabilities, bonus simplifiés), donc il faut un pont évolutif.

### Principe : 2 couches
1) **Normalisation (stable, exhaustive)**  
   On transforme tous les effets DofusDB (sorts + items) en une structure unique `EffectInstance`.
2) **Mapping Krosmoz (évolutif)**  
   On traduit progressivement certains `effectId` (et contexte) vers des bonus/capabilities KrosmozJDR.

---

## DofusDB : dictionnaire vs instances
### Dictionnaire d’effets
- Endpoint : `GET /effects?lang=fr`
- Contenu : définitions (description + métadonnées : `characteristic`, `elementId`, flags…)

### Instances d’effets
Les effets “appliqués” sont des instances, qui référencent un `effectId` (dictionnaire) et ajoutent les paramètres gameplay.

- **Sorts** : `GET /spell-levels?lang=fr` → `effects[]`
- **Items** : `GET /items/{id}?lang=fr` → `effects[]` (et variantes : `possibleEffects` dans certains contextes)

---

## Normalisation : `EffectInstance`
Objectif : unifier sorts/items en un format stable, sans perdre d’information.

Champs minimaux (toujours présents) :
- `sourceType` : `spell_level|item|...`
- `effectId` : int|null
- `effectUid` : int|null
- `raw` : objet DofusDB original (si `includeRaw=true`)

Champs “best effort” (si présents dans l’input) :
- `targetMask`, `targetId`, `duration`, `delay`, `triggers`, `dispellable`
- valeurs numériques : `from`, `to`, `diceNum`, `diceSide`, `value`, `min`, `max`
- `zone` (si `zoneDescr` existe) : `{ shape, param1, param2, cellIds, raw }`

> Cette couche doit rester **pure** (sans IO) et stable.

---

## Implémentation actuelle (couche A)
### Formatter
- Nom : `normalizeDofusdbEffects`
- Type : `pure`
- Registry : `resources/scrapping/formatters/registry.json`

### Stockage temporaire (compat)
Pour démarrer sans migrations DB, on encode en JSON dans des champs string existants :
- `spell.effect` : JSON des `EffectInstance` (issus de `levels.0.effects`)
- `item.bonus` : JSON des `EffectInstance` (issus de `effects`)

Le JSON est produit via :
- `jsonEncode` (formatter `pure`)

---

## Prochaine étape (couche B)
Créer un mapping déclaratif (par `effectId` + contexte) vers :
- bonus KrosmozJDR (stats plates),
- ou création/liaison de `Capability`.

Cette étape pourra être faite sans casser la collect/conversion, car la normalisation conserve `raw`.

---

## Couche B (première implémentation)
Une première version du mapping couche B est en place pour les **items** :
- Formatter : `mapDofusdbEffectsToKrosmozBonuses`
- Stockage : `Item.effect` (JSON)

Il produit un payload structuré :
- `stats` (force/intel/agi/chance/vitality/sagesse)
- `res_percent` (terre/feu/air/eau/neutre) — basé sur les IDs DofusDB 210-219
- `res_fixed` (terre/feu/air/eau/neutre) — basé sur les IDs DofusDB 240-249
- `damage_fixed` (élémentaire) — heuristique basée sur (category=2, characteristic=0, elementId 0..4)
- `unmapped` pour itérer (audit)

### Sorts (pack couche A + B dans un seul champ)
Pour éviter une migration DB immédiate, les sorts packent couche A + couche B :
- Formatter : `packDofusdbEffects`
- Stockage : `Spell.effect` (JSON) avec la forme :
  - `{ normalized: [...], bonuses: {...} }`

