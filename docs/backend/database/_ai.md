# Database — IA

> Schéma MySQL et seeders.

## Fichiers pivots

- `database/migrations/`
- `database/seeders/`
- `database/seeders/data/`
- Favoris user : table `user_favorites` (`user_id`, `entity_type`, `entity_id`)
- Colonnes JSON MySQL : pas de `DEFAULT` SQL (erreur 1101) ; défaut via `$attributes` Eloquent (`users.notification_channels`).

## Liens

- Entités : [../../features/entities/_ai.md](../../features/entities/_ai.md)
- Scrapping : [../../features/scrapping/_ai.md](../../features/scrapping/_ai.md)
