# Notifications — KrosmozJDR

Documentation du système de notifications (toasts, centre de notifications, préférences).

## Fondamental en place (2026-02)

- **Flash Laravel → toasts** : `HandleInertiaRequests` partage `flash.success`, `flash.error`, `flash.warning`, `flash.info` ; le composable `useFlashNotifications` (dans `Main.vue`) les affiche en toasts.
- **Compteur non lus** : `auth.notifications_unread_count` partagé (Inertia) pour le badge du header.
- **API notifications** : `GET /notifications` (JSON), `POST /notifications/read-all`, `POST /notifications/{id}/read` ; `NotificationController` (liste paginée, marquer lu).
- **Centre de notifications (header)** : dropdown dans `LoggedHeaderContainer` (badge, liste, « Tout marquer comme lu », clic → visite URL + marquer lu).

## Notifications dynamiques (toasts)

Les toasts gérés par `useNotificationStore` supportent les **mises à jour en cours** (processus en arrière-plan) :

- **Création** : `addNotification({ message, type, duration: 0, ... })` — `duration: 0` garde la notification affichée jusqu’à fermeture manuelle.
- **Mise à jour** : `updateNotification(id, { message, progress, type, ... })` — met à jour le message, la barre de progression (0–100), le type, etc. L’affichage se met à jour immédiatement (réactivité Vue).
- **Progression** : si la notification a une propriété `progress` (0–100), la barre de progression du toast utilise cette valeur au lieu du compte à rebours basé sur `duration`.

**Exemple** : import en arrière-plan

```js
const id = addNotification({ message: 'Démarrage...', type: 'info', duration: 0 });
// plus tard
updateNotification(id, { message: 'Import en cours...', progress: 30 });
updateNotification(id, { message: 'Terminé', progress: 100 });
removeNotification(id); // ou laisser l’utilisateur fermer
```

L’historique temporaire (onglet « Temporaires » du centre) reçoit aussi les mises à jour de `message` pour l’entrée correspondante.

## Préférences par type (2026-02)

- **Base de données** : colonnes `users.notification_preferences` (JSON), `users.last_login_at` (timestamp nullable). Table `notification_digest_queue` pour les envois en différé. Migrations : `add_notification_preferences_to_users_table`, `add_last_login_at_to_users_table`, `create_notification_digest_queue_table`. À exécuter : `php artisan migrate`.
- **Config** : `config/notifications.php` — types (label, canaux par défaut, fréquence par défaut, rôles éligibles), canaux, fréquences (`instant`, `daily`, `weekly`, `monthly`).
- **Modèle User** : `getChannelsForNotificationType(string $type)`, `getFrequencyForNotificationType(string $type)`, `wantsNotificationForType(string $type)`. Préférence par type : `{ channels: ['database','mail'], frequency: 'instant'|'daily'|'weekly'|'monthly' }`.
- **NotificationService** : chaque envoi utilise le type (ex. `entity_modified`, `entity_created`, `profile_modified`) et n’envoie que si l’utilisateur a activé au moins un canal pour ce type.
- **NotificationService** : envoi immédiat si fréquence `instant`, sinon enregistrement en `notification_digest_queue` ; respect des canaux et du type (entity / page_section / profile / admin).
- **Paramétrage utilisateur** : page « Mon compte » → « Modifier » ; section « Notifications » avec par type : canaux (Aucune / Sur le site / Par email / Les deux) et fréquence (Au fur et à mesure / Quotidienne / Hebdomadaire / Mensuelle). Données : `notificationTypes`, `notificationChannelsLabels`, `notificationFrequencies`.
- **Mapping frontend/backend** : le formulaire envoie `notification_preferences[typeKey] = ['database'|'mail']` ; le `UserController` normalise en `{ channels: [...], frequency }` ; `initNotificationForm` (Settings, Edit) extrait les canaux depuis le format backend via `normalizePrefsChannels` / `channelsFromPref`.

## Emails de notification (canal mail)

Lorsqu’un utilisateur a activé le canal **mail** pour un type de notification, le contenu est envoyé par email via les templates personnalisés (layout commun avec VerifyEmail).

- **Mailable** : `App\Mail\NotificationMail` — sujet, salutation, lignes de contenu, bouton d’action (optionnel).
- **Templates** : `emails/notification.blade.php` (HTML), `emails/notification-text.blade.php` (texte brut, URL avec `&` pour copie depuis logs).
- **Notifications migrées** : `ProfileModifiedNotification`, `EntityModifiedNotification`, `NewUserCreatedNotification`, `UserDeletedNotification`, `LastConnectionNotification`, `DigestNotification`, `ProjectMaintenanceNotification`.
- **Pipeline** : `NotificationService` → `getChannelsForNotificationType()` / `wantsNotificationForType()` → `via()` inclut `'mail'` si activé → `toMail()` retourne `NotificationMail`.
- **Voir** : [docs/00-Project/EMAIL_SYSTEM.md](../../00-Project/EMAIL_SYSTEM.md).

## Types métier (backend)

- **Tous** : `last_connection`, `profile_modified`, `entity_modified`, `entity_deleted`, `page_section_modified`, `page_section_deleted`.
- **Admins** : `new_account_registered`, `user_deleted`, `entity_modified_admin`, `entity_deleted_admin`, `page_section_modified_admin`, `page_section_deleted_admin`, `entity_created`, `entity_restored`, `entity_force_deleted`.
- Modifications d’entité / page / section : payload avec **changements (diff)** pour l’affichage côté UI.
- Page/Section : notifiés le créateur + les utilisateurs avec droits (pivot `page_user` / `section_user`) + les admins.
- **Dernière connexion** : mise à jour à chaque login (`last_login_at`), notification optionnelle (type `last_connection`).
- **Nouveau compte** : `NotificationService::notifyNewUserCreated` (inscription) → admins.
- **Suppression utilisateur** : `NotificationService::notifyUserDeleted` (avant delete) → admins.

## Digest (quotidien, hebdo, mensuel)

- **File** : `notification_digest_queue` (user_id, notification_type, frequency, payload, created_at).
- **Job** : `App\Jobs\SendNotificationDigestsJob` (paramètre `frequency` : daily / weekly / monthly). Agrège par (user, type), envoie une `DigestNotification` puis supprime les lignes.
- **Planification** : `Kernel::schedule` — daily à 00:05, weekly lundi 00:10, monthly le 1er à 00:15.

## Fichiers

- **[ETAT_DES_LIEUX_NOTIFICATIONS.md](./ETAT_DES_LIEUX_NOTIFICATIONS.md)** — Ce qui existe (toasts, flash, notifications BDD, limites).
- **[BESOINS_ET_SPEC_NOTIFICATIONS.md](./BESOINS_ET_SPEC_NOTIFICATIONS.md)** — Deux types (A éphémères, B métier), préférences, récap.
- **[INVENTAIRE_NOTIFICATIONS_PAR_ROLE.md](./INVENTAIRE_NOTIFICATIONS_PAR_ROLE.md)** — Liste exhaustive des types de notifications par rôle, avec inscription/désinscription (in‑app, mail, les deux) et digest.
- **[OPTIONS_LARAVEL_COMMUNAUTE.md](./OPTIONS_LARAVEL_COMMUNAUTE.md)** — Ce que Laravel offre, packages utiles, recommandations.

## Liens

- UI toasts : [docs/30-UI/NOTIFICATIONS.md](../../30-UI/NOTIFICATIONS.md).
- Service backend : `App\Services\NotificationService` ; notifications : `App\Notifications\*`.
- Laravel 12 Notifications : [laravel.com/docs/12.x/notifications](https://laravel.com/docs/12.x/notifications).
