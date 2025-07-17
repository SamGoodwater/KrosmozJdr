# Scénarios (`scenarios`)

## Rôle et description
Les scénarios représentent les aventures, quêtes, donjons ou missions jouables dans le jeu. Ils structurent la progression narrative, les objectifs, les récompenses et les interactions entre joueurs, MJ et entités du jeu.

## Relations principales
- **Créateur** : chaque scénario référence son créateur (`created_by`).
- **Pages** : via le pivot `scenario_page` (N:N avec `pages`).
- **Fichiers** : via le pivot `file_scenario` (N:N avec `files`).
- **Objets, ressources, consommables, panoplies** : via les pivots `item_scenario`, `resource_scenario`, `consumable_scenario`, `panoply_scenario`.
- **Monstres, NPC** : via les pivots `monster_scenario`, `npc_scenario`.
- **Boutiques, sorts** : via les pivots `scenario_shop`, `scenario_spell`.
- **Campagnes** : via le pivot `campaign_scenario` (N:N avec `campaigns`).
- **Utilisateurs** : via le pivot `scenario_user` (N:N avec `users`).
- **Liens scénarios** : via le pivot `scenario_link` (enchaînement, conditions).

## Exemples d’utilisation
- Création d’une quête ou d’un donjon.
- Ajout d’objets, monstres, pages à un scénario.

## Liens utiles
- [ENTITY_CAMPAIGNS.md](ENTITY_CAMPAIGNS.md)
- [ENTITY_PAGES.md](ENTITY_PAGES.md)
- [ENTITY_ITEMS.md](ENTITY_ITEMS.md)
- [ENTITY_MONSTERS.md](ENTITY_MONSTERS.md) 