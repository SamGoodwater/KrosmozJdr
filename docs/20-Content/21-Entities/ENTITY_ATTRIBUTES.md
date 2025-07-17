# Attributs (`attributes`)

## Rôle et description
Les attributs représentent les caractéristiques fondamentales des créatures et classes (force, intelligence, agilité, chance, etc.). Ils influencent les statistiques, les jets de dés, les compétences et la progression des personnages.

## Relations principales
- **Créatures** : via le pivot `attribute_creature` (N:N avec `creatures`).
- **Classes** : via le pivot `attribute_class` (N:N avec `classes`).

## Exemples d’utilisation
- Définition des points de force, d’intelligence, etc. d’un personnage.
- Calcul des bonus/malus lors d’une action ou d’un combat.

## Liens utiles
- [ENTITY_CREATURES.md](ENTITY_CREATURES.md)
- [ENTITY_CLASSES.md](ENTITY_CLASSES.md) 