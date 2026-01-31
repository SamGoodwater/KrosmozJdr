## Définitions — Intégration (Scrapping)

### Objectif
Ce document décrit les conventions d’intégration des données scrappées (DofusDB) dans KrosmozJDR :
- comment on identifie une entité existante,
- quelles tables/modèles sont ciblés,
- quelles options modifient le comportement (dry-run, force-update, images, relations),
- comment s’interprètent les “actions” renvoyées.

---

## Identification d’une entité (anti-doublon)
Règle de base :
1) si le modèle a un champ `dofusdb_id`, on cherche d’abord par **`dofusdb_id`**,
2) sinon (ou en fallback), on peut chercher par **`name`**.

Pourquoi :
- `name` n’est pas une clé stable (variations, collisions, traductions),
- `dofusdb_id` est la meilleure clé externe.

---

## Cibles (tables / modèles)
La conversion “config-driven” peut produire un payload multi-modèles (ex: monstre → `creatures` + `monsters`).

Exemples de cibles courantes :
- `class` → `classes`
- `monster` → `creatures` + `monsters`
- `item` → `items` ou `resources` ou `consumables` (selon typeId + registry)
- `spell` → `spells`
- `panoply` → `panoplies`

> La source de vérité du mapping est `resources/scrapping/sources/dofusdb/entities/*.json`.

---

## Options d’intégration
Ces options viennent typiquement de l’API / CLI et sont propagées jusqu’à l’intégration :

- **`dry_run`**
  - `true` : aucune écriture DB, mais on exécute la chaîne pour savoir “ce qui se passerait”
  - `false` : écritures normales (transactions, relations, etc.)

- **`force_update`**
  - `true` : autorise l’update si l’entité existe déjà
  - `false` : si l’entité existe, on renvoie une action `skipped` (ou `would_skip` en dry-run)

- **`with_images`** (défaut `true`)
  - `true` : télécharge/stocke les images (selon la config scrapping images)
  - `false` : ne télécharge rien (utile en batch/perf)

- **`include_relations`** (défaut `true`)
  - `true` : l’orchestrateur peut importer des entités liées
  - `false` : import unitaire (sans cascades)

- **`validate_only`**
  - stoppe avant intégration (retourne raw/converted)

---

## Actions renvoyées
Pour faciliter l’UI et la traçabilité, l’intégration renvoie une action :
- `would_create` / `would_update` / `would_skip` (si `dry_run=true`)
- `created` / `updated` / `skipped` (si `dry_run=false`)

---

## Lien avec le code
Implémentation :
- `app/Services/Scrapping/DataIntegration/DataIntegrationService.php`

Orchestration (incluant relations, validate_only, etc.) :
- `app/Services/Scrapping/Orchestrator/ScrappingOrchestrator.php`

