# Fonctionnalité Scrapping — Index (KrosmozJDR)

### Objectif
Le scrapping permet de **collecter** des données depuis DofusDB, de les **convertir** via des règles/formatters, puis de les **intégrer** dans la base KrosmozJDR.

Cette documentation est alignée sur l’implémentation actuelle (approche **config-driven** via JSON).

### Pipeline (vue d’ensemble)
```
DofusDB (API)
  ↓
Collect (HTTP + pagination + cache)
  ↓
Conversion (mapping + formatters whitelistés)
  ↓
Intégration (DB + relations + images)
  ↓
KrosmozJDR
```

### Sources de vérité (config-driven)
- Configs JSON : `resources/scrapping/README.md` + `resources/scrapping/sources/dofusdb/entities/*.json`
- Registry des formatters : `resources/scrapping/formatters/registry.json`

### Documentation recommandée (ordre de lecture)
- **Système d’effets (sorts + équipements)**
  - `EFFECTS_SYSTEM.md` : dictionnaire vs instances, normalisation `EffectInstance`, stratégie de mapping vers Krosmoz
- **Couverture des props (quoi scrapper / quoi convertir)**
  - `MAPPING_COVERAGE_KROSMOZ_PROPS.md` : table “auto-complétable vs dérivable vs Krosmoz-only” par entité
- **Mapping DofusDB → KrosmozJDR**
  - `MAPPING_DOFUSDB_TO_KROSMOZJDR.md` : tables de mapping (champs + formatters) basées sur les configs JSON
- **Collect / API DofusDB**
  - `Data-collect/API.md` : syntaxe Feathers (`$limit/$skip/$search`, cap à 50, endpoints utilisés)
  - `Data-collect/README.md` : comment KrosmozJDR fait la collect (client + collector)
- **Conversion**
  - `Data-conversion/README.md` : conversion config-driven vs legacy
  - `Data-conversion/DEFINITIONS.md` : mapping JSON + formatters
- **Intégration**
  - `Data-integration/README.md` : règles d’écriture (dofusdb_id, auto_update, options)
  - `Data-integration/DEFINITIONS.md` : conventions d’intégration (tables cibles, relations)
- **Orchestration / API interne**
  - `Orchestrateur/README.md`
  - `Orchestrateur/API.md` : endpoints Laravel `/api/scrapping/*` + commande `php artisan scrapping`

### UI (admin)
- Page : `/scrapping` (route `scrapping.index`)
- Collect UI (recherche) : `GET /api/scrapping/search/{entity}`
- Import : `POST /api/scrapping/import/*`
