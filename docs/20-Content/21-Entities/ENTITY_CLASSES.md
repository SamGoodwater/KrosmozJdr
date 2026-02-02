# Classes (`breeds`)

## Rôle et description
Les classes (entité technique `breed` / `breeds`) représentent les archétypes jouables du jeu (ex : Féca, Iop, Eniripsa…). Elles définissent les capacités de base, la progression, les spécialisations et les spécificités de chaque personnage joueur ou PNJ associé à une classe.

## Relations principales
- **NPC** : chaque NPC peut référencer une classe (`breed_id`, relation `breed`).
- **Spécialisations** : chaque classe peut avoir plusieurs spécialisations (`specializations`).
- **Créatures** : certains personnages joueurs ou PNJ sont associés à une classe.

## Exemples d’utilisation
- Création d’un personnage joueur.
- Attribution d’une classe à un PNJ.

## Liens utiles
- [ENTITY_NPCS.md](ENTITY_NPCS.md)
- [ENTITY_SPECIALIZATIONS.md](ENTITY_SPECIALIZATIONS.md) 