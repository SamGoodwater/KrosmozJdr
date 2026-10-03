# Notifications — IA

> Notifications DB/email et toasts front.

## Fichiers pivots

- `app/Support/Notifications/NotificationCatalog.php`
- `app/Notifications/` (`ScrappingJobProgressNotification`, `ProjectConsoleJobProgressNotification` : synchrone, visibles tout de suite)
- File `notifications` (`QueuesOnNotifications`) : maintenance, connexion, entité modifiée, etc. Invisibles dans le centre tant que le worker n’a pas écrit la table `notifications`. Un worker ponctuel part à la fin de la requête.
- `resources/js/Composables/notifications/`
- `resources/js/Composables/admin/useProjectConsoleJob.js`
- `resources/js/Pages/Pages/notifications/Index.vue`
