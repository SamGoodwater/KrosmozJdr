# Division des tâches — Scrapping (backend / services / frontend)

Ce document décrit la répartition des responsabilités entre **contrôleurs** (API), **services** (métier) et **frontend** (UI) pour le module scrapping, et signale les incohérences éventuelles.

---

## 1. Backend (contrôleurs)

**Rôle** : HTTP (validation des entrées, appels aux services, format de réponse). Aucune logique métier (collecte, conversion, intégration).

| Contrôleur | Responsabilités |
|------------|-----------------|
| **ScrappingConfigController** | Lit la config via `ConfigLoader`, expose `label`, `meta`, `filters`, `relations`, **comparisonKeys** pour l’UI. Une seule source : les JSON d’entité. |
| **ScrappingController** | **meta** : agrège limites + labels (alias, config, `EntityLimits` en fallback). **preview / previewBatch** : appelle `Orchestrator` + `IntegrationService::getExistingAttributesForComparison`, renvoie raw + converted + existing. **import*** : valide la requête, appelle `Orchestrator::runOne`, renvoie le résultat. **resolveEntityForImport** : délègue à `CollectAliasResolver`. |
| **ScrappingSearchController** | Valide l’entité, extrait filtres/pagination, appelle `CollectService::fetchManyResult`, délègue l’enrichissement à **SearchResultEnricher**, renvoie JSON. |
| **ScrappingImportController** / **ScrappingV2Controller** | Import unitaire : résolution d’entité, options, `Orchestrator::runOne`, réponse. |
| **ResourceTypeRegistryController** / **ItemTypeRegistryController** / **ConsumableTypeRegistryController** | Registres de types (allowed/blocked), listes, bulk PATCH, etc. Hors pipeline collect/conversion. |

**Points cohérents** :
- Aucun contrôleur n’effectue de collecte HTTP, de conversion ou d’écriture en base : tout passe par les services.
- Validation des paramètres (type, id, plages) dans les contrôleurs ; règles métier dans les services.

---

## 2. Services (métier)

**Rôle** : logique de collecte, conversion, intégration, config. Une seule lecture des JSON (config).

| Couche | Service | Responsabilités |
|--------|---------|----------------|
| **Config** | **ConfigLoader** | Charge `source.json` et `entities/*.json`. Utilisé par Collect, Conversion, Orchestrator, et les contrôleurs (config / meta). |
| | **CollectAliasResolver** | Résout les alias UI (`resource`, `consumable`, `equipment`, etc.) vers `source` + `entity` + `defaultFilter`. Fichier `collect_aliases.json`. |
| **Collecte** | **CollectService** | Construit les URLs à partir de la config (endpoints, queryDefaults, filters), appelle DofusDB, gère la pagination. |
| **Conversion** | **ConversionService** | Applique le **mapping** (from.path → formatters → to.model/field). Lit uniquement la config. |
| **Intégration** | **IntegrationService** | Validation, écriture en base, relations. **getExistingAttributesForComparison** : renvoie les attributs existants pour la comparaison UI (monster, spell, breed, item, panoply). |
| **Orchestration** | **Orchestrator** | Enchaîne collecte → conversion → validation → intégration (si pas dry_run). |
| **Catalogues / registres** | **DofusDbMonsterRacesCatalogService**, **ItemEntityTypeFilterService**, **MonsterRaceFilterService**, **TypeRegistryBatchTouchService** | Filtres métier (races, types item), enrichissement, mise à jour des registres. |

**Points cohérents** :
- La liste des propriétés “utiles” (affichage comparaison) est définie **une seule fois** dans les JSON (`mapping[].key`), exposée via l’API config en `comparisonKeys`. Aucune whitelist côté frontend.
- Collecte et conversion sont 100 % pilotées par la config (endpoints, mapping, formatters).

---

## 3. Frontend (Vue)

**Rôle** : affichage, formulaires, appels API. Aucune règle métier (pas de décision sur “quelles propriétés afficher”, pas de logique de conversion).

| Élément | Responsabilités |
|--------|-----------------|
| **ScrappingDashboard** | Charge config (`/api/scrapping/config`) et meta (`/api/scrapping/meta`). Construit la requête de recherche (filtres, pagination), appelle search puis preview batch. Affiche le tableau ; les **colonnes de comparaison** sont dérivées de `comparisonKeys` (backend) : le frontend filtre les clés affichées en fonction de cette liste uniquement. |
| **ScrappingSearchTableSection** | Idem : search + import batch, affichage à partir des données et de la config. |
| **SearchPreviewSection** / **EntityDiffTable** | Affichage détail (brut / converti / existant) à partir des props ; pas de liste d’exclusion en dur. |
| **CompareModal** | Preview + import with merge ; pas de logique de mapping. |

**Points cohérents** :
- Les libellés d’entités, les filtres supportés et les clés de comparaison viennent de l’API (config). Pas de duplication de listes “utiles” ou d’exclusions dans le frontend.
- Le “tri” des propriétés affichées est fait côté backend (comparisonKeys = clés du mapping).

---

## 4. Synthèse de cohérence

| Règle | Statut |
|-------|--------|
| Config (JSON) = seule source pour mapping, endpoints, formatters | OK |
| comparisonKeys = seule source pour “quelles propriétés afficher” en comparaison | OK |
| Contrôleurs = HTTP + délégation aux services | OK |
| Services = collecte, conversion, intégration, config | OK |
| Frontend = affichage + appels API, pas de whitelist/exclusion métier | OK |

---

## 5. Points d’attention et améliorations mineures

### 5.1 Alias `class` (fait)

- L’alias **`class`** a été ajouté dans `collect_aliases.json` (même config que `classe` → breed).
- Les contrôleurs utilisent **CollectAliasResolver** pour résoudre l’entité de config ; la constante ENTITY_ALIASES a été supprimée.

### 5.2 Labels d’entités (fait)

- Dans **meta()**, le libellé est désormais pris dans l’ordre : alias (`CollectAliasResolver`), puis config (`cfg['label']`), puis **getEntityLabel()** en secours.

### 5.3 Limites (maxId) : config vs EntityLimits

- Les JSON d’entité peuvent définir **meta.maxId**.
- **EntityLimits::LIMITS** définit aussi des plages max par type (validation preview, import range).

**Recommandation** : à long terme, dériver les limites de validation depuis la config (meta.maxId) lorsque c’est possible, et n’utiliser `EntityLimits` qu’en secours ou pour des caps “métier” explicites (éviter deux sources différentes pour la même chose).

### 5.4 Enrichissement des résultats de search (fait)

- L’enrichissement (exists/existing, typeName/typeDecision/typeKnown, raceName) a été déplacé dans **SearchResultEnricher** (`App\Services\Scrapping\Core\Search\SearchResultEnricher`). Le contrôleur appelle `$this->searchEnricher->enrich($entity, $result['items'])`.

---

## 6. Références

- [Orchestrateur/API.md](Orchestrateur/API.md) — Endpoints exposés
- [PROPRIETES_UTILES_KROSMOZ.md](PROPRIETES_UTILES_KROSMOZ.md) — Source des propriétés affichées (comparisonKeys)
- [README.md](README.md) — Pipeline et sources de vérité
