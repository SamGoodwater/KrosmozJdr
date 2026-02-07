# Résumé — Ce qu’il reste à faire pour finir le scrapping (backend)

Ce document liste ce qui reste à faire pour **finaliser la partie backend** du scrapping (hors interface, à traiter plus tard).

> **Plan détaillé** : voir [PLAN_FINALISATION_SCRAPPING.md](./PLAN_FINALISATION_SCRAPPING.md) pour les tâches priorisées et les fichiers à modifier.

---

## 1. Relations monster (sorts + drops) — fait

- **Orchestrator** : lorsque `integrate` et `include_relations` sont true, après intégration de l’entité principale, l’Orchestrator appelle `resolveRelationsAndDrain` (RelationResolutionService + pile). Pas de branchement spécifique dans le contrôleur : tout passe par `runOne` / `runOneWithRaw` avec les options.
- **API** : `ScrappingController::optionsFromRequest()` lit `include_relations` (query ou body, défaut `true`) et le transmet à l’Orchestrator.
- **CLI** : `ScrappingCommand` transmet déjà `include_relations` via `buildImportOptions()` et `importOne()`.
- **Drops** : le code utilise désormais `itemId` avec fallback sur `id` dans `RelationResolutionService` et `RelationImportStack`, aligné avec la config `monster.json` (`idPath: "itemId"`).

---

## 2. Panoplie — en place

- Le fichier `entities/panoply.json` existe (endpoints, mapping, filterOutCosmetic).  
- `IntegrationService` gère l’entité panoply (création/mise à jour, sync des items).  
- Aucune action requise pour le support de base.

---

## 3. Déjà en place (aucune action requise pour “finir”)

- **Pipeline Core** : Collect → Conversion → Validation → Intégration (Orchestrator, CollectService, ConfigLoader, ConversionService, ValidationService, IntegrationService).  
- **Imports unitaires** : class, monster, spell, item, resource, consumable (API + CLI) via `runOne`.  
- **Batch / plage / tout** : `importBatch`, `importRange`, `importAll` (boucles `runOne`).  
- **Import avec fusion** : `importWithMerge` (runOne avec `force_update`).  
- **Prévisualisation** : `preview` (runOne en `dry_run`).  
- **Formules BDD** : level, life, attributs, résistances (batch) pour monster ; formatters `dofusdb_*` et `resistanceBatch` dans la config.

---

## 4. Optionnel (améliorations possibles)

- **Classes (breeds)** : DofusDB n’expose pas level, life ni attributs pour les classes ([dofusdb.fr/database/breeds](https://dofusdb.fr/fr/database/breeds/)) — uniquement descriptions, noms, illustrations, sorts liés (spell-levels par breedId), rôles. Le mapping `breed.json` est aligné sur ce qui existe.  
- **Initiative (ini)** : Ajouter le champ initiative dans `monster.json` si l’API DofusDB l’expose.  
- **Optimisation** : Utiliser `Orchestrator::runMany` pour des imports en masse par filtres (au lieu de boucles `runOne`) si besoin de performance.

---

## 5. Récap ordre de priorité (backend uniquement)

| Priorité | Tâche | État |
|----------|--------|------|
| 1 | Brancher **RelationResolutionService** pour l’import monster (API + CLI) avec option `include_relations`. | Fait |
| 2 | Vérifier/corriger le champ utilisé pour les drops (id vs itemId) dans RelationResolutionService et la config. | Fait |
| 3 | Panoply : config + intégration. | En place |
| 4 | (Optionnel) Initiative pour monster, optimisation runMany. Classes (breeds) : pas de level/life/attributs dans DofusDB. | Optionnel |

La partie backend du scrapping est considérée comme terminée pour le périmètre actuel (hors interface). Voir [PLAN_FINALISATION_SCRAPPING.md](./PLAN_FINALISATION_SCRAPPING.md) pour les tâches P2/P3 optionnelles (robustesse, doc, 100-Done).
