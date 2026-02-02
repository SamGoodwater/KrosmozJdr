# Implications de la nouvelle architecture caractéristiques / formules pour le scrapping

**Contexte** : Les caractéristiques (limites, requis, type, etc.) et les formules de conversion DofusDB → KrosmozJDR sont désormais stockées en base de données et exposées via des services. Ce document décrit ce que cela implique pour le pipeline de scrapping .

---

## 1. Nouvelle architecture (résumé)

| Donnée | Avant | Maintenant |
|--------|--------|------------|
| **Limites / règles par entité** (min, max, required, validation_message) | `config/characteristics.php` | Tables `characteristics` + `characteristic_entities` → **CharacteristicService** (cache) |
| **Formules de conversion** (level, life, attributs, initiative par entité) | `config/dofusdb_conversion.php` (section formulas) | Table `dofusdb_conversion_formulas` → **DofusDbConversionFormulaService** (cache) |
| **Évaluation d’une formule** (ex. `[d]/10`, table JSON) | Code PHP fixe | **FormulaEvaluator** (syntaxe `[d]`, `[level]`, opérateurs, tables) |

- **CharacteristicService** : `getCharacteristics()`, `getCompetences()`, `getFullConfig()`, `getLimits(characteristicId, entity)`. Même structure que l’ancienne config pour compatibilité.
- **DofusDbConversionFormulaService** : `getConversionFormula(characteristicId, entity)` (chaîne à évaluer), `getFormula(characteristicId, entity)` (formula_type + parameters). Fallback sur `config/dofusdb_conversion.php` si rien en BDD.
- **DofusDbConversionFormulas** : utilise d’abord la BDD (conversion_formula ou formula_type/parameters), puis la config. Utilise **CharacteristicService** pour le clamp (limites) et **FormulaEvaluator** pour les formules en chaîne.

---

## 2. Qui utilise quoi côté scrapping

| Composant | Limites (min/max, required) | Formules de conversion (level, life, attributs, initiative) |
|-----------|-----------------------------|----------------------------------------------------------------|
| **ValidationService ** | CharacteristicService (BDD) | — |
| **DofusDbConversionFormulas** | CharacteristicService (BDD) pour clamp | DofusDbConversionFormulaService (BDD) + fallback config |
| **ConversionService ** | — | **Non** : n’utilise que les formatters JSON (toInt, clampInt, etc.) |
| **DataConversionService (ancien)** | Via DofusDbConversionFormulas + ValidationService | **Oui** : DofusDbConversionFormulas (level, life, attributs, initiative) |

---

## 3. Implications pour le scrapping

### 3.1 Validation 
- **ValidationService** lit déjà **CharacteristicService** (plus `Config::get('characteristics.characteristics')`).
- Les données converties sont validées avec les **limites et champs requis définis en BDD** (et en cache). Aucun changement à prévoir côté validation.

### 3.2 Ancien pipeline (DataConversionService)

- **DataConversionService** utilise **DofusDbConversionFormulas** pour level, life, strength, intelligence, chance, agility, etc., et **ValidationService** pour valider.
- Les **formules en BDD** (et le fallback config) sont donc bien utilisées pour la conversion.
- Les **limites** utilisées pour le clamp dans DofusDbConversionFormulas viennent de **CharacteristicService** (BDD).
- Conséquence : toute modification en admin (caractéristiques ou formules de conversion) s’applique au scrapping qui passe par **DataConversionService** (ancien orchestrateur).

### 3.3 Pipeline (ConversionService + FormatterApplicator)

- **ConversionService** applique uniquement le **mapping** défini dans les JSON d’entités (`resources/scrapping/config/sources/dofusdb/entities/*.json`) et les **formatters** (toString, pickLang, toInt, clampInt, truncate, etc.).
- Il **n’appelle pas** DofusDbConversionFormulas ni DofusDbConversionFormulaService.
- Donc aujourd’hui, :
  - **level** : typiquement `toInt` + `clampInt` avec min/max **fixés dans le JSON** (ex. 1–200), pas la formule BDD (k = d/10).
  - **life** : idem, pas de formule k = d/200 + level×5.
  - **strength, intelligence, chance, agility** : idem, pas de formule sqrt en BDD.
  - **initiative** : idem, pas de formule ratio en BDD.
- Les **limites** utilisées en conversion sont celles **codées en dur dans les formatters** (ex. `clampInt` avec `min`/`max` dans le mapping JSON), pas celles de CharacteristicService (BDD).

En résumé : **le pipeline consomme les formules ni les limites stockées en BDD.** Seuls les formatters simples et les bornes définies dans les JSON d’entités sont utilisés.

---

## 4. Que faire pour aligner le scrapping sur la nouvelle architecture

Pour que le scrapping utilise les **formules et limites en BDD** comme le fait l’ancien pipeline :

1. **Utiliser DofusDbConversionFormulas (et donc la BDD) dans la conversion**  
   - Soit en faisant appel à **DofusDbConversionFormulas** depuis le **ConversionService** pour certains champs (level, life, attributs, initiative) lorsque l’entité est monster/class.  
   - Soit en ajoutant des **formatters dédiés** dans **FormatterApplicator** (ex. `dofusdb_level`, `dofusdb_life`, `dofusdb_attribute`, `dofusdb_initiative`) qui appellent **DofusDbConversionFormulas** avec la valeur DofusDB et l’entité, et en utilisant ces formatters dans les JSON d’entités à la place de toInt/clampInt pour ces champs.

2. **Passer l’entité (monster / class / item) et le contexte au ConversionService**  
   - Les formules et limites dépendent de l’entité. Le contexte passé à `convert()` doit contenir au minimum l’**entityType** (monster, class, item) pour que DofusDbConversionFormulas et CharacteristicService puissent appliquer les bonnes règles.

3. **Gérer l’ordre des conversions**  
   - **life** dépend du **level** Krosmoz déjà converti. Soit on applique les formatters dans un ordre défini (level avant life), soit on garde une phase dédiée (comme dans DataConversionService) qui appelle explicitement convertLevel puis convertLife avec ce level.

4. **Optionnel : utiliser CharacteristicService pour les bornes dans les formatters**  
   - Remplacer les min/max en dur dans les JSON par un formatter qui appelle CharacteristicService.getLimits(characteristicId, entity) pour clamp, ou s’appuyer sur le clamp déjà fait dans DofusDbConversionFormulas (qui utilise déjà CharacteristicService).

---

## 5. Rôle des configs restantes

- **config/characteristics.php** : n’est plus la source de vérité pour les limites si tout est chargé depuis la BDD via CharacteristicService. Peut servir de **référence ou de seed** pour les migrations/seeders.
- **config/dofusdb_conversion.php** :  
  - **formulas** : utilisé en **fallback** par DofusDbConversionFormulas lorsqu’il n’y a pas de ligne en BDD pour (characteristic_id, entity).  
  - **pass_through_characteristics**, **effect_id_to_characteristic**, **element_id_to_resistance**, **limits_source** : toujours lus par le code (mapping effets, résistances, etc.). À garder tant que ces données ne sont pas en BDD.

---

## 6. Synthèse

| Question | Réponse |
|----------|---------|
| Les limites (min/max, required) viennent-elles de la BDD pour le scrapping ? | **Oui** pour la validation (ValidationService) et pour l’ancien pipeline (DataConversionService via DofusDbConversionFormulas). **Non** pour la conversion (formatters avec bornes dans le JSON). |
| Les formules de conversion (level, life, attributs, initiative) viennent-elles de la BDD ? | **Oui** pour l’ancien pipeline (DataConversionService → DofusDbConversionFormulas → DofusDbConversionFormulaService). **Non** pour la conversion (ConversionService n’utilise pas DofusDbConversionFormulas). |
| Faut-il modifier le scrapping ? | **Oui** si on veut que le **pipeline** utilise les formules et limites en BDD : brancher DofusDbConversionFormulas (et le contexte entité) dans la conversion, et gérer l’ordre (level avant life). |

Une fois la conversion branchée sur **DofusDbConversionFormulas** et **CharacteristicService**, les changements faits en admin (caractéristiques et formules de conversion) s’appliqueront aussi au scrapping, comme pour l’ancien pipeline.
