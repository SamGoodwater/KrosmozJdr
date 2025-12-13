# Cartographie Pages/Sections — Surface technique (Backend + Frontend)

**Objectif** : lister tous les points d’entrée du système Pages/Sections, les actions mutantes, et leur protection (authn/authz/validation).

> Ce document sert de référence pour l’audit DRY + sécurité et pour éviter plusieurs logiques concurrentes.

---

## Backend — Routes

### Pages — `routes/page.php`

- **Lecture publique**
  - `GET /pages` → `PageController@index` (policy `viewAny`)
  - `GET /pages/menu` → `PageController@menu` (filtrage via `PageService::getMenuPages`)
  - `GET /pages/{page:slug}` → `PageController@show` (Gate/policy `view`, invités autorisés selon visibilité)

- **Mutations (auth middleware)**
  - `PATCH /pages/reorder` → `PageController@reorder` (validation inline + `authorize('update', $page)` par item)
  - `GET /pages/{page}/edit` → `PageController@edit` (`authorize('update', $page)`)
  - `PATCH /pages/{page}` → `PageController@update` (`UpdatePageRequest` + `authorize('update', $page)`)
  - `GET /pages/create` → `PageController@create` (`authorize('create', Page::class)`)
  - `POST /pages` → `PageController@store` (`StorePageRequest` + `authorize('create', Page::class)`)
  - `DELETE /pages/{page}` → `PageController@delete` (`authorize('delete', $page)`)
  - `POST /pages/{page}/restore` → `PageController@restore` (`authorize('restore', $page)`)
  - `DELETE /pages/{page}/force` → `PageController@forceDelete` (`authorize('forceDelete', $page)`)

### Sections — `routes/page.php`

- **Mutations (auth middleware)**
  - `GET /sections` → `SectionController@index` (`authorize('viewAny', Section::class)`)
  - `PATCH /sections/reorder` → `SectionController@reorder` (validation inline + `authorize('update', $section)` par section)
  - `GET /sections/create` → `SectionController@create` (deprecated, redirige)
  - `POST /sections` → `SectionController@store` (`StoreSectionRequest` + `authorize('create', [Section::class, $page])`)
  - `GET /sections/{section}` → `SectionController@show` (Gate/policy `view`, redirige vers page)
  - `GET /sections/{section}/edit` → `SectionController@edit` (`authorize('update', $section)`, deprecated)
  - `PATCH /sections/{section}` → `SectionController@update` (`UpdateSectionRequest` + `authorize('update', $section)`)
  - `DELETE /sections/{section}` → `SectionController@delete` (`authorize('delete', $section)`)
  - `POST /sections/{section}/restore` → `SectionController@restore` (`authorize('restore', $section)`)
  - `DELETE /sections/{section}/force` → `SectionController@forceDelete` (`authorize('forceDelete', $section)`)

- **Fichiers liés aux sections**
  - `POST /sections/{section}/files` → `SectionController@storeFile` (`StoreFileRequest` + `Gate::authorize('update', $section)`)
  - `DELETE /sections/{section}/files/{file}` → `SectionController@deleteFile` (`Gate::authorize('update', $section)`)

---

## Backend — Contrats d’autorisation (AuthZ)

### Policies

- `app/Policies/PagePolicy.php`
  - `viewAny(?User)`
  - `view(?User, Page)`
  - `create(User)`
  - `update(User, Page)`
  - `delete(User, Page)`
  - `restore(User, Page)`
  - `forceDelete(User, Page)`

- `app/Policies/SectionPolicy.php`
  - `viewAny(User)`
  - `view(?User, Section)`
  - `create(User, Page)` (appelée via `authorize('create', [Section::class, $page])` et `StoreSectionRequest::authorize()`)
  - `update(User, Section)` (délègue à `Section::canBeEditedBy`)
  - `delete(User, Section)` (délègue à `$user->can('update', $section->page)`)
  - `restore(User, Section)`
  - `forceDelete(User, Section)`

> Note : `app/Providers/AuthServiceProvider.php` ne mappe pas explicitement les policies. Laravel les résout par convention (`Page` → `PagePolicy`, `Section` → `SectionPolicy`).

### Modèles (helpers & scopes)

- `app/Models/Page.php`
  - Scopes: `published`, `inMenu`, `visibleFor`, `forMenu`
  - Helpers: `isVisibleFor`, `canBeViewedBy`, `canBeEditedBy`
  - Casts: `is_visible`/`can_edit_role` (`Visibility`), `state` (`PageState`)

- `app/Models/Section.php`
  - Scopes: `published`, `visibleFor`, `displayable`
  - Helpers: `isVisibleFor`, `canBeViewedBy`, `canBeEditedBy`
  - Casts: `template` (`SectionType`), `data/settings` (array), `is_visible`/`can_edit_role` (`Visibility`), `state` (`PageState`)

---

## Backend — Validation (FormRequests)

- Pages
  - `app/Http/Requests/StorePageRequest.php` (autorise via policy + règles enums `Visibility`/`PageState`)
  - `app/Http/Requests/UpdatePageRequest.php` (autorise via policy)

- Sections
  - `app/Http/Requests/StoreSectionRequest.php` (autorise via policy + règles dynamiques par `SectionType`)
  - `app/Http/Requests/UpdateSectionRequest.php` (autorise via policy + règles dynamiques)

## Backend — Sanitization / XSS

- `app/Services/SectionService.php`
  - Sanitization HTML **server-side** via `mews/purifier` (`Purifier::clean(..., 'section_text')`) pour `SectionType::TEXT` (champ `data.content`).
  - Configuration profil: `config/purifier.php` → `settings.section_text` (allowlist stricte, `URI.AllowedSchemes` http/https).

---

## Frontend — Pages/Sections (Inertia)

### Pages Inertia

- `resources/js/Pages/Pages/page/Index.vue`
  - Create page via modal `CreatePageModal` → `POST pages.store`
  - Reorder via drag&drop → `PATCH pages.reorder`
  - Delete page → `DELETE pages.delete`

- `resources/js/Pages/Pages/page/Show.vue`
  - Render via `PageRenderer` (organism)

### Organismes/section

- `resources/js/Pages/Organismes/section/PageRenderer.vue`
  - Rend les sections via `SectionRenderer`
  - Ouvre `EditPageModal` + `CreateSectionModal`
  - Utilise `can.update` depuis `PageResource` (avec fallback rôle: à supprimer dans l’audit)

- `resources/js/Pages/Organismes/section/SectionRenderer.vue`
  - Load templates read/edit dynamiquement
  - Appels backend via `useSectionAPI` (`sections.update`, `sections.delete`, etc.)
  - Ouvre `SectionParamsModal`

- `resources/js/Pages/Organismes/section/PageSectionEditor.vue`
  - Réordonne les sections → `useSectionAPI.reorderSections()` → `PATCH sections.reorder`

### Composables API

- `resources/js/Pages/Organismes/section/composables/useSectionAPI.js`
  - CRUD + reorder + restore + forceDelete + fichiers (FormData)

---

## Notes “audit” (points à vérifier ensuite)

- Source de vérité permissions côté UI :
  - **Droits actionnables** : utiliser `page.can.*` / `section.can.*` fournis par `PageResource` et `SectionResource`.
  - **Rôles UI (menus, accès admin)** : utiliser `auth.user.is_admin / is_game_master / is_super_admin` fournis par `UserLightResource`.
  - Éviter les checks `role === 4` / `role >= 4` dans le frontend (magic numbers).

- `v-html` sur contenu riche (XSS): ex `resources/js/Pages/Organismes/section/templates/text/SectionTextRead.vue`.
- Cohérence rôles: `users.role` est un **integer** (migration `database/migrations/2025_06_01_100000_entity_users_table.php`), mais on voit des checks string dans certains endroits.
- Nettoyage de la doc: les routes `forceDelete` sont actuellement **policy-only** (pas de middleware `role:*`).


