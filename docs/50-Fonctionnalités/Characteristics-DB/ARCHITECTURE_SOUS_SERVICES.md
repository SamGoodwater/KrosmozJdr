# Architecture : les 4 sous-services de caractéristiques

Ce document décrit les **quatre sous-services** qui permettent d’utiliser et de manipuler les données des caractéristiques. Ils s’appuient sur les tables centrales (`characteristics`, `characteristic_creature`, `characteristic_object`, `characteristic_spell`) et constituent le cœur technique du système.

**Vue d’ensemble :** [PRESENTATION_SERVICE_CARACTERISTIQUES.md](./PRESENTATION_SERVICE_CARACTERISTIQUES.md).

---

## 1. Vue d’ensemble

| Service | Rôle principal | Entrées typiques | Sorties |
|--------|----------------|------------------|---------|
| **Getter** | Récupérer les définitions et métadonnées des caractéristiques. | Clé de caractéristique, entité (monster, item, spell, etc.). | Définition complète (min, max, formula, conversion_formula, type, etc.). |
| **Limit** | Vérifier qu’une valeur est valide pour une caractéristique donnée (selon son type), ou valider tout un ensemble de champs d’une entité. | Entité, caractéristique (ou clé), valeur(s). | Validation OK / KO + erreurs ; ou valeur clampée. |
| **Formula** | Résoudre les formules de façon sécurisée (sans `eval`). | Formule (ou table JSON), variables. | Valeur numérique ou tableau de valeurs (courbe). |
| **Conversion** | Convertir des valeurs du jeu vidéo Dofus en valeurs exploitables pour Krosmoz JDR. | Valeur(s) Dofus, type d’entité. | Valeur(s) Krosmoz (niveau, vie, rareté, etc.). |

Le **Conversion** s’appuie sur le Getter (pour lire la formule de conversion et les limites), le service Formula (pour évaluer la formule) et le service Limit (pour clamper le résultat). Les **propriétés de conversion** (champs en BDD et format des formules) sont décrites dans [PROPRIETES_CONVERSION_DOFUS_KROSMOZ.md](./PROPRIETES_CONVERSION_DOFUS_KROSMOZ.md).

---

## 2. Getter (CharacteristicGetterService)

**Rôle :** Fournir les **définitions** des caractéristiques par clé et par entité. C’est la porte d’entrée pour toute lecture des paramètres (limites, formules, conversion, type, etc.).

**Principales méthodes :**

- `getDefinition(string $characteristicKey, string $entity): ?array`  
  Retourne la définition fusionnée (table générale + ligne de groupe, avec surcharge par entité). Contient notamment : `key`, `name`, `type`, `min`, `max`, `formula`, `default_value`, `conversion_formula`, et pour le groupe object : `forgemagie_allowed`, `forgemagie_max`, `base_price_per_unit`, `rune_price_per_unit`, `allowed_item_type_ids`.
- `getLimits(string $characteristicKey, string $entity, array $variables = []): ?array`  
  Retourne `['min' => int, 'max' => int]` après évaluation des champs min/max (valeur fixe, formule ou table).
- `getLimitsByField(string $field, string $entity, ...): ?array`  
  Même chose en résolvant d’abord le nom de champ (ou nom court) en clé de caractéristique.
- `getConversionFormula(string $characteristicKey, string $entity): ?string`  
  Retourne la formule de conversion Dofus → Krosmoz pour cette caractéristique et cette entité.
- `getGroupForEntity(string $entity): string`  
  Retourne le groupe (`creature`, `object`, `spell`) pour une entité donnée.

La définition peut provenir d’une ligne **entity = '*'** (défaut du groupe) et d’une ligne **entity = entité précise** (ex. `monster`) : les propriétés non vides de la ligne entité l’emportent (voir [PRESENTATION_SERVICE_CARACTERISTIQUES.md § 4.1](./PRESENTATION_SERVICE_CARACTERISTIQUES.md#41-surcharge-par-entité-dans-un-groupe)).

**Fichier :** `App\Services\Characteristic\Getter\CharacteristicGetterService`.

---

## 3. Limit (CharacteristicLimitService)

**Rôle :** Vérifier qu’une **valeur** de caractéristique est **valide** par rapport aux limites définies pour cette caractéristique et cette entité. Le type de la caractéristique (`boolean`, `list`, `string`) détermine la règle de validation. On peut aussi valider **l’ensemble des champs** d’une entité d’un coup (ex. données converties après scrapping).

**Règles par type (comportement cible) :**

| Type de la caractéristique | Validation |
|---------------------------|------------|
| **boolean** | La valeur doit être interprétable comme vrai ou faux (true/false). |
| **list** | La valeur doit appartenir à la liste des valeurs possibles (`value_available` ou équivalent) définie pour cette caractéristique. |
| **string** | La valeur doit être comprise entre **min** et **max** (bornes éventuellement évaluées si ce sont des formules ou des tables). |

**Entrées :** entité, caractéristique (clé ou champ), valeur. Pour la validation globale : tableau de données (par modèle ou aplati) + type d’entité.

**Sorties :**

- Validation unitaire : indiquer si la valeur est valide (et éventuellement un message d’erreur).
- Validation globale : `ValidationResult` (ok ou fail avec liste d’erreurs par champ).
- **Clamp** : pour une caractéristique numérique (string avec min/max), retourner la valeur ramenée dans l’intervalle [min, max].

**Méthodes actuelles (à aligner sur les types) :**

- `validate(array $convertedData, string $entityType): ValidationResult` — valide tous les champs dont une définition existe pour l’entité (actuellement en min/max uniquement).
- `clamp(string $characteristicKey, int $value, string $entity): int` — ramène la valeur dans les bornes min/max.

L’implémentation actuelle repose sur min/max pour les champs numériques. L’extension aux types **boolean** et **list** (vérification true/false et appartenance à la liste) est à prévoir pour refléter entièrement ce document.

**Fichier :** `App\Services\Characteristic\Limit\CharacteristicLimitService`.  
**DTO :** `App\Services\Characteristic\Limit\ValidationResult`.

---

## 4. Formula (CharacteristicFormulaService / FormulaResolutionService)

**Rôle :** Résoudre les **formules** de caractéristiques de façon **sécurisée** (sans `eval` PHP brut). Les formules peuvent être une expression simple ou une **table par caractéristique** (JSON avec seuils et valeurs ou sous-formules).

**Capacités :**

- **Opérateurs** : principaux opérateurs arithmétiques et de comparaison (+, -, *, /, etc.).
- **Fonctions mathématiques** : celles couramment utilisées en PHP (ex. `floor`, `ceil`, `round`, `sqrt`, `abs`, `min`, `max`, `pow`, etc.), dans une liste blanche pour la sécurité.
- **Aléatoire** : dés `ndX` (ex. `1d6`, `2d10`) et plages `[x1-x2]` (tirage aléatoire entre x1 et x2).
- **Variables** : repérées par `[nom]` (ex. `[level]`, `[vitality]`), remplacées par les valeurs fournies au moment de l’évaluation.

**Entrées :** formule (chaîne ou JSON table), map de variables (ex. `['level' => 5, 'vitality' => 12]`).

**Sorties :** valeur numérique (float/int) ou tableau de valeurs (ex. courbe niveau → valeur pour l’API formula-preview).

**Services impliqués :**

- `CharacteristicFormulaService` : façade (evaluate, validateFormula, evaluateForVariableRange).
- `FormulaResolutionService` : résolution effective (formule simple ou table).
- `FormulaConfigDecoder` : décodage formule vs table JSON (structure « characteristic » + seuils).
- `SafeExpressionEvaluator` : évaluation de l’expression après substitution des variables.
- `FormulaVariableResolver` : résolution des variables `[id]` dans l’expression.

**Fichiers :**  
`App\Services\Characteristic\Formula\CharacteristicFormulaService`,  
`App\Services\Characteristic\Formula\FormulaResolutionService`,  
`App\Services\Characteristic\Formula\FormulaConfigDecoder`,  
`App\Services\Characteristic\Formula\SafeExpressionEvaluator`,  
`App\Services\Characteristic\Formula\FormulaVariableResolver`.

**Syntaxe détaillée :** [SYNTAXE_FORMULES_CARACTERISTIQUES.md](../../10-BestPractices/SYNTAXE_FORMULES_CARACTERISTIQUES.md).

---

## 5. Conversion (DofusConversionService)

**Rôle :** Prendre en entrée des **valeurs de caractéristiques du jeu vidéo Dofus** et les **convertir** en valeurs exploitables pour Krosmoz JDR (niveau, vie, attributs, initiative, rareté, etc.). La conversion s’appuie sur une **computation** (formule : fixe, formule ou table) issue des **propriétés de la caractéristique** (champ `conversion_formula` ou propriétés dédiées en BDD).

**Flux :**

1. Pour une entité et une caractéristique cible (ex. niveau, vie), le service Conversion demande au **Getter** la formule (ou la config) de conversion.
2. Il utilise le service **Formula** pour évaluer cette formule avec la valeur Dofus (et éventuellement d’autres variables déjà converties, ex. niveau).
3. Il utilise le service **Limit** pour **clamper** le résultat dans les bornes autorisées pour cette caractéristique et cette entité.

**Exemples :** niveau Dofus → niveau Krosmoz (ex. d/10), vie Dofus → vie Krosmoz (formule ou table), rareté dérivée du niveau Krosmoz, etc.

Les **propriétés de conversion** (champ `conversion_formula` au format fixe / formule / table, variables `d`, et échantillons par niveau pour graphiques) sont détaillées dans [PROPRIETES_CONVERSION_DOFUS_KROSMOZ.md](./PROPRIETES_CONVERSION_DOFUS_KROSMOZ.md). Ce paramétrage est au cœur du système et doit être clairement décrit pour définir les besoins (mapping Dofus → Krosmoz par caractéristique et par entité).

**Fichier :** `App\Services\Characteristic\Conversion\DofusConversionService`.

---

## 6. Export des données vers les seeders

Pour que l’initialisation du projet dispose déjà de caractéristiques bien définies, la base de données peut être **exportée** vers des fichiers PHP utilisables par les seeders.

**Commande :**  
`php artisan db:export-seeder-data`  
Options : `--characteristics` (exporte uniquement les caractéristiques), `--formulas` (formules de conversion dans les tables de groupe), etc.

**Fichiers générés (pour les caractéristiques) :**  
`database/seeders/data/characteristics.php`,  
`characteristic_creature.php`,  
`characteristic_object.php`,  
`characteristic_spell.php`.

Voir [SEEDERS_DONNEES.md](./SEEDERS_DONNEES.md) pour le contenu de ces fichiers et la commande d’export.

---

## 7. Restriction par types d’items (item_types) — une seule solution, pas de doublon

**Objectif métier :** Pour les caractéristiques du groupe **object**, indiquer **quels types d’items** (équipements) peuvent porter cette caractéristique (ex. « PA bonus » uniquement sur armes et amulettes).

**Solution retenue (source unique de vérité) :** La table pivot **characteristic_object_item_type** associe chaque ligne `characteristic_object` aux **id** de la table **item_types**. Si une définition n’a aucune entrée dans cette pivot, la caractéristique s’applique à tous les types ; sinon elle ne s’applique qu’aux types listés. Le Getter expose cette liste sous la clé **allowed_item_type_ids** dans la définition (voir [TYPES_VALEURS_ET_CONTENU_JSON.md](./TYPES_VALEURS_ET_CONTENU_JSON.md) § 5.3).

---

## 8. Suite

- **Propriétés de conversion** (formule fixe/formule/table, échantillons Dofus/Krosmoz, génération de formule) : [PROPRIETES_CONVERSION_DOFUS_KROSMOZ.md](./PROPRIETES_CONVERSION_DOFUS_KROSMOZ.md).
- **Types de valeurs** (boolean, list, string) et contenu JSON : [TYPES_VALEURS_ET_CONTENU_JSON.md](./TYPES_VALEURS_ET_CONTENU_JSON.md).
- **Présentation** et groupes d’entités : [PRESENTATION_SERVICE_CARACTERISTIQUES.md](./PRESENTATION_SERVICE_CARACTERISTIQUES.md).
