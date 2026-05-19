# Visibilité des entités — Phase A (release 1.3.2)

**Objectif** : aligner la lecture **invité / rôles** avec la matrice admin **« Gérer l’affichage »** (type d’entité × état workflow × rôle minimal), **sans** dupliquer ces règles en dehors des **Policies**.

**Voir aussi** : [permissions — source of truth](./PERMISSIONS_SOURCE_OF_TRUTH.md).

## Composants principaux

| Élément | Rôle |
| --- | --- |
| `EntityDisplayVisibilityService` | Fusion des défauts (`*`) + JSON `application_settings.key = entity_display_visibility_rules`, cache résolu ~1 h. |
| `BaseEntityPolicy` | Passe **`passesDisplayVisibilityGate()`** avant `read_level` / `write_level` / état dans `view()`. Les policies enfants surchargeant `update` / `delete` acceptent **`Model`** + **`instanceof`** (compatibilité LSP avec la classe abstraite). |
| `EntityDisplayVisibilityController` | Pages Inertia **`/admin/entity-display-visibility`** (middleware `auth` + `role:admin`). |
| Cache permissions Inertia | `EntityPermissionService::forUser()` met en cache le payload **`permissions`** par utilisateur (**TTL 10 min**), avec suffixe **`.r{révision}`** ; **`bumpPermissionsCacheRevision()`** est appelé après sauvegarde de la matrice pour ne pas garder les anciennes entrées. |

## Pages / sections (décision Q6)

À la création, si le client **n’envoie pas** `read_level` / `write_level` :

- **`read_level`** : invité (`User::ROLE_GUEST`) pour la lecture maximale hors réglage fin.
- **`write_level`** : **meneur de jeu et au-dessus** (`User::ROLE_GAME_MASTER`), aligné sur le fait que *admin, super_admin, créateur, MJ* peuvent encore affiner au cas par cas.

Voir `StorePageRequest` et `StoreSectionRequest` (`prepareForValidation`). **`PageFactory`** garde encore **`write_level` = administrateur** par défaut (tests qui isolent l’édition hors auteur/admin) ; surcharger en test si nécessaire.

## Tests

- `tests/Feature/Admin/EntityDisplayVisibilityControllerTest.php` — contrôle d’accès admin + bump de révision après PATCH.
- `tests/Feature/Cms/DefaultCmsWriteLevelsTest.php` — défauts création page/section + exemple invité / sort playable.
- `tests/Feature/Api/GlobalSearchControllerTest.php` — recherche globale avec Gate `view`.

## Recette manuelle croisée (Phase B)

1. Modifier la matrice : vérifier en navigation privée / compte joueur qu’un contenu « jouable » se comporte comme attendu.
2. Après modification de la matrice, un utilisateur déjà connecté peut au pire retarder jusqu’à **10 min** (TTL cache permissions) avant de refléter l’état SSR ; une navigation complète aide à rafraîchir les props Inertia.
3. Consulter également la doc [**Phase B légal/changelog**](LEGAL_CHANGELOG_PHASE_B.md) (RGPD hub + routes `/legal/*`).
