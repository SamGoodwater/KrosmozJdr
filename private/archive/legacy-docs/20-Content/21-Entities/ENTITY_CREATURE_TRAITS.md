# CreatureTraits (`creature_traits`)

## Rôle et description
Les `CreatureTrait` représentent les traits permanents d’une créature. Contrairement aux conditions, ils font partie de l’identité de la créature, d’une classe ou d’une spécialisation et ne sont pas dissipables.

## Relations principales
- **Créatures** : via `creature_creature_trait`.
- **Classes** : via `breed_creature_trait`.
- **Spécialisations** : via `creature_trait_specialization`.

## Seed de base
Le référentiel contient notamment Malade, Lourd, Petite taille, Grande taille, Gigantesque, Insensible aux poisons, Métaboliseur rapide, Vif / Vive et Agile.

## Liens utiles
- [ENTITY_CREATURES.md](ENTITY_CREATURES.md)
- [ENTITY_CLASSES.md](ENTITY_CLASSES.md)
