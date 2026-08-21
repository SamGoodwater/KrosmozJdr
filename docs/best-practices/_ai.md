# Best practices — IA

> Conventions rapides.

## Documentation

- `README.md` humain + `_ai.md` condensé par nœud.
- Pas d'historique dans `/docs`, sauf le cadrage `docs/IA/` (LLM métier, pas encore de code).
- Jeu/règles : `private/game/`.
- Archives/plans/prompts : `private/archive/`.

## Code

- Backend Laravel classique, Form Requests, policies, services.
- Frontend Vue 3 JS, Atomic Design, Tailwind/DaisyUI.
- CVE transitives Node : `pnpm.overrides` dans `package.json` (`undici` 6.28.0).
- Dependabot : `.github/dependabot.yml` (weekly minor+patch uniquement ; toutes les majors ignorées).
