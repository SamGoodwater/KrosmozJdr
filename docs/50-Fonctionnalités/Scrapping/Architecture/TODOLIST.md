# To-do list scrapping

Liste de tâches optionnelles ou de suivi. Contexte : [ETAT_AVANCEMENT.md](./ETAT_AVANCEMENT.md).

---

## Alignement formules / limites BDD

- [x] **1.1** Passer `entityType` (monster / class / item) dans le contexte de conversion  
  - Orchestrator : ajouter `context['entityType']` (breed → class, sinon entity) avant d’appeler `convert()`.  
  - ConversionService : transmettre `context` à `FormatterApplicator::apply()`.

- [x] **1.2** Formatters BDD dans FormatterApplicator  
  - Injecter `DofusDbConversionFormulas` dans FormatterApplicator (optionnel pour rétrocompat).  
  - Ajouter les formatters : `dofusdb_level`, `dofusdb_life`, `dofusdb_attribute`, `dofusdb_ini`.  
  - Signature `apply(..., array $context = [])` ; formatters BDD utilisent `$context['entityType']`.  
  - `dofusdb_life` : utiliser `args['levelPath']` (ex. `grades.0.level`) pour récupérer le niveau Dofus et appeler `convertLife(value, levelKrosmoz, entityType)`.

- [x] **1.3** Config monster.json : utiliser les formatters BDD  
  - Remplacer pour level : `toInt` + `clampInt` par `dofusdb_level`.  
  - Remplacer pour life : `toInt` + `clampInt` par `dofusdb_life` (avec `levelPath: "grades.0.level"`).  
  - Remplacer pour strength, intelligence, agility, chance : par `dofusdb_attribute` avec `characteristicId` dans args.  
  - wisdom : laissé en toInt + clampInt (pas de formule BDD pour wisdom).  
  - Si initiative (ini) est mappée plus tard : utiliser `dofusdb_ini`.

- [x] **1.4** Config breed.json : DofusDB n’expose pas level, life ni attributs pour les classes (breeds) ; uniquement descriptions, noms, illustrations, sorts liés, rôles. Mapping aligné (pas de life/life_dice depuis l’API).

- [x] **1.5** Tests  
  - Test OrchestratorTest : runOne avec convert vérifie level/life issus des formules BDD (level 5 → 1, life 100 + level 1 → 6).

---

## Résistances

- [x] **2.1** Appeler `convertResistancesBatch()` dans ConversionService après le mapping (si entité monster/class/item et config `resistanceBatch: true`), et fusionner les champs `res_*` et `res_fixe_*` dans la sortie. monster.json : `resistanceBatch: true` ; entrées res_* retirées du mapping.
- [ ] **2.2** (N/A) Champ par champ : non retenu ; le batch est utilisé pour monster.

---

## Relations (doc)

- [x] **3.1** Exposer une route dédiée `POST /api/scrapping/import/{entity}/{id}` (ScrappingController) qui appelle l’Orchestrator. Entités : monster, breed, spell, item, class (→ breed).  
- [x] **3.2** Basculer l’import monster : `POST /api/scrapping/import/monster/{id}` utilise la collecte puis runOne / runOneWithRaw (conversion BDD + validation + intégration). Relations (sorts, drops) en cascade via RelationResolutionService + sync sur la créature.  
- [x] **3.3** Dashboard / UI : indication pour monster — tooltip « Rafraîchir les données depuis DofusDB (pipeline) » et badge sur l’action Rafraîchir (entity-actions-config monsters, EntityActionButton / dropdown / context).
- [x] **3.4** API : lire `include_relations` dans `optionsFromRequest` (défaut true). Drops : utiliser `itemId` avec fallback `id` dans RelationResolutionService et RelationImportStack. Test de régression runMany + include_relations (ScrappingOrchestratorTest::test_run_many_monster_with_include_relations_passes_raw_to_relations).

---

## Doc

- [x] **4.1** Créer `PLAN_IMPLEMENTATION.md` (ordre des étapes, greenfield, découverte API) si utile.  
- [x] **4.2** Documenter l’ordre de résolution des relations et le comparer à la vision : [RELATIONS.md](./RELATIONS.md).  
- [ ] **4.3** Optionnel : déprécier / supprimer l’ancien pipeline (ancien pipeline, DataConversionService utilisé par l’orchestrateur).

---

Voir [ETAT_AVANCEMENT.md](./ETAT_AVANCEMENT.md) et [RELATIONS.md](./RELATIONS.md) pour l'état actuel.
