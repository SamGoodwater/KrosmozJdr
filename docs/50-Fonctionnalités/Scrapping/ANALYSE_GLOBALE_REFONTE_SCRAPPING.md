# Analyse globale — Refonte du service de scrapping

**Date :** 2026-03-03  
**Objectif :** Analyser le projet KrosmozJDR et le service de scrapping (et sous-services) pour préparer une refonte : prise en compte de la profondeur de l’API DofusDB, mapping et conversion cohérents, intégration avec le service Caractéristiques.

---

## 1. Vue d’ensemble du projet KrosmozJDR

- **Stack :** Laravel 12 (PHP 8.4), Vue 3, Inertia.js, Tailwind + DaisyUI, Pinia, Vite.
- **Rôle du scrapping :** Récupérer des données depuis l’API **DofusDB** (jeu Dofus) et les importer dans le modèle de données **KrosmozJDR** (JDR sur table). Les entités concernées : monstres, classes (breeds), sorts, items (ressources, consommables, équipements), panoplies.
- **Contrainte centrale :** L’API DofusDB a une **architecture différente par entité** (structure plate vs imbriquée, endpoints multiples pour une même entité logique, tout en IDs). Le mapping et la conversion doivent en tenir compte.

---

## 2. Architecture actuelle du service de scrapping

### 2.1 Pipeline principal

```
Collect (API DofusDB) → Conversion (mapping + formatters) → Validation (limites BDD) → Intégration (écriture BDD)
```

- **Orchestrator** (`Core/Orchestrator/Orchestrator.php`) : point d’entrée unique. Enchaîne Collect → Conversion → Validation → Intégration. Gère `runOne` / `runMany`, options (lang, validate, dry_run, include_relations).
- **Points d’entrée :** CLI `php artisan scrapping:run` (alias legacy : `scrapping`), API `POST /api/scrapping/import/{entity}/{id}`.

### 2.2 Sous-services (briques)

| Brique | Fichier | Rôle |
|--------|---------|------|
| **ConfigLoader** | `Core/Config/ConfigLoader.php` | Charge source + entités depuis `resources/scrapping/config/sources/dofusdb/`. **Mapping :** BDD via `ScrappingMappingService` si présent, sinon fallback sur le `mapping` des JSON d’entité. |
| **ScrappingMappingService** | `Core/Config/ScrappingMappingService.php` | Lit les règles en BDD (`scrapping_entity_mappings` + targets). Retourne un tableau au format attendu par ConversionService (key, from.path, to[], formatters[], characteristic_id, characteristic_key). |
| **CollectService** | `Core/Collect/CollectService.php` | Exécute les requêtes décrites en config (endpoints fetchOne/fetchMany, pagination, filtres). Gère les cas spéciaux : spell → fetch des spell-levels ; item → recette (fetchRecipeByResultId). |
| **ConversionService** | `Core/Conversion/ConversionService.php` | Applique le **mapping** : pour chaque règle, extraction par `from.path` (dot-notation), chaîne de **formatters**, écriture dans `out[model][field]`. Gère aussi le **batch résistances** (monster, class, item) via `DofusConversionService`. |
| **FormatterApplicator** | `Core/Conversion/FormatterApplicator.php` | Registry de formatters (toString, pickLang, toInt, clampInt, clampToCharacteristic, dofusdb_level, dofusdb_life, dofusdb_attribute, dofusdb_ini, itemEffectsToKrosmozBonus, zoneDescrToNotation, etc.). Les formatters « Dofus » délèguent à `DofusConversionService` et/ou `CharacteristicGetterService`. |
| **SpellEffectsConversionService** | `Core/Conversion/SpellEffects/SpellEffectsConversionService.php` | **Sous-service dédié aux effets de sorts.** Prend spell brut + spell-levels, mappe effectId → sous-effet Krosmoz, résout formules (characteristic_spell), produit `spell_effects` (effect_group + effects avec sub_effects). Appelé **après** la conversion des champs plats du sort (dans l’Orchestrator). |
| **SpellGlobalNormalizer** | `Core/Normalizer/SpellGlobalNormalizer.php` | Construit un objet `spell_global` à partir du sort racine + premier niveau (levels[0]) + zone du premier effet, pour que le mapping spell.json utilise des chemins stables (ex. spell_global.apCost, spell_global.minRange). |
| **CharacteristicLimitService** | `App\Services\Characteristic\Limit\CharacteristicLimitService` | Valide et clampe les données converties selon les définitions BDD (Getter) par type d’entité (monster, class, item, spell, etc.). |
| **IntegrationService** | `Core/Integration/IntegrationService.php` | Écrit en BDD (creatures, monsters, breeds, spells, items, etc.) ou simule (dry_run). Gère les effets de sorts (EffectGroup, Effect, EffectSubEffect) et les relations (drops, creature_spell, etc.). |
| **RelationResolutionService** | `Core/Relation/RelationResolutionService.php` | Après intégration d’une entité (ex. monster), importe les entités liées (sorts, drops) et synchronise les tables de liaison. |

### 2.3 Config et données

- **Source :** `resources/scrapping/config/sources/dofusdb/source.json` (baseUrl, langue).
- **Entités :** `entities/*.json` (monster, spell, breed, item, item-type, monster-race, item-super-type). Chaque fichier définit : endpoints, filtres, target (krosmozEntity), **mapping** (from.path → to model/field + formatters). Le mapping peut être **écrasé par la BDD** si `ScrappingMappingService` est injecté dans ConfigLoader et retourne des règles.
- **Mapping BDD :** tables `scrapping_entity_mappings` et `scrapping_entity_mapping_targets` ; seeders dans `database/seeders/data/scrapping_entity_mappings.php`.
- **Effets / bonus :**
  - **Sorts :** mapping effectId → sous-effet dans `DofusDbEffectMapping` (constante PHP) + `DofusdbEffectMappingService` ; formules de conversion via `characteristic_spell` et `SpellEffectConversionFormulaResolver`.
  - **Items (bonus) :** `item.json` mappe `effects` → `effect` (formatter `itemEffectsToKrosmozBonus`) et `bonus` (toJson brut). Le formatter lit `dofusdb_characteristic_to_krosmoz.json` pour id caractéristique DofusDB → characteristic_key Krosmoz, puis appelle `DofusConversionService::convertObjectAttribute` par effet. **Aucun mapping par champ imbriqué** : un seul formatter dédié, logique dans FormatterApplicator.

---

## 3. Service Caractéristiques (Characteristics)

### 3.1 Les 4 sous-services

| Service | Rôle | Utilisation par le scrapping |
|--------|------|------------------------------|
| **CharacteristicGetterService** | Définitions par clé + entité (min, max, type, conversion_formula, etc.). Résout entity → groupe (creature, object, spell). | FormatterApplicator (clampToCharacteristic, formatters dofusdb_*), SpellEffectConversionFormulaResolver, DofusConversionService. |
| **CharacteristicLimitService** | Validation (validate) et clamp des valeurs selon type (boolean, list, min/max). | Orchestrator : clamp puis validate des données converties avant intégration. |
| **FormulaResolutionService** / **CharacteristicFormulaService** | Évaluation sécurisée des formules (variables, dés, tables). | Getter et DofusConversionService pour évaluer min/max et formules de conversion. |
| **DofusConversionService** | Convertit valeur Dofus → Krosmoz (niveau, vie, attributs, initiative, résistances, object attributes). S’appuie sur Getter (formule, limites), Formula, Limit. | ConversionService (batch résistances), FormatterApplicator (dofusdb_level, dofusdb_life, dofusdb_attribute, dofusdb_ini), itemEffectsToKrosmozBonus, SpellEffectsConversionService (valeurs d’effets). |

### 3.2 Groupes d’entités

- **creature :** monster, class, npc (alias breed → class).
- **object :** item, consumable, resource, panoply.
- **spell :** spell.

Les définitions (limites, formules de conversion) sont dans `characteristic_creature`, `characteristic_object`, `characteristic_spell` avec une ligne par entité ou `entity = '*'`.

### 3.3 Problème de profondeur

- Le Getter et le Limit travaillent sur des **champs plats** (un champ = une caractéristique, résolue par `getDefinitionByField` / `resolveFieldToKey`). La validation et le clamp s’appliquent à l’ensemble des champs d’un modèle (creatures, monsters, spells, items) **après merge** des blocs (mergeModels).
- Les **données imbriquées** (effets de sorts = liste d’effets par niveau, chaque effet avec sous-effets ; bonus d’équipement = liste d’effets DofusDB) ne sont **pas** décrites comme des champs « par caractéristique » dans le mapping plat. Elles passent par des **sous-services ou formatters dédiés** (SpellEffectsConversionService, itemEffectsToKrosmozBonus) qui utilisent eux-mêmes le service Caractéristiques (formules, conversion) mais en dehors du flux mapping → formatter → to[model][field] standard.
- Conséquence : pas de **modèle unifié** « une règle de mapping + une caractéristique par nœud imbriqué ». Chaque entité à profondeur a son propre chemin (spell : pipeline séparé dans l’Orchestrator ; item : un formatter qui boucle sur effects[]).

---

## 4. Problèmes identifiés (synthèse)

### 4.1 Mapping et profondeur

| Problème | Détail |
|----------|--------|
| **API différente par entité** | Spell = spell + spell-levels (plusieurs appels) ; item = item + effects[] imbriqué ; monster = grades[], drops[], etc. Le mapping actuel est **par entité** dans des JSON (ou BDD) avec des chemins plats ou un seul niveau (ex. spell_global.*). Les structures profondes (effects[], grades[].*) sont soit normalisées en amont (spell_global), soit traitées par un formatter unique (item effects → bonus). |
| **Mapping pas parfait** | Sur les entités à profondeur, le mapping « paramètre DofusDB → paramètre Krosmoz » ne se fait pas de façon homogène : pour les sorts, les paramètres globaux passent par le mapping, les effets par un sous-service ; pour les items, les bonus passent par un formatter avec un JSON de mapping characteristic id → key. Les règles ne sont pas toutes dans la même source (JSON entité vs BDD vs constante PHP pour effectId). |
| **characteristic_id sous-exploité** | La BDD a `scrapping_entity_mappings.characteristic_id`. Le pipeline pourrait utiliser cette FK pour appeler DofusConversionService / Getter sans dupliquer la logique dans les args des formatters. Aujourd’hui, les formatters utilisent souvent un arg `characteristicId` (string) pour clamp/conversion. |

### 4.2 Conversion et caractéristiques

| Problème | Détail |
| **Chaque paramètre doit passer par la conversion Krosmoz** | C’est bien le cas pour les champs mappés (formatters dofusdb_*, clampToCharacteristic) et pour le batch résistances. En revanche, les **valeurs à l’intérieur** des structures imbriquées (sous-effets de sort, bonus d’item) sont converties par des chemins dédiés (SpellEffectConversionFormulaResolver + characteristic_spell ; itemEffectsToKrosmozBonus + dofusdb_characteristic_to_krosmoz.json + convertObjectAttribute). Donc la logique « une caractéristique = une formule + limites » est respectée, mais **dispersée** (pas un seul flux « mapping récursif + formatter par caractéristique »). |
| **Service Caractéristiques morceau par morceau** | Le service Caractéristiques (Getter, Limit, Formula, Conversion) a été étendu au fil du temps. Il gère bien les groupes creature / object / spell et les champs plats. Il **ne propose pas** d’API « valider / convertir un nœud imbriqué selon une règle de mapping et une caractéristique ». La profondeur est gérée côté scrapping (sous-services, formatters), pas côté Characteristic. |

### 4.3 Cohérence et maintenabilité

| Problème | Détail |
| **Plusieurs sources de vérité pour le mapping** | JSON d’entité (mapping), BDD (scrapping_entity_mappings), constante PHP (DofusDbEffectMapping), fichier JSON (dofusdb_characteristic_to_krosmoz.json). Difficile d’avoir une vue unique « tout le mapping DofusDB → Krosmoz » par entité et par profondeur. |
| **Spells : deux flux** | 1) ConversionService sur spell_global (mapping spell.json). 2) SpellEffectsConversionService sur raw + levels. Les deux sont nécessaires mais le lien entre « paramètre de sort » et « paramètre d’effet » (characteristic_spell) n’est pas exposé de façon unifiée dans l’admin (panneau Mapping par caractéristique). |
| **Items : bonus** | Les bonus d’équipement sont convertis en un seul formatter ; la liste des characteristic_id DofusDB → key Krosmoz est dans un fichier séparé. Pas de règle par « effet » dans scrapping_entity_mappings (car le mapping est au niveau item, pas au niveau item.effects[].characteristic). |

---

## 5. Références utiles (existant)

- **Architecture cible :** [ARCHITECTURE_SCRAPPING_MAPPING_CARACTERISTIQUES.md](../ARCHITECTURE_SCRAPPING_MAPPING_CARACTERISTIQUES.md) — flux Mapping → Conversion → Caractéristiques, BDD, UI 3 panneaux.
- **État d’avancement :** [Architecture/ETAT_AVANCEMENT.md](./Architecture/ETAT_AVANCEMENT.md).
- **Vision et architecture :** [Architecture/VISION_ET_ARCHITECTURE.md](./Architecture/VISION_ET_ARCHITECTURE.md).
- **Effets DofusDB :** [DOFUSDB_EFFECTS_CONVERSION.md](./DOFUSDB_EFFECTS_CONVERSION.md), [DOFUSDB_API_SPELLS_REFERENCE.md](./DOFUSDB_API_SPELLS_REFERENCE.md).
- **Refonte (analyse et plan) :** [REFONTE_SCRAPPING_ANALYSE_ET_PLAN.md](./REFONTE_SCRAPPING_ANALYSE_ET_PLAN.md) — focus UI + composables + contrôleurs ; pas de refonte big-bang.
- **Caractéristiques (4 sous-services) :** [Characteristics-DB/ARCHITECTURE_SOUS_SERVICES.md](../Characteristics-DB/ARCHITECTURE_SOUS_SERVICES.md).

---

## 6. Suite prévue (travail avec l’agent)

1. **Liste des fonctionnalités nécessaires et besoins exacts** — à produire à partir de cette analyse et des docs existantes.
2. **État des lieux détaillé de l’existant** — par brique (Collect, Conversion, Mapping, Validation, Intégration, Caractéristiques) et par entité (monster, spell, item, etc.).
3. **Plan d’amélioration / restructuration** — ce qui peut être remanié sans tout reconstruire : unification du mapping, prise en compte de la profondeur, lien characteristic_id, évolution du service Caractéristiques si besoin.

Ce document sert de base pour les trois étapes suivantes.
