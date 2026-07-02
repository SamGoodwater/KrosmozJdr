# Données personnelles et RGPD — KrosmozJDR

**Date** : 2026-03-07

Ce document décrit les fonctionnalités RGPD mises en place pour permettre aux utilisateurs d'exercer leurs droits d'accès et d'effacement.

## Droits implémentés

### 1. Droit d'accès (export des données)

- **Page** : `/user/privacy` (Paramètres → Données personnelles)
- **Action** : Demande d'export de l'ensemble des données personnelles
- **Protection** : Modale de confirmation par mot de passe (`ConfirmPasswordModal`), rate limit (10 requêtes / 15 min)
- **Format** : Archive ZIP contenant :
  - `account.json` : profil, préférences, dates
  - `relations/*.json` : pages, sections, scénarios, campagnes
  - `notifications.json`
  - `media-index.json`
  - `metadata.json`
- **Livraison** : URL signée temporaire (24 h), stockage privé
- **Expiration** : Les exports expirent après 24 h (défaut)

### 2. Droit à l'effacement (suppression du compte)

- **Page** : `/user/privacy` (section Supprimer mon compte)
- **Protection** : Modale de confirmation par mot de passe (`ConfirmPasswordModal`)
- **Garde-fous** :
  - Impossible de supprimer un compte système (`is_system`)
  - Impossible de supprimer le dernier super administrateur
- **Processus** :
  1. L'utilisateur confirme avec son mot de passe
  2. Création d'une demande RGPD avec délai de rétractation (défaut 7 jours, config `PRIVACY_ERASURE_WITHDRAWAL_DAYS`)
  3. Déconnexion immédiate + message expliquant la possibilité de récupérer le compte
  4. **Récupération** : Si l'utilisateur se reconnecte avant la fin du délai, une bannière propose « Récupérer mon compte » (`POST /user/privacy/delete/cancel`)
  5. Après le délai : la commande `privacy:process-deletion-requests` (planifiée quotidiennement) envoie le job d'anonymisation
  6. Anonymisation + soft delete du compte
  7. Un **super administrateur** peut supprimer définitivement ou **restaurer** un compte soft-deleted depuis l'interface admin utilisateurs

### 3. Anonymisation et purge

Le service `UserErasureService` :

- Dissocie `created_by` sur le contenu collaboratif (campagnes, scénarios, pages, etc.) pour conserver le contenu
- Supprime les pivots (accès personnalisés : `campaign_user`, `scenario_user`, etc.)
- Supprime sessions, notifications, exports RGPD, filter presets
- Supprime les médias (avatar)
- Anonymise le profil : nom → "Utilisateur supprimé", email → pseudonyme, puis soft delete

## Audit

Les actions sensibles sont enregistrées dans `privacy_audit_logs` :

- `export_requested`, `export_downloaded`
- `erasure_requested`, `erasure_executed`

## Planification et exécution des jobs

- **Export** : Job `GenerateUserDataExportJob` (queue par défaut)
  - **Avec worker** : `php artisan queue:work` doit tourner pour traiter les exports ; sinon l'export reste en « pending » et affiche « Indisponible » sans date d'expiration.
  - **Sans worker** : Mettre `PRIVACY_EXPORT_SYNC=true` dans `.env` pour exécuter l'export immédiatement dans la requête HTTP. L'export est alors disponible dès le retour sur la page (utile en dev ou petits déploiements).
- **Purge exports expirés** : Job `PurgeExpiredPrivacyExportsJob` (à planifier si besoin)
- **Suppression du compte** : Délai de rétractation (défaut 7 jours), puis `privacy:process-deletion-requests` (planifié à 02:00) envoie `ExecuteUserErasureJob`

## Protection par modale (ConfirmPasswordModal)

Les actions sensibles (export, téléchargement, suppression) sont protégées par une **modale de confirmation par mot de passe** réutilisable :

- **Composant** : `ConfirmPasswordModal` (`@/Pages/Molecules/action/ConfirmPasswordModal.vue`)
- **Endpoint** : `POST /user/password/confirm` — valide le mot de passe, met à jour la session, retourne JSON
- **Usage** : Ouvrir la modale avant l'action, sur confirmation exécuter l'action protégée (le middleware `password.confirm` accepte alors la requête)
- **Réutilisable** : Peut être utilisé ailleurs (ex. actions admin, fonctionnalités sensibles)

### Délai d'inactivité (2026-03)

Le middleware `RequirePasswordWithInactivity` remplace le comportement par défaut de Laravel :

- **Une confirmation** débloque l'accès aux fonctions sensibles
- **Délai d'inactivité** : la sécurité se réactive si l'utilisateur n'accède à aucune fonction protégée pendant plus d'1 heure (config : `auth.password_inactivity_timeout`, défaut 3600 s)
- **Props Inertia** : `auth.password_recently_confirmed` permet au frontend (ex. page Scrapping) de savoir si la confirmation est encore valide et d'éviter de redemander à chaque chargement de page

## Routes

| Méthode | Route | Description |
|---------|-------|-------------|
| GET | `/user/privacy` | Page Mes données |
| POST | `/user/password/confirm` | Confirmation mot de passe (API modal) |
| POST | `/user/privacy/export` | Demande d'export |
| GET | `/user/privacy/exports/{id}` | Téléchargement (URL signée) |
| POST | `/user/privacy/delete/request` | Demande de suppression du compte |
| POST | `/user/privacy/delete/cancel` | Annuler la demande (récupérer le compte) |

## Références

- [SECURITY_PRACTICES.md](../../10-BestPractices/SECURITY_PRACTICES.md)
- [politique-donnees.md](/storage/app/public/legal/politique-donnees.md)
