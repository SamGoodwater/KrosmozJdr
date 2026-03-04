# Analyse des zones à risque de nommage (propriétés)

**Objectif** : Repérer tous les endroits du projet où des **noms de propriétés** sont définis ou utilisés, afin d’uniformiser les conventions et d’éviter les erreurs de mapping liées au naming.

**Conventions de référence** : [NAMING_CONVENTIONS.md](../../10-BestPractices/NAMING_CONVENTIONS.md) — Laravel : `snake_case` pour variables/propriétés ; Vue : `camelCase` pour props.

---

## 1. Zones identifiées (inventaire)

### 1.1 Base de données et migrations

| Fichier / zone | Ce qui est nommé | Risque |
|----------------|------------------|--------|
| **Migrations** (`database/migrations/`) | Colonnes des tables : `characteristics.key`, `characteristic_creature.db_column`, `characteristic_object.db_column`, `items.level`, `resources.weight`, etc. | Les noms de colonnes sont la **référence d’écriture** pour l’intégration (payload Laravel). Toute divergence avec les modèles ou le mapping scrapping casse l’écriture. |
| **Tables characteristics** | `key` (ex. `level_creature`, `pa_spell`) — unique, utilisé partout comme identifiant logique. | Point central : cette clé doit être cohérente avec seeders, config, mapping DofusDB, icônes. |
| **Tables characteristic_creature / _object / _spell** | `db_column` (ex. `level`, `rarity`, `price`) — nom de la colonne sur l’entité cible (item, monster, etc.). | Doit correspondre exactement aux attributs des modèles Eloquent (Item, Resource, Creature, etc.). |

**Fichiers clés** :
- `database/migrations/2026_02_03_100000_create_characteristics_table.php` (colonne `key`)
- `database/migrations/2026_02_03_100001_create_characteristic_creature_table.php` (`db_column`)
- `database/migrations/2026_02_03_100002_create_characteristic_object_table.php` (`db_column`)
- Migrations des entités (items, resources, consumables, breeds, monsters, spells, etc.) pour les noms de colonnes.

---

### 1.2 Modèles Eloquent

| Fichier / zone | Ce qui est nommé | Risque |
|----------------|------------------|--------|
| **Modèles** (`app/Models/`) | `$fillable`, attributs (colonnes BDD), noms de relations. | Les clés du payload d’intégration (IntegrationService) et les `target_field` du mapping scrapping doivent correspondre aux noms d’attributs. |
| **Characteristic** | `key`, `icon`, `name`, `short_name`, etc. | `key` = identifiant logique ; `icon` = nom de fichier ou chemin (voir § 1.6). |
| **Item, Resource, Consumable, Creature, Breed, Spell…** | Colonnes métier : `level`, `rarity`, `price`, `weight`, `bonus`, `effect`, etc. | Toute nouvelle colonne ou renommage doit être reflété dans le mapping scrapping (`target_field`) et dans les formules / caractéristiques (`db_column`). |

**Fichiers clés** :
- `app/Models/Characteristic.php`
- `app/Models/Entity/Item.php`, `Resource.php`, `Consumable.php`
- `app/Models/Entity/Creature.php` (ou Monster, Breed selon le modèle)
- `app/Models/Scrapping/ScrappingEntityMapping.php`, `ScrappingEntityMappingTarget.php`

---

### 1.3 Caractéristiques (seeders et données)

| Fichier / zone | Ce qui est nommé | Risque |
|----------------|------------------|--------|
| **characteristics.php** | `key` (ex. `level_creature`, `pv_object`, `objet_sav_agi_creature`). | **Source de vérité** pour l’identifiant logique. Incohérence avec les autres fichiers (icônes, DofusDB, effect_sub_effects) = mapping cassé. |
| **characteristic_creature.php** | `characteristic_key`, `db_column`, `entity` (`*`, `monster`, `class`, `npc`). | `characteristic_key` doit être une `key` existante dans `characteristics`. `db_column` = colonne sur la table cible. |
| **characteristic_object.php** | Idem + `item_type_ids`, etc. | Même logique que creature. |
| **characteristic_spell.php** | Idem pour les sorts. | Cohérence avec `characteristics.key` et avec les colonnes des tables spells. |
| **characteristic_icons_colors.php** | Clés du tableau `icons` et `colors` = **characteristic key** (ex. `life_creature`, `pa_object`). | Si une clé est renommée dans `characteristics.php` mais pas ici, les icônes/couleurs par défaut ne s’appliquent plus. **Incohérence connue** : `pv_object` dans icons/colors vs `pv_object` dans characteristics (voir § 2). |

**Fichiers clés** :
- `database/seeders/data/characteristics.php`
- `database/seeders/data/characteristic_creature.php`
- `database/seeders/data/characteristic_object.php`
- `database/seeders/data/characteristic_spell.php`
- `database/seeders/data/characteristic_icons_colors.php`

---

### 1.4 Config et mapping scrapping

| Fichier / zone | Ce qui est nommé | Risque |
|----------------|------------------|--------|
| **effect_sub_effects.php** | `key` dans la liste des caractéristiques (ex. `pa`, `pm`, `vita`, `force`, `terre`, `feu`). | **Convention différente** : clés **courtes** sans suffixe `_creature`/`_object`/`_spell`. Utilisées pour les sous-effets (pattern action → caractéristique → valeur). Risque de confusion avec les `characteristic_key` du reste du projet. |
| **scrapping_entity_mappings.php** | `mapping_key`, `from_path`, `characteristic_key` (nullable), `target_model`, `target_field`. | `target_field` = nom du champ côté Krosmoz (doit être un attribut du modèle cible). `characteristic_key` = `characteristics.key` (ex. `level_object`, `rarity_object`). |
| **DofusDbEffectMapping** | Slugs de sous-effets (`frapper`, `autre`), noms d’éléments (`neutre`, `feu`, `eau`, `terre`, `air`). | Les éléments sont alignés avec les clés « courtes » de `effect_sub_effects.php`. Pas de lien direct avec les `characteristic_key` du type `do_fixe_feu_object`. |
| **dofusdb_characteristic_to_krosmoz.json** | Mapping `id DofusDB` → `characteristic_key` Krosmoz (ex. `0` → `pv_object`, `10` → `strong_object`). | **Incohérence connue** : `pv_object` dans ce fichier alors que la caractéristique objet « Points de vie » est `pv_object` dans `characteristics.php` (voir § 2). |

**Fichiers clés** :
- `config/effect_sub_effects.php`
- `database/seeders/data/scrapping_entity_mappings.php`
- `app/Services/Scrapping/Core/Conversion/SpellEffects/DofusDbEffectMapping.php`
- `resources/scrapping/config/sources/dofusdb/dofusdb_characteristic_to_krosmoz.json`

---

### 1.5 Services (conversion, intégration, formatters)

| Fichier / zone | Ce qui est nommé | Risque |
|----------------|------------------|--------|
| **ScrappingMappingService** | Expose `characteristic_key` (depuis la BDD) pour le pipeline de conversion. | La clé vient de `Characteristic.key` ; cohérence avec seeders et JSON DofusDB. |
| **FormatterApplicator** | Utilise `context.mappingRule.characteristic_key` pour les formatters `dofusdb_*`. | Si la règle de mapping n’a pas la bonne `characteristic_key`, la conversion par caractéristique échoue ou ne s’applique pas. |
| **IntegrationService** | Construit les payloads avec des clés (ex. `level`, `rarity`, `price`, `bonus`, `effect`) pour Item/Resource/Consumable. | Les clés du payload **doivent** être les noms de colonnes/attributs des modèles. |
| **DofusConversionService / ConversionFormulaGenerator** | Utilisent `characteristic_key` (ex. `level_creature`, `life_creature`) pour résoudre formules et limites. | Dépendance forte à la cohérence des clés dans `characteristics` et tables dérivées. |

**Fichiers clés** :
- `app/Services/Scrapping/Core/Config/ScrappingMappingService.php`
- `app/Services/Scrapping/Core/Conversion/FormatterApplicator.php`
- `app/Services/Scrapping/Core/Integration/IntegrationService.php`
- `app/Services/Characteristic/Conversion/DofusConversionService.php`

---

### 1.6 Icônes et chemins (backend + frontend)

| Fichier / zone | Ce qui est nommé | Risque |
|----------------|------------------|--------|
| **characteristic_icons_colors.php** | Clé = `characteristic_key` ; valeur = nom de fichier (ex. `life.webp`, `pa.webp`) ou chemin. | Voir incohérence `pv_object` vs `pv_object` (§ 2). |
| **CharacteristicSeeder** | Complète `icon` et `color` depuis characteristic_icons_colors quand NULL. | Les clés du config doivent être exactement les `key` de la table `characteristics`. |
| **MonsterTableController** (et autres APIs) | Préfixe `icons/caracteristics/` avant d’envoyer l’icône au frontend. | Côté front, la source attendue est du type `icons/caracteristics/pa.webp` (ImageService construit `/storage/images/{path}`). |
| **ImageService.js** | Préfixe statique `icons/` ; URL finale `/storage/images/${path}`. | Si le backend envoie un chemin relatif sans le préfixe attendu, l’icône ne s’affiche pas. |
| **Index.vue (admin characteristics)** | `iconBasePath = '/storage/images/icons/caracteristics'` ; affichage de l’icône à partir de `effective.icon`. | Alignement avec ce qui est stocké en BDD (nom de fichier vs URL complète après upload). |

**Fichiers clés** :
- `database/seeders/data/characteristic_icons_colors.php`
- `database/seeders/CharacteristicSeeder.php`
- `app/Http/Controllers/Api/Table/MonsterTableController.php` (ex. ligne ~215)
- `resources/js/Utils/file/ImageService.js`
- `resources/js/Pages/Admin/characteristics/Index.vue`

---

### 1.7 Frontend (Vue, descriptors, configs)

| Fichier / zone | Ce qui est nommé | Risque |
|----------------|------------------|--------|
| **Descriptors** (ex. `monster-descriptors.js`, `breed-descriptors.js`) | Noms de champs affichés (ex. `level`, `life`, `pa`), labels, icônes FontAwesome. | Usage **affichage** uniquement ; pas de lien direct avec les clés BDD sauf si on affiche des caractéristiques dynamiques (ex. CreatureSummaryCell). |
| **CreatureSummaryCell / CellRenderer** | Reçoit un mapping `db_column` → `{ name, short_name, icon, color, … }`. | L’icône peut être un chemin relatif (ex. `icons/caracteristics/pa.webp`) ou un nom de fichier ; cohérence avec le backend. |
| **Configs entités** (ex. `entities.js`, `SharedConstants.js`) | Types d’entités, labels, icônes. | Peu de risque de conflit avec les characteristic_key si on ne mélange pas les namespaces. |

**Fichiers clés** :
- `resources/js/Entities/**/*-descriptors.js`
- `resources/js/Pages/Molecules/entity/creature/CreatureSummaryCell.vue`
- `resources/js/Pages/Atoms/data-display/CellRenderer.vue`
- `resources/js/Utils/Entity/Configs/TableConfig.js` (si colonnes dynamiques)

---

### 1.8 Effets et sous-effets (SubEffect, Effect)

| Fichier / zone | Ce qui est nommé | Risque |
|----------------|------------------|--------|
| **SubEffect** (modèle) | `slug` (ex. `frapper`, `autre`) — identifiant du sous-effet. | Doit correspondre à ce que produit SpellEffectsConversionService (`sub_effect_slug`) et à ce qui est configuré en BDD / seeders. |
| **effect_sub_effects.php** | Clés « courtes » pour les caractéristiques des sous-effets (`pa`, `vita`, `force`, etc.). | Distinct des `characteristics.key` ; utilisé pour le **pattern** action → caractéristique → valeur, pas pour le stockage par entité. |
| **DofusDbEffectMapping** | `EFFECT_ID_TO_SUB_EFFECT`, `ELEMENT_ID_TO_KEY`. | Alignement avec les slugs SubEffect et les noms d’éléments dans effect_sub_effects. |

**Fichiers clés** :
- `app/Models/SubEffect.php`
- `config/effect_sub_effects.php`
- `app/Services/Scrapping/Core/Conversion/SpellEffects/DofusDbEffectMapping.php`
- `app/Services/Scrapping/Core/Conversion/SpellEffects/SpellEffectsConversionService.php`

---

## 2. Incohérences repérées (à traiter)

### 2.1 `pv_object` vs `pv_object`

- **characteristics.php** et **characteristic_object.php** : la caractéristique « Points de vie » (objet) est **pv_object**.
- **characteristic_icons_colors.php** (icons et colors) : entrées **pv_object**.
- **dofusdb_characteristic_to_krosmoz.json** : id `0` (hitPoints) mappé vers **pv_object**.
- **Documentation / commandes** (ExtractObjectConversionSamplesCommand, STRUCTURE_JSON_OBJECT_SAMPLES.md, etc.) : références à **pv_object**.

**Impact** : Pour la conversion DofusDB → Krosmoz, le formatter s’attend à une characteristic_key ; si le code ou la config utilisent `pv_object` alors que la BDD ne connaît que `pv_object`, le mapping échoue ou les icônes/couleurs par défaut ne s’appliquent pas pour cette caractéristique.

**Recommandation** : Choisir une seule clé canonique (idéalement `pv_object` pour rester aligné avec la BDD) et remplacer `pv_object` partout (icons/colors, JSON DofusDB, docs, commandes).

---

### 2.2 Préfixe « objet_sav » / « objet_save » (sauvegardes)

- **characteristics.php** : `objet_sav_agi_creature`, `objet_sav_intel_object`, `objet_sav_sagesse_object`, **objet_save_chance_creature** (avec « e »).
- Incohérence **sav** vs **save** et mélange **objet_** (français) avec le reste en anglais.

**Recommandation** : Uniformiser (ex. `save_*_creature` / `save_*_object` et supprimer le préfixe `objet_`, ou documenter une exception si le préfixe est volontaire).

---

### 2.3 Clés courtes (effect_sub_effects) vs clés complètes (characteristics)

- **effect_sub_effects.php** : `pa`, `pm`, `vita`, `force`, `intel`, `terre`, `feu`, etc.
- **characteristics** : `pa_creature`, `pa_spell`, `pa_object`, `vitality_creature`, `strong_creature`, `do_fixe_terre_object`, etc.

Ce n’est pas une erreur mais **deux espaces de nommage** : un pour les sous-effets (contexte « action → caractéristique »), un pour les caractéristiques par groupe (creature/object/spell). Le risque est de **mélanger** les deux (ex. utiliser `pa` comme characteristic_key dans un mapping qui attend `pa_object`).

**Recommandation** : Documenter clairement la différence et s’assurer qu’aucun code n’utilise une clé courte à la place d’une clé complète (ou inversement) là où ce n’est pas voulu.

---

## 3. Synthèse : chaîne de cohérence

Pour qu’un flux « DofusDB → conversion → intégration → affichage » soit fiable :

1. **characteristics.key** = identifiant logique unique (ex. `level_object`, `pv_object`).
2. **characteristic_creature / _object / _spell** : `characteristic_key` = une de ces `key` ; `db_column` = colonne sur l’entité cible (ex. `level`, `rarity`).
3. **scrapping_entity_mappings** : `characteristic_key` = même `characteristics.key` ; `target_field` = même nom que `db_column` ou attribut du modèle cible.
4. **characteristic_icons_colors** : les clés du tableau = **characteristics.key** (pas de variante comme `pv_object` si la BDD a `pv_object`).
5. **dofusdb_characteristic_to_krosmoz.json** : les valeurs du mapping = **characteristics.key** (même convention).
6. **IntegrationService** : les clés du payload = noms d’attributs des modèles (alignés sur les colonnes BDD et donc sur `db_column` / `target_field`).
7. **Frontend** : affichage des caractéristiques par `key` ou par `db_column` selon le contexte ; icônes via chemin cohérent avec le backend (`icons/caracteristics/` + nom de fichier).

Toute nouvelle propriété ou renommage doit être propagé le long de cette chaîne pour éviter les erreurs de mapping.

---

## 4. Fichiers à maintenir en phase (checklist)

Lors d’un ajout ou d’un renommage de caractéristique ou de propriété d’entité :

- [ ] **characteristics** (table + seeder data) : `key` unique.
- [ ] **characteristic_creature / _object / _spell** (seeders) : `characteristic_key` + `db_column` si applicable.
- [ ] **characteristic_icons_colors.php** : entrées `icons` et `colors` avec la même clé que `characteristics.key`.
- [ ] **scrapping_entity_mappings** : `target_field` = attribut modèle ; `characteristic_key` si lié à une caractéristique.
- [ ] **dofusdb_characteristic_to_krosmoz.json** : si la propriété vient de DofusDB, mapping id → `characteristics.key`.
- [ ] **Modèles Eloquent** : colonnes/attributs alignés avec `db_column` et payload d’intégration.
- [ ] **Documentation** (STRUCTURE_JSON_OBJECT_SAMPLES, DOFUSDB_CHARACTERISTIC_ID_REFERENCE, etc.) : mêmes noms que la source de vérité BDD/config.
- [ ] **Frontend** : si affichage par clé ou par colonne, utiliser les mêmes identifiants que le backend.

---

*Document généré dans le cadre de l’uniformisation du nommage (scrapping, caractéristiques, config, icônes). À mettre à jour lors de tout changement de convention ou de correction d’incohérences.*
