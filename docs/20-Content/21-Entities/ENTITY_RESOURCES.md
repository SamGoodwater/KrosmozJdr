# Ressources (`resources`)

## Rôle et description
Les ressources regroupent tous les matériaux de base du jeu : minerais, plantes, peaux, ingrédients, etc. Elles servent à la fabrication d’objets, de consommables, à l’accomplissement de quêtes ou à l’économie du jeu.

## Relations principales
- **Type de ressource** : chaque ressource référence un type (`resource_type_id`).
- **Créatures** : via le pivot `creature_resource` (N:N avec `creatures`).
- **Objets** : via le pivot `item_resource` (N:N avec `items`).
- **Consommables** : via le pivot `consumable_resource` (N:N avec `consumables`).
- **Boutiques** : via le pivot `resource_shop` (N:N avec `shops`).
- **Scénarios, campagnes** : via les pivots `resource_scenario`, `resource_campaign`.

## Exemples d’utilisation
- Récolte d’une ressource par un joueur.
- Utilisation d’une ressource dans une recette d’objet ou de consommable.

## Typage
Chaque ressource possède un type (`resource_type_id`) qui permet de regrouper les ressources par famille (minerai, plante, peau, bois, etc.).
Exemples de types : minerai, plante, peau, bois, poisson, tissu, etc.

## Liens utiles
- [ENTITY_ITEMS.md](ENTITY_ITEMS.md)
- [ENTITY_CONSUMABLES.md](ENTITY_CONSUMABLES.md)
- [ENTITY_SHOPS.md](ENTITY_SHOPS.md)
- [ENTITY_RESOURCE_TYPES.md](ENTITY_RESOURCE_TYPES.md) 