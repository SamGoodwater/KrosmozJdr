# Scrapping — Mapping & conversion (DofusDB → KrosmozJDR)

### Objectif
Cette section documente le **mapping** et les **fonctions de conversion (formatters)** du scrapping.

Le pipeline complet reste le même, mais la source de vérité est désormais **config-driven** (JSON) :

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

### Sources de vérité
- **Configs JSON (entités)** : `resources/scrapping/sources/dofusdb/entities/*.json`
- **Registry des formatters** : `resources/scrapping/formatters/registry.json`

### À lire (focus mapping + formatters)
- **1) Mapping (champs + formatters)**
  - `MAPPING_DOFUSDB_TO_KROSMOZJDR.md`
- **2) Référence des formatters / schéma JSON**
  - `Data-conversion/DEFINITIONS.md`
- **3) Effets (couche A normalisation + couche B bonus Krosmoz)**
  - `EFFECTS_SYSTEM.md`
- **4) Couverture (quoi est mappable vs Krosmoz-only)**
  - `MAPPING_COVERAGE_KROSMOZ_PROPS.md`

### Références utiles (sans redites)
- **API DofusDB (collect)** : `Data-collect/API.md`
- **API interne (UI/CLI)** : `Orchestrateur/API.md`

### Architecture
- **[Architecture/](Architecture/)** — Documentation de l’architecture scrapping (config-driven, pipeline).

### UI (admin)
- Page : `/scrapping`
- Recherche (collect-only) : `GET /api/scrapping/search/{entity}`
- Import : `POST /api/scrapping/import/*`

### Types (import/filtrage)
Le scrapping enregistre automatiquement les nouveaux `typeId` DofusDB détectés (pending), afin de pouvoir :
- **valider** les types (allowed/blocked),
- filtrer le scrapping sur des **types connus** (par nom) dans l’UI.
