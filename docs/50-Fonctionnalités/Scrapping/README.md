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

### Protection (accès et sécurité)
- **Réservé aux admins** : toutes les routes scrapping (web et API) utilisent le middleware `role:admin`
- **Confirmation mot de passe** : la page `/scrapping` affiche une porte d'accès (`ConfirmPasswordModal`) ; l'utilisateur doit confirmer son mot de passe avant d'accéder au dashboard. Les routes API utilisent le middleware `password.confirm`

### UI (admin)
- Page : `/scrapping`
- Recherche (collect-only) : `GET /api/scrapping/search/{entity}`
- Import : `POST /api/scrapping/import/*`
- **Propriétés affichées en comparaison (Brut / Converti / Krosmoz)** : définies par le mapping ; voir `PROPRIETES_COMPARAISON_UI.md` et `MAPPING_ENTRIES_REFERENCE.md` (liste par entité + propriétés en réserve).

### Types (import/filtrage)
Le scrapping enregistre automatiquement les nouveaux `typeId` DofusDB détectés (pending), afin de pouvoir :
- **valider** les types (allowed/blocked),
- filtrer le scrapping sur des **types connus** (par nom) dans l’UI.

### Types et races — source de vérité en BDD
Les **types d’objets** (item-types, super-types) et les **races de monstres** sont gérés en base de données (`resource_types`, `consumable_types`, `item_types`, `monster_races`). Les catalogues DofusDB sont exposés via des services dédiés (`DofusDbItemTypesCatalogService`, `DofusDbMonsterRacesCatalogService`) qui construisent les URLs d’API en dur (sans config d’entité). Les anciens fichiers de config d’entité **catalog-only** (`item-type.json`, `item-super-type.json`, `monster-race.json`) ont été supprimés : ils ne servaient qu’à les faire apparaître dans la liste d’entités de l’API config, sans être utilisés par la recherche, l’import ou les services de catalogue.
