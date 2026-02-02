# Sorts (`spells`)

## Rôle et description
Les sorts représentent toutes les magies, attaques spéciales, pouvoirs surnaturels utilisables par les créatures (joueurs, monstres, NPC). Ils définissent les capacités offensives, défensives ou utilitaires du jeu.

## Relations principales
- **Créatures** : via le pivot `creature_spell` (N:N avec `creatures`).
- **Classes** : via le pivot `breed_spell` (N:N avec `breeds`).
- **Types de sort** : via le pivot `spell_type` (N:N avec `spell_types`).
- **Invocations** : via le pivot `spell_invocation` (N:N avec `monsters`).
- **Scénarios, campagnes** : via les pivots `scenario_spell`, `campaign_spell`.

## Exemples d’utilisation
- Attribution d’un sort à un personnage ou un monstre.
- Création d’un sort de soin, d’attaque ou d’invocation.

## Typage
Chaque sort peut être associé à un ou plusieurs types (`spell_types`) pour catégoriser ses effets (attaque, soin, invocation, utilitaire, etc.).
Exemples de types : attaque, soin, invocation, buff, debuff, utilitaire, etc.

## Liens utiles
- [ENTITY_CREATURES.md](ENTITY_CREATURES.md)
- [ENTITY_CLASSES.md](ENTITY_CLASSES.md)
- [ENTITY_SPELL_TYPES.md](ENTITY_SPELL_TYPES.md)
- [ENTITY_MONSTERS.md](ENTITY_MONSTERS.md) 