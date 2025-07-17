# Consommables (`consumables`)

## Rôle et description
Les consommables regroupent tous les objets à usage unique ou limité : potions, nourritures, parchemins, etc. Ils permettent de restaurer des points de vie, d’obtenir des bonus temporaires, ou de déclencher des effets spéciaux.

## Relations principales
- **Type de consommable** : chaque consommable référence un type (`consumable_type_id`).
- **Créatures** : via le pivot `consumable_creature` (N:N avec `creatures`).
- **Ressources** : via le pivot `consumable_resource` (N:N avec `resources`).
- **Boutiques** : via le pivot `consumable_shop` (N:N avec `shops`).
- **Scénarios, campagnes** : via les pivots `consumable_scenario`, `consumable_campaign`.

## Exemples d’utilisation
- Utilisation d’une potion par un joueur.
- Création d’un parchemin de boost temporaire.

## Typage
Chaque consommable possède un type (`consumable_type_id`) qui permet de regrouper les consommables par usage (potion, nourriture, parchemin, etc.).
Exemples de types : potion, nourriture, parchemin, élixir, etc.

## Liens utiles
- [ENTITY_CREATURES.md](ENTITY_CREATURES.md)
- [ENTITY_SHOPS.md](ENTITY_SHOPS.md)
- [ENTITY_CONSUMABLE_TYPES.md](ENTITY_CONSUMABLE_TYPES.md) 