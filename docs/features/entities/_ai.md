# Entités JDR — carte IA (degré 1a)

> Système générique de gestion des « entités de jeu » (sorts, objets, monstres, classes, etc.). Toutes partagent un modèle de champs commun, des droits unifiés, et un pipeline d'affichage/édition standardisé côté front.

## Quand lire ce nœud

- Ajouter/modifier un type d'entité, un champ, une vue, une colonne de table.
- Comprendre les droits de lecture/écriture d'une fiche.
- Travailler sur les pages Index/Show/Edit d'une entité.

## Concepts clés

- **Champs communs** : `state` (`raw`/`draft`/`auto`/`playable`/`archived`), `read_level` (0-5), `write_level` (0-5), `created_by`, soft delete. Beaucoup ont aussi `official_id`, `dofusdb_id`, `auto_update`. Détail : [README](./README.md#champs-communs). L’état `auto` (proposition IA/script à relire) est dans le code ; le pipeline LLM reste cadré : [IA générative](../../IA/_ai.md).
- **Droits** : matrice rôle × état, puis `read_level`/`write_level`, l'auteur garde l'accès. Code : `app/Policies/Entity/BaseEntityPolicy.php`. Détail : [README](./README.md#droits).
- **Backend CRUD** : un contrôleur web par entité dans `app/Http/Controllers/Entity/` ; validation par Form Requests `app/Http/Requests/Entity/`.
- **Suppression** : `EntityDeletionService` (soft/restore/force + impact) ; API `EntityDeletionController` ; web `delete` → même service. UI : `ConfirmModal` + `delete-impact`.
- **Tables (lecture)** : API server-side TanStack via `app/Http/Controllers/Api/*TableController.php`, changement d'état via `EntityStateController`. Index front : vue **minimal** par défaut (`useTanStackTablePreferences` v4). Sélection de lignes (cases toujours visibles, IDs normalisés) pour CSV / PDF, indépendante des droits d’édition. Raccourcis : `useTanStackTableKeyboard.js` (ignorés pendant la saisie ; pas de Ctrl+N). Pas de panneau d’édition multiple. Filtres : menus select par défaut (`TanStackTableFilters.vue`, `resolveTableFilterLayout`) ; chips seulement si `ui.layout: "chips"`.
- **Registre front** : `resources/js/Entities/entity-registry.js` (modèle + descriptors + adapter par type ; `normalizeEntityType()` normalise singulier/pluriel).
- **Vues** : `minimal` | `line` | `text` | `full` | `edit`. Résolution dynamique : `resources/js/Utils/entity/resolveEntityViewComponent.js`. Conventions : rule `.cursor/rules/entity-views.mdc`. Sorts / capacités Minimal+Line : bordure colorée par l’élément (`getElementGlassSurfaceStyle`, `--element-border-color` / dégradé `--element-border-image`) ; le fond glass reste le thème. Notes de règles : `spellTypeRuleNotes` (sorts), `consumableRuleNotes` / `itemRuleNotes` (conso, monture).
- **Monstres** : coquille `Monster` + stats/sorts/équipements sur `Creature` ; sorts liés en aperçu (`effect_usages_chips`, pas l’arbre) **et** `visibleToUser` (comme les équipements) ; PDF multi + `visibleToUser` ; Full affiche sorts/empty states, équipements seulement s’il y en a. `resolved-stats` créature : même visibilité que le monstre/PNJ lié. Tri catalogue par nom via sous-requête (pas de JOIN `creatures`).
- **Classes (`breeds`)** : fiche / table / PDF filtrent sorts, capacités, traits et PNJ liés avec `visibleToUser` (un sort brouillon n’apparaît pas sur une classe jouable). L’édition charge toujours toutes les liaisons.
- **Spécialisations** : fiche / table / PDF filtrent sorts, capacités, traits, objets, consommables, ressources et PNJ liés avec `visibleToUser` (un sort brouillon n’apparaît pas sur une spécialisation jouable). L’édition charge toujours toutes les liaisons.
- **Capacités** : fiche / index filtrent spécialisations, créatures et états liés avec `visibleToUser` (une spécialisation brouillon n’apparaît pas sur une capacité jouable). L’édition charge toujours toutes les liaisons.
- **Listes lourdes** : index items/monstres/ressources/consommables/conditions/sorts en pagination serveur ; PDF multi filtré `visibleToUser` sur les entités concernées. Nested relations des classes et spécialisations (sorts/capacités) aussi.
- **Tableaux (query)** : `filters[k][]` (multi) ou `filters[k][min]`/`[max]` (plages) ; `InterpretsEntityTableFilters`. Niveau = range dual (`RangeDualCore`, bornes SQL). État = pastilles à droite de « Filtres », défaut Jouable. Types / rareté = menu select. Stats combat en filtres avancés, icône/couleur via `filterCharacteristicMeta`. Pas de filtre texte (recherche `search=`). Tri : alias UI→SQL (`item_type` → `item_type_id`) + `sort.field` colonnes. Défauts de filtres : uniquement si la clé n’est pas déjà posée ; pas de re-apply sur tout `filterOptions` (évite d’effacer la saisie). Overlay sort depuis un monstre : chips dans le payload nested (`SpellNestedPreviewSerializer`).
- **Objets (vue Colonnes)** : image, nom, niveau, type, rareté, bonus (`items.bonus`) ; description et résumé masqués ; `state` si `updateAny`. En-têtes : `column.tooltip`. Filtre Type : types `show_in_catalog` précochés (flag admin, pas apparats/costumes par défaut).
- **Consommables** : filtre Type précoché via `show_in_catalog` (potions, nourritures, parchemins… ; pas certificats / coffres / fées d’artifice par défaut).
- **Ressources, monstres, sorts** : mêmes filtres type / race précochés via `show_in_catalog` (`defaultByCatalog` + options API).
- **Registres de types** : page commune `/admin/content/types/{equipment|resource|consumable|race|spell}` (anciennes URLs `/entities/item-types` etc. redirigent). Deux flags persistés : `show_in_catalog` (en jeu / filtre catalogue) et `allow_scrap` (maj DofusDB). `decision` reste une colonne historique syncée sur les types objet. Déplacement de catégorie seulement item/resource/consumable. Boutons catalogues → cette page.
- **États (conditions)** : scrap Dofus en `raw` ; catalogue préfiltre hors Brut. Les sorts pointent vers les 5 états JDR `playable` (Pesanteur, Empoisonné, Étourdi, Ralenti, Affaibli) via `ConditionCanonicalMapper` ; jeton Dofus conservé (`canonical_condition_id`). Flags mécaniques en chips. Filtre dissipable = interrupteur (`boolean`). Les textes scrapés disent **dissipable** (plus **désenvoûtable**) via `KrosmozGameTerms`. Maintenance : `php artisan conditions:remap-canonical`.
- **Panoplies** : pièces en vue texte + vignette d’équipements ; bonus par palier de pièces (`2p` / `3p`) via `buildCharacteristicEffectCell`. Catalogue : filtres nombre de pièces (`items_count` range), types d’objets présents (`item_type_id`, `defaultByCatalog`), état = `EntityState::options()` (pas dérivé des lignes). Catalogue / fiche lecture : `items` eager-load `visibleToUser` (un brouillon lié ne fuit pas). Édition : `items` sans ce filtre (sinon un sync détacherait la pièce). Fiches **équipement** : `ItemPanoplyMark` + payload `panoplies[]` (`ItemPanoplyPayload`, eager-load table/show **filtré `visibleToUser` / `view`** — un objet jouable ne fuit pas un set ou une pièce brouillon). Édition : équipements puis bonus en tête (`PanoplyBonusEditor`, champs glass) ; recherche via `api.tables.items` (`EntityPickerCore`) ; droits en bas de page.
- **Ouverture fiche** : Afficher (Minimal / Line / Index / table CMS) → `EntityModal` full ; Agrandir (modal) ou Ctrl+clic → page Show. Éditer (options Minimal / Line / tableau) → page Modifier. Config : `entity-actions-config.js`.
- **Rareté** : 0 Commun, 1 Peu commun, 2 Rare, 3 Très rare, 4 Légendaire, 5 Unique — mêmes libellés filtres / vues / édition (`Resource::RARITY`, `RARITY_GRADIENT`).

## Fichiers pivots

- `app/Policies/Entity/BaseEntityPolicy.php` — logique de droits commune (view/create/update/delete + matrice visibilité).
- `app/Http/Controllers/Entity/SpellController.php` — exemple de contrôleur web d'entité (pattern réutilisé).
- `app/Http/Controllers/Api/` — `*TableController`, `EntityStateController`, `EntityDeletionController`.
- `app/Services/Entity/EntityDeletionService.php` — soft delete / restore / force delete + récapitulatif d’impact.
- `resources/js/Entities/entity-registry.js` — point d'entrée front d'une entité.
- `resources/js/Utils/entity/resolveEntityViewComponent.js` — charge le bon composant de vue.
- `resources/js/Pages/Organismes/table/EntityTanStackTable.vue` — table d'index.
- `resources/js/Pages/Molecules/entity/<type>/` — composants de vue par type (`*ViewMinimal`, `*LineRow`, `*ViewText`, `*ViewFull`).

## Descendre

- [README humain](./README.md) — modèle complet, liste des types, droits, flux front/back détaillés.
- Doc existante (L2) : `docs/features/entities/README.md`, `docs/frontend/entity-views/README.md`, `docs/frontend/entity-views/README.md`.
