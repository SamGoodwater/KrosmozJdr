# Types de valeurs et contenu JSON des caractéristiques

Ce document définit les **types de valeurs** qu’une caractéristique peut prendre dans les entités (Boolean, Liste, String), les **propriétés associées à chaque type**, et le **format JSON** utilisé pour stocker les champs qui peuvent être une valeur fixe, une formule ou une table par caractéristique. C’est la base du service de caractéristiques pour définir correctement les valeurs possibles côté entités.

**Vue d’ensemble :** [PRESENTATION_SERVICE_CARACTERISTIQUES.md](./PRESENTATION_SERVICE_CARACTERISTIQUES.md).

---

## 1. Types de valeurs

Une caractéristique a un **type** qui détermine quelles propriétés sont pertinentes et comment la valeur est interprétée dans les entités.

| Type      | Rôle | Propriétés spécifiques (en plus des communes) |
|-----------|------|------------------------------------------------|
| **boolean** | Valeur oui/non. | Valeur calculée, valeur par défaut, helper, calcul affiché |
| **list**    | Valeur choisie dans une liste fermée. | Valeur calculée, valeurs possibles, valeur par défaut, helper, calcul affiché |
| **string**  | Texte ou chaîne avec éventuelles bornes. | Valeur calculée, valeur par défaut, valeur minimale, valeur maximale, helper, calcul helper, calcul affiché |

Les propriétés **communes** (face visible) restent : clé, nom, nom court, icône, unité, description (voir [PRESENTATION_SERVICE_CARACTERISTIQUES.md](./PRESENTATION_SERVICE_CARACTERISTIQUES.md)).

---

## 2. Propriétés par type

### 2.1 Type **Boolean**

La caractéristique vaut « oui » ou « non ». En entité, on utilise soit une **valeur calculée** (si définie), soit la **valeur par défaut**.

| Propriété | Rôle |
|-----------|------|
| **Valeur calculée** | Détermine le booléen à partir d’autres caractéristiques ou d’une formule. Stockée en JSON (voir section 3). Si absente ou non évaluable, on utilise la valeur par défaut. |
| **Valeur par défaut** | Valeur utilisée quand il n’y a pas de valeur calculée (ou en secours). Stockée en JSON (fixe, formule ou table). |
| **Helper** | Texte d’aide pour guider la saisie ou la compréhension (affichage UI). |
| **Calcul affiché** | Chaîne lisible qui explique comment on obtient la valeur calculée (ex. « Si Vitalité ≥ 12 alors true »). Affichage uniquement, non évaluée. |

---

### 2.2 Type **Liste** (list)

La caractéristique prend une valeur parmi un ensemble **fermé** (valeurs possibles). On peut avoir une valeur calculée ou une valeur par défaut.

| Propriété | Rôle |
|-----------|------|
| **Valeur calculée** | Valeur résultant d’un calcul (formule ou table). Stockée en JSON. Doit être parmi les valeurs possibles une fois évaluée. |
| **Valeurs possibles** | Liste des valeurs autorisées (ex. `[0, 1, 2, 3, 4]` pour rareté, ou libellés). Définit le domaine de la caractéristique. |
| **Valeur par défaut** | Valeur utilisée si pas de calcul ou en secours. Stockée en JSON. Doit être dans les valeurs possibles. |
| **Helper** | Texte d’aide pour le choix dans la liste. |
| **Calcul affiché** | Chaîne lisible décrivant comment la valeur calculée est obtenue. |

---

### 2.3 Type **String**

Chaîne de caractères, avec éventuelles contraintes (min/max). Une valeur de type string peut être un **nombre** (ex. `"42"`), une **formule** (ex. intelligence d’un monstre dépendant du niveau) ou du texte. Les champs min, max et valeur par défaut peuvent être **valeur fixe**, **formule** ou **table** (voir section 3).

| Propriété | Rôle |
|-----------|------|
| **Valeur calculée** | Chaîne résultant d’un calcul. Stockée en JSON. |
| **Valeur par défaut** | Valeur utilisée si pas de calcul ou en secours. Stockée en JSON. |
| **Valeur minimale** | Longueur minimale (ou borne sémantique selon usage). Stockée en JSON (fixe, formule ou table). |
| **Valeur maximale** | Longueur maximale (ou borne sémantique). Stockée en JSON. |
| **Helper** | Texte d’aide pour la saisie. |
| **Calcul helper** | Texte d’aide spécifique pour la partie « calcul » (comment la valeur calculée est obtenue). |
| **Calcul affiché** | Chaîne lisible décrivant le calcul. |

---

## 3. Contenu JSON : valeur fixe, formule ou table

Les champs **valeur calculée**, **valeur par défaut**, **valeur minimale** et **valeur maximale** sont enregistrés en **JSON** (ou chaîne interprétable) dans la base de données, car ils peuvent prendre trois formes différentes.

### 3.1 Les trois formes possibles

| Forme | Description | Exemple (conceptuel) |
|-------|-------------|----------------------|
| **Valeur fixe** | Un nombre ou une chaîne littérale. | `0`, `"true"`, `"Commun"`, `6` |
| **Formule** | Une expression évaluable à partir d’autres caractéristiques (variables `[id]`, opérateurs, fonctions). La syntaxe des formules est détaillée dans [SYNTAXE_FORMULES_CARACTERISTIQUES.md](../../10-BestPractices/SYNTAXE_FORMULES_CARACTERISTIQUES.md). | `"[vitality]*10+[level]*2"`, `"floor([d]/10)"` |
| **Table par caractéristique** | Un objet JSON dont la **première clé** est le nom d’une **caractéristique de référence**. Les autres clés sont des **seuils** (nombres). À chaque seuil on associe une **valeur fixe** ou une **formule**. Pour une valeur `v` de la caractéristique de référence : on prend l’entrée dont le seuil est le plus grand ≤ `v` ; de la valeur `n` à la valeur `(n+1) - 1` (ou jusqu’au prochain seuil), on applique la valeur ou formule de l’entrée « à partir de n ». | Voir ci‑dessous. |

### 3.2 Format de la table par caractéristique

- **Clé principale** : `characteristic` = clé de la caractéristique de référence (ex. `level`, `level_creature`).
- **Clés numériques** : chaque clé numérique est un **seuil** « à partir de ». La valeur associée (fixe ou formule) s’applique pour toute valeur de la caractéristique de référence **≥ ce seuil** et **< au prochain seuil**.
- **Dernière entrée** : la plus grande valeur de seuil s’applique à **toutes les valeurs supérieures ou égales** à ce seuil.

**Exemple :**  
`{"characteristic":"level","0":0,"3":1,"7":2,"10":3,"17":4}`

- Pour `level` 0, 1, 2 → on utilise la valeur **0**.
- Pour `level` 3, 4, 5, 6 → on utilise la valeur **1**.
- Pour `level` 7, 8, 9 → on utilise la valeur **2**.
- Pour `level` 10 à 16 → on utilise la valeur **3**.
- Pour `level` ≥ 17 → on utilise la valeur **4**.

Une entrée peut être une **formule** (chaîne) au lieu d’un nombre : par ex. `"7":"[level]*2"` pour une tranche à partir du niveau 7.

En résumé : **de la valeur n (seuil) à la valeur (seuil suivant) − 1** de la caractéristique nommée, on applique la valeur fixe ou la formule associée à ce seuil.

### 3.3 Décodage et évaluation côté projet

- **Décodage** (identifier si c’est fixe, formule ou table) : `App\Services\Characteristic\Formula\FormulaConfigDecoder` (PHP).
- **Évaluation** (formule ou table avec variables) : `FormulaResolutionService` / `SafeExpressionEvaluator` (voir [SYNTAXE_FORMULES_CARACTERISTIQUES.md](../../10-BestPractices/SYNTAXE_FORMULES_CARACTERISTIQUES.md)).

---

## 4. Récapitulatif par type

| Type    | Champs stockés en JSON (ou équivalent) | Autres propriétés spécifiques |
|---------|----------------------------------------|--------------------------------|
| boolean | Valeur calculée, valeur par défaut     | Helper, calcul affiché |
| list    | Valeur calculée, valeur par défaut     | Valeurs possibles, helper, calcul affiché |
| string  | Valeur calculée, valeur par défaut, valeur min, valeur max | Helper, calcul helper, calcul affiché |

Chaque champ listé en JSON peut être, indépendamment : **valeur fixe**, **formule**, ou **table par caractéristique** (section 3).

---

## 5. Propriétés spécifiques au groupe **object**

Pour les entités du groupe **object** (item, resource, consumable, panoply), des propriétés supplémentaires permettent de **calculer automatiquement le prix** d’un objet et de gérer la **forgemagie** (bonus limité + coût par rune).

### 5.1 Prix et calcul du prix de l’objet

| Propriété | Rôle | Type / remarque |
|-----------|------|------------------|
| **Prix par unité** | Prix (ex. en kamas) attribué à **chaque unité** de la caractéristique. Permet de calculer automatiquement le prix total d’un objet à partir de la valeur de la caractéristique (ex. bonus PA × prix par unité). | Numérique (décimal). Peut être défini par caractéristique et par entité (ex. item vs resource). |

*Exemple :* si « PA » (bonus) a un prix par unité de 50 kamas et que l’objet donne +2 PA, la part de prix due à cette caractéristique est 2 × 50 = 100 kamas. Le prix global de l’objet peut être la somme des contributions de chaque caractéristique concernée.

### 5.2 Forgemagie

Une caractéristique d’objet peut recevoir un **bonus par forgemagie**. Deux propriétés la concernent :

| Propriété | Rôle | Type / remarque |
|-----------|------|------------------|
| **forgemagie_max** | **Maximum** de bonus ajoutable par forgemagie pour cette caractéristique (entier). Limite simple : on ne peut pas dépasser ce nombre de « points » (ou unités) en forgemagie. | Entier (int). |
| **Prix par rune** | Prix (ex. en kamas) d’**une rune de forgemagie** pour cette caractéristique. Utilisé pour calculer le coût de l’amélioration (ex. nombre de runes × prix par rune). | Numérique (décimal). |

En complément, une caractéristique peut être **autorisée ou non** à la forgemagie (ex. champ booléen « forgemagie autorisée ») : si non, forgemagie_max et prix par rune ne s’appliquent pas.

### 5.3 Restriction par type d’équipement (slot)

Une caractéristique du groupe **object** peut être **réservée à certains types d’équipement** (slots), via la table pivot `characteristic_object_item_type` qui lie `characteristic_object` aux **id** de la table **item_types**.

| Propriété | Rôle | Type / remarque |
|-----------|------|------------------|
| **allowed_item_type_ids** | Liste des **id** des types d’équipement (item_types) pour lesquels cette caractéristique est proposée. | Tableau d’entiers (ids). **Vide ou absent** = la caractéristique s’applique à **tous** les types ; sinon elle ne s’affiche / ne s’applique qu’aux types listés. |

En base : une ligne dans `characteristic_object` sans entrée dans `characteristic_object_item_type` → tous les types. Avec des entrées dans la pivot → uniquement les `item_type_id` listés. Le getter expose cette liste sous la clé **allowed_item_type_ids** dans la définition retournée pour l’entité.

**Récapitulatif objet :**

- **Prix par unité** → calcul du prix de l’objet à partir des valeurs des caractéristiques.
- **forgemagie_max** → limite du bonus forgemagie (entier).
- **Prix par rune** → coût d’une rune de forgemagie pour cette caractéristique.
- **allowed_item_type_ids** → restriction optionnelle aux types d’équipement (item_types) ; vide = tous les types.

---

## 6. Suite

- **Syntaxe des formules** (variables `[id]`, dés, fonctions) : [SYNTAXE_FORMULES_CARACTERISTIQUES.md](../../10-BestPractices/SYNTAXE_FORMULES_CARACTERISTIQUES.md).
- **Présentation du service** et propriétés communes : [PRESENTATION_SERVICE_CARACTERISTIQUES.md](./PRESENTATION_SERVICE_CARACTERISTIQUES.md).
- Autres propriétés (conversion Dofus, etc.) : à documenter dans ce dossier.
