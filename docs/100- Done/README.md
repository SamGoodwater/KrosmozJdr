Le dossier `docs/100-Done` a pour objectif de centraliser les fichiers Markdown relatifs aux actions réalisées durant le développement du projet, ainsi qu'aux fonctionnalités finalisées. Il s'agit de l'espace où l'on documente l'évolution concrète du projet (refactorings, jalons atteints, fonctionnalités livrées, etc.), à la différence de la documentation technique pure. Ce dossier permet de garder une trace claire de l'historique des avancées et des décisions prises au fil du temps.

**Note** : Certains documents de ce dossier sont temporaires et seront supprimés une fois les systèmes stabilisés (ex: `ENTITY_DESCRIPTORS_PROGRESSION.md`).

## 2026-01-06 — Optimisations UI

- **Checkboxes optimisées** : Taille réduite (`xs`, `w-8`), affichage sur toutes les lignes dès qu'une est sélectionnée
- **Layout full-width** : Retrait de `max-w-4xl`, tableaux utilisent toute la largeur disponible avec scroll horizontal
- **Nom dans menus** : Affichage du nom de l'entité dans les menus dropdown et contextuels
- **Actions contextuelles** : Masquage intelligent des actions selon le contexte (`inPage`, `inModal`)
- **Doc** : `docs/100- Done/OPTIMISATIONS_UI_2026_01.md`

## 2025-12-26 — Refonte "Table v2" (TanStack Table)

- **Décision d'architecture** : nouvelle table générique basée sur TanStack Table, en **Atomic Design**.
- **Hybride "client-first"** : tri/recherche/filtres/pagination côté client par défaut, serveur **opt‑in** via `serverUrl` complet (Option A).
- **Contrats figés** : `TableConfig` (front) + `TableResponse`/`Cell{type,value,params}` (backend → front).
- **Doc technique** : `docs/30-UI/TANSTACK_TABLE.md`.
- **Fix sélection bulk/édition rapide** : normalisation des IDs + `useBulkEditPanel` rendu compatible `ref/computed` pour que la sélection multi soit bien prise en compte.
- **Design System** : bulk panels (Ressources / Types) migrés vers les Atoms (`SelectCore/InputCore/TextareaCore/RadioCore`).
- **Qualité UX** : debug panel activable sans console (bouton UI + param URL).
