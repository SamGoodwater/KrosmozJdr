# Vérification confidentialité — Données envoyées vers GitHub

**Date** : 2026-03-03

## Règle appliquée

Seules les identités suivantes peuvent figurer dans le dépôt public :

- **Surnom** : Goodwateur  
- **Email** : contact@krosmoz-jdr.fr  

Tout autre fichier, clé, mot ou phrase faisant référence à l’utilisateur qui crée l’application est considéré comme information personnelle et ne doit pas être poussé vers GitHub.

---

## Vérifications effectuées

### 1. Fichiers et répertoires sensibles

- **`.env`** : bien présent dans `.gitignore`, non versionné.
- **`bootstrap/cache/config.php`** : ignoré via `bootstrap/cache/.gitignore`, non versionné.
- **`storage/debugbar/`** : contenu ignoré (`*` + `!.gitignore`), les JSON de debug (IP, URIs) ne sont pas poussés.
- **`.cursor/mcp.json`** : ignoré (peut contenir des secrets).

### 2. Emails et identifiants

- **contact@krosmoz-jdr.fr** : présent uniquement dans `.env.example`, `Footer.vue` et config mail → **autorisé**.
- **Email personnel** (ancien compte) : trouvé dans les commentaires / aide Playwright → **corrigé** (voir ci‑dessous).

### 3. Clés et tokens

- Aucune clé API, token ou secret en dur détecté dans le code versionné (recherche patterns courants : AWS, Stripe, GitHub token, etc.).

### 4. Auteurs tiers

- **barryvdh@gmail.com** : présent dans `.phpstorm.meta.php`, `_ide_helper.php`, `_ide_helper_models.php` (générés par Laravel IDE Helper). Auteur du package, pas du projet → acceptable pour un dépôt public.

---

## Corrections appliquées (2026-03-03)

Les références à l’email personnel **contact@jdr.iota21.fr** ont été supprimées des fichiers versionnés et alignées sur la configuration réelle des utilisateurs de test :

| Fichier | Modification |
|--------|---------------|
| `playwright/tools/auto-login.js` | Commentaire : `contact@jdr.iota21.fr` → `super-admin@test.fr`, `test@example.com` → `test-user@test.fr` |
| `playwright/playwright-cli.js` | Aide CLI : même remplacement + alignement des autres comptes sur `*@test.fr` (au lieu de `*@test.com`) |
| `playwright/docs/AUTO_LOGIN_GUIDE.md` | Exemple de config : `identifier: 'contact@jdr.iota21.fr'` → `'super-admin@test.fr'` |

La configuration réelle dans `playwright/config/test-users.js` utilisait déjà `super-admin@test.fr` ; seuls les commentaires et la doc affichée étaient encore à jour.

---

## Neutralisation doc règles (complétée)

Les références à l'ancien site ont été neutralisées (Source/Provenance → « DofusJDR (archivé) », INDEX → « PDF du site DofusJDR (archivé) »). Seules **2 mentions** de l'ancienne adresse du JDR (**jdr.iota21.fr**) sont conservées : `1.1.4-ressources-disponibles.md` et `1.3.4-archives-et-ressources.md`.

---

## Synthèse

- **Identités autorisées** : Goodwateur, contact@krosmoz-jdr.fr → conformes.
- **Email personnel / ancien domaine** : retirés des scripts Playwright et de la doc règles, sauf 2 mentions de l'ancienne adresse (1.1.4, 1.3.4).
- **Secrets / .env / cache / debugbar** : non versionnés.
- **Auteurs** : uniquement auteurs de packages tiers (IDE Helper) ; pas d’autre donnée personnelle identifiée dans le code.

Une relecture manuelle des commits avant push reste recommandée (notamment pour les messages de commit et les branches).
