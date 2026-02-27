# Architecture DofusDB des effets et conversion vers KrosmozJDR

Ce document décrit la structure des données **effets** côté DofusDB (tout en IDs), comment les récupérer, et comment envisager la conversion vers le modèle KrosmozJDR (Effect, SubEffect, EffectSubEffect, EffectUsage).

---

## 1. Architecture DofusDB : tout est en IDs

### 1.1 Sorts : niveaux non embarqués

- **`GET /spells/{id}?lang=fr`** ne contient **pas** les effets en ligne.
- Le sort renvoie notamment : `id`, `name`, `description`, `img`, `typeId`, **`spellLevels`** = tableau d’**IDs de niveaux** (ex. `[1001, 1002, 1003, 1004, 1005, 10642]` pour le sort 201).

Les effets sont donc sur les **spell-levels**, pas sur le sort lui-même.

### 1.2 Spell-levels : instances d’effets par niveau

- **`GET /spell-levels/{levelId}?lang=fr`** (ex. `1001`) renvoie un niveau avec :
  - `id`, `spellId`, **`grade`** (degré 1, 2, 3…)
  - `apCost`, `minRange`, `range`, `maxCastPerTurn`, etc.
  - **`effects`** : tableau d’**instances d’effets**
  - **`criticalEffect`** : tableau pour le **critique**

Chaque élément de `effects[]` / `criticalEffect[]` contient notamment :

| Champ DofusDB   | Rôle |
|-----------------|------|
| **`effectId`**  | Référence au dictionnaire `/effects/{id}` |
| `order`         | Ordre d’application |
| **`diceNum`**   | Nombre de dés (ex. 13) |
| **`diceSide`**  | Faces du dé (ex. 18) |
| `value`         | Valeur fixe (souvent 0 si dés) |
| `effectElement` | Élément (parfois redondant avec le dictionnaire) |
| `zoneDescr`     | Forme de zone (shape, param1, param2…) |

### 1.3 Dictionnaire des effets : définition par ID

- **`GET /effects/{effectId}?lang=fr`** (ex. `98`) renvoie la **définition** :
  - `id`, **`characteristic`** (ID), **`category`** (ex. 2 = dégâts)
  - **`elementId`** : élément (0 neutre, 1 feu, 2 eau, 3 terre, 4 air — à valider)
  - `useDice`, `forceMinMax`, `description` (multilingue)

Exemple : effet 98 → category 2, elementId 4, description "dommages Air".

---

## 2. Chaîne de données pour la conversion

1. **`GET /spells/{id}`** → récupérer `spellLevels[]`.
2. Pour chaque niveau : **`GET /spell-levels/{levelId}`** → `grade`, `effects[]`, `criticalEffect[]`.
3. Pour chaque instance : **`GET /effects/{effectId}`** (via `DofusDbEffectCatalog::get()`) → interpréter (élément, catégorie) et décider du sous-effet Krosmoz.

Tout est identifié par ID : sort → niveau (spellLevels) → instance (effectId) → définition (/effects/{id}).

---

## 3. Modèle KrosmozJDR

- **Effect** : conteneur (nom, groupe, degree).
- **EffectSubEffect** : pivot avec order, params (characteristic, value_formula, value_formula_crit…), scope, crit_only.
- **SubEffect** : action (frapper, soigner, protéger, voler-vie, booster, retirer, voler-caracteristiques, invoquer, déplacer).
- **EffectUsage** : lien spell → effect avec level_min / level_max.

Pour un sort multi-grades : un **Effect** par grade (degree = grade), dans un même **EffectGroup**, et une **EffectUsage** par (spell_id, effect_id) avec level_min = level_max = grade.

---

## 4. Stratégie de conversion proposée

### 4.1 Flux

1. Collecte : récupérer le sort puis **chaque spell-level** (spellLevels[]).
2. Pour chaque spell-level (grade) :
   - Créer/récupérer **EffectGroup** (nom du sort).
   - Créer **Effect** (degree = grade, effect_group_id = groupe).
   - Pour chaque `effects[]` : appeler **DofusDbEffectCatalog::get(effectId)** ; mapper (effectId, category, elementId) → **SubEffect** + params (characteristic, value_formula) ; créer **EffectSubEffect** ; si entrée correspondante dans **criticalEffect[]**, remplir **value_formula_crit**.
   - Créer **EffectUsage** (spell, effect_id, level_min = level_max = grade).

### 4.2 Table de correspondance effectId → Krosmoz

- Fichier de config ou table : **dofusdb_effect_id** → **sub_effect_slug** + règle pour characteristic (from_element_id / from_characteristic_id).
- Convention **elementId** DofusDB → clé Krosmoz : 0 → neutre, 1 → feu, 2 → eau, 3 → terre, 4 → air (à valider).
- Exemple : effectId 98 (category 2, elementId 4) → **frapper** + characteristic **air** ; value_formula = `{diceNum}d{diceSide}` (ex. 13d18) ; value_formula_crit depuis criticalEffect (ex. 19d27).

### 4.3 Intégration code

- **Collecte** : étendre pour récupérer les spell-levels après le sort (N+1 appels ou endpoint bulk).
- **Conversion** : service "SpellToEffectConverter" utilisant DofusDbEffectCatalog + table de mapping effectId → SubEffect.
- **Intégration** : dans `integrateSpell`, après création du Spell, créer EffectGroup, Effects, EffectSubEffects, EffectUsages.

---

## 5. Résumé

| DofusDB | KrosmozJDR |
|---------|------------|
| /spells/{id} + spellLevels[] | Choix des grades à importer |
| /spell-levels/{levelId} (grade, effects[], criticalEffect[]) | Un Effect par grade + sous-effets |
| effects[].effectId + diceNum/diceSide/value | EffectSubEffect : order, params.value_formula, value_formula_crit |
| /effects/{effectId} (category, elementId, characteristic) | Mapping → SubEffect (slug) + params.characteristic |
| (spell_id, grade) | EffectUsage (spell, effect_id, level_min = level_max = grade) |

La conversion est faisable en s’appuyant sur les IDs et une **table/config de mapping effectId (+ category/elementId) → sub_effect_slug + règle characteristic**.

---

## 6. Sous-service dédié (implémentation)

Un **sous-service du service de conversion** est dédié à cette logique, pour garder le flux principal lisible et isoler la complexité.

- **Namespace** : `App\Services\Scrapping\Core\Conversion\SpellEffects`
- **Service principal** : **`SpellEffectsConversionService`**
  - Méthode **`convert(array $spellRaw, array $spellLevelsData, array $options)`** : prend le sort brut + la liste des spell-levels déjà récupérés, retourne un **`SpellEffectsConversionResult`** (effect_group + effects avec sub_effects, prêts pour l’intégration).
  - S’appuie sur **`DofusDbEffectCatalog`** pour résoudre chaque `effectId` et sur **`DofusDbEffectMapping`** pour le mapping effectId → sub_effect_slug + règle characteristic (ex. `element` = utiliser elementId).
- **DTO** : **`SpellEffectsConversionResult`** — expose `getEffectGroup()`, `getEffects()`, `hasEffects()`, `getEffectsCount()`.
- **Mapping** : **`DofusDbEffectMapping`** — classe statique avec `getSubEffectForEffectId(int)`, `elementIdToCharacteristicKey(?int)`. Les entrées effectId → [sub_effect_slug, characteristic_source] sont extensibles (constante pour l’instant, puis config ou BDD si besoin).

L’orchestrateur ou l’intégration peut injecter `SpellEffectsConversionService`, récupérer les spell-levels (après collecte du sort), appeler `convert()`, puis créer en BDD EffectGroup, Effects, EffectSubEffects et EffectUsages à partir du résultat.
