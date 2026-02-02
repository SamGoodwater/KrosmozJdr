# Estimation : renommage de l'entité « classe » en « breed »

**Objectif** : utiliser `breed` dans le code et la documentation technique, tout en conservant **« Classe »** pour l’affichage utilisateur (menu, titres, libellés, règles du jeu).

**Périmètre** : tout ce qui est identifiant technique (fichiers, classes PHP/JS, tables, routes, clés de config, entity key) → `breed` / `breeds`. Tout ce qui est libellé visible par l’utilisateur → rester « Classe » / « Classes ».

---

## 1. Synthèse de l’ampleur

| Catégorie | Fichiers / éléments concernés | Estimation |
|-----------|-------------------------------|------------|
| Base de données (tables, colonnes, pivot) | 4 migrations + 1 nouvelle migration de renommage | Moyen |
| Backend PHP (models, controllers, policies, requests, resources, services) | ~25 fichiers | Élevé |
| Routes (web, api) | 2 fichiers + 1 fichier à renommer | Faible |
| Frontend (entity key, composants, descriptors, registry, config) | ~35 fichiers JS/Vue | Élevé |
| Tests | ~15 fichiers | Moyen |
| Documentation technique | ~20 fichiers | Moyen |
| Fichiers à ne pas modifier (affichage / métier) | docs/400- Règles, libellés UI | — |

**Total estimé** : **~100+ fichiers** à toucher, avec une **nouvelle migration** pour renommer tables/colonnes sans casser l’existant.

---

## 2. Détail par zone

### 2.1 Base de données

- **Table actuelle** : `classes` → à renommer en `breeds`.
- **Colonne FK** : `classe_id` (dans `npcs`, pivot `class_spell`) → `breed_id`.
- **Pivot** : `class_spell` (`classe_id`, `spell_id`) → `breed_spell` (`breed_id`, `spell_id`).

**Fichiers concernés :**

| Fichier | Rôle |
|---------|------|
| `database/migrations/2025_06_01_100110_entity_classes_table.php` | Référence historique (ne pas modifier les anciennes migrations). |
| `database/migrations/2025_06_01_100140_entity_npcs_table.php` | Idem. |
| `database/migrations/2025_11_26_165034_create_pivot_class_spell_table.php` | Idem. |
| `database/migrations/2026_01_30_000001_refactor_states_and_access_levels.php` | Référence `classes` dans une liste. |
| **Nouvelle migration** | À créer : `rename_classes_to_breeds` (rename table, colonnes, contraintes, index). |

**Points d’attention :**  
Créer une seule migration qui :  
`renameTable('classes', 'breeds')`, puis sur `npcs` et l’ancien pivot : renommer `classe_id` → `breed_id`, et renommer la table pivot `class_spell` → `breed_spell`. Vérifier contraintes FK et index.

---

### 2.2 Backend PHP

**Models :**

| Fichier actuel | Action |
|----------------|--------|
| `app/Models/Entity/Classe.php` | Renommer en `Breed.php`, classe `Breed`, `$table = 'breeds'`, relations `breed_id` / `breed_spell`. |
| `app/Models/Entity/Npc.php` | Remplacer `classe_id`, relation `classe()` par `breed_id` et `breed()`, docblocks. |
| `app/Models/Entity/Spell.php` | Relation `classes()` → `breeds()`, pivot `breed_spell`, référence au model `Breed`. |
| `app/Models/User.php` | Relation `createdClasses()` → `createdBreeds()`, type hint `Breed`. |

**Controllers :**

| Fichier actuel | Action |
|----------------|--------|
| `app/Http/Controllers/Entity/ClasseController.php` | Renommer en `BreedController.php`, route model binding `Breed $breed`, références à `Classe` et table. |
| `app/Http/Controllers/Api/ClasseBulkController.php` | → `BreedBulkController.php`, model `Breed`, règles `exists:breeds,id`. |
| `app/Http/Controllers/Api/Table/ClasseTableController.php` | → `BreedTableController.php`, idem. |
| `app/Http/Controllers/Entity/NpcController.php` | Filtre `classe_id` → `breed_id`, eager load `breed`. |
| `app/Http/Controllers/Entity/SpellController.php` | Relations `classes` → `breeds`, `updateClasses` → `updateBreeds`, validation `exists:breeds,id`. |
| `app/Http/Controllers/Api/Table/NpcTableController.php` | `classe` → `breed`, `classe_id` → `breed_id`. |
| `app/Http/Controllers/Api/NpcBulkController.php` | Validation et champs `classe_id` → `breed_id`, `exists:breeds,id`. |
| Controllers Scrapping (DataCollect, ScrappingController, ScrappingSearchController, etc.) | Remplacer références à l’entité « classe » (alias, clés, model) par `breed` / `Breed` selon le rôle de chaque fichier. |

**Requests :**

| Fichier actuel | Action |
|----------------|--------|
| `app/Http/Requests/Entity/StoreClasseRequest.php` | → `StoreBreedRequest.php`. |
| `app/Http/Requests/Entity/UpdateClasseRequest.php` | → `UpdateBreedRequest.php`. |
| `app/Http/Requests/Entity/StoreNpcRequest.php` | `classe_id` → `breed_id`, règle `exists:breeds,id`. |
| `app/Http/Requests/Entity/UpdateNpcRequest.php` | Idem. |

**Resources :**

| Fichier actuel | Action |
|----------------|--------|
| `app/Http/Resources/Entity/ClasseResource.php` | → `BreedResource.php`. |
| `app/Http/Resources/Entity/SpellResource.php` | `classes` → `breeds` (structure API). |
| `app/Http/Resources/Entity/NpcResource.php` | `classe_id` / `classe` → `breed_id` / `breed`. |

**Policies :**

| Fichier actuel | Action |
|----------------|--------|
| `app/Policies/Entity/ClassePolicy.php` | → `BreedPolicy.php`, model `Breed`, paramètre `$breed`. |

**Services :**

| Fichier | Action |
|---------|--------|
| `app/Services/PdfService.php` | Clé d’entité et relations : `classe` → `breed`, `classes` → `breeds`, et pour NPC `classe` → `breed`. |
| `app/Services/Scrapping/fields_config.php` | `entity` / clés liées à l’entité classe → `breed`. |
| Services Scrapping (CollectAliasResolver, ConversionService, IntegrationService, etc.) | Remplacer alias/clés « classe » par `breed`, model `Breed`, table `breeds`. |

**Autres :**

| Fichier | Action |
|---------|--------|
| `config/entity-permissions.php` | Clé `classes` → `breeds`, valeur `Breed::class`. |
| `database/factories/Entity/ClasseFactory.php` | → `BreedFactory.php`, model `Breed`, table `breeds`. |
| `database/factories/Entity/NpcFactory.php` | `classe_id` → `breed_id`. |
| `database/seeders/Entity/ClasseSeeder.php` | → `BreedSeeder.php`, références au model Breed. |
| `app/Console/Commands/IconsJsonGenerator.php` | Chemins d’icônes « classes » / « classe_orientations » : décider si on renomme en `breeds` ou garde pour assets. |
| `_ide_helper.php` / `_ide_helper_models.php` | Régénérer après renommage (ou adapter manuellement). |

---

### 2.3 Routes

| Fichier | Action |
|---------|--------|
| `routes/web.php` | Remplacer `require .../entities/classe.php` par `.../entities/breed.php`. |
| `routes/entities/classe.php` | Renommer le fichier en `breed.php`, préfixe `entities/breeds`, noms de routes `entities.breeds.*`, paramètre `{breed}`, contrôleur `BreedController`. |
| `routes/api.php` | Routes API : segment `entities/classes` → `entities/breeds`, noms `api.entities.classes.*` → `api.entities.breeds.*`, contrainte `entity` (où présent) `classe` → `breed`. |

---

### 2.4 Frontend (Vue / JS)

**Principe :**  
- **Clé d’entité / technique** : `classe` → `breed`, `classes` → `breeds` (registry, entityType, routes, API, permissions).  
- **Libellés utilisateur** : garder « Classe » / « Classes » (menu, titres, placeholders, messages). Idéalement via un mapping central (ex. `entityKey → label`).

**Fichiers et dossiers à renommer / déplacer :**

| Actuel | Nouveau |
|--------|---------|
| `resources/js/Entities/classe/` | `resources/js/Entities/breed/` |
| `resources/js/Entities/classe/classe-descriptors.js` | `breed/breed-descriptors.js` |
| `resources/js/Models/Entity/Classe.js` | `Breed.js` |
| `resources/js/Pages/Pages/entity/classe/` | `resources/js/Pages/Pages/entity/breed/` |
| `resources/js/Pages/Molecules/entity/classe/ClasseViewLarge.vue` | `breed/BreedViewLarge.vue` (et idem Compact, Minimal, Text) |

**Fichiers à adapter (sans renommer) :**

- **Entity registry & config**  
  - `resources/js/Entities/entity-registry.js` : clé `classes` → `breeds`, import `Breed`, `getBreedFieldDescriptors`, `normalizeEntityType('breed'|'breeds')` → `breeds`.  
  - `resources/js/Entities/entity-actions-config.js` : entrée `classes` → `breeds` (config d’actions).  
  - `resources/js/Composables/entity/entityRouteRegistry.js` : si entrée explicite pour classe, la remplacer par `breeds` avec paramètre `breed`.  
  - `resources/js/Entities/entity-actions-config.js` : bloc d’actions « Classes » → clé `breeds`, libellés restant « Classe » si besoin.

- **Descriptors**  
  - Nouveau `breed/breed-descriptors.js` : `getBreedFieldDescriptors`, `_tableConfig.entityType` → `breed`, id de table `breeds.index`, placeholders / titres en « Classe » / « Classes ». Exporter et utiliser partout à la place de l’ancien descriptor classe.

- **Pages entity**  
  - `resources/js/Pages/Pages/entity/breed/Index.vue` : entity-type `breed` / `breeds`, routes `entities.breeds.*`, param `breed`, appels API et permissions sur `breed`/`breeds`.  
  - Inertia : côté Laravel, rendre `Pages/entity/breed/Index` (au lieu de `classe/Index`) avec props `breeds` (ou garder nom de prop `breeds` pour cohérence).

- **Vues entity (Large, Compact, Minimal, Text)**  
  - Renommer composants en `BreedView*`, props/entity-type `breed`, permissions `breed`, routes `entities.breeds.*`, paramètre `breed`.  
  - Garder les libellés affichés « Classe » / « Classes ».

- **Layout / navigation**  
  - `resources/js/Pages/Layouts/Aside.vue` : clé de route et entity `breed`, **label** restant « Classes ».

- **Composables / utils**  
  - `resources/js/Composables/permissions/usePermissions.js` : si liste d’entités en dur, `classe` → `breed`.  
  - `resources/js/Composables/utils/useDownloadPdf.js` : entité `classe` → `breed`.  
  - `resources/js/Composables/entity/useEntityActions.js`, `useBulkEditPanel.js`, `useEntityFormSubmit.js` : pas de changement si tout passe par entity key (sinon `classe` → `breed`).  
  - `resources/js/Utils/entity/resolveEntityViewComponent.js` : mapping `classe` → `breed`, composant `BreedView*`.  
  - `resources/js/Utils/Entity/Configs/TableConfig.js`, `FormConfig.js`, `BulkConfig.js`, `FormFieldConfig.js`, `TableColumnConfig.js` : exemples ou clés `classe` → `breed`.  
  - `resources/js/Utils/Services/SectionStyleService.js`, `SectionParameterService.js`, `BaseMapper.js` : idem si référence explicite à l’entité.  
  - `resources/js/Utils/Formatters/BaseFormatter.js`, `text-truncate.js`, `atomic-design/uiHelper.js` : idem.

- **NPC / Spell / autres entités**  
  - `resources/js/Entities/npc/npc-descriptors.js` : champ `classe` (affichage) peut rester en clé `classe` pour l’API si le backend envoie encore `classe` temporairement, ou passer en `breed` et adapter les vues NPC. À aligner avec NpcResource (breed).  
  - `resources/js/Models/Entity/Npc.js` : propriété / champ `classe` → `breed`.  
  - `resources/js/Pages/Pages/entity/spell/Edit.vue` : `itemLabel: 'classe'` → `itemLabel: 'breed'` (ou garder libellé « Classe » via config).  
  - `resources/js/Pages/Molecules/entity/npc/NpcView*.vue` : champs importants / colonnes `classe` → `breed` (données).  
  - `resources/js/Pages/Organismes/entity/CreateEntityModal.vue` : mapping `classe: 'classe'` → `breed: 'breed'` (ou équivalent).

- **Scrapping frontend**  
  - `resources/js/Composables/utils/useScrapping.js`, `resources/js/Pages/Pages/scrapping/...`, `resources/js/Pages/Organismes/scrapping/...` : remplacer clés d’entité `classe` par `breed` partout où c’est un identifiant technique.

- **Index Atomic Design**  
  - `resources/js/Pages/Molecules/molecules.index.json` (et atoms si besoin) : entrées pour les vues « classe » → chemins et noms `breed/BreedView*`, name `breed`.

- **Autres composants**  
  - Tous les composants qui reçoivent `entity-type="classe"` ou `entityType: 'classe'` / `'classes'` : passer à `breed` / `breeds`.  
  - Conserver partout les libellés visibles « Classe » / « Classes » (titres, placeholders, boutons).

---

### 2.5 Tests

- **PHP**  
  - Renommer / créer : `tests/Feature/Api/Bulk/ClasseBulkControllerTest.php` → `BreedBulkControllerTest.php`, adapter à `Breed`, routes et `breeds`.  
  - Tests de table, contrôleurs entity, policies : tout ce qui référence `Classe`, `classes`, `classe_id` → `Breed`, `breeds`, `breed_id`.  
  - Tests Scrapping : alias et clés `classe` → `breed`.  
  - Tests NPC / Spell : `classe_id`, relation `classe` → `breed_id`, `breed`.

- **JS**  
  - `tests/unit/descriptors/classe-descriptors.test.js` → `breed/breed-descriptors.test.js`, descriptors `getBreedFieldDescriptors`.  
  - `tests/unit/adapters/classe-adapter.test.js` → `breed/breed-adapter.test.js`, entityType `breeds`.  
  - `tests/unit/utils/entity-registry.test.js` : `normalizeEntityType('breed')` → `'breeds'`.  
  - `tests/unit/integration/sectionWorkflow.test.js` : entité « classe » → `breed` si c’est un type d’entité.

---

### 2.6 Documentation (technique)

- **À mettre à jour (terme technique = breed)**  
  - `docs/docs.index.json` : entrées pointant vers « classe » / « classes » (entité) → `breed` / `breeds`.  
  - `docs/20-Content/21-Entities/ENTITIES_OVERVIEW.md` : table et section « Classes » → « Breeds (affichés comme « Classe ») », noms de tables/colonnes `breeds`, `breed_id`, `breed_spell`.  
  - `docs/20-Content/21-Entities/ENTITY_CLASSES.md` : renommer en `ENTITY_BREEDS.md`, contenu technique avec `breed` / `breeds`, et préciser que l’affichage reste « Classe ».  
  - `docs/20-Content/21-Entities/ENTITY_NPCS.md`, `ENTITY_SPELLS.md`, `ENTITY_SPECIALIZATIONS.md`, `ENTITY_CAPABILITIES.md`, `ENTITY_ATTRIBUTES.md` : remplacer `classe_id`, `classes`, `class_spell` par `breed_id`, `breeds`, `breed_spell` dans les descriptions techniques.  
  - `docs/10-BestPractices/SYNTAXE_FORMULES_CARACTERISTIQUES.md` : références à l’entité (config, code) → `breed`.  
  - `docs/20-Content/SCHEMA.md`, `PAGES_SECTIONS.md`, `SECTION_PARAMETERS.md`, `PAGES_SECTIONS_COMPOSABLES.md` : exemples ou listes d’entités `classe` → `breed`.  
  - `docs/30-UI/*` (TANSTACK_TABLE, AVATAR_SYSTEM, FRONTEND_MODELS, etc.) : exemples « classe » → `breed`.  
  - `docs/50-Fonctionnalités/Characteristics-DB/*`, `Scrapping/*` : partout où l’entité est désignée techniquement → `breed`.  
  - `docs/110- To Do/*` : idem si référence à l’entité.

- **À ne pas modifier (métier / affichage)**  
  - `docs/400- Règles/*` : conserver « classe » comme terme du jeu (affichage et règles).

---

### 2.7 Fichiers à ignorer ou à traiter à part

- **storage/** (debugbar, phpstan, cache) : ignorer ou régénérer après coup.  
- **.phpstorm.meta.php** : régénérer ou mettre à jour les clés `entities.classes` → `entities.breeds`, etc.  
- **composer.lock** : pas de changement sémantique « classe » lié à l’entité.  
- **resources/scrapping/sources/dofusdb/entities/class.json** : nom de fichier côté DofusDB (à garder ou renommer selon convention du module scrapping).  
- **resources/scrapping/config/sources/dofusdb/entities/breed.json** : déjà « breed » ; vérifier cohérence avec le reste du renommage.  
- **resources/css** (app.css, custom.css, theme.css) : si « classe » apparaît uniquement comme sélecteur CSS (.classe), ne pas toucher ; si c’est un nom de thème/entité, remplacer par `breed` dans les noms techniques.

---

## 3. Affichage : garder « Classe » / « Classes »

À centraliser si ce n’est pas déjà le cas :

- Un mapping **entity key → libellé** (singulier / pluriel), par exemple :  
  `breed` → « Classe », `breeds` → « Classes ».  
- Utiliser ce mapping pour :  
  - menu (Aside),  
  - titres de page, breadcrumbs,  
  - placeholders (« Rechercher une classe… »),  
  - messages (succès, erreur),  
  - noms de colonnes ou de sections quand ils désignent la même notion métier.

Cela permet de ne pas disperser des chaînes en dur « Classe » partout et de garder une seule source pour l’affichage.

---

## 4. Ordre de travail recommandé

1. **Migration BDD** : créer et exécuter la migration de renommage (`classes` → `breeds`, `classe_id` → `breed_id`, `class_spell` → `breed_spell`).  
2. **Backend** : Models (`Breed`, Npc, Spell, User), puis Policies, Requests, Resources, Controllers, config, factories, seeders, services (dont Scrapping).  
3. **Routes** : renommer `routes/entities/classe.php` en `breed.php`, mettre à jour web.php et api.php.  
4. **Frontend** : registry, entity key, descriptors, puis pages et composants (vues breed, NPC, Spell, layout, composables, config).  
5. **Tests** : PHP puis JS, en suivant les mêmes clés (`breed` / `breeds`).  
6. **Documentation** : mise à jour des docs techniques (entités, schéma, fonctionnalités, UI).  
7. **Vérifications** : IDE helpers, Ziggy (routes), cache front/build, tests E2E si présents.

---

## 5. Risques et points de vigilance

- **Route model binding** : Laravel résout `{breed}` avec le model `Breed` si la variable de route s’appelle `breed` ; vérifier que tous les contrôleurs et middlewares utilisent bien ce nom.  
- **Anciennes URLs** : si des liens publics ou des favoris pointent vers `/entities/classes/...`, prévoir des redirects 301 vers `/entities/breeds/...` (optionnel mais recommandé).  
- **API** : les clients qui appellent `/api/entities/classes/*` ou utilisent des champs `classe_id` / `classe` devront être migrés vers `breeds` et `breed_id` / `breed` ; versionner l’API si besoin.  
- **Scrapping** : config (collect_aliases, sources dofusdb) et code (CollectAliasResolver, ConversionService, IntegrationService) doivent tous utiliser la même clé `breed` et le model `Breed` / table `breeds`.

---

**Conclusion** : la tâche est **volumineuse mais mécanique**. Une estimation réaliste est de **2 à 5 jours** selon la familiarité avec le projet et le niveau de tests/documentation à remettre à jour. Faire le changement en une branche dédiée et valider avec les tests existants + une vérification manuelle des écrans « Classe », NPC et Sorts.
