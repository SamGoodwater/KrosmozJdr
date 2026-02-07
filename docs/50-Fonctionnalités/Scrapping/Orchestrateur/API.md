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

`{entity}` correspond aux entités DofusDB supportées (ex: `monster`, `spell`, `class`, `panoply`, `effect`)
et aux variantes “items” pilotées par nos registries :
- `item` (liste globale DofusDB `/items`)
- `equipment` (items hors ressources/consommables)
- `resource` (items dont le `typeId` est autorisé comme ressource)
- `consumable` (items dont le `typeId` est autorisé comme consommable)

Paramètres (principaux) :
- **Filtres (selon entité)** :
  - `id` (int)
  - `ids` (csv) : `1,2,3`
  - `idMin` / `idMax` (int) : bornes sur l’id
  - `name` (string) : recherche texte (convertie en `name[$search]`)
  - `typeId`, `typeIds`, `typeIdsNot`, `raceId`, `breedId`, `levelMin`, `levelMax` (selon entité)
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
- `include_relations` (bool, défaut `true`) : si `true`, après intégration de l’entité principale (ex. monster), le service résout et importe les relations (sorts, drops, recettes, etc.) puis met à jour les tables de liaison. Si `false`, seul l’entité principale est importée.

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
# Recherche + import (par défaut)
php artisan scrapping --entity=monster --name="Bouftou" --limit=50 --max-pages=2 --json

# Import plusieurs entités à la suite
php artisan scrapping --entity=monster,item --ids=31,32 --include-relations

# Simulation (sans écriture en base)
php artisan scrapping --entity=item --id=15 --simulate --skip-cache
```

