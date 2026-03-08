# Audit de sécurité — KrosmozJDR

**Date** : 2026-03-07

Ce document recense les points de sécurité vérifiés et les recommandations issues d'une revue de l'application.

---

## 1. Points positifs

| Domaine | État | Détail |
|---------|------|--------|
| **Authentification** | ✅ | Laravel Auth, policies sur User, rôles (admin, super_admin) |
| **Autorisation** | ✅ | Policies systématiques sur les contrôleurs (viewAny, update, delete, etc.) |
| **Mise à jour profil** | ✅ | `UpdateUserRequest` n'inclut pas `role` — pas de mass assignment sur le rôle |
| **Rôle utilisateur** | ✅ | Modification du rôle via route dédiée `updateRole` avec policy |
| **Tri / SQL** | ✅ | Colonnes de tri whitelistées dans EntityTableDataController, SpellController, TableControllers |
| **Raw SQL** | ✅ | `DB::raw`, `orderByRaw` utilisent des chaînes fixes (pas d'injection) |
| **Secrets** | ✅ | `.env` dans `.gitignore`, pas de clés en dur |
| **RGPD** | ✅ | Export, suppression, rate limiting, confirmation mot de passe |
| **Open redirect** | ✅ | Vérification du host dans `AuthenticationException` (referer local uniquement) |
| **Headers sécurité** | ✅ | `X-Content-Type-Options`, `X-Frame-Options` sur les images |

---

## 2. Points d'attention (corrigés ou à surveiller)

### 2.1 Path traversal — Images (corrigé)

**Risque** : Les routes `/media/images/{path}` et `/media/thumbnails/{path}` acceptent un chemin arbitraire. Sans validation, un chemin comme `originals/../../../.env` pourrait permettre de lire des fichiers hors du répertoire de stockage.

**Correction** : Validation du chemin dans `ImageController` — rejet des chemins contenant `..` ou commençant par `/`.

### 2.2 Route clean-thumbnails (corrigé)

**Risque** : `POST /media/clean-thumbnails` était accessible sans authentification. Utilisation abusive possible (DoS, nettoyage répété).

**Correction** : Protection par middleware `auth` et `role:admin`.

### 2.3 CSRF exclu sur API scrapping

**Constat** : Les routes `api/scrapping/*` sont exclues de la vérification CSRF (`bootstrap/app.php`, `VerifyCsrfToken`). Le frontend envoie bien `X-CSRF-TOKEN`, mais le backend ne le valide pas pour ces routes.

**Justification actuelle** : Éviter les erreurs 419 en cas de session/token régénéré pendant les opérations longues (scrapping). Les routes sont protégées par `auth`, `role:admin` et `password.confirm`.

**Recommandation** : Accepter le risque (faible : même origine, admin uniquement) ou réactiver la validation CSRF et gérer les 419 côté frontend (rechargement, nouvelle confirmation mot de passe).

### 2.4 API tables sans auth au niveau route

**Constat** : Les routes `api/tables/*` n'ont que le middleware `web`, pas `auth`. Les contrôleurs appellent toutefois `$this->authorize('viewAny', ...)`, ce qui renvoie 401 si non authentifié.

**Recommandation** : Ajouter `auth` au niveau des routes pour cohérence et fail-fast.

### 2.5 Sections / templates — sourceUrl legal_markdown (corrigé)

**Risque** : Le champ `sourceUrl` du template `legal_markdown` acceptait toute chaîne (max 2048). Le frontend restreint au same-origin, mais le backend ne validait pas, permettant de stocker des URLs externes ou `file://`.

**Correction** : Validation backend via `SectionTemplatePayloadValidator::validateLegalMarkdownSourceUrl()` — uniquement chemins relatifs same-origin (`/...`), caractères sûrs, rejet de `..` (path traversal) et des protocoles dangereux (`javascript:`, `data:`, `file:`).

---

## 3. Recommandations générales

1. **APP_DEBUG** : S'assurer que `APP_DEBUG=false` en production.
2. **Rate limiting** : Le rate limiter `privacy-actions` est en place ; vérifier les limites sur les routes sensibles (login, reset password).
3. **Logs** : Ne pas exposer de stack traces ou chemins internes aux utilisateurs en production.
4. **Mises à jour** : Maintenir Laravel, Vue et les dépendances à jour pour les correctifs de sécurité.

---

## 4. Références

- [SECURITY_PRACTICES.md](./SECURITY_PRACTICES.md) — Bonnes pratiques projet
- [VERIFICATION_CONFIDENTIALITE_GITHUB.md](../100-%20Done/VERIFICATION_CONFIDENTIALITE_GITHUB.md) — Données poussées vers GitHub
