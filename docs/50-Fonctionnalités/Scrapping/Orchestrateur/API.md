## API Scrapping (Laravel) — Endpoints exposés

### Objectif
Cette page documente l’API interne utilisée par :
- l’UI `/scrapping`,
- la commande `php artisan scrapping`,
- les tests.

Elle couvre :
- endpoints **collect/search** (sans écriture),
- endpoints **preview**,
- endpoints **import** (écriture DB).

> Base : routes déclarées dans `routes/api.php` (préfixe `/api/scrapping`).

---

## Authentification / sécurité (contexte)
Les routes API scrapping sont des routes Laravel “internes” :
- appelées depuis le back-office (session/cookies + protections usuelles du projet),
- pas de JWT imposé par cette doc (éviter les mentions génériques non alignées).

---

## Métadonnées et configuration
### Métadonnées entités (limites + labels)
```http
GET /api/scrapping/meta
```

Retour :
- `data[]` avec `type`, `label`, `maxId`

### Config scrapping (sources + entités)
```http
GET /api/scrapping/config
```

But :
- exposer côté UI la liste des entités configurées (JSON) et leurs capacités.

---

## Collect / Search (sans écriture)
### Recherche générique (config-driven)
```http
GET /api/scrapping/search/{entity}
```

`{entity}` correspond aux entités DofusDB supportées (ex: `monster`, `item`, `spell`, `class`, `panoply`, `effect`).

Paramètres (principaux) :
- **Filtres (selon entité)** :
  - `id` (int)
  - `ids` (csv) : `1,2,3`
  - `idMin` / `idMax` (int) : bornes sur l’id
  - `name` (string) : recherche texte (convertie en `name[$search]`)
  - `typeId`, `raceId`, `breedId`, `levelMin`, `levelMax` (selon entité)
- **Pagination / perf**
  - `limit` (int) : taille de page demandée
  - `start_skip` (int) : offset initial
  - `max_pages` (int) : nombre max de pages (0 = illimité côté backend)
  - `max_items` (int) : nombre max d’items à accumuler (0 = illimité côté backend)
- **Options**
  - `skip_cache` (bool)

Notes importantes :
- DofusDB **cappe** souvent `limit` à 50 → le backend renvoie `meta.limit` (le limit effectif).
- Le search ajoute aussi des infos “existe déjà” quand possible (champ `exists`).

---

## Preview (collect + conversion)
### Prévisualisation d’une entité
```http
GET /api/scrapping/preview/{type}/{id}
```

But :
- obtenir un aperçu **raw + converted** (et souvent **existing** si déjà en DB),
- préparer une UX de comparaison/validation.

---

## Import (collect + conversion + intégration)
### Import unitaire
```http
POST /api/scrapping/import/class/{id}
POST /api/scrapping/import/monster/{id}
POST /api/scrapping/import/item/{id}
POST /api/scrapping/import/resource/{id}
POST /api/scrapping/import/consumable/{id}
POST /api/scrapping/import/spell/{id}
POST /api/scrapping/import/panoply/{id}
```

Options (query ou body, selon usage UI) :
- `skip_cache` (bool)
- `force_update` (bool)
- `dry_run` (bool)
- `validate_only` (bool)
- `with_images` (bool, défaut `true`)
- `include_relations` (bool, défaut `true`)

### Import batch (IDs)
```http
POST /api/scrapping/import/batch
```

Payload (exemple) :
```json
{
  "entities": [
    { "type": "monster", "id": 31 },
    { "type": "monster", "id": 32 }
  ],
  "options": {
    "dry_run": true,
    "force_update": false,
    "with_images": true,
    "include_relations": true,
    "skip_cache": false,
    "validate_only": false
  }
}
```

### Import range
```http
POST /api/scrapping/import/range
```

### Import complet (1..maxId)
```http
POST /api/scrapping/import/all
```

---

## CLI (commande unifiée)
Pour les usages CLI (collect/search/import), voir :
- `app/Console/Commands/ScrappingCommand.php`

Exemples :
```bash
# Search (collect-only)
php artisan scrapping --collect=monster --name="Bouftou" --limit=50 --max-pages=2 --json

# Import (écriture DB)
php artisan scrapping --import=monster --ids=31,32 --with-images --include-relations

# Simulation
php artisan scrapping --import=item --id=15 --dry-run --skip-cache
```

