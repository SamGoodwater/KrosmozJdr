# Permissions & Auth — carte IA (degré 1a)

> Authentification par session (Laravel, + OAuth GitHub/Discord/Steam) et autorisation à plusieurs niveaux : rôle utilisateur entier 0→5, middlewares de zone, policies (matrice rôle×état + `read_level`/`write_level`). Le front ne fait que projeter les droits calculés côté serveur.

## Quand lire ce nœud

- Gérer les rôles, l'inscription/login, l'OAuth, la confirmation de mot de passe.
- Restreindre une route ou une action (middleware, policy, ability).
- Comprendre comment les droits arrivent au front.

## Concepts clés

- **Rôles** : entier `users.role` 0→5 = guest, user, player, game_master, admin, super_admin (`app/Models/User.php`, constantes `ROLES`). Invité = `user === null` → niveau 0. `verifyRole()`, `isAdmin()` (≥4), `isGameMaster()` (≥3), `isSuperAdmin()` (=5). Détail : [README](./README.md#roles).
- **Super admin** : `isSuperAdmin()` inclut le compte système ; `isInteractiveSuperAdmin()` = humain (utilisé pour le bypass web). Côté Inertia, `is_super_admin` = interactif. Ne pas confondre.
- **Auth** : session guard `web` ; contrôleurs `app/Http/Controllers/Auth/*` ; routes `routes/auth.php`. Sanctum présent mais **non branché** sur `User` (pas de tokens) → auth réelle = session.
- **OAuth** : `OAuthController` + modèle `OAuthAccount` (providers github/discord/steam), flux redirect/callback/link/transfer. Détail : [README](./README.md#oauth).
- **Middlewares** : `role:` (`CheckRole`), `admin.area`, `content.area`, `password.confirm` (`RequirePasswordWithInactivity`, 423 si JSON). Détail : [README](./README.md#middlewares).
- **Policies** : `BaseEntityPolicy` (admin → auteur → matrice affichage → state + read/write_level) ; surcharges par entité ; `UserPolicy`. `CreaturePolicy::viewResolvedStats` suit le `view` du monstre/PNJ lié (pas un bypass public). Détail : [README](./README.md#policies).
- **Projection front** : `EntityPermissionService` calcule les droits (cache 10 min) → partagés via `HandleInertiaRequests` → composable `usePermissions` (`can`, `canAccess`). Les droits **par ligne** viennent du champ `can` des Resources, pas de `usePermissions`.

## Fichiers pivots

- `app/Models/User.php`, `app/Models/OAuthAccount.php`.
- `app/Http/Controllers/Auth/*`, `app/Http/Controllers/Auth/OAuthController.php`, `app/Support/OAuthConfig.php`.
- `app/Http/Middleware/CheckRole.php`, `EnsureAdminAreaAccess.php`, `EnsureContentManagementAccess.php`, `RequirePasswordWithInactivity.php`, `HandleInertiaRequests.php`.
- `app/Policies/Entity/BaseEntityPolicy.php`, `app/Policies/UserPolicy.php`.
- `app/Support/EntityPermissions/EntityPermissionService.php`, `app/Services/EntityDisplay/EntityDisplayVisibilityService.php` (`constrainQueryToViewer` pour listes ; colonnes `state`/`read_level`/`write_level`/`created_by` qualifiées par table, pour rester valides après un JOIN). Nested spécialisations : `SpecializationController` / `SpecializationTableController` / `PdfService` appliquent `visibleToUser` aux sorts/capacités/traits/objets/PNJ liés.
- `config/entity-permissions.php`, `config/access-permissions.php`, `config/auth.php`.
- `resources/js/Composables/permissions/usePermissions.js`, `Composables/auth/useProtectedAdminAction.js`, `Pages/Molecules/action/ConfirmPasswordModal.vue`.

## Descendre

- [README humain](./README.md) — rôles, auth, OAuth, middlewares, policies, configs, flux Inertia.
- Droits par entité : [../entities/_ai.md](../entities/_ai.md).
- Doc existante (L2) : `docs/features/permissions/README.md`, `ENTITY_VISIBILITY_PHASE_A.md`.
