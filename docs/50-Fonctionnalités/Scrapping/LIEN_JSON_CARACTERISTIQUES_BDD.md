# Lien entre le mapping JSON (scrapping) et les caractéristiques BDD Krosmoz

Ce document décrit **comment une propriété issue du JSON d’entité (ou du scrapping DofusDB) est reliée à la caractéristique en base Krosmoz** pour les formules de conversion et les limites.

---

## 1. Chaîne globale

```
JSON entité (mapping[].from.path + formatters[].args)
    → FormatterApplicator (formatter name + args)
    → DofusConversionService (construction de la clé caractéristique)
    → CharacteristicGetterService (formule) + CharacteristicLimitService (clamp)
    → BDD : characteristics + characteristic_creature / characteristic_object / characteristic_spell
```

La **clé de caractéristique** (ex. `level_creature`, `strong_creature`, `ini_creature`) est ce qui relie le formatter à la définition en BDD (formule de conversion, min/max, type).

---

## 2. Construction de la clé caractéristique

### 2.1 Depuis les args du formatter (JSON)

Dans les JSON d’entité (`entities/*.json`), les formatters **dofusdb_*** reçoivent des arguments qui déterminent la caractéristique cible :

| Formatter        | Argument(s) dans le JSON      | Clé utilisée côté service                          |
|------------------|-------------------------------|----------------------------------------------------|
| **dofusdb_level**  | — (aucun)                     | Clé fixe selon l’entité : `level_creature` (monster, class, npc), `level_object` (item, consumable, resource, panoply). |
| **dofusdb_life**   | `levelPath` (ex. `grades.0.level`) | Toujours `life_creature` ; la formule reçoit aussi le niveau Krosmoz (lu depuis `raw` via levelPath). |
| **dofusdb_attribute** | `characteristicId` (ex. `"strength"`, `"intelligence"`) | **`characteristicId + "_creature"`** → ex. `strength` → `strength_creature`. Pour que la formule et les limites BDD soient utilisées, la table `characteristics` doit contenir une entrée avec **`key` = cette valeur** (ex. en BDD les clés sont parfois abrégées : `strong_creature`, `intel_creature`, `agi_creature` ; dans ce cas mettre `characteristicId: "strong"`, `"intel"`, `"agi"` dans le JSON pour obtenir la même clé). |
| **dofusdb_ini**    | —                             | Toujours **`ini_creature`**. |

Convention côté code : pour les créatures, **DofusConversionService::convertAttribute()** fait `$key = $characteristicId . '_creature'`. Donc la **clé en BDD** doit être exactement `{characteristicId}_creature` (ex. `strong_creature` si `characteristicId = "strong"`).

### 2.2 Résolution champ → clé (Getter)

**CharacteristicGetterService** peut aussi résoudre un **nom de champ** (ex. colonne ou nom court) en clé de caractéristique :

- **resolveFieldToKey(field, entity)** : pour le groupe `creature`, si la clé complète n’est pas trouvée, teste `field . '_creature'` ; sinon cherche dans `characteristic_creature` une ligne dont `characteristic.key` ou `db_column` vaut `field`.
- Utilisé par la **validation** (CharacteristicLimitService) et par les formulaires qui passent un nom de champ ; le scrapping passe surtout la **clé complète** construite ci‑dessus.

---

## 3. Où sont lues formule et limites

| Besoin              | Service / table                                      |
|---------------------|------------------------------------------------------|
| **Formule de conversion** (Dofus → Krosmoz) | **CharacteristicGetterService::getConversionFormula(key, entity)** → lit `conversion_formula` dans `characteristic_creature` / `characteristic_object` / `characteristic_spell` (selon l’entité). |
| **Limites (min/max)** pour le clamp après conversion | **CharacteristicLimitService::clamp(key, value, entity)** qui s’appuie sur **CharacteristicGetterService::getLimits(key, entity)** → min/max issus des mêmes tables. |
| **Validation** des données converties (avant intégration) | **CharacteristicLimitService::validate()** avec les définitions (type, min, max, value_available) pour chaque champ du modèle. |

Tout repose sur la **clé de caractéristique** (`key` dans la table `characteristics`, et référencée dans les tables par entité).

---

## 4. Fichiers de mapping DofusDB → clé (hors formatters directs)

Ces fichiers ne sont **pas** utilisés par les formatters du pipeline de conversion ; ils servent à d’autres usages (ex. export d’échantillons, effets d’objets) :

- **dofusdb_monster_grade_to_creature.json** : nom de champ DofusDB (grade monstre) → `characteristic_key` Krosmoz (ex. `strength` → `strong_creature`, `lifePoints` → `life_creature`). Utilisé par exemple par `ExtractCreatureConversionSamplesCommand`.
- **dofusdb_characteristic_to_krosmoz.json** : ID de caractéristique DofusDB (effets d’item) → clé Krosmoz (ex. `intel_object`). Utilisé par le formatter **itemEffectsToKrosmozBonus** dans FormatterApplicator.

La **convention dans les JSON d’entité** (args `characteristicId`, etc.) est donc la source directe du lien avec la BDD pour level, life, attributs et initiative.

---

## 5. Récapitulatif : de la propriété JSON à la BDD

1. **JSON** (`entities/monster.json`) : entrée de mapping avec `from.path` (ex. `grades.0.strength`) et formatter `dofusdb_attribute` avec `args: { "characteristicId": "strong" }`.
2. **ConversionService** : extrait la valeur brute, appelle FormatterApplicator avec ce formatter et ces args.
3. **FormatterApplicator** : appelle `DofusConversionService::convertAttribute("strong", value, "monster")`.
4. **DofusConversionService** : construit `key = "strong" + "_creature" = "strong_creature"`, puis :
   - `getter->getConversionFormula("strong_creature", "monster")` → formule en BDD ;
   - `formulaService->evaluate(formula, ['d' => $d])` → valeur convertie ;
   - `limitService->clamp("strong_creature", $k, "monster")` → limites BDD.
5. La valeur finale est écrite dans la structure convertie (ex. `creatures.strength`) puis validée et intégrée.

**En résumé** : le lien est **l’argument du formatter** (ex. `characteristicId`) qui, via une convention fixe dans le code (`_creature`, `level_creature`, `life_creature`, `ini_creature`), produit la **clé de caractéristique**. Cette clé doit correspondre à une entrée en BDD (`characteristics.key` et lignes dans les tables par groupe) pour que formules et limites soient appliquées.
