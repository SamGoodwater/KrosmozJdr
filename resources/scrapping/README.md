## Scrapping configs (JSON)

### Objectif
Ces fichiers décrivent, **par source**, les entités scrappables, leurs endpoints, filtres, relations, et le mapping **source → KrosmozJDR** (avec formatters).

Ils sont **versionnés** et chargés par le backend avec une liste blanche de formatters.
Les endpoints, filtres et métadonnées restent dans les JSON. Le mapping actif est éditable
en BDD puis exporté dans `database/seeders/data/scrapping_entity_mappings.php`.

### Arborescence
- `resources/scrapping/config/sources/<source>/source.json`
- `resources/scrapping/config/sources/<source>/entities/<entity>.json`

### Conventions
- Schéma minimal obligatoire : `version` (entier positif), `source`, `entity`, `endpoints` et `target`.
- **Pas d’expressions libres** dans les formatters : uniquement `{ name, args }`, validés côté backend.
- Les transformations numériques utilisent `convertCharacteristic` et la `characteristic_key` liée.
- **`from.path`** utilise une dot-notation (avec support futur pour `[]` sur arrays).
- **`to.model`** + **`to.field`** permettent de cibler les modèles KrosmozJDR (y compris multi-modèles, ex: `creatures` + `monsters`).

### Fichiers exemples
Voir `resources/scrapping/config/sources/dofusdb/`.

