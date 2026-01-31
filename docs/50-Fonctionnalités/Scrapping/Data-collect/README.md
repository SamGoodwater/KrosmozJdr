# Service Data-collect (Collect)

### Objectif
La couche **Data-collect** récupère des données brutes depuis DofusDB (ou, à terme, d’autres sources) et les expose au reste du système.

Elle ne fait **ni conversion**, ni **écriture en base** : elle renvoie des objets “raw” (ou des listes paginées).

### Source of truth : configs JSON
La collecte est **pilotée par configuration** :
- source : `resources/scrapping/sources/dofusdb/source.json`
- entités : `resources/scrapping/sources/dofusdb/entities/*.json`

Ces fichiers définissent :
- l’endpoint `fetchOne`,
- l’endpoint `fetchMany`,
- les filtres supportés (ex: `id`, `ids`, `idMin`, `idMax`, `name`, `typeId`, etc.),
- la manière de convertir les filtres vers la query Feathers.

### Implémentation (backend)
Les composants principaux côté code :
- `app/Services/Scrapping/Http/DofusDbClient.php` : HTTP + cache + retry + timeout
- `app/Services/Scrapping/DataCollect/ConfigDrivenDofusDbCollector.php` : `fetchOne()` / `fetchManyResult()` (pagination Feathers)
- `app/Services/Scrapping/DataCollect/DataCollectService.php` : façade + fallback legacy

### Pagination et limites
DofusDB utilise une pagination de type Feathers (`$limit/$skip`) et **cappe souvent la page à 50 items**.

Règle importante :
- Toujours avancer le `$skip` avec le `limit` réellement renvoyé (`resp.limit`) et pas avec le `$limit` demandé.

Voir :
- `Data-collect/API.md` (référence API DofusDB)

### Endpoints KrosmozJDR (collect-only)
Pour alimenter l’UI / les tests, on expose une recherche générique :
- `GET /api/scrapping/search/{entity}`

Elle s’appuie sur la config JSON + `ConfigDrivenDofusDbCollector` et accepte notamment :
- filtres : `id`, `ids`, `idMin`, `idMax`, `name`, `typeId`… (selon entité)
- pagination : `limit`, `start_skip`, `max_pages`, `max_items`
- options : `skip_cache`

### CLI
Pour tester la collect (sans intégration), utiliser la commande unifiée :
- `php artisan scrapping --collect=<entity> ...`

