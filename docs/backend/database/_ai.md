# Database — IA

> Schéma MySQL et seeders.

## Fichiers pivots

- `database/migrations/`
- `database/seeders/`
- `database/seeders/data/` — JSON versionnés (items, panoplies, consommables, sorts de classe niveau 1)
- Favoris user : table `user_favorites` (`user_id`, `entity_type`, `entity_id`)
- Jobs console admin : table `project_console_jobs` (domaine, %, sortie filtrée)
- Réglages IA métier : table `ia_generation_settings` (JSON `payload`, surcharge de `resources/ia/generation.json`)
- Colonnes JSON/TEXT MySQL : pas de `DEFAULT` SQL (erreur 1101) ; défaut via `$attributes` Eloquent (`users.notification_channels`, `creatures.res_fixe_*`, registres de types `show_in_catalog` / `allow_scrap`).
- `conditions.canonical_condition_id` : FK self nullable, jeton Dofus → état JDR `playable`.

## Liens

- Entités : [../../features/entities/_ai.md](../../features/entities/_ai.md)
- Scrapping : [../../features/scrapping/_ai.md](../../features/scrapping/_ai.md)
