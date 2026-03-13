# Authentification OAuth — GitHub, Discord et Steam

## Vue d'ensemble

Le projet Krosmoz-JDR propose une authentification multi-méthodes :

- **Classique** : inscription par email + mot de passe
- **GitHub** : connexion/inscription via OAuth GitHub
- **Discord** : connexion/inscription via OAuth Discord
- **Steam** : connexion/inscription via Steam OpenID

Les utilisateurs peuvent combiner plusieurs méthodes (ex. compte classique + GitHub lié) et gérer leurs connexions depuis les paramètres du compte.

## Configuration

### Variables d'environnement

Ajouter dans `.env` :

```env
# GitHub OAuth — https://github.com/settings/developers
GITHUB_CLIENT_ID=
GITHUB_CLIENT_SECRET=
GITHUB_REDIRECT_URI="${APP_URL}/auth/github/callback"

# Discord OAuth — https://discord.com/developers/applications
DISCORD_CLIENT_ID=
DISCORD_CLIENT_SECRET=
DISCORD_REDIRECT_URI="${APP_URL}/auth/discord/callback"

# Steam OpenID — https://steamcommunity.com/dev/apikey
STEAM_CLIENT_SECRET=
STEAM_REDIRECT_URI="${APP_URL}/auth/steam/callback"
STEAM_ALLOWED_HOSTS=example.com,www.example.com
```

**Important** : Si les credentials d'un provider ne sont pas renseignés (client_id + client_secret pour GitHub/Discord, client_secret pour Steam), ce provider est automatiquement désactivé. Les boutons OAuth correspondants ne s'affichent pas sur les pages Login, Register et Paramètres.

### Création des applications OAuth

1. **GitHub** : [Developer settings](https://github.com/settings/developers) → New OAuth App
   - Authorization callback URL : `https://votredomaine.com/auth/github/callback`

2. **Discord** : [Discord Developer Portal](https://discord.com/developers/applications) → New Application → OAuth2
   - Redirects : `https://votredomaine.com/auth/discord/callback`
   - Scopes : `identify`, `email` (pour récupérer l'email)

3. **Steam** : [Steam Web API Key](https://steamcommunity.com/dev/apikey)
   - Pas de redirect à configurer côté Steam (OpenID)
   - `STEAM_ALLOWED_HOSTS` : domaines autorisés pour la validation du retour (sécurité)

## Flux utilisateur

### Connexion / Inscription

- Sur les pages Login et Register, des boutons permettent de se connecter ou s'inscrire via GitHub, Discord ou Steam.
- Si le compte OAuth existe déjà (provider + provider_id) : connexion directe.
- Si l'email OAuth correspond à un compte existant : **page de confirmation** avant liaison (évite la fusion silencieuse).
- Sinon : création d'un nouveau compte (email, pseudo, avatar depuis OAuth ; mot de passe null).

### Liaison d'un provider

- Depuis **Paramètres → Connexions**, un utilisateur connecté peut lier GitHub ou Discord à son compte.
- Un clic sur « Lier » redirige vers le provider OAuth ; au retour, le compte est lié.

### Déliaison

- Un provider peut être délié uniquement si l'utilisateur conserve au moins une autre méthode de connexion (mot de passe ou autre provider).

### Définir un mot de passe (comptes OAuth-only)

- Un utilisateur inscrit uniquement via OAuth (sans mot de passe) peut définir un mot de passe :
  - **Paramètres → Connexions** : section « Définir un mot de passe »
  - **Mon compte** : section « Définir un mot de passe » (sans champ « mot de passe actuel »)
- Une fois défini, il peut se connecter par email + mot de passe ou via les providers liés.

## Données récupérées

| Provider | Email | Pseudo | Avatar |
|----------|-------|--------|--------|
| GitHub   | Oui   | Oui    | Oui    |
| Discord  | Oui*  | Oui    | Oui    |
| Steam    | Non** | Oui    | Oui    |

\* Discord peut retourner un email null si l'utilisateur a masqué son email. Dans ce cas, un email placeholder est utilisé et l'utilisateur est invité à compléter son profil.

\** Steam (OpenID) ne fournit pas d'email. Un email placeholder est utilisé ; l'utilisateur peut compléter son profil dans les paramètres.

## Sécurité

- **Rate limiting** : 10 requêtes/minute sur les routes OAuth (redirect, callback, link, unlink, convert).
- **Validation provider** : seuls les providers avec credentials configurés et dans la whitelist sont acceptés.
- **Déliaison** : impossible de délier le dernier moyen de connexion (mot de passe ou provider).
- **State OAuth** : géré automatiquement par Laravel Socialite (protection CSRF sur le flux OAuth).

## Architecture technique

- **Laravel Socialite** : OAuth client
- **socialiteproviders/discord** : provider Discord
- **socialiteproviders/steam** : provider Steam (OpenID)
- **Table `oauth_accounts`** : liaison user ↔ provider (provider_id, provider_email, etc.)
- **Colonne `users.password`** : nullable pour les comptes OAuth-only

## Routes

| Méthode | URI | Rôle |
|---------|-----|------|
| GET | `/auth/{provider}` | Redirection vers le provider OAuth |
| GET | `/auth/{provider}/callback` | Callback OAuth (connexion, inscription, liaison) |
| POST | `/user/oauth/convert` | Conversion OAuth-only → compte classique |
| GET | `/user/oauth/link/{provider}` | Lier un provider (redirige vers OAuth) |
| DELETE | `/user/oauth/unlink/{provider}` | Délier un provider |
