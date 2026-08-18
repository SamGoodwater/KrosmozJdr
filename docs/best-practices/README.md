# Bonnes pratiques

Conventions appliquées au projet.

## Documentation

- Décrire l'état actuel, sans historique.
- Exception : cadrage de l’IA générative métier dans `docs/IA/` (pas encore de code).
- Mettre les contenus de jeu dans `private/game/`.
- Mettre les plans, prompts et anciennes notes dans `private/archive/`.
- Ajouter un `README.md` humain et un `_ai.md` condensé à chaque nœud.

## Code

- Backend : validation via Form Requests, policies pour les droits, services pour la logique métier.
- Frontend : Composition API, composants Atomic Design, pas de classes Tailwind dynamiques.
- Sécurité : valider les entrées, ne pas versionner `.env`, garder les actions admin sous confirmation de mot de passe si sensibles.

## Tests

Adapter la couverture au risque : tests ciblés pour une modification locale, tests plus larges pour une feature partagée.
