## Mapping DofusDB → KrosmozJDR (configs JSON)

### Objectif
Centraliser le lien entre :
- **l’API DofusDB** (champs / structures renvoyées),
- **les propriétés KrosmozJDR** (modèles + champs),
- **les fonctions de conversion** (formatters).

**Source de vérité** : `resources/scrapping/sources/dofusdb/entities/*.json`

> Si un mapping n’est pas listé ici, il doit être ajouté d’abord dans la config JSON (puis documenté).

---

## Conventions
- **from.path** : chemin DofusDB (dot-notation)
- **to** : `{ model, field }` côté KrosmozJDR (un mapping peut écrire dans plusieurs modèles)
- **formatters** : suite de formatters whitelistés (voir `resources/scrapping/formatters/registry.json`)

---

## Entité `monster`
### Endpoints DofusDB
- `GET /monsters/{id}?lang=fr`
- `GET /monsters?lang=fr&...&$limit=...&$skip=...`

### Mapping (exhaustif selon config)
| DofusDB (`from.path`) | KrosmozJDR (`to.model.field`) | Formatters |
| --- | --- | --- |
| `id` | `monsters.dofusdb_id` | `toString` |
| `name` (multi-langue) | `creatures.name` | `pickLang(lang={lang}, fallback=fr)` |
| `grades.0.level` | `creatures.level` | `toInt` → `clampInt(1..200)` |
| `grades.0.lifePoints` | `creatures.life` | `toInt` → `clampInt(1..10000)` |
| `grades.0.strength` | `creatures.strength` | `toInt` → `clampInt(0..1000)` |
| `grades.0.intelligence` | `creatures.intelligence` | `toInt` → `clampInt(0..1000)` |
| `grades.0.agility` | `creatures.agility` | `toInt` → `clampInt(0..1000)` |
| `grades.0.wisdom` | `creatures.wisdom` | `toInt` → `clampInt(0..1000)` |
| `grades.0.chance` | `creatures.chance` | `toInt` → `clampInt(0..1000)` |
| `img` | `creatures.image` | `storeScrappedImage(entityFolder=monsters, idPath=id)` |
| `size` | `monsters.size` | `mapSizeToKrosmoz(default=medium)` |
| `race` | `monsters.monster_race_id` | `nullableInt` |

### Relations (config)
- **spells** (par défaut) : `monster.spells[]` → import + lien vers entité `spell`
- **drops** (par défaut) : `monster.drops[]` → import + lien vers entité `resource` (itemId)
  - filtrage possible basé sur `resource_types.allowed` (DB)

---

## Entité `item`
### Endpoints DofusDB
- `GET /items/{id}?lang=fr`
- `GET /items?lang=fr&...&$limit=...&$skip=...`

### Mapping (exhaustif selon config)
| DofusDB (`from.path`) | KrosmozJDR (`to.model.field`) | Formatters |
| --- | --- | --- |
| `id` | `items.dofusdb_id` | `toString` |
| `name` (multi-langue) | `items.name` | `pickLang(lang={lang}, fallback=fr)` |
| `description` (multi-langue) | `items.description` | `pickLang(lang={lang}, fallback=fr)` → `truncate(255)` |
| `level` | `items.level` | `toInt` → `clampInt(1..200)` |
| `typeId` | `items.type_id` | `toInt` |
| `typeId` | `items.type` | `mapDofusdbItemType` |
| `typeId` | `items.category` | `mapDofusdbItemCategory` |
| `rarity` | `items.rarity` | `toString` |
| `price` | `items.price` | `toInt` |
| `img` | `items.image` | `storeScrappedImage(entityFolder=items, idPath=id)` |
| `recipe` | `items.recipe` | _(aucun)_ |
| `effects[]` | `items.bonus` | `normalizeDofusdbEffects(sourceType=item, includeRaw=true)` → `jsonEncode(pretty=false)` |

### Notes (type / category / table finale)
L’entité DofusDB “item” est ensuite intégrée dans **une table KrosmozJDR** (`items`, `resources`, `consumables`) en fonction de :
- `typeId` DofusDB,
- la registry DB `resource_types` (ex: liste “allowed”).

---

## Entité `spell`
### Endpoints DofusDB
- `GET /spells?lang=fr&...&$limit=...&$skip=...`
- `GET /spell-levels?lang=fr&...&$limit=...&$skip=...` (levels)

### Mapping (exhaustif selon config)
| DofusDB (`from.path`) | KrosmozJDR (`to.model.field`) | Formatters |
| --- | --- | --- |
| `id` | `spells.dofusdb_id` | `toString` |
| `name` (multi-langue) | `spells.name` | `pickLang(lang={lang}, fallback=fr)` |
| `description` (multi-langue) | `spells.description` | `pickLang(lang={lang}, fallback=fr)` → `truncate(255)` |
| `img` | `spells.image` | `storeScrappedImage(entityFolder=spells, idPath=id)` |
| `breedId` | `spells.class` | `nullableInt` |
| `cost` | `spells.cost` | `toInt` → `clampInt(0..20)` |
| `range` | `spells.range` | `toInt` → `clampInt(0..50)` |
| `area` | `spells.area` | `toInt` → `clampInt(0..50)` |
| `levels.0.effects[]` | `spells.effect` | `normalizeDofusdbEffects(sourceType=spell_level, includeRaw=true)` → `jsonEncode(pretty=false)` |

### Relations (config)
- **summon** (par défaut) : détection des invocations via un “detector” (niveaux de sorts) → import + lien vers `monster`

---

## Entités avec mapping minimal (à compléter)
Certaines entités sont déclarées pour collect/search/import mais leur mapping reste volontairement minimal (dofusdb_id seulement) :
- `class` → `classes.dofusdb_id`
- `panoply` → `panoplies.dofusdb_id`
- `effect` → `effects.dofusdb_id`

> Prochaine étape de refonte : compléter ces mappings dans les configs JSON, puis enrichir ce document.

