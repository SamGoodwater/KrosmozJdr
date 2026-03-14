# Système de retour utilisateur (feedback) — Krosmoz-JDR

Documentation du système permettant aux visiteurs et utilisateurs de remonter aux admins des signalements (bug, erreur, suggestion, autre).

## 1. Vue d'ensemble

- **Objectif** : Permettre à tout visiteur (connecté ou non) de signaler un bug, une erreur, une suggestion ou un autre type de retour.
- **Accès** : Bouton FAB (Floating Action Button) en bas à droite de l'écran, visible sur toutes les pages utilisant le layout Main.
- **Envoi** : Les retours sont envoyés par email aux administrateurs (utilisateurs avec rôle `admin` ou `super_admin`).

## 2. Composants

### 2.1 Frontend

- **`FeedbackFab.vue`** : Organisme contenant le bouton FAB et le modal de formulaire.
  - Récupère l'URL courante (`window.location.href`)
  - Pré-remplit le pseudo avec `auth.user.name` si l'utilisateur est connecté
  - Champs : type (bogue, erreur, suggestion, autre), message (requis), pièce jointe (optionnel)
- **Emplacement** : Intégré dans [Main.vue](../resources/js/Pages/Layouts/Main.vue)

### 2.2 Backend

- **Route** : `POST /feedback` (nom : `feedback.store`)
- **Contrôleur** : `App\Http\Controllers\FeedbackController`
- **Requête** : `App\Http\Requests\StoreFeedbackRequest`
- **Mailable** : `App\Mail\FeedbackMail`

## 3. Configuration

Fichier `config/feedback.php` :

- **`fallback_email`** : Adresse utilisée si aucun admin n'existe en base (variable `FEEDBACK_FALLBACK_EMAIL`)
- **`throttle_per_minute`** : Nombre maximum de requêtes par minute et par IP (défaut : 6, variable `FEEDBACK_THROTTLE_PER_MINUTE`)

## 4. Validation

- **message** : requis, string, max 2000 caractères
- **type** : requis, in:bug,error,suggestion,other
- **url** : nullable, string, max 500
- **pseudo** : nullable, string, max 100
- **attachment** : nullable, file, max 2 Mo, mimes:jpg,jpeg,png,gif,pdf,txt

## 5. Destinataires

Les emails sont envoyés à tous les utilisateurs ayant `role >= User::ROLE_ADMIN`. Si aucun admin n'existe, l'adresse `config('feedback.fallback_email')` ou `config('mail.from.address')` est utilisée.

## 6. Emails

- **Templates** : `resources/views/emails/feedback.blade.php` (HTML) et `feedback-text.blade.php` (texte brut)
- **Contenu** : Type, URL, pseudo, message, mention de la pièce jointe si présente
- Voir [EMAIL_SYSTEM.md](EMAIL_SYSTEM.md) pour le contexte général.

## 7. Sécurité

- Route accessible sans authentification (tout le monde peut envoyer)
- Throttle pour limiter le spam
- Validation stricte des entrées
- Pièce jointe : pas de stockage persistant, fichier attaché au mail puis jeté
