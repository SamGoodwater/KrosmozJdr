# Plan de migration : caractéristiques en base de données

**Objectif** : Remplacer `config/characteristics.php` et `config/equipment_characteristics.php` par une source de vérité en base de données, exposée via un **service** accessible partout dans le projet, avec une interface admin (super_admin) pour modifier les données.

**Références** : `config/characteristics.php`, `config/equipment_characteristics.php`, `docs/10-BestPractices/SYNTAXE_FORMULES_CARACTERISTIQUES.md`.

---

## 1. Périmètre et objectifs

### 1.1 Données à migrer

| Source actuelle | Contenu | Usage actuel |
|-----------------|---------|--------------|
| `config/characteristics.php` | `characteristics` (tableau id => définition), `competences` (sous-ensemble is_competence) | ValidationService, DofusDbConversionFormulas, DataConversionService |
| `config/equipment_characteristics.php` | `slots` (weapon, hat, cape, …) avec pour chaque slot : `characteristics` (id => bracket_max, forgemagie_max) | Référencé en doc ; pas encore consommé en PHP (à prévoir) |

### 1.2 Contrat du service (API cible)

Le service doit exposer la **même structure** que la config actuelle pour ne pas casser les consommateurs :

- **CharacteristicService::getCharacteristics()** → équivalent de `config('characteristics.characteristics')` : tableau `id => [ name, short_name, type, entities => [ monster => [...], class => [...], item => [...] ], ... ]`.
- **CharacteristicService::getCompetences()** → équivalent de `config('characteristics.competences')` : même structure que les entrées avec `is_competence === true`.
- **CharacteristicService::getFullConfig()** → équivalent de `config('characteristics')` : `['characteristics' => [...], 'competences' => [...]]`.
- **EquipmentCharacteristicService::getSlots()** (ou intégré au même service) → équivalent de `config('equipment_characteristics.slots')` : `slot_id => [ name, characteristics => [ char_id => [ bracket_max => [], forgemagie_max, base_price_per_unit?, rune_price_per_unit? ] ] ]`.

### 1.3 Consommateurs à basculer

| Fichier | Utilisation actuelle | Action |
|---------|----------------------|--------|
| `app/Services/Scrapping/V2/Validation/ValidationService.php` | `Config::get('characteristics.characteristics', [])` | Injecter `CharacteristicService`, utiliser `$this->characteristicService->getCharacteristics()` |
| `app/Services/Scrapping/V2/Conversion/DofusDbConversionFormulas.php` | `Config::get('characteristics.characteristics', [])` (clamp) | Idem : passer par le service |
| `app/Services/Scrapping/DataConversion/DataConversionService.php` | `config('characteristics', [])` | Utiliser `CharacteristicService::getFullConfig()` ou équivalent |
| `config/dofusdb_conversion.php` | Commentaire « characteristics » | Documenter que la source est le service (ou config déléguée au service) |
| Tout futur code (formules, équipements, admin) | — | Utiliser uniquement le service |

---

## 2. Schéma de base de données

### 2.1 Tables principales

#### 2.1.1 `characteristics`

Définition globale d’une caractéristique (id = clé métier, ex. `life`, `mod_strength`, `athletisme`).

| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| `id` | string (PK) | non | Identifiant métier (ex. life, strength, res_neutre, athletisme) |
| `db_column` | string | oui | Nom de colonne BDD si différent de id (ex. strong pour strength) |
| `name` | string | non | Nom affiché |
| `short_name` | string | oui | Nom abrégé |
| `description` | text | oui | Description |
| `type` | string | non | int, string, array |
| `unit` | string | oui | Unité (PV, PA, points, …) |
| `icon` | string | oui | Icône (clé ou chemin) |
| `color` | string | oui | Couleur (neutral, red, …) |
| `sort_order` | int | oui | Ordre d’affichage (default 0) |
| `forgemagie_allowed` | boolean | non | Forgemagie autorisée (default false) |
| `forgemagie_max` | int | non | Bonus max forgemagie (default 0) |
| `applies_to` | json | oui | Liste d’entités : ["monster","class","item"] |
| `is_competence` | boolean | non | Compétence (default false) |
| `characteristic_id` | string (FK) | oui | Pour compétence : id de la caractéristique principale (ex. strength) |
| `alternative_characteristic_id` | string (FK) | oui | Pour compétence : carac alternative |
| `skill_type` | string | oui | physique, mental, social, technique |
| `value_available` | json | oui | Pour type array : liste de valeurs autorisées |
| `labels` | json | oui | Pour type array : libellés par valeur |
| `validation` | json | oui | Règles de validation globales (ex. max 255 pour string) |
| `mastery_value_available` | json | oui | Pour compétence : [0,1,2] |
| `mastery_labels` | json | oui | Pour compétence : {0: "Aucune", 1: "Maîtrisé", 2: "Expertise"} |
| `base_price_per_unit` | decimal(12,2) | oui | Prix par unité de caractéristique (base), ex. kamas par point de bonus (valeur par défaut si non renseigné au niveau slot) |
| `rune_price_per_unit` | decimal(12,2) | oui | Prix par unité de rune (forgemagie), ex. kamas par point de forgemagie (valeur par défaut si non renseigné au niveau slot) |
| `created_at` | timestamp | oui | |
| `updated_at` | timestamp | oui | |

Contraintes : `characteristic_id` et `alternative_characteristic_id` référencent `characteristics.id`.

#### 2.1.2 `characteristic_entities`

Valeurs et règles **par entité** (monster, class, item) pour une caractéristique.

| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| `id` | bigint (PK, auto) | non | |
| `characteristic_id` | string (FK) | non | Référence characteristics.id |
| `entity` | string | non | monster | class | item |
| `min` | int | oui | Borne min (type int) |
| `max` | int | oui | Borne max (type int) |
| `formula` | string (text) | oui | Formule exploitable (sans équipement/forgemagie) |
| `formula_display` | string (text) | oui | Formule affichage |
| `default_value` | string | oui | Valeur par défaut (stockée en string, interprétée selon type) |
| `required` | boolean | non | Champ requis (default false) |
| `validation_message` | string (text) | oui | Message d’erreur de validation |
| `created_at` | timestamp | oui | |
| `updated_at` | timestamp | oui | |

Contrainte unique : `(characteristic_id, entity)`.

#### 2.1.3 `equipment_slots`

Slots d’équipement (arme, chapeau, cape, …).

| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| `id` | string (PK) | non | weapon, hat, cape, amulet, boots, ring, belt, shield |
| `name` | string | non | Nom affiché |
| `sort_order` | int | oui | Ordre d’affichage |
| `created_at` | timestamp | oui | |
| `updated_at` | timestamp | oui | |

#### 2.1.4 `equipment_slot_characteristics`

Pour chaque slot, quelles caractéristiques il peut donner et avec quelles limites.

| Colonne | Type | Nullable | Description |
|---------|------|----------|-------------|
| `id` | bigint (PK, auto) | non | |
| `equipment_slot_id` | string (FK) | non | Référence equipment_slots.id |
| `characteristic_id` | string (FK) | non | Référence characteristics.id |
| `bracket_max` | json | non | Tableau de 10 entiers (paliers niveau 1–2, 3–4, …, 19–20) |
| `forgemagie_max` | int | oui | Bonus max forgemagie pour ce slot/carac (null = non forgemageable) |
| `base_price_per_unit` | decimal(12,2) | oui | Prix par unité de caractéristique (base) pour ce slot/carac, ex. kamas par point de bonus (override ou complément à la valeur sur characteristics) |
| `rune_price_per_unit` | decimal(12,2) | oui | Prix par unité de rune (forgemagie) pour ce slot/carac, ex. kamas par point de rune (override ou complément à la valeur sur characteristics) |
| `created_at` | timestamp | oui | |
| `updated_at` | timestamp | oui | |

Contrainte unique : `(equipment_slot_id, characteristic_id)`.

### 2.2 Index et performances

- `characteristics` : index sur `sort_order`, `is_competence`.
- `characteristic_entities` : index sur `characteristic_id`, index composite `(characteristic_id, entity)`.
- `equipment_slot_characteristics` : index sur `equipment_slot_id`, `characteristic_id`.

### 2.3 Prix par unité (base et rune)

- **Sur `characteristics`** : `base_price_per_unit` et `rune_price_per_unit` servent de **valeurs par défaut** (ex. kamas par point de bonus, kamas par point de rune). Utiles pour les caractéristiques sans slot (affichage, référence) ou comme fallback.
- **Sur `equipment_slot_characteristics`** : `base_price_per_unit` et `rune_price_per_unit` définissent le **prix par unité pour ce slot et cette caractéristique** (base = bonus équipement, rune = forgemagie). Si renseignés, ils priment sur les valeurs de la caractéristique ; sinon, on peut utiliser celles de la caractéristique.
- Unité : typiquement **kamas** ; le champ est numérique (decimal) pour permettre d’autres devises ou facteurs plus tard.

### 2.4 Remarques

- Les modificateurs (`mod_strength`, …) sont des lignes comme les autres dans `characteristics` avec `formula` dans `characteristic_entities` (ex. `floor(([strength]-10)/2)`).
- Pas de table « base » (RES_BASE, DO_FIXE_BASE) : chaque caractéristique est complète ; la répétition est en base (une ligne par res_*, do_fixe_*, etc.).
- `competences` en sortie du service = filtre `is_competence = true` sur `characteristics` + jointure avec `characteristic_entities`, rien de plus.

---

## 3. Service applicatif

### 3.1 CharacteristicService (principal)

- **Namespace** : `App\Services\Characteristic\CharacteristicService` (ou `App\Services\Characteristics\CharacteristicService`).
- **Responsabilités** :
  - Charger et mettre en cache la structure « characteristics » (tableau id => définition complète avec `entities`).
  - Exposer : `getCharacteristics()`, `getCompetences()`, `getFullConfig()`, et si besoin `getCharacteristic(string $id): ?array`, `getLimits(string $id, string $entity): ?array`.
- **Cache** : Cache Laravel (ex. `Cache::remember('characteristics.full', 3600, fn () => $this->buildFullConfig())`). Invalidation à chaque création/update/suppression (observer ou appel explicite dans le repo).
- **Format de sortie** : Identique à `config('characteristics.characteristics')` et `config('characteristics')` pour ne pas modifier ValidationService, DofusDbConversionFormulas, DataConversionService (sauf passage par le service).

### 3.2 EquipmentCharacteristicService (équipements)

- **Namespace** : `App\Services\Characteristic\EquipmentCharacteristicService` (ou dans le même module).
- **Responsabilités** : Charger/cache des slots et des caractéristiques par slot ; exposer `getSlots()` au format `config('equipment_characteristics.slots')`.
- **Cache** : Même principe (clé dédiée, invalidation à l’édition).

### 3.3 Enregistrement et injection

- Enregistrer les services dans `AppServiceProvider` ou un `CharacteristicServiceProvider` (binding singleton).
- ValidationService, DofusDbConversionFormulas, DataConversionService : injection du `CharacteristicService` (et si besoin `EquipmentCharacteristicService`) ; plus d’appel direct à `Config::get('characteristics.*')`.

### 3.4 Compatibilité config (optionnel)

- Pour une transition en douceur : une config `config/characteristics.php` qui délègue au service (ex. `return app(CharacteristicService::class)->getFullConfig();`). Ainsi tout code qui fait encore `config('characteristics')` continue de fonctionner jusqu’à ce qu’il soit migré vers le service.

---

## 4. Migrations et seeders

### 4.1 Migrations

1. **create_characteristics_table** : table `characteristics` (colonnes ci-dessus).
2. **create_characteristic_entities_table** : table `characteristic_entities` + FK vers `characteristics`.
3. **create_equipment_slots_table** : table `equipment_slots`.
4. **create_equipment_slot_characteristics_table** : table `equipment_slot_characteristics` + FK vers `equipment_slots` et `characteristics`.

### 4.2 Seeders / import depuis la config actuelle

- **CharacteristicConfigSeeder** (ou script unique) :
  - Lire `config/characteristics.php` (en chargeant l’app pour avoir les tableaux PHP).
  - Pour chaque entrée de `characteristics.characteristics` : insérer ou mettre à jour `characteristics` (y compris **base_price_per_unit**, **rune_price_per_unit** si présents dans la config ou à null par défaut) + pour chaque entité dans `entities` insérer ou mettre à jour `characteristic_entities`.
  - Gérer les clés JSON (`applies_to`, `value_available`, `labels`, `mastery_value_available`, `mastery_labels`, `validation`, `forgemagie` → forgemagie_allowed, forgemagie_max).
- **EquipmentCharacteristicConfigSeeder** :
  - Lire `config/equipment_characteristics.php` ; pour chaque slot et chaque caractéristique du slot : insérer ou mettre à jour `equipment_slots` et `equipment_slot_characteristics` (bracket_max, forgemagie_max, **base_price_per_unit**, **rune_price_per_unit** — à ajouter dans la config actuelle si on veut les seedér, sinon null).

Après migration, conserver les anciens fichiers de config en lecture pour le seeder ; en prod, la source de vérité sera la base.

---

## 5. Interface admin (super_admin)

### 5.1 Droits

- Route(s) et contrôleur(s) réservés aux utilisateurs ayant un rôle **super_admin** (middleware ou policy).
- Vérifier que le projet a déjà une notion de super_admin (table `users`, roles, permissions) et l’utiliser.

### 5.2 Pages / fonctionnalités

1. **Liste des caractéristiques**
   - Tableau (ou grille) : id, name, short_name, type, applies_to, order.
   - Filtres : type, is_competence, appliqué à (monster/class/item).
   - Actions : créer, éditer, supprimer (avec prudence : vérifier les références).

2. **Édition d’une caractéristique**
   - Formulaire : champs globaux (id, db_column, name, short_name, description, type, unit, icon, color, sort_order, forgemagie_allowed, forgemagie_max, applies_to, is_competence, characteristic_id, alternative_characteristic_id, skill_type, value_available, labels, validation, mastery_*, **base_price_per_unit**, **rune_price_per_unit**).
   - Bloc par entité (monster, class, item) : min, max, formula, formula_display, default_value, required, validation_message.
   - Aide à la saisie : rappel de la syntaxe des formules ([id], [mod_*], ndX, floor, etc.) + lien vers la doc.

3. **Validation des formules (optionnel mais recommandé)**
   - À la sauvegarde : parser la formule (syntaxe [id], ndX, floor, etc.) et vérifier que les références [id] pointent vers des characteristics existantes.
   - Affichage des erreurs dans l’UI (ex. « [vitalty] inconnu : did you mean [vitality]? »).

4. **Graphe de dépendances (optionnel)**
   - Page ou panneau : nœuds = caractéristiques, arêtes = « utilise » (d’après les formules : extraction des [id] et [mod_*]).
   - Lib : simple (liste des dépendances par carac) ou graphe (ex. D3, Cytoscape.js, ou Mermaid) pour visualiser les liens (life → vitality, level ; mod_strength → strength ; etc.).

5. **Équipements par slot**
   - Liste des slots ; pour chaque slot : liste des caractéristiques associées avec bracket_max (tableau 10 valeurs), forgemagie_max, **base_price_per_unit**, **rune_price_per_unit**.
   - Édition : choix des caractéristiques, saisie des 10 valeurs bracket_max, forgemagie_max, prix par unité (base et rune).

### 5.3 Stack technique suggérée

- Backend : Laravel (controllers, Form Requests pour la validation).
- Frontend : Vue 3 (pages Inertia ou SPA) avec composants existants du projet (Atomic Design).
- Formulaires : champs texte pour formules ; champs nombre pour min/max/default ; select ou multi-select pour applies_to, value_available ; JSON éditable (ou sous-formulaires) pour bracket_max, labels, etc.

---

## 6. Points d’attention (ne rien oublier)

### 6.1 Données et cohérence

- **Références circulaires** : les formules ne doivent pas créer de cycle (ex. A utilise B, B utilise A). Validation à l’édition ou warning en admin.
- **Suppression** : interdire (ou soft-delete) la suppression d’une caractéristique utilisée dans une formule ([id] ou [mod_*]) ou dans equipment_slot_characteristics.
- **Id métier** : `characteristics.id` = string (life, mod_strength, …). Pas d’auto-increment pour l’id métier ; choix par l’admin à la création.

### 6.2 Cache et déploiement

- Invalider le cache des characteristics (et equipment) à chaque modification en admin (observer sur les models ou appel dans les controllers).
- En déploiement : pas de problème si la config PHP n’est plus utilisée ; la base et les seeders suffisent. Option : conserver un export JSON/YAML de la config généré depuis la base pour backup ou versioning.

### 6.3 Tests

- **Unit** : CharacteristicService : avec une base en mémoire (SQLite) ou des repos mockés, vérifier que `getCharacteristics()` et `getCompetences()` retournent la même structure que la config actuelle pour un jeu de données connu.
- **Feature** : ValidationService et DofusDbConversionFormulas : s’assurer que les données lues via le service donnent les mêmes résultats qu’avec la config (snapshot ou jeux de données communs).
- **Seeder** : après avoir seedé depuis la config actuelle, comparer (snapshot ou assertions) la sortie du service avec `config('characteristics')` pour détecter les écarts.

### 6.4 Documentation et évolutions

- Mettre à jour :
  - `docs/10-BestPractices/SYNTAXE_FORMULES_CARACTERISTIQUES.md` : préciser que la source de vérité est la base et le service.
  - `docs/50-Fonctionnalités/Scrapping/Refonte/` et `config/dofusdb_conversion.php` : indiquer que les limites et définitions viennent du CharacteristicService.
- **I18n** : si plus tard les libellés (name, short_name, description) doivent être traduits, prévoir une table `characteristic_translations` (characteristic_id, locale, name, short_name, description) et adapter le service pour retourner la locale courante.

### 6.5 Récap des fichiers à créer ou modifier

| Fichier / élément | Action |
|-------------------|--------|
| Migrations (4) | Créer characteristics, characteristic_entities, equipment_slots, equipment_slot_characteristics |
| Models | Characteristic, CharacteristicEntity, EquipmentSlot, EquipmentSlotCharacteristic |
| CharacteristicService | Créer + cache + getCharacteristics, getCompetences, getFullConfig (incl. base_price_per_unit, rune_price_per_unit dans chaque carac) |
| EquipmentCharacteristicService | Créer + cache + getSlots (incl. base_price_per_unit, rune_price_per_unit par slot/carac) |
| CharacteristicServiceProvider (ou AppServiceProvider) | Enregistrer les services |
| ValidationService | Injecter CharacteristicService, remplacer Config::get |
| DofusDbConversionFormulas | Injecter CharacteristicService, remplacer Config::get |
| DataConversionService | Injecter CharacteristicService, remplacer config('characteristics') |
| Seeders | CharacteristicConfigSeeder, EquipmentCharacteristicConfigSeeder |
| Routes admin | Groupe super_admin : characteristics CRUD, equipment-slots CRUD |
| Controllers admin | CharacteristicController, EquipmentSlotController (ou un seul) |
| Form Requests | StoreCharacteristicRequest, UpdateCharacteristicRequest (validation formula, min/max) |
| Pages Vue (admin) | Liste caractéristiques, édition caractéristique, liste slots, édition slot |
| Optional | Page ou composant « graphe de dépendances » |
| config/characteristics.php | Optionnel : délégation au service pour compatibilité |
| Docs | Mise à jour SYNTAXE_FORMULES, dofusdb_conversion, README du module |

---

## 7. Ordre de réalisation suggéré

1. **Migrations + models** : tables, relations, accessors pour JSON.
2. **Seeders** : import depuis la config actuelle ; vérifier en local que les données sont complètes.
3. **CharacteristicService** : construction de la structure en mémoire depuis la base, cache, API getCharacteristics / getCompetences / getFullConfig.
4. **EquipmentCharacteristicService** : idem pour getSlots.
5. **Injection et remplacement** : ValidationService, DofusDbConversionFormulas, DataConversionService ; tests existants (ou ajout de tests) pour vérifier non-régression.
6. **Interface admin** : routes, middleware super_admin, CRUD caractéristiques (liste + édition), puis CRUD équipements par slot.
7. **Validation des formules** (optionnel) : parser et vérifier les références en édition.
8. **Graphe de dépendances** (optionnel) : extraction des [id] / [mod_*] et affichage.
9. **Documentation** : mise à jour des docs et, si besoin, délégation dans config pour compatibilité.

---

## 8. Résumé

- **Base** : 4 tables (characteristics, characteristic_entities, equipment_slots, equipment_slot_characteristics).
- **Service** : CharacteristicService + EquipmentCharacteristicService, cache, API identique à la config actuelle.
- **Consommateurs** : ValidationService, DofusDbConversionFormulas, DataConversionService basculés sur le service.
- **Admin** : super_admin uniquement ; CRUD caractéristiques et équipements par slot ; optionnel : validation de formules, graphe de dépendances.
- **Rien d’oublié** : seeders depuis la config, invalidation du cache, tests, docs, et attention aux références (suppression, cycles, id métier).

Ce plan peut servir de base pour des tickets ou une implémentation par étapes.
