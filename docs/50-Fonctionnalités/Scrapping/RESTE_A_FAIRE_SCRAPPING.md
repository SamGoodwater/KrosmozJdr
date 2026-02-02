# Résumé — Ce qu’il reste à faire pour finir le scrapping (backend)

Ce document liste ce qui reste à faire pour **finaliser la partie backend** du scrapping (hors interface, à traiter plus tard).

---

## 1. Relations monster (sorts + drops) — prioritaire

**État actuel**  
- L’import monster (API et CLI) appelle uniquement `Orchestrator::runOne` : on crée/met à jour **Creature + Monster** mais **pas** les relations (sorts, drops).  
- `RelationResolutionService` existe et fait déjà : import des sorts/drops via `runOne`, puis `creature->spells()->sync()` et `creature->resources()->sync()`. Il n’est **pas** appelé depuis le contrôleur ni depuis la CLI.

**À faire**

1. **API**  
   - Dans `ScrappingController::importMonster` :  
     - Si `include_relations=true` (paramètre requête) :  
       1. Collecte : `CollectService::fetchOne('dofusdb', 'monster', $id)` → données brutes (avec `spells` / `drops` si l’API DofusDB les renvoie).  
       2. Pipeline : `Orchestrator::runOneWithRaw('dofusdb', 'monster', $raw, $options)` → intégration Creature + Monster.  
       3. Récupérer l’ID de la créature depuis le résultat d’intégration.  
       4. Appeler `RelationResolutionService::resolveAndSyncMonsterRelations($raw, $creatureId, $options)`.  
     - Sinon : garder le comportement actuel (uniquement `runOne`).  
   - Dans `optionsFromRequest` : ajouter la lecture de `include_relations` (query ou body) et l’inclure dans le tableau d’options.

2. **CLI**  
   - Dans `ScrappingCommand::importOne`, pour l’entité **monster** :  
     - Si `$options['include_relations']` est vrai :  
       1. Collecte : `CollectService::fetchOne('dofusdb', 'monster', $id)`.  
       2. `Orchestrator::runOneWithRaw('dofusdb', 'monster', $raw, $options)`.  
       3. Puis `RelationResolutionService::resolveAndSyncMonsterRelations($raw, $creatureId, $options)`.  
     - Sinon : garder l’appel actuel à `runOne`.

3. **Cohérence drops**  
   - La config `monster.json` indique pour les drops `idPath: "itemId"`.  
   - `RelationResolutionService` utilise aujourd’hui `$dropData['id']`.  
   - Vérifier la réponse réelle de l’API DofusDB pour les drops (champ `id` ou `itemId`) et adapter le code ou la config si besoin.

---

## 2. Panoplie

**État actuel**  
- Les routes et le contrôleur proposent l’import **panoply** (`importPanoply`, batch, range, etc.).  
- Il n’existe **pas** de fichier `resources/scrapping/config/sources/dofusdb/entities/panoply.json`.  
- `ConfigLoader::loadEntity('dofusdb', 'panoply')` échoue donc au premier import panoplie.

**À faire (au choix)**

- **Option A** : Si l’API DofusDB expose les panoplies :  
  - Ajouter `panoply.json` (endpoints, filtres, mapping) dans `entities/`.  
  - S’assurer que `IntegrationService` gère bien l’entité panoply (création/mise à jour des modèles KrosmozJDR).  

- **Option B** : En attendant une config :  
  - Retirer panoply des types acceptés dans les routes/validation (ScrappingController, EntityLimits, etc.) pour éviter les erreurs au runtime.  
  - Réintégrer quand `panoply.json` sera en place.

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

| Priorité | Tâche |
|----------|--------|
| 1 | Brancher **RelationResolutionService** pour l’import monster (API + CLI) avec option `include_relations`. |
| 2 | Vérifier/corriger le champ utilisé pour les drops (id vs itemId) dans RelationResolutionService et la config. |
| 3 | Décider pour **panoply** : soit ajouter `panoply.json` (et support intégration), soit retirer panoply des routes/limites en attendant. |
| 4 | (Optionnel) Initiative pour monster, optimisation runMany. Classes (breeds) : pas de level/life/attributs dans DofusDB. |

Une fois les points 1 à 3 traités, la partie backend du scrapping peut être considérée comme terminée pour le périmètre actuel (hors interface).
