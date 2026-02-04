# Plan d’architecture : groupes d’entités et sous-services

Ce document analyse le plan proposé : **tables par groupe d’entités** + **4 sous-services** (getter, limite, formule, conversion) + **un seeder par groupe**. Il précise ce qui tient la route, les points d’attention et une proposition de mise en œuvre.

---

## 1. Synthèse : le plan tient la route

L’ensemble est **cohérent et réalisable**. La séparation en groupes (créature / objet / sort) et en 4 sous-services (getter, limite, formule, conversion) clarifie les responsabilités et évite le mélange actuel validation / conversion dans un même bloc. Les points à trancher concernent surtout le périmètre exact des groupes (panoply, équipement) et l’endroit où stocker les formules de conversion Dofus.

---

## 2. Tables par groupe d’entités

### 2.1 Mapping proposé

| Groupe (table) | Entités incluses | Rôle |
|----------------|------------------|------|
| **characteristic_creature** | monster, npc, class (et player plus tard) | Caractéristiques communes : vie, stats, résistances, initiative, etc. |
| **characteristic_object** | item (équipement), consumable, resource | Level, rareté, prix, poids, effets (item), recettes, etc. |
| **characteristic_spell** | spell | Caractéristiques des sorts (PA, portée, effets, etc.) |

Principe **« qui peut le plus peut le moins »** : pour un groupe, on prend l’**union** des caractéristiques de toutes les entités du groupe. Les colonnes spécifiques à une seule entité (ex. forgemagie pour item, pas pour resource) restent **optionnelles** (nullable ou ignorées selon l’entité). C’est déjà le cas aujourd’hui avec `entity_characteristics` (une ligne par `(entity, characteristic_key)`).

Chaque table conserve une colonne **`entity`** pour distinguer les entités au sein du groupe.

**Champ `entity` : défaut groupe vs surcharge par entité**

- **`entity = '*'`** : la ligne s’applique **par défaut à toutes les entités du groupe**. Une seule configuration pour monster, npc et class (ou pour item, consumable, resource, panoply) quand les paramètres sont identiques.
- **`entity = 'monster'`** (ou une autre entité) : **surcharge** pour cette entité uniquement. Permet d’avoir les mêmes réglages pour la plupart des entités (ligne `*`) et d’ajuster une entité en particulier (ex. level 1–20 pour resource, 1–200 pour les autres).

Résolution dans le Getter : pour une entité demandée, on cherche d’abord une ligne avec `entity = entité demandée`, sinon on utilise la ligne `entity = '*'`. La clé unique reste `(characteristic_id, entity)` par table.

### 2.2 Points à trancher

- **Panoply** : aujourd’hui `ENTITY_PANOPLY` existe. Deux options cohérentes avec ton plan :
  - Inclure la panoplie dans **characteristic_object** avec `entity = 'panoply'` (une panoplie est un ensemble d’objets/équipements), ou
  - Garder une petite table dédiée si la panoplie a un modèle très différent (ex. seulement des liens vers des sets). À définir selon le métier.
- **Slots d’équipement** : `equipment_slots` et `equipment_slot_characteristics` concernent les **items** (équipement). Ils peuvent rester tels quels, en étant considérés comme une extension « par slot » du groupe object (item). Pas besoin de les fusionner dans characteristic_object si tu veux garder une séparation slot / caractéristiques globales.

---

## 3. Les 4 sous-services

### 3.1 Getter (généraliste)

- **Rôle** : fournir les **définitions** d’une caractéristique par nom/id et entité : name, short_name, description, icon, color, unit, type, min, max, default_value, formula, validation_message, etc., et éventuellement la formule de conversion Dofus si tu la stockes dans les tables de groupe.
- **Entrées** : `characteristic_key` (ou id), `entity`.
- **Sorties** : tableau de définition (tout ce dont les autres services ont besoin).
- **Implémentation** : résolution **entity → groupe** (monster → creature, item → object, spell → spell), puis lecture dans la bonne table (`characteristic_creature`, `characteristic_object`, `characteristic_spell`). Cache possible par `(entity, characteristic_key)` ou par groupe.
- **Remarque** : aujourd’hui les **formules de conversion Dofus** sont dans `dofusdb_conversion_formulas`. Tu peux soit les laisser là et que le getter les agrège (lecture BDD supplémentaire), soit ajouter une colonne `conversion_formula` (ou équivalent) dans chaque table de groupe et n’avoir qu’une source (les 3 tables). Les deux approches sont valides ; la seconde simplifie le getter et regroupe toute la définition « caractéristique » par groupe.

### 3.2 Service Limite

- **Rôle** : vérifier qu’un **objet entité** (ou une seule caractéristique) respecte les **limites** (min, max, required, value_available) définies pour son entité.
- **Entrées** : données à valider (tableau caractéristique → valeur), type d’entité ; ou une seule clé + valeur.
- **Sorties** : succès / erreurs (liste de champs hors limites ou requis manquants).
- **Dépendance** : utilise le **Getter** pour obtenir les définitions (min, max, required, validation_message, value_available, db_column) pour l’entité donnée. Pas de formule ni de conversion.
- **Correspondance** : c’est l’actuel **ValidationService**, recentré uniquement sur la validation des limites (et éventuellement des valeurs autorisées), sans mélange avec la conversion.

**Interface admin (Vue)** : La page `Admin/characteristics/Index.vue` permet de lister les caractéristiques (liste à gauche), d’en créer de nouvelles (bouton « Créer une caractéristique », choix du groupe puis clé/nom et paramètres par entité du groupe) et d’éditer une caractéristique existante. L’édition est organisée par **groupe d’entités** (Créature, Objet, Sort) en accordéons ; chaque groupe affiche une carte par entité (Défaut, Monstre, Classe, PNJ, etc.) avec min/max, formule (simple ou table), formule d’affichage, valeur par défaut, et pour l’objet : forgemagie et prix. Dès qu’une formule est renseignée, un **graphique** (FormulaChart) affiche l’évolution (axe X = variable, axe Y = résultat). Les formules de conversion Dofus → JDR sont en bas de page avec leur propre aperçu graphique. La barre latérale droite liste les caractéristiques par entité (vue par type d’entité).

**Option B — Validation des FormRequests dérivée du Getter** : les règles de validation HTTP (min/max) des formulaires d’entités (Resource, Spell, Item, Consumable) sont dérivées dynamiquement de `CharacteristicGetterService::getLimitsByField()` au lieu d’être codées en dur. Le trait `HasCharacteristicValidation` et la méthode `characteristicMinMaxRules($field, $entity)` permettent d’appliquer les bornes définies en base (tables characteristic_*) aux champs `rarity`, `level`, `area`, `element`, `powerful`, etc. Un fallback (ex. `min:0`, `max:5`) est utilisé lorsque aucune limite n’est configurée pour l’entité. Fichiers concernés : `App\Http\Requests\Concerns\HasCharacteristicValidation`, `UpdateResourceRequest`, `StoreResourceRequest`, `UpdateSpellRequest`, `StoreSpellRequest`, `UpdateItemRequest`, `StoreItemRequest`, `UpdateConsumableRequest`, `StoreConsumableRequest`.

### 3.3 Service de calcul de formules

- **Rôle** : évaluer des **formules** (expressions ou tables niveau → valeur) de façon **sécurisée** (pas d’injection, fonctions limitées au mathématique : floor, ceil, +, -, *, /, variables `[id]`).
- **Entrées** : formule (chaîne ou JSON table) + variables (ex. level, vitality, d).
- **Sorties** : résultat numérique (ou null si invalide).
- **Responsabilités** : validation syntaxique des formules, évaluation, respect des règles de sécurité (déjà en place avec `FormulaResolutionService` / `SafeExpressionEvaluator`).
- **Utilisé par** : Getter (pour valeurs par défaut ou dérivées si besoin), **Conversion** (calcul des valeurs à partir des formules Dofus), et éventuellement l’admin (aperçu de formules). Il ne dépend pas des tables « characteristic » : seulement formule + variables.

Ce sous-service correspond à l’actuel **FormulaEvaluator** + **Formula** (FormulaResolutionService, SafeExpressionEvaluator), éventuellement regroupés sous un seul point d’entrée « CharacteristicFormulaService » ou équivalent.

### 3.4 Service de conversion (Dofus → Krosmoz)

- **Rôle** : convertir des **données brutes Dofus** en données **Krosmoz** exploitables (niveau, vie, rareté, bonus, etc.).
- **Entrées** : valeurs brutes Dofus (et contexte : entité, niveau JDR si déjà calculé, etc.).
- **Sorties** : valeurs Krosmoz (par champ ou objet).
- **Dépendances** :
  - **Getter** : pour récupérer les formules de conversion (par characteristic_key + entity) et les limites.
  - **Service de formules** : pour **calculer** les valeurs à partir des formules (ex. level = floor(d/10), rareté = table(level)).
  - **Service limite** : pour **clamper** les résultats dans les bornes et, si tu veux, pour valider l’objet converti entier.
- **Correspondance** : l’actuel **DofusDbConversionFormulas** + usage de CharacteristicService (getLimits, getRarityByLevel) et FormulaEvaluator. La logique « conversion » ne fait plus la validation elle‑même ; elle délègue au service limite.

Résumé des flux :

- **Conversion** appelle **Getter** (formules de conversion + limites) et **Formules** (calcul) → puis **Limite** (clamp / validation).
- **Limite** appelle uniquement **Getter** (définitions).
- **Formules** ne dépend d’aucun autre sous-service characteristic.
- **Getter** ne dépend que des 3 tables (et éventuellement d’une table ou colonne de formules de conversion).

---

## 4. Seeders par groupe

- **Un seeder par groupe** : idée cohérente avec les 3 tables.
  - **CreatureCharacteristicSeeder** → `characteristic_creature`
  - **ObjectCharacteristicSeeder** → `characteristic_object`
  - **SpellCharacteristicSeeder** → `characteristic_spell`
- Fichiers de données suggérés :  
  `database/seeders/data/characteristic_creature.php`,  
  `characteristic_object.php`,  
  `characteristic_spell.php`.
- Chaque fichier contient les lignes pour toutes les entités du groupe (monster, class, npc pour creature ; item, consumable, resource pour object ; spell pour spell), avec des valeurs cohérentes (min/max, messages, formules par défaut). Permet d’initialiser le projet avec une base exploitable.

---

## 5. Récap : ce qui tient la route et à surveiller

| Élément | Verdict | Note |
|--------|--------|------|
| 3 tables par groupe (creature, object, spell) | OK | Conserver une colonne `entity` dans chaque table. |
| Principe « qui peut le plus peut le moins » (union des caractéristiques du groupe) | OK | Colonnes optionnelles selon l’entité. |
| Getter généraliste (nom/id + entité → infos) | OK | + résolution entity → groupe. |
| Service limite (validation entité ou une caractéristique) | OK | = ValidationService recentré, dépend du Getter. |
| Service formules (calcul sécurisé) | OK | = FormulaEvaluator / Formula existants, sans dépendance aux tables. |
| Service conversion (Dofus → Krosmoz, s’appuie sur getter + formules + limite) | OK | Délègue calcul et validation au lieu de tout mélanger. |
| Un seeder par groupe | OK | 3 seeders + 3 fichiers de données. |
| Panoply | À trancher | Object avec entity=panoply ou table dédiée. |
| Où stocker les formules de conversion Dofus | À trancher | Soit table dédiée (actuelle), soit colonne dans les 3 tables de groupe. |
| equipment_slots / equipment_slot_characteristics | Optionnel | Peuvent rester en l’état comme extension « item ». |

---

## 6. Ordre de mise en œuvre suggéré

1. **Décider** : panoply (dans object ou table à part), stockage des formules de conversion (table dédiée vs colonnes dans les groupes).
2. **Créer les 3 tables** : migrations `characteristic_creature`, `characteristic_object`, `characteristic_spell` (schéma proche de l’actuel `entity_characteristics`, avec `entity` dans chaque table).
3. **Migrer les données** : script ou migration qui lit `entity_characteristics` et répartit les lignes dans les 3 tables selon entity → groupe.
4. **Implémenter le Getter** : résolution entity → groupe, lecture depuis les 3 tables, API « par characteristic_key + entity ». Si les formules de conversion restent en table dédiée, le getter les agrège ; sinon, une colonne par table de groupe.
5. **Extraire le service Limite** : prendre l’actuel ValidationService, le faire dépendre uniquement du Getter (limites, required, value_available), sans logique de conversion.
6. **Structurer le service Formules** : un seul point d’entrée (ex. CharacteristicFormulaService) qui encapsule FormulaEvaluator / FormulaResolutionService / SafeExpressionEvaluator.
7. **Refondre le service Conversion** : il utilise Getter (formules de conversion + limites), service Formules (calcul), service Limite (clamp / validation). Rarity par niveau, level, life, etc. passent par ce service.
8. **Seeders** : créer les 3 seeders et les 3 fichiers de données, puis les appeler depuis `DatabaseSeeder`.
9. **Adapter l’admin et les appels** : remplacer les usages de `CharacteristicService` / `ValidationService` / `DofusDbConversionFormulas` par les 4 sous-services (Getter, Limite, Formules, Conversion) selon le besoin.
10. **Déprécier / supprimer** : `entity_characteristics` (après migration et bascule), et l’ancien CharacteristicService une fois tout migré.

---

## 7. Références

- État actuel : [RESUME_EXISTANT.md](./RESUME_EXISTANT.md)
- Besoin et refonte : [BESOIN_REFONTE.md](./BESOIN_REFONTE.md)
- Syntaxe des formules : [SYNTAXE_FORMULES_CARACTERISTIQUES.md](../../10-BestPractices/SYNTAXE_FORMULES_CARACTERISTIQUES.md)
