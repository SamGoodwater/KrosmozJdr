# Besoins et spécification — Notifications KrosmozJDR

**Date** : 2026-02  
**Référence** : [ETAT_DES_LIEUX_NOTIFICATIONS.md](./ETAT_DES_LIEUX_NOTIFICATIONS.md).

---

## 1. Deux familles de notifications

### 1.1 Type A — Notifications éphémères (feedback de navigation)

**Objectif** : Afficher le retour d’action immédiat (succès, erreur, info) lors de la navigation (connexion, mise à jour d’une table, formulaire, etc.). Pas besoin de les garder en base.

**Souhait** : Pouvoir consulter l’**historique de la session** (depuis la connexion ou le chargement de la page) :  
- soit en mémoire (déjà partiellement le cas avec le store actuel),  
- soit via un mécanisme léger (ex. session storage / cookie) pour survivre à un rechargement de page si besoin.

**Exemples** :  
- Connexion réussie / échec.  
- “Table mise à jour”, “Entité créée”, “Erreur de validation”.  
- Messages flash Laravel (success, error) après une redirection.

**Spécification** :  
- Conserver les toasts actuels (affichage + auto-dismiss).  
- Ajouter un **historique de session** : liste des notifications éphémères de la session (optionnel : persistance session storage pour survivre au reload).  
- Optionnel : **centre de notifications** avec onglet “Session” listant cet historique (avec possibilité de vider).  
- Côté backend : partager les flash Laravel avec Inertia (`flash.success`, `flash.error`, etc.) et, au chargement de la page, les injecter dans le store toast + dans l’historique session.

---

### 1.2 Type B — Notifications métier (événements importants, persistés)

**Objectif** : Notifier des événements importants, persistés en BDD, avec possibilité de les consulter plus tard et de gérer les préférences par type.

**Exemples** :  
- **Admins** : nouveau compte créé (inscription).  
- **Créateur / admins** : entité modifiée (déjà en place avec `EntityModifiedNotification`).  
- **Récap** : résumé quotidien (digest) de ce qui a été modifié (entités, inscriptions, etc.).

**Spécification** :  
- Continuer à utiliser la table `notifications` et le trait `Notifiable`.  
- Ajouter les types manquants (ex. “nouveau compte créé” pour les admins).  
- **Centre de notifications** :  
  - Liste des notifications (non lues / toutes), avec lien vers la cible (URL dans `data`).  
  - Marquage “lu” (individuel ou tout marquer).  
  - Optionnel : indicateur “cloche” dans le header avec compteur de non lus.  
- **Préférences par type** :  
  - Pouvoir **désactiver** un type de notification (ex. “nouvelle inscription” pour un admin).  
  - Pouvoir choisir **instantané** vs **récap** (digest) pour certains types (ex. une notif récap par jour au lieu d’une par inscription).

---

## 2. Préférences utilisateur (désactivation / récap)

- **Global** : déjà présent (`notifications_enabled`, `notification_channels`).  
- **Par type** : à ajouter (ex. tableau ou JSON `notification_preferences` : type → activé/désactivé, canal, mode instantané vs digest).  
- **Exemple** : en tant qu’admin, désactiver “nouvelle inscription” en instantané et choisir “un récap par jour” (digest).

Cela implique :  
- Un modèle ou une structure de préférences (en BDD ou dans une colonne JSON `users.notification_preferences`).  
- Dans le service d’envoi (ou une couche au-dessus), ne pas envoyer / ou regrouper selon les préférences.  
- Une UI (profil ou paramètres) pour modifier ces préférences.

---

## 3. Récapitulatif des livrables souhaités

| Livrable | Priorité | Type |
|----------|----------|------|
| Historique de session des toasts (éphémères) | Haute | A |
| Partage flash Laravel → Inertia → toast + historique | Haute | A |
| Centre de notifications (liste BDD + marquer lu) | Haute | B |
| API notifications (liste, marquer lu) | Haute | B |
| Indicateur cloche + compteur non lus (header) | Moyenne | B |
| Notification “nouveau compte” pour admins | Moyenne | B |
| Préférences par type (désactiver / activer) | Moyenne | B |
| Digest (récap quotidien) pour certains types | Basse | B |
| UI paramètres “Notifications” (profil) | Moyenne | B |

La suite (options Laravel et communauté) est dans [OPTIONS_LARAVEL_COMMUNAUTE.md](./OPTIONS_LARAVEL_COMMUNAUTE.md).
