# État d’avancement — Refonte scrapping V2

Ce document résume **où en est la refonte du scrapping V2** : ce qui est en place, ce qui est utilisé en production, et ce qui reste à faire.

---

## 1. Vue d’ensemble

| Élément | Statut | Détail |
|--------|--------|--------|
| **Pipeline actuel (production)** | En place | **ScrappingOrchestrator** (legacy) : Collect (DataCollectService) → Conversion (DataConversionService ou ConfigDrivenConverter) → Intégration (DataIntegrationService). Utilisé par l’API (`ScrappingController`, `POST /api/scrapping/import/*`) et le dashboard. |
| **Pipeline V2 (refonte)** | Implémenté, non branché en prod | **Orchestrator V2** : CollectService → ConversionService → ValidationService → IntegrationService. Utilisé **uniquement** par la commande CLI `php artisan scrapping:v2`. |
| **Formules / limites en BDD** | En place | CharacteristicService (limites), DofusDbConversionFormulaService (formules), DofusDbConversionFormulas (conversion level, life, attributs, ini, résistances). Handlers nommés (ex. résistances) + admin (select handler). |
| **Consommation BDD par pipeline** | Legacy oui, V2 oui (monster) | L’**ancien** pipeline utilise DofusDbConversionFormulas + CharacteristicService. Le **V2** utilise désormais les formules BDD pour **monster** (level, life, strength, intelligence, agility, chance) via les formatters `dofusdb_level`, `dofusdb_life`, `dofusdb_attribute` ; `entityType` est passé dans le contexte (breed → class). |

---

## 2. Ce qui est fait (V2)

### 2.1 Architecture et services

| Composant | Fichier / zone | Rôle |
|-----------|----------------|------|
| **CollectService V2** | `app/Services/Scrapping/V2/Collect/CollectService.php` | Collecte pilotée par config JSON (endpoints, pagination, filtres). |
| **ConfigLoader V2** | `app/Services/Scrapping/V2/Config/ConfigLoader.php` | Charge les configs depuis `resources/scrapping/v2/sources/dofusdb/`. |
| **ConversionService V2** | `app/Services/Scrapping/V2/Conversion/ConversionService.php` | Applique le mapping (propriété source → cible) + formatters (FormatterApplicator). |
| **FormatterApplicator** | `app/Services/Scrapping/V2/Conversion/FormatterApplicator.php` | Formatters purs : toString, pickLang, toInt, clampInt, truncate, etc. |
| **ValidationService** | `app/Services/Scrapping/V2/Validation/ValidationService.php` | Valide les données converties contre **CharacteristicService** (BDD). |
| **IntegrationService V2** | `app/Services/Scrapping/V2/Integration/IntegrationService.php` | Enregistre en base (ou simulation dry_run). |
| **Orchestrator V2** | `app/Services/Scrapping/V2/Orchestrator/Orchestrator.php` | Enchaîne Collect → Conversion → Validation → Intégration. |

### 2.2 Configs V2

| Ressource | Emplacement | Contenu |
|-----------|-------------|---------|
| **Source** | `resources/scrapping/v2/sources/dofusdb/source.json` | baseUrl, langue, http. |
| **Entités** | `resources/scrapping/v2/sources/dofusdb/entities/*.json` | monster, spell, breed, item, item-type, monster-race, item-super-type. Chaque fichier : endpoints (fetchOne, fetchMany), filtres, mapping (source → cible + formatter). |
| **Alias collecte** | `resources/scrapping/v2/collect_aliases.json` | Alias CLI (ex. classe → breed, ressource → item avec filtre). |
| **Item super types** | `resources/scrapping/v2/sources/dofusdb/item-super-types.json`, `item-types.json` | Référence pour les types d’items. |

### 2.3 Point d’entrée V2

- **CLI** : `php artisan scrapping:v2 --collect=monster --id=31 [--convert] [--validate] [--integrate] [--dry-run] [--json]`  
  Utilise `Orchestrator::default()` → runOne / runMany.

- **Tests** : `tests/Unit/Scrapping/V2/CollectServiceTest.php`, `OrchestratorTest.php`.

- **API V2** : **POST /api/scrapping/v2/import/{entity}/{id}** (ScrappingV2Controller::importOne) appelle l’Orchestrator V2.
- **Import monster en production** : **POST /api/scrapping/import/monster/{id}** utilise désormais le **pipeline V2** (collecte legacy pour avoir spells/drops, puis **runOneWithRaw** : conversion BDD, validation, intégration V2). Les relations (sorts, drops) sont importées en cascade via l’orchestrateur legacy et synchronisées sur la créature. Le bouton « Rafraîchir » sur un monstre déclenche donc le V2.

---

## 3. Ce qui n’est pas fait (ou partiel)

### 3.1 Alignement V2 sur formules / limites BDD

- **Fait (monster)** : ConversionService V2 transmet un **contexte** (`entityType`, `lang`) à FormatterApplicator. FormatterApplicator injecte **DofusDbConversionFormulas** et expose les formatters `dofusdb_level`, `dofusdb_life`, `dofusdb_attribute`, `dofusdb_ini`. Le fichier **monster.json** utilise ces formatters pour level, life, strength, intelligence, agility, chance ; l’ordre (level avant life) est géré dans `dofusdb_life` via `levelPath`.
- **Résistances** : pour monster, **resistanceBatch: true** dans la config ; ConversionService appelle `convertResistancesBatch()` après le mapping et fusionne res_* et res_fixe_* dans creatures. Les entrées res_* ont été retirées du mapping monster.json.
- **À faire** : étendre à **breed** (class) et **item** si level/life/attributs sont mappés en V2 ; ajouter **ini** (initiative) dans monster.json si le champ existe côté DofusDB.

### 3.2 Branchement du pipeline V2 en production

- Aucune route API ni bouton UI n’utilisent le pipeline V2.
- **À faire** (quand la conversion V2 sera alignée sur la BDD) :
  - Soit basculer progressivement les imports (ex. `POST /api/scrapping/import/*`) vers l’Orchestrator V2.
  - Soit exposer une route dédiée (ex. `POST /api/scrapping/v2/import/*`) pour tester en parallèle, puis remplacer l’ancien.

### 3.3 Autres points de la vision (VISION_ET_ARCHITECTURE.md)

| Sujet | Statut |
|-------|--------|
| **Relations / interdépendances** (sorts ↔ monstres, classes → sorts, drops, recettes) | Géré côté legacy (DataIntegrationService). En V2, IntegrationService ne résout pas les relations ; l’import monster en prod utilise V2 pour Creature+Monster puis legacy pour sorts/drops + sync. Voir [RELATIONS_V2.md](./RELATIONS_V2.md) pour l’ordre de résolution et la comparaison à la vision. |
| **Simulation (dry-run)** | Présente en V2 (option `dry_run`). |
| **Pagination / limit effectif** | Géré dans CollectService V2. |
| **Un seul tableau de mapping (config)** | En V2 : mapping par entité dans les JSON ; formules complexes (level, life, attributs, résistances) déléguées à la BDD pour monster (formatters dofusdb_*, resistanceBatch). |
| **Plan d’implémentation (PLAN_IMPLEMENTATION.md)** | Créé : ordre des étapes, approche greenfield, lien vers [DECOUVERTE_API_DOFUSDB.md](./DECOUVERTE_API_DOFUSDB.md). |

---

## 4. Synthèse

| Question | Réponse |
|----------|---------|
| **Où en est la refonte V2 ?** | **Architecture et briques V2 en place** ; **conversion monster alignée sur la BDD** (formatters dofusdb_*). **Non utilisée en production** : l’API et l’UI restent sur l’orchestrateur legacy. |
| **Les formules / limites en BDD sont-elles utilisées ?** | **Oui** par l’**ancien** pipeline et **oui** par le **V2** pour monster (level, life, attributs via FormatterApplicator + DofusDbConversionFormulas). |
| **Prochaine étape logique ?** | Étendre les formatters BDD à breed/item si besoin, puis **brancher le pipeline V2** sur l’API/UI (ou une route dédiée) pour remplacer progressivement l’existant. |

---

## 5. Références

- [README Refonte](./README.md) — Principes et contenu du dossier.
- [VISION_ET_ARCHITECTURE.md](./VISION_ET_ARCHITECTURE.md) — Vision cible (Collect → Conversion → Validation → Intégration).
- [AUDIT_ETAT_DES_LIEUX.md](./AUDIT_ETAT_DES_LIEUX.md) — État des lieux du code scrapping (legacy).
- [IMPLICATIONS_NOUVELLE_ARCHI_CARACTERISTIQUES.md](./IMPLICATIONS_NOUVELLE_ARCHI_CARACTERISTIQUES.md) — Formules/limites en BDD et ce qu’il faut faire pour que le V2 les consomme.
- [CONVERSION_100_BDD_ET_HANDLERS.md](../Characteristics-DB/CONVERSION_100_BDD_ET_HANDLERS.md) — Handlers nommés et conversion pilotée par la BDD.
