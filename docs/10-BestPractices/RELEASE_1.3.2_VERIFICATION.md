# Release 1.3.2 — Vérification transverse

## Transverse livré

### Feedback — email récap (connectés)

- Case **« Recevoir un email récapitulatif »** dans `FeedbackFab.vue` (défaut décoché).
- Backend : `email_recap` validé ; envoi `FeedbackRecapMail` **uniquement** si utilisateur authentifié et case cochée (email du compte, jamais depuis le formulaire invité).
- Tests : `tests/Feature/Web/FeedbackControllerTest.php`.

## Contrôles exécutés (2026-05-20)

| Contrôle | Résultat |
|----------|----------|
| `composer audit` (via `project:review --security`) | OK |
| `docs/docs.index.json` + guides | OK (entrées recette 1.3.2 + calibration 282) |
| PHPStan (`phpstan.neon`) | OK |
| ESLint (`pnpm run lint`) | OK |
| `characteristics:definitions-progress` | 282/282 (100 %) |
| `CharacteristicGetterServiceTest` (restrictions équipement) | OK |
| `project:init:verify` + option `--verify` sur `project:init` | Livré |
| Vitest `test:a11y` (Alert) | Livré |
| Playwright smoke `tests/e2e/release-1.3.2.spec.ts` | Livré (exécution locale avec serveur) |

## À lancer avant merge prod

```bash
php artisan project:review --all --pint-timeout=900
# ou au minimum :
php artisan project:review --test-back --test-front --phpstan --eslint --security --docs
php artisan project:init:verify
pnpm run test:run
pnpm run test:a11y
```

Le rapport `project:review` est écrit sous `storage/app/dev-reports/review-*.md`.

Recette manuelle : [MANUAL_RECIPE_RELEASE_1.3.2.md](./MANUAL_RECIPE_RELEASE_1.3.2.md).

## Points de vigilance (revue code)

- **Feedback** : throttle IP (`config/feedback.php`) ; pas d’email récap pour les invités même si `email_recap=1`.
- **Recherche globale** : Gate `view` sur chaque hit ; `meta.hasMore` pour pagination.
- **Notifications** : pause survol/focus (éviter `focusout` interne au toast — `relatedTarget` vérifié).
- **Commandes** : `project:seed` / `project:refresh --fast` sans DofusDB ; `project:cron --update` délégué à `project:data:sync`.
