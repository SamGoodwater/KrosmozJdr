# Guide de documentation

La documentation active décrit l'état actuel du projet. Elle ne raconte pas les phases, plans passés ni décisions historiques.

## Structure

Chaque nœud de `/docs` contient :

- `README.md` : version humaine, descriptive.
- `_ai.md` : version condensée pour agents IA.

Les feuilles n'ont pas de sous-dossiers. Si un sujet devient trop gros, il devient un nœud avec ses propres enfants.

## Style

- Une idée principale par section.
- Exemples courts et concrets.
- Chemins de code exacts.
- Pas de changelog, pas de TODO, pas de « pourquoi on a migré » dans `/docs`.

## Contenus séparés

- `private/game/` : règles, lore, ressources de jeu.
- `private/archive/` : historique, plans, prompts, backups.
