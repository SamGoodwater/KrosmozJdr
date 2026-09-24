## Scan secrets (push / CI)

Empêche de pousser des credentials vers GitHub.

### Local (chaque clone)

```bash
git config core.hooksPath .githooks
pnpm run secrets:scan          # arbre tracké
```

Le hook `.githooks/pre-push` appelle `node scripts/scan-secrets.mjs --mode=pre-push` et **refuse le push** si un secret est trouvé dans les commits à envoyer.

Optionnel : installer [gitleaks](https://github.com/gitleaks/gitleaks) pour un scan plus complet (config `.gitleaks.toml`) ; sinon le fallback Node couvre DSN MySQL, clés PEM, Anthropic, APP_KEY, AWS, GitHub PAT, et les chemins `.env` / `.cursor/mcp.json`.

### CI GitHub

Workflow [`.github/workflows/secret-scan.yml`](../../.github/workflows/secret-scan.yml) : `gitleaks/gitleaks-action` sur `push` et `pull_request` vers `main` / `master` / `develop`.

### SEC-01 (historique)

Un mot de passe MySQL a déjà figuré dans l’historique (`.cursor/mcp.json`). Le scan **ne réécrit pas** l’historique : rotation manuelle du mot de passe + éventuelle purge d’historique restent nécessaires.
