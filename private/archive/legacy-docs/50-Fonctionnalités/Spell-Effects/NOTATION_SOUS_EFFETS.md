# Notation standardisée des sous-effets

Description **mathématique** des sous-effets pour outils interactifs et traduction en texte lisible. Pattern : **action → caractéristique (élément = caractéristique) → valeur**.

Références : [ARCHITECTURE_EFFETS_3_COUCHES.md](./ARCHITECTURE_EFFETS_3_COUCHES.md), [SYNTAXE_EFFETS.md](./SYNTAXE_EFFETS.md), [TAXONOMIE_SOUS_EFFETS.md](./TAXONOMIE_SOUS_EFFETS.md).

---

## 1. Principe

- Chaque sous-effet est une **action** (booster, retirer, soigner, frapper, …) avec un **param_schema** (structure machine).
- Les paramètres à l’attachement à un effet : **characteristic** (une seule notion ; l’élément est une caractéristique) et **value** (formule). Pour “soigner”, characteristic peut être vide.
- La **valeur** peut être une **formule** : nombre, `ndX`, fourchette `[min-max]`, variables `[level]`, `[agi]`, etc., et fonctions (floor, ceil).

---

## 2. Formule de valeur (syntaxe)

La formule est une chaîne évaluable, alignée sur le moteur de formules du projet (caractéristiques). Exemples :

| Écriture   | Signification                    |
|-----------|-----------------------------------|
| `10`      | Valeur fixe 10.                  |
| `2d6`     | 2 dés à 6 faces (somme).         |
| `[1-4]`   | Valeur aléatoire entre 1 et 4.   |
| `[level]` | Variable niveau (contexte).       |
| `[level]*2` | Niveau × 2.                    |
| `[agi]`   | Caractéristique agilité.          |
| `floor([agi]/2)` | Partie entière inférieure de agi/2. |
| `2d6+[level]` | 2d6 + niveau.                 |

- **Variables autorisées** : `[level]`, `[agi]`, `[strong]`, `[intel]`, `[cha]`, `[wis]`, `[vita]`, `[value]`, `[duration]`, `[element]`, etc. (voir SYNTAXE_EFFETS.md).
- **Fourchette** : `[min-max]` = entier aléatoire entre min et max (inclus).
- **Dés** : `ndX` = n dés à X faces, somme des jets.

---

## 3. Représentation machine (param_schema)

Sur chaque sous-effet (table `sub_effects`), le champ **param_schema** (JSON) décrit les paramètres attendus. Un seul type “caractéristique” : pour “frapper” la caractéristique est un élément (filtré par `categories: ['element']`), pour “booster”/“retirer” c’est une stat/ressource.

Exemple (action booster) :

```json
{
  "action": "booster",
  "params": [
    { "key": "characteristic", "type": "characteristic", "label": "Caractéristique", "categories": ["stat", "resource"] },
    { "key": "value", "type": "formula", "label": "Valeur (formule)" }
  ]
}
```

Exemple (action frapper, caractéristique = élément) :

```json
{
  "action": "frapper",
  "params": [
    { "key": "characteristic", "type": "characteristic", "label": "Élément", "categories": ["element"] },
    { "key": "value", "type": "formula", "label": "Valeur (formule)" }
  ]
}
```

À l’attachement (pivot `effect_sub_effect`), **params** stocke les choix (même clé `characteristic` pour élément ou stat) :

```json
{
  "characteristic": "agi",
  "value_formula": "2d6+[level]"
}
```

ou pour un dégât feu :

```json
{
  "characteristic": "feu",
  "value_formula": "2d6+[level]"
}
```

---

## 4. Traducteur (description lisible)

À partir de :

- **template_text** du sous-effet : `"Ajout [characteristic] de [value]."` ou `"Dégâts [value] [characteristic]."`
- **params** de la ligne : `{ "characteristic": "agi", "value_formula": "2d6+[level]" }` ou `{ "characteristic": "feu", "value_formula": "2d6" }`
- **Référentiel** : une seule liste de caractéristiques (stats + éléments) avec labels (agi → "Agilité", feu → "Feu")

Le traducteur :

1. Remplace `[characteristic]` par le libellé (stat ou élément selon la clé).
2. Remplace `[value]` par la formule brute ou par une évaluation si un contexte est fourni (ex. niveau 5 → "2d6+5").

Résultat lisible : *« Ajout Agilité de 2d6+[niveau]. »* ou *« Dégâts 2d6 Feu. »*

---

## 5. Outils interactifs

La représentation **action + params** (caracteristic, element, value_formula) permet :

- D’**évaluer** la formule avec un contexte (niveau, caractéristiques du personnage).
- De **comparer** des effets (même action, paramètres différents).
- De **générer** des descriptions ou des aides au jeu à partir de la structure machine.

Les fondamentaux (actions booster, retirer, soigner, frapper) sont en base via **SubEffectSeeder** et ne devraient guère bouger ; les paramètres sont saisis à la construction de l’effet (admin Effets).
