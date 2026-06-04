# Database — carte IA (degré 1) [STUB]

> Schéma MySQL (`krosmozDB`) : ~111 migrations, entités de jeu + CMS + scrapping + caractéristiques/effets + infra Laravel.

> Statut : stub. À remixer dans `docV2`.

## Quand lire ce nœud

- Comprendre le schéma, ajouter/modifier une table, une relation, une migration.

## Repères

- Migrations : `database/migrations/`.
- Seeders : `database/seeders/` (+ data `database/seeders/data/`).
- Schéma ER généré : `docs/20-Content/SCHEMA.md` (régénéré par `pnpm run update:docs`).
- Modèle métier détaillé : `docs/20-Content/21-Entities/ENTITIES_OVERVIEW.md`.

## Groupes de tables

- Entités : `breeds`, `specializations`, `capabilities`, `creatures`, `monsters`, `npcs`, `spells`, `items`, `consumables`, `resources`, `panoplies`, `conditions`, `creature_traits`, `campaigns`, `scenarios`, `shops`, `languages`.
- Types : `item_types`, `resource_types`, `consumable_types`, `spell_types`, `monster_races`.
- Effets / caractéristiques : `characteristics` (+ pivots), `effects`, `sub_effects`, `effect_degrees`, `object_effects`, `dofusdb_effect_mappings`.
- CMS : `pages`, `sections` (+ pivots).
- Scrapping : `scrapping_jobs`, `scrapping_entity_mappings` (+ targets).

## Descendre

- Entités (modèle + droits) : [features/entities/_ai.md](../features/entities/_ai.md).
