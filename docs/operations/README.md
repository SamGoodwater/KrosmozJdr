# Operations

Commandes et opérations de maintenance du projet.

## Commandes importantes

- `php artisan project:init` : initialisation projet.
- `php artisan project:data:import-rules-toc` : importe les règles Markdown depuis `private/game/rules/TABLE_DES_MATIERES.md` vers le CMS.
- `php artisan pages:import-rules-toc` : commande bas niveau d'import des règles.
- `php artisan scrapping:setup` : initialise les données nécessaires au scrapping.
- `php artisan scrapping:run` : lance les imports DofusDB.

## Contenus privés utilisés par commandes

- Règles JDR : `private/game/rules/`.
- Ressources de jeu : `private/game/resources/`.
