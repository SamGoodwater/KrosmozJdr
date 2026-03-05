# Architecture DofusDB des effets et conversion vers KrosmozJDR

Ce document décrit la structure des données **effets** côté DofusDB (tout en IDs), comment les récupérer, et comment envisager la conversion vers le modèle KrosmozJDR (Effect, SubEffect, EffectSubEffect, EffectUsage).

---

## 0. Résumé : pipeline DofusDB → KrosmozJDR (sorts)

**Modèle DofusDB** : un **spell** est lié à des **spell-level(s)** qui le décrivent. Dans chaque spell-level on trouve une table d’**effets spécifiques** (instances). Chaque instance a sa **zone** (`zoneDescr`) ; côté KrosmozJDR on assume une zone par niveau en prenant la **première** zone valide parmi les effets du niveau. Chaque instance renvoie vers un **effet généraliste** (`GET /effects/{effectId}`) qui décrit l’action (retirer PO, dommages eau, etc.) — cela correspond aux **sous-effets** KrosmozJDR.

**KrosmozJDR dispose bien de la pipeline complète** pour créer des sorts avec leurs effets à partir de l’API DofusDB, en convertissant les valeurs selon les règles définies au niveau des caractéristiques (groupe spell) :

| Étape | Service / source | Rôle |
|-------|------------------|------|
| **Collecte** | `CollectService` | `GET /spells/{id}` puis `GET /spell-levels?spellId=…&$sort=grade` → `raw` + `raw['levels']` |
| **Conversion (propriétés sort)** | `ConversionService` + `spell.json` / `scrapping_entity_mappings` | Propriétés du sort (pa, po_min, po_max, name, sight_line, area au niveau spell, etc.) depuis `levels.0.*` |
| **Conversion (effets)** | `SpellEffectsConversionService` | Pour chaque niveau : instances `effects[]` → effectId → `dofusdb_effect_mappings` → sous-effet Krosmoz ; zone = première `zoneDescr` du niveau ; `value_formula`, `value_converted`, `dice_formula` via règles **characteristic_spell** (Phase 3) |
| **Validation** | `CharacteristicLimitService` | Clamp et validation des données converties |
| **Intégration** | `IntegrationService::integrateSpell` + `integrateSpellEffectsForSpell` | Création/mise à jour du **Spell**, puis **EffectGroup**, **Effect** (par grade), **EffectSubEffect** (params dont value_converted), **EffectUsage** |

Les valeurs numériques des effets (dégâts, soins, bonus PO/PO, etc.) sont converties selon les **formules de conversion** et **convertToDice** définies dans `characteristic_spell` (voir `SpellEffectConversionFormulaResolver`, `DofusConversionService`).

---

## 1. Architecture DofusDB : tout est en IDs

### 1.1 Sorts : niveaux non embarqués

- **`GET /spells/{id}?lang=fr`** ne contient **pas** les effets en ligne.
- Le sort renvoie notamment : `id`, `name`, `description`, `img`, `typeId`, **`spellLevels`** = tableau d’**IDs de niveaux** (ex. `[1001, 1002, 1003, 1004, 1005, 10642]` pour le sort 201).

Les effets sont donc sur les **spell-levels**, pas sur le sort lui-même.

### 1.2 Spell-levels : instances d’effets par niveau

- **`GET /spell-levels?spellId=…&$sort=grade&lang=fr`** renvoie la liste des niveaux (fusionnée dans `raw['levels']`). Chaque niveau contient : portée = **`minRange`** et **`range`** (deux champs séparés), **`castTestLos`** (ligne de vue). Détails :
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
| `zoneDescr`     | Forme de zone (shape, param1, param2…) ; shape 80 = 1 case (CAC) |

Pour les chemins exacts (levels.0.minRange, levels.0.range, levels.0.castTestLos, etc.) et exemples JSON, voir [DOFUSDB_API_SPELLS_REFERENCE.md](./DOFUSDB_API_SPELLS_REFERENCE.md).

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
- **Sous-effet de repli « autre »** : tout `effectId` non présent dans le mapping est converti en sous-effet **`autre`** (slug `DofusDbEffectMapping::SUB_EFFECT_SLUG_OTHER`). Ce sous-effet ne prend pas de caractéristique ; ses params contiennent `value_formula` (dés/valeur de l’instance), `value` (description DofusDB du dictionnaire `/effects/{id}` pour affichage et sous-effets personnalisés) et éventuellement `value_formula_crit`. Cela permet de ne rien perdre à l’import et de créer ensuite des sous-effets spéciaux avec la description en valeur.
- **Résolution de characteristic pour `characteristic_source=characteristic`** : la conversion utilise en priorité `characteristic_key` de `dofusdb_effect_mappings`. Si la clé est absente, elle est déduite depuis `GET /effects/{id}.characteristic` via les caractéristiques BDD (groupe `spell`), puis en fallback via `resources/scrapping/config/sources/dofusdb/dofusdb_characteristic_to_krosmoz_spell.json`. Cela évite de perdre `value_converted` sur des mappings partiels.

### Commande de rattrapage des mappings existants

Pour corriger en base les lignes `dofusdb_effect_mappings` avec `characteristic_source=characteristic` et `characteristic_key` vide :

```bash
php artisan scrapping:effects:backfill-characteristics --dry-run
php artisan scrapping:effects:backfill-characteristics --ids=116,117
```

La commande résout la clé via `GET /effects/{id}.characteristic`, puis via les caractéristiques `spell` en BDD, avec fallback sur `dofusdb_characteristic_to_krosmoz_spell.json`.

Pour prioriser les corrections restantes, un rapport est disponible :

```bash
php artisan scrapping:effects:report-missing-characteristics --limit=20
php artisan scrapping:effects:report-missing-characteristics --json > storage/app/scrapping_missing_characteristics_report.json
```

Le rapport regroupe les lignes manquantes par `characteristic` DofusDB et les trie par fréquence (avec exemples d`effectId`).

### Audit qualité global (robustesse conversion)

Pour auditer en une commande la qualité du système (couverture mapping + sous-effets sans `value_converted`) :

```bash
php artisan scrapping:effects:audit-quality
php artisan scrapping:effects:audit-quality --json > storage/app/scrapping_effects_quality_audit.json
```

L'audit remonte:
- les mappings `characteristic_source=characteristic` sans `characteristic_key`,
- les sous-effets de sorts qui devraient avoir `value_converted` (selon `SpellEffectConversionFormulaResolver`) mais ne l'ont pas.

### Quality gate CI (seuils bloquants)

Pour bloquer automatiquement un pipeline CI si la qualité est insuffisante:

```bash
php artisan scrapping:effects:quality-gate
php artisan scrapping:effects:quality-gate --allow-empty
php artisan scrapping:effects:quality-gate --min-coverage=99.5 --max-missing-mappings=0 --max-missing-value-converted=0 --json
```

Comportement:
- la commande relance `scrapping:effects:audit-quality --json`,
- échoue (`exit code 1`) si un seuil est dépassé,
- échoue aussi si `conversion_expected_rows=0` (base vide) sauf si `--allow-empty` est fourni,
- retourne un JSON exploitable en CI via `--json`.

Raccourcis disponibles via la commande projet `run`:

```bash
php artisan run --check:effects-quality
php artisan run --check:effects-quality:dev
```

- `--check:effects-quality` = mode strict (échoue si base d'effets vide),
- `--check:effects-quality:dev` = mode dev (autorise base vide via `--allow-empty`).

### Pipeline import + gate (enchaînement unique)

Pour lancer un lot de sorts puis contrôler automatiquement la qualité:

```bash
php artisan scrapping:effects:pipeline --max-items=300 --limit=100
php artisan scrapping:effects:pipeline --ids=201,202,203 --allow-empty --json
```

La commande enchaîne:
1) `scrapping:run --entity=spell ...`
2) `scrapping:effects:quality-gate ...`

Raccourcis via `run`:

```bash
php artisan run --pipeline:effects-quality --max-items=300
php artisan run --pipeline:effects-quality:dev --max-items=300 --simulate
```

L’orchestrateur ou l’intégration peut injecter `SpellEffectsConversionService`, récupérer les spell-levels (après collecte du sort), appeler `convert()`, puis créer en BDD EffectGroup, Effects, EffectSubEffects et EffectUsages à partir du résultat.
