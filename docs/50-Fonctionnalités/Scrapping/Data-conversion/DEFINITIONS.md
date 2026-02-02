## Définitions — Conversion “config-driven” (Scrapping)

### Objectif
La conversion “config-driven” décrit **comment transformer les données DofusDB** en un payload compatible KrosmozJDR à partir de :
- mappings déclaratifs (JSON),
- formatters whitelistés (registry),
- conventions (multi-langue, types d’items, images).

Le but est d’avoir une conversion **répétable**, **testable**, et **sans logique ad-hoc dispersée**.

---

## Source de vérité : configurations JSON
Les configs vivent dans :
- `resources/scrapping/sources/dofusdb/entities/<entity>.json`

Une config décrit (entre autres) :
- **Collect** : `fetchOne`, `fetchMany`, `supportedFilters`
- **Conversion** : `mappings[]`
- **Métadonnées** : `meta` (ex: `maxId`)

---

## Structure d’un mapping (source → KrosmozJDR)
Un mapping décrit :
- **from.path** : où lire la valeur dans l’objet source (dot-notation)
- **to.model / to.field** : où écrire la valeur côté KrosmozJDR
- **formatters[]** : transformations appliquées dans l’ordre

Exemple (illustratif) :

```json
{
  "from": { "path": "name" },
  "to": { "model": "monsters", "field": "name" },
  "formatters": [
    { "name": "pickLang", "args": { "lang": "fr", "fallback": "en" } },
    { "name": "truncate", "args": { "max": 255 } }
  ]
}
```

### Dot-notation (`from.path`)
Exemples :
- `name.fr`
- `type.id`
- `img`
- `effects` (array)

> Les cas complexes (arrays avec `[]`, map/flatten, etc.) doivent rester explicites, revus et couverts par des tests, pour éviter les surprises.

---

## Formatters (registry)
La liste blanche des formatters est dans :
- `resources/scrapping/formatters/registry.json`

Elle est appliquée/validée par :
- `app/Services/Scrapping/Config/FormatterRegistry.php`

Chaque formatter est décrit par :
- `name` : identifiant
- `type` : `pure` ou `side_effect`
- `argsSchema` : forme des arguments autorisés

### Formatters disponibles (DofusDB)
#### `pickLang` (pure)
- **But** : extraire une valeur multilingue
- **Args** : `{ lang: string, fallback: string }`

#### `toString` (pure)
- **But** : convertir en string (ou `''` si null)

#### `toInt` (pure)
- **But** : convertir en int (ou `0` si invalide)

#### `nullableInt` (pure)
- **But** : convertir en int ou `null`

#### `clampInt` (pure)
- **But** : borner un int
- **Args** : `{ min: number, max: number }`

#### `truncate` (pure)
- **But** : tronquer une string
- **Args** : `{ max: number }`

#### `mapSizeToKrosmoz` (pure)
- **But** : normaliser une taille (tiny/small/medium/large/huge) au format attendu KrosmozJDR
- **Args** : `{ default: string }`

#### `mapDofusdbItemType` (pure)
- **But** : mapper un `typeId` DofusDB vers un type KrosmozJDR
- **Dépendance** : registry DB des `resource_types` (ex: “allowed”)

#### `mapDofusdbItemCategory` (pure)
- **But** : mapper un `typeId` DofusDB vers une catégorie KrosmozJDR
- **Dépendance** : registry DB des `resource_types`

#### `storeScrappedImage` (side_effect)
- **But** : télécharger et stocker une image distante
- **Args** : `{ entityFolder: string, idPath: string }`
- **Contrôlé par** : option `with_images` (import) + config scrapping images

#### `normalizeDofusdbEffects` (pure)
- **But** : normaliser une liste d’effets DofusDB (sorts/items) dans un format stable (`EffectInstance`)
- **Args** : `{ sourceType: string, includeRaw: boolean }`
- **Sortie** : `array<EffectInstance>`

#### `jsonEncode` (pure)
- **But** : encoder une valeur (array/object/…) en JSON pour stockage dans une colonne string (compat immédiate)
- **Args** : `{ pretty: boolean }`

#### `mapDofusdbEffectsToKrosmozBonuses` (pure)
- **But** : convertir une liste d’effets DofusDB (items/spell-levels) en un payload “bonus” KrosmozJDR (stats, résistances, dommages).
- **Args** : `{ lang: string }`
- **Sortie** : objet structuré avec `stats`, `res_percent`, `res_fixed`, `damage_fixed`, `unmapped`.

#### `packDofusdbEffects` (pure)
- **But** : regrouper couche A (normalisation) et couche B (bonus) dans un seul objet (utile quand on n’a qu’un champ string disponible).
- **Args** : `{ sourceType: string, includeRaw: boolean, lang: string }`
- **Sortie** : `{ normalized: EffectInstance[], bonuses: {...} }`

---

## Conversion côté code (points d’entrée)
Le point d’entrée “conversion config-driven” est :
- Conversion pilotée par config : `App\Services\Scrapping\Core\Conversion\ConversionService` (pipeline Core).

Il est utilisé par :
- `App\Services\Scrapping\Core\Orchestrator\Orchestrator`

Notes :
- Les formatters `side_effect` doivent rester rares et explicites (principalement images).

