# Audit code : caractéristiques (doublons, obsolète, à optimiser)

Ce document recense les écarts et points d’attention repérés dans le code du service de caractéristiques après plusieurs évolutions. À traiter pour aligner le code sur la documentation et supprimer les doublons ou parties obsolètes.

**Référence fonctionnelle :** [ARCHITECTURE_SOUS_SERVICES.md](./ARCHITECTURE_SOUS_SERVICES.md), [TYPES_VALEURS_ET_CONTENU_JSON.md](./TYPES_VALEURS_ET_CONTENU_JSON.md).

---

## 1. Service Limit : validation par type (boolean, list, string)

**Documentation :** [ARCHITECTURE_SOUS_SERVICES.md § 3](./ARCHITECTURE_SOUS_SERVICES.md#3-limit-characteristiclimitservice) décrit une validation selon le **type** de la caractéristique :
- **boolean** : valeur = true/false ;
- **list** : valeur dans la liste des valeurs possibles (`value_available`) ;
- **string** : valeur entre min et max.

**Code actuel :** `CharacteristicLimitService` ne fait que :
- `validate()` : min/max pour chaque champ (valeur numérique) ;
- `clamp()` : ramener une valeur dans [min, max].

**À faire :** Étendre le service pour prendre en compte le **type** (lu depuis la définition Getter) et appliquer la règle correspondante. Prévoir une méthode de validation unitaire (entité, clé, valeur) retournant ok/ko + message, en plus de la validation globale et du clamp.

---

## 2. Export seeder : données manquantes

**Commande :** `php artisan db:export-seeder-data --characteristics`  
**Fichiers générés :** `characteristics.php`, `characteristic_creature.php`, `characteristic_object.php`, `characteristic_spell.php`.

**Manques identifiés :**

| Donnée | Où c’est défini | Export actuel |
|--------|------------------|---------------|
| **Pivot characteristic_object_item_type** | Table pivot (characteristic_object_id, item_type_id) | **Non exportée.** Les associations « caractéristique object ↔ types d’items » ne sont pas reflétées dans les fichiers de données. Au seed, on ne peut pas réinjecter ces liaisons. |
| **value_available** (groupe object) | Colonne `characteristic_object.value_available` (JSON) | **Non exportée** dans le mapping de `exportCharacteristics()` pour `characteristic_object`. |
| **value_available** (groupe spell) | Colonne `characteristic_spell.value_available` | **Non exportée** dans le mapping pour `characteristic_spell`. |

**À faire :**
- Ajouter `value_available` dans l’export des lignes `characteristic_object` et `characteristic_spell`.
- Décider du format d’export pour la pivot `characteristic_object_item_type` (ex. un fichier `characteristic_object_item_type.php` listant les paires characteristic_key + entity + item_type_id, ou une section dans `characteristic_object.php`) et l’implémenter dans `ExportSeederDataCommand`. Adapter le seeder (ex. `ObjectCharacteristicSeeder` ou un seeder dédié) pour réimporter ces liaisons.

---

## 3. Config obsolète ou absente

**Fichier :** `app/Services/Scrapping/Core/Conversion/FormatterApplicator.php` (méthode de rareté par niveau).

**Code :**  
`config('characteristics_rarity.rarity_default_by_level', [0 => 0, 3 => 1, 7 => 2, 10 => 3, 17 => 4])`

**Constat :** Aucun fichier `config/characteristics_rarity.php` (ou clé équivalente) n’existe dans le projet. La valeur utilisée est donc **toujours** le tableau par défaut en second argument.

**À faire :** Soit créer une config dédiée si on veut rendre cette règle configurable, soit s’appuyer sur la **caractéristique** en BDD (formule de conversion / valeur par défaut pour la rareté) et retirer la référence à `characteristics_rarity` pour éviter une config fantôme.

---

## 4. Restriction par types d’items (item_types)

**Décision :** La source de vérité pour « quelles caractéristiques pour quels équipements » est **characteristic_object_item_type** (item_types). Voir [ARCHITECTURE_SOUS_SERVICES.md § 7](./ARCHITECTURE_SOUS_SERVICES.md#7-restriction-par-types-ditems-item_types----une-seule-solution-pas-de-doublon).

---

## 5. API formula-preview : deux endpoints distincts

**Constat :** Deux routes d’aperçu de formules existent :
- `admin/characteristics/formula-preview` (CharacteristicController) : courbe pour la **formule** de la caractéristique (min, max, default_value, formula).
- `admin/dofus-conversion-formulas/formula-preview` (DofusConversionFormulaController) : courbe pour la **formule de conversion Dofus → Krosmoz** (variable `d` = valeur Dofus).

Le frontend (`Index.vue`) appelle les deux selon le contexte (formule caractéristique vs conversion). **Pas de doublon** : rôles différents. Aucune action requise si le comportement est voulu.

---

## 6. Récapitulatif des actions recommandées

| Priorité | Action |
|----------|--------|
| Haute | Étendre **CharacteristicLimitService** pour valider selon le type (boolean, list, string) et exposer une validation unitaire. |
| Haute | **Export seeder** : ajouter `value_available` pour object et spell ; exporter la pivot **characteristic_object_item_type** et adapter le seeder pour la réimporter. |
| Moyenne | Supprimer ou remplacer la référence à `config('characteristics_rarity.rarity_default_by_level')` (config inexistante) ; privilégier la BDD (caractéristique / conversion). |

---

## 7. Suite

- Propriétés de **conversion** (champs, format) : [PROPRIETES_CONVERSION_DOFUS_KROSMOZ.md](./PROPRIETES_CONVERSION_DOFUS_KROSMOZ.md).
- Architecture des 4 sous-services : [ARCHITECTURE_SOUS_SERVICES.md](./ARCHITECTURE_SOUS_SERVICES.md).
