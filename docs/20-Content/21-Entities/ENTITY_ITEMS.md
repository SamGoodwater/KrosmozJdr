# Objets / Équipements (`items`)

## Rôle et description
Les objets regroupent tous les équipements, armes, armures, anneaux, etc. Ils sont utilisés par les créatures (joueurs, NPC, monstres) pour améliorer leurs capacités, obtenir des bonus, ou remplir des quêtes.

## Relations principales
- **Type d’objet** : chaque objet référence un type (`item_type_id`).
- **Créatures** : via le pivot `creature_item` (N:N avec `creatures`).
- **Panoplies** : via le pivot `item_panoply` (N:N avec `panoplies`).
- **Ressources** : via le pivot `item_resource` (N:N avec `resources`).
- **Scénarios, campagnes, boutiques** : via les pivots `item_scenario`, `item_campaign`, `item_shop`.

## Exemples d’utilisation
- Attribution d’un équipement à un joueur ou un monstre.
- Création d’une recette d’objet.

## Typage
Chaque objet possède un type (`item_type_id`) qui permet de regrouper les objets par catégorie (arme, armure, anneau, ceinture, bottes, etc.).
Exemples de types : arme, armure, anneau, ceinture, bottes, chapeau, familier, monture, etc.

## Liens utiles
- [ENTITY_CREATURES.md](ENTITY_CREATURES.md)
- [ENTITY_PANOPLIES.md](ENTITY_PANOPLIES.md)
- [ENTITY_ITEM_TYPES.md](ENTITY_ITEM_TYPES.md) 