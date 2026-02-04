# Résumé : paramétrage des caractéristiques (état actuel)

Document de synthèse du système **après refonte** : organisation par entité, table `entity_characteristics`, formules et équipement par `(entity, characteristic_key)`.

---

## 1. Tables et modèles

| Table | Modèle | Rôle |
|-------|--------|------|
| **entity_characteristics** | `EntityCharacteristic` | Définition d’une caractéristique **par entité** : une ligne = (entity, characteristic_key) avec name, short_name, min, max, formula, formula_display, default_value, required, validation_message, forgemagie_allowed/max, base_price_per_unit, rune_price_per_unit, etc. Source de vérité unique par entité. |
| **dofusdb_conversion_formulas** | `DofusdbConversionFormula` | Formule de conversion DofusDB → Krosmoz **par (characteristic_key, entity)** : formula_type, parameters, conversion_formula, handler_name, formula_display. |
| **dofusdb_conversion_config** | `DofusdbConversionConfig` | Config globale de conversion en clé/valeur (pass_through_characteristics, limits_source, effect_id_to_characteristic, element_id_to_resistance, etc.). |
| **equipment_slots** | `EquipmentSlot` | Slots d’équipement (weapon, hat, cape, …) : id, name, sort_order. |
| **equipment_slot_characteristics** | `EquipmentSlotCharacteristic` | Par (equipment_slot_id, characteristic_key) : bracket_max, forgemagie_max, base_price_per_unit, rune_price_per_unit. Référence characteristic_key (plus characteristic_id). |
| **spell_effect_types** | `SpellEffectType` | Référentiel des types d’effets de sort. Géré comme le reste (data file + seeder + export). |

Les entités métier (Creature, Item, Spell, Resource, etc.) stockent les **valeurs** dans des colonnes ou JSON. La **définition** (bornes, formules, requis) est dans `entity_characteristics`.

---

## 2. Services

| Service | Responsabilité | Entrées / sorties |
|---------|----------------|-------------------|
| **CharacteristicService** | Lecture des définitions depuis `entity_characteristics`. Expose getCharacteristics (structure par characteristic_key avec entities), getCompetences, getFullConfig, getCharacteristic, getLimits, getRarityByLevel, clearCache. | BDD → tableau characteristic_key => [ name, type, entities => [ entity => [...] ], ... ]. Cache 1h. |
| **ValidationService** | Validation des données converties selon les définitions (CharacteristicService). | CharacteristicService + convertedData + entityType → ValidationResult. |
| **DofusDbConversionFormulaService** | Lecture des formules (dofusdb_conversion_formulas) par characteristic_key + entity. | BDD → characteristic_key => entity => [ formula_type, parameters, conversion_formula, handler_name ]. Cache 1h. |
| **DofusdbConversionConfigService** | Lecture de la config de conversion (dofusdb_conversion_config). Pas d’observer ni d’export. | BDD → tableau associatif. Cache 1h. |
| **DofusDbConversionFormulas** | Moteur de conversion : formules, FormulaEvaluator, ConversionHandlerRegistry, clampe selon limits (CharacteristicService). | Valeurs Dofus + entityType → valeurs Krosmoz. |
| **EquipmentCharacteristicService** | Slots et caractéristiques par slot (characteristic_key). | BDD → slot_id => [ name, characteristics => [ characteristic_key => [ bracket_max, forgemagie_max, ... ] ] ]. Cache 1h. |
| **FormulaEvaluator** / **ConversionHandlerRegistry** | Évaluation des formules et handlers nommés. | formula + variables → float ; handler_name → callable. |

---

## 3. Flux et consommateurs

- **Scrapping** : DofusDbConversionFormulas → valeurs Krosmoz → ValidationService (CharacteristicService) → intégration.
- **Validation** : CharacteristicService fournit définitions ; ValidationService en dérive requis, min/max, value_available (alias player/npc/breed → class).
- **Admin** : CharacteristicController édite EntityCharacteristic (par characteristic_key, agrégat des lignes par entité) et formules de conversion par entité.
- **Rareté par niveau** : FormatterApplicator appelle `CharacteristicService::getRarityByLevel(level, entity)` ; la formule (table niveau → rareté) vient de `dofusdb_conversion_formulas` (conversion uniquement), sinon fallback sur `config/characteristics_rarity.php`.

---

## 3.1 Rôle du CharacteristicService et paramètres requis

**À quoi sert le CharacteristicService ?**

C’est le **lecteur unique** des définitions de caractéristiques stockées en BDD (`entity_characteristics`). Il ne modifie pas les données ; il les expose sous plusieurs formes pour des usages différents.

**Paramètres qu’on a besoin de récupérer — et pourquoi :**

| Besoin | Méthode / donnée | Consommateur | Raison |
|--------|-------------------|--------------|--------|
| **Validation** (requis, min/max, valeurs autorisées) | `getCharacteristics()` → par `characteristic_key`, avec `entities[entity].required`, `min`, `max`, `validation_message`, `value_available`, `db_column` | ValidationService | Vérifier les données converties (scrapping) ou saisies contre les règles par entité. |
| **Clamp après conversion Dofus → Krosmoz** | `getLimits(characteristicId, entity)` → `min`, `max` | DofusDbConversionFormulas | Garder les valeurs converties dans les bornes autorisées (ex. level 1–20 pour resource). |
| **Rareté par niveau (conversion)** | `getRarityByLevel(levelKrosmoz, entity)` | FormatterApplicator | Lors du scrapping, déduire l’indice de rareté à partir du niveau (formule dans dofusdb_conversion_formulas, sinon config). |
| **Admin : liste et édition** | `getCharacteristics()`, `getCharacteristic(id)`, `getLimits(axisVar, entity)` | CharacteristicController | Afficher la liste par caractéristique, le détail par entité, les bornes pour les graphiques de formules. |
| **Invalidation du cache** | `clearCache()` | Observers, ExportSeederDataCommand | Après création/modif/suppression en BDD ou après export seeder. |

**Formes de données exposées :**

- **Par caractéristique** (`getCharacteristics()` / `getFullConfig()`) : `characteristic_key => [ name, type, db_column, entities => [ entity => [ min, max, required, ... ] ], ... ]`. Utilisée par ValidationService, DofusDbConversionFormulas (via getLimits), et l’admin (liste + détail). La liste d’entités est dérivée de cette structure quand besoin (ex. panneau admin).

---

## 4. Seeders et données par défaut

Fichiers dans `database/seeders/data/` :

| Fichier | Seeder | Tables |
|---------|--------|--------|
| entity_characteristics.php | EntityCharacteristicSeeder | entity_characteristics |
| dofusdb_conversion_formulas.php | DofusdbConversionFormulaSeeder | dofusdb_conversion_formulas |
| dofusdb_conversion_config.php | DofusdbConversionConfigSeeder | dofusdb_conversion_config |
| equipment_slots.php | EquipmentCharacteristicConfigSeeder | equipment_slots, equipment_slot_characteristics |
| spell_effect_types.php | SpellEffectTypeSeeder | spell_effect_types |

Export BDD → fichiers : `php artisan db:export-seeder-data` (options : --characteristics, --formulas, --equipment, --spell-effect-types). Pas d’export pour dofusdb_conversion_config.

---

## 5. Cache et observers

- **CharacteristicService** : invalidé par CharacteristicConfigObserver (EntityCharacteristic saved/deleted).
- **DofusDbConversionFormulaService** : DofusdbConversionFormulaObserver.
- **EquipmentCharacteristicService** : EquipmentCharacteristicConfigObserver (EquipmentSlot, EquipmentSlotCharacteristic).
- **DofusdbConversionConfigService** : aucun observer.

---

## 6. Références

- Besoin et structure détaillée : [BESOIN_REFONTE.md](./BESOIN_REFONTE.md).
- Formules et syntaxe : [docs/10-BestPractices/SYNTAXE_FORMULES_CARACTERISTIQUES.md](../../10-BestPractices/SYNTAXE_FORMULES_CARACTERISTIQUES.md) (source de vérité limites/formules : BDD via CharacteristicService).
