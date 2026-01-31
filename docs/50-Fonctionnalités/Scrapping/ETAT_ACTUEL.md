## État actuel — Scrapping (KrosmozJDR)

**Date** : 2026-01-30

### Ce qui est en place
- **Collect config-driven (DofusDB)** : `DofusDbClient` + `ConfigDrivenDofusDbCollector`
- **Configs JSON** (source + entités) : `resources/scrapping/sources/dofusdb/`
- **Registry de formatters** : `resources/scrapping/formatters/registry.json`
- **Endpoints API scrapping** :
  - `GET /api/scrapping/config`
  - `GET /api/scrapping/meta`
  - `GET /api/scrapping/search/{entity}`
  - `GET /api/scrapping/preview/{type}/{id}`
  - `POST /api/scrapping/import/*` (unitaire/batch/range/all)
- **UI admin** : `/scrapping`
- **CLI unifiée** : `php artisan scrapping`

### Points importants (DofusDB)
- Pagination Feathers (`$limit/$skip`) avec **cap fréquent à 50**.
- Le code avance le `$skip` en utilisant le **limit renvoyé par l’API** (`resp.limit`).

### Chantiers en cours (documentation / refonte)
- Documenter exhaustivement l’API DofusDB utilisée → `Data-collect/API.md`
- Formaliser le mapping DofusDB → KrosmozJDR par entité via configs JSON
- Documenter les formatters et la “whitelist” → `Data-conversion/DEFINITIONS.md`

