# Où placer les formules de conversion DofusDB → KrosmozJDR

## Contexte

- **`config/characteristics.php`** définit les **valeurs limites** (min/max) et les **formules de génération** (ex. `[vitality] * 7` pour la vie) utilisées pour la **validation** et la **génération** de données KrosmozJDR.
- Il ne contient pas les **formules / fonctions** qui transforment les données brutes DofusDB en données exploitables KrosmozJDR (ex. mapping effectId → caractéristique, conversion résistance DofusDB → res_terre, etc.).

Ce document indique où placer ces formules et comment les relier aux limites de `characteristics.php`.

---

## Source unique pour les limites (pas de doublon)

**Une seule source de vérité pour les limites min/max et champs requis : `config/characteristics.php` (clé `characteristics.characteristics`).**

- **V2** : `ValidationService` et `DofusDbConversionFormulas` lisent uniquement `config('characteristics.characteristics')` pour les bornes et champs requis. Aucune copie des limites dans `config/dofusdb_conversion.php` : ce fichier contient seulement des **mappings** (effectId → champ, elementId → res_*) et `limits_source` => `'characteristics'` pour indiquer d’où prendre les min/max.
- **`config/dofusdb_conversion.php`** : pas de redéfinition des min/max ; la section optionnelle `limits` (vide par défaut) ne sert que si `limits_source` === `'local'` (cas exceptionnel). En pratique, toutes les limites viennent de `characteristics.php`.

**Autres configs susceptibles de contenir des règles proches (à ne pas dupliquer) :**

- **`config/characteristics/*.json`** (characteristics.json, formulas.json, entity_mappings.json, validation_rules.json) : **non chargés par Laravel** (aucun `config('characteristics.definitions')` etc. dans l’app). Ce sont des définitions parallèles ou legacy ; pour éviter les doublons, toute règle de limite ou de validation doit rester dans **`config/characteristics.php`**.
- **`config/scrapping.php`** (section `data_conversion`) et **`app/Services/Scrapping/DataConversion/config.php`** : contiennent des règles par entité (min_value, max_value, required_fields) pour l’ancien pipeline DataConversion. Ce sont des doublons potentiels ; à terme, faire converger vers `characteristics.php` ou documenter clairement quel fichier fait autorité pour quel code.

---

## Recommandation : séparer données et code

| Rôle | Où | Contenu |
|------|-----|--------|
| **Données de conversion** (mapping, constantes) | Config | Tables de correspondance DofusDB ↔ KrosmozJDR (effectId → champ, elementId → res_*, etc.), bornes à appliquer après conversion (ou référence à characteristics). |
| **Implémentation des formules** (PHP) | Service(s) | Fonctions qui lisent la config et transforment une valeur DofusDB en valeur KrosmozJDR (clamp, mapping, agrégation d’effets, etc.). |

Les **limites** (min/max) restent la source de vérité dans **`config/characteristics.php`** ; la conversion peut s’y référer pour appliquer les mêmes bornes après transformation.

---

## 1. Données de conversion (config)

**Fichier recommandé : `config/dofusdb_conversion.php`**

- Tables de mapping : effectId → champ KrosmozJDR, elementId → res_*, etc.
- Optionnel : bornes spécifiques à la conversion (sinon on utilise celles de `characteristics`).
- Pas de logique métier : uniquement tableaux et constantes.

Exemple de structure :

```php
return [
    'effect_id_to_characteristic' => [
        118 => 'strength',
        126 => 'intelligence',
        // ...
    ],
    'element_id_to_resistance' => [
        1 => 'res_terre',
        2 => 'res_feu',
        3 => 'res_air',
        4 => 'res_eau',
        // -1 ou 0 => res_neutre
    ],
    'limits_source' => 'characteristics', // ou 'local' si bornes définies ici
];
```

On peut aussi ajouter une clé sous **`config/scrapping.php`** (ex. `dofusdb_conversion`) qui charge ce fichier ou duplique ces clés, selon la préférence du projet.

---

## 2. Implémentation des formules (PHP)

**Emplacement : `App\Services\Scrapping\V2\Conversion\`**

Deux options complémentaires :

### A. Formatters dans `FormatterApplicator`

- Pour les conversions **simples** (clamp, type, troncature, etc.) déjà gérées par les formatters existants : `toString`, `pickLang`, `clampInt`, `truncate`, etc.
- Pour de **nouvelles conversions simples** (ex. « valeur DofusDB / 10 → entier KrosmozJDR »), ajouter un formatter nommé dans `FormatterApplicator` et le référencer dans les JSON d’entités (`resources/scrapping/v2/sources/dofusdb/entities/*.json`).

### B. Service dédié `DofusDbConversionFormulas`

- Pour les formules **plus complexes** (effets DofusDB → bonus KrosmozJDR, agrégation de plusieurs champs, utilisation des tables de mapping).
- Ce service :
  - lit `config('dofusdb_conversion')` (et éventuellement `config('characteristics.characteristics')` pour les limites) ;
  - expose des méthodes publiques (ex. `convertResistance(int $elementId, $value): int`, `effectsToBonus(array $effects): array`) ;
  - est appelé par `FormatterApplicator` lorsque un formatter correspond à une de ces formules (ex. formatter `dofusdb_resistance_to_krosmoz` qui délègue à `DofusDbConversionFormulas::convertResistance`).

Ainsi, les **formules** au sens « comment passer de DofusDB à KrosmozJDR » vivent dans ce service ; les **limites** restent dans `config/characteristics.php` et sont utilisées soit par `ValidationService`, soit par ce service de conversion après calcul.

---

## 3. Utilisation dans le pipeline V2

1. **Config**  
   - `config/characteristics.php` : limites et règles de validation (inchangé).  
   - `config/dofusdb_conversion.php` : mapping DofusDB → KrosmozJDR (données seules).

2. **Conversion**  
   - Les mappings JSON des entités (`resources/scrapping/v2/.../entities/*.json`) définissent **path** → **model/field** et **formatters**.  
   - Les formatters nommés sont implémentés dans `FormatterApplicator` (et éventuellement délèguent à `DofusDbConversionFormulas` pour les formules complexes).

3. **Validation**  
   - `ValidationService` continue d’utiliser `config('characteristics.characteristics')` pour vérifier min/max et champs requis sur les données **déjà converties**.

4. **Cohérence**  
   - Les formules de conversion peuvent appliquer les mêmes min/max que la validation en lisant les mêmes entrées dans `characteristics` (ex. par entité : monster, class, item), afin que « formule de conversion » et « règles de validation » restent alignées.

---

## Résumé

| Besoin | Fichier / classe |
|--------|-------------------|
| Limites min/max, champs requis | `config/characteristics.php` (existant) |
| Tables de correspondance DofusDB ↔ KrosmozJDR | `config/dofusdb_conversion.php` (recommandé) |
| Formules simples (clamp, type, troncature) | `FormatterApplicator` (formatters nommés) |
| Formules complexes (effets → bonus, résistances, etc.) | `App\Services\Scrapping\V2\Conversion\DofusDbConversionFormulas` |
| Déclaration du champ converti + formatter à utiliser | Fichiers JSON d’entités sous `resources/scrapping/v2/sources/dofusdb/entities/` |

Les **formules** au sens « comment calculer / transformer » sont donc du **code PHP** dans `FormatterApplicator` et/ou `DofusDbConversionFormulas` ; les **données** (mapping, constantes) sont dans la **config** (`dofusdb_conversion.php`), et les **limites** restent dans **`config/characteristics.php`**.
