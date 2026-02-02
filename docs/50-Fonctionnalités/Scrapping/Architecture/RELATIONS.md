# Ordre de résolution des relations

Ce document décrit le **graphe des dépendances** entre entités et le **comportement actuel** du pipeline pour les relations (sorts, drops).

---

## 1. Graphe des dépendances

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

## 2. Comportement actuel

### 2.1 Orchestrateur et IntegrationService

- **Orchestrator** : enchaîne Collect → Conversion → Validation → Intégration. Il ne gère pas la résolution des relations entre entités (sorts, drops, recettes).
- **IntegrationService** : enregistre les données converties **d’une entité à la fois**. Pour `monster`, il crée/met à jour **Creature** et **Monster** ; il ne crée pas les sorts ni les drops, et ne fait pas de `creature->spells()->sync()` ni `creature->resources()->sync()`.

### 2.2 RelationResolutionService

Le **RelationResolutionService** résout les relations **monster** (sorts, drops) lorsqu’il est appelé explicitement avec :
- les données brutes du monstre (contenant `spells` et `drops`),
- l’ID de la créature KrosmozJDR déjà intégrée,
- les options du pipeline (integrate, dry_run, etc.).

Il importe chaque sort et chaque item des drops via `Orchestrator::runOne`, récupère les IDs KrosmozJDR, puis synchronise `creature_spell` et `creature_resource` sur la créature. Voir [RelationResolutionService](../../../app/Services/Scrapping/Core/Relation/RelationResolutionService.php).

L’API et la CLI d’import monster appellent actuellement uniquement `Orchestrator::runOne` (entité principale). Le branchement de RelationResolutionService depuis le contrôleur ou la CLI (ex. option `include_relations`) est possible si les données brutes collectées incluent `spells` et `drops`.

---

## 3. Références

- [VISION_ET_ARCHITECTURE.md](./VISION_ET_ARCHITECTURE.md) — Vision et interdépendances.
- [ETAT_AVANCEMENT.md](./ETAT_AVANCEMENT.md) — État actuel du scrapping.
