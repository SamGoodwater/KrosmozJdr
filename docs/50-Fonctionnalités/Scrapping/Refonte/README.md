# Refonte du scrapping KrosmozJDR

Ce dossier centralise les idées, l’audit et la documentation de la refonte de la fonctionnalité scrapping. L’objectif est de **remplacer entièrement** l’ancienne implémentation par une version plus simple, modulaire et maintenable.

## Contenu

- **[ETAT_AVANCEMENT_REFONTE_V2.md](./ETAT_AVANCEMENT_REFONTE_V2.md)** — **État d’avancement** : ce qui est fait (V2), ce qui est en prod (legacy), ce qui reste à faire.
- **[TODOLIST_REFONTE_V2.md](./TODOLIST_REFONTE_V2.md)** — **To-do list** : tâches détaillées (Phase 1 alignement BDD, Phase 2 résistances, Phase 3 branchement prod, Phase 4 nettoyage).
- **[PLAN_IMPLEMENTATION.md](./PLAN_IMPLEMENTATION.md)** — **Plan d’implémentation** : ordre des étapes, approche greenfield, lien vers la découverte API.
- **[RELATIONS_V2.md](./RELATIONS_V2.md)** — **Ordre de résolution des relations** (V2) et comparaison à la vision.
- **[AUDIT_ETAT_DES_LIEUX.md](./AUDIT_ETAT_DES_LIEUX.md)** — État des lieux : où se trouve le code, comment il est utilisé, dépendances et points de douleur.
- **[VISION_ET_ARCHITECTURE.md](./VISION_ET_ARCHITECTURE.md)** — Vision cible, architecture (Collect → Conversion → Validation → Intégration), tableau de mapping, interdépendances, validation vs characteristics, questions et difficultés anticipées.
- **[DECOUVERTE_API_DOFUSDB.md](./DECOUVERTE_API_DOFUSDB.md)** — Découverte API DofusDB : endpoints, structure des réponses, pagination, filtres.
- **[SCHEMA_CONFIG_V2.md](./SCHEMA_CONFIG_V2.md)** — Schéma des configs V2 (requêtes + mapping) et entité pilote (monster).
- **[DECISION_BONUS_EFFECTS_STOCKAGE.md](./DECISION_BONUS_EFFECTS_STOCKAGE.md)** — Bonus / effets d’équipement : garder JSON en base ou passer à une table / système plus robuste (type DofusDB).

## Principes visés

- **Services indépendants** : chaque brique (collecte, conversion, intégration) doit avoir une responsabilité claire et des dépendances explicites.
- **Code regroupé** : moins de dispersion entre `config/`, `app/Services/Scrapping/`, `resources/scrapping/`, caractéristiques, etc.
- **Remplacer, pas dupliquer** : la refonte remplace l’existant ; pas de double système en parallèle à long terme.
