# Syntaxe des formules — Caractéristiques (config)

Convention pour les formules **exploitables** et **affichage** des caractéristiques (classe, monstre). Source de vérité : `config/characteristics.php` ; formules par entité dans `entities.class` et `entities.monster`. Les items n’ont pas de formule (bonus bruts).

## 1. Convention exploitable

Une formule exploitable est une chaîne que l’on peut parser et évaluer pour calculer une valeur à partir d’autres caractéristiques et de dés.

| Syntaxe | Signification | Exemple |
|--------|----------------|--------|
| `[id]` | Référence à une autre caractéristique (id = clé dans `characteristics`) | `[vitality]`, `[level]`, `[strength]` |
| `[mod_<carac>]` | Modificateur d’une caractéristique primaire (carac = strength, intelligence, agility, chance, wisdom, vitality). Défini comme caractéristique dérivée dans la config ; permet de ne pas recalculer le mod et de vérifier les limites. | `[mod_strength]`, `[mod_vitality]` |
| `[competence_mastery]` | **Variable contextuelle** (compétences uniquement). Lors de l’évaluation d’un jet de compétence, l’évaluateur injecte la valeur de maîtrise de l’entité pour cette compétence : **0** = aucune, **1** = maîtrisé (+1×master_bonus), **2** = expertise (+2×master_bonus). Source : `competences_mastery.<id_compétence>` ou équivalent. | Utilisé dans la formule des compétences |
| `ndX` | Dés : n dés à X faces | `1d6`, `2d10`, `1d20` |
| Opérateurs | `+`, `-`, `*`, `/`, `(`, `)` | `[vitality] * 10 + [level] * 2` |
| Arrondis | `floor(...)`, `ceil(...)` | `floor(([strength]-10)/2)` (ou utiliser `[mod_strength]`) |

- **Référence caractéristique** : entre crochets, l’`id` est la clé de la caractéristique. Lors de l’évaluation, remplacer chaque `[id]` par la valeur courante pour l’entité.
- **Modificateurs** : `mod_strength`, `mod_intelligence`, `mod_agility`, `mod_chance`, `mod_wisdom`, `mod_vitality` sont des caractéristiques à part entière (formula = `floor(([<carac>]-10)/2)`), avec min/max pour la validation.
- **Dés** : à l’évaluation, `ndX` donne une valeur entière (somme de n jets de dé à X faces).
- **Reste** : nombres et opérateurs mathématiques de base ; `floor` et `ceil` si besoin.

Exemples de formules exploitables (sans équipement ni forgemagie) :

- PV classe : `[vitality] * 10 + [level] * 2` ; PV monstre : `[vitality] * 7`
- PA / PM : `6`, `3` (classe), `4` (monstre)
- CA : `10 + [mod_vitality]`
- Initiative : `1d20 + [mod_intelligence]`
- Fuite : `1d20 + [mod_agility]` ; Tacle : `1d20 + [mod_chance]`
- Esquive PA/PM : `8 + [mod_wisdom]`
- Réserve Wakfu : `1 + floor([level]/4)`
- **Compétences** : `1d20 + [mod_characteristic] + [competence_mastery] * [master_bonus]` — `[mod_characteristic]` = mod de la caractéristique de la compétence (clé `characteristic`) ; `[competence_mastery]` = 0, 1 ou 2 selon la maîtrise de l’entité pour cette compétence.

## 2. Table par caractéristique (formula = JSON)

À la place d’une formule unique, le champ `formula` peut contenir un **tableau par caractéristique** (JSON) : à partir de chaque valeur indiquée, on applique un résultat (fixe ou formule) **jusqu’à la valeur suivante (non comprise)**. La **dernière ligne** s’applique à **toutes les valeurs supérieures** — on a donc toujours une définition.

**Format JSON :** `characteristic` = id de la caractéristique de référence ; clés numériques (string en JSON) = « à partir de » (seuil) ; valeur = nombre fixe ou chaîne formule.

Exemple (à partir de 1 → 0, à partir de 7 → 2, à partir de 14 → 4) : `{"characteristic":"level","1":0,"7":2,"14":4}`. Une ligne peut avoir une formule : `"14":"[level]*2"`.

Décodage / encodage : `App\Services\Characteristic\FormulaConfigDecoder` (PHP), `Utils/characteristic/formulaConfig.js` (frontend). Évaluation : `FormulaEvaluator::evaluateFormulaOrTable()`.

## 3. Formule d’affichage (formula_display)

`formula_display` est une chaîne **lisible** pour l’UI, propre à chaque entité (`entities.class`, `entities.monster`). Elle décrit le calcul final (base + équipement + forgemagie) sans être évaluée. Ex. : `"Vitalité × 10 + Niveau × 2 + équipement + forgemagie"`.

- **Classe / monstre** : chaque entité peut avoir son propre `formula_display` (le calcul peut différer, ex. PV monstre vs PV classe).
- **Item** : pas de `formula_display` (pas de calcul à partir d’autres caractéristiques).

## 4. Où sont définies les formules

| Emplacement | Contenu |
|------------|--------|
| `config/characteristics.php` | Pour chaque caractéristique, dans `entities.monster` et `entities.class` : `formula` (exploitable), `formula_display` (affichage). |
| Évaluation | Un service ou helper parse la chaîne `formula`, remplace les `[id]` par les valeurs des caractéristiques, évalue les `ndX` et les opérateurs / `floor` / `ceil`. |

Référence croisée : [FORMULAS_PRACTICES.md](FORMULAS_PRACTICES.md), [CONTENT_OVERVIEW.md – section 5](../20-Content/CONTENT_OVERVIEW.md) si existant.
