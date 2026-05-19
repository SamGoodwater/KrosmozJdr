# Release 1.3.2 — Vérification transverse

## Transverse livré

### Feedback — email récap (connectés)

- Case **« Recevoir un email récapitulatif »** dans `FeedbackFab.vue` (défaut décoché).
- Backend : `email_recap` validé ; envoi `FeedbackRecapMail` **uniquement** si utilisateur authentifié et case cochée (email du compte, jamais depuis le formulaire invité).
- Tests : `tests/Feature/Web/FeedbackControllerTest.php`.

## Contrôles exécutés (2026-05-19)

| Contrôle | Résultat |
|----------|----------|
| `composer audit` (via `project:review --security`) | OK |
| `docs/docs.index.json` + guides | OK |
| PHPStan (`phpstan.neon`) | OK |
| ESLint (`pnpm run lint`) | OK |
| Tests Feature ciblés release (28 tests) | OK |
| Vitest unit (search, notifications, routes) | OK |

Tests Feature regroupés : feedback, global-search, project:cron/seed, admin dashboard/recap, CMS write levels.

## À lancer avant merge prod

```bash
php artisan project:review --all
# ou au minimum :
php artisan test
pnpm run test:run
```

Le rapport `project:review` est écrit sous `storage/app/dev-reports/review-*.md`.

## Points de vigilance (revue code)

- **Feedback** : throttle IP (`config/feedback.php`) ; pas d’email récap pour les invités même si `email_recap=1`.
- **Recherche globale** : Gate `view` sur chaque hit ; `meta.hasMore` pour pagination.
- **Notifications** : pause survol/focus (éviter `focusout` interne au toast — `relatedTarget` vérifié).
- **Commandes** : `project:seed` / `project:refresh --fast` sans DofusDB ; `project:cron --update` délégué à `project:data:sync`.
