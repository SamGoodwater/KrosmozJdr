# Options Laravel et communauté — Notifications KrosmozJDR

**Date** : 2026-02  
**Référence** : [BESOINS_ET_SPEC_NOTIFICATIONS.md](./BESOINS_ET_SPEC_NOTIFICATIONS.md).

---

## 1. Ce que Laravel fournit en natif

- **Notifications** : Système complet (database, mail, broadcast, custom), file d’attente, trait `Notifiable`, table `notifications`, `unreadNotifications`, `markAsRead()`. Déjà utilisé dans le projet.
- **Session / Flash** : `session()->flash('key', 'value')` ; une seule lecture puis consommation. Pas de “liste d’historique” côté Laravel.
- **Pas de** : préférences par type, digest / regroupement, ni UI de centre de notifications. À construire ou compléter par un package.

---

## 2. Historique de session (Type A — éphémères)

- **Côté frontend** : Le store actuel (`useNotificationStore`) garde déjà les toasts en mémoire. Pour un “historique de session” :
  - **Option 1** : Étendre le store pour conserver toutes les notifications dans un tableau “historique” (en plus de l’affichage toast), avec option de persistance `sessionStorage` (survie au reload).
  - **Option 2** : Ne pas persister ; historique uniquement pour la session SPA en cours (plus simple).
- **Flash Laravel → Inertia** : Dans `HandleInertiaRequests::share()`, ajouter par exemple :
  - `'flash' => ['success' => session('success'), 'error' => session('error'), 'warning' => session('warning')]`
  - Côté Vue (layout ou composable global), au `page.props.flash`, appeler le notificationStore et ajouter à l’historique. Aucun package obligatoire.

**Recommandation** : Implémentation maison légère (partage flash + extension du store + optionnel sessionStorage).

---

## 3. Centre de notifications et API (Type B)

- **Laravel** : Pas de contrôleur “notification center” ni de routes prédéfinies. À ajouter :
  - Routes (ex. `GET /notifications`, `POST /notifications/{id}/read`, `POST /notifications/read-all`).
  - Contrôleur qui s’appuie sur `$request->user()->unreadNotifications` / `notifications()`, et `markAsRead()`.
- **Frontend** : Composant “centre de notifications” (liste + détail + marquer lu), éventuellement icône cloche avec badge (compteur non lus). Le compteur peut être fourni par une prop partagée Inertia (ex. `auth.notifications_unread_count`) ou une requête dédiée.

**Recommandation** : Implémentation maison (routes + contrôleur + ressource API), en restant sur le modèle Laravel standard. Pas de package nécessaire pour ce socle.

---

## 4. Préférences par type et digest (désactivation, récap)

- **Laravel** : Aucun système de préférences par type ni de digest intégré.
- **Packages communauté** :
  - **offload-project/laravel-notification-preferences** (Laravel 11+, PHP 8.3+) : préférences par type et par canal, groupes, désactivation, opt-in/opt-out. Pas de digest natif mais bonne base pour “désactiver un type” ou “choisir canal”. [Packagist](https://packagist.org/packages/offload-project/laravel-notification-preferences) / [GitHub](https://github.com/offload-project/laravel-notification-preferences).
  - **Spatie Laravel Notification Log** et autres : plutôt log d’envoi, pas préférences utilisateur.
  - **Digest / récap** : Souvent implémenté en custom : job planifié (daily) qui agrège les événements “non encore envoyés en digest” et envoie une seule notification “récap du jour”. Les préférences (qui veut le digest, pour quels types) peuvent être stockées en BDD (colonne JSON ou table dédiée).

**Recommandation** :
- **Court terme** : Étendre le modèle User (ex. colonne JSON `notification_preferences`) pour stocker par type : `{ "new_account": { "enabled": true, "channel": "database", "digest": "daily" }, ... }`. Dans `NotificationService` (ou avant l’envoi), vérifier ces préférences et soit envoyer, soit mettre en file pour le digest. Pas de package obligatoire pour une première version.
- **Plus tard** : Si les besoins se complexifient (nombreux types, groupes, UI riche), évaluer **offload-project/laravel-notification-preferences** pour la partie préférences.

---

## 5. Synthèse et ordre de mise en œuvre suggéré

| Besoin | Approche recommandée |
|--------|----------------------|
| Historique session (toasts) | Étendre `useNotificationStore` (liste historique + option sessionStorage) ; pas de package. |
| Flash Laravel → toast | Partager `flash` dans `HandleInertiaRequests` ; dans le frontend, lire `page.props.flash` et appeler le store. |
| Centre de notifications | Routes + contrôleur Laravel (liste, marquer lu) ; composant Vue (liste + cloche + badge). |
| Nouveau compte → admins | Nouvelle notification Laravel (ex. `NewAccountRegisteredNotification`) envoyée aux admins via `NotificationService`. |
| Désactiver / récap par type | Colonne (ou table) de préférences ; logique dans le service d’envoi + job quotidien pour digest. Optionnel : package offload si la logique devient lourde. |

En résumé : **Laravel natif suffit pour le socle** (centre de notifications, API, flash, nouvelles notifications). L’historique de session et les préférences peuvent être réalisés en custom ; un package comme **laravel-notification-preferences** devient pertinent si vous voulez une gestion avancée des préférences sans tout coder vous‑même.
