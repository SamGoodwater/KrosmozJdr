# Entités JDR

Les « entités de jeu » sont les objets métier du référentiel KrosmozJDR : sorts, objets, ressources, consommables, monstres, PNJ, classes, spécialisations, capacités, conditions, traits, panoplies, boutiques, campagnes, scénarios. Elles partagent un modèle de champs, un système de droits et un pipeline d'affichage communs.

## Champs communs

Toutes les entités principales et leurs typages exposent au minimum :

| Champ | Type | Rôle |
| --- | --- | --- |
| `state` | string | `raw`, `draft`, `playable`, `archived` |
| `read_level` | tinyint (0-5) | Rôle minimal pour lire |
| `write_level` | tinyint (0-5) | Rôle minimal pour modifier (≥ `read_level`) |
| `created_by` | FK users | Auteur (nullable) |
| `created_at` / `updated_at` | datetime | Horodatage |
| `deleted_at` | datetime | Soft delete |

De nombreuses entités importables ajoutent `official_id`, `dofusdb_id`, `dofus_version`, `auto_update` (voir [Scrapping](../scrapping/README.md)), ainsi que `image` / `icon`.

Référence exhaustive des champs par entité : `docs/features/entities/README.md` (un fichier `ENTITY_*.md` par type).

## Types d'entités

| Catégorie | Types |
| --- | --- |
| Gameplay | `spells`, `items`, `resources`, `consumables`, `monsters`, `npcs`, `breeds` (classes), `capabilities`, `conditions`, `creature-traits`, `specializations`, `panoplies` |
| Méta | `campaigns`, `scenarios`, `shops` |
| Typages (référentiels) | `item-types`, `resource-types`, `consumable-types`, `spell-types`, `monster-races` |
| Interne | `creatures` (classe mère de `monsters` et `npcs`, pas d'accès direct) |

Note : la table des classes est `breeds` (et la FK `breed_id`) pour éviter le mot réservé `class`.

### Métadonnées globales des sorts

Les fiches de sort stockent indépendamment des effets détaillés les contraintes globales de lancement :
`cast_in_line`, `cast_in_diagonal`, `target_type` (`direct`, `trap`, `glyph`), `max_stack` et
`global_cooldown`. Les deux limites numériques vont de 0 à 10 ; `max_stack = 0` signifie « non limité ».
Lors d'un import DofusDB, ces valeurs viennent du premier niveau du sort et `target_type` est déduit de ses
triggers. Cette couche descriptive ne modifie pas l'exécution ni la résolution des effets.

## Droits

La logique est centralisée dans `app/Policies/Entity/BaseEntityPolicy.php`. Pour la lecture (`view`) :

1. Un **admin** voit tout ; l'**auteur** (`created_by`) voit toujours sa fiche.
2. Une **matrice « Gérer l'affichage »** (`EntityDisplayVisibilityService`) fixe le rôle minimal par état.
3. Ensuite, selon `state` :
   - `playable` / `archived` : visible si `rôle ≥ read_level`.
   - `raw` / `draft` : réservé aux éditeurs (`rôle ≥ write_level`).

Pour l'écriture (`update`/`delete`) : admin, auteur, ou `rôle ≥ write_level`. Les abilities « bulk » (`updateAny`, `deleteAny`, `manageAny`) ciblent game_master/admin. Le registre des permissions exposées au front est dans `config/entity-permissions.php` (consommé par `EntityPermissionService`, partagé via Inertia → composable `usePermissions`).

La restauration et la suppression définitive (`restore`, `forceDelete`) sont réservées aux admins/super-admins via la policy de base.

## Backend (CRUD)

- Un contrôleur web par entité : `app/Http/Controllers/Entity/<Type>Controller.php` (ex. `SpellController`, `ItemController`, `MonsterController`). Pattern : `index`/`show` publics, `create`/`store`/`edit`/`update`/`destroy` sous `auth`, plus des routes relationnelles (ex. sorts d'une classe).
- Validation : Form Requests dédiées dans `app/Http/Requests/Entity/` (une par action).
- Transformation de sortie : `app/Http/Resources/Entity/`.
- Lecture en table : `app/Http/Controllers/Api/<Type>TableController.php` (datasets TanStack server-side), édition multiple via `*BulkController`, changement d'état via `EntityStateController`.
- Corbeille entités : API générique `api/entities/{entityType}/{id}` (`DELETE` soft delete), `POST .../restore`, `DELETE .../force`, `GET .../delete-impact`. La logique commune est dans `app/Services/Entity/EntityDeletionService.php` et la résolution des modèles dans `app/Support/EntityModelRegistry.php`. Les routes web `DELETE entities/{type}/{id}` déléguent aussi à ce service (notifications + journal admin).
- Soft delete : trait `SoftDeletes` sur les modèles d’entité JDR, y compris `Monster` et `Npc` (colonne `deleted_at`).
- Force delete : détache les relations `BelongsToMany`, supprime les médias Spatie, puis `forceDelete` ; refusé (422) si l’entité n’est pas déjà en corbeille.
- Journal admin : les suppressions/restaurations d'entités alimentent `admin_activity_logs` via `AdminActivityLogger`. La page `/admin/activity-log` affiche les activités récentes et la corbeille centralisée, avec confirmation + récapitulatif d’impact avant restauration / suppression définitive.

## Frontend

- **Registre** : `resources/js/Entities/entity-registry.js` associe à chaque type son modèle (`resources/js/Models/Entity/`), ses descriptors de champs (`resources/js/Entities/<type>/<type>-descriptors.js`), son `responseAdapter` (via `createEntityAdapter`) et des `minimalImportantFields`. `normalizeEntityType()` gère les variantes singulier/pluriel/camelCase.
- **Pages** : `resources/js/Pages/Pages/entity/<type>/` (`Index.vue` table, `Show.vue`, `Edit.vue`).
- **Vues** : 5 vues canoniques résolues par `resources/js/Utils/entity/resolveEntityViewComponent.js` :

  | Vue | Composant | Usage |
  | --- | --- | --- |
  | `minimal` | `*ViewMinimal` | Cartes, grille |
  | `line` | `*LineRow` | Liste dense (table) |
  | `text` | `*ViewText` | Inline + overlay |
  | `full` | `*ViewFull` | Détail page ou modal |
  | `edit` | `EntityEditForm` / pages `Edit.vue` | Édition unitaire |

  Les composants par type sont dans `resources/js/Pages/Molecules/entity/<type>/`. Le resolver charge encore une vue `quickedit` (`EntityQuickEdit.vue`) pour le panneau d’édition multiple du tableau (voir rule `.cursor/rules/entity-views.mdc`).
- **Table** : `resources/js/Pages/Organismes/table/EntityTanStackTable.vue` (server-side) ; préférences/filtres via `resources/js/Composables/table/*`. Les en-têtes lisent `column.tooltip`. Catalogue objets : image, nom, niveau, type, rareté, bonus (`items.bonus`) ; `state` réservé aux éditeurs. Rareté 0–5 : Commun, Peu commun, Rare, Très rare, Légendaire, Unique (mêmes libellés filtres et vues). Panoplies : pièces en vue texte, vignette = images d’équipements ou initiales. Un équipement du set affiche `ItemPanoplyMark` (payload `panoplies` via `ItemPanoplyPayload`). Édition panoplie : équipements puis bonus en tête (cartes du formulaire, `PanoplyBonusEditor`) ; droits en bas. Recherche catalogue (`EntityPickerCore`).
- **Afficher** : Minimal / Line / Index / CMS (`SectionEntityTableRead`) ouvrent la modal full (`EntityModal`). **Agrandir** (depuis la modal) ou Ctrl+clic mènent à la page Show.
- **Éditer** : raccourci des options → page Modifier. Panneau tableau `EntityQuickEditPanel` pour l’édition multiple.
- **Query tableaux** : filtres multi en `filters[key][]` (CSV encore accepté) ; `InterpretsEntityTableFilters` fait un `whereIn`. Le tri mappe les ids de colonnes (`item_type`, `monster_race`) vers les FK SQL ; `column.sort.field` côté front. La recherche serveur envoie `search=` ; en client elle porte aussi sur le nom / la description de l’entité. Les inputs de filtres gardent la saisie en cours (défauts seulement si la clé n’est pas déjà posée).
- **Aperçu sort depuis un monstre** : sorts liés avec `effect_usages_chips` (`SpellNestedPreviewSerializer`) ; eager-load table/show filtré `visibleToUser` (un sort brouillon ne fuit pas via un monstre jouable). Clic → `SpellViewMinimal` étendu (effets + actions à droite du titre, overflow dropdown). Fetch `api.tables.spells` seulement si les chips manquent.
- **Édition** : `resources/js/Pages/Organismes/entity/EntityEditForm.vue`, modales (`EntityModal`, `CreateEntityModal`, `EntityQuickEditPanel`).
- **Suppression UI** : `useEntityActionDispatcher` ouvre une `ConfirmModal` avec le récapitulatif `delete-impact` (relations détachées, médias) avant soft delete depuis page/modal d’entité.

## Exemple : ajouter un champ à une entité

1. Migration + champ sur le modèle Eloquent (`app/Models/Entity/<Type>.php`).
2. Form Request (`app/Http/Requests/Entity/`) pour la validation.
3. Descriptor front (`resources/js/Entities/<type>/<type>-descriptors.js`) pour l'affichage/édition.
4. Si pertinent en table : colonne dans la config TanStack du type.

## Pour aller plus loin

- `docs/features/entities/README.md` — modèle de données détaillé, champs par entité, pivots.
- `docs/frontend/entity-views/README.md` — système de vues.
- `.cursor/rules/entity-views.mdc` — conventions de vues.
- [IA générative](../../IA/README.md) — cadrage d’un état `ai_review` (proposition LLM à relire), non implémenté.
