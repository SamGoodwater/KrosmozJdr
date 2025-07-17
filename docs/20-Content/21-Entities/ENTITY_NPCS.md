# NPC (`npcs`)

## Héritage
Un NPC (Personnage Non Joueur) est une [créature](ENTITY_CREATURES.md) avec des champs et relations spécifiques. Il représente un personnage contrôlé par le MJ ou le système, non par un joueur.

## Rôle et description
Les NPC servent à enrichir l’univers, proposer des quêtes, vendre des objets, interagir avec les joueurs, etc. Ils peuvent avoir une histoire, une classe, une spécialisation, et être liés à des boutiques.

## Relations principales
- **Créature** : chaque NPC référence une créature (`creature_id`).
- **Classe** : chaque NPC peut référencer une classe (`classe_id`).
- **Spécialisation** : chaque NPC peut référencer une spécialisation (`specialization_id`).
- **Boutique** : un NPC peut être lié à une boutique (`npc_id` dans `shops`).
- **Panoplies** : via le pivot `npc_panoply` (N:N avec `panoplies`).
- **Scénarios** : via le pivot `npc_scenario` (N:N avec `scenarios`).
- **Campagnes** : via le pivot `npc_campaign` (N:N avec `campaigns`).

## Exemples d’utilisation
- Création d’un marchand, d’un donneur de quête, d’un personnage clé d’un scénario.

## Liens utiles
- [ENTITY_CREATURES.md](ENTITY_CREATURES.md)
- [ENTITY_CLASSES.md](ENTITY_CLASSES.md)
- [ENTITY_SPECIALIZATIONS.md](ENTITY_SPECIALIZATIONS.md)
- [ENTITY_SHOPS.md](ENTITY_SHOPS.md) 