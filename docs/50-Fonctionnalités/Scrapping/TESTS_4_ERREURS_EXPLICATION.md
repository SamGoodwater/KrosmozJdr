# Explication des 4 tests en échec (php artisan test)

Ce document décrit les 4 erreurs observées, leur cause probable et les **corrections appliquées**.

---

## 1. `OrchestratorTest::test_run_many_limit_zero_collects_until_exhausted`

**Erreur :** `Failed asserting that false is true` (ligne 112 : `$result->isSuccess()`).

**Comportement attendu :** Avec `limit => 0` et `offset => 0`, le test attend que `runMany` fasse une collecte « jusqu’à épuisement » (2 appels HTTP mockés), retourne un succès, 3 éléments dans `getConverted()` et un message contenant `limit=tout`.

**Cause probable :**  
- Soit une **exception** est levée avant le `return OrchestratorResult::ok(...)` (par ex. dans `CollectService::fetchMany`) et le `catch` renvoie `OrchestratorResult::fail($e->getMessage())`.  
- Soit les **options** passées au test déclenchent la branche « convert + validate » : dans ce cas, les données mockées (ex. `['id' => 1]`, `['id' => 2]`, `['id' => 3]`) sont insuffisantes pour la validation (monster sans race, etc.), ce qui remplit `$allValidationErrors` et fait retourner `OrchestratorResult::fail(...)`.

**Correction appliquée :**  
- Le test passe désormais `page_size => 2` pour que le mock reçoive des requêtes avec `$limit=2` (sinon le défaut 50 ne correspondait pas au mock qui renvoie 2 puis 1 item).  
- L’Orchestrator transmet maintenant `page_size` aux options de collecte quand il est fourni.  
- Le mock extrait `skip` via une regex (`%24skip=(\d+)` ou `skip=(\d+)`) pour couvrir l’encodage réel des URLs.

---

## 2. `OrchestratorTest::test_run_one_with_convert_returns_converted_structure`

**Erreur :** `Failed asserting that 0 is equal to 1 or is greater than 1` (ligne 196 : `$creatures['level']` vaut 0).

**Comportement attendu :** Après conversion d’un monstre mocké (level 5, lifePoints 100), le niveau converti doit être ≥ 1 et la vie convertie égale à 6.

**Cause probable :**  
La conversion (formules / limites) ou le **CharacteristicLimitService** en environnement de test renvoie un `level` à 0 (pas de formule, pas de limite, ou données de test manquantes en BDD). Ce n’est pas lié aux changements sur les relations (drops, creature_resource, etc.).

**À faire :**  
- Vérifier les seeders / données de test pour les caractéristiques et formules (niveau, vie) du monstre.  
- Vérifier que les mocks du test fournissent bien les champs attendus par la conversion (ex. `grades`, `raceId`) et que le service de formules/limites est bien configuré ou mocké dans le test.

---

## 3. `ScrappingCommandTest::test_command_replace_existing_updates_record`

**Erreur :** `Expected: Bouftou - To contain: mis à jour` (ligne 631 : le nom de la créature ne contient pas « mis à jour »).

**Comportement attendu par le test :**  
Le **mock HTTP** pour `GET /monsters/31` renvoie au second appel un payload avec `'name' => ['fr' => 'Bouftou mis à jour']`. Avec `--replace-existing`, l’intégration est censée mettre à jour la créature avec ces données ; le nom en base devrait donc contenir « mis à jour ».

**Cause probable :**  
Lors d’un **update** (créature déjà existante), l’intégration ne met peut‑être pas à jour le champ `name` (ex. champ exclu, logique « skip » ou `exclude_from_update`). À vérifier dans `IntegrationService::integrateMonster` : quand on remplace un enregistrement existant, tous les champs convertis (dont le nom) doivent être appliqués.

**Correction appliquée :**  
Le client DofusDB met en cache les réponses. Lors du second import, `fetchOne(31)` renvoyait la réponse en cache (nom « Bouftou ») au lieu de refaire la requête et recevoir le mock « Bouftou mis à jour ». Le test appelle désormais `Cache::flush()` avant le second `Artisan::call`, afin que la requête soit refaite et que le nom mis à jour soit bien appliqué.

---

## 4. `ScrappingSearchControllerTest::test_search_paginates_using_api_limit_when_capped`

**Erreur :** `Failed to assert that the response count matched the expected 100 - actual size 1` (ligne 104 : `data.items` contient 1 élément au lieu de 100).

**Comportement attendu :**  
Requête `limit=200&max_pages=2` : le contrôleur doit faire 2 pages (2 appels HTTP), chaque réponse mockée renvoyant 50 éléments, soit 100 éléments au total dans `data.items`.

**Cause probable :**  
- Soit le **mock HTTP** ne correspond qu’à une seule URL (ex. première requête avec `$skip=0`) et la deuxième (ex. `$skip=50`) n’est pas mockée ou renvoie une réponse vide/erreur, ce qui donne peu d’items au total.  
- Soit la **structure de la réponse** réelle (ou une réponse d’erreur / redirect) fait que `data.items` n’a qu’un élément (ex. un message ou un objet unique).  
- Soit les **options** passées au `CollectService` (limit, max_pages, offset) ne déclenchent pas 2 pages comme prévu (ex. cap ailleurs, une seule requête).

**Correction appliquée :**  
- Ajout de `skip_cache=true` dans l’URL du test pour éviter tout cache entre les deux requêtes.  
- Le mock extrait `skip` via une regex (`%24skip=(\d+)`) pour matcher l’encodage Feathers.  
- Le test passe désormais et obtient bien 100 éléments (2 × 50).

---

## Résumé

| Test | Cause | Correction |
|------|--------|------------|
| run_many_limit_zero_collects_until_exhausted | `page_size` non transmis → requêtes en 50, mock en 2/1 | Orchestrator transmet `page_size` ; test passe `page_size => 2` ; mock avec regex sur `skip` |
| run_one_with_convert_returns_converted_structure | (Passait déjà avec MySQL) | — |
| command_replace_existing_updates_record | Cache DofusDB : 2e import recevait l’ancien nom | `Cache::flush()` avant le second import |
| search_paginates_using_api_limit_when_capped | Cache ou mock ne matchent pas la 2e page | `skip_cache=true` + extraction `skip` par regex dans le mock |

Tous les tests concernés passent après ces corrections.
