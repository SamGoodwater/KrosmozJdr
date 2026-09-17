# État des lieux — Système de notifications KrosmozJDR

**Date** : 2026-02  
**Contexte** : Préparer l’évolution du système de notifications (historique session, centre de notifications, préférences par type).

---

## 1. Ce qui existe déjà

### 1.1 Notifications toast (frontend — éphémères)

- **Composables / store** : `useNotificationStore` (`resources/js/Composables/store/useNotificationStore.js`)  
  - Liste en mémoire (ref), types : success, error, info, warning, primary, secondary.  
  - Durée configurable (défaut 8 s), permanentes (duration = 0), placements (top-right, etc.), max 20 par placement.  
  - Aucune persistance : tout est perdu au rechargement ou changement de page (SPA : conservé tant que l’app ne se recharge pas).
- **Provider** : `useNotificationProvider` injecte le store dans l’app (utilisé dans `Main.vue`).
- **Affichage** : `NotificationContainer` (organisme) dans le layout principal (`Main.vue`), qui affiche les toasts par position.
- **Composants** : `NotificationToast`, `Toast` (atome), intégration possible via `validation` des inputs (`processValidation`, `useValidation`).
- **Utilisation** : Login, Register, EntityEditForm, Scrapping (dashboard, batch, search), CreateEntityModal, PanoplyItemsManager, EntityRelationsManager, bulk requests, etc.  
- **Documentation** : `docs/30-UI/NOTIFICATIONS.md` (guide toasts uniquement).

**Limitations** :  
- Pas d’historique de session : rechargement = perte de toutes les toasts.  
- Pas de centre de notifications (liste, “cloche”, marquage lu).  
- Pas de lien avec les messages flash Laravel (voir ci‑dessous).

---

### 1.2 Messages flash Laravel (backend → frontend)

- **Backend** : Un seul usage repéré : `session()->flash('error', '...')` dans `Admin/CharacteristicController` (caractéristique introuvable).
- **Inertia** : `HandleInertiaRequests` ne partage pas les clés `flash` (success, error, warning, etc.) avec le frontend.  
  Donc les messages flash ne sont pas automatiquement affichés en toast ni stockés côté client.

**Conclusion** : Les flash Laravel ne sont pas exploités côté UI pour l’instant (pas de partage Inertia, pas de lecture dans les pages Vue).

---

### 1.3 Notifications Laravel (base de données + mail)

- **Table** : `notifications` (migration type Laravel standard : uuid, type, notifiable_type/id, data, read_at, timestamps).
- **Modèle User** :  
  - Trait `Notifiable` (donc `$user->notifications`, `unreadNotifications`, `readNotifications`, `markAsRead()`).  
  - Colonnes `notifications_enabled` (bool), `notification_channels` (JSON, ex. `['database','email']`).  
  - Méthodes : `wantsNotification(?string $type)`, `notificationChannels()`, `wantsProfileNotification()` (toujours true).  
  - Pour l’instant, `wantsNotification()` ne tient pas compte d’un `$type` : seul le flag global est utilisé.
- **Notifications métier existantes** :  
  - `ProfileModifiedNotification` : profil modifié par un autre utilisateur (database + mail).  
  - `EntityModifiedNotification` : modification d’une entité (page, section, campagne, etc.) → notifie le créateur et les admins (database + mail selon canaux user).
- **Service** : `NotificationService` centralise l’envoi (notifyEntityModified, notifyProfileModified, computeChanges, etc.).  
  Les notifications sont mises en file (`ShouldQueue`).
- **Ressource utilisateur** : `UserLightResource` expose `notifications_enabled` et `notification_channels`, mais pas le nombre de notifications non lues ni la liste des notifications.

**Limitations** :  
- Aucune route API dédiée pour lister / marquer comme lues les notifications.  
- Pas d’UI “centre de notifications” (cloche, liste, détail).  
- Pas de préférences par type (ex. désactiver “nouvelle inscription” ou choisir “récap quotidien” au lieu d’instantané).  
- Pas de notification “nouveau compte créé” pour les admins, ni de récap quotidien des modifications.

---

### 1.4 Autres usages “notification”

- **Scrapping** : Config interne (database, mail, slack, événements process_started/completed/failed, etc.) pour le pipeline scrapping, sans lien avec le système utilisateur.
- **Email verification** : Flux Laravel standard (verification notification), sans lien avec les toasts ou le centre de notifications.

---

## 2. Synthèse

| Bloc | Présent | Manquant |
|------|--------|----------|
| Toasts (success/error, etc.) | Oui (store + container + usage dans plusieurs pages) | Historique de session, lien avec flash Laravel |
| Flash Laravel → frontend | Presque pas (1 flash côté admin, non partagé via Inertia) | Partage flash dans Inertia, affichage toast + optionnellement historique session |
| Notifications BDD (User) | Table + Notifiable + 2 types (profil, entité modifiée) | API (liste / marquer lu), centre de notifications (UI), préférences par type |
| Événements métier (admins) | — | Notification “nouveau compte”, récap quotidien (digest) |
| Désactivation / récap par type | `notifications_enabled` + canaux globaux | Désactiver un type (ex. inscriptions), choix “instantané” vs “récap” |

La suite est décrite dans [BESOINS_ET_SPEC_NOTIFICATIONS.md](./BESOINS_ET_SPEC_NOTIFICATIONS.md) et les options techniques dans [OPTIONS_LARAVEL_COMMUNAUTE.md](./OPTIONS_LARAVEL_COMMUNAUTE.md).
