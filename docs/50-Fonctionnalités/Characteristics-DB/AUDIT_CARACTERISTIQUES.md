# Audit des caractéristiques — Exhaustivité, expressions dynamiques, types

Ce document synthétise une **vérification approfondie** des données de caractéristiques (seeders, cohérence, formules, types). Dernière mise à jour : 2026-02.

**Fichiers concernés :** `database/seeders/data/characteristics.php`, `characteristic_creature.php`, `characteristic_object.php`, `characteristic_spell.php`, `characteristic_icons_colors.php`.

---

## 1. Exhaustivité — Table générale (characteristics.php)

| Critère | Résultat |
|--------|----------|
| **Nombre d’entrées** | 106 |
| **Répartition par groupe** | creature 50, object 40, spell 16 |
| **Clés dupliquées** | Aucune |
| **name** | 106/106 renseignés |
| **short_name** | 106/106 renseignés |
| **helper** | 106/106 renseignés |
| **descriptions** | Complétées au seed via `characteristic_icons_colors.php` pour les principales |
| **icon / color** | Complétés au seed via `characteristic_icons_colors.php` (icônes dans `storage/app/public/images/icons/characteristics/`) |
| **type** | 102 × `int`, 4 × `bool` (`sight_line_spell`, `is_magic_spell`, `po_editable_spell`, `number_between_two_cast_editable_spell`) |
| **sort_order** | Présent pour toutes les entrées, pas de doublon par groupe |
| **unit** | 7 entrées avec unité (ex. kamas, pods) |
| **linked_to_key** | 1 entrée liée : `level_spell` → `level_creature` |

**Types attendus par le service Limite :** `boolean`/`bool`, `list`/`array`, `string`/`int`/`integer`. Les types `int` et `bool` des données sont cohérents.

---

## 2. Cohérence clés / groupe (fichiers de groupe)

| Fichier | Lignes | Clés uniques | Vérification |
|---------|--------|--------------|--------------|
| **characteristic_creature.php** | 51 | 50 | Toutes les clés du fichier existent dans `characteristics.php` avec `group` = creature. Toutes les clés main du groupe creature (hors liées) ont au moins une ligne dans ce fichier. |
| **characteristic_object.php** | 43 | 40 | Idem pour le groupe object. |
| **characteristic_spell.php** | 15 | 15 | Idem pour le groupe spell. La 16ᵉ caractéristique spell est `level_spell`, qui est **liée** à `level_creature` : elle n’a pas de ligne dans le fichier spell (héritage depuis la maître). |

Aucune clé de groupe ne pointe vers une caractéristique manquante ou vers un mauvais groupe.

---

## 3. Expressions dynamiques (formules, min/max, conversion)

### 3.1 Conversion (conversion_formula)

- **Valeurs NULL** : Aucune. Toutes les lignes des trois fichiers de groupe ont une `conversion_formula` (soit formule, soit table JSON).
- **Validation** : Les formules et tables ont été passées à `FormulaResolutionService::validateFormula()` : **0 erreur**.
- **Conventions** : Pass-through `[d]`, table par `d` ou `level`, formules avec `floor`, `pow`, `sqrt`, etc. Champs 0/1 (sorts) : `min(1,max(0,round([d])))`.

### 3.2 Min / Max

- **Valeurs fixes** : Nombre ou chaîne numérique (ex. `'1'`, `'12'`).
- **Valeurs dynamiques** : Formule ou **table JSON** (ex. `life_creature` : `max` = table par `level_creature`). Ces champs ont été validés : **0 erreur**.
- **Type bool** : Les 4 caractéristiques `bool` du groupe spell ont `min` = 0 et `max` = 1 dans les données.

### 3.3 Formula (champ « valeur calculée »)

- **life_creature** : La formule était tronquée (`'[char'`) ; elle a été corrigée en `[vitality_creature]*10+[de_vie_creature]` (alignée sur le formula_display « Vitalité×10 + dés de vie »).
- Les autres lignes ont soit une formule cohérente, soit `NULL` (pas d’évaluation).

### 3.4 Syntaxe des formules

- **Formule simple** : Variables `[id]` ou `[key]` (ex. `[level_creature]`, `[d]`), opérateurs et fonctions autorisées (floor, ceil, round, sqrt, pow, min, max, etc.). Voir [SYNTAXE_FORMULES_CARACTERISTIQUES.md](../../10-BestPractices/SYNTAXE_FORMULES_CARACTERISTIQUES.md).
- **Table JSON** : Clé `characteristic` (variable de référence) + clés numériques (seuils) → valeur ou sous-formule. Décodage via `FormulaConfigDecoder`.

---

## 4. conversion_function et value_available

- **conversion_function** : Champ géré dans `CharacteristicGroupSeeder::commonAttributes`. Peut être renseigné dans les fichiers de données pour appliquer une fonction du `ConversionFunctionRegistry` après la formule.
- **value_available** : Présent dans les fichiers object et spell pour les caractéristiques de type liste (ex. `element_spell`, `category_spell`). Cohérent avec le type et l’usage dans le service Limite.

---

## 5. Icônes et couleurs

- **characteristic_icons_colors.php** : Définit des valeurs par défaut pour `icon`, `color` et `descriptions` par clé. Utilisé par `CharacteristicSeeder` lorsque la valeur en base ou dans `characteristics.php` est NULL.
- **Fichiers physiques** : Les icônes utilisées (ex. `life_temp.svg`, `shield.svg`, `casting_time.svg`) sont présentes dans `storage/app/public/images/icons/characteristics/` (copiées depuis `icons/caracteristiques/old/`).

---

## 6. Points d’attention et recommandations

| Point | Statut | Recommandation |
|-------|--------|----------------|
| Formule life_creature | Corrigé | Conserver `[vitality_creature]*10+[de_vie_creature]` ou adapter si les variables d’évaluation utilisent des noms courts ([vitality], [de_vie]) selon le contexte. |
| Caractéristiques liées | OK | `level_spell` est la seule liée ; pas de ligne dans characteristic_spell (héritage maître). |
| Type list | Non utilisé en main | Les types en base sont `int` ou `bool`. Si des listes sont ajoutées (ex. choix dans value_available), vérifier que le service Limite et l’UI gèrent bien le type `list`. |
| Export BDD → seeders | À garder en tête | Après édition en admin, `php artisan scrapping:seeders:export --characteristics` (alias legacy : `db:export-seeder-data`) écrase les fichiers data ; les compléments de `characteristic_icons_colors.php` sont réappliqués au prochain seed si les champs exportés sont NULL. |

---

## 7. Résumé

- **Exhaustivité** : 106 caractéristiques, champs obligatoires (name, short_name, helper) remplis, icon/color/descriptions complétés par défaut via characteristic_icons_colors.php.
- **Cohérence** : Aucune incohérence clé/groupe ; caractéristique liée gérée correctement.
- **Expressions dynamiques** : Aucune erreur de validation sur les conversion_formula et min/max dynamiques ; formule life_creature corrigée.
- **Types** : Alignés avec le service Limite (int, bool) ; value_available présent où nécessaire.

Pour relancer une vérification des formules après modification des données, exécuter un script qui charge les trois fichiers de groupe et appelle `FormulaResolutionService::validateFormula()` sur chaque `conversion_formula` et sur chaque min/max de type formule ou table.
