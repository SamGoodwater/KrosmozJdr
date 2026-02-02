# Plan d’implémentation scrapping

Ce document décrit l’approche du scrapping : **config-driven**, CLI d’abord, et les liens vers la découverte de l’API. Contexte : [ETAT_AVANCEMENT.md](./ETAT_AVANCEMENT.md).

---

## 1. Découverte API et configuration

- **Référence API DofusDB** : [DECOUVERTE_API_DOFUSDB.md](./DECOUVERTE_API_DOFUSDB.md) — base URL, pagination (`$limit`, `$skip`), filtres Feathers, structure des réponses, endpoints par entité (breeds, monsters, spells, items, etc.).
- **Configs** : `resources/scrapping/config/sources/dofusdb/` — `source.json`, `entities/*.json`, `collect_aliases.json`. Le service de collecte et le mapping sont pilotés par ces fichiers ; toute nouvelle entité ou nouveau champ passe par la config.

---

## 2. Principes

| Principe | Application |
|----------|--------------|
| **Config-driven** | Requêtes (endpoints, pagination, filtres) et mapping (source → cible + formatter) dans des JSON. Les formules complexes (level, life, attributs, résistances) sont déléguées à la BDD (`dofusdb_conversion_formulas`, handlers nommés). |
| **CLI d’abord** | Tout testable en ligne de commande : `php artisan scrapping --collect=monster --id=31 [--convert] [--validate] [--integrate] [--dry-run]`. L’API et l’UI s’appuient sur les mêmes services. |
| **Services indépendants** | Collect → Conversion → Validation → Intégration. Chaque brique a une responsabilité claire ; l’orchestrateur enchaîne sans logique métier DofusDB/KrosmozJDR. |

---

## 3. Ordre de résolution des relations

Voir [RELATIONS.md](./RELATIONS.md) : graphe des dépendances, comportement actuel (Orchestrator n’intègre que l’entité principale ; RelationResolutionService pour monster sorts/drops), option `include_relations`.

---

## 4. Références

- [ETAT_AVANCEMENT.md](./ETAT_AVANCEMENT.md) — État actuel du scrapping.
- [DECOUVERTE_API_DOFUSDB.md](./DECOUVERTE_API_DOFUSDB.md) — API DofusDB (endpoints, pagination, filtres).
- [VISION_ET_ARCHITECTURE.md](./VISION_ET_ARCHITECTURE.md) — Vision et interdépendances.
- [RELATIONS.md](./RELATIONS.md) — Ordre de résolution des relations.
- [README](./README.md) — Contenu du dossier Architecture.
