# Notifications — KrosmozJDR

Documentation du système de notifications (toasts, centre de notifications, préférences).

## Fichiers

- **[ETAT_DES_LIEUX_NOTIFICATIONS.md](./ETAT_DES_LIEUX_NOTIFICATIONS.md)** — Ce qui existe (toasts, flash, notifications BDD, limites).
- **[BESOINS_ET_SPEC_NOTIFICATIONS.md](./BESOINS_ET_SPEC_NOTIFICATIONS.md)** — Deux types (A éphémères, B métier), préférences, récap.
- **[INVENTAIRE_NOTIFICATIONS_PAR_ROLE.md](./INVENTAIRE_NOTIFICATIONS_PAR_ROLE.md)** — Liste exhaustive des types de notifications par rôle, avec inscription/désinscription (in‑app, mail, les deux) et digest.
- **[OPTIONS_LARAVEL_COMMUNAUTE.md](./OPTIONS_LARAVEL_COMMUNAUTE.md)** — Ce que Laravel offre, packages utiles, recommandations.

## Liens

- UI toasts : [docs/30-UI/NOTIFICATIONS.md](../../30-UI/NOTIFICATIONS.md).
- Service backend : `App\Services\NotificationService` ; notifications : `App\Notifications\*`.
- Laravel 12 Notifications : [laravel.com/docs/12.x/notifications](https://laravel.com/docs/12.x/notifications).
