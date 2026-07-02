# Notifications

Le système de notifications combine notifications Laravel, toasts front et emails/digests.

## Backend

- Catalogue : `app/Support/Notifications/NotificationCatalog.php`.
- Service : `app/Services/Notifications/` et notifications dans `app/Notifications/`.
- Jobs/digests : `notification_digest_queue`, `SendNotificationDigestsJob`.

## Frontend

- Toasts et historique via `resources/js/Composables/notifications/`.
- Page : `resources/js/Pages/Pages/notifications/Index.vue`.
