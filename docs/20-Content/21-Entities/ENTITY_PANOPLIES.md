# Panoplies (`panoplies`)

## Rôle et description
Les panoplies représentent les ensembles d’objets (sets) qui, une fois réunis, confèrent des bonus ou des effets spéciaux aux personnages. Elles encouragent la collection, la spécialisation et la stratégie d’équipement.

## Relations principales
- **Objets** : via le pivot `item_panoply` (N:N avec `items`).
- **Boutiques** : via le pivot `panoply_shop` (N:N avec `shops`).
- **Scénarios, campagnes** : via les pivots `panoply_scenario`, `panoply_campaign`.
- **NPC, monstres** : via les pivots `npc_panoply`, `monster_panoply`.

## Exemples d’utilisation
- Attribution d’un bonus de set à un joueur ayant réuni tous les objets d’une panoplie.
- Vente d’une panoplie complète dans une boutique.

## Liens utiles
- [ENTITY_ITEMS.md](ENTITY_ITEMS.md)
- [ENTITY_SHOPS.md](ENTITY_SHOPS.md) 