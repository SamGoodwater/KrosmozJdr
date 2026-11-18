# Best practices — IA

> Conventions rapides.

## Documentation

- `README.md` humain + `_ai.md` condensé par nœud.
- Pas d'historique dans `/docs`, sauf le cadrage `docs/IA/` (LLM métier ; pipeline Anthropic + config JSON).
- Jeu/règles : `private/game/`.
- Archives/plans/prompts : `private/archive/`.

## Code

- Backend Laravel classique, Form Requests, policies, services.
- Frontend Vue 3 JS, Atomic Design, Tailwind/DaisyUI.
- Lint JS/Vue : ESLint 10 + `eslint-plugin-vue` 10, config plate `eslint.config.js`, script `pnpm lint` (sous-ensemble de fichiers).
- Tests PHP : PHPUnit 13 (`php artisan test`).
- CVE transitives Node : `pnpm.overrides` dans `package.json` (`qs` 6.16.0, `brace-expansion` 1.1.18 / 2.1.4 / 5.0.12, `glob` avant 14.0.2 forcé en 14.0.6, `minimatch` avant 11.0.1 forcé en 11.0.9, `postcss-selector-parser` 7.1.6).
- Dependabot : `.github/dependabot.yml` (weekly minor+patch uniquement ; toutes les majors ignorées).
- Git : `main` seule branche durable ; branche courte par sujet → commit → merge → suppression. PR optionnelle en solo. Rule : `.cursor/rules/git-github.mdc`.
