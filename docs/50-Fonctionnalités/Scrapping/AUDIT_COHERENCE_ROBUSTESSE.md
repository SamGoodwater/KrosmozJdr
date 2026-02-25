# Audit global — Système de scrapping

**Date** : 2026-02-25  
**Objectif** : Vérifier la cohérence, la robustesse, la simplicité et les optimisations du système de scrapping.

---

## 1. Synthèse

| Critère      | État global | Commentaire |
|-------------|-------------|-------------|
| **Cohérence** | Bon avec points à corriger | Pipeline et config uniques ; incohérences DI / alias / contrôleurs. |
| **Robustesse** | À renforcer | runMany intègre des items invalides ; erreurs bien capturées ailleurs. |
| **Simplicité** | Bon avec duplication | Fichier mort, `resultToJson` dupliqué, alias en double. |
| **Optimisation** | À améliorer | Cache DofusDB non utilisé pour import/preview ; `Orchestrator::default()` non injecté. |

---

## 2. Cohérence

### 2.1 Points positifs

- **Pipeline unique** : Collect → Conversion → Validation → Intégration, piloté par l’Orchestrator.
- **Config unique** : `config/scrapping.php` et `resources/scrapping/config/sources/dofusdb/` ; pas de config dispersée.
- **Contrôleurs** : Aucune logique métier (collecte/conversion/intégration) dans les contrôleurs ; tout passe par les services.
- **Règles métier** : Validation et limites dans les services (CharacteristicLimitService, ConfigLoader).

### 2.2 Incohérences relevées

1. **Orchestrator non injecté**  
   `ScrappingController` appelle `Orchestrator::default()` à chaque action au lieu d’injecter `Orchestrator` (pourtant enregistré en singleton dans `AppServiceProvider`). Le singleton n’est utilisé que par la commande et `ScrappingImportController` via `::default()`.

2. **CollectService sans DofusDbClient pour l’import**  
   `Orchestrator::default()` construit `new CollectService($configLoader)` sans client. Donc :
   - **Import / preview (API)** : pas de cache ni retry centralisé (fallback `Http::timeout(30)->get()`).
   - **Search** : utilise le `CollectService` injecté (container) qui a `DofusDbClient` → cache et retry.  
   Comportement différent selon l’entrée (search vs import).

3. **Alias « class » en double**  
   - `CollectService::ENTITY_ALIASES = ['class' => 'breed']` (en dur).  
   - `CollectAliasResolver` + `collect_aliases.json` font la même chose.  
   Une seule source (CollectAliasResolver + JSON) suffit.

4. **Fichier ScrappingV2Controller.php**  
   Le fichier déclare `class ScrappingImportController` (copie de `ScrappingImportController.php`). Aucune route n’utilise `ScrappingV2Controller`. Code mort et risque de conflit de déclaration de classe.

5. **Options d’import différentes**  
   - `ScrappingController::optionsFromRequest()` : `replace_mode`, `exclude_from_update`, `property_whitelist`, `download_images`, etc.  
   - `ScrappingImportController::importOne()` : options réduites (`validate`, `integrate`, `dry_run`, `force_update`, `lang`).  
   L’endpoint générique `POST /api/scrapping/import/{entity}/{id}` n’expose pas les mêmes options que les endpoints dédiés (class, monster, etc.).

---

## 3. Robustesse

### 3.1 Points positifs

- **Orchestrator** : `runOne` / `runMany` dans un `try/catch` ; retour `OrchestratorResult::fail()` avec message.
- **DofusDbClient** : retry et timeout ; log des erreurs.
- **Validation** : `CharacteristicLimitService::validate()` + `clampConvertedData` ; erreurs exposées dans le résultat.
- **Contrôleurs** : validation des entrées (Request), codes HTTP adaptés (422, 503, 500).

### 3.2 Points à corriger

1. **runMany : intégration malgré échec de validation**  
   Dans `Orchestrator::runMany()`, pour chaque item on enregistre les erreurs de validation dans `$allValidationErrors`, mais on appelle quand même `integrate()` pour tous les items. En fin de boucle, si `$allValidationErrors` n’est pas vide, on retourne `OrchestratorResult::fail()`.  
   **Conséquence** : des enregistrements invalides peuvent être intégrés en base avant de renvoyer une erreur.  
   **Recommandation** : n’appeler `integrate()` que pour les items dont la validation a réussi (ou traiter en deux passes : validation globale puis intégration).

2. **Fallback HTTP sans retry**  
   Quand `CollectService` n’a pas de `DofusDbClient` (cas import/preview), `getJson()` utilise `Http::timeout(30)->get($url)` sans retry. En cas d’échec réseau temporaire, l’import échoue sans nouvelle tentative.

---

## 4. Simplicité

### 4.1 Points positifs

- **Responsabilités claires** : ConfigLoader, Collect, Conversion, Integration, Orchestrator bien séparés.
- **Config-driven** : endpoints et mapping dans les JSON ; pas de logique dispersée.

### 4.2 Duplications et complexité

1. **resultToJson dupliqué**  
   - `ScrappingController::resultToJson()` (inclut `error` et `getIntegrationResult()?->getData() ?? getConverted()`).  
   - `ScrappingImportController::resultToJson()` (même idée, forme légèrement différente).  
   **Recommandation** : extraire dans un trait partagé ou une classe dédiée (ex. `OrchestratorResultResponder`).

2. **Résolution d’entité pour l’import**  
   - `ScrappingController::resolveEntityForImport()` utilise `CollectAliasResolver`.  
   - `ScrappingImportController::importOne()` refait la résolution (alias + `entity === 'class' ? 'breed'`) et en plus vérifie `listEntities()`.  
   Même logique à deux endroits ; à centraliser (ex. dans un service ou le resolver).

3. **Fichier mort**  
   Supprimer `ScrappingV2Controller.php` (doublon de `ScrappingImportController` avec le mauvais nom de classe).

4. **ScrappingController volumineux**  
   Beaucoup de méthodes d’import (importClass, importMonster, importItem, …) répètent le même schéma : `optionsFromRequest` → `runOne` → `resultToJson`. Possibilité de factoriser avec une méthode générique `importOneByType(string $type, Request $request, int $id)` et une route/action unique si souhaité (en gardant les routes dédiées pour compatibilité).

---

## 5. Optimisations

### 5.1 Cache et HTTP

- **Search** : utilise `CollectService` (container) → `DofusDbClient` → cache configuré (`cache_ttl`).  
- **Import / preview** : `Orchestrator::default()` → `CollectService` sans client → pas de cache.  
  **Recommandation** : faire construire l’Orchestrator (et donc le CollectService) avec `DofusDbClient` injecté (container), et utiliser `app(Orchestrator::class)` dans les contrôleurs pour que l’import/preview bénéficient du cache et du retry.

### 5.2 Prévisualisation en lot

- `previewBatch` : pour chaque ID, appel à `runOne` (collecte + conversion + validation). Jusqu’à 100 appels séquentiels.  
- Pas de N+1 côté BDD pour `getExistingAttributesForComparison` (un appel par ID, cohérent avec le design actuel).  
  Pour des lots très grands, un traitement par lots (ex. jobs) ou une limite plus basse peut être envisagé ; la limite à 100 est déjà une garde.

### 5.3 Config

- `config('scrapping.data_collect')` et sous-clés utilisés à plusieurs endroits (DofusDbClient, CLI, IntegrationService images, catalogues). Une seule source de config, c’est cohérent ; pas de duplication de valeurs en dur au-delà des fallbacks raisonnables.

---

## 6. Recommandations prioritaires

| Priorité | Action |
|----------|--------|
| **Haute** | Dans `Orchestrator::runMany()`, n’intégrer que les items dont la validation a réussi (ou valider tout le lot avant toute intégration). |
| **Haute** | Utiliser l’Orchestrator et le CollectService du container partout : injecter `Orchestrator` dans `ScrappingController` (et éventuellement `ScrappingImportController`) pour que import/preview utilisent le même CollectService que search (avec DofusDbClient, cache, retry). |
| **Moyenne** | Supprimer le fichier `ScrappingV2Controller.php` (code mort, classe en doublon). |
| **Moyenne** | Supprimer `ENTITY_ALIASES` de `CollectService` et s’appuyer uniquement sur `CollectAliasResolver` (ou config) pour l’alias `class` → `breed`. |
| **Moyenne** | Factoriser `resultToJson` (trait ou helper) et la résolution d’entité pour l’import. |
| **Basse** | Aligner les options de `ScrappingImportController::importOne()` sur celles de `ScrappingController` (replace_mode, exclude_from_update, etc.) si l’API générique doit offrir les mêmes possibilités. |

### Implémentations (2026-02-25)

Toutes les recommandations ci-dessus ont été appliquées : runMany (intégration uniquement pour items validés), Orchestrator/CollectService via container et injection dans les contrôleurs, suppression de ScrappingV2Controller.php, ENTITY_ALIASES remplacé par CollectAliasResolver dans CollectService, trait RespondsWithOrchestratorResult pour resultToJson et resolveEntityForImport, options ScrappingImportController alignées via optionsFromRequest().

---

## 7. Références

- [ETAT_AVANCEMENT.md](Architecture/ETAT_AVANCEMENT.md) — Pipeline et services.
- [DIVISION_TACHES_SCRAPPING.md](DIVISION_TACHES_SCRAPPING.md) — Répartition contrôleurs / services / frontend.
- [config/scrapping.php](../../../config/scrapping.php) — Configuration Laravel.
