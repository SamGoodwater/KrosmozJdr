## Table de couverture — Props KrosmozJDR vs DofusDB

### Objectif
Cette table sert à décider **quoi convertir** et **comment** :
- si une propriété KrosmozJDR a un équivalent DofusDB,
- si elle est dérivable (avec règles),
- ou si elle est “Krosmoz-only” (édition / gameplay JDR / workflow).

Colonnes :
- **Krosmoz field** : champ côté modèle KrosmozJDR
- **DofusDB source** : chemin(s) / endpoint(s) DofusDB
- **Statut**
  - ✅ **Direct** : 1→1 (collectable)
  - 🟨 **Dérivable** : nécessite conversion/agrégation/choix
  - ❌ **Krosmoz-only** : pas de source DofusDB fiable/pertinente
- **Conversion (formatters / règles)** : suggestion “config-driven”
- **Notes**

> Source-of-truth des mappings actuels : `resources/scrapping/sources/dofusdb/entities/*.json`

---

## `Spell` (`app/Models/Entity/Spell.php`)
Source DofusDB :
- fiche : `/spells`
- gameplay : `/spell-levels` (via `levels.0.*` dans notre raw)

| Krosmoz field | DofusDB source | Statut | Conversion (formatters / règles) | Notes |
| --- | --- | --- | --- | --- |
| `dofusdb_id` | `spells.id` | ✅ Direct | `toString` | Identifiant externe |
| `name` | `spells.name.{lang}` | ✅ Direct | `pickLang` | Multi-langue |
| `description` | `spells.description.{lang}` | ✅ Direct | `pickLang` → `truncate(255)` | |
| `image` | `spells.img` | ✅ Direct | `storeScrappedImage` (side-effect) | Peut rester URL en preview |
| `pa` | `spell-levels.apCost` (ex: `levels.0.apCost`) | 🟨 Dérivable | `toInt` → clamp, puis cast string côté intégration | Choix du grade/level |
| `po` | `spell-levels.range` (ex: `levels.0.range`) | 🟨 Dérivable | `toInt` → clamp, puis cast string | Choix du grade/level |
| `area` | `spell-levels.effects.0.zoneDescr.shape` | 🟨 Dérivable | `nullableInt` | À affiner (zone ≠ “area” Krosmoz) |
| `effect` | `spell-levels.effects[]` + `/effects/{effectId}` | 🟨 Dérivable | `packDofusdbEffects(sourceType=spell_level)` → `jsonEncode` | JSON `{normalized, bonuses}` (couche A+B) |
| `level` | (pas 1→1) | 🟨 Dérivable | règle à définir | Krosmoz ≠ grades Dofus |
| `official_id` | — | ❌ Krosmoz-only | — | |
| `category` | (possible via `spells.typeId` / taxonomy) | 🟨 Dérivable | mapping à créer | |
| `element` | (possible via `effectElement` / elementId) | 🟨 Dérivable | mapping à créer | |
| `po_editable` | — | ❌ Krosmoz-only | — | |
| `cast_per_turn` | — | ❌ Krosmoz-only | — | |
| `cast_per_target` | — | ❌ Krosmoz-only | — | |
| `sight_line` | — | ❌ Krosmoz-only | — | |
| `number_between_two_cast` | — | ❌ Krosmoz-only | — | |
| `number_between_two_cast_editable` | — | ❌ Krosmoz-only | — | |
| `is_magic` | — | ❌ Krosmoz-only | — | |
| `powerful` | — | ❌ Krosmoz-only | — | |
| `state` | — | ❌ Krosmoz-only | — | Workflow Krosmoz |
| `read_level` / `write_level` | — | ❌ Krosmoz-only | — | Permissions |
| `auto_update` | — | ❌ Krosmoz-only | — | Gouvernance |
| `created_by` | — | ❌ Krosmoz-only | — | |

---

## `Item` (`app/Models/Entity/Item.php`)
Source DofusDB :
- `/items/{id}`

| Krosmoz field | DofusDB source | Statut | Conversion (formatters / règles) | Notes |
| --- | --- | --- | --- | --- |
| `dofusdb_id` | `items.id` | ✅ Direct | `toString` | |
| `name` | `items.name.{lang}` | ✅ Direct | `pickLang` | |
| `description` | `items.description.{lang}` | ✅ Direct | `pickLang` → `truncate(255)` | |
| `level` | `items.level` | ✅ Direct | `toInt` → `clampInt(1..200)` | |
| `rarity` | `items.rarity` | ✅ Direct | `toString`/`toInt` selon DB | À harmoniser (string/int) |
| `price` | `items.price` | ✅ Direct | `toInt` | |
| `recipe` | `items.recipe` | ✅ Direct | (aucun) | Structure complexe (à normaliser plus tard) |
| `image` | `items.img` | ✅ Direct | `storeScrappedImage` | |
| `bonus` | `items.effects[]` | 🟨 Dérivable | `normalizeDofusdbEffects(sourceType=item)` → `jsonEncode` | Stockage JSON (temporaire) |
| `effect` | `items.effects[]` + `/effects/{effectId}` | 🟨 Dérivable | `mapDofusdbEffectsToKrosmozBonuses(lang=fr)` → `jsonEncode` | Payload bonus structuré (stats/résistances/dommages + unmapped) |
| `item_type_id` | `items.typeId` → table `item_types` | 🟨 Dérivable | mapping + lookup DB | Dépend des types internes |
| `dofus_version` | — | ❌ Krosmoz-only | — | |
| `state` | — | ❌ Krosmoz-only | — | |
| `read_level` / `write_level` | — | ❌ Krosmoz-only | — | |
| `auto_update` | — | ❌ Krosmoz-only | — | |
| `created_by` | — | ❌ Krosmoz-only | — | |
| `official_id` | — | ❌ Krosmoz-only | — | |

---

## `Consumable` (`app/Models/Entity/Consumable.php`)
Source DofusDB :
- `/items/{id}` filtré par type/superType (c’est un “item” DofusDB)

| Krosmoz field | DofusDB source | Statut | Conversion (formatters / règles) | Notes |
| --- | --- | --- | --- | --- |
| `dofusdb_id` | `items.id` | ✅ Direct | `toString` | |
| `name` | `items.name.{lang}` | ✅ Direct | `pickLang` | |
| `description` | `items.description.{lang}` | ✅ Direct | `pickLang` → `truncate(255)` | |
| `effect` | `items.effects[]` | 🟨 Dérivable | `normalizeDofusdbEffects` → `jsonEncode` | À brancher quand on migre `consumable` en config dédiée |
| `level` | `items.level` | ✅ Direct | `toInt` → clamp | |
| `recipe` | `items.recipe` | ✅ Direct | (aucun) | |
| `price` | `items.price` | ✅ Direct | `toInt` | |
| `rarity` | `items.rarity` | ✅ Direct | `toInt` (si int) | |
| `image` | `items.img` | ✅ Direct | `storeScrappedImage` | |
| `consumable_type_id` | `items.typeId` → table `consumable_types` | 🟨 Dérivable | mapping + lookup DB | |
| `dofus_version` | — | ❌ Krosmoz-only | — | |
| `state`, `read_level`, `write_level`, `auto_update`, `created_by`, `official_id` | — | ❌ Krosmoz-only | — | |

---

## `Resource` (`app/Models/Entity/Resource.php`)
Source DofusDB :
- `/items/{id}` (ressources = sous-ensemble des items)

| Krosmoz field | DofusDB source | Statut | Conversion (formatters / règles) | Notes |
| --- | --- | --- | --- | --- |
| `dofusdb_id` | `items.id` | ✅ Direct | `toString` | |
| `name` | `items.name.{lang}` | ✅ Direct | `pickLang` | |
| `description` | `items.description.{lang}` | ✅ Direct | `pickLang` → `truncate(255)` | |
| `level` | `items.level` | ✅ Direct | `toInt` → clamp | |
| `price` | `items.price` | ✅ Direct | `toInt` | |
| `weight` | `items.realWeight` / `items.weight` | 🟨 Dérivable | `toInt` / `toString` | À confirmer sur endpoint item |
| `rarity` | `items.rarity` | ✅ Direct | `toInt` | |
| `image` | `items.img` | ✅ Direct | `storeScrappedImage` | |
| `resource_type_id` | `items.typeId` → table `resource_types` | 🟨 Dérivable | mapping + lookup DB | |
| `official_id` | — | ❌ Krosmoz-only | — | |
| `dofus_version`, `state`, `read_level`, `write_level`, `auto_update`, `created_by` | — | ❌ Krosmoz-only | — | |

---

## `Monster` + `Creature` (monstre = extension de créature)
Source DofusDB :
- `/monsters/{id}`

### `Monster` (`app/Models/Entity/Monster.php`)
| Krosmoz field | DofusDB source | Statut | Conversion (formatters / règles) | Notes |
| --- | --- | --- | --- | --- |
| `dofusdb_id` | `monsters.id` | ✅ Direct | `toString` | |
| `size` | `monsters.size` | ✅ Direct | `mapSizeToKrosmoz` puis `convertSizeToInt` à l’intégration | |
| `monster_race_id` | `monsters.race` | 🟨 Dérivable | `nullableInt` + validation existence | Dépend de la table `monster_races` |
| `is_boss` | (existe peut-être dans DofusDB) | 🟨 Dérivable | mapping à créer | Pas encore mappé |
| `boss_pa` | — | ❌ Krosmoz-only | — | |
| `dofus_version`, `auto_update`, `official_id`, `creature_id` | — | ❌ Krosmoz-only | — | `creature_id` est une relation interne |

### `Creature` (`app/Models/Entity/Creature.php`)
Beaucoup de champs “JDR” n’ont pas d’équivalent 1→1.  
Par contre, pour les monstres, DofusDB fournit des stats (via `grades.0.*`) exploitables.

| Krosmoz field | DofusDB source | Statut | Conversion (formatters / règles) | Notes |
| --- | --- | --- | --- | --- |
| `name` | `monsters.name.{lang}` | ✅ Direct | `pickLang` | |
| `level` | `monsters.grades.0.level` | ✅ Direct | `toInt` → clamp → cast string | |
| `life` | `monsters.grades.0.lifePoints` | ✅ Direct | `toInt` → clamp → cast string | |
| `strong` | `monsters.grades.0.strength` | ✅ Direct | `toInt` → clamp → cast string | Aujourd’hui via clés `strength` dans convertedData |
| `intel` | `monsters.grades.0.intelligence` | ✅ Direct | `toInt` → clamp → cast string | |
| `agi` | `monsters.grades.0.agility` | ✅ Direct | `toInt` → clamp → cast string | |
| `sagesse` | `monsters.grades.0.wisdom` | ✅ Direct | `toInt` → clamp → cast string | |
| `chance` | `monsters.grades.0.chance` | ✅ Direct | `toInt` → clamp → cast string | |
| `image` | `monsters.img` | ✅ Direct | `storeScrappedImage` | |
| `pa` | `monsters.grades.0.actionPoints` | ✅ Direct | `toInt` → `clampInt(0..20)` → cast string | Implémenté (mapping JSON + intégration) |
| `pm` | `monsters.grades.0.movementPoints` | ✅ Direct | `toInt` → `clampInt(0..20)` → cast string | Implémenté (mapping JSON + intégration) |
| `kamas` | `monsters.grades.0.kamas` | ✅ Direct | `toInt` → clamp → cast string | Implémenté (mapping JSON + intégration) |
| `po` | `monsters.grades.0.bonusRange` | ✅ Direct | `toInt` → `clampInt(0..50)` → cast string | Implémenté (bonus de portée) |
| `dodge_pa` | `monsters.grades.0.paDodge` | ✅ Direct | `nullableInt` → cast string | Implémenté (mapping JSON + intégration) |
| `dodge_pm` | `monsters.grades.0.pmDodge` | ✅ Direct | `nullableInt` → cast string | Implémenté (mapping JSON + intégration) |
| `vitality` | `monsters.grades.0.vitality` | ✅ Direct | `nullableInt` → cast string | Implémenté (mapping JSON + intégration) |
| `res_neutre` | `monsters.grades.0.neutralResistance` | ✅ Direct | `nullableInt` → cast string | Implémenté (mapping JSON + intégration) |
| `res_terre` | `monsters.grades.0.earthResistance` | ✅ Direct | `nullableInt` → cast string | Implémenté (mapping JSON + intégration) |
| `res_feu` | `monsters.grades.0.fireResistance` | ✅ Direct | `nullableInt` → cast string | Implémenté (mapping JSON + intégration) |
| `res_air` | `monsters.grades.0.airResistance` | ✅ Direct | `nullableInt` → cast string | Implémenté (mapping JSON + intégration) |
| `res_eau` | `monsters.grades.0.waterResistance` | ✅ Direct | `nullableInt` → cast string | Implémenté (mapping JSON + intégration) |
| (beaucoup d’autres champs) | — | ❌ Krosmoz-only | — | masteries/skills, workflow, etc. |

---

## `Classe` (`app/Models/Entity/Classe.php`)
Source DofusDB :
- `/breeds/{id}`

| Krosmoz field | DofusDB source | Statut | Conversion (formatters / règles) | Notes |
| --- | --- | --- | --- | --- |
| `dofusdb_id` | `breeds.id` | ✅ Direct | `toString` | |
| `name` | (incertain selon payload) | 🟨 Dérivable | `pickLang` si `name` existe | À vérifier : DofusDB expose surtout `description` |
| `description` | `breeds.description.{lang}` | ✅ Direct | `pickLang` | |
| `image` / `icon` | `breeds.*Artwork` / `iconId` (selon payload) | 🟨 Dérivable | mapping à définir | |
| `life`, `life_dice` | — | ❌ Non exposés par DofusDB pour breeds | — | Les classes DofusDB sont généralistes : noms, descriptions, illustrations, sorts liés, rôles. |
| `specificity` | `breeds.specificity` (si présent) | ✅ Direct | `pickLang` + truncate | |
| `state`, `read_level`, `write_level`, `auto_update`, `created_by`, `official_id`, `dofus_version` | — | ❌ Krosmoz-only | — | |

---

## `Panoply` (`app/Models/Entity/Panoply.php`)
Source DofusDB :
- `/item-sets/{id}`

| Krosmoz field | DofusDB source | Statut | Conversion (formatters / règles) | Notes |
| --- | --- | --- | --- | --- |
| `dofusdb_id` | `item-sets.id` | ✅ Direct | `toString` | |
| `name` | `item-sets.name.{lang}` (si présent) | 🟨 Dérivable | `pickLang` | Pas encore mappé en config |
| `description` | `item-sets.*` | 🟨 Dérivable | mapping à définir | |
| `bonus` | `item-sets.effects[]` / bonus sets | 🟨 Dérivable | `normalizeDofusdbEffects` → `jsonEncode` | À confirmer sur endpoint |
| `state`, `read_level`, `write_level`, `created_by` | — | ❌ Krosmoz-only | — | |

---

## “Effet” (dictionnaire) — `Effect` (si modèle DB)
Source DofusDB :
- `/effects/{id}`

Utilité :
- peu “Krosmoz” directement, mais très utile pour :
  - enrichir les `EffectInstance` (tooltip),
  - alimenter la future table de mapping couche B (`effectId` → bonus/capability).

