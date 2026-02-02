# Ordre de résolution des relations — Scrapping V2

Ce document décrit **l’ordre de résolution des relations** dans le pipeline scrapping V2 et le compare à la vision cible ([VISION_ET_ARCHITECTURE.md](./VISION_ET_ARCHITECTURE.md) § 2.4). Il sert de référence pour la Phase 4.2.

---

## 1. Graphe des dépendances (vision)

D’après la vision, les entités DofusDB/KrosmozJDR sont liées ainsi :

| Entité | Dépend de / est liée à | Explication |
|--------|------------------------|-------------|
| **Classes** | **Sorts** | Les classes possèdent des sorts (spell-levels par breed). |
| **Sorts** | **Monstres** | Les sorts peuvent invoquer → lien vers des monstres (invocations). |
| **Monstres** | **Ressources, équipements, consommables** | Drops : les monstres donnent des ressources, équipements ou consommables. |
| **Consommables / Équipements** | **Ressources** | Recettes : les consommables et équipements sont constitués de ressources (ingrédients). |

Résumé des flux :
- **Classes** → ont des **sorts**
- **Sorts** → peuvent invoquer des **monstres**
- **Monstres** → ont des **drops** (ressources, équipements, consommables)
- **Équipements / Consommables** → ont des **recettes** (ressources)

Un **ordre d’import** cohérent exige que les objets référencés existent avant ceux qui les référencent (ex. ressources et types avant équipements/consommables ; monstres et sorts avant classes si on résout les liens à l’import).

---

## 2. Comportement actuel du pipeline V2

### 2.1 Orchestrateur et IntegrationService V2

- **Orchestrateur V2** : enchaîne Collect → Conversion → Validation → Intégration. Il ne gère **pas** la résolution des relations entre entités (sorts, drops, recettes).
- **IntegrationService V2** : enregistre uniquement les données converties **d’une entité à la fois**. Pour `monster`, il crée/met à jour **Creature** et **Monster** ; il ne crée pas les sorts ni les drops, et ne fait pas de `creature->spells()->sync()` ni `creature->resources()->sync()`.

En résumé : **le V2 ne résout pas les relations** ; il ne fait qu’intégrer l’entité principale (ex. monstre) sans lier les entités associées.

### 2.2 Import monster en production (hybride V2 + legacy)

L’import monster utilisé par l’API `POST /api/scrapping/import/monster/{id}` (bouton « Rafraîchir ») fonctionne ainsi :

1. **Collecte** (legacy) : `DataCollectService::collectMonster($id, $includeRelations, $includeRelations, $options)` récupère les données brutes du monstre **et** les tableaux `spells` et `drops` si `include_relations` est vrai.
2. **Conversion, validation, intégration** (V2) : `ScrappingV2Orchestrator::runOneWithRaw('dofusdb', 'monster', $rawData, $v2Options)` applique le pipeline V2 (conversion BDD, validation, intégration). Seuls Creature et Monster sont créés/mis à jour.
3. **Relations** (legacy) : si `include_relations` est vrai et qu’une Creature a été intégrée :
   - Pour chaque entrée de `rawData['spells']`, appel à `ScrappingOrchestrator::importSpell($spellId, ...)` (legacy).
   - Pour chaque entrée de `rawData['drops']`, appel à `ScrappingOrchestrator::importItem($resourceId, ...)` (legacy).
   - Puis `$creature->spells()->sync($validSpellIds)` et `$creature->resources()->sync(...)` pour lier la créature aux sorts et ressources importés.

**Ordre de résolution effectif pour l’import monster :**
1. Monstre (V2) → Creature + Monster.
2. Sorts liés (legacy, un par un).
3. Ressources/items des drops (legacy, un par un).
4. Sync des relations sur la Creature (spells, resources).

Les relations sont donc gérées **en aval** du V2, dans le contrôleur, en s’appuyant sur l’orchestrateur legacy pour importer les entités liées puis en synchronisant les associations sur la créature.

---

## 3. Comparaison avec la vision

| Point de la vision | Statut actuel |
|--------------------|----------------|
| **Ordre de résolution** | Partiel : pour monster, l’entité principale est intégrée en V2 ; les entités liées (sorts, drops) sont importées ensuite via legacy, puis les associations sont synchronisées. Il n’existe pas encore d’ordre global documenté côté V2 (ex. ressources → équipements → monstres → sorts → classes). |
| **Résolution des relations** | Hors V2 : les IDs DofusDB (spells, drops) sont résolus en important les entités via l’orchestrateur legacy et en synchronisant les relations sur la Creature. Le V2 ne contient pas de module « résolution de relations » ni de config des relations par entité. |
| **Option « avec relations »** | Présente côté API : `include_relations` dans la requête d’import monster contrôle si les sorts et drops sont importés et synchronisés. C’est une option du contrôleur, pas de l’orchestrateur V2. |
| **Config des relations** | Absente en V2 : la configuration ne décrit pas, par entité, quelles relations existent (champ source DofusDB → entité cible KrosmozJDR). La logique des relations monster (spells, drops) est en dur dans `ScrappingController::importMonster`. |

En résumé : la **vision** prévoit un graphe de dépendances explicite, un ordre de résolution et une config des relations pilotant l’intégration ; **actuellement**, le V2 ne gère que l’entité principale, et les relations pour l’import monster sont gérées en hybride (legacy + sync dans le contrôleur).

---

## 4. Pistes pour une résolution des relations 100 % V2 (futur)

Pour aligner le V2 sur la vision sans dépendre du legacy :

1. **Config des relations** : dans les configs d’entités V2 (ex. `monster.json`), décrire les relations (ex. `spells` → entité `spell`, `drops` → entité `item`/resource), avec les champs source et cible.
2. **Ordre de résolution** : soit l’orchestrateur V2 enchaîne l’import des entités liées (selon un ordre défini ou un graphe) avant de synchroniser les associations ; soit un module dédié « résolution de relations » reçoit les données converties + les entités déjà intégrées et met à jour les FK / tables pivot.
3. **Option include_relations** : la passer jusqu’à l’orchestrateur V2 (ou au module de résolution) pour décider d’importer ou non les entités liées et de faire les sync.

Ces évolutions restent à specifier et à implémenter ; ce document sert de base pour la Phase 4.2 et les suivantes.

---

## 5. Références

- [VISION_ET_ARCHITECTURE.md](./VISION_ET_ARCHITECTURE.md) — Vision cible, § 2.4 Interdépendances des objets.
- [ETAT_AVANCEMENT_REFONTE_V2.md](./ETAT_AVANCEMENT_REFONTE_V2.md) — État d’avancement de la refonte.
- [PLAN_IMPLEMENTATION.md](./PLAN_IMPLEMENTATION.md) — Plan d’implémentation, Phase 4.2.
