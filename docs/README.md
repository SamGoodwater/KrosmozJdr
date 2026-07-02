# Documentation KrosmozJDR

Documentation technique active du projet. Elle décrit l'état actuel du site et sert à la fois aux humains et aux agents IA.

## Lire efficacement

- [Carte IA racine](_ai.md) : point d'entrée condensé pour choisir le bon domaine.
- Chaque dossier contient un `README.md` humain et un `_ai.md` condensé.
- Les contenus de jeu (règles, lore, ressources MJ) sont dans `private/game/`, pas dans cette documentation technique.
- Les anciennes notes, plans, prompts et backups sont dans `private/archive/`.

## Domaines

| Domaine | Rôle |
| --- | --- |
| [project](project/README.md) | Vue d'ensemble, stack, conventions globales. |
| [backend](backend/README.md) | Laravel, modèles, routes, base de données, services. |
| [frontend](frontend/README.md) | Vue 3, Inertia, Atomic Design, vues d'entités. |
| [features](features/README.md) | Fonctionnalités transverses : entités, CMS, scrapping, droits, effets. |
| [operations](operations/README.md) | Commandes projet, import de règles, maintenance. |
| [best-practices](best-practices/README.md) | Documentation, sécurité, tests, nommage. |

## Règles de rédaction

- Décrire ce qui existe maintenant, sans historique d'évolution.
- Donner les chemins de code pivots et 1 ou 2 exemples courts si utiles.
- Supprimer les doublons plutôt que les déplacer dans une autre page active.
- Archiver ce qui explique une ancienne décision dans `private/archive/`, pas dans `/docs`.
