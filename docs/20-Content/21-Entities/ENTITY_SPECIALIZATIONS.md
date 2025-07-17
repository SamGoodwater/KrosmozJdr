# Spécialisations (`specializations`)

## Rôle et description
Les spécialisations représentent les sous-classes ou orientations spécifiques d’une classe (ex : tank, soigneur, dps…). Elles permettent de personnaliser davantage les personnages et d’enrichir la diversité des rôles en jeu.

## Relations principales
- **Classe** : chaque spécialisation est liée à une classe (`class_id`).
- **NPC** : chaque NPC peut référencer une spécialisation (`specialization_id`).
- **Capacités** : via le pivot `capability_specialization` (N:N avec `capabilities`).

## Exemples d’utilisation
- Création d’un personnage avec une spécialisation tank ou soigneur.
- Attribution de capacités spécifiques à une spécialisation.

## Liens utiles
- [ENTITY_CLASSES.md](ENTITY_CLASSES.md)
- [ENTITY_CAPABILITIES.md](ENTITY_CAPABILITIES.md)
- [ENTITY_NPCS.md](ENTITY_NPCS.md) 