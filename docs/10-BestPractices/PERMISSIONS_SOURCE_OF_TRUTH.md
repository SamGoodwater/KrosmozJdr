# Permissions — Source of Truth (KrosmozJDR)

## Principe

- **Backend = vérité** : toutes les décisions d’autorisation (lecture/écriture) sont prises via **Policies/Gates** Laravel.
- **Frontend = affichage/UX** : l’UI se base sur des flags `can.*` fournis par le backend (Inertia props / Resources) pour afficher/masquer des actions, mais **ne remplace jamais** l’autorisation backend.

---

## Règles à respecter

- Ne jamais coder des checks de rôle dans l’UI du type `user.role === 4` ou `user.role === 'admin'`.
- Utiliser uniquement :
  - **`PageResource::can` / `SectionResource::can`** pour les actions liées à une instance (update/delete/restore/forceDelete).
  - **props `can.*`** sur une page Inertia pour les actions globales (ex: “Créer une page”).
  - Les helpers backend `User::isAdmin()`, `User::isGameMaster()`, `User::verifyRole()` dans les Policies/Services.

---

## Données exposées au frontend

- `auth.user` (Inertia shared props) est construit via `UserLightResource` et fournit :
  - `role` (int)
  - `role_name`
  - `is_admin`, `is_super_admin`, `is_game_master` (booleans)

---

## Règles par zone (Scrapping, Effets, Caractéristiques)

| Zone | Lecture | Écriture / Gestion |
|------|---------|---------------------|
| **Scrapping** | Admin uniquement | Admin uniquement |
| **Effets** (sous-effets, effects, usages) | Tout le monde (guest sans connexion) | Game master et au-dessus |
| **Caractéristiques** (admin) | Admin (pages de gestion) | Admin (création, modification, suppression) |

- **Scrapping** : routes API et page dashboard protégées par `auth` + `role:admin`. Menu « Scrapping » via `access-permissions.scrapping` (users manageAny = admin).
- **Effets** : API GET (for-entity, usages, sub-effects, effects) sans auth ; API POST/PATCH/DELETE et pages admin `admin/sub-effects`, `admin/effects` avec `auth` + `role:game_master`. Menu « Sous-effets » / « Effets » via `canAccess('effectsAdmin')` (spells updateAny = game_master).
- **Caractéristiques** : routes `admin/characteristics` avec `auth` + `role:admin`. Lecture publique des définitions (si exposée ailleurs) pour tout le monde.

---

## Objectif DRY

Une seule logique de permission :

- **Policies** = logique métier et sécurité.
- **Resources/props** = projection stable pour le frontend.


