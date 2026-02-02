# To-do list — Refonte scrapping V2

Liste de tâches concrètes pour faire avancer la refonte. Voir [ETAT_AVANCEMENT_REFONTE_V2.md](./ETAT_AVANCEMENT_REFONTE_V2.md) pour le contexte.

---

## Phase 1 : Aligner la conversion V2 sur les formules / limites BDD

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

- [ ] **1.4** Config breed.json (optionnel)  
  - Si des champs level/life/attributs sont ajoutés côté DofusDB pour les classes, utiliser les formatters BDD avec `entityType: class`.

- [x] **1.5** Tests  
  - Test OrchestratorTest : runOne avec convert vérifie level/life issus des formules BDD (level 5 → 1, life 100 + level 1 → 6).

---

## Phase 2 : Résistances (optionnel en V2)

- [x] **2.1** Appeler `convertResistancesBatch()` dans ConversionService après le mapping (si entité monster/class/item et config `resistanceBatch: true`), et fusionner les champs `res_*` et `res_fixe_*` dans la sortie. monster.json : `resistanceBatch: true` ; entrées res_* retirées du mapping.
- [ ] **2.2** (N/A) Champ par champ : non retenu ; le batch est utilisé pour monster.

---

## Phase 3 : Brancher le pipeline V2 en production

- [x] **3.1** Exposer une route dédiée `POST /api/scrapping/v2/import/{entity}/{id}` (ScrappingV2Controller::importOne) qui appelle l’Orchestrator V2. Entités : monster, breed, spell, item, class (→ breed).  
- [x] **3.2** Basculer l’import monster : `POST /api/scrapping/import/monster/{id}` utilise la collecte legacy (spells/drops), puis **runOneWithRaw** V2 (conversion BDD + validation + intégration). Relations (sorts, drops) en cascade via legacy + sync sur la créature.  
- [x] **3.3** Dashboard / UI : indication V2 pour monster — tooltip « Rafraîchir les données depuis DofusDB (pipeline V2) » et badge « V2 » sur l’action Rafraîchir (entity-actions-config monsters, EntityActionButton / dropdown / context).

---

## Phase 4 : Nettoyage et doc

- [x] **4.1** Créer `PLAN_IMPLEMENTATION.md` (ordre des étapes, greenfield, découverte API) si utile.  
- [x] **4.2** Documenter l’ordre de résolution des relations (V2) et le comparer à la vision : [RELATIONS_V2.md](./RELATIONS_V2.md).  
- [ ] **4.3** Quand V2 est la référence : déprécier / supprimer l’ancien pipeline (orchestrateur legacy, DataConversionService utilisé par l’orchestrateur).

---

## Légende

- **Phase 1** : prioritaire pour que le V2 utilise les formules et limites en BDD.  
- **Phase 2** : résistances (batch ou champ par champ).  
- **Phase 3** : mise en production (API / UI).  
- **Phase 4** : finalisation et suppression du legacy.
