# Plan de finalisation du service de scrapping

Ce document décrit les **tâches restantes** pour considérer le service de scrapping comme terminé (backend), dans l’ordre de priorité recommandé. Contexte : [ETAT_AVANCEMENT.md](./Architecture/ETAT_AVANCEMENT.md), [RESTE_A_FAIRE_SCRAPPING.md](./RESTE_A_FAIRE_SCRAPPING.md).

---

## 1. Priorités et ordre d’exécution

| Priorité | Bloc | Description |
|----------|------|-------------|
| **P0** | Relations & cohérence | Brancher `include_relations` à l’API, unifier id/itemId pour les drops, vérifier le flux monster → sorts/drops. |
| **P1** | Panoplie | Valider que la config et l’intégration panoply sont complètes (ou retirer des routes si non supporté). |
| **P2** | Robustesse & UX | Gestion d’erreurs, logs, optionnellement initiative monster, tests ciblés. |
| **P3** | Doc & clôture | Mise à jour de la doc (état d’avancement, RESTE_A_FAIRE), entrée dans 100-Done si pertinent. |

---

## 2. P0 — Relations et cohérence

### 2.1 Exposer `include_relations` dans l’API

**Constat** : L’Orchestrator gère déjà `include_relations` (défaut `true`) et appelle `resolveRelationsAndDrain` après intégration. Le **contrôleur** ne lit pas ce paramètre dans la requête.

**À faire** :
- Dans `ScrappingController::optionsFromRequest()` : lire `include_relations` (query ou body, booléen), avec défaut `true`, et l’ajouter au tableau d’options passé à l’Orchestrator.
- Documenter le paramètre dans l’API (Orchestrateur/API.md ou équivalent) : `include_relations=false` pour importer uniquement l’entité principale (monster/class/spell/…) sans résoudre les relations (sorts, drops, recettes).

**Fichiers** : `app/Http/Controllers/Scrapping/ScrappingController.php`, `docs/50-Fonctionnalités/Scrapping/Orchestrateur/API.md`.

### 2.2 Drops monster : id vs itemId

**Constat** : La config `monster.json` indique pour les drops `idPath: "itemId"`. Le code utilise `$dropData['id']` dans :
- `RelationResolutionService::resolveAndSyncMonsterRelationsInline()`
- `RelationImportStack::registerCreatureRelationDependents()`

**À faire** :
1. Vérifier la **réponse réelle** de l’API DofusDB pour un monstre (champ `drops` : propriété `id` ou `itemId`).
2. Selon le résultat :
   - Si l’API renvoie **itemId** : adapter le code pour utiliser `$dropData['itemId']` (avec fallback `$dropData['id']` si besoin), dans les deux classes ci-dessus.
   - Si l’API renvoie **id** : aligner la config (changer `idPath` en `"id"` dans `monster.json` relations.drops.extract) pour cohérence doc/config.

**Fichiers** : `RelationResolutionService.php`, `RelationImportStack.php`, `resources/scrapping/config/sources/dofusdb/entities/monster.json`.

### 2.3 Vérifier le flux complet monster (sorts + drops)

**Constat** : L’Orchestrator appelle déjà `resolveRelationsAndDrain` après une intégration réussie quand `include_relations` est true ; la pile est drainée (runOne en cascade pour chaque sort/drop puis mise à jour des tables de liaison).

**À faire** :
- Tester de bout en bout : import d’un monstre avec `include_relations=true` (API et CLI) et vérifier que `creature_spell` et `creature_resource` sont bien remplis.
- Si la CLI ne transmet pas `include_relations` aux options d’import : vérifier que `buildImportOptions()` dans `ScrappingCommand` inclut bien `include_relations` (déjà le cas si on s’appuie sur `extractCollectOptions()` / `buildImportOptions()` avec l’option `--include-relations`).

---

## 3. P1 — Panoplie

**Constat** : Le fichier `entities/panoply.json` existe. RESTE_A_FAIRE mentionnait soit ajouter la config soit retirer panoply des routes.

**À faire** :
1. Vérifier que `panoply.json` contient endpoints, filtres et mapping cohérents avec l’API DofusDB.
2. Vérifier que `IntegrationService` gère bien l’entité `panoply` (création/mise à jour des modèles Krosmoz, tables concernées).
3. Si tout est en place : marquer panoply comme supporté dans la doc (ETAT_AVANCEMENT, README Scrapping).
4. Si panoply n’est pas encore supporté côté API DofusDB ou intégration : retirer panoply des types acceptés dans les routes/validation (EntityLimits, ScrappingController) et documenter “à venir”.

**Fichiers** : `resources/scrapping/config/sources/dofusdb/entities/panoply.json`, `IntegrationService.php`, `EntityLimits.php`, contrôleurs/routes.

---

## 4. P2 — Robustesse et UX

### 4.1 Gestion d’erreurs et logs

- S’assurer que les erreurs de collecte, conversion, validation et intégration remontent clairement (message, codes HTTP ou sortie CLI).
- Vérifier que les logs (CollectService, RelationResolutionService, Orchestrator) sont suffisants pour le debug (sans surcharger).

### 4.2 Initiative (monster)

- Si l’API DofusDB expose l’initiative pour les monstres : ajouter le champ dans `monster.json` (mapping + formatter `dofusdb_ini`) si ce n’est pas déjà fait.
- Sinon : laisser en optionnel / documenter “non exposé par DofusDB”.

### 4.3 Tests

- Conserver ou ajouter des tests pour : runOne avec/sans relations, runMany (dont le correctif `$raw` au lieu de `$rawItem`), validation des données converties.
- Au moins un test feature ou E2E pour l’import monster avec relations (sorts + drops) et vérification des tables de liaison.

---

## 5. P3 — Documentation et clôture

- Mettre à jour **ETAT_AVANCEMENT.md** : préciser que les relations sont résolues par l’Orchestrator lorsque `integrate` et `include_relations` sont true (API et CLI).
- Mettre à jour **RESTE_A_FAIRE_SCRAPPING.md** : pointer vers ce plan, cocher les points traités au fur et à mesure.
- Mettre à jour **TODOLIST.md** (Architecture) : refléter l’état des tâches (relations, drops, panoply).
- Après finalisation : ajouter une entrée dans `docs/100-Done/README.md` (ou équivalent) pour “Finalisation du service de scrapping (backend)” avec lien vers ce plan et les docs mises à jour.

---

## 6. Récapitulatif des fichiers à modifier (par priorité)

| Priorité | Fichiers |
|----------|----------|
| P0 | `ScrappingController.php` (optionsFromRequest), `RelationResolutionService.php`, `RelationImportStack.php`, `monster.json` (si itemId), `Orchestrateur/API.md` |
| P1 | `panoply.json`, `IntegrationService.php`, `EntityLimits.php`, routes/contrôleurs si retrait panoply, ETAT_AVANCEMENT / README |
| P2 | Logs/erreurs (fichiers concernés), `monster.json` (ini), tests (Feature/Unit Scrapping) |
| P3 | `ETAT_AVANCEMENT.md`, `RESTE_A_FAIRE_SCRAPPING.md`, `TODOLIST.md`, `100-Done/README.md` |

---

## 7. Références

- [Architecture scrapping](./Architecture/README.md)
- [État d’avancement](./Architecture/ETAT_AVANCEMENT.md)
- [Reste à faire (résumé)](./RESTE_A_FAIRE_SCRAPPING.md)
- [Relations (ordre de résolution)](./Architecture/RELATIONS.md)
- [Plan d’implémentation](./Architecture/PLAN_IMPLEMENTATION.md)
