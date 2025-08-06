# Créatures (`creatures`)

## Rôle et description
Les créatures représentent tous les êtres vivants ou animés du jeu : personnages jouables, monstres, PNJ, invocations, animaux, etc. Elles constituent la base commune pour les entités vivantes, avec des caractéristiques, compétences, attributs et relations variées.

- **NPC** (Personnage Non Joueur) et **Monster** (Monstre) sont des sous-types de créature : ils héritent de la structure de base des créatures et ajoutent des champs ou relations spécifiques.
- Les créatures peuvent être contrôlées par un joueur, un MJ, ou le système.

## Relations principales
- **NPC** (`npcs`) : chaque NPC référence une créature (FK `creature_id`).
- **Monster** (`monsters`) : chaque monstre référence une créature (FK `creature_id`).
- **Attributs** : via le pivot `attribute_creature` (relation N:N avec `attributes`).
- **Objets possédés** : via le pivot `creature_item` (relation N:N avec `items`).
- **Sorts connus** : via le pivot `creature_spell` (relation N:N avec `spells`).
- **Ressources** : via le pivot `creature_resource` (relation N:N avec `resources`).
- **Capacités** : via le pivot `capability_creature` (relation N:N avec `capabilities`).
- **Consommables** : via le pivot `consumable_creature` (relation N:N avec `consumables`).
- **Panoplies** : via le pivot `npc_panoply` (pour les NPC).
- **Autres** : relations avec campagnes, scénarios, etc. via d'autres pivots.

## Exemples d’utilisation
- Création d’un personnage joueur ou d’un monstre pour un scénario.
- Attribution d’objets, sorts, attributs à une créature.
- Gestion des relations entre créatures et autres entités du jeu.

## Liens utiles
- [entity_npcs.md](entity_npcs.md) — NPC (sous-type de créature)
- [entity_monsters.md](entity_monsters.md) — Monster (sous-type de créature)
- [pivot_creature_item.md](../pivots/pivot_creature_item.md)
- [pivot_creature_spell.md](../pivots/pivot_creature_spell.md)
- [pivot_attribute_creature.md](../pivots/pivot_attribute_creature.md)
- [pivot_capability_creature.md](../pivots/pivot_capability_creature.md)
- [pivot_consumable_creature.md](../pivots/pivot_consumable_creature.md)
- [pivot_creature_resource.md](../pivots/pivot_creature_resource.md) 