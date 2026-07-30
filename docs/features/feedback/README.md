# Feedback

Le feedback permet aux utilisateurs d'envoyer des retours depuis le site.

## Backend

- Contrôleur : `app/Http/Controllers/FeedbackController.php`.
- Validation : `app/Http/Requests/StoreFeedbackRequest.php`.
- Email récapitulatif : `app/Mail/FeedbackRecapMail.php`.
- Conversations connectées : `app/Models/FeedbackThread.php`, `app/Models/FeedbackMessage.php`.
- Réponses utilisateur : `app/Http/Controllers/FeedbackThreadController.php`.
- Réponses staff : `app/Http/Controllers/Admin/FeedbackThreadController.php`.
- Notifications in-app : `app/Notifications/FeedbackThreadNotification.php`.

Les invités conservent le flux simple par email aux admins. Les utilisateurs connectés créent une conversation persistante visible dans `Mes retours`; seuls les admins et super admins peuvent répondre côté staff.

## Frontend

- FAB et formulaire dans `resources/js/Pages/Organismes/feedback/`.
- Mes retours : `resources/js/Pages/Pages/feedback/`.
- Inbox admin : `resources/js/Pages/Admin/feedback/`.
