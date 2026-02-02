# Plan de réalisation : renommage classe → breed

Plan exécutable, phase par phase. Chaque phase se termine par un point de contrôle (tests / vérifications). Référence : [ESTIMATION_RENOMMAGE_CLASSE_EN_BREED.md](./ESTIMATION_RENOMMAGE_CLASSE_EN_BREED.md).

**Principe** : code et BDD en `breed` / `breeds` ; affichage utilisateur reste « Classe » / « Classes ».

---

## Avant de commencer

- [ ] Créer une branche dédiée : `git checkout -b refactor/classe-to-breed`
- [ ] S’assurer que les tests passent et que l’app tourne (migrations à jour, `php artisan migrate`, build front)
- [ ] Prévoir une sauvegarde BDD si environnement partagé

---

## Phase 1 — Base de données

**Objectif** : renommer tables et colonnes sans modifier les migrations existantes.

### 1.1 Créer la migration de renommage

Créer une nouvelle migration (ex. `database/migrations/YYYY_MM_DD_HHMMSS_rename_classes_to_breeds.php`) qui :

1. **Renommer la table principale**
   - `Schema::rename('classes', 'breeds')`

2. **Renommer la table pivot et ses colonnes**
   - Créer une nouvelle table `breed_spell` avec `breed_id`, `spell_id`
   - Copier les données depuis `class_spell` (mapper `classe_id` → `breed_id`)
   - Supprimer l’ancienne table `class_spell`

3. **Renommer la colonne FK dans `npcs`**
   - `Schema::table('npcs', fn ($t) => $t->renameColumn('classe_id', 'breed_id'))`
   - Vérifier que les contraintes FK sont correctes (référence vers `breeds.id`)

4. **Mettre à jour la migration `2026_01_30_000001_refactor_states_and_access_levels.php`**  
   Si elle référence la table `classes` dans une liste de tables, remplacer par `breeds` dans cette migration (ou, si déjà exécutée en prod, ajouter une migration séparée qui met à jour les éventuelles configs/states qui citent `classes`).

**Checkpoint Phase 1**

- [ ] `php artisan migrate` s’exécute sans erreur
- [ ] Tables `breeds`, `breed_spell` existent ; `npcs` a bien `breed_id`
- [ ] Aucune référence à `classes` ou `classe_id` dans le schéma actuel

---

## Phase 2 — Backend : Models et relations

**Objectif** : modèle `Breed` et relations cohérentes partout.

### 2.1 Model Breed

1. Créer `app/Models/Entity/Breed.php` à partir de `Classe.php` :
   - Renommer la classe en `Breed`
   - `protected $table = 'breeds'`
   - Relation `npcs()` : `hasMany(Npc::class, 'breed_id')`
   - Relation `spells()` : `belongsToMany(Spell::class, 'breed_spell', 'breed_id', 'spell_id')`
   - Conserver les constantes (STATE_*), fillable, casts, `createdBy()`
2. Supprimer `app/Models/Entity/Classe.php`

### 2.2 Models liés

1. **Npc** (`app/Models/Entity/Npc.php`)  
   - `$fillable` : `classe_id` → `breed_id`  
   - Relation `classe()` → `breed()` : `return $this->belongsTo(Breed::class, 'breed_id')`  
   - DocBlock : `@property int|null $breed_id`, `@property-read Breed|null $breed`

2. **Spell** (`app/Models/Entity/Spell.php`)  
   - `use App\Models\Entity\Breed`  
   - Relation `classes()` → `breeds()` : `belongsToMany(Breed::class, 'breed_spell', 'spell_id', 'breed_id')`  
   - DocBlock : `@property-read ... $breeds`

3. **User** (`app/Models/User.php`)  
   - `use App\Models\Entity\Breed`  
   - Relation `createdClasses()` → `createdBreeds()` : `hasMany(Breed::class, 'created_by')`  
   - DocBlock : `@property-read ... $createdBreeds`

**Checkpoint Phase 2**

- [ ] Plus aucune référence à `Classe` dans les models
- [ ] `php artisan tinker` : `Breed::count()`, `Npc::with('breed')->first()`, `Spell::with('breeds')->first()` fonctionnent

---

## Phase 3 — Backend : Policies, Requests, Resources

**Objectif** : tout le HTTP stack utilise `Breed` et `breed` / `breeds`.

### 3.1 Policy

1. Créer `app/Policies/Entity/BreedPolicy.php` à partir de `ClassePolicy.php` :  
   - Model `Breed`, paramètre `$breed` dans les méthodes  
2. Enregistrer dans `AppServiceProvider` (ou `AuthServiceProvider`) : `Breed::class => BreedPolicy::class`  
3. Supprimer `app/Policies/Entity/ClassePolicy.php`

### 3.2 Requests

1. Créer `app/Http/Requests/Entity/StoreBreedRequest.php` (copie de `StoreClasseRequest`, adapter docblock)  
2. Créer `app/Http/Requests/Entity/UpdateBreedRequest.php` (idem depuis `UpdateClasseRequest`)  
3. Dans `StoreNpcRequest` et `UpdateNpcRequest` :  
   - Règle `classe_id` → `breed_id`, `exists:classes,id` → `exists:breeds,id`  
4. Supprimer `StoreClasseRequest.php` et `UpdateClasseRequest.php`

### 3.3 Resources

1. Créer `app/Http/Resources/Entity/BreedResource.php` à partir de `ClasseResource.php` (renommer classe et docblock)  
2. Supprimer `app/Http/Resources/Entity/ClasseResource.php`  
3. **SpellResource** : clé `classes` → `breeds`, variable `$classe` → `$breed` dans le map  
4. **NpcResource** : `classe_id` → `breed_id`, `classe` → `breed` (whenLoaded)

**Checkpoint Phase 3**

- [ ] Aucun use de `Classe`, `StoreClasseRequest`, `UpdateClasseRequest`, `ClasseResource`, `ClassePolicy` dans `app/Http`

---

## Phase 4 — Backend : Controllers et routes

**Objectif** : contrôleurs Breed, routes `entities/breeds` et `api/entities/breeds`.

### 4.1 Controllers Entity

1. Créer `app/Http/Controllers/Entity/BreedController.php` à partir de `ClasseController.php` :
   - Use `Breed`, `StoreBreedRequest`, `UpdateBreedRequest`, `BreedResource`
   - Route model binding : `Breed $breed`
   - Méthodes : `index` (query Breed, paginate, `BreedResource::collection`), `store`, `show($breed)`, `edit($breed)`, `update($breed)`, `delete($breed)`, `downloadPdf($breed)`
   - Inertia : `Inertia::render('Pages/entity/breed/Index', ['breeds' => ...])` (ou garder clé `breeds` pour cohérence)
   - PdfService : `generateForEntity($breed, 'breed')`, `generateForEntities(..., 'breed')`
2. Supprimer `app/Http/Controllers/Entity/ClasseController.php`

### 4.2 Controllers API

1. Créer `app/Http/Controllers/Api/BreedBulkController.php` à partir de `ClasseBulkController` :  
   - `Breed::class`, `exists:breeds,id`, `Breed::query()->whereIn('id', $ids)`  
2. Créer `app/Http/Controllers/Api/Table/BreedTableController.php` à partir de `ClasseTableController` :  
   - Même logique avec `Breed`, routes `entities.breeds.show`, `entityType` => `'breeds'`  
3. Supprimer `ClasseBulkController.php` et `ClasseTableController.php`  
4. **NpcController** (Entity) :  
   - `with(['creature', 'breed', 'specialization'])`, filtre `breed_id`, `request()->only([..., 'breed_id'])`  
5. **NpcTableController** (Api/Table) :  
   - `with(['creature', 'breed', 'specialization'])`, colonnes `breed_id`, `breed` (id, name)  
6. **NpcBulkController** :  
   - Validation `breed_id`, `exists:breeds,id`, fill `breed_id`  
7. **SpellController** :  
   - `with(..., 'breeds')`, `availableBreeds`, `updateBreeds` (ou garder nom de méthode et juste sync sur `breeds()`), validation `exists:breeds,id`  
8. **SpellController::updateClasses** : renommer en `updateBreeds` (ou garder route/méthode pour compatibilité temporaire et déléguer à la relation `breeds()`)

### 4.3 Routes

1. Renommer le fichier : `routes/entities/classe.php` → `routes/entities/breed.php`  
2. Dans `breed.php` :
   - Préfixe `entities/breeds`, noms `entities.breeds.*`
   - Paramètre `{breed}`, contrôleur `BreedController`
3. Dans `routes/web.php` :  
   - `require __DIR__ . '/entities/breed.php'` (au lieu de `classe.php`)  
4. Dans `routes/api.php` :
   - Routes bulk/table : segment `entities/classes` → `entities/breeds`, noms `api.entities.breeds.*`
   - Contrainte `entity` : ajouter `breed` si une route générique utilise `entity` (ex. `monster|breed|spell|...`)

**Checkpoint Phase 4**

- [ ] `php artisan route:list` affiche `entities/breeds`, `api/entities/breeds/*`
- [ ] Plus de routes `entities/classes` ni `api/entities/classes`
- [ ] Test manuel : ouvrir `/entities/breeds`, créer/éditer une breed, PDF

---

## Phase 5 — Backend : Config, factories, seeders, services

**Objectif** : config, données de test et services utilisent `breed` / `Breed`.

### 5.1 Config

- `config/entity-permissions.php` : clé `'classes'` → `'breeds'`, valeur `Breed::class`

### 5.2 Factories et seeders

1. Créer `database/factories/Entity/BreedFactory.php` à partir de `ClasseFactory` :  
   - `Breed::class`, `'breeds'` si besoin, constantes `Breed::STATE_*`  
2. Supprimer `ClasseFactory.php`  
3. **NpcFactory** : `classe_id` → `breed_id`  
4. Créer `database/seeders/Entity/BreedSeeder.php` (copie de `ClasseSeeder`, adapter si contenu)  
5. Supprimer `ClasseSeeder.php`  
6. Dans `DatabaseSeeder` (ou équivalent) : remplacer `ClasseSeeder` par `BreedSeeder`

### 5.3 Services

1. **PdfService** :  
   - Clés `'classe'` → `'breed'`, `'classes'` → `'breeds'` dans les tableaux d’eager load et de libellés  
   - Pour NPC : `'classe' => $entity->breed?->name` (et clé `breed` dans le payload)  
2. **Scrapping** :  
   - `app/Services/Scrapping/fields_config.php` : entrée `entity` / clés liées à l’entité classe → `breed`  
   - CollectAliasResolver, ConversionService, IntegrationService : alias/clés `classe` → `breed`, model `Breed`, table `breeds`  
   - DataCollectController, ScrappingController, ScrappingSearchController : références à l’entité → `breed` / `Breed`  
3. **IconsJsonGenerator** (si utilisé) :  
   - Décider : chemins `classes` / `classe_orientations` → `breeds` (et déplacer dossiers) ou garder pour assets ; documenter

**Checkpoint Phase 5**

- [ ] `php artisan db:seed` (ou seed partiel) fonctionne avec BreedFactory / BreedSeeder
- [ ] PdfService génère un PDF pour une breed sans erreur
- [ ] Scrapping (si testable) : import breed sans référence à `classes` / `Classe`

---

## Phase 6 — Frontend : entity key, registry, descriptors

**Objectif** : le front utilise partout l’entity key `breed` / `breeds`, avec libellés « Classe » / « Classes ».

### 6.1 Dossiers et fichiers à créer/renommer

1. Créer le dossier `resources/js/Entities/breed/`  
2. Créer `resources/js/Entities/breed/breed-descriptors.js` à partir de `classe/classe-descriptors.js` :
   - Exporter `getBreedFieldDescriptors`
   - `_tableConfig.entityType` → `'breed'`, id `breeds.index`
   - Placeholders / titres : garder « Classe » / « Classes » (ex. « Rechercher une classe… », `filename: "classes.csv"` si souhaité pour l’export)
3. Créer `resources/js/Models/Entity/Breed.js` à partir de `Classe.js` (nom de classe/module Breed)  
4. Supprimer `resources/js/Entities/classe/` (après avoir tout migré vers breed)  
5. Supprimer `resources/js/Models/Entity/Classe.js`

### 6.2 Entity registry et config

1. **entity-registry.js** :
   - Import `Breed`, `getBreedFieldDescriptors`
   - Dans `normalizeEntityType` : `'breed'` / `'breeds'` → `'breeds'`
   - Enregistrer l’entité `breeds` avec model Breed et descriptors getBreedFieldDescriptors
   - Remplacer toute référence à `classe`/`classes` par `breed`/`breeds` (ex. defaults minimalImportantFields pour NPC : `breed` au lieu de `classe`)
2. **entity-actions-config.js** :  
   - Clé `classes` → `breeds` (config d’actions par entité), libellés possibles gardés « Classe » si besoin  
3. **entityRouteRegistry.js** :  
   - Si entrée explicite pour `classes` : remplacer par `breeds` avec paramKey `breed` et noms de routes `entities.breeds.*`

**Checkpoint Phase 6 (partiel)**

- [ ] Build front : `pnpm run build` (ou `npm run build`) sans erreur
- [ ] Aucun import vers `classe-descriptors` ou `Classe.js` ailleurs que dans les fichiers en cours de migration

---

## Phase 7 — Frontend : pages et composants entity breed

**Objectif** : pages et vues « classe » deviennent « breed » (technique), affichage reste « Classe ».

### 7.1 Pages

1. Créer `resources/js/Pages/Pages/entity/breed/Index.vue` à partir de `classe/Index.vue` :
   - Entity type `breed` / `breeds`
   - Routes `entities.breeds.*`, paramètre `breed`
   - Props : utiliser `breeds` (aligné avec Inertia depuis BreedController)
2. Supprimer `resources/js/Pages/Pages/entity/classe/Index.vue` (après vérification)

### 7.2 Composants de vue (Molecules)

1. Créer `resources/js/Pages/Molecules/entity/breed/`  
2. Créer les composants à partir de ceux dans `classe/` :
   - `BreedViewLarge.vue`, `BreedViewCompact.vue`, `BreedViewMinimal.vue`, `BreedViewText.vue`
   - Dans chaque fichier : entity-type `breed`, permissions `breed`, routes `entities.breeds.*`, paramètre `breed`, props `breed` (objet)
   - Garder les libellés visibles « Classe » / « Classes » où c’est du texte utilisateur
3. Supprimer le dossier `resources/js/Pages/Molecules/entity/classe/`

### 7.3 Résolution de composant et layout

1. **resolveEntityViewComponent.js** :  
   - Mapping `classe` → composant Breed (Large/Compact/Minimal/Text), et ajouter `breed` → même chose  
2. **Aside.vue** (menu) :  
   - Entrée : key `breed`, route `entities.breeds.index`, **label** « Classes » (affichage)  
3. **CreateEntityModal.vue** :  
   - Mapping `classe: 'classe'` → `breed: 'breed'` (ou équivalent selon la structure)

**Checkpoint Phase 7**

- [ ] Page `/entities/breeds` s’affiche, liste et actions (voir, éditer, PDF) fonctionnent
- [ ] Menu latéral affiche « Classes » et pointe vers `entities/breeds`
- [ ] Vues Large/Compact/Minimal/Text pour une breed s’affichent correctement

---

## Phase 8 — Frontend : NPC, Spell et composants génériques

**Objectif** : NPC et Spell utilisent `breed` / `breeds` ; composants génériques (table, modals, etc.) reconnaissent l’entité `breed`.

### 8.1 NPC

1. **npc-descriptors.js** :  
   - Champ clé `classe` → `breed` (key et structure), label peut rester « Classe »  
2. **Npc.js** (model front) :  
   - Propriété / champ `classe` → `breed`  
3. **NpcViewLarge.vue, NpcViewCompact.vue, NpcViewMinimal.vue** :  
   - Champs importants / colonnes : `classe` → `breed` (données)

### 8.2 Spell

1. **spell/Edit.vue** :  
   - `itemLabel: 'classe'` → `itemLabel: 'breed'` (ou config qui affiche « Classe »)

### 8.3 Composants génériques

1. Parcourir les composants qui utilisent `entity-type` ou `entityType` avec valeur `classe` / `classes` :  
   - EntityModal, EntityQuickEdit*, EntityTanStackTable, EntityActions*, CreateEntityModal, etc.  
   - Remplacer par `breed` / `breeds` partout où c’est l’identifiant technique  
2. **TableConfig, FormConfig, BulkConfig, FormFieldConfig, TableColumnConfig** :  
   - Exemples ou listes d’entités : `classe` → `breed`  
3. **usePermissions, useDownloadPdf, useSectionUI, SectionStyleService, SectionParameterService, BaseMapper, Formatters, text-truncate, uiHelper** :  
   - Références explicites à l’entité `classe` → `breed`  
4. **atoms.index.json / molecules.index.json** :  
   - Entrées pour les vues « classe » → chemins et noms `breed/BreedView*`, name `breed`

**Checkpoint Phase 8**

- [ ] Fiche NPC avec « Classe » affichée utilise bien la relation `breed`
- [ ] Édition d’un sort : liaison aux breeds fonctionne, libellé « Classe » si conservé
- [ ] Table générique, modals, actions : entité breed reconnue partout

---

## Phase 9 — Scrapping frontend et derniers fichiers

**Objectif** : scrapping et autres écrans (admin, sections) utilisent `breed` en technique.

### 9.1 Scrapping

1. **useScrapping.js** :  
   - Clés d’entité `classe` → `breed`  
2. **ScrappingDashboard, ScrappingSection, ScrappingModal, SearchPreviewSection, EntityTypeSelector, HistorySection, ImportOptionsSection** :  
   - Entity type / options : `classe` → `breed`  
3. **collect_aliases.json** (ou équivalent) :  
   - Alias `classe` → `breed` si utilisé côté front ou partagé  
4. **resources/scrapping/config/sources/dofusdb/entities/breed.json** :  
   - Vérifier cohérence (entityFolder peut rester ou passer en `breeds` selon convention)

### 9.2 Admin / Characteristics

1. **resources/js/Pages/Admin/characteristics/Index.vue** :  
   - Si des références à l’entité « classe » (entity key, config) existent, les remplacer par `breed`  
   - Libellés utilisateur « Classe » conservés

### 9.3 Fichiers divers

1. **config/characteristics.php** :  
   - Références à l’entité (ex. `entities.class` / table) → `breed` / `breeds` selon la structure  
2. **docs/index** (atoms, molecules) :  
   - Mettre à jour les entrées pointant vers les anciens chemins `classe/ClasseView*` → `breed/BreedView*`

**Checkpoint Phase 9**

- [ ] Scrapping (recherche, import) : entité breed disponible, pas d’erreur console
- [ ] Admin characteristics : pas de référence cassée à l’entité classe

---

## Phase 10 — Tests

**Objectif** : tous les tests passent avec `Breed` et `breed` / `breeds`.

### 10.1 Tests PHP

1. Renommer / créer :
   - `tests/Feature/Api/Bulk/ClasseBulkControllerTest.php` → `BreedBulkControllerTest.php`  
   - Adapter : model Breed, routes `api.entities.breeds.*`, `exists:breeds,id`  
2. Tests Table :  
   - Références à Classe/classes → Breed/breeds, routes entities.breeds  
3. Tests Scrapping :  
   - Alias et clés `classe` → `breed`  
4. Tests NPC / Spell :  
   - `classe_id`, relation `classe` → `breed_id`, `breed`  
5. Policies :  
   - Enregistrer BreedPolicy, tests sur Breed si présents  
6. Supprimer les anciens tests qui ne font plus sens (ex. ClasseBulkControllerTest si renommé)

### 10.2 Tests JS

1. **classe-descriptors.test.js** → `breed/breed-descriptors.test.js` :  
   - getBreedFieldDescriptors, entityType `breeds`  
2. **classe-adapter.test.js** → `breed/breed-adapter.test.js` :  
   - entityType `breeds`  
3. **entity-registry.test.js** :  
   - `normalizeEntityType('breed')` → `'breeds'`  
4. **sectionWorkflow.test.js** (ou équivalent) :  
   - Entité « classe » → `breed` dans les cas de test  
5. Supprimer ou adapter tout test qui importe encore `classe-descriptors` ou `Classe.js`

**Checkpoint Phase 10**

- [ ] `php artisan test` (ou `./vendor/bin/phpunit`) : tous les tests PHP passent  
- [ ] `pnpm test` (ou `npm run test`) : tous les tests JS passent  

---

## Phase 11 — Documentation technique

**Objectif** : la doc technique parle de `breed` / `breeds` ; la doc métier (400 Règles) garde « classe ».

### 11.1 Fichiers à mettre à jour

1. **docs/docs.index.json** :  
   - Entrées « classe » / « classes » (entité) → `breed` / `breeds`  
2. **docs/20-Content/21-Entities/** :  
   - ENTITIES_OVERVIEW.md : table et section Classes → Breeds (affichés « Classe »), tables `breeds`, `breed_id`, `breed_spell`  
   - ENTITY_CLASSES.md → renommer en ENTITY_BREEDS.md, contenu technique en `breed` / `breeds`  
   - ENTITY_NPCS.md, ENTITY_SPELLS.md, ENTITY_SPECIALIZATIONS.md, ENTITY_CAPABILITIES.md, ENTITY_ATTRIBUTES.md : `classe_id` → `breed_id`, `classes` → `breeds`, `class_spell` → `breed_spell`  
3. **docs/10-BestPractices/SYNTAXE_FORMULES_CARACTERISTIQUES.md** :  
   - Références à l’entité (config, code) → `breed`  
4. **docs/20-Content/SCHEMA.md, PAGES_SECTIONS.md, SECTION_PARAMETERS.md, PAGES_SECTIONS_COMPOSABLES.md** :  
   - Exemples ou listes d’entités : `classe` → `breed`  
5. **docs/30-UI/** (TANSTACK_TABLE, AVATAR_SYSTEM, FRONTEND_MODELS, etc.) :  
   - Exemples « classe » → `breed`  
6. **docs/50-Fonctionnalités/Characteristics-DB/, Scrapping/, 110- To Do/** :  
   - Partout où l’entité est désignée techniquement → `breed`  
7. **Ne pas modifier** :  
   - `docs/400- Règles/*` (terme métier « classe » conservé)

**Checkpoint Phase 11**

- [ ] Recherche « classe » dans la doc technique (hors 400 Règles) ne renvoie que des usages métier ou libellés « Classe »  
- [ ] ENTITY_BREEDS.md existe et décrit l’entité avec tables/colonnes `breeds`, `breed_id`, `breed_spell`

---

## Phase 12 — Finalisation

**Objectif** : IDE, cache, redirects et recette manuelle.

### 12.1 IDE et helpers

1. Régénérer ou mettre à jour :  
   - `_ide_helper.php` / `_ide_helper_models.php` (si utilisés)  
   - `.phpstorm.meta.php` : clés `entities.classes` → `entities.breeds`, routes `api.entities.classes` → `api.entities.breeds`  
2. Supprimer ou vider le cache PHPStan si besoin :  
   - `storage/phpstan/` (ou relancer l’analyse)

### 12.2 Redirects (optionnel)

1. Si des URLs publiques ou des favoris pointent vers `/entities/classes/...` :  
   - Dans `routes/web.php` (ou un middleware), ajouter des redirects 301 :  
     - `Route::redirect('entities/classes', 'entities/breeds')`  
     - `Route::redirect('entities/classes/{id}', 'entities/breeds/{id}')` (et variantes show/edit)  
   - Documenter la décision (redirect permanent ou non)

### 12.3 Recette manuelle

- [ ] Menu : « Classes » → page liste breeds  
- [ ] CRUD breed : création, édition, suppression  
- [ ] PDF d’une breed et PDF multiple  
- [ ] Fiche NPC : « Classe » affichée, liaison vers une breed  
- [ ] Fiche Sort : liaison breeds, mise à jour  
- [ ] Table TanStack : tri, filtres, export CSV pour breeds  
- [ ] Permissions : viewAny, createAny, updateAny, deleteAny sur breeds  
- [ ] Scrapping : recherche / import breed (si applicable)  
- [ ] Admin characteristics : pas de régression  
- [ ] Anciennes URLs `/entities/classes/...` : redirect ou 404 selon choix  

### 12.4 Commit et revue

- [ ] Tout le code compilable et tests verts  
- [ ] Commit(s) clairs (ex. « refactor: rename entity classe to breed (backend) », « refactor: rename entity classe to breed (frontend) », « docs: update entity classe → breed »)  
- [ ] MR/PR avec lien vers ce plan et l’estimation  

---

## Récapitulatif des phases

| Phase | Contenu | Ordre |
|-------|---------|--------|
| 1 | BDD : migration rename | 1 |
| 2 | Backend : Models (Breed, Npc, Spell, User) | 2 |
| 3 | Backend : Policies, Requests, Resources | 3 |
| 4 | Backend : Controllers, routes | 4 |
| 5 | Backend : Config, factories, seeders, services | 5 |
| 6 | Frontend : entity key, registry, descriptors | 6 |
| 7 | Frontend : pages et vues breed | 7 |
| 8 | Frontend : NPC, Spell, composants génériques | 8 |
| 9 | Frontend : Scrapping, admin, divers | 9 |
| 10 | Tests PHP et JS | 10 |
| 11 | Documentation technique | 11 |
| 12 | Finalisation (IDE, redirects, recette) | 12 |

**Durée indicative** : 2 à 5 jours selon familiarité et volume de tests/doc. Chaque phase peut être un ou plusieurs commits ; les checkpoints permettent de valider avant de passer à la suivante.
