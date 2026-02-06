# Propriétés de conversion Dofus → Krosmoz

Ce document décrit les **propriétés** permettant de convertir des valeurs du jeu Dofus en valeurs exploitables pour Krosmoz JDR : format de la formule de conversion (fixe, formule, table) et **échantillons par niveau** pour l’affichage de graphiques et l’aide à la création de la formule.

**Contexte :** [ARCHITECTURE_SOUS_SERVICES.md](./ARCHITECTURE_SOUS_SERVICES.md) § 5 (Conversion), [TYPES_VALEURS_ET_CONTENU_JSON.md](./TYPES_VALEURS_ET_CONTENU_JSON.md) § 3 (formes fixe / formule / table).

---

## 1. Formule de conversion : les trois formats

Le champ **conversion_formula** (déjà présent dans les tables `characteristic_creature`, `characteristic_object`, `characteristic_spell`) peut contenir une **valeur fixe**, une **formule** ou une **table**, comme pour les champs min/max/formula. La variable d’entrée pour la conversion est **`d`** (valeur Dofus à convertir).

| Format | Description | Exemple |
|--------|-------------|---------|
| **Fixe** | Un nombre. La valeur Krosmoz est toujours ce nombre (conversion constante). | `1`, `0`, `100` |
| **Formule** | Une expression avec la variable `[d]` (valeur Dofus). Opérateurs et fonctions autorisées (floor, ceil, etc.). | `"floor([d]/10)"`, `"[d]*20/200"` |
| **Table** | Objet JSON : `characteristic` = `"d"`, clés numériques = seuils de **d** (valeur Dofus), valeur associée = résultat Krosmoz (fixe ou sous-formule). Pour une valeur **d** donnée, on prend le plus grand seuil ≤ d et on applique la valeur associée. | Voir ci-dessous. |

### 1.1 Exemple de table par valeur Dofus

`{"characteristic":"d","0":0,"10":1,"50":5,"200":20}`

- Pour **d** = 0 à 9 → résultat **0**
- Pour **d** = 10 à 49 → résultat **1**
- Pour **d** = 50 à 199 → résultat **5**
- Pour **d** ≥ 200 → résultat **20**

Le service Conversion utilise le même décodeur et évaluateur que pour les autres formules (FormulaConfigDecoder, CharacteristicFormulaService), en passant la variable `d` = valeur Dofus.

---

## 2. Échantillons par niveau : affichage et graphiques

Pour **afficher des graphiques** qui comparent les valeurs Dofus et les valeurs cibles Krosmoz en fonction du **niveau**, on stocke deux objets JSON optionnels :

- **Valeurs Dofus en fonction du niveau** (échelle du jeu Dofus, niveaux 1 à 200 typiquement).
- **Valeurs cibles Krosmoz en fonction du niveau** (échelle Krosmoz JDR, niveaux 1 à 20 typiquement).

Cela permet de tracer deux courbes (niveau → valeur Dofus, niveau → valeur Krosmoz) et de dériver visuellement — ou automatiquement — une formule de conversion.

### 2.1 conversion_dofus_sample (JSON, nullable)

**Clé** : `conversion_dofus_sample`  
**Rôle** : Pour chaque **niveau** (clé), la **valeur de la caractéristique côté Dofus** à ce niveau. Sert à tracer la courbe « niveau (abscisse) → valeur Dofus (ordonnée) ».

**Format** : objet JSON dont les clés sont des niveaux (entiers, en chaîne) et les valeurs sont des nombres.  
Exemple : `{"1": 200, "4": 800, "10": 2000, "50": 15000, "200": 50000}`

**Valeur par défaut suggérée** : `{"1": 1, "200": 200}` (linéaire 1→200 sur l’échelle Dofus).

### 2.2 conversion_krosmoz_sample (JSON, nullable)

**Clé** : `conversion_krosmoz_sample`  
**Rôle** : Pour chaque **niveau** (clé), la **valeur cible Krosmoz** à ce niveau. Sert à tracer la courbe « niveau (abscisse) → valeur Krosmoz (ordonnée) ».

**Format** : objet JSON dont les clés sont des niveaux (entiers, en chaîne) et les valeurs sont des nombres.  
Exemple : `{"1": 1, "5": 5, "10": 10, "20": 20}`

**Valeur par défaut suggérée** : `{"1": 1, "20": 20}` (linéaire 1→20 sur l’échelle Krosmoz).

### 2.3 Utilisation

- **Affichage** : l’interface admin peut tracer deux courbes (niveau en abscisse ; ordonnées = échantillon Dofus et échantillon Krosmoz) pour comparer les deux échelles.
- **Création de la formule** : en disposant des paires (niveau, valeur Dofus) et (niveau, valeur Krosmoz), on peut déduire pour chaque niveau un couple (valeur Dofus, valeur Krosmoz) et en dériver une **table de conversion** ou une **formule** (voir § 3).

---

## 3. Automatisation de la formule de conversion

À partir des deux échantillons (`conversion_dofus_sample` et `conversion_krosmoz_sample`), on peut **automatiser** la génération d’une **conversion_formula** au format **table** (et éventuellement suggérer une formule simple si la relation est linéaire).

### 3.1 Génération d’une table par `d`

**Principe** : Les deux JSON sont indexés par **niveau**. En faisant correspondre niveau par niveau, on obtient des paires (valeur Dofus, valeur Krosmoz). On peut alors produire une table de conversion par **d** (valeur Dofus) :

- Pour chaque niveau **L** présent dans les deux échantillons :  
  `d_L` = conversion_dofus_sample[L],  
  `k_L` = conversion_krosmoz_sample[L].  
- Trier les niveaux et construire l’objet :  
  `{"characteristic":"d", "d_1": k_1, "d_2": k_2, ...}`  
  où les seuils sont les valeurs Dofus (d_L) et les valeurs associées sont les valeurs Krosmoz (k_L).

Cette approche convient à **toute forme de courbe** (linéaire, puissance, etc.) car elle ne suppose aucun modèle.

### 3.2 Suggestion de formules paramétrées (puissance)

Les courbes Dofus augmentent souvent **fortement** avec le niveau ; des formules en **puissance** sont donc plus adaptées qu’une simple régression linéaire. Le service **ConversionFormulaGenerator** propose deux familles :

| Type | Forme | Exemple de formule générée |
|------|--------|-----------------------------|
| **Linéaire** | k = a × d + b | `0.1 * [d]` ou `[d]/10 + 2` |
| **Puissance** | k = a × d^b | `8 * pow([d], 1.35)` |
| **Puissance décalée** | k = a + b × ((d − c) / e)^f | `8 + 24 * pow(([d]-50)/1150, 0.6)` |

Exemples réels (variable de conversion : `[d]`) :
- `8 + 24 * pow(([d]-50)/1150, 0.6)` (puissance décalée)
- `8 * pow([d], 1.35)` (puissance). Pour des formules dépendant aussi d’autres variables (ex. niveau, vitalité), on utilise `[d]` pour la valeur Dofus en entrée.

Le service ajuste les paramètres à partir des paires (d, k) déduites des échantillons et retourne un **R²** pour chaque type. L’interface peut afficher la table générée + les trois formules suggérées (linéaire, puissance, puissance décalée) et laisser l’utilisateur choisir.

### 3.3 Service et intégration

**Service :** `App\Services\Characteristic\Conversion\ConversionFormulaGenerator`

- `pairsFromSamples(array $dofusSample, array $krosmozSample): array` — construit les paires (d, k) en alignant par niveau.
- `generateTableFromSamples(...): string` — retourne la table JSON (conversion_formula au format table).
- `fitLinear(array $pairs): ?array` — ajustement k = a × d + b ; retourne `['formula' => '...', 'a' => ..., 'b' => ..., 'r2' => ...]` ou null.
- `fitPower(array $pairs): ?array` — ajustement k = a × d^b ; retourne `['formula' => '...', 'a' => ..., 'b' => ..., 'r2' => ...]` ou null.
- `fitShiftedPower(array $pairs): ?array` — ajustement k = a + b × ((d−c)/e)^f ; retourne formula, paramètres et r2.
- `suggestFormulas(array $dofusSample, array $krosmozSample): array` — retourne `['table' => '...', 'linear' => ...|null, 'power' => ...|null, 'shifted_power' => ...|null]`.

**Endpoint API :** `POST /admin/characteristics/suggest-conversion-formula`  
Body : `conversion_dofus_sample` (object niveau → valeur Dofus), `conversion_krosmoz_sample` (object niveau → valeur Krosmoz), `curve_type` (`table` | `linear` | `power` | `shifted_power`).  
Réponse : `{ formula: string, r2?: number }` (ou 422 si l’ajustement demandé est impossible). L’UI l’appelle après remplissage des échantillons pour obtenir la formule à enregistrer.

---

## 4. Récapitulatif des champs

| Champ | Table(s) | Type | Rôle |
|-------|----------|------|------|
| **conversion_formula** | characteristic_creature, characteristic_object, characteristic_spell | text (nullable) | Formule de conversion : **fixe**, **formule** avec `[d]`, ou **table** par `d`. |
| **conversion_dofus_sample** | idem | json (nullable) | Échantillon niveau → valeur Dofus (pour graphiques et génération de formule). Défaut suggéré : `{"1":1,"200":200}`. |
| **conversion_krosmoz_sample** | idem | json (nullable) | Échantillon niveau → valeur Krosmoz (pour graphiques et génération de formule). Défaut suggéré : `{"1":1,"20":20}`. |

---

## 5. Suite

- **Service d’automatisation** : `App\Services\Characteristic\Conversion\ConversionFormulaGenerator` (table + formules puissance et puissance décalée, avec R²). Tests : `tests/Unit/Characteristic/ConversionFormulaGeneratorTest.php`.
- Évaluation de la conversion (variable `d`) : [ARCHITECTURE_SOUS_SERVICES.md](./ARCHITECTURE_SOUS_SERVICES.md) § 5, `DofusConversionService`.
- Syntaxe des formules et tables : [TYPES_VALEURS_ET_CONTENU_JSON.md](./TYPES_VALEURS_ET_CONTENU_JSON.md) § 3, [SYNTAXE_FORMULES_CARACTERISTIQUES.md](../../10-BestPractices/SYNTAXE_FORMULES_CARACTERISTIQUES.md).
