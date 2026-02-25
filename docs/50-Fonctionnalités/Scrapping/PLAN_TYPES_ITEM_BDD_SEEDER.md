# Plan : Types item (ressource / consommable / équipement) — BDD unique et seeder

**Objectif** : une seule source de vérité en base de données pour savoir si un `typeId` DofusDB est une ressource, un consommable ou un équipement ; permettre de modifier la catégorie d’un type dans les listes ; alimenter les registres via extraction (comme les caractéristiques) et seeders.

**Contexte** : aujourd’hui, DofusDB ne distingue pas métier : tout est « item ». Le projet utilise trois tables (`resource_types`, `consumable_types`, `item_types`) et un fichier de config (`item-super-types.json`) + catalogue API pour filtrer. Certains types sont encore mal classés (ressources prises pour équipements). Ce plan découpe le travail en trois phases.

---

## État des lieux

- **Tables** : `resource_types`, `consumable_types`, `item_types` (chacune : `dofusdb_type_id`, `name`, `decision`, `state`, etc.). Un même `dofusdb_type_id` ne doit figurer que dans **une seule** table.
- **Résolution actuelle** : `IntegrationService::resolveItemTargetTable($typeId)` consulte les trois tables (ResourceType, ConsumableType, ItemType) puis fallback `ITEM_TYPE_TO_TABLE` (config codée).
- **Filtrage des listes** : `ResourceTypeRegistryController::index()` utilise `getTypeIdsForGroup('resource')` qui s’appuie sur **config** (`item-super-types.json`) + **catalogue DofusDB** (`/item-types`), pas uniquement la BDD.
- **Filtres de recherche** : `ItemEntityTypeFilterService` utilise en priorité les registres BDD (`getAllowedTypeIdsFromRegistry`), avec repli sur `getTypeIdsForGroup()` (config + catalogue) quand un registre est vide.
- **Caractéristiques (référence)** : données en BDD, seeders depuis `database/seeders/data/*.php`, commande `db:export-seeder-data` pour réexporter la BDD vers ces fichiers, et extraction (ex. `characteristics:extract-object-samples`) pour alimenter des samples depuis DofusDB.

---

## Phase 1 — Rendre les types changeables (ressource / consommable / équipement)

**But** : depuis les listes « Gérer les types » (Ressource, Consommable, Équipement), pouvoir **changer la catégorie** d’un type (ex. passer un type de « ressource » à « consommable » ou « équipement »).

### 1.1 Contraintes métier

- Un `dofusdb_type_id` ne doit exister que dans **une seule** des trois tables. Changer de catégorie = **supprimer** (ou retirer) de la table source et **créer** (ou réattribuer) dans la table cible.
- Conserver `name`, `decision`, `seen_count`, `last_seen_at` si possible (ou réinitialiser selon la politique choisie).

### 1.2 Backend

1. **API « changer catégorie »**  
   - Nouvel endpoint (ex. `POST /api/scrapping/resource-types/{id}/move` ou `PATCH .../category` avec body `{ "target": "consumable" }`).  
   - Rôle : lire l’enregistrement source (ex. `ResourceType`), créer un enregistrement dans la table cible (ex. `ConsumableType`) avec le même `dofusdb_type_id` (et nom, etc.), supprimer (ou soft-delete) l’enregistrement source.  
   - Vérifier l’unicité de `dofusdb_type_id` côté cible avant création ; gérer les conflits (déjà présent dans la cible, etc.).

2. **Autorisations**  
   - Réutiliser les policies existantes sur les types (resource-types, consumable-types, item-types) pour autoriser cette action (ex. `update` ou droit dédié « changer catégorie »).

### 1.3 Frontend

1. **Listes « Gérer les types »**  
   - Dans chaque liste (Ressource, Consommable, Équipement), ajouter une action par ligne (ou menu) : **« Changer de catégorie »** / **« Déplacer vers… »** avec choix : Ressource / Consommable / Équipement (en excluant la catégorie courante).

2. **UX**  
   - Confirmation avant déplacement (effet : le type disparaît de la liste actuelle et apparaît dans l’autre).  
   - Rafraîchir la liste après succès (déjà partiellement en place avec `refreshTrigger`).  
   - Afficher un message de succès / erreur (ex. « Type déplacé vers Consommables »).

### 1.4 Points d’attention

- Si des entités (resources, consumables, items) référencent déjà ce type, le déplacer peut rendre des FK orphelines ou incohérentes. À décider : interdire le déplacement si des entités utilisent ce type, ou migrer les entités (complexe). Pour la phase 1, on peut **interdire le déplacement** si au moins une entité utilise ce type.
- Cache : invalider le cache lié aux types (ex. `scrapping_allowed_type_ids_*`) après un déplacement.

---

## Phase 2 — Une seule source de vérité : la base de données

**But** : que **toute** la logique qui décide si un `typeId` est ressource / consommable / équipement s’appuie **uniquement** sur les trois tables (et éventuellement les données issues des seeders), sans dépendre de `item-super-types.json` ni du catalogue DofusDB pour cette décision.

### 2.1 Fichiers / services à auditer

| Fichier / service | Usage actuel | Action |
|-------------------|--------------|--------|
| `IntegrationService::resolveItemTargetTable()` | BDD (3 tables) puis `ITEM_TYPE_TO_TABLE` | Supprimer le fallback config (ou le remplacer par « inconnu → équipement » ou par une table BDD de dernier recours). |
| `IntegrationService::resolveItemEntityType()` | Déjà basé sur `resolveItemTargetTable` (BDD) | Rien si Phase 2.1 fait. |
| `ItemEntityTypeFilterService::getTypeIdsForGroup()` | Config + catalogue DofusDB | **Ne plus l’utiliser** pour « quel typeId appartient à quel groupe ». À la place : pour « resource » retourner les `dofusdb_type_id` de `resource_types` ; idem consumable_types, item_types. Renommer si besoin (ex. `getTypeIdsForEntityFromRegistry()`). |
| `ItemEntityTypeFilterService::defaultResourceFilters()` (et consumable, equipment) | `getAllowedTypeIdsFromRegistry` puis `getTypeIdsForGroup` en fallback | Fallback : si le registre est vide, ne pas filtrer par type (ou utiliser une liste vide / « tous les types connus en BDD » selon le comportement voulu). Plus de lecture de `item-super-types.json` pour ce choix. |
| `ResourceTypeRegistryController::index()` | `getTypeIdsForGroup('resource')` pour filtrer les lignes affichées | Afficher **tous** les `ResourceType` (sans filtre par config). La liste = contenu de la table `resource_types`. Idem pour consumable_types et item_types. |
| `ConsumableTypeRegistryController`, `ItemTypeRegistryController` (ou équivalents) | Idem (filtre par groupe config) | Même principe : liste = contenu de la table. |
| `DofusDbItemSuperTypeMappingService` / `item-super-types.json` | Utilisés par `getTypeIdsForGroup` et extraction object samples | Garder **uniquement** pour l’extraction / l’import initial (Phase 3) ou pour des besoins annexes (stats, rapport). Ne plus s’en servir pour « ce typeId est-il ressource ? » en runtime. |
| `ExtractObjectConversionSamplesCommand` | Lit `item-super-types.json` pour filtrer par superType | Optionnel : faire dépendre la liste des types depuis la BDD (ex. types « equipment » = `ItemType::pluck('dofusdb_type_id')`) au lieu du JSON. |

### 2.2 Comportement cible

- **Résolution typeId → catégorie** : uniquement en interrogeant les trois tables (ordre : resource_types, consumable_types, item_types, puis défaut « equipment » ou « non trouvé » selon choix métier).
- **Listes « Gérer les types »** : contenu intégral de chaque table (sans filtre dérivé de la config).
- **Recherche / filtres** : `getAllowedTypeIdsFromRegistry()` (déjà BDD) ; si registre vide, pas de filtre par type (ou comportement explicite documenté), sans appel au catalogue ou au JSON.

### 2.3 Migration / données

- Si aujourd’hui les tables sont vides ou partielles, la **Phase 3** (extraction + seeder) permettra de les remplir. En Phase 2, on se contente de faire en sorte que le code ne dépende plus de la config pour la catégorie.

---

## Phase 3 — Extraction des types et alimentation des seeders

**But** : comme pour les caractéristiques, avoir une **extraction** des types depuis DofusDB et des **fichiers de données** (seeders) pour initialiser / mettre à jour les trois tables, avec possibilité d’exporter la BDD vers ces fichiers.

### 3.1 Extraction depuis DofusDB

1. **Source** : endpoint DofusDB **`/item-types`** (déjà utilisé par `DofusDbItemTypesCatalogService`). Chaque type a un `id`, `superTypeId`, `name`, etc.

2. **Commande Artisan** (ex. `php artisan scrapping:extract-item-types`) :
   - Récupérer tous les item-types (paginer si besoin).
   - Pour chaque type : selon la **règle de catégorie** choisie :
     - **Option A** : utiliser encore une fois la config `item-super-types.json` pour attribuer resource / consumable / equipment (uniquement pour l’extraction, pas pour le runtime après Phase 2).
     - **Option B** : tout insérer dans une table « neutre » (ex. `item_types_dofusdb`) avec `dofusdb_type_id`, `super_type_id`, `name`, puis un script / seeder qui remplit les trois tables à partir de cette table + règles (fichier ou BDD).
   - Écrire le résultat dans un fichier de données (ex. `database/seeders/data/item_types_dofusdb.php` ou trois fichiers `resource_types.php`, `consumable_types.php`, `item_types.php`) au format attendu par les seeders.

3. **Intégration optionnelle** : possibilité d’appeler cette commande depuis un job ou un bouton admin pour « rafraîchir les types depuis DofusDB » sans toucher manuellement aux seeders.

### 3.2 Seeders

1. **Fichiers de données** (dans `database/seeders/data/`) :
   - `resource_types.php` : tableau de types à insérer en tant que ressources (ex. `dofusdb_type_id`, `name`, `decision` par défaut).
   - `consumable_types.php` : idem pour consommables.
   - `item_types.php` (équipements) : idem pour équipements.

   Format proposé (aligné caractéristiques) : une entrée par type avec au minimum `dofusdb_type_id`, `name`, et éventuellement `decision`, `state`.

2. **Seeders dédiés** (ou extension des existants) :
   - `ResourceTypeSeeder`, `ConsumableTypeSeeder`, `ItemTypeSeeder` (ou un seul `ItemTypeCategorySeeder` qui lit les trois fichiers et remplit les trois tables).
   - Logique : `updateOrCreate` sur `dofusdb_type_id` pour chaque table, sans écraser une catégorie déjà choisie en BDD si on souhaite préserver les changements faits en Phase 1 (sinon, écraser depuis le fichier).

3. **Commande d’export** (comme `db:export-seeder-data`) :
   - Option `--item-types` (ou `--resource-types`, `--consumable-types`, `--item-types`) pour exporter le contenu actuel des trois tables vers `database/seeders/data/resource_types.php` (et idem pour les deux autres).
   - Permet de « figer » en seed les changements faits en UI (dont les changements de catégorie de la Phase 1).

### 3.3 Workflow cible (implémenté)

1. **Setup initial** : `php artisan db:seed` (ou seeders types) → tables remplies depuis les fichiers PHP.
2. **Remplir depuis l’API** : `php artisan scrapping:seed-item-types` → récupère tout le catalogue DofusDB, écrit les 3 fichiers puis exécute les seeders (une commande, rien n’est oublié).
3. **Mise à jour fichiers seuls** : `php artisan scrapping:extract-item-types` → régénère uniquement les trois fichiers.
4. **Ajustements manuels** : via l’UI « Gérer les types » (décision, changement de catégorie).
5. **Sauvegarde en seed** : `php artisan db:export-seeder-data --item-types` → les fichiers de data reflètent la BDD.

**Remplir en une commande** : `php artisan scrapping:seed-item-types` récupère tout le catalogue via l’API DofusDB (superTypeId → Ressource / Consommable / Équipement), écrit les 3 fichiers puis exécute les seeders. Option `--no-files` pour n’exécuter que les seeders. Implémentation : `scrapping:extract-item-types`, `scrapping:seed-item-types`, `getCategoryForSuperTypeId()`, seeders `LoadsSeederDataFile` + `updateOrCreate`, export `--item-types`.

---

## Ordre de réalisation recommandé

1. **Phase 1** : Changer la catégorie d’un type (backend API + frontend listes). Pas de changement de source de vérité.
2. **Phase 2** : Audit et modification du code pour ne plus utiliser config/catalogue pour « typeId → ressource/consommable/équipement » ; uniquement BDD.
3. **Phase 3** : Commande d’extraction, fichiers de données, seeders et option d’export dans `db:export-seeder-data`.

---

## Références

- [Architecture/ITEM_TYPES_REFERENCE.md](./Architecture/ITEM_TYPES_REFERENCE.md) — Référence item-types DofusDB.
- [Architecture/RELATIONS.md](./Architecture/RELATIONS.md) — Relations (recettes, drops).
- [docs/50-Fonctionnalités/Characteristics-DB/EXTRACTION_SAMPLES_DOFUS_KROSMOZ.md](../Characteristics-DB/EXTRACTION_SAMPLES_DOFUS_KROSMOZ.md) — Extraction des samples (référence pour l’extraction des types).
- [database/seeders/data/README.md](../../../database/seeders/data/README.md) — Workflow seeders et export.
