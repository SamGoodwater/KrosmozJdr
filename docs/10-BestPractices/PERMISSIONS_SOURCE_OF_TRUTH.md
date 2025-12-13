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

## Objectif DRY

Une seule logique de permission :

- **Policies** = logique métier et sécurité.
- **Resources/props** = projection stable pour le frontend.


