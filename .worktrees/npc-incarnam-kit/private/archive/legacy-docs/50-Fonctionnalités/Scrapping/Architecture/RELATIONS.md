# Ordre de résolution des relations

Ce document décrit le **graphe des dépendances** entre entités, le **comportement actuel** du pipeline et le **système de pile** pour **toutes** les relations (classes→sorts, sorts→invocations, monstres→sorts/drops, ressources→recettes).

---

## 1. Système de pile (toutes les relations)

Un **même mécanisme de pile** sert pour **toutes** les relations : on empile ce qui reste à récupérer (sorts, monstres, items, etc.) et on garde en mémoire **qui dépend de quoi**. Lorsqu’un élément est chargé, on récupère son id et on met à jour les tables de relation concernées. Une requête initiale (ex. « importer une classe ») peut ainsi déclencher l’import de dizaines d’objets (sorts de la classe → sorts d’invocation → monstres invoqués → drops des monstres → recettes des ressources, etc.).

- **RelationImportStack** : pile partagée pendant un run.
  - **En attente** : file de `(source, entity, dofusdb_id)` à importer (ex. `spell`, `monster`, `item`).
  - **Dépendants** : pour chaque clé `entity:dofusdb_id`, liste de `{ type, payload }` à résoudre à l’import (types : `recipe`, `breed_spell`, `creature_spell`, `creature_resource`, `spell_invocation`).

**Flux :**

1. Après intégration d’une entité (classe, sort, monstre, ressource), si `include_relations` est actif :
   - **Classe** : récupération des IDs de sorts via `/spell-levels?breedId=`, enregistrement des dépendants `breed_spell`, ajout des sorts manquants à la pile.
   - **Sort** : si `raw.summon` existe, enregistrement du dépendant `spell_invocation` et ajout du monstre invoqué à la pile.
   - **Monstre** : pour chaque sort et chaque drop (item), enregistrement des dépendants `creature_spell` / `creature_resource` et ajout des sorts/items manquants à la pile.
   - **Ressource** : pour chaque ingrédient de recette absent, enregistrement du dépendant `recipe` et ajout de l’item à la pile.
2. **Drainage de la pile** : tant qu’il reste un élément, on dépile, on appelle `runOne(source, entity, dofusdb_id)` (avec la même pile en options), puis `onImported(entity, dofusdb_id, primaryId, table)` pour résoudre tous les dépendants (mise à jour de `breed_spell`, `creature_spell`, `creature_resource`, `resource_recipe`, `spell_invocation`).
3. Les éléments importés pendant le drainage peuvent à leur tour ajouter des dépendances à la pile (ex. un monstre importé a des sorts et drops → nouveaux éléments en attente). La pile est donc drainée jusqu’à vide.

Fichiers concernés :  
`RelationImportStack`, `RelationResolutionService` (resolveAndSyncBreedSpells, resolveAndSyncMonsterRelations, resolveAndSyncSpellInvocation, resolveAndSyncResourceRecipe avec `?RelationImportStack $stack`), `Orchestrator::resolveRelationsAndDrain()` et `drainRelationImportStack()`.

---

## 2. Graphe des dépendances

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

Un ordre d’import cohérent exige que les objets référencés existent avant ceux qui les référencent.

---

## 3. Comportement actuel

### 3.1 Orchestrateur et IntegrationService

- **Orchestrator** : enchaîne Collect → Conversion → Validation → Intégration. Après chaque intégration réussie, si `include_relations` est actif, il appelle **resolveRelationsAndDrain** (pile unique pour toutes les relations).
- **IntegrationService** : enregistre les données converties **d’une entité à la fois**. Il ne fait pas lui-même les sync de relations (breed_spell, creature_spell, etc.) ; c’est la pile qui les fait au drainage.

### 3.2 RelationResolutionService

Le **RelationResolutionService** enregistre les dépendances et, si une **RelationImportStack** est fournie, ajoute les éléments manquants à la pile (sinon, comportement inline historique). Méthodes :

- **resolveAndSyncBreedSpells** : récupère les IDs de sorts via `CollectService::fetchSpellIdsByBreedId` (/spell-levels?breedId=), enregistre les dépendants `breed_spell`, sync avec les sorts déjà en base.
- **resolveAndSyncMonsterRelations** : pour les données brutes (spells, drops), enregistre les dépendants `creature_spell` et `creature_resource`, sync avec les sorts/ressources déjà en base.
- **resolveAndSyncSpellInvocation** : si le sort a un `summon`, enregistre le dépendant `spell_invocation` et ajoute le monstre à la pile.
- **resolveAndSyncResourceRecipe** : comme avant, dépendants `recipe` et pile des ingrédients manquants.

L’orchestrateur appelle ces résolutions après chaque intégration (breed, spell, monster, resource) puis draine la pile une seule fois par entité intégrée. La pile est partagée entre tous les appels récursifs (runOne pendant le drainage), ce qui permet de gérer les cascades (classe → sorts → invocations → monstres → drops → recettes).

---

## 4. Références

- [VISION_ET_ARCHITECTURE.md](./VISION_ET_ARCHITECTURE.md) — Vision et interdépendances.
- [ETAT_AVANCEMENT.md](./ETAT_AVANCEMENT.md) — État actuel du scrapping.
