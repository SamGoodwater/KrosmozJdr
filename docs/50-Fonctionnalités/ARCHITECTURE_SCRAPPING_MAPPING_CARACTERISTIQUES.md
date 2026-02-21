# Architecture cible : Scrapping → Mapping → Conversion → Caractéristiques

Ce document résume **comment les services doivent être composés et architecturés** (Scrapping, Mappings, Conversion, Caractéristiques, UI admin), puis **compare avec l’existant**.

**Référence vision UI :** [VISION_UI_ADMIN_MAPPING_ET_CARACTERISTIQUES.md](./VISION_UI_ADMIN_MAPPING_ET_CARACTERISTIQUES.md).

---

## 1. Objectif global

- **Récupération** : Données depuis l’API externe (DofusDB), dont l’architecture diffère de KrosmozJDR.
- **Mapping** : Lier chaque propriété DofusDB à une propriété KrosmozJDR (aujourd’hui JSON → objectif **UI + BDD**).
- **Conversion** : Chaque propriété mappée est **convertie** (formules, limites, types) pour être compatible KrosmozJDR.
- **Deux blocs métier** :
  1. **Scrapping** : récupère les données (Collect) et les fait passer par Mapping + Conversion.
  2. **Characteristics** : définit limites, formules, conversion Dofus→Krosmoz, validation ; utilisé par la conversion et l’affichage.

Tout ce qui est **variable** (mapping, formules, limites, échantillons) doit être gérable **côté admin** (BDD + UI), sans éditer de JSON.

---

## 2. Flux de données (cible)

```
DofusDB (API)
    → Collect (config JSON : endpoints, filtres)
    → Données brutes
    → Mapping (BDD : source + entity + from_path → characteristic / to model.field + formatters)
    → Conversion (formatters + DofusConversionService si characteristic_id)
    → Validation (CharacteristicLimitService)
    → Intégration BDD KrosmozJDR
```

- **Mapping** : une règle = (source, entity, mapping_key) avec `from_path`, `characteristic_id` (optionnel), formatters, et cibles (model.field). **Unique par entité** (ex. même caractéristique logique peut avoir un mapping différent pour monster, spell, item).
- **Conversion** : utilise le mapping pour savoir *où* prendre la valeur (from_path) et *comment* la transformer (formatters). Si une règle est liée à une **caractéristique** (`characteristic_id`), la formule de conversion et les limites viennent du service Characteristics (Getter + Formula + Limit).
- **Expression dynamique** : valeur par défaut, min, max et formule de conversion sont des **expressions dynamiques** (constante, formule avec `[level]`, `[life]`, dés, conditions). Évaluées à l’exécution par le service Formula.

---

## 3. Composition des services (cible)

### 3.1 Scrapping (récupération + orchestration)

| Composant | Rôle |
|-----------|------|
| **ConfigLoader** | Charge config par source/entité (endpoints, filtres, target). **Mapping** fourni par **ScrappingMappingService** (BDD), plus par JSON. |
| **ScrappingMappingService** | Lit les règles de mapping en BDD (`scrapping_entity_mappings` + targets). Retourne un tableau au format attendu par ConversionService (key, from.path, from.langAware, to[], formatters[]). |
| **CollectService** | Exécute les requêtes (endpoints, pagination, filtres) décrites en config. |
| **ConversionService** | Applique le mapping (extraction par path, formatters, écriture model.field). Délègue à **DofusConversionService** pour level, life, attributs, initiative, résistances quand le formatter le demande (et selon entity). |
| **FormatterApplicator** | Registry de formatters (toString, pickLang, toInt, clampInt, dofusdb_level, dofusdb_life, dofusdb_attribute, dofusdb_ini, etc.). Les formatters **dofusdb_*** utilisent **DofusConversionService** + clé déduite des args ou de la règle (ex. `characteristic_id` → Getter pour formule + limites). |
| **ValidationService** | Valide les données converties (CharacteristicLimitService + définitions par entité). |
| **IntegrationService** | Enregistre en BDD (ou dry-run). |
| **Orchestrator** | Enchaîne Collect → Conversion → Validation → Intégration. |

### 3.2 Characteristics (définition + conversion + validation)

| Service | Rôle |
|---------|------|
| **CharacteristicGetterService** | Définitions par clé/entité : min, max, default_value, conversion_formula, formula_display, type, value_available, etc. Expose getLimits(), getConversionFormula(), getDefinition(). |
| **CharacteristicLimitService** | Validation (validate) et clamp selon type (boolean, list, min/max). |
| **CharacteristicFormulaService** / **FormulaResolutionService** | Évaluation sécurisée des expressions dynamiques (variables, dés, fonctions, tables conditionnelles). |
| **DofusConversionService** | Convertit valeur Dofus → Krosmoz en s’appuyant sur Getter (formule), Formula (évaluation), Limit (clamp). Utilisé par FormatterApplicator et par l’ancien pipeline si besoin. |

### 3.3 Lien Mapping ↔ Caractéristique

- Une règle de mapping peut avoir un **characteristic_id** (FK vers `characteristics`). Dans ce cas :
  - Les formatters **dofusdb_*** peuvent utiliser la **clé** de cette caractéristique (ou la clé dérivée par entité) pour appeler DofusConversionService.
  - Formule de conversion et limites viennent de la BDD caractéristiques (Getter/Limit), pas d’args figés dans le JSON.
- **Panneau 3 (Mapping)** dans l’UI doit donc exposer : source, entity, mapping_key, from_path, formatters, to (model.field), et **lien optionnel vers une caractéristique** (pour conversion/limites).

---

## 4. Base de données (cible)

### 4.1 Caractéristiques (existant, à conserver)

- **characteristics** : id, key, name, short_name, icon, color, unit, type, sort_order, description, etc.
- **characteristic_creature** / **characteristic_object** / **characteristic_spell** : par groupe et entité (entity = '*' ou 'monster', 'item', …) : min, max, default_value (expressions dynamiques), conversion_formula, formula_display, conversion_dofus_sample, conversion_krosmoz_sample, etc.

### 4.2 Mapping (remplace le mapping dans les JSON d’entité)

- **scrapping_entity_mappings** : source, entity, mapping_key, from_path, from_lang_aware, **characteristic_id** (nullable), formatters (JSON), sort_order.
- **scrapping_entity_mapping_targets** : scrapping_entity_mapping_id, target_model, target_field, sort_order.

Une règle = une ligne dans `scrapping_entity_mappings` + une ou plusieurs lignes dans `scrapping_entity_mapping_targets`. Le **mapping est unique par (source, entity, mapping_key)**.

---

## 5. UI Admin (cible)

### 5.1 Par caractéristique (écran existant à enrichir en 3 panneaux)

- **Panneau 1 — Limite et valeur**  
  Défaut, limite basse, limite haute : **expressions dynamiques**. Affichage formule (string).

- **Panneau 2 — Conversion**  
  - Formule de conversion (DofusDB → Krosmoz), expression dynamique, affichée en string.  
  - **Graphe** : axes issus des limites du panneau 1 ; menu d’options pour modifier les bornes **uniquement pour l’affichage** (aucun enregistrement).  
  - **Sous-panneau Échantillonnage** : tableau DofusDB (niveau → valeur) et Krosmoz (niveau → valeur). Niveaux par défaut DofusDB : 1, 40, 80, 120, 160, 200 ; Krosmoz : 1, 4, 8, 12, 16, 20. Permet de proposer des types de formules (linéaire, carré, exponentielle, etc.).

- **Panneau 3 — Mapping (nouveau)**  
  Toutes les infos pour lier **propriété API DofusDB** ↔ **propriété KrosmozJDR** : from_path, formatters, to (model.field), et **characteristic_id** pour réutiliser formules/limites. **Unique par entité** (même groupe peut avoir des mappings différents par entité).

### 5.2 Écran Mappings (liste / CRUD par source et entité)

- **Contrôleur** : ScrappingMappingController (ou équivalent) — liste des mappings par source/entité, création/édition/suppression de règles et de cibles.
- **Modèles** : ScrappingEntityMapping, ScrappingEntityMappingTarget.
- **Routes** : admin.scrapping-mappings.* (index, store, update, destroy, etc.).

---

## 6. Contrôleurs (cible)

| Contrôleur | Rôle |
|------------|------|
| **ScrappingController** / **ScrappingImportController** | Lancement import (Collect → Conversion → Validation → Intégration), utilisation de ConfigLoader (donc mapping depuis BDD). |
| **ScrappingMappingController** | CRUD des règles de mapping (source, entity, mapping_key, from_path, characteristic_id, formatters, targets). |
| **CharacteristicController** | CRUD caractéristiques, panneaux Limite/valeur, Conversion (formule + graphe + échantillonnage). À étendre avec le **panneau Mapping** (ou lien depuis la fiche caractéristique vers les règles de mapping qui référencent cette caractéristique). |
| **DofusConversionFormulaController** | Si encore utilisé : prévisualisation formules, suggestion de formules à partir des échantillons. |

---

## 7. Comparaison avec l’existant

### 7.1 Déjà en place

| Élément | Existant |
|--------|----------|
| **ConfigLoader** | Charge source + entité ; **mapping lu depuis BDD** via ScrappingMappingService (si injecté), sinon fallback `mapping` dans JSON. |
| **ScrappingMappingService** | Retourne le mapping pour (source, entity) au format attendu par ConversionService (key, from, to, formatters). |
| **Tables mapping** | `scrapping_entity_mappings` (avec characteristic_id), `scrapping_entity_mapping_targets`. |
| **ConversionService** | Applique mapping (path, formatters, to). Appelle DofusConversionService pour résistances batch (monster, class, item). |
| **FormatterApplicator** | Formatters dofusdb_level, dofusdb_life, dofusdb_attribute, dofusdb_ini ; utilisent DofusConversionService. Le lien avec la BDD se fait aujourd’hui via **convention** (clé construite à partir des args du formatter, ex. characteristicId + "_creature"), pas encore via **characteristic_id** de la règle de mapping. |
| **Characteristics** | Getter, Limit, Formula, DofusConversionService ; tables characteristics + characteristic_creature/object/spell avec min, max, default_value, conversion_formula, samples. |
| **UI admin caractéristiques** | CharacteristicController : index, show, update, formula-preview, suggest-conversion-formula, upload-icon. Pas encore structuré en 3 panneaux (Limite/valeur, Conversion+graphe+échantillons, Mapping). |
| **UI admin mappings** | ScrappingMappingController + Index.vue (liste par source/entité). Pas encore le panneau « Mapping » intégré à la fiche caractéristique. |

### 7.2 À faire / à renforcer

| Élément | Écart cible / action |
|--------|----------------------|
| **Lien formatter → characteristic** | Les formatters **dofusdb_*** utilisent aujourd’hui les **args** du formatter (ex. characteristicId) pour construire la clé. Il faut que le **pipeline** (ou le FormatterApplicator) puisse utiliser **characteristic_id** de la règle de mapping pour appeler le Getter/Conversion avec la bonne clé, sans dupliquer la logique dans les args. |
| **Panneaux UI caractéristique** | Réorganiser l’écran en **3 panneaux** : 1) Limite/valeur, 2) Conversion + graphe + échantillonnage, 3) Mapping (ou lien vers les règles qui référencent cette caractéristique). |
| **Graphe conversion** | Options d’affichage (bornes min/max pour le graphe) sans enregistrement. Déjà partiellement présent (formula-preview) ; à aligner avec la vision (limites du panneau 1, menu d’options). |
| **Échantillonnage** | Tableau DofusDB niv→valeur / Krosmoz niv→valeur avec niveaux par défaut et suggestion de formules (linéaire, carré, etc.) — à renforcer côté UI et éventuellement API (suggest-conversion-formula existe). |
| **Panneau Mapping dans la fiche caractéristique** | Soit on ajoute un bloc « Règles de mapping qui utilisent cette caractéristique » (lecture seule + lien vers admin scrapping-mappings), soit on intègre un mini-CRUD par entité. La vision indique que le panneau 3 contient « toutes les infos » pour faire le lien DofusDB ↔ Krosmoz ; la BDD est déjà prête (scrapping_entity_mappings.characteristic_id). |
| **ConfigLoader** | Déjà branché sur ScrappingMappingService dans l’app (AppServiceProvider) ; en tests/CLI sans container le fallback peut être un mapping vide ou lu depuis JSON. À documenter. |

### 7.3 Synthèse

- **Services** : Architecture déjà alignée (Scrapping : ConfigLoader, Mapping en BDD, Conversion, Validation, Intégration ; Characteristics : Getter, Limit, Formula, Conversion). Le chaînage FormatterApplicator ↔ DofusConversionService ↔ Getter/Limit est en place ; le pas restant est d’utiliser **characteristic_id** de la règle de mapping pour piloter ce chaînage au lieu de s’appuyer uniquement sur les args des formatters.
- **BDD** : Tables caractéristiques et tables de mapping en place ; characteristic_id nullable dans scrapping_entity_mappings déjà prévu.
- **UI** : Structuration en 3 panneaux (Limite/valeur, Conversion+graphe+échantillons, Mapping) et exposition du panneau Mapping (liste des règles par caractéristique ou par entité) restent à finaliser. L’écran admin scrapping-mappings existe (liste/CRUD par source/entité).

---

## 8. Résumé schématique

```
┌─────────────────────────────────────────────────────────────────────────────┐
│  ADMIN (UI)                                                                 │
│  • Caractéristiques : 3 panneaux (Limite/valeur, Conversion, Mapping)        │
│  • Scrapping Mappings : CRUD règles par source/entité, characteristic_id    │
└─────────────────────────────────────────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│  BDD                                                                         │
│  characteristics, characteristic_creature/object/spell                      │
│  scrapping_entity_mappings (source, entity, mapping_key, from_path,          │
│    characteristic_id, formatters), scrapping_entity_mapping_targets          │
└─────────────────────────────────────────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│  SCRAPPING                                                                   │
│  ConfigLoader (+ ScrappingMappingService) → mapping depuis BDD               │
│  CollectService → ConversionService (mapping + FormatterApplicator)           │
│  FormatterApplicator ↔ DofusConversionService (Getter, Formula, Limit)      │
│  ValidationService → IntegrationService ; Orchestrator enchaîne tout        │
└─────────────────────────────────────────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│  CHARACTERISTICS                                                             │
│  Getter (définitions, limites, formules) ; Limit (validation, clamp)         │
│  Formula (expression dynamique) ; DofusConversionService (Dofus → Krosmoz)  │
└─────────────────────────────────────────────────────────────────────────────┘
```

Ce document peut servir de référence pour les prochaines évolutions (utilisation de `characteristic_id` dans le pipeline, refonte UI en 3 panneaux, renforcement échantillonnage et graphe).
