# Pipeline de scrapping et où modifier le mapping

**Objectif :** un seul endroit pour comprendre le flux et savoir où changer une règle de mapping.

---

## 1. Schéma du pipeline

```
┌─────────────────────────────────────────────────────────────────────────────┐
│  ConfigLoader (+ ScrappingMappingService)                                    │
│  → Charge source.json, entities/{entity}.json ; mapping runtime = BDD only   │
└─────────────────────────────────────────────────────────────────────────────┘
                                        │
                                        ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│  COLLECTE (CollectService)                                                  │
│  → Requêtes API (fetchOne / fetchMany), enrichissements (spell levels, etc.)│
│  → Lit : config (endpoints, filtres)                                        │
└─────────────────────────────────────────────────────────────────────────────┘
                                        │
                                        ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│  NORMALISATION (optionnel, sorts uniquement)                                │
│  → SpellGlobalNormalizer : raw['spell_global'] pour chemins stables         │
└─────────────────────────────────────────────────────────────────────────────┘
                                        │
                                        ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│  CONVERSION (ConversionService + FormatterApplicator)                      │
│  → Pour chaque règle : getByPath(raw, from_path) → formatters → to[model]   │
│  → Lit : mapping BDD, CharacteristicGetterService (limites, map)             │
│  → Blocs métier : ItemEffectsToBonusConverter (BDD dofusdb_characteristic_id)│
│                   SpellEffectsConversionService (dofusdb_effect_mappings)    │
└─────────────────────────────────────────────────────────────────────────────┘
                                        │
                                        ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│  VALIDATION (CharacteristicLimitService)                                    │
│  → Merge des blocs convertis, clamp min/max, validate par caractéristique   │
│  → Lit : characteristic_creature / object / spell (limites, value_available)│
└─────────────────────────────────────────────────────────────────────────────┘
                                        │
                                        ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│  INTÉGRATION (IntegrationService)                                           │
│  → integrateMonster, integrateSpell, integrateItem, integratePanoply, etc.  │
│  → Écrit en BDD (creatures, monsters, spells, items, panoplies, …)          │
└─────────────────────────────────────────────────────────────────────────────┘
                                        │
                                        ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│  RELATIONS (RelationResolutionService, optionnel)                           │
│  → Import des entités liées (drops, spells), sync tables de liaison         │
│  → Lit : config relations par entité                                       │
└─────────────────────────────────────────────────────────────────────────────┘
```

**Construction du pipeline :** `ScrappingPipelineFactory::createDefault()` (ou `Orchestrator::default()`). Les dépendances sont résolues via le conteneur Laravel.

---

## 2. Où modifier le mapping ?

| Type de règle | Où la définir | Fichier / table |
|---------------|----------------|-----------------|
| **Règles « chemin → cible »** (level, name, etc.) par entité (monster, spell, item, breed, panoply) | BDD (runtime) | **BDD :** `scrapping_entity_mappings` + `scrapping_entity_mapping_targets`. **Bootstrap :** `ScrappingEntityMappingSeeder` importe `database/seeders/data/scrapping_entity_mappings.php`, sinon fallback JSON des entités au moment du seed. |
| **Id caractéristique DofusDB → caractéristique Krosmoz** (bonus items / panoply) | BDD uniquement | **BDD :** colonne `dofusdb_characteristic_id` sur `characteristic_object`. Remplissage : `DofusdbCharacteristicIdSeeder` (à partir du JSON une fois, puis plus de fallback). |
| **EffectId DofusDB → sous-effet Krosmoz** (effets de sorts) | BDD (fallback constante PHP) | **BDD :** table `dofusdb_effect_mappings`. **Seeder :** `DofusdbEffectMappingSeeder`. Fallback si table vide : `DofusDbEffectMapping` (PHP, déprécié). |
| **Formules / limites de conversion** (niveau, vie, attributs) | BDD | Tables `characteristic_creature`, `characteristic_object`, `characteristic_spell` (formula, min, max, conversion_formula, etc.). |

En résumé : **règles par entité** = `scrapping_entity_mappings` (runtime BDD only) ; **id → caractéristique (objets)** = `characteristic_object.dofusdb_characteristic_id` ; **effectId → sous-effet** = `dofusdb_effect_mappings`.

### Bootstrap recommandé au lancement

- `php artisan scrapping:setup`
- Variantes :
  - `php artisan scrapping:setup --fresh` (reset + migrate + seed)
  - `php artisan scrapping:setup --skip-migrate` (seed uniquement)

---

## 3. Glossaire

| Terme | Signification |
|-------|----------------|
| **mapping rule** | Une règle qui lie un chemin de données source (ex. `grades.0.level`) à une ou plusieurs cibles (model + field) et optionnellement à une caractéristique (formules, limites). |
| **from_path** | Chemin dans les données brutes (notation point, ex. `spell_global.apCost`, `effects`). |
| **target** | Cible d’écriture : couple `(model, field)` (ex. `creatures`, `level`). |
| **characteristic_key** | Clé de la caractéristique en BDD (ex. `level_creature`, `strength_object`). Utilisée pour formules, limites et conversion. |
| **characteristic_id** | Id (PK) de la table `characteristics`. Une règle de mapping peut être liée à une caractéristique via `characteristic_id`. |
| **entityType** | Type d’entité dans le pipeline : `monster`, `spell`, `item`, `panoply`, `class` (breed), etc. Détermine le groupe de caractéristiques (creature / object / spell). |
| **mappingRule** | Objet passé dans le context aux formatters : contient la règle courante (key, from, to, formatters, characteristic_id, characteristic_key). |
| **formatter** | Fonction appliquée à une valeur extraite (ex. `dofusdb_level`, `itemEffectsToKrosmozBonus`). Enregistrée dans FormatterApplicator. |

---

## 4. Références

- [SIMPLIFICATIONS_SCRAPPING.md](./SIMPLIFICATIONS_SCRAPPING.md) — Constats et pistes de simplification.
- [PLAN_REFONTE_SCRAPPING.md](./PLAN_REFONTE_SCRAPPING.md) — Plan de refonte (phases 1–5).
- [ETAT_DES_LIEUX_REFONTE_SCRAPPING.md](./ETAT_DES_LIEUX_REFONTE_SCRAPPING.md) — Inventaire détaillé par brique.

---

## 5. Commandes scrapping (ordre et rôle)

| Commande | Rôle | Statut |
|---------|------|--------|
| `scrapping:setup` | Initialise le socle (migrations + seeders caractéristiques/mappings) | **Commande d’entrée recommandée** |
| `scrapping:run` | Collecte/preview/import des entités DofusDB | **Commande d’exploitation** |
| `scrapping:seeders:export` | Exporte les données BDD vers `database/seeders/data/*` (dont mappings scrapping) | **Maintenance / backup data** |
| `scrapping:types:seed` | Extrait les item-types depuis l’API puis exécute les seeders de types | **Maintenance catalogues** |
| `scrapping:types:extract` | Étape technique d’extraction vers fichiers data (appelée par `scrapping:types:seed`) | **Commande interne (peut être utilisée seule)** |
| `scrapping:effects:map` | Génère des propositions de mapping effectId -> sous-effet | **Assistance ponctuelle** |

### Compatibilité

- Alias conservés pour l’existant : `scrapping`, `scrapping:bootstrap`, `db:export-seeder-data`, `scrapping:seed-item-types`, `scrapping:extract-item-types`, `dofusdb:fetch-effect-mappings`.
