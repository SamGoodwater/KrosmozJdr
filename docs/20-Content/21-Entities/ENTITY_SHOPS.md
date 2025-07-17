# Boutiques (`shops`)

## Rôle et description
Les boutiques représentent tous les points de vente, marchands, échoppes ou vendeurs du jeu. Elles permettent aux joueurs d’acheter, vendre ou échanger des objets, ressources, consommables, etc.

## Relations principales
- **NPC** : chaque boutique peut être liée à un NPC (`npc_id`).
- **Objets** : via le pivot `item_shop` (N:N avec `items`).
- **Ressources** : via le pivot `resource_shop` (N:N avec `resources`).
- **Consommables** : via le pivot `consumable_shop` (N:N avec `consumables`).
- **Panoplies** : via le pivot `panoply_shop` (N:N avec `panoplies`).
- **Monstres** : via le pivot `monster_shop` (N:N avec `monsters`).
- **Scénarios, campagnes** : via les pivots `scenario_shop`, `campaign_shop`.

## Exemples d’utilisation
- Achat d’un objet ou d’une ressource par un joueur.
- Création d’un marchand spécifique pour un scénario.

## Liens utiles
- [ENTITY_NPCS.md](ENTITY_NPCS.md)
- [ENTITY_ITEMS.md](ENTITY_ITEMS.md)
- [ENTITY_RESOURCES.md](ENTITY_RESOURCES.md)
- [ENTITY_CONSUMABLES.md](ENTITY_CONSUMABLES.md) 