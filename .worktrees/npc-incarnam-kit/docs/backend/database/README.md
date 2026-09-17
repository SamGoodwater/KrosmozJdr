# Database

La base MySQL contient les tables Laravel, le CMS, les entités JDR, les caractéristiques/effets et le scrapping.

## Groupes

- Entités : `breeds`, `spells`, `items`, `resources`, `monsters`, `npcs`, `campaigns`, `scenarios`, etc.
- CMS : `pages`, `sections`, pivots liés.
- Caractéristiques / effets : `characteristics`, pivots `characteristic_*`, `effects`, `sub_effects`, `effect_degrees`, `object_effects`.
- Scrapping : `scrapping_jobs`, `scrapping_entity_mappings`, targets et pivots.
- Infra Laravel : `users`, `jobs`, `notifications`, `sessions`, `media`.

## Où modifier

- Migrations : `database/migrations/`.
- Seeders : `database/seeders/`.
- Données de seed : `database/seeders/data/`.
