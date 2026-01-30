## Scrapping configs (JSON)

### Objectif
Ces fichiers décrivent, **par source**, les entités scrappables, leurs endpoints, filtres, relations, et le mapping **source → KrosmozJDR** (avec formatters).

Ils sont **versionnés** et destinés à être chargés/validés par le backend via un loader (schéma + liste blanche de formatters).

### Arborescence
- `resources/scrapping/sources/<source>/source.json`
- `resources/scrapping/sources/<source>/entities/<entity>.json`

### Conventions
- **Pas d’expressions libres** dans les formatters : uniquement `{ name, args }`, validés côté backend.
- **`from.path`** utilise une dot-notation (avec support futur pour `[]` sur arrays).
- **`to.model`** + **`to.field`** permettent de cibler les modèles KrosmozJDR (y compris multi-modèles, ex: `creatures` + `monsters`).

### Fichiers exemples
Voir `resources/scrapping/sources/dofusdb/`.

