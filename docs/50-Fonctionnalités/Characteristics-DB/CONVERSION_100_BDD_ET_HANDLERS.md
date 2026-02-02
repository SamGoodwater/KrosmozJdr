# Conversion 100 % pilotée par la BDD et handlers nommés

**Objectif** : Que la conversion DofusDB → KrosmozJDR (côté caractéristiques) ne dépende que de la base de données. Les formules, limites et mappings sont en BDD ; les cas complexes (ex. résistances) sont gérés par des **handlers nommés** enregistrés en BDD et exécutés en PHP.

---

## 1. Principe : tout en BDD

| Donnée | Où | Consommateur |
|--------|-----|---------------|
| Limites (min, max, required) par caractéristique/entité | `characteristics` + `characteristic_entities` | CharacteristicService |
| Formules de conversion (formula_type, parameters, conversion_formula) | `dofusdb_conversion_formulas` | DofusDbConversionFormulaService → DofusDbConversionFormulas |
| **Handler nommé** (fonction complexe) | `dofusdb_conversion_formulas.handler_name` | Registry PHP → callable |
| Mapping Dofus → Krosmoz (ex. elementId → res_*) | À migrer en BDD (table dédiée ou paramètres JSON par handler) | Handlers / service |

**Config fichiers** : ne plus utiliser `config/dofusdb_conversion.php` (ou uniquement en fallback vide) pour les formules et limites. Les mappings (effectId → characteristic_id, elementId → characteristic_id) peuvent être soit une table `dofusdb_conversion_mappings`, soit des paramètres stockés en BDD par formule/handler.

---

## 2. Handlers nommés (fonctions complexes)

Lorsqu’une simple formule (linéaire, sqrt, ratio, expression `[d]`/`[level]`) ne suffit pas, on enregistre en BDD le **nom d’un handler** PHP. Le moteur de conversion appelle ce handler avec les données Dofus et le contexte (entité, niveau JDR, etc.) ; le handler retourne la valeur (ou les paires champ/valeur) Krosmoz.

### 2.1 Schéma

- **Colonne** `handler_name` (nullable string) dans `dofusdb_conversion_formulas`.
- Si `handler_name` est renseigné, la conversion **ignore** `formula_type`, `conversion_formula`, `parameters` pour cette ligne et appelle le handler enregistré en PHP.
- **Registry** : une classe (ex. `ConversionHandlerRegistry`) associe chaque nom à un callable (classe + méthode ou fonction). Seuls les noms enregistrés sont autorisés (liste blanche).

Exemple en BDD :

| characteristic_id | entity  | formula_type | handler_name                    | parameters (optionnel)      |
|-------------------|---------|--------------|----------------------------------|-----------------------------|
| res_neutre        | monster | custom       | resistance_dofus_to_krosmoz     | {"max_resistant": 3, ...}   |

Le code : `$registry->get('resistance_dofus_to_krosmoz')` → callable ; appel avec `(rawDofusData, entityType, $parameters)`.

### 2.2 Signature type d’un handler

- **Entrée** : `(array $dofusData, string $entityType, array $parameters)`  
  - `$dofusData` : données brutes Dofus pour l’entité (ex. grades pour un monstre, champs résistances par élément).  
  - `$entityType` : monster, class, item.  
  - `$parameters` : JSON décodé depuis `dofusdb_conversion_formulas.parameters` (seuils, plafonds, etc.).
- **Sortie** :  
  - Soit une **valeur unique** (int/string) pour une caractéristique simple.  
  - Soit un **tableau [ characteristic_id => value ]** pour un handler qui remplit plusieurs caractéristiques (ex. toutes les res_* et res_fixe_*).

Le moteur (DofusDbConversionFormulas ou ConversionService) doit prévoir d’appeler le handler par (characteristic_id, entity) et, si la sortie est un tableau, écrire toutes les paires dans la structure convertie.

---

## 3. Résistances : Dofus → Krosmoz

### 3.1 Côté KrosmozJDR

- **res_*** (string) : pourcentage de résistance, limité à **4 valeurs** :
  - **50** = résistant  
  - **100** = invulnérable  
  - **-50** = faiblesse  
  - **-100** = vulnérable  
  - (et **0** = neutre)
- **res_fixe_*** (text) : valeur fixe (nombre, ex. réduction de dégâts fixe).

Donc par élément (neutre, terre, feu, air, eau) : un **%** (0, 50, 100, -50, -100) et une **valeur fixe**.

### 3.2 Côté Dofus

- Les % sont **variés** (ex. 12 %, -35 %, 80 %).
- Il faut les **ramener** à un des 4 tiers (ou 0) et éventuellement garder une partie en **fixe** pour ne pas perdre trop d’info.

### 3.3 Règle métier : ne pas surcharger une créature

- Il ne faut **pas** donner trop de résistances ou de faiblesses **fortes** sur trop d’éléments pour la même créature.
- Exemples de contraintes (à rendre configurables en BDD via `parameters`) :
  - Nombre max d’éléments **invulnérables** (100) par créature (ex. 1).
  - Nombre max d’éléments **résistants** (50) (ex. 3).
  - Nombre max d’éléments **vulnérables** (-100) (ex. 2).
  - Nombre max d’éléments **faibles** (-50) (ex. 3).
- Si Dofus donne plus de résistances/faiblesses que les plafonds, le handler **priorise** (ex. garder les plus fortes en %, ou les éléments les plus cohérents avec le niveau) et **plafonne** le nombre par tier.

### 3.4 Handler `resistance_dofus_to_krosmoz`

- **Rôle** : à partir des résistances Dofus (par élément : % et éventuellement fixe), produire pour chaque élément Krosmoz (res_neutre, res_terre, … et res_fixe_neutre, …) une valeur **%** (0, 50, 100, -50, -100) et une valeur **fixe**.
- **Algorithme type** :
  1. Mapping **elementId Dofus → characteristic_id** (res_neutre, res_terre, …) : soit en BDD (table ou paramètres), soit en dur dans le handler.
  2. Pour chaque élément Dofus : convertir le **% Dofus** en **tier Krosmoz** (seuils configurables, ex. ]40, 100] → 50, ]90, 100] → 100, [-100, -40[ → -50, [-40, 0[ → -100 ? ou l’inverse). Une partie peut être “mise” en **fixe** pour éviter de sur-représenter un tier.
  3. Appliquer les **plafonds** : par créature, limiter le nombre d’éléments par tier (invulnérable, résistant, vulnérable, faiblesse). Si dépassement, modifier certaines valeurs (ex. repasser à 0 ou au tier inférieur) selon une règle (priorité niveau, priorité élément, etc.).
  4. Retourner un tableau `[ 'res_neutre' => '50', 'res_fixe_neutre' => '5', … ]` (ou structure équivalente avec res_fixe_*).
- **Paramètres** (stockés en BDD dans `dofusdb_conversion_formulas.parameters` pour ce handler) :
  - Seuils **% Dofus → tier** (ex. thresholds: { 50: [40, 90], 100: [90, 101], -50: [-90, -40], -100: [-101, -90] }).
  - **Plafonds** par créature : max_invulnerable, max_resistant, max_weak, max_vulnerable (entiers).
  - Option : **priorité** (ex. garder les plus hauts % en valeur absolue quand on plafonne).

---

## 4. Plan d’implémentation

1. **Migration** : ajouter `handler_name` (nullable string, 64) à `dofusdb_conversion_formulas`.
2. **ConversionHandlerRegistry** : classe qui enregistre les callables (nom → callable). Enregistrer au moins `resistance_dofus_to_krosmoz`.
3. **DofusDbConversionFormulas** (ou service appelant les formules) :  
   - Si une ligne a `handler_name` non vide, appeler le handler via le registry avec (dofusData, entity, parameters).  
   - Sinon, garder le comportement actuel (conversion_formula, formula_type + parameters, fallback config).
4. **Handler résistances** : implémenter la logique décrite en 3.4 (mapping élément, seuils %, plafonds par créature), avec paramètres en BDD.
5. **Migration des données** : déplacer les mappings actuellement en config (element_id_to_resistance, effect_id_to_characteristic) vers une table ou des paramètres JSON en BDD pour que la conversion ne lise plus la config (ou seulement en secours).

6. **Implémenté** : colonne `handler_name` dans `dofusdb_conversion_formulas`, `ConversionHandlerRegistry`, handler `resistance_dofus_to_krosmoz`, et `DofusDbConversionFormulas::convertResistancesBatch()` pour appeler le handler quand (res_neutre, entity) a un handler_name renseigné.

---

## 5. Résumé

- **Conversion 100 % BDD** : limites et formules viennent des tables `characteristics`, `characteristic_entities`, `dofusdb_conversion_formulas`. Pas de dépendance aux configs pour le cœur de la conversion.
- **Handlers nommés** : pour les cas complexes, on stocke un `handler_name` en BDD ; un registry PHP exécute le callable associé avec les données Dofus et le contexte.
- **Résistances** : handler dédié qui mappe les % Dofus vers les 4 tiers Krosmoz (50, 100, -50, -100) + valeur fixe, et applique des **plafonds par créature** pour ne pas donner trop de résistances/faiblesses sur trop d’éléments.
