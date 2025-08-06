# Monstres (`monsters`)

## Héritage
Un monstre est une [créature](ENTITY_CREATURES.md) avec des champs et relations spécifiques. Il représente un adversaire, une menace ou un obstacle dans le jeu, contrôlé par le MJ ou le système.

## Rôle et description
Les monstres servent à défier les joueurs, enrichir les combats, proposer des récompenses, ou incarner des boss. Ils peuvent appartenir à une race, avoir des capacités, des objets, des sorts, etc.

## Relations principales
- **Créature** : chaque monstre référence une créature (`creature_id`).
- **Race** : chaque monstre peut référencer une race (`monster_race_id`).
- **Scénarios** : via le pivot `monster_scenario` (N:N avec `scenarios`).
- **Campagnes** : via le pivot `monster_campaign` (N:N avec `campaigns`).
- **Boutiques** : via le pivot `monster_shop` (N:N avec `shops`).
- **Panoplies** : via le pivot `monster_panoply` (N:N avec `panoplies`).

## Typage
Chaque monstre peut appartenir à une race (`monster_race_id`) qui regroupe les monstres par famille ou espèce (bouftou, tofu, pichon, etc.).
Exemples de races : bouftou, tofu, pichon, craqueleur, etc.

## Exemples d’utilisation
- Création d’un groupe d’ennemis pour un donjon.
- Attribution d’une race, d’objets ou de sorts à un monstre.

## Liens utiles
- [ENTITY_CREATURES.md](ENTITY_CREATURES.md)
- [ENTITY_MONSTER_RACES.md](ENTITY_MONSTER_RACES.md) 