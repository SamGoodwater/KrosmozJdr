# Inventaire des notifications par rôle — KrosmozJDR

**Date** : 2026-02  
**Objectif** : Liste exhaustive des types de notifications envisagées, par rôle, avec possibilité d’inscription/désinscription par canal (in‑app, mail, ou les deux). À utiliser avant la mise en place des queues et des préférences (middleware, modèle, etc.).

**Référence** : [ROLES_AND_RIGHTS.md](../../20-Content/ROLES_AND_RIGHTS.md) (guest, user, player, game_master, admin, super_admin).

---

## 1. Légende

- **Rôles** : `guest` | `user` | `player` | `game_master` | `admin` | `super_admin`
- **Canal** :
  - **In‑app (pop)** : notification stockée en BDD et affichée dans le centre de notifications du site (toast optionnel).
  - **Mail** : email envoyé.
- **Inscription** : pour chaque type, l’utilisateur éligible peut choisir **aucun**, **in‑app uniquement**, **mail uniquement**, ou **in‑app + mail** (sauf cas forcés, ex. modification de son propre profil).
- **Digest** : option possible pour recevoir un **récap** (quotidien/hebdo) au lieu d’une notification à chaque événement (ex. récap des nouvelles inscriptions pour les admins).

---

## 2. Compte et authentification

| Type (clé) | Description | Rôles destinataires | Canaux par défaut | Inscription | Digest possible |
|------------|-------------|---------------------|-------------------|-------------|------------------|
| `profile_modified` | Mon profil a été modifié (par moi ou un admin) | Utilisateur concerné | in‑app + mail | Oui (mail peut être désactivé pour « modif par soi ») | Non |
| `password_reset_requested` | Demande de réinitialisation de mot de passe | Utilisateur concerné | mail (souvent forcé) | — | Non |
| `email_verified` | Adresse email vérifiée | Utilisateur concerné | in‑app (+ mail optionnel) | Oui | Non |
| `new_account_registered` | Nouveau compte créé (inscription) | admin, super_admin | in‑app + mail | Oui (in‑app, mail, les deux, ou aucun) | Oui (récap quotidien) |
| `login_new_device` *(optionnel)* | Connexion depuis un nouvel appareil / IP | Utilisateur concerné | mail (sécurité) | Oui (désactiver possible) | Non |

---

## 3. Entités (création, modification, suppression)

*Entités concernées : Page, Section, Campaign, Scenario, Resource, Item, Monster, Spell, Breed, etc. (tout ce qui a `created_by`).*

| Type (clé) | Description | Rôles destinataires | Canaux par défaut | Inscription | Digest possible |
|------------|-------------|---------------------|-------------------|-------------|------------------|
| `entity_created` | Une entité a été créée | admin, super_admin | in‑app + mail | Oui | Oui (récap quotidien) |
| `entity_modified` | Une entité que j’ai créée a été modifiée par quelqu’un d’autre | Créateur de l’entité | in‑app + mail | Oui | Oui (récap par type d’entité) |
| `entity_modified_admin` | Une entité a été modifiée (vue admin) | admin, super_admin | in‑app + mail | Oui | Oui (récap quotidien) |
| `entity_deleted` | Une entité que j’ai créée a été supprimée (soft) | Créateur | in‑app + mail | Oui | Non |
| `entity_deleted_admin` | Une entité a été supprimée (soft) | admin, super_admin | in‑app + mail | Oui | Oui |
| `entity_restored` | Une entité a été restaurée | Créateur + admin, super_admin | in‑app + mail | Oui | Non |
| `entity_force_deleted` | Une entité a été supprimée définitivement | Créateur + admin, super_admin | in‑app + mail | Oui | Non |
| `entity_state_changed` *(optionnel)* | Changement d’état (ex. brouillon → publié) | Créateur + admin selon état | in‑app (+ mail) | Oui | Non |

*Note : aujourd’hui le service utilise une seule classe générique `EntityModifiedNotification` avec des variantes de message (création, modif, suppression, etc.). On peut garder une seule notification et distinguer par `type` ou `action` en BDD pour les préférences.*

---

## 4. Campagnes, scénarios, pages (collaboration)

| Type (clé) | Description | Rôles destinataires | Canaux par défaut | Inscription | Digest possible |
|------------|-------------|---------------------|-------------------|-------------|------------------|
| `campaign_invitation` | Invitation à rejoindre une campagne | user, player, game_master | in‑app + mail | Oui | Non |
| `scenario_invitation` | Invitation à rejoindre un scénario | user, player, game_master | in‑app + mail | Oui | Non |
| `campaign_role_changed` | Mon rôle dans une campagne a changé | Membre de la campagne | in‑app + mail | Oui | Non |
| `scenario_role_changed` | Mon rôle dans un scénario a changé | Membre du scénario | in‑app + mail | Oui | Non |
| `campaign_updated` | Une campagne à laquelle je participe a été modifiée | game_master, player (membres) | in‑app (+ mail) | Oui | Oui (récap par campagne) |
| `scenario_updated` | Un scénario auquel je participe a été modifié | game_master, player (membres) | in‑app (+ mail) | Oui | Oui |
| `page_updated` | Une page d’une campagne/scénario auquel je participe a été modifiée | game_master, player (membres) | in‑app | Oui | Oui |
| `section_updated` | Une section d’une page à laquelle j’ai accès a été modifiée | game_master, player (membres) | in‑app | Oui | Oui |
| `player_joined_campaign` | Un joueur a rejoint ma campagne | game_master (propriétaire/créateur) | in‑app + mail | Oui | Oui (récap) |
| `player_joined_scenario` | Un joueur a rejoint mon scénario | game_master | in‑app + mail | Oui | Oui |
| `request_to_join_campaign` *(optionnel)* | Demande d’un utilisateur pour rejoindre ma campagne | game_master | in‑app + mail | Oui | Non |
| `request_to_join_scenario` *(optionnel)* | Demande pour rejoindre mon scénario | game_master | in‑app + mail | Oui | Non |

---

## 5. Administration et modération

| Type (clé) | Description | Rôles destinataires | Canaux par défaut | Inscription | Digest possible |
|------------|-------------|---------------------|-------------------|-------------|------------------|
| `admin_digest_daily` | Récap quotidien admin (inscriptions, entités créées/modifiées/supprimées) | admin, super_admin | mail (ou in‑app) | Oui (choix récap vs instantané par sous-type) | — (c’est le digest) |
| `project_maintenance` | Résultat project:init ou project:update (succès, durée, heure) | admin, super_admin | in‑app | Oui | Non |
| `user_reported` *(optionnel)* | Un utilisateur ou un contenu a été signalé | admin, super_admin | in‑app + mail | Oui | Oui |
| `role_changed` | Mon rôle a été modifié par un admin | Utilisateur concerné | in‑app + mail | Forcé (toujours notifier) | Non |

---

## 6. Système et annonces

| Type (clé) | Description | Rôles destinataires | Canaux par défaut | Inscription | Digest possible |
|------------|-------------|---------------------|-------------------|-------------|------------------|
| `maintenance_scheduled` | Maintenance planifiée | user, player, game_master, admin, super_admin | in‑app + mail | Oui (mail désactivable) | Non |
| `announcement` | Annonce générale (nouvelle fonctionnalité, info) | Tous les rôles connectés | in‑app (+ mail si prioritaire) | Oui | Non |

---

## 7. Synthèse par rôle

| Rôle | Types de notifications concernés (liste non exhaustive) |
|------|----------------------------------------------------------|
| **guest** | Aucune (non connecté). |
| **user** | `profile_modified`, `email_verified`, `password_reset_requested`, `login_new_device`, `campaign_invitation`, `scenario_invitation`, `campaign_role_changed`, `scenario_role_changed`, `role_changed`, `maintenance_scheduled`, `announcement`. |
| **player** | Idem user + `campaign_updated`, `scenario_updated`, `page_updated`, `section_updated` (pour campagnes/scénarios auxquels il participe). |
| **game_master** | Idem player + `player_joined_campaign`, `player_joined_scenario`, `request_to_join_*`, et en tant que créateur : `entity_*` (modifié, supprimé, restauré). |
| **admin** | Tous les types ci‑dessus selon contexte + `new_account_registered`, `entity_created`, `entity_modified_admin`, `entity_deleted_admin`, `entity_restored`, `entity_force_deleted`, `admin_digest_daily`, `project_maintenance`, `user_reported`. |
| **super_admin** | Idem admin (même liste, pas de distinction fonctionnelle pour les notifs). |

---

## 8. Options d’inscription par type (récap)

Pour **chaque type** où l’utilisateur est éligible, il doit pouvoir choisir :

1. **Désinscription** : ne recevoir aucune notification pour ce type.
2. **In‑app uniquement** : notifications dans le centre du site (BDD), pas d’email.
3. **Mail uniquement** : email uniquement, pas de notification in‑app (utile pour certains admins).
4. **In‑app + mail** : les deux.

Pour certains types (ex. `profile_modified` quand c’est un admin qui modifie le profil), on peut imposer au moins in‑app (ou mail) pour des raisons de sécurité / traçabilité.

**Digest** : pour les types marqués « digest possible », proposer en plus le choix « instantané » vs « récap quotidien » (ou hebdo) pour limiter le volume (ex. nouvelles inscriptions pour les admins).

---

## 9. Suite (implémentation)

- Définir la structure des **préférences** en BDD (table dédiée ou JSON sur `users`, ex. `notification_preferences`).
- Mapper chaque **type** ci‑dessus à une (ou plusieurs) classes de notification Laravel et à la logique d’envoi (NotificationService, events, observers).
- Mettre en place les **queues** pour les envois massifs (voir [Laravel Notifications – Queueing](https://laravel.com/docs/12.x/notifications#queueing-notifications)).
- Exposer une **API** (liste, marquer lu) et une **UI** (centre de notifications + page paramètres « Notifications ») avec cases par type et par canal (in‑app, mail, digest si applicable).

Ce document peut servir de référence pour la spec détaillée et les migrations (préférences, index sur `type` dans `notifications`).
