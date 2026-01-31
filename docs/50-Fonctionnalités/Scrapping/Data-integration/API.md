## API — Intégration (écriture en base)

### Objectif
L’intégration correspond à l’étape “écriture” du pipeline : prendre des données déjà collectées + converties et les appliquer à la base KrosmozJDR (create/update), en respectant :
- le flag `auto_update` (quand applicable),
- les règles anti-doublon,
- les options d’import (dry-run, force-update, images…),
- les relations (optionnel).

### Où l’intégration est exposée ?
Dans KrosmozJDR, on ne propose pas une “API d’intégration” isolée : elle est déclenchée via les endpoints d’import :
- `POST /api/scrapping/import/<type>/{id}`
- `POST /api/scrapping/import/batch`
- `POST /api/scrapping/import/range`
- `POST /api/scrapping/import/all`

Voir :
- `docs/50-Fonctionnalités/Scrapping/Orchestrateur/API.md`

### Options d’intégration (communes)
Les options acceptées par l’API et propagées à l’intégration :
- `dry_run` (bool) : simule l’intégration **sans écriture DB**
- `force_update` (bool) : autorise l’écrasement si l’entité existe
- `with_images` (bool) : téléchargement/stockage des images (défaut `true`)
- `include_relations` (bool) : importe aussi les relations (défaut `true`)
- `skip_cache` (bool) : bypass cache HTTP côté collect
- `validate_only` (bool) : stoppe avant intégration (retourne raw/converted)

### Sémantique des retours (actions)
Les retours d’intégration incluent une notion d’“action” (utile en UI / logs), typiquement :
- `would_create` / `would_update` / `would_skip` (quand `dry_run=true`)
- `created` / `updated` / `skipped` (quand `dry_run=false`)

### Détection “existe déjà”
Pour éviter les collisions, l’intégration privilégie (quand disponible) :
1) la recherche par `dofusdb_id`,
2) puis la recherche par `name` en fallback.

### Lien avec le code
Implémentation principale :
- `app/Services/Scrapping/DataIntegration/DataIntegrationService.php`

