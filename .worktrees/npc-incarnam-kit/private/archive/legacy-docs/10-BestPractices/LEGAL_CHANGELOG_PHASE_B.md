# Légal, RGPD & changelog — Phase B (release 1.3.2)

**Objectif** : exposer CGU / confidentialité / cookies et le journal de versions **sans URL absolues de développement**, avec Markdown versionné sous `storage/app/public` et navigation semver pour le changelog.

**Voir aussi** : [architecture des routes](./ROUTES_ARCHITECTURE.md) · [audit sécurité](./SECURITY_AUDIT_2026-03.md) (§2.5 *Sections / templates — sourceUrl legal_markdown*).

## Flux Markdown légal (`legal/*.md`)

| Route | Nom Ziggy | Fichier sous `storage/app/public/legal/` |
| --- | --- | --- |
| `/legal/cgu` | `legal.cgu` | `cgu.md` |
| `/legal/politique-donnees` | `legal.politique-donnees` | `politique-donnees.md` |
| `/legal/cookies` | `legal.cookies` | `cookies.md` |

- Les sections CMS `legal_markdown` **préfèrent** `sourceUrl = /legal/{document}` pour rester invariantes quel que soit `APP_URL`.
- Ancien flux toléré lors de migrations : **`/storage/legal/{document}.md`** (whitelist backend inchangée côté contraintes).
- Le rendu Markdown côté client reste **`SectionLegalMarkdownRead`** (Marked + sanitisation HTML).

Pages CMS critiques : voir **`database/seeders/CriticalPagesSeeder.php`** (`/pages/cgu`, `/pages/politique-donnees`, **`/pages/cookies` synthèse**).

Consentement cookies (**`CookieConsentBanner`**) utilise **`route('pages.show', …)`** (pas de `/pages/cgu` en dur).

## Changelog semver (`changelog/{version}.md`)

| Rôle | Emplacement |
| --- | --- |
| Intro commune | `changelog/intro.md` (injectée avant la navigation lorsqu’elle existe) |
| Fichier par release | `changelog/X.Y.Z.md` (semver stricte) |
| Flux agrégé pour le CMS | `GET /changelog/feed/{version}` — route **`changelog.feed`** |

Construction du flux :

1. Contenu facultatif **`intro.md`**
2. Bloc Markdown **navigation** listant tous les semver détectés (liens relatifs `/changelog/feed/…`), version courante en gras Markdown
3. Corps du fichier **`{version}.md`**

Configurer la semver servant de référence publique (**URL par défaut des sections seeded**) via **`PUBLIC_CHANGELOG_SEMVER`** → `config/releases.php`.

## RGPD — parcours attendu

Flux déjà disponibles (**`routes/web/user.php`**, groupe `auth` + throttle) :

- Hub **`route('user.privacy.index')`** : demandes DSAR historisées et exports **`PrivacyExport`**.
- Export (**`POST`** `user.privacy.export`) et suppression (**`POST`** `user.privacy.delete.request`) : middleware **`password.confirm`** + **`throttle:privacy-actions`**.
- Téléchargement export : route signée **`user.privacy.exports.download`**.

Test de fumée : `tests/Feature/Web/LegalAndChangelogMarkdownRoutesTest.php`.

## Rédiger une release semver

Éditer (ou créer) **`changelog/{semver}.md`**. Le fichier public est **destiné aux joueurs et MJ** : pas de jargon dev (routes, classes PHP, CLI).

Structurer ainsi :

1. **Pour tous** — recherche, lecture, bibliothèque, interface, légal, retours.
2. **Pour les MJ / rédacteurs** — droits, création, tableaux de bord (si pertinent).
3. Optionnel : tableau **En bref** ou équivalent.

La section **Technique** et les notes d’implémentation vivent dans `/docs/` (ex. `RELEASE_1.3.2_VERIFICATION.md`), pas dans `storage/app/public/changelog/`.

Ajuster ensuite **`PUBLIC_CHANGELOG_SEMVER`** et, dans le CMS, `sourceUrl` de la section « Changelog » si tu veux un pointage fix différent après gel.
