# Conditions (`conditions`)

## Rôle et description
Les conditions représentent les états temporaires appliqués à une créature par un sort, une capacité ou un effet environnemental. Elles peuvent être dissipables et portent les flags utiles importés depuis DofusDB quand elles viennent du scrapping de sorts.

Les caractéristiques fondamentales restent modélisées par les systèmes de caractéristiques. Les traits permanents sont modélisés par `CreatureTrait`.

## Relations principales
- **Sorts** : via le pivot `condition_spell`, avec le mode d’application, la durée, la dissipabilité et les métadonnées DofusDB.
- **Créatures** : via le pivot `condition_creature` lorsqu’une condition est attachée directement à une créature.

## Liens utiles
- [ENTITY_CREATURES.md](ENTITY_CREATURES.md)
- [ENTITY_SPELLS.md](ENTITY_SPELLS.md)
