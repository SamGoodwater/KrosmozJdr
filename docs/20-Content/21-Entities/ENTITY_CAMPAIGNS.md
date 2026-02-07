# Campagnes (`campaigns`)

## Rôle et description
Les campagnes regroupent un ensemble de scénarios, pages, entités et joueurs autour d’une même aventure ou d’un même arc narratif. Elles permettent de structurer la progression sur le long terme, la gestion des groupes et la personnalisation de l’expérience.

## Relations principales
- **Créateur** : chaque campagne référence son créateur (`created_by`).
- **Scénarios** : via le pivot `campaign_scenario` (N:N avec `scenarios`).
- **Pages** : via le pivot `campaign_page` (N:N avec `pages`).
- **Fichiers** : médias attachés via Media Library (`$campaign->getMedia('files')`). Voir [ENTITY_FILES.md](ENTITY_FILES.md).
- **Objets, ressources, consommables, panoplies** : via les pivots `item_campaign`, `resource_campaign`, `consumable_campaign`, `panoply_campaign`.
- **Monstres, NPC** : via les pivots `monster_campaign`, `npc_campaign`.
- **Boutiques, sorts** : via les pivots `campaign_shop`, `campaign_spell`.
- **Utilisateurs** : via le pivot `campaign_user` (N:N avec `users`).

## Exemples d’utilisation
- Création d’une campagne de plusieurs scénarios.
- Gestion des groupes de joueurs et de la progression.

## Liens utiles
- [ENTITY_SCENARIOS.md](ENTITY_SCENARIOS.md)
- [ENTITY_PAGES.md](ENTITY_PAGES.md)
- [ENTITY_ITEMS.md](ENTITY_ITEMS.md) 