Le dossier `docs/100-Done` a pour objectif de centraliser les fichiers Markdown relatifs aux actions réalisées durant le développement du projet, ainsi qu’aux fonctionnalités finalisées. Il s’agit de l’espace où l’on documente l’évolution concrète du projet (refactorings, jalons atteints, fonctionnalités livrées, etc.), à la différence de la documentation technique pure. Ce dossier permet de garder une trace claire de l’historique des avancées et des décisions prises au fil du temps.

## 2025-12-26 — Refonte “Table v2” (TanStack Table)

- **Décision d’architecture** : nouvelle table générique basée sur TanStack Table, en **Atomic Design**.
- **Hybride “client-first”** : tri/recherche/filtres/pagination côté client par défaut, serveur **opt‑in** via `serverUrl` complet (Option A).
- **Contrats figés** : `TableConfig` (front) + `TableResponse`/`Cell{type,value,params}` (backend → front).
- **Doc technique** : `docs/30-UI/TANSTACK_TABLE.md`.
